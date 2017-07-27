<?php
namespace Core\Model;
use PDO;

use \Core as Core;

use Core\DB as DB;

class POST extends DB {

  public static $content;

  protected function createOne(string $route, array $post_data) {
      if(in_array($route, DB::$routes)) {
          $col = "";
          $val = "";

          foreach ($post_data as $key => $value) {
              if($route === "author" && $key === "password") {
                  $col .= $key.", ";
                  $value = password_hash($value, PASSWORD_DEFAULT);
                  $val .= "'$value', ";
              } else {
                  $col .= $key.", ";
                  $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                  $val .= "'$value', ";
              }
          }

          $col = rtrim($col, ", ");
          $val = rtrim($val, ", ");

        if($route === "comment" && $col !== "post_id, content, author, author_ip") {
            http_response_code(400);
            exit();
        }

        $stmt = DB::$connection->prepare("INSERT INTO $route ($col) VALUES ($val)");
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
      if(in_array($route, DB::$routes)) {
          $stmt = DB::$connection->prepare("DELETE FROM $route WHERE id= :id");
          $stmt->bindValue(":id", $id, PDO::PARAM_INT);
          $exec = $stmt->execute();
          $done = $stmt->rowCount();

          if($done) {
              http_response_code(200);
          } else {
              echo "Row affected: '".$done."'. There is not resource with id like '".$id. "' or/and there was no change.";
              http_response_code(404);
          }
      }
  }

  protected function editOne(string $route, int $id, array $post_data, $author_id = 0) {
      if(in_array($route, DB::$routes)) {
          $part = "";

          foreach ($post_data as $key => $value) {
              $part .= "`$key` = '$value', ";
          }

          $part = rtrim($part, ", ");
          if($author_id > 0) {
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
              echo "Row affected: '".$done."'. There is not resource with id like '".$id. "' or/and there was no change.";
              http_response_code(404);
          }
      } else {
          http_response_code(400);
      }
  }

}
