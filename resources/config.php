<?php

  return [
    "db" => [
        "host" => "localhost",
        "dbname" => "blog",
        "charset" => "utf8",
        "login" => "root",
        "password" => "",
    ],
    "routes" => [
        "author" => ["name","email","img_path", "about","access_type", "login", "password"],
        "post" => ["author_id", "content", "content_short", "date", "title", "thumbnail_path"],
        "comment" => ["post_id", "content", "author", "author_ip", "date"],
    ],
    "access_types" => [
        1 => ["edit",
              "delete",
              "create" => ["author","post","comment"],
              "upload",
              ],
        2 => ["create" => ["post","comment"],
              "edit_own",
              "upload",
             ],
        3 => ["create" => ["comment"]]
    ],
    "files" => [
        "img" => ["jpg", "jpeg", "png"],
        "doc" => ["pdf", "doc", "txt"]
    ],
    "http" => [
        'PUT' => 'edit',
        'DELETE' => 'delete',
        'POST' => 'create'
    ]
  ];
