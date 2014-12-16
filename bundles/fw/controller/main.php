<?php
namespace NextFW\Bundles\Fw\Controller;

use NextFW;
use NextFW\Engine as Engine;

class Main extends Engine\Controller {

    /** @var NextFW\Bundles\Test\Module\Test */
    public $mod;
    /** @var \RainTpl */
    public $tpl;

    function index($args = null)
    {
        $this->tpl->draw("main");
    }
}
