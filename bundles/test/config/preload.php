<?php
namespace NextFW\Bundles\Test\Config;

class Preload {
    static function init($obj)
    {
        $tpl = "\\".Main::$tpl['engine'];
        $tpl::configure("base_url", Main::$tpl['base_url'] );
        $tpl::configure("tpl_dir", $obj->bundlePath.DS."view".DS."default".DS );
        $tpl::configure("cache_dir", "cache".DS );

        //initialize a Rain TPL object
        $tplObj = new $tpl;

        return $tplObj;
    }
} 