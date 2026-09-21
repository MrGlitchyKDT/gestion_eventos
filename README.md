# Sistema de Gestión de Eventos Académicos y Certificaciones — UAB Eventos

Plataforma web integral para la administración, control de cupos en tiempo real, registro de asistencia por sesiones y emisión criptográfica de certificados académicos. Desarrollada bajo el patrón arquitectónico **MVC (Modelo-Vista-Controlador)** en **PHP puro orientado a objetos** y **MySQL**, con una interfaz moderna y adaptativa implementada en **Bootstrap 5.3**.

---

## Características Principales

* **Control de Acceso Basado en Roles (RBAC):** Espacios de trabajo y permisos independientes para `ADMINISTRADOR`, `EXPOSITOR` y `PARTICIPANTE`.
* **Transaccionalidad y Bloqueo Pesimista:** Control de cupos con concurrencia segura mediante `SELECT ... FOR UPDATE` en MySQL para evitar sobreinscripciones simultáneas.
* **Acreditación Criptográfica y Verificación Pública:** Emisión automatizada de certificados con folios y códigos únicos verificables mediante consulta pública o código QR sin requerir inicio de sesión.
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
| **Seguridad** | Hashing con `Bcrypt` (`password_hash`), Sentencias Preparadas contra SQLi |

---

## Inicio de sesión y acceso por rol

El formulario de acceso está en `public/index.php?action=login`. En XAMPP, con Apache y MySQL iniciados, se puede abrir en `http://localhost/gestion_eventos/public/index.php?action=login`.

La autenticación consulta `usuarios` y `roles` mediante PDO, valida la contraseña con `password_verify()` y requiere una cuenta activa. El destino se define por el nombre del rol en `AuthHelper`, sin depender de sus identificadores numéricos:

| Rol | Acción de destino | Módulo inicial |
| :--- | :--- | :--- |
| `ADMINISTRADOR` | `admin_dashboard` | Resumen administrativo |
| `EXPOSITOR` | `expositor_eventos` | Eventos asignados al expositor |
| `PARTICIPANTE` | `participante_dashboard` | Convocatorias abiertas e inscripciones |

* Las cuentas con roles no reconocidos no pueden iniciar sesión.
* Una sesión vigente vuelve a su módulo al abrir la raíz del sistema o las pantallas de acceso y registro.
* El estado y el rol se consultan nuevamente en cada petición al controlador frontal: la desactivación revoca el acceso y los cambios de rol actualizan los permisos.
* Los controladores verifican los roles permitidos. El administrador conserva el acceso adicional a las áreas de participante y expositor; estos dos roles no acceden al panel administrativo ni al área privada del otro.
* El registro público asigna exclusivamente `PARTICIPANTE`, resuelto desde la tabla `roles`, y verifica la confirmación de contraseña.
* Inicio de sesión, registro y cierre de sesión usan formularios POST con token CSRF. Para salir se utiliza **Cerrar sesión** desde el menú de perfil de la cabecera.
* PHP requiere las extensiones `pdo_mysql`, `mbstring` y `fileinfo`. La conexión se configura en `config/Database.php`.

### Pruebas de autenticación

Desde la carpeta `gestion_eventos`, con MySQL iniciado:

```powershell
python tests/auth_http.py --php C:/xampp/php/php.exe
```

La suite usa Python estándar y un servidor PHP local temporal. Comprueba los tres roles, permisos, credenciales inválidas, CSRF, cierre de sesión, registro y cambios de rol o estado durante una sesión. Requiere permiso para crear tablas temporales en la base configurada; utiliza cuentas sintéticas y una transacción de solo lectura para las tablas permanentes. Las sesiones y los datos de prueba se eliminan al terminar.

---

## Funcionalidad implementada por rol

### Administrador

El administrador inicia en `admin_dashboard`, reservado como resumen institucional. La operación diaria se concentra en **Gestión de Eventos** desde la cabecera.

* Crear y editar eventos en formularios modales con validación de datos, estado de publicación, cupo, fechas, modalidad y reglas de certificación.
* Cargar varios materiales al crear o editar un evento. Se aceptan `PDF`, `ZIP`, `RAR`, `PPTX`, `DOCX`, `XLSX` y `TXT`, hasta 20 MB por archivo.
* Gestionar los archivos ya publicados: descargarlos o eliminarlos desde el modal de edición.
* Asignar expositores activos a un evento, indicar su función y retirar asignaciones cuando sea necesario.
* Registrar varias sesiones por evento con título, fecha, horario y lugar. La fecha de cada sesión se valida contra el rango del evento; también se pueden eliminar sesiones.
* Configurar tipos y categorías de eventos. Los registros sin eventos asociados se eliminan; los que forman parte del historial se desactivan.
* Gestionar usuarios, roles, estados de cuentas, inscripciones administrativas y reportes CSV.
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
| `ADMINISTRADOR` | Inicio, Gestión de Eventos, Gestión de Usuarios, Reportes y perfil |
| `EXPOSITOR` | Mis eventos, Asistencia y perfil |
| `PARTICIPANTE` | Inicio, Seminarios, Certificados y perfil |

En móvil, los accesos se muestran en un panel lateral derecho. Los menús desplegables tienen transición visual, se cierran correctamente y la cabecera tiene una capa superior a tarjetas `sticky`, por lo que el menú de perfil y **Cerrar sesión** permanecen accesibles en cualquier vista.

