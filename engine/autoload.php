<?php
namespace NextFW\Engine;

use NextFW\Config as Config;
use NextFW\Bundles as Bundle;

class Autoload
{
    private $namespace = null;
    private $mainPath = null;
    public function __construct($ns = null, $mp = null)
    {
        $this->namespace = strtolower($ns);
        $this->mainPath = $mp.DIRECTORY_SEPARATOR;
    }

    /**
     * Инициализация автозагрузчика
     */
    public function register()
    {
        spl_autoload_register([$this, 'autoload']);
    }

    /**
     * Парсер класса и включение его по запросу
     * @param object $class Вызываемый класс
     */
    public function autoload($class)
    {
        if(stripos($class,$this->namespace) !== false)
            $class = $this->namespace == null ? strtolower(strtolower(ltrim($class, '\\'))) : strtolower(str_replace($this->namespace."\\","",strtolower(ltrim($class, '\\'))));
        else
            $class = "ext\\".$class;
        $fileName = null;
        if ($lastNsPos = strrpos($class, '\\')) {
            $namespace = substr($class, 0, $lastNsPos);
            $class = substr($class, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= $class . '.php';

        if(file_exists($this->mainPath.$fileName))
        {
            require_once $this->mainPath.$fileName;
        } else {
            throw new AutoloadException("File not found: ".$fileName);
        }
    }
}

class AutoloadException extends \Exception {}
