<?php
/**
 * =====================================================================
 * tarefaController - controlador das TAREFAS (to-do list)
 * =====================================================================
 * Recebe os pedidos de listar / criar / excluir / editar tarefas,
 * conversa com o model Tarefa (banco de dados) e devolve a view
 * correspondente.
 *
 * IMPORTANTE: só é chamado pelo index.php DEPOIS que o login foi
 * conferido (quem não está logado nem chega aqui).
 *
 * Rotas que ele atende (definidas no index.php):
 *   (nenhuma action)  ->  index()     lista as tarefas
 *   action = criar    ->  criar()     cadastra
 *   action = excluir  ->  excluir()   apaga
 *   action = editar   ->  editar()    atualiza
 */

# Carrega o model Tarefa (o require_once evita incluir duas vezes).
require_once __DIR__ . '/../model/tarefa.php';

class tarefaController{
    # Atributo que guardará o objeto do model Tarefa, usado por todos
    # os métodos abaixo para acessar o banco de dados.
    private $tarefaModel;

    # -------------------------------------------------------------------
    # Método: __construct() - construtor da classe
    # -------------------------------------------------------------------
    # Executado AUTOMATICAMENTE ao criarmos "new tarefaController()".
    # Instancia o model Tarefa, cujo construtor já abre a conexão com
    # o banco - deixando o controlador pronto para trabalhar.
    # -------------------------------------------------------------------
    public function __construct(){
        $this->tarefaModel = new Tarefa();
    }

    # -------------------------------------------------------------------
    # Método: index() - lista todas as tarefas
    # -------------------------------------------------------------------
    # 1) Pede ao model a lista completa de tarefas (do banco);
    # 2) Inclui a view listar.php - ela recebe a variável $tarefas e
    #    desenha cada tarefa na tela usando um laço foreach.
    # -------------------------------------------------------------------
    public function index(){
        $tarefas = $this->tarefaModel->listar();   # array com as tarefas
        include __DIR__ . '/../view/listar.php';   # mostra a página da lista
    }

    # -------------------------------------------------------------------
    # Método: criar() - cadastra uma nova tarefa (formulário POST)
    # -------------------------------------------------------------------
    # 1) Confere se o campo "descricao" chegou e não está vazio
    #    (o trim() ignora um texto feito só de espaços);
    # 2) Se estiver ok, grava a tarefa no banco através do model;
    # 3) Redireciona de volta para a lista.
    #
    # Esse padrão (gravar -> redirecionar) é o POST/redirect/GET:
    # evita que apertar F5 reenvie o formulário e crie tarefa duplicada.
    # -------------------------------------------------------------------
    public function criar(){
        # isset = o campo existe? E !empty = tem alguma coisa além de espaços?
        if(isset($_POST['descricao']) && !empty(trim($_POST['descricao']))){
            $this->tarefaModel->criar($_POST['descricao']);   # INSERT no banco
        }
        header("Location: index.php");   # volta para a lista (sem reenviar o POST)
    }

    # -------------------------------------------------------------------
    # Método: excluir() - apaga uma tarefa (link GET com ?id=)
    # -------------------------------------------------------------------
    # 1) Lê o id enviado na URL ($_GET['id']);
    # 2) Manda o model apagar o registro com esse id;
    # 3) Redireciona para a lista já atualizada (a tarefa some da tela).
    # -------------------------------------------------------------------
    public function excluir(){
        if(isset($_GET['id'])){   # veio um id na URL?
            $this->tarefaModel->excluir($_GET['id']);   # DELETE no banco
        }
        header("Location: index.php");   # recarrega a lista
    }

    # -------------------------------------------------------------------
    # Método: editar() - atualiza a descrição de uma tarefa (POST)
    # -------------------------------------------------------------------
    # 1) Confere se chegaram o id e a descrição, e se a descrição não
    #    está vazia;
    # 2) Chama o model passando o NOVO texto e o id da tarefa;
    # 3) Redireciona para a lista (mostrando a descrição já alterada).
    # -------------------------------------------------------------------
    public function editar(){
        # As três condições precisam ser verdadeiras AO MESMO TEMPO (&&).
        if(isset($_POST['id']) && isset($_POST['descricao']) && !empty(trim($_POST['descricao']))){
            $this->tarefaModel->editar($_POST['descricao'], $_POST['id']);   # UPDATE no banco
        }
        header("Location: index.php");   # volta para a lista
    }
}
?>
