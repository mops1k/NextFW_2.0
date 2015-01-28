<?php
namespace NextFW;

use NextFW\Config\Main;

define("DS",DIRECTORY_SEPARATOR);
define("NS",__NAMESPACE__);
define("ROOT",__DIR__);

if(version_compare(phpversion(),"5.4","<")) exit("Для работы ".NS." необходима версия PHP не ниже 5.4");

require_once 'engine/autoload.php';

$autoload = new Engine\Autoload(NS,ROOT);
$autoload->register();

// start:вывод ошибок
if(Main::$global['status'] == 'development') {
    ini_set('display_errors','On');
    ini_set('html_errors', 'Off');
    error_reporting(-1);
}
if(Main::$global['status'] == 'production') {
    ini_set('display_errors','Off');
    ini_set('html_errors', 'Off');
    error_reporting(0);
}
// end:вывод ошибок

ob_start();
register_shutdown_function(function() { $error = new Engine\Error(); $error->fatal_error_handler(); });
set_exception_handler(["NextFW\\Engine\\Error","exception_handler"]);
set_error_handler(["NextFW\\Engine\\Error","error_handler"]);


$route = new Engine\Route(Config\Main::$global['action'],Config\Main::$global['bundle']);

$route->parse();
