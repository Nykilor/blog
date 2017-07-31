<?php
namespace Core;

trait UtilityTrait {

    public function getCallingMethod($how_far) {
        return debug_backtrace()[$how_far]['function'];
    }

    public function getHomeUrl() : string {
        return 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    }

    public function getJson($d) {
        header("Content-Type: application/json;charset=utf-8");
        echo json_encode($d, true);
    }

    public function slugify($text) : string {
         // replace non letter or digits by -
         $text = preg_replace('~[^\pL\d]+~u', '-', $text);

         // transliterate
         $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

         // remove unwanted characters
         $text = preg_replace('~[^-\w]+~', '', $text);

         // trim
         $text = trim($text, '-');

         // remove duplicate -
         $text = preg_replace('~-+~', '-', $text);

         // lowercase
         $text = strtolower($text);

         if (empty($text)) {
             return 'n-a';
         }

         return $text;
     }
     //webcopied
     public function getClientIp() {
         $ipaddress = '';
         if (isset($_SERVER['HTTP_CLIENT_IP']))
             $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
         else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
             $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
         else if(isset($_SERVER['HTTP_X_FORWARDED']))
             $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
         else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
             $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
         else if(isset($_SERVER['HTTP_FORWARDED']))
             $ipaddress = $_SERVER['HTTP_FORWARDED'];
         else if(isset($_SERVER['REMOTE_ADDR']))
             $ipaddress = $_SERVER['REMOTE_ADDR'];
         else
             $ipaddress = 'UNKNOWN';
         return $ipaddress;
    }

}
