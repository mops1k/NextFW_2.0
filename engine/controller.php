<?php
namespace NextFW\Engine;

use NextFW;

abstract class Controller {

    public $bundle;
    public $bundleNS;
    public $bundlePath;
    public $bundleController;
    public $mod;
    public $tpl;

    function __construct()
    {
        $this->bundle = Route::$bundle;
        $this->bundleNS = implode("\\",[
            NS,
            "Bundles",
            ucfirst($this->bundle)
        ]);
        $this->bundlePath = Route::$bundlePath;
        $this->bundleController = explode("/", Route::$request)[0];


        // running bundle preload config
        if(file_exists($this->bundlePath.DS."config".DS."preload.php"))
        {
            $bundlePreload = $this->bundleNS."\\Config\\Preload";
            $this->tpl = $bundlePreload::init($this);
        }

        // loading bundle module for controller
        if(file_exists($this->bundlePath.DS."module".DS.$this->bundleController.".php"))
        {
            $this->mod = $this->bundleNS."\\Module\\".ucfirst($this->bundleController);
            $this->mod = new $this->mod;
        }
    }

    function __call($m, $a) {
        throw new \Exception('Метода '.$m.' не существует.');
    }
}

class ControllerException extends \Exception {}
