<?php
namespace NextFW\Bundles\Fw\Config;

use NextFW;

class Preload {
    static function init($obj)
    {
        $tpl = "\\".Main::$tpl['engine'];
        $tpl::configure("base_url", NextFW\Config\Main::$global['base_url'] );
        $tpl::configure("tpl_dir", $obj->bundlePath.DS."view".DS."default".DS );
        $tpl::configure("cache_dir", "cache".DS );

        //initialize a Rain TPL object
        $tplObj = new $tpl;

        return $tplObj;
    }
} 