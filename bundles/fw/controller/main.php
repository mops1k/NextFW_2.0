<?php
namespace NextFW\Bundles\Fw\Controller;

use NextFW;
use NextFW\Engine as Engine;

class Main extends Engine\Controller {

    /** @var NextFW\Bundles\Fw\Module\Main */
    public $mod;
    /** @var \RainTpl */
    public $tpl;

    function index($args = null)
    {
        $this->tpl->draw("main");
    }
}
