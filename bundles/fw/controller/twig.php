<?php
namespace NextFW\Bundles\Fw\Controller;

use NextFW;
use NextFW\Engine as Engine;

class twig extends Engine\Controller {
    function test($args = null) {
        \Twig\Twig_Autoloader::register(true);
        $loader = new \Twig_Loader_Filesystem($this->bundlePath.'/view');
        $twig = new \Twig_Environment($loader, array(
            'cache' => ROOT.'/cache',
        ));
        $twig->clearTemplateCache();
        $twig->clearCacheFiles();

        echo $twig->render('test2.html.twig',[ 'name' => $args['name'] ]);
    }
}