## Eventos, sesiones y materiales

### Archivos adjuntos

Los archivos de apoyo no se guardan bajo `public`. La aplicación los almacena en `storage/materiales/`, que incluye una regla `.htaccess` de denegación directa. La tabla `materiales_evento` conserva el evento, usuario que subió el material, metadatos y nombre interno aleatorio. La ruta `descargar_material` comprueba la sesión y autorización antes de enviar el archivo como descarga.

### Cronograma

Las sesiones se persisten en `sesiones_evento` y se muestran en orden cronológico en el detalle público del evento. Cada una posee `titulo`, `fecha`, `hora_inicio`, `hora_fin`, `lugar_especifico` y `estado`.

### Asistencia y certificación

La tabla `asistencias` mantiene un único registro por combinación de sesión e inscripción. Las opciones `PRESENTE` y `JUSTIFICADO` cuentan como asistencia válida; `FALTA` no. Tras cada cambio, el sistema recalcula el porcentaje sobre las sesiones no canceladas y actualiza `porcentaje_asistencia` y `habilitado_certificado` en `inscripciones` según el mínimo configurado para el evento.

## Seguridad aplicada

* Contraseñas con `password_hash()` y validación con `password_verify()`.
* Sentencias PDO preparadas para las consultas con datos externos.
* Sesiones con regeneración de identificador al autenticarse, cookies `HttpOnly`, `SameSite=Lax` y verificación del rol y estado de cuenta en cada petición.
* Tokens CSRF para acceso, registro, cierre de sesión y operaciones administrativas. La API de asistencia también exige el token.
* Validación de tipo por extensión y MIME, tamaño máximo y nombre interno aleatorio para archivos adjuntos.
* Verificación de pertenencia del participante a la sesión y de la asignación del expositor antes de guardar asistencia.

## Rutas destacadas

| Acción (`public/index.php?action=...`) | Uso |
| :--- | :--- |
| `admin_eventos` | Crear, editar y administrar eventos, materiales, sesiones y expositores |
| `admin_evento_asignar_expositor` / `admin_evento_desasignar_expositor` | Gestionar responsables académicos del evento |
| `admin_evento_crear_sesion` / `admin_evento_eliminar_sesion` | Gestionar el cronograma |
| `admin_reportes` | Exportaciones, las 10 actividades administrativas más recientes y el consolidado activo |
| `admin_auditoria` | Consultar el historial administrativo completo con paginación |
| `descargar_material` | Descarga protegida de un material de evento |
| `expositor_asistencia` | Planilla filtrable de asistencia |
| `api_guardar_asistencia` | Endpoint JSON para marcar asistencia |
| `expositor_exportar_asistencia` | Informe CSV de asistencia por evento |

## Estructura del Repositorio

```text
gestion_eventos/
├── config/
│   └── Database.php                 # Conexión persistente PDO (Patrón Singleton)
├── controllers/
│   ├── AdminController.php          # KPIs, gestión de usuarios, roles y auditoría
│   ├── AuthController.php           # Login, registro, sesiones y redirección por rol
│   ├── CertificadoController.php     # Generación, impresión y verificación de certificados
│   ├── EventoController.php         # Eventos, materiales, sesiones y expositores
│   ├── ExpositorController.php      # Planilla, API e informes de asistencia
│   └── ReporteController.php        # Consultas analíticas y exportación nativa a CSV
├── helpers/
│   └── AuthHelper.php               # Middleware de autenticación y verificación RBAC
├── models/
│   ├── Asistencia.php               # Registro de sesiones y cálculo de porcentajes
│   ├── Auditoria.php                # Bitácora inmutable de eventos del sistema
│   ├── Certificado.php              # Folios criptográficos y validación de autenticidad
│   ├── Evento.php                   # Consultas de eventos y cupos disponibles
│   ├── Inscripcion.php              # Transacciones con bloqueo pesimista de plazas
│   ├── Material.php                 # Almacenamiento privado y acceso a materiales
│   ├── SesionEvento.php             # Cronograma de sesiones por evento
│   ├── SolicitudReimpresion.php     # Flujo administrativo de solicitudes de reimpresión
│   └── Usuario.php                  # Perfiles, contraseñas y estado de cuentas
├── public/
│   ├── assets/
│   │   └── css/
│   │       └── styles.css           # Hoja de estilos institucional unificada
│   └── index.php                    # Front Controller y enrutador central
├── storage/
│   └── materiales/                  # Archivos privados, fuera del acceso público
├── tests/
│   ├── auth_http.py                 # Pruebas HTTP de autenticación y roles
│   └── auth_router.php              # Asistente de rutas para las pruebas
├── views/
│   ├── admin/                       # Dashboard, gestión de usuarios, eventos y reportes
│   ├── auth/                        # Vistas de autenticación y registro
│   ├── expositor/                   # Planillas de asistencia por sesión
│   ├── layouts/                     # Header, navbar, footer y sidebar responsivo
│   ├── participante/                # Dashboard de convocatorias y mis inscripciones
│   ├── publico/                     # Catálogo general y verificación de folios
│   └── usuario/                     # Perfil personal y cambio de contraseña
├── index.php                        # Redireccionador raíz hacia public/index.php
└── README.md                        # Documentación técnica del proyecto
```
