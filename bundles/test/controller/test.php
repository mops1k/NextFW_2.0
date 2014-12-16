<?php
namespace NextFW\Bundles\Test\Controller;

use NextFW;
use NextFW\Engine as Engine;

class Test extends Engine\Controller {

    /** @var NextFW\Bundles\Test\Module\Test */
    public $mod;
    /** @var \RainTpl */
    public $tpl;

    function index($args = null)
    {
        Engine\R::setup("mysql:host=localhost;dbname=mysql","root","ma5Haash");
        $this->tpl->test = "someValue";
        $this->tpl->draw("main");
    }
}