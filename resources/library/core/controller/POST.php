<?php
namespace Core\Controller;

use \Core as Core;

use Core\Session as Session;
use Core\FilesUpload as FilesUpload;

class POST extends Core\Model\POST implements ControllerInterface {

        public static $access_type;
        public static $author_id;
        private $data;
        private $method;
        private $route;
        private $var;
        private $a_privilage;
        private $method_only = ['logout'];
        private $http;


        public function __construct(string $url, $config) {
            if(!isset($_SESSION)) {
                session_start();
            }
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->data = $_POST;
            } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                $this->data = explode("&", file_get_contents('php://input'));
                foreach ($this->data as $key => $value) {
                    $var = explode("=", $value);
                    unset($this->data[$key]);
                    $this->data[$var[0]] = $var[1];
                }
            }

            $this->http = $config['http'];
            $this->a_files = $config['files'];
            $this->a_privilage = $config['access_types'];
            $this->checkPrivilage();
            $this->route($url);
        }

        //triggered by PUT
        public function edit() {
            if($this->data && $this->var[0] > 0 && in_array(__FUNCTION__, $this->a_privilage[self::$access_type])) {
                $this->editOne($this->route, $this->var, $this->data);
            } else if (in_array(__FUNCTION__."_own", $this->a_privilage[self::$access_type])){
                $this->editOne($this->route, $this->var[0], $this->data, self::$author_id);
            } else {
                http_response_code(400);
            }
        }

        //triggered by DELETE
        public function delete() {
            if($this->var > 0) {
                $this->deleteOne($this->route, $this->var[0]);
            } else {
                http_response_code(400);
            }
        }

        //triggered by POST
        public function create() {
            if($this->data) {
                if(in_array($this->route, $this->a_privilage[self::$access_type][__FUNCTION__])) {
                    $this->createOne($this->route, $this->data);
                } else {
                    http_response_code(403);
                }
            } else {
                http_response_code(400);
            }
        }

        //triggered by $_POST['logout_form']
        public function logout() {
            $s = new Session($_POST, false);
        }

        //triggered by $_POST['upload_form']
        public function upload() {
            $file = new FilesUpload($this->a_files, $_FILES, $this->var[0], $this->var[1]);
            $file->check()->upload();
        }

        // np. domain.com/post/1, domain.com/author/1
        public function route(string $url) {
            $url = str_replace("/blog/", "", $url);
            $v = explode("/", $url);

            foreach ($v as $key => $value) {
                switch ($key) {
                    case 0:
                        $this->setOrExit("route", $v, 0);
                        break;
                    default:
                        $this->var[] = $value;
                        break;
                }
            }

            $this->setOrExit("route", $v, 0);

            //sets method, either the method is from config or form
            if(isset($this->data[$this->route."_form"])) {
                $this->method = $this->route;
            } else if (array_key_exists($_SERVER['REQUEST_METHOD'], $this->http)) {
                $this->method = $this->http[$_SERVER['REQUEST_METHOD']];
            } else {
                http_response_code(400);
                exit();
            }

            //finish early if is in method_only;
            if(in_array($this->method, $this->method_only)) {
                $this->{$this->method}();
                return;
            }

            //check if method exists and the user has privilage to use it
            if(
                method_exists($this, $this->method) &&
                (in_array($this->method, $this->a_privilage[self::$access_type]) OR
                array_key_exists($this->method, $this->a_privilage[self::$access_type]))
            ) {
                $this->{$this->method}();
            } else if (method_exists($this, $this->method)) {
                http_response_code(403);
            } else {
                http_response_code(400);
            }
        }

        private function setOrExit($var, array $v, int $i) {
            if(isset($v[$i])) {
                $this->$var = $v[$i];
            } else {
                http_response_code(400);
                exit();
            }
        }

        private function checkPrivilage() {
            if(!isset($_SESSION)) {
                http_response_code(401);
            } else if(isset($_POST['login']) AND isset($_POST['password']) AND isset($_POST['login_form'])) {
                $s = new Session($_POST);
            } else {
                self::$access_type = (isset($_SESSION['access_type']))? $_SESSION['access_type'] : 3;
                self::$author_id = (isset($_SESSION['author_id']))? $_SESSION['author_id'] : null;
            }
        }
}
