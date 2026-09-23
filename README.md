# Sistema de Gestión de Eventos Académicos y Certificaciones — UAB Eventos

Plataforma web integral para administrar eventos académicos, controlar cupos, registrar asistencia por sesiones y emitir certificados digitales verificables. Está desarrollada con el patrón **MVC (Modelo-Vista-Controlador)** en **PHP orientado a objetos**, **MySQL/MariaDB** y **Bootstrap 5.3**.

---

## Características Principales

* **Control de Acceso Basado en Roles (RBAC):** Espacios de trabajo y permisos independientes para `ADMINISTRADOR`, `EXPOSITOR` y `PARTICIPANTE`.
* **Transaccionalidad y Bloqueo Pesimista:** Control de cupos con concurrencia segura mediante `SELECT ... FOR UPDATE` en MySQL para evitar sobreinscripciones simultáneas.
* **Certificados PDF por plantilla:** El administrador carga una plantilla PDF para cada evento, ajusta visualmente los campos y emite documentos individuales con folio y QR verificable.
* **Asistencia Modular por Sesiones:** Registro de presencias por fecha y hora con recálculo automático del porcentaje de asistencia y validación contra el umbral mínimo del evento.
* **Motor Analítico y Reportería:** Exportación de padrones, listas de asistencia y eventos a archivos CSV compatibles con Microsoft Excel mediante streaming HTTP y codificación UTF-8 BOM.
* **Diseño Responsivo e Institucional:** Cabecera azul institucional unificada, navegación horizontal por rol y menú lateral `offcanvas` en móvil. Los modales de eventos conservan sus acciones visibles y permiten desplazamiento en pantallas reducidas.

---

## Stack Tecnológico

| Componente | Detalle Técnico |
| :--- | :--- |
| **Lenguaje Backend** | PHP 8.1+ (Tipado estricto, Programación Orientada a Objetos) |
| **Base de Datos** | MySQL 8.0 / MariaDB 10.4+ (Motor InnoDB, Llaves Foráneas) |
| **Capa Frontend** | HTML5, Bootstrap 5.3, Bootstrap Icons |
| **Tipografía** | Google Fonts (*Poppins* y *Open Sans*) |
| **Arquitectura** | MVC Nativo + Front Controller + Singleton PDO |
| **PDF** | `setasign/fpdf` y `setasign/fpdi`, administrados con Composer |
| **Seguridad** | Hashing con `Bcrypt` (`password_hash`), CSRF, RBAC y sentencias preparadas PDO |

---

## Inicio de sesión y acceso por rol

El formulario de acceso está en `public/index.php?action=login`. En XAMPP, con Apache y MySQL iniciados, se puede abrir en `http://localhost/gestion_eventos/public/index.php?action=login`.

La autenticación consulta `usuarios` y `roles` mediante PDO, valida la contraseña con `password_verify()` y requiere una cuenta activa. El destino se define por el nombre del rol en `AuthHelper`, sin depender de sus identificadores numéricos:

| Rol | Acción de destino | Módulo inicial |
| :--- | :--- | :--- |
| `ADMINISTRADOR` | `admin_dashboard` | Inicio administrativo y accesos a módulos de gestión |
| `EXPOSITOR` | `expositor_eventos` | Eventos asignados al expositor |
| `PARTICIPANTE` | `participante_dashboard` | Convocatorias abiertas e inscripciones |

* Las cuentas con roles no reconocidos no pueden iniciar sesión.
* Una sesión vigente vuelve a su módulo al abrir la raíz del sistema o las pantallas de acceso y registro.
* El estado y el rol se consultan nuevamente en cada petición al controlador frontal: la desactivación revoca el acceso y los cambios de rol actualizan los permisos.
* Los controladores verifican los roles permitidos. El administrador conserva el acceso adicional a las áreas de participante y expositor; estos dos roles no acceden al panel administrativo ni al área privada del otro.
* El registro público asigna exclusivamente `PARTICIPANTE`, resuelto desde la tabla `roles`, y verifica la confirmación de contraseña.
* Inicio de sesión, registro y cierre de sesión usan formularios POST con token CSRF. Para salir se utiliza **Cerrar sesión** desde el menú de perfil de la cabecera.
* PHP requiere las extensiones `pdo_mysql`, `mbstring` y `fileinfo`. La conexión se configura en `config/Database.php`.
* Las dependencias PHP se instalan desde el archivo `composer.json`. En un entorno nuevo, ejecute `composer install` desde la raíz del proyecto.

