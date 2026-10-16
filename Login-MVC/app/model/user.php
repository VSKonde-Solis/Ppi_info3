<?php 

require_once __DIR__ . '/../config/database.php';

Class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conect();
    }

    public function searchByEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

?>