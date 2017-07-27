<?php
namespace Core\Controller;

interface ControllerInterface {
    public function route(string $url);
}
