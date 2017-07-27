<?php
namespace Core;
use PDO;

class Session extends DB {

    use UtilityTrait;

    private $credentials;
    private $data;

    public function __construct(array $post_data, bool $login = true) {
        if(!isset($_SESSION)) {
            session_start();
        }

        $this->credentials = $post_data;

        if($login) {
            $this->check()->login();
        } else if (!$login AND isset($_POST['logout_form'])) {
            $this->logout();
        }
    }

    private function logout() {
        session_destroy();
        header('Location:'. $this->getHomeUrl());
        exit();
    }

    private function login() {
        if(password_verify($this->credentials['password'], $this->data['password'])) {
            $_SESSION['access_type'] = $this->data['access_type'];
            $_SESSION['author_id'] = $this->data['id'];
            $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            header('Location:'. $this->getHomeUrl());
            exit();
        } else {
            http_response_code(401);
        }
    }

    private function check() {
        $stmt = DB::$connection->prepare("SELECT id, login, password, access_type FROM author WHERE login = :login");
        $stmt->bindValue(":login", $this->credentials['login'], PDO::PARAM_STR);
        $result = $stmt->execute();
        $this->data = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result) {
            return $this;
        } else {
            http_response_code(400);
        }
    }
}
