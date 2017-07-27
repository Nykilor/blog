<?php
namespace Core\Controller;

use \Core as Core;

use Core\Model\Basic as Model;
use Core\View\TwigInit as View;

class Basic extends Model implements ControllerInterface {

  use Core\UtilityTrait;

  public $type;
  public $var;
  public $json = false;
  public $user_type;
  public $per_page;

  public function __construct(string $url, int $per_page = 10) {
        $this->user_type = (isset($_SESSION['access_type']) AND $_SESSION['access_type'] > 0) ? "admin" : "user";
        $this->per_page = $per_page;
        $this->route($url);
  }

  public function __destruct() {

     if (empty(Model::$content) && $this->json) {
          http_response_code(400);
      }

      if(!$this->json) {
          //var_dump(Model::$content);
          echo View::$twig->render($this->user_type."/".$this->type.".html.twig", Model::$content);
      } else {
          $this->getJson(Model::$content);
      }
  }

  public function index() {
      $this->json = false;
      $l_o = $this->pagination();
      $this->getAllPosts($l_o['limit'], $l_o['offset']);
  }

  public function search() {
      Model::$content = [];
      if(!empty($_GET)) {
          $vars = [
                   'title' => ['type' => 'string'],
                   'author' => ['type' => 'string'],
                   'date' => ['type' => 'date']
                  ];
          foreach ($_GET as $key => $value) {
              if(array_key_exists($key, $vars)) {
                  $vars[$key][] = filter_var($value, FILTER_SANITIZE_STRING);
                  if(empty($vars[$key][0])) {
                      http_response_code(400);
                      $this->type = "400";
                      $this->user_type = "errorCode";
                      $this->error();
                      return;
                  }
              }
          }
          $this->searchFor($vars);
      }
  }

  public function post(array $var) {
      if($var['id'] !== 0) {
         $this->getOnePost($var['id']);
         $this->checkIfDataExists("post");
         $this->getAllComments($var['id']);
     } else {
         $l_o = $this->pagination();
         $this->getAllPosts($l_o['limit'], $l_o['offset']);
         $this->checkIfDataExists("posts");
     }
  }

  public function comment(array $var) {
    $this->getOneComment($var['id']);
    $this->checkIfDataExists("comment");
  }

  public function author(array $var) {
     $this->getAuthorPage($var['id']);
     $this->checkIfDataExists("author");
  }

  public function panel() {
    Model::$content = [];
  }

  public function error() {
    Model::$content = [];
  }

  public function route(string $url) {
      $url = str_replace("/blog/", "", $url);
      $v = explode("/", $url);
      if($v[0] === "json" && !empty($v[1])) {
          $this->type = $v[1];
          $this->var['id'] = (isset($v[2])) ? intval($v[2]) : 0;
          $this->json = true;
      } else {
          $this->type = strtok($v[0], "?");
          $this->var['id'] = (isset($v[1])) ? intval($v[1]) : 0;
      }
    if(method_exists($this, $this->type)) {
        $this->{$this->type}($this->var);
    } else {
      $this->type = "index";
      $this->index();
    }
  }

  private function pagination() {
      if(!isset($_GET['page'])) {
          if(isset($_GET['limit'])) {
              $limit = filter_var($_GET['limit'], FILTER_SANITIZE_NUMBER_INT);
          } else if($this->json) {
              $limit = 0;
          } else {
              $limit = $this->per_page;
          }
          $offset = (isset($_GET['offset']))? intval($_GET['offset']) : 0;
      } else {
          $limit = (isset($_GET['limit']))? intval($_GET['limit']) : $this->per_page;
          $offset = floor((filter_var($_GET['page'], FILTER_SANITIZE_NUMBER_INT) - 1) * $limit);
      }
      return ["limit" => $limit, "offset" => $offset];
  }

  private function checkIfDataExists(string $data_type) {
      if($this->json && !self::$content[$data_type]) {
          http_response_code(404);
          exit();
      }
      if(!self::$content[$data_type]) {
          http_response_code(404);
          $this->type = "404";
          $this->user_type = "errorCode";
          $this->error();
          return;
      }
  }

}
