<?php
namespace Core;

class Files {

    const DIRPATH = __DIR__."/../../";

    public $file;

    private $allowed_file_types;
    private $file_name;
    private $file_type;

    public function __construct($config, array $file, $file_type, $rewrite) {
        if(empty($file['file']['name'])) {
            http_response_code(400);
            exit();
        }
        $this->rewrite = $rewrite;
        $this->file = $file;
        $this->file_type = $file_type;
        $this->allowed_file_types = (isset($config[$file_type])) ? $config[$file_type] : $this->terminate();
        $this->check()->upload();
    }


    private function check() {
        $file_name = $this->file['file']['name'];
        $file_size = $this->file['file']['size'];
        $file_type = $this->file['file']['type'];
        $file_tmp = $this->file['file']['tmp_name'];
        $file_ext = explode('.',$file_name);
        $file_ext = end($file_ext);
        $file_ext = strtolower($file_ext);

        if(!in_array($file_ext, $this->allowed_file_types)){
            http_response_code(415);
            echo "Extension not allowed, please choose a ".implode(",", $this->allowed_file_types)." file.";
            exit();
        }

        if($file_size > 5242880){
            http_response_code(409);
            echo "File size can't be bigger than 5 MB";
            exit();
        }

        if(file_exists(self::DIRPATH.$this->file_type."/".$file_name) && !$this->rewrite) {
            http_response_code(409);
            echo "File with this name already exists";
            exit();
        }

        $this->file_tmp = $file_tmp;

        return $this;
    }

    private function upload() {
        if(move_uploaded_file($this->file_tmp, self::DIRPATH.$this->file_type."/".$this->file['file']['name'])) {
            http_response_code(201);
            echo "The file ". basename( $this->file["file"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    protected function terminate() {
        http_response_code(400);
        exit();
    }
}
