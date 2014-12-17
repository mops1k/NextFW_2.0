<?php
namespace NextFW\Bundles\Error\Controller;

use NextFW;
use NextFW\Engine as Engine;

class Response extends Engine\Controller {
    function show($args = null) {
        // Enter code below:
        $this->tpl->title = "Ошибка кода: ".$args[0];
        $this->tpl->message = $args[1];
        $this->tpl->code = $args[2];
        $this->tpl->draw('main');
    }
}