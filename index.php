<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
define('PATH_ROOT', __DIR__);
// Autoload class trong PHP
// spl_autoload_register(function (string $class_name) {
//     include_once PATH_ROOT . '/' . $class_name . '.php';
// });
// load class Route
// $filepath = realpath (dirname(__FILE__));
// require_once($filepath."/lib/database.php");
require_once('vendor/autoload.php');

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__)->safeLoad();
require_once 'core/http/Route.php';
require_once 'lib/database.php';

use Core\Http\Route;
use Database\DB;
$router = new Route();
$DB = new DB();
require  'routes/route.php';
// Lấy url hiện tại của trang web. Mặc định la /
$request_url = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
// Lấy phương thức hiện tại của url đang được gọi. (GET | POST). Mặc định là GET.
$method_url = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
// map URL
$router->map($request_url, $method_url);
