<?php
namespace NextFW;

define("DS",DIRECTORY_SEPARATOR);
define("NS",__NAMESPACE__);
define("ROOT",__DIR__);

if(version_compare(phpversion(),"5.4","<")) exit("Для работы ".NS." необходима версия PHP не ниже 5.4");

// start:вывод ошибок
ini_set('display_errors','On');
ini_set('html_errors', 'Off');
error_reporting(-1);
// end:вывод ошибок

require_once 'engine/autoload.php';

$autoload = new Engine\Autoload(NS,ROOT);
$autoload->register();

$route = new Engine\Route(Config\Main::$global['action'],Config\Main::$global['bundle']);

$route->parse();
