<?php
namespace Core\Model;
use PDO;

use \Core as Core;

use Core\DB as DB;

class Basic extends DB {

  public static $content;
  //posty powinny pojawiać się od 1 do n zaczynając od najstarczego konczac na najnowszym, nie wiem czy ORDER BY ma sens
  protected function getAllPosts(int $l = 0, int $of = 0) {
     if($l > 0) {
         $sql = "SELECT post.*,author.name,author.id AS author_id FROM post,author WHERE author.id = post.author_id ORDER BY date DESC LIMIT $l OFFSET $of";
     } else if( $of > 0) {
         $big_int = 2147483647;//max auto_increment value
         $sql = "SELECT post.*,author.name,author.id AS author_id FROM post,author WHERE author.id = post.author_id ORDER BY date DESC LIMIT $big_int OFFSET $of";
     } else {
         $sql = "SELECT post.*,author.name,author.id AS author_id FROM post,author WHERE author.id = post.author_id ORDER BY date DESC";
     }
      self::$content["posts"] =  DB::$connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  protected function getOnePost($id) {
      if(filter_var($id, FILTER_VALIDATE_INT)) {
          $sql = "SELECT post.id, post.content, post.date, post.title, post.title_slug, post.author_id, author.name FROM post,author WHERE post.id=:id AND author.id = post.author_id";
      } else {
          $sql = "SELECT post.id, post.content, post.date, post.title, post.title_slug, post.author_id, author.name FROM post,author WHERE post.title_slug=:id AND author.id = post.author_id";
      }
      $stmt = DB::$connection->prepare($sql);
      $stmt->bindParam(":id", $id);
      $stmt->execute();
      self::$content["post"] =  $stmt->fetch(PDO::FETCH_ASSOC);
  }

  protected function getOneComment(int $id) {
      $stmt = DB::$connection->prepare("SELECT id,content,author,date FROM comment WHERE id=:id");
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      self::$content["comment"] =  $stmt->fetch(PDO::FETCH_ASSOC);
  }

  protected function getAllComments(int $id) {
      $stmt = DB::$connection->prepare("SELECT id,content,author,date FROM comment WHERE post_id=:id ORDER BY date DESC");
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      self::$content["comments"] =  $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  protected function getAuthorPage($id) {
      if(filter_var($id, FILTER_VALIDATE_INT)) {
          $sql = "SELECT id, name, email, img_path, about FROM author WHERE id = :id";
      } else {
          $sql = "SELECT id, name, email, img_path, about FROM author WHERE name = :id";
          $id = str_replace("-", " ", $id);
      }

      $stmt2 = DB::$connection->prepare($sql);
      $stmt2->bindParam(":id", $id);
      $stmt2->execute();

      if($stmt2->rowCount() > 0) {
          self::$content["author"] = $stmt2->fetch(PDO::FETCH_ASSOC);

          $stmt = DB::$connection->prepare("SELECT id, content, date, title, title_slug, author_id FROM post WHERE author_id = :id ORDER BY date DESC");
          $stmt->bindParam(":id", self::$content["author"]["id"], PDO::PARAM_INT);
          $stmt->execute();

          if($stmt->rowCount() > 0) self::$content["author"]["posts"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
  }

  protected function searchFor(array $what) {
      $where_after = "";
      foreach ($what as $key => $value) {
          if(isset($value[0])) {
            if($what[$key]['type'] === 'string' && $key === "author") {
                $where_after .= "$key.name LIKE '%$value[0]%' AND ";
            } else if($what[$key]['type'] === 'string') {
                $where_after .= "$key LIKE '%$value[0]%' AND ";
            } else if($what[$key]['type'] === 'date') {
                if(strpos($value[0], "-")) {
                    $f_t = explode("-", $value[0]);
                    foreach ($f_t as $key2 => $value2) {
                        $f_t[$key2] = date("Y-m-d H:i:s", intval($value2));
                    }
                    $where_after .= "$key BETWEEN '$f_t[0]' AND '$f_t[1]' AND ";
                } else if(strpos($value[0], ">") !== false) {
                    $after = (strpos($value[0], ">") === 0)? false : true;
                    $value[0] = date("Y-m-d H:i:s", intval(str_replace(">", "", $value[0])));

                    $where_after .= (!$after)? "$key > '$value[0]' AND " : "$key < '$value[0]' AND ";
                } else {
                    if(intval($value[0]) > 0) {
                        $where_after .= "$key = $value[0] AND ";
                    }
                }
            }
          }
      }
      $where_after = rtrim($where_after, " AND ");
      if(!empty($where_after)) {
          $stmt = DB::$connection->query("SELECT post.*,author.name,author.id AS author_id FROM post,author WHERE author.id = post.author_id AND $where_after");
          self::$content["posts"] =  $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
  }
}
