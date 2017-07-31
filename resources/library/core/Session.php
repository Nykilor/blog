<?php
namespace Core;
use PDO;

class Session extends DB {

    use UtilityTrait;

    private $credentials;
    private $data;
    private $authorized = false;

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

    public function setVar($name, $value) {
        $_SESSION[$name] = $value;
    }

    private function logout() {
        session_destroy();
        header('Location:'. $this->getHomeUrl());
        exit();
    }

    private function login() {
        if(password_verify($this->credentials['password'], $this->data['password']) && $this->authorized) {

            $this->setVar('access_type', $this->data['access_type']);
            $this->setVar('author_id', $this->data['id']);

            header('Location:'. $this->getHomeUrl(). "/panel");
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
            $this->authorized = true;
            return $this;
        } else {
            http_response_code(400);
        }
    }
}
