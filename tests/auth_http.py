"""Pruebas HTTP aisladas. Ejecutar: python tests/auth_http.py --php C:/xampp/php/php.exe

Requiere MySQL configurado en Database.php y permiso CREATE TEMPORARY TABLE.
No modifica tablas permanentes ni requiere paquetes Python adicionales.
"""
import argparse
import html.parser
import http.cookiejar
import json
import os
from pathlib import Path
import shutil
import socket
import subprocess
import tempfile
import time
import unittest
import urllib.error
import urllib.parse
import urllib.request

PROJECT = Path(__file__).resolve().parents[1]
PASSWORD = 'AuthTest!2026'
HOMES = {'admin': 'admin_dashboard', 'expositor': 'expositor_eventos', 'participante': 'participante_dashboard'}
BASE_URL = ''
STATE_PATH = None
BASE_STATE = None


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


class Inputs(html.parser.HTMLParser):
    def __init__(self, body):
        super().__init__()
        self.values = {}
        self.feed(body)

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == 'input' and attrs.get('name'):
            self.values[attrs['name']] = attrs.get('value', '')


class Browser:
    def __init__(self):
        self.cookies = http.cookiejar.CookieJar()
        self.opener = urllib.request.build_opener(urllib.request.ProxyHandler({}),
            urllib.request.HTTPCookieProcessor(self.cookies), NoRedirect())

    def request(self, action=None, data=None):
        url = BASE_URL + '/index.php'
        if action is not None:
            url += '?' + urllib.parse.urlencode({'action': action})
        try:
            response = self.opener.open(url,
                data=None if data is None else urllib.parse.urlencode(data).encode(), timeout=10)
        except urllib.error.HTTPError as error:
            response = error
        with response:
            return response.code, response.headers, response.read().decode('utf-8', errors='replace')

    def session_id(self):
        return next((c.value for c in self.cookies if c.name == 'PHPSESSID'), None)


