<?php
/**
 * =====================================================================
 * User - model de USUÁRIOS (camada que conversa com a tabela "usuarios")
 * =====================================================================
 * No MVC, o model só cuida de DADOS: ler e gravar no banco.
 * Ele NÃO sabe nada sobre HTML nem sobre sessões - isso é papel do
 * controller (userController) e da view (login.php).
 */

# Conecta com a classe Database (arquivo config/database.php),
# que é quem de fato abre a conexão com o MySQL.
require_once __DIR__ . '/../config/database.php';

Class User {
    # Guarda a conexão com o banco para uso nos métodos abaixo.
    private $conn;

    # -------------------------------------------------------------------
    # Método: __construct() - construtor da classe
    # -------------------------------------------------------------------
    # Executado automaticamente em "new User()".
    # Cria um objeto Database e chama conectar(), guardando a conexão
    # ativa em $this->conn para todos os métodos da classe usarem.
    # -------------------------------------------------------------------
    public function __construct() {
        $db = new Database();           # monta a configuração da conexão
        $this->conn = $db->conectar();  # abre a conexão com o MySQL
    }

    # -------------------------------------------------------------------
    # Método: searchByEmail($email) - procura um usuário pelo e-mail
    # -------------------------------------------------------------------
    # Parâmetro: $email (string) - o e-mail digitado no formulário.
    # Retorno:   array com os dados do usuário (id, nome, email, senha)
    #            ou null se ninguém for encontrado.
    #
    # Como funciona, passo a passo:
    #   1) Monta a consulta com um "?" no lugar do e-mail;
    #   2) prepare() envia a consulta ao banco ANTES de executá-la
    #      (isso protege contra SQL Injection: o valor não é "colado"
    #      na string, é substituído de forma segura);
    #   3) bind_param("s", $email) liga o valor ao "?"; o "s" indica
    #      que ele é texto (string);
    #   4) execute() roda a consulta;
    #   5) get_result() pega o resultado e fetch_assoc() devolve a
    #      primeira linha como array associativo - assim dá para usar
    #      $usuario['senha'], $usuario['nome'] etc.
    # -------------------------------------------------------------------
    public function searchByEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";   # consulta parametrizada
        $stmt = $this->conn->prepare($sql);   # prepara a consulta
        $stmt->bind_param("s", $email);       # liga o e-mail ao "?"
        $stmt->execute();                     # executa no banco

        $result = $stmt->get_result();   # resultado da consulta
        return $result->fetch_assoc();   # devolve a linha como array (ou null)
    }
}
?>
