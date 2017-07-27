<?php
namespace Core;

trait UtilityTrait {

    public function getHomeUrl() {
        return 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    }

    public function getJson($d) {
        header("Content-Type: application/json;charset=utf-8");
        echo json_encode($d, true);
    }

}
