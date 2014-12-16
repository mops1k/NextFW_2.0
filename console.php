#!/usr/bin/php
<?php
namespace NextFW;

use NextFW\Engine as Engine;

define("DS",DIRECTORY_SEPARATOR);
define("NS",__NAMESPACE__);
define("NS_SEP", "\\");
define("ROOT", __DIR__);

require_once 'engine/autoload.php';

$load = new Engine\Autoload(NS, ROOT);
$load->register();

$console = new Engine\Console();
$file = new Engine\IO();

$console->writeLn("Welcome to NextFW console tool.");

if($argc <= 1)
{
    $console->write("
1) Create bundle
2) Create controller
3) Create module
");
    $action = $console->ioRead("Choose [1]: ",1);

    $templateClass = <<<TPL
<?php
namespace {namespace};

use NextFW;
use NextFW\Engine as Engine;

class {class} extends {extends} {
    function {action}(\$args = null) {
        // Enter code below:
    }
}
TPL;
    $templateConfig = <<<TPL
<?php
namespace {namespace};

class Main {
    static \$tpl = array(
        "engine" => "RainTPL",
        "base_url" => null
    );
}
TPL;
    $templatePreload = <<<TPL
<?php
namespace {namespace};

class Preload {
    static function init(\$obj)
    {
        \$tpl = "\\\\".Main::\$tpl['engine'];
        \$tpl::configure("base_url", Main::\$tpl['base_url'] );
        \$tpl::configure("tpl_dir", \$obj->bundlePath.DS."view".DS."default".DS );
        \$tpl::configure("cache_dir", "cache".DS );

        //initialize a Rain TPL object
        \$tplObj = new \$tpl;

        return \$tplObj;
    }
}
TPL;

    switch($action) {
        case 1:
            $bundleName = ucfirst($console->readRecursive("Enter bundle name: "));
            if(!preg_match("#^[a-z0-9]+$#i",$bundleName)) exit("Wrong bundle name!\n");

            $controllerName = ucfirst($console->readRecursive("Enter controller name: "));
            if(!preg_match("#^[a-z0-9]+$#i",$controllerName)) exit("Wrong controller name!\n");

            $actionName = strtolower($console->ioRead("Enter action name [index]: ","index"));
            if(!preg_match("#^[a-z0-9]+$#i",$actionName)) exit("Wrong action name!\n");

            try {
                $bundleDir = ROOT.DS.strtolower("bundles".DS.$bundleName).DS;

                $file->mkDir($bundleDir);
                $file->mkDir($bundleDir."config");
                $file->mkDir($bundleDir."controller");
                $file->mkDir($bundleDir."module");
                $file->mkDir($bundleDir."view");
                $file->mkDir($bundleDir."view".DS."default");

                $namespace = __NAMESPACE__.NS_SEP."Bundles".NS_SEP.$bundleName;

                $controller = str_replace("{namespace}",$namespace.NS_SEP."Controller",$templateClass);
                $controller = str_replace("{class}",$controllerName,$controller);
                $controller = str_replace("{extends}","Engine\\Controller",$controller);
                $controller = str_replace("{action}",$actionName,$controller);

                $file->fileSave($controller,$bundleDir.strtolower("controller".DS.$controllerName.".php"));

                $config = str_replace("{namespace}",$namespace.NS_SEP."Config",$templateConfig);
                $preload = str_replace("{namespace}",$namespace.NS_SEP."Config",$templatePreload);

                $file->fileSave($config,$bundleDir."config".DS."main.php");
                $file->fileSave($preload,$bundleDir."config".DS."preload.php");

                $module = str_replace("{namespace}",$namespace.NS_SEP."Module",$templateClass);
                $module = str_replace("{class}",$controllerName,$module);
                $module = str_replace("{extends}","Engine\\Module",$module);
                $module = str_replace("{action}",$actionName,$module);

                $file->fileSave($module,$bundleDir.strtolower("module".DS.$controllerName.".php"));

            } catch (Engine\IOException $e) {
                $console->writeLn($e->getMessage());
            }

            $console->writeLn();
            $console->writeLn("Bundle <$bundleName> created sucessfully!");
            break;
        case 2:
            $bundleName = ucfirst($console->readRecursive("Enter bundle name: "));
            if(!preg_match("#^[a-z0-9]+$#i",$bundleName)) exit("Wrong bundle name!\n");

            $controllerName = ucfirst($console->readRecursive("Enter controller name: "));
            if(!preg_match("#^[a-z0-9]+$#i",$controllerName)) exit("Wrong controller name!\n");

            $actionName = strtolower($console->ioRead("Enter action name [index]: ","index"));
            if(!preg_match("#^[a-z0-9]+$#i",$actionName)) exit("Wrong action name!\n");

            try {
                $bundleDir = ROOT.DS.strtolower("bundles".DS.$bundleName).DS;

                $namespace = __NAMESPACE__.NS_SEP."Bundles".NS_SEP.$bundleName;

                $controller = str_replace("{namespace}",$namespace.NS_SEP."Controller",$templateClass);
                $controller = str_replace("{class}",$controllerName,$controller);
                $controller = str_replace("{extends}","Engine\\Controller",$controller);
                $controller = str_replace("{action}",$actionName,$controller);

                $file->fileSave($controller,$bundleDir.strtolower("controller".DS.$controllerName.".php"));

                $module = str_replace("{namespace}",$namespace.NS_SEP."Module",$templateClass);
                $module = str_replace("{class}",$controllerName,$module);
                $module = str_replace("{extends}","Engine\\Module",$module);
                $module = str_replace("{action}",$actionName,$module);

                $file->fileSave($module,$bundleDir.strtolower("module".DS.$controllerName.".php"));

                $console->writeLn();
                $console->writeLn("Controller <$controllerName> for Bundle <$bundleName> created sucessfully!");
            } catch (Engine\IOException $e) {
                $console->writeLn($e->getMessage());
            }
            break;
        case 3:
            $bundleName = ucfirst($console->readRecursive("Enter bundle name: "));
            if(!preg_match("#^[a-z0-9]+$#i",$bundleName)) exit("Wrong bundle name!\n");

            $moduleName = ucfirst($console->readRecursive("Enter module name: "));
            if(!preg_match("#^[a-z0-9]+$#i",$moduleName)) exit("Wrong controller name!\n");

            $methodName = strtolower($console->ioRead("Enter method name [index]: ","index"));
            if(!preg_match("#^[a-z0-9]+$#i",$methodName)) exit("Wrong action name!\n");

            try {
                $bundleDir = ROOT.DS.strtolower("bundles".DS.$bundleName).DS;

                $namespace = __NAMESPACE__.NS_SEP."Bundles".NS_SEP.$bundleName;

                $module = str_replace("{namespace}",$namespace.NS_SEP."Module",$templateClass);
                $module = str_replace("{class}",$moduleName,$module);
                $module = str_replace("{extends}","Engine\\Module",$module);
                $module = str_replace("{action}",$moduleName,$module);

                $file->fileSave($module,$bundleDir.strtolower("module".DS.$moduleName.".php"));

                $console->writeLn();
                $console->writeLn("Module <$moduleName> for Bundle <$bundleName> created sucessfully!");
            } catch (Engine\IOException $e) {
                $console->writeLn($e->getMessage());
            }
            break;
    }
} else {
    $console->writeLn();
    @list($bundle, $controller, $action) = explode(":",$argv[1]);
    if(!isset($controller) OR !isset($action)) exit("Wrong parameter for starting\n");

    $namespace = __NAMESPACE__.NS_SEP."Bundles".NS_SEP.$bundle;

    $controller = $namespace.NS_SEP."Controller".NS_SEP.$controller;
    $controller = new $controller;

    unset($argv[0]);
    unset($argv[1]);
    $argv = array_values($argv);
    $controller->$action($argv);
}
