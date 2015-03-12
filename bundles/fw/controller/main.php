<?php
namespace NextFW\Bundles\Fw\Controller;

use NextFW;
use NextFW\Engine as Engine;

class Main extends Engine\Controller {

    /** @var NextFW\Bundles\Fw\Module\Main */
    public $mod;
    /** @var \Twig_Environment */
    public $tpl;

    function index($args = null)
    {
        echo $this->tpl->render("default/main.html");
    }
}
