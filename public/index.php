<?php
session_start();
if (!isset($_SESSION['perfil'])){
    $_SESSION['perfil'] = 'invitado';
    $_SESSION['user'] = null;
}

require_once "../vendor/autoload.php";
require_once "../bootstrap.php";

use App\Controllers\BlogController;
use App\Controllers\IndexController;
use Aura\Router\RouterContainer as router;
use App\Controllers\UserController;
use App\Controllers\AuthController;

// session_start();

// if (!isset($_SESSION['perfil'])) {
//     $_SESSION['user'] = 'Invitado';
//     $_SESSION['perfil'] = "Invitado";
// }

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$router = new Router();
$rutas = $router->getMap();
// ******************************* RUTAS PÚBLICAS *********************************
$rutas->get("Inicio", "/", [IndexController::class, "indexAction"]);
$rutas->get("Mostrar el sobre la web", "/about", [IndexController::class, "aboutAction"]);
$rutas->get("Mostrar el contacto de la web", "/contactos", [IndexController::class, "contactAction"]);
$rutas->get("Mostrar detalles del blog", "/showPost", [BlogController::class, "showPostAction"]);
$rutas->post("Agregar comentario", "/postComment", [BlogController::class, "addCommentAction"]);

// ******************************* RUTAS INVITADO *********************************
$rutas->post("addUser", "/register", [UserController::class, "userAction", 'auth' => "invitado"]);
$rutas->get("formuser", "/register", [UserController::class, "userAction", 'auth' => "invitado"]);
$rutas->post("Accion de iniciar sesión", "/login", [AuthController::class, "loginAction", 'auth' => "invitado"]);
$rutas->get("Mostrar el iniciar sesión", "/login", [AuthController::class, "loginAction", 'auth' => "invitado"]);

// ******************************* RUTAS DE USUARIO *********************************
$rutas->get("Mostrar el añadir blog", "/blog", [BlogController::class, "blogsAction", 'auth' => "usuario"]);
$rutas->post("Accion de añadir blog", "/blog", [BlogController::class, "blogsAction", 'auth' => "usuario"]);
$rutas->get("admin", "/admin", [IndexController::class, "adminAction", 'auth' => "usuario"]);
$rutas->get("Cerrar sesión", "/logout", [AuthController::class, "logoutAction", 'auth' => "usuario"]);



$route = $router->getMatcher()->match($request);

if (!$route) {
    exit(http_response_code(404));
}

$handler = $route->handler;
if (isset($handler['auth'])){
    $needsAuth = $handler['auth'];
} else{
    $needsAuth = false;
}

// Si la ruta necesita autenticación y el usuario no está logeado, redirigir al login
if ($needsAuth && $needsAuth !== $_SESSION['perfil']) {
    header("Location: /");
    exit;
}

$controller = new $handler[0];
$action = $handler[1];
$response = $controller->$action($request);
echo $response->getBody();