class AuthHTTPTests(unittest.TestCase):
    def setUp(self):
        STATE_PATH.write_text(json.dumps(BASE_STATE), encoding='utf-8')
        self.browser = Browser()

    def csrf(self, action='login'):
        code, _, body = self.browser.request(action)
        self.assertEqual(code, 200, body[:800])
        token = Inputs(body).values.get('csrf_token')
        self.assertTrue(token, 'El formulario debe incluir csrf_token')
        return token

    def redirect(self, response, action, status=None):
        code, headers, body = response
        self.assertIn(code, (302, 303) if status is None else (status,), body[:800])
        query = urllib.parse.urlparse(headers.get('Location', '')).query
        self.assertEqual(urllib.parse.parse_qs(query).get('action'), [action])

    def login(self, role):
        token = self.csrf()
        before = self.browser.session_id()
        self.redirect(self.browser.request('do_login', {
            'correo': role + '@auth.test', 'password': PASSWORD, 'csrf_token': token,
        }), HOMES[role], 303)
        self.assertNotEqual(before, self.browser.session_id())

    def page(self, action):
        code, _, body = self.browser.request(action)
        self.assertEqual(code, 200, body[:800])
        self.assertIn('<main', body, 'El módulo debe mostrar contenido útil')
        self.assertGreater(len(body.strip()), 500)
        self.assertNotRegex(body, r'(?i)(fatal error|warning:|uncaught .*exception)')
        return body

    def fixture_update(self, email, **changes):
        state = json.loads(STATE_PATH.read_text(encoding='utf-8'))
        next(user for user in state['usuarios'] if user['correo'] == email).update(changes)
        STATE_PATH.write_text(json.dumps(state), encoding='utf-8')

    def test_01_roles_and_entry_redirects(self):
        for role, action in HOMES.items():
            with self.subTest(role=role):
                self.browser = Browser()
                self.login(role)
                self.page(action)
                for entry in (None, 'login', 'registro'):
                    self.redirect(self.browser.request(entry), action)

    def test_02_anonymous_and_role_permissions(self):
        for action in HOMES.values():
            self.redirect(self.browser.request(action), 'login')
        for role, denied in {
            'participante': ('admin_dashboard', 'expositor_eventos'),
            'expositor': ('admin_dashboard', 'participante_dashboard'),
        }.items():
            self.browser = Browser()
            self.login(role)
            for action in denied:
                self.assertEqual(self.browser.request(action)[0], 403)
        self.browser = Browser()
        self.login('admin')
        self.page('participante_dashboard')
        self.page('expositor_eventos')

    def test_03_rejected_credentials_and_csrf(self):
        for email, password in (('admin@auth.test', 'incorrecta'), ('nadie@auth.test', PASSWORD),
                                ('inactivo@auth.test', PASSWORD), ('desconocido@auth.test', PASSWORD)):
            with self.subTest(email=email):
                self.browser = Browser()
                self.redirect(self.browser.request('do_login', {
                    'correo': email, 'password': password, 'csrf_token': self.csrf(),
                }), 'login')
                self.redirect(self.browser.request('participante_dashboard'), 'login')
                self.assertIn('alert-danger', self.browser.request('login')[2])
        for extra in ({}, {'csrf_token': 'invalido'}):
            self.browser = Browser()
            self.csrf()
            self.redirect(self.browser.request('do_login', {
                'correo': 'admin@auth.test', 'password': PASSWORD, **extra,
            }), 'login')
            self.redirect(self.browser.request('admin_dashboard'), 'login')

    def test_04_logout_post_csrf_and_session(self):
        self.login('participante')
        token = Inputs(self.page('participante_dashboard')).values.get('csrf_token')
        self.assertTrue(token)
        self.assertEqual(self.browser.request('logout')[0], 405)
        self.page('participante_dashboard')
        self.assertEqual(self.browser.request('logout', {})[0], 403)
        self.page('participante_dashboard')
        self.redirect(self.browser.request('logout', {'csrf_token': token}), 'login', 303)
        self.redirect(self.browser.request('participante_dashboard'), 'login')

    def test_05_registration_validation_and_database_role(self):
        data = {'ci': 'AUTH-REGISTRO', 'nombres': 'Registro', 'apellidos': 'Temporal',
                'correo': 'nuevo@auth.test', 'telefono': '', 'password': PASSWORD, 'password_confirm': PASSWORD}
        self.csrf('registro')
        self.redirect(self.browser.request('do_registro', data), 'registro')
        for extra in ({'password_confirm': 'diferente'}, {'password': '12345', 'password_confirm': '12345'},
                      {'correo': 'admin@auth.test'}, {'ci': 'AUTH-ADMIN'}):
            with self.subTest(extra=extra):
                self.redirect(self.browser.request('do_registro', {
                    **data, **extra, 'csrf_token': self.csrf('registro'),
                }), 'registro')
                self.assertEqual(len(json.loads(STATE_PATH.read_text())['usuarios']), len(BASE_STATE['usuarios']))
        self.redirect(self.browser.request('do_registro', {
            **data, 'csrf_token': self.csrf('registro'), 'id_rol': '11', 'rol_nombre': 'ADMINISTRADOR',
        }), 'login', 303)
        user = next(u for u in json.loads(STATE_PATH.read_text())['usuarios'] if u['correo'] == data['correo'])
        self.assertEqual(int(user['id_rol']), 33, 'Debe resolver PARTICIPANTE por nombre e ignorar el rol recibido')
        self.assertNotEqual(user['password_hash'], PASSWORD)
        self.redirect(self.browser.request('do_login', {
            'correo': data['correo'], 'password': PASSWORD, 'csrf_token': self.csrf(),
        }), 'participante_dashboard', 303)
        self.page('participante_dashboard')

    def test_06_live_session_role_and_account_changes(self):
        self.login('admin')
        self.fixture_update('admin@auth.test', id_rol=33)
        self.assertEqual(self.browser.request('admin_dashboard')[0], 403)
        self.redirect(self.browser.request(None), 'participante_dashboard')
        self.page('participante_dashboard')
        for changes in ({'activo': 0}, {'id_rol': 44}):
            with self.subTest(changes=changes):
                self.setUp()
                self.login('admin')
                self.fixture_update('admin@auth.test', **changes)
                self.redirect(self.browser.request('admin_dashboard'), 'login')
                self.redirect(self.browser.request('participante_dashboard'), 'login')