### Pruebas de autenticación

Desde la carpeta `gestion_eventos`, con MySQL iniciado:

```powershell
python tests/auth_http.py --php C:/xampp/php/php.exe
```

La suite usa Python estándar y un servidor PHP local temporal. Comprueba los tres roles, permisos, credenciales inválidas, CSRF, cierre de sesión, registro y cambios de rol o estado durante una sesión. Requiere permiso para crear tablas temporales en la base configurada; utiliza cuentas sintéticas y una transacción de solo lectura para las tablas permanentes. Las sesiones y los datos de prueba se eliminan al terminar.

---

## Funcionalidad implementada por rol

### Administrador

El administrador inicia en `admin_dashboard`, una pantalla breve de acceso a los módulos. La operación diaria se concentra en **Gestión de Eventos** desde la cabecera.

* Crear y editar eventos en formularios modales con validación de datos, estado de publicación, cupo, fechas, modalidad y reglas de certificación.
* Cargar varios materiales al crear o editar un evento. Se aceptan `PDF`, `ZIP`, `RAR`, `PPTX`, `DOCX`, `XLSX` y `TXT`, hasta 20 MB por archivo.
* Gestionar los archivos ya publicados: descargarlos o eliminarlos desde el modal de edición.
* Asignar expositores activos a un evento, indicar su función y retirar asignaciones cuando sea necesario.
* Registrar varias sesiones por evento con título, fecha, horario y lugar. La fecha de cada sesión se valida contra el rango del evento; también se pueden eliminar sesiones.
* Configurar tipos y categorías de eventos. Los registros sin eventos asociados se eliminan; los que forman parte del historial se desactivan.
* Gestionar usuarios, roles, estados de cuentas, inscripciones administrativas y reportes CSV.
* Cargar una plantilla PDF por evento, editar visualmente las posiciones de sus campos y emitir o regenerar certificados PDF para participantes habilitados.
* Consultar los diez últimos registros de auditoría en Reportes y abrir una vista paginada con el historial administrativo completo.
* Las operaciones relevantes de eventos, materiales, sesiones y asignaciones de expositores se registran en `auditoria_actividad`.

### Participante

* Explorar convocatorias publicadas, aplicar filtros y completar una inscripción dentro del periodo habilitado y del cupo disponible.
* Consultar sus inscripciones, perfil y certificados.
* Ver el cronograma de sesiones y los expositores en el detalle de cada evento.
* Descargar materiales sólo si tiene una inscripción activa o completada en el evento (`INSCRITO`, `ASISTIO`, `APROBADO` o `REPROBADO`).

### Expositor

* Consultar directamente sus eventos asignados desde la cabecera.
* Abrir la planilla de asistencia y filtrar por evento y sesión.
* Marcar a cada participante como **Presente**, **Justificado** o **Falta** sin recargar la página.
* Imprimir la planilla actual o exportar un CSV detallado de todas las sesiones del evento.
* El expositor sólo puede consultar, registrar y exportar la asistencia de eventos que le fueron asignados.

## Navegación y experiencia responsive

La interfaz emplea una cabecera azul institucional común a las áreas autenticadas.

| Rol | Accesos principales en la cabecera |
| :--- | :--- |
| `ADMINISTRADOR` | Inicio, Gestión de Eventos, Gestión de Usuarios, Reportes, Configuración y perfil |
| `EXPOSITOR` | Mis eventos, Asistencia y perfil |
| `PARTICIPANTE` | Inicio, Seminarios, Certificados y perfil |

En móvil, los accesos se muestran en un panel lateral derecho. Los menús desplegables tienen transición visual y se cierran correctamente. La cabecera se mantiene sobre el contenido ordinario, pero queda debajo de los modales de Bootstrap para que los formularios de eventos y el editor de certificados cubran correctamente la pantalla.

