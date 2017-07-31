<?php
namespace Core\Model;
use PDO;
use Exception;

use \Core as Core;

use Core\DB as DB;

class POST extends DB {

  public static $content;

  use Core\UtilityTrait;

  protected function createOne(string $route, array $post_data) {
      if(array_key_exists($route, DB::$routes)) {

        $sql = $this->createColumns($post_data, $route);


        if($route === "comment" && $sql['columns'] !== "post_id, content, author, author_ip") {
            http_response_code(400);
            exit();
        }

        $stmt = DB::$connection->prepare("INSERT INTO $route ({$sql["columns"]}) VALUES ({$sql["values"]})");
        $exec = $stmt->execute();

        if($stmt->rowCount()) {
            http_response_code(201);
        } else {
            http_response_code(500);
        }
      } else {
          http_response_code(404);
      }
  }

  protected function deleteOne(string $route, int $id) {
      if(array_key_exists($route, DB::$routes)) {
          $stmt = DB::$connection->prepare("DELETE FROM $route WHERE id= :id");
          $stmt->bindValue(":id", $id, PDO::PARAM_INT);
          $exec = $stmt->execute();
          $done = $stmt->rowCount();

          if($done) {
              http_response_code(200);
          } else {
              echo "Row affected: '".$done."'. There is not resource with id like '".$id. "' or/and there was no change.";
              http_response_code(200);
          }
      }
  }

  protected function editOne(string $route, int $id, array $post_data, $author_id = 0) {
      if(array_key_exists($route, DB::$routes)) {

          $part = $this->createColumns($post_data, $route);

          if($author_id > 0 && in_array("author_id", DB::$routes[$route])) {
              $stmt = DB::$connection->prepare("UPDATE $route SET $part WHERE id = :id AND author_id = $author_id");
          } else {
              $stmt = DB::$connection->prepare("UPDATE $route SET $part WHERE id = :id");
          }

          $stmt->bindParam(":id", $id, PDO::PARAM_INT);
          $exec = $stmt->execute();
          $done = $stmt->rowCount();

          if($done) {
              http_response_code(200);
          } else {
              if(!$author_id > 0) {
                  echo "Row affected: '".$done."'. There is not resource with id like '".$id. "' or/and there was no change.";
              } else {
                  echo "No rows affected.";
              }
          }
      } else {
          http_response_code(400);
      }
  }

  private function createColumns(array $post_data, $route) {
      switch ($this->getCallingMethod(2)) {
          case 'createOne':
              $col = "";
              $val = "";
              foreach ($post_data as $key => $value) {
                  if(in_array($key, DB::$routes[$route])) {
                      if($route === "author" && $key === "password") {
                          $col .= $key.", ";
                          $value = password_hash($value, PASSWORD_DEFAULT);
                          $val .= "'$value', ";
                      } else if($route === "post" && $key === "title") {
                          $author_id = Core\Controller\POST::$author_id;
                          $col .= "author_id, ";
                          $val .= "'$author_id', ";
                          $col .= $key.", ";

                          $val .= "'$value', ";
                          $col .= "title_slug, ";
                          $val .= "'{$this->slugify($value)}'".", ";
                      } else if($route === "post" ) {
                          $col .= $key.", ";
                          $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                          $val .= "'$value', ";
                      } else if($route === "comment" && $key === "post_id") {
                          $col .= $key.", ";
                          $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                          if(filter_var($value, FILTER_SANITIZE_NUMBER_INT)) {
                              $val .= "'$value', ";
                          } else {
                              http_response_code(400);
                              exit();
                          }
                      } else if($route === "comment" && $key === "author") {
                          $col .= $key.", ";
                          $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                          $val .= "'$value', ";

                          $col .= "author_ip, ";
                          $value = $this->getClientIp();
                          $val .= "'$value', ";
                      } else {
                          $col .= $key.", ";
                          $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                          $val .= "'$value', ";
                      }
                  } else {
                      http_response_code(400);
                      exit();
                  }
              }

              $col = rtrim($col, ", ");
              $val = rtrim($val, ", ");

              return ['columns' => $col, 'values' => $val];
              break;
          case 'editOne':
              $part = "";

              foreach ($post_data as $key => $value) {
                 if(in_array($key, DB::$routes[$route])){
                     $part .= "`$key` = '$value', ";
                 } else {
                     http_response_code(400);
                     exit();
                 }
              }

              $part = rtrim($part, ", ");

              return $part;
              break;
          default:
            throw new Exception("Can't call this method direct", 1);
          break;
      }
  }

}