def main():
    global BASE_URL, STATE_PATH, BASE_STATE
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--php', default=shutil.which('php') or 'C:/xampp/php/php.exe')
    args = parser.parse_args()
    password_hash = subprocess.check_output([args.php, '-r',
        "echo password_hash('AuthTest!2026', PASSWORD_BCRYPT, ['cost' => 4]);"], text=True).strip()
    roles = {'admin': 11, 'expositor': 22, 'participante': 33, 'inactivo': 33, 'desconocido': 44}
    BASE_STATE = {
        'roles': [{'id_rol': i, 'nombre': name} for i, name in
                  ((11, 'ADMINISTRADOR'), (22, 'EXPOSITOR'), (33, 'PARTICIPANTE'), (44, 'DESCONOCIDO'))],
        'usuarios': [{'id_usuario': 910000 + index, 'ci': 'AUTH-' + role.upper(), 'nombres': 'Prueba ' + role,
                      'apellidos': 'Temporal', 'correo': role + '@auth.test', 'telefono': '',
                      'password_hash': password_hash, 'id_rol': role_id, 'activo': 0 if role == 'inactivo' else 1}
                     for index, (role, role_id) in enumerate(roles.items(), 1)],
    }
    with socket.socket() as sock:
        sock.bind(('127.0.0.1', 0))
        port = sock.getsockname()[1]
    BASE_URL = f'http://127.0.0.1:{port}'
    with tempfile.TemporaryDirectory(prefix='uab-auth-tests-') as directory:
        temp = Path(directory)
        STATE_PATH = temp / 'fixtures.json'
        STATE_PATH.write_text(json.dumps(BASE_STATE), encoding='utf-8')
        (temp / 'sessions').mkdir()
        environment = os.environ.copy()
        environment.update(UAB_AUTH_TEST_MODE='1', UAB_AUTH_TEST_STATE=str(STATE_PATH))
        with (temp / 'php-server.log').open('w+', encoding='utf-8') as log:
            server = subprocess.Popen([args.php, '-d', 'display_errors=1', '-d', 'log_errors=1',
                '-d', 'session.save_path=' + str(temp / 'sessions'), '-S', f'127.0.0.1:{port}',
                '-t', str(PROJECT / 'public'), str(PROJECT / 'tests' / 'auth_router.php')],
                cwd=PROJECT, env=environment, stdout=log, stderr=log,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0))
            try:
                readiness = urllib.request.build_opener(urllib.request.ProxyHandler({}))
                for _ in range(100):
                    if server.poll() is not None:
                        raise RuntimeError('PHP terminó antes de iniciar')
                    try:
                        with readiness.open(BASE_URL + '/__health', timeout=0.2) as response:
                            if response.read() == b'auth-test-ready':
                                break
                    except (urllib.error.URLError, TimeoutError):
                        time.sleep(0.05)
                else:
                    raise RuntimeError('PHP no respondió')
                result = unittest.TextTestRunner(verbosity=2).run(
                    unittest.defaultTestLoader.loadTestsFromTestCase(AuthHTTPTests))
                if not result.wasSuccessful():
                    log.flush()
                    log.seek(0)
                    print('\nÚltimas líneas del servidor PHP:\n' + '\n'.join(log.read().splitlines()[-35:]))
                return 0 if result.wasSuccessful() else 1
            finally:
                server.terminate()
                try:
                    server.wait(timeout=5)
                except subprocess.TimeoutExpired:
                    server.kill()
                    server.wait(timeout=5)


if __name__ == '__main__':
    raise SystemExit(main())
