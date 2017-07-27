<?php
    require(__DIR__ . '/resources/library/vendor/autoload.php');

    $config = require("resources/config.php");

    session_start();
    //session_destroy();
    //TODO: categories, tags, search, basic templates

    //$_SESSION['access_type'] = 1;

    if(!empty($_POST)) {
        $m = new Core\Model\POST($config);
        $c = new Core\Controller\POST($_SERVER['REQUEST_URI'], $config, $_POST);
    } else {
        $m = new Core\Model\Basic($config);
        $c = new Core\Controller\Basic($_SERVER['REQUEST_URI']);

        if(!$c->json) {
            $v = new Core\View\TwigInit(__DIR__."/resources/templates");
        }
    }
