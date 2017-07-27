<?php
namespace Core;
use PDO;

class DB {
    public static $config;
    protected static $connection;
    public static $routes;

    public function __construct($config) {
         $this->db = $config['db'];
         self::$routes = $config['routes'];

          self::$connection = new PDO(
            "mysql:host={$this->db['host']};dbname={$this->db['dbname']};charset={$this->db['charset']}",
             $this->db['login'],
             $this->db['password'],
             array(
              PDO::ATTR_EMULATE_PREPARES => false,
              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
              )
           );
    }

    public function __destruct() {
        self::close();
    }

    public static function close() {
        self::$connection = NULL;
    }
}
