<?php
namespace NextFW\Bundles\Fw\Controller;

use NextFW;
use NextFW\Engine as Engine;

class twig extends Engine\Controller {
    function test($args = null) {
        echo $this->tpl->render('test2.html.twig',[ 'name' => $args['name'] ]);
    }
}