## Eventos, sesiones y materiales

### Archivos adjuntos

Los archivos de apoyo no se guardan bajo `public`. La aplicación los almacena en `storage/materiales/`, que incluye una regla `.htaccess` de denegación directa. La tabla `materiales_evento` conserva el evento, usuario que subió el material, metadatos y nombre interno aleatorio. La ruta `descargar_material` comprueba la sesión y autorización antes de enviar el archivo como descarga.

### Cronograma

Las sesiones se persisten en `sesiones_evento` y se muestran en orden cronológico en el detalle público del evento. Cada una posee `titulo`, `fecha`, `hora_inicio`, `hora_fin`, `lugar_especifico` y `estado`.

### Asistencia y certificación

La tabla `asistencias` mantiene un único registro por combinación de sesión e inscripción. Las opciones `PRESENTE` y `JUSTIFICADO` cuentan como asistencia válida; `FALTA` no. Tras cada cambio, el sistema recalcula el porcentaje sobre las sesiones no canceladas y actualiza `porcentaje_asistencia` y `habilitado_certificado` en `inscripciones` según el mínimo configurado para el evento.

### Certificados con plantilla PDF

La tabla `plantillas_certificado` contiene una plantilla activa por evento, su página de trabajo, el usuario que la cargó y `configuracion_campos`. La plantilla y los certificados finales se almacenan fuera de `public`, en `storage/plantillas_certificado/` y `storage/certificados/`.

El flujo administrativo es el siguiente:

1. Desde el botón de diploma de **Gestión de Eventos**, el administrador abre la gestión de certificados del evento.
2. Carga un PDF de hasta 10 MB y selecciona la página que se utilizará como fondo.
3. Abre **Editar posiciones** y arrastra las etiquetas de nombre, CI, evento, horas, fechas, folio, URL y QR sobre la plantilla.
4. Al guardar, se persisten las coordenadas y el tamaño real de la página PDF. Las configuraciones antiguas se mantienen compatibles y se migran al guardar de nuevo el diseño.
5. Sólo se habilita la emisión para inscripciones activas que tienen `habilitado_certificado = 1`.
6. FPDI importa la página de plantilla; FPDF superpone los datos, el folio y el QR. La ruta generada se guarda en `certificados.ruta_archivo_pdf` y la URL pública de verificación en `codigo_qr`.
7. Los certificados ya emitidos se pueden abrir, imprimir o regenerar con el diseño actualizado. La descarga está autorizada únicamente para el administrador o el titular.

La validación pública se mantiene en `verificar_certificado` mediante `codigo_unico` y el contenido del QR. La anulación conserva el historial en la tabla `certificados`.

## Seguridad aplicada

* Contraseñas con `password_hash()` y validación con `password_verify()`.
* Sentencias PDO preparadas para las consultas con datos externos.
* Sesiones con regeneración de identificador al autenticarse, cookies `HttpOnly`, `SameSite=Lax` y verificación del rol y estado de cuenta en cada petición.
* Tokens CSRF para acceso, registro, cierre de sesión y operaciones administrativas. La API de asistencia también exige el token.
* Validación de tipo por extensión y MIME, tamaño máximo y nombre interno aleatorio para archivos adjuntos.
* Verificación de pertenencia del participante a la sesión y de la asignación del expositor antes de guardar asistencia.
* Plantillas y certificados PDF fuera del directorio público; el administrador es el único rol que puede ver la plantilla y editar sus posiciones.
* La emisión verifica que el evento tenga plantilla activa, que emita certificados y que el participante esté habilitado.

## Rutas destacadas

