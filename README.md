# Abonos de fútbol (MVC en PHP nativo)
 
Sistema web para la gestión de abonos de un club de fútbol (UD Almería), desarrollado en **PHP nativo**, aplicando una arquitectura **MVC hecha a mano** (sin ningún framework).
 
La aplicación permite a los usuarios registrarse, iniciar sesión y comprar un abono de temporada seleccionando el tipo (Tribuna, Preferencia o Fondo). El sistema genera automáticamente un código de asiento único y aplica descuentos según la edad del abonado. Los usuarios, solo de rol **administrador**, pueden acceder a un listado completo de todos los abonos vendidos.

Este proyecto forma parte de una serie de tres versiones de la misma aplicación:
 
- [`Abonos_de_futbol`](https://github.com/Desire-e/Abonos_de_futbol) versión en Laravel
- [`Abonos_futbol_PHP_v0`](https://github.com/Desire-e/Abonos_de_futbol_PHP_v0) versión en PHP puro, sin patrón MVC

Desarrollado como práctica académica para trabajar el patrón MVC, el enrutamiento centralizado, el autoload con namespaces y el uso del patrón Singleton en PHP nativo, sin apoyarse en ningún framework.

---
## Funcionalidades
 
- **Enrutador central** (`index.php`): único punto de entrada de la aplicación. Recibe `controller` y `action` por GET, valida que el método exista en el controlador correspondiente y delega la petición — evita el acceso directo a modelos o vistas.
- **Registro e inicio de sesión** con control de acceso, contraseñas cifradas (`password_hash` / `password_verify`) y cierre de sesión.
- **Compra de abonos**:
  - Validación de formulario (nombre, DNI con letra de control, fecha de nacimiento, teléfono, IBAN).
  - Cálculo automático de descuentos (menores de 12 años y jubilados).
  - Generación de un código de asiento único, comprobando que no exista ya en la base de datos.
  - Autocompletado de campos mediante cookies de la última compra.
  - Ticket final con los datos del abono comprado.
- **Listado de abonos** (solo administrador): tabla con todos los abonos registrados.

---
## Arquitectura
 
- **Enrutador** (`index.php`): interpreta la URL (`?controller=...&action=...`) y llama al método correspondiente del controlador.
- **Controladores** (`Controllers/`): coordinan modelo y vista. Son clases estáticas, sin estado propio; reciben la petición, llaman al modelo y deciden qué vista renderizar o a dónde redirigir.
- **Modelos** (`Models/`): contienen toda la lógica de negocio y el acceso a datos (validación, cálculos, consultas SQL mediante PDO con sentencias preparadas).
- **Vistas** (`Views/`): plantillas PHP con el HTML de cada página, cargadas mediante buffering de salida (`ob_start()` / `ob_get_clean()`) e insertadas dentro de una plantilla común (`Views/plantillas/main.php`).
- **Autoload** (`Autoload.php`): registra un autoloader con `spl_autoload_register()` que traduce el namespace de cada clase (`Controllers\`, `Models\`, `Database\`) a su ruta de archivo, evitando `require_once` manuales.
- **Conexión a BD** (`Database/ConexionBD.php`): implementa el patrón **Singleton**, garantizando una única instancia de conexión PDO reutilizada por todos los modelos.

---
## Tecnologías
 
- PHP (nativo, con namespaces y autoload — sin framework)
- MySQL / MariaDB
- PDO (Prepared Statements)
- HTML5 + CSS3
- Sesiones y cookies de PHP

---
## Estructura del proyecto
 
```
├── index.php                   # Enrutador (punto de entrada)
├── Autoload.php                # Autoload de clases por namespace
├── config.php                  # Credenciales de acceso a la BBDD
├── uda.sql                     # Script de creación e inicialización de la base de datos
├── Controllers/
│   ├── AbonosController.php
│   └── UsuariosController.php
├── Models/
│   ├── AbonoModel.php
│   ├── TipoAbonoModel.php
│   └── UsuarioModel.php
├── Database/
│   └── ConexionBD.php          # Singleton de conexión PDO
└── Views/
    ├── plantillas/
    │   └── main.php            # Plantilla común (header + $content)
    ├── Abonos/
    │   ├── CompraView.php
    │   ├── TicketView.php
    │   ├── ListadoView.php
    │   └── AlertaAccesoView.php
    ├── Usuarios/
    │   └── LoginView.php
    ├── css/
    └── img/
```

---
## Instalación
 
### Requisitos
 
- PHP 7.4 o superior (con extensión PDO habilitada)
- MySQL o MariaDB
- Servidor local tipo XAMPP, WAMP, Laragon o similar

### Pasos
 
1. Clona el repositorio dentro de la carpeta de tu servidor local (por ejemplo, `htdocs` en XAMPP):
```bash
   git clone https://github.com/tu-usuario/abonos-futbol-mvc.git
```
 
2. Crea la base de datos importando el script `uda.sql` (por ejemplo, desde phpMyAdmin o por consola).
 
3. Configura el acceso a la base de datos. Copia el archivo de ejemplo y ajusta tus credenciales:
 
```php
   define('BD_HOST', 'localhost');
   define('BD_NAME', 'uda');
   define('BD_USER', 'tu_usuario');
   define('BD_PASSWORD', 'tu_contraseña');
```
 
4. Accede a la aplicación siempre a través del enrutador, por ejemplo:
```
   http://localhost/abonos-futbol-mvc/index.php?controller=abonos&action=compra
```

---
## Usuarios de prueba
 
El script `uda.sql` crea dos usuarios de ejemplo (el usuario `uda` tiene rol de administrador; el resto, rol normal):

**usuario normal**: antonio40 **contraseña**: contrasena
**usuario administrador**: uda **contraseña**: 1234
 
--- 
## Autor

**Desire-e** — [GitHub](https://github.com/Desire-e)
