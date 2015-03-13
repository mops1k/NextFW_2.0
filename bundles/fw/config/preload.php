<?php
namespace NextFW\Bundles\Fw\Config;

use NextFW;

class Preload {
    static function init($obj)
    {
        if(Main::$tpl['engine'] !== 'Twig')
        {
            $tpl = "\\".Main::$tpl['engine'];
            $tpl::configure("base_url", NextFW\Config\Main::$global['base_url'] );
            $tpl::configure("tpl_dir", $obj->bundlePath.DS."view".DS."default".DS );
            $tpl::configure("cache_dir", "cache".DS );

            //initialize a Rain TPL object
            $tplObj = new $tpl;

            return $tplObj;
        } else {
            \Twig\Twig_Autoloader::register(true);
            $loader = new \Twig_Loader_Filesystem($obj->bundlePath.DS.'view');
            $debug = NextFW\Config\Main::$global['status'] == 'development' ? true : false;
            $twig = new \Twig_Environment($loader, array(
                'cache' => ROOT.DS.'cache',
                'debug' => $debug
            ));

            $asset = new \Twig_SimpleFunction('asset', function($context, $bool = true) use($loader,$twig){
                $source = $twig->getCompiler()->getSource();
                preg_match('/\/\* (.*) \*\//',$source,$additionalPath);
                $additionalPath = explode('/',$additionalPath[1]);
                if($bool) {
                    $additionalPath = $additionalPath[0];
                } else {
                unset($additionalPath[count($additionalPath)-1]);
                $additionalPath = implode('/',$additionalPath);
                }
                return $loader->getPaths()[0].'/'.$additionalPath.'/'.$context;
            });
            $twig->addFunction($asset);

            // clear cache
            $twig->clearTemplateCache();
            $twig->clearCacheFiles();

            return $twig;
        }
    }
}
