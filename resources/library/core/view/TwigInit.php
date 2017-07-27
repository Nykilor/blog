<?php
namespace Core\View;
use Twig_Loader_Filesystem;
use Twig_Environment;

class TwigInit {
    public static $loader;
    public static $twig;

    public function __construct($template_dir) {
        self::$loader = new Twig_Loader_Filesystem($template_dir);
        self::$twig = new Twig_Environment(self::$loader);
    }
}
