<?php
/**
 * =====================================================================
 * Database - classe de conexão com o banco de dados
 * =====================================================================
 * Esta é a ÚNICA classe de conexão do projeto (foi unificada a partir
 * dos dois projetos originais, que tinham duas classes com o mesmo nome).
 *
 * Ela concentra toda a configuração do MySQL: host, usuário, senha e
 * nome do banco. Assim, se um dia algo mudar, mexe-se só aqui.
 *
 * Como usar:
 *   $db   = new Database();      # cria o objeto com a configuração
 *   $conn = $db->conectar();     # devolve a conexão mysqli pronta
 */

class Database {
    # ---------------- Configurações da conexão ----------------

    # Host = onde o MySQL está rodando. "localhost" significa a própria
    # máquina (no XAMPP, o MySQL roda junto na porta 3306).
    private $host = 'localhost';

    # Usuário do MySQL. No XAMPP o padrão é "root".
    private $username = 'root';

    # Senha do usuário do MySQL. O padrão do XAMPP é vazia (sem senha).
    private $senha = '';

    # Nome do banco de dados. É nele que ficam as duas tabelas do
    # projeto: "usuarios" (do login) e "tarefas" (da to-do list).
    private $banco = 'todo_list';

    # Variável que guardará o objeto de conexão (mysqli) depois de
    # conectar. "public" = pode ser lida de fora da classe, se precisar.
    public $conn = '';

    # -------------------------------------------------------------------
    # Método: conectar()
    # -------------------------------------------------------------------
    # Abre a conexão com o MySQL e a devolve para quem chamou.
    #
    # Retorno: objeto mysqli, pronto para usar ->query(), ->prepare(),
    # ->real_escape_string() etc.
    #
    # Em caso de falha (MySQL desligado, senha errada, banco não
    # existindo), imprime o erro e ENCERRA o programa com die(),
    # porque sem banco a aplicação não tem como continuar.
    # -------------------------------------------------------------------
    public function conectar(){
        # new mysqli(host, usuário, senha, banco) = abre a conexão.
        # O objeto de conexão fica guardado em $this->conn.
        $this->conn = new mysqli($this->host, $this->username, $this->senha, $this->banco);

        # connect_error traz uma mensagem de erro se a conexão falhou
        # (vazio = conectou normalmente, então o if não entra).
        if($this->conn->connect_error){
            # die(texto) = imprime a mensagem e PARA a execução do script.
            die("algo deu errado". $this->conn->connect_error);
        }

        # Devolve a conexão ativa para o model (User ou Tarefa) que chamou.
        return $this->conn;
    }
}
?>
