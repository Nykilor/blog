<?php

  return [
    "db" => [
        "host" => "localhost",
        "dbname" => "blog",
        "charset" => "utf8",
        "login" => "root",
        "password" => "",
    ],
    "routes" => ["author","post","comment"],
    "access_types" => [
        1 => ["edit",
              "delete",
              "create" => ["author","post","comment"],
              "edit_self",
              "upload",
              ],
        2 => ["create" => ["post","comment"],
              "edit_self",
              "upload",
             ],
        3 => ["create" => ["comment"]]
    ],
    "files" => [
        "img" => ["jpg", "jpeg", "png"],
        "doc" => ["pdf", "doc", "txt"]
    ]
  ];
