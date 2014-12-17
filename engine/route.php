<?php
namespace NextFW\Engine;

use NextFW\Config as Config;
use NextFW\Bundles as Bundle;

class Route {
    public static $bundle;
    public static $request;
    private $reqMethod;
    public static $bundlePath;
    private static $args = null;

    function __construct($request = 'main/index',$bundle = 'default')
    {
        self::$bundle = isset($_GET['b']) ? $_GET['b'] : $bundle;
        self::$request = isset($_GET['r']) ? $_GET['r'] : $request;
        $this->reqMethod = $_SERVER['REQUEST_METHOD'];
        unset($_GET['b']);
        unset($_GET['r']);
        $count = count($_GET);
        if($count > 0)
        {
            $i = 0;
            foreach ($_GET as $key => $value) {
                $args[$i] = $value;
                $args[$key] = $value;
                ++$i;
            }
            self::$args = $args;
        }
    }

    function run($request,$bundle,$args = null)
    {
        self::$bundle = $bundle;
        self::$request = $request;
        self::$args = $args;
        $this->parse();
    }

    /**
     * Подготовка к запуску метода класса в бандле
     */
    function parse()
    {
        list($controller, $method) = explode("/",self::$request);
        $constructor = array(
            NS,
            "bundles",
            self::$bundle,
            "controller",
            $controller
        );
        self::$bundlePath = implode(DIRECTORY_SEPARATOR,[
            "bundles",
            self::$bundle
        ]);
        $class = implode("\\",$constructor);
        $class = new $class;

        if(self::is_ajax())
        {
            $method .= "Ajax";
        }
        elseif(!self::is_ajax() && $this->reqMethod == "POST")
        {
            $method .= !method_exists($class,$method."Post") ?  : "Post";
        }

        $class->$method(self::$args);
    }

    /**
     * Проверка запроса на асинхронный js
     * @return bool
     */
    public static function is_ajax()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && @empty($_SERVER['HTTP_X_REQUESTED_WITH']) && @strtolower(
                        $_SERVER['HTTP_X_REQUESTED_WITH']
                    ) != 'xmlhttprequest'
        ) {
            return false;
        } else {
            return true;
        }
    }
}