| Acción (`public/index.php?action=...`) | Uso |
| :--- | :--- |
| `admin_eventos` | Crear, editar y administrar eventos, materiales, sesiones y expositores |
| `admin_evento_asignar_expositor` / `admin_evento_desasignar_expositor` | Gestionar responsables académicos del evento |
| `admin_evento_crear_sesion` / `admin_evento_eliminar_sesion` | Gestionar el cronograma |
| `admin_certificados` | Cargar plantilla, editar posiciones, emitir y consultar certificados de un evento |
| `admin_plantilla_certificado_guardar` | Subir o reemplazar una plantilla PDF |
| `admin_plantilla_certificado_diseno` | Guardar posiciones del editor visual |
| `admin_plantilla_certificado_ver` | Vista privada del PDF para el editor visual |
| `admin_certificado_emitir_pdf` / `admin_certificado_regenerar_pdf` | Emitir o regenerar un certificado con la plantilla activa |
| `descargar_certificado` | Abrir o imprimir el PDF autorizado del certificado |
| `admin_reportes` | Exportaciones, las 10 actividades administrativas más recientes y el consolidado activo |
| `admin_auditoria` | Consultar el historial administrativo completo con paginación |
| `descargar_material` | Descarga protegida de un material de evento |
| `expositor_asistencia` | Planilla filtrable de asistencia |
| `api_guardar_asistencia` | Endpoint JSON para marcar asistencia |
| `expositor_exportar_asistencia` | Informe CSV de asistencia por evento |

## Estructura del Repositorio

```text
gestion_eventos/
├── composer.json                    # Dependencias de generación PDF
├── composer.lock                    # Versiones reproducibles de dependencias
├── config/
│   └── Database.php                 # Conexión persistente PDO (Patrón Singleton)
├── controllers/
│   ├── AdminController.php          # KPIs, gestión de usuarios, roles y auditoría
│   ├── AuthController.php           # Login, registro, sesiones y redirección por rol
│   ├── CertificadoController.php     # Plantillas, emisión PDF, descarga, anulación y verificación
│   ├── EventoController.php         # Eventos, materiales, sesiones y expositores
│   ├── ExpositorController.php      # Planilla, API e informes de asistencia
│   └── ReporteController.php        # Consultas analíticas y exportación nativa a CSV
├── database/
│   └── gestion_eventos_db.sql       # Volcado estructural y datos de referencia de MariaDB
├── helpers/
│   ├── AuthHelper.php               # Middleware de autenticación y verificación RBAC
│   └── CertificadoPdfService.php     # Composición de plantilla, textos y QR con FPDI/FPDF
├── models/
│   ├── Asistencia.php               # Registro de sesiones y cálculo de porcentajes
│   ├── Auditoria.php                # Bitácora inmutable de eventos del sistema
│   ├── Certificado.php              # Folios, habilitados, documentos y validación de autenticidad
│   ├── Evento.php                   # Consultas de eventos y cupos disponibles
│   ├── Inscripcion.php              # Transacciones con bloqueo pesimista de plazas
│   ├── Material.php                 # Almacenamiento privado y acceso a materiales
│   ├── PlantillaCertificado.php     # Carga, consulta y medidas reales de plantillas PDF
│   ├── SesionEvento.php             # Cronograma de sesiones por evento
│   ├── SolicitudReimpresion.php     # Flujo administrativo de solicitudes de reimpresión
│   └── Usuario.php                  # Perfiles, contraseñas y estado de cuentas
├── public/
│   ├── assets/
│   │   └── css/
│   │       └── styles.css           # Hoja de estilos institucional unificada
│   └── index.php                    # Front Controller y enrutador central
├── storage/
│   ├── materiales/                  # Archivos de apoyo privados
│   ├── plantillas_certificado/      # Plantillas PDF privadas por evento
│   └── certificados/                # PDFs finales emitidos
├── tests/
│   ├── auth_http.py                 # Pruebas HTTP de autenticación y roles
│   └── auth_router.php              # Asistente de rutas para las pruebas
├── views/
│   ├── admin/                       # Inicio, usuarios, eventos, certificados, reportes y configuración
│   ├── auth/                        # Vistas de autenticación y registro
│   ├── expositor/                   # Planillas de asistencia por sesión
│   ├── layouts/                     # Header, navbar, footer y sidebar responsivo
│   ├── participante/                # Dashboard de convocatorias y mis inscripciones
│   ├── publico/                     # Catálogo general y verificación de folios
│   └── usuario/                     # Perfil personal y cambio de contraseña
├── index.php                        # Redireccionador raíz hacia public/index.php
└── README.md                        # Documentación técnica del proyecto
```
