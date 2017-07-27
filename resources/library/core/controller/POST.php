<?php
namespace Core\Controller;

use \Core as Core;

use Core\Session as Session;
use Core\Files as Files;

class POST extends Core\Model\POST implements ControllerInterface {

        public static $access_type;
        public static $author_id;
        private $post_data;
        private $method;
        private $route;
        private $var;
        private $a_privilage;
        private $method_only = ['logout'];


        public function __construct(string $url, $config, $post_data = null) {
            if(!isset($_SESSION)) {
                session_start();
            }
            $this->a_files = $config['files'];
            $this->a_privilage = $config['access_types'];
            $this->post_data = $post_data;
            $this->checkPrivilage();
            $this->route($url);
        }

        public function edit() {
            if($this->post_data && $this->var > 0) {
                $this->editOne($this->route, $this->var, $this->post_data);
            } else {
                http_response_code(400);
            }
        }

        public function edit_self() {
            if($this->post_data && $this->var > 0 && $this->route !== "author") {
                $this->editOne($this->route, $this->var, $this->post_data, self::$author_id);
            } else {
                http_response_code(400);
            }
        }

        public function delete() {
            if($this->var > 0) {
                $this->deleteOne($this->route, $this->var);
            } else {
                http_response_code(400);
            }
        }

        public function create() {
            if($this->post_data) {
                if(in_array($this->route, $this->a_privilage[self::$access_type][__FUNCTION__])) {
                    $this->createOne($this->route, $this->post_data);
                } else {
                    http_response_code(403);
                }
            } else {
                http_response_code(400);
            }
        }

        public function logout() {
            $s = new Session($_POST, false);
        }

        public function upload() {
            $file = new Files($this->a_files, $_FILES, $this->route, $this->var);
        }

        // np. domain.com/post/1, domain.com/author/1
        public function route(string $url) {
            $url = str_replace("/blog/", "", $url);
            $v = explode("/", $url);

            $this->setOrExit("method", $v, 0);

            if(in_array($this->method, $this->method_only)) {
                $this->{$this->method}();
            }

            $this->setOrExit("route", $v, 1);

            if(!empty($v[2]) && intval($v[2]) > 0) {
                $this->var = intval($v[2]);
            } else {
                $this->var = 0;
            }

            if(method_exists($this, $this->method) && (in_array($this->method, $this->a_privilage[self::$access_type]) OR array_key_exists($this->method, $this->a_privilage[self::$access_type]))) {
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
