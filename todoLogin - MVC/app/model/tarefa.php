<?php
/**
 * =====================================================================
 * Tarefa - model das TAREFAS (tabela "tarefas")
 * =====================================================================
 * Responsável apenas por consultar, inserir, apagar e atualizar
 * registros no banco de dados. Nenhuma regra de interface (HTML)
 * vive aqui - isso é papel da view e do controller.
 */

# Conecta com a classe Database (arquivo config/database.php),
# que é quem de fato abre a conexão com o MySQL.
require_once __DIR__ . '/../config/database.php';

class Tarefa{
    # Guarda a conexão com o banco para uso nos métodos abaixo.
    private $conn;

    # -------------------------------------------------------------------
    # Método: __construct() - construtor da classe
    # -------------------------------------------------------------------
    # Executado automaticamente em "new Tarefa()".
    # Cria o objeto Database e chama conectar(), guardando a conexão
    # ativa em $this->conn.
    # -------------------------------------------------------------------
    public function __construct(){
        $db = new Database();           # monta a configuração da conexão
        $this->conn = $db->conectar();  # abre a conexão com o MySQL
    }

    # -------------------------------------------------------------------
    # Método: listar() - devolve TODAS as tarefas
    # -------------------------------------------------------------------
    # SELECT * FROM tarefas ORDER BY data_criacao DESC
    #   = busca todas as colunas, ordenando da tarefa mais NOVA para
    #     a mais antiga.
    #
    # query() executa a consulta; num_rows informa quantas linhas
    # voltaram. Se houver linhas, o while lê linha por linha
    # (fetch_assoc() = cada linha vira um array) e acumula tudo
    # no array $tarefas.
    #
    # Retorno: array de arrays - ex.: [0 => ['id' => 1, 'descricao' => ...]]
    # Se não houver nenhuma tarefa, devolve a lista vazia [].
    # -------------------------------------------------------------------
    public function listar(){
        $tarefas = [];   # começa com uma lista vazia (caso não ache nada)
        $sql = "SELECT * FROM tarefas ORDER BY data_criacao DESC";
        $resultado = $this->conn->query($sql);   # executa a consulta

        if($resultado->num_rows>0){                      # tem resultado?
            while($row = $resultado->fetch_assoc()){     # lê uma linha por vez
                $tarefas[] = $row;                       # e acrescenta ao array
            }
        }

        return $tarefas;   # devolve a lista para o controller
    }

    # -------------------------------------------------------------------
    # Método: criar($descricao) - insere uma nova tarefa
    # -------------------------------------------------------------------
    # 1) real_escape_string() "escapa" aspas e caracteres especiais do
    #    texto - assim ele não quebra a consulta nem permite SQL Injection;
    # 2) Monta o INSERT com a descrição já tratada;
    # 3) query() executa e devolve true (ok) ou false (falhou).
    # -------------------------------------------------------------------
    public function criar($descricao){
        $descricao = $this->conn->real_escape_string($descricao);   # trata o texto
        $sql = "INSERT INTO tarefas (descricao) VALUES ('$descricao')";
        return $this->conn->query($sql);   # executa o INSERT
    }

    # -------------------------------------------------------------------
    # Método: excluir($id) - apaga uma tarefa pelo id
    # -------------------------------------------------------------------
    # intval() transforma o id em NÚMERO INTEIRO. Se alguém mandar
    # texto inválido pela URL, ele vira um número e não consegue
    # "injetar" código na consulta (proteção extra contra SQL Injection).
    # -------------------------------------------------------------------
    public function excluir($id){
        $id = intval($id);   # id vira número inteiro
        $sql = "DELETE FROM tarefas WHERE id = $id";
        return $this->conn->query($sql);   # executa o DELETE
    }

    # -------------------------------------------------------------------
    # Método: editar($descricao, $id) - atualiza a descrição da tarefa
    # -------------------------------------------------------------------
    # 1) Escape no NOVO texto (protege contra SQL Injection);
    # 2) intval no id (garante que é número);
    # 3) UPDATE troca apenas a descrição da tarefa daquele id,
    #    deixando data_criacao e os demais campos intactos.
    # -------------------------------------------------------------------
    public function editar($descricao, $id){
        $descricao = $this->conn->real_escape_string($descricao);   # trata o texto
        $id = intval($id);                                          # id vira número
        $sql = "UPDATE tarefas SET descricao = '$descricao' WHERE id = '$id'";
        return $this->conn->query($sql);   # executa o UPDATE
    }
}
?>
