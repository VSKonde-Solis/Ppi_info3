<?php 

Class Database {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "login";


    public $conn = '';

    public function conect() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die("ERR.: " . $this->conn->connect_error);
        }
        return $this->conn;
    }

    
}



?>