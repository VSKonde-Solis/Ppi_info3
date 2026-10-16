<?php 

require_once __DIR__ . "/../model/user.php";

Class UserController {
    private $userModel;
    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        if (isset($_SESSION['id'])) {
            header ("Location : index.php?action=panel");
            exit;
        }

        include __DIR__ . "/../view/login.php";
    }

    public function authenticate() {
        if(!isset($_POST['email'], $_POST['senha'])) {
            header("Location: index.php");
            exit;
        }

        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        if (empty($email) || empty($senha)) {
            $error = "Fill your email and password";
            include __DIR__ . "/../view/login.php";
        }
    }
}




?>