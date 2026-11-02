<?php
/**
 * =====================================================================
 * UserController - controlador do LOGIN
 * =====================================================================
 * Recebe os pedidos referentes ao login vindos da URL, conversa com o
 * model User (banco de dados) e decide qual view (tela) mostrar.
 *
 * Rotas que ele atende (definidas no index.php):
 *   action = login         ->  index()         mostra o formulário
 *   action = authenticate  ->  authenticate()  valida e-mail e senha
 *   action = logout        ->  logout()         encerra a sessão
 */

# Carrega o model de usuários. O require_once garante que ele será
# incluído apenas uma vez, mesmo que apareça em vários lugares.
require_once __DIR__ . "/../model/user.php";

Class UserController {
    # Atributo que guardará o objeto do model User, para o controlador
    # poder consultar o banco sempre que precisar (nos métodos abaixo).
    private $userModel;

    # -------------------------------------------------------------------
    # Método: __construct() - construtor da classe
    # -------------------------------------------------------------------
    # É executado AUTOMATICAMENTE quando criamos "new UserController()".
    # Cria o model User, e o construtor dele já abre a conexão com o
    # banco de dados - assim o controlador fica pronto para usar.
    # -------------------------------------------------------------------
    public function __construct() {
        $this->userModel = new User();
    }

    # -------------------------------------------------------------------
    # Método: index() - mostra a tela de login
    # -------------------------------------------------------------------
    # 1) Se a pessoa JÁ está logada, não faz sentido mostrar o login de
    #    novo: redireciona para a lista de tarefas (index.php).
    # 2) Se NÃO está logada, inclui a view login.php (o formulário).
    # -------------------------------------------------------------------
    public function index() {
        # isset($_SESSION['id']) = existe um id guardado na sessão?
        # (ou seja: está logado?)
        if (isset($_SESSION['id'])) {
            # header() envia um cabeçalho HTTP antes de qualquer HTML.
            # "Location: ..." manda o navegador para outra página.
            header("Location: index.php");   # já logado -> vai para a lista
            exit;                            # exit = encerra o script na hora
        }

        # include = carrega e executa o arquivo da view.
        # O formulário de login é "impresso" dentro desta página.
        include __DIR__ . "/../view/login.php";
    }

    # -------------------------------------------------------------------
    # Método: authenticate() - valida o login enviado pelo formulário
    # -------------------------------------------------------------------
    # Executada quando o formulário é enviado para
    # index.php?action=authenticate (método POST).
    #
    # Fluxo passo a passo:
    #   a) confere se email e senha chegaram no formulário;
    #   b) confere se estão preenchidos (não vazios);
    #   c) procura o e-mail no banco (através do model User);
    #   d) compara a senha digitada com a senha guardada (hash);
    #   e) se estiver tudo certo, cria a sessão e vai para a lista;
    #   f) senão, mostra o login de novo com mensagem de erro.
    # -------------------------------------------------------------------
    public function authenticate() {
        # isset($_POST['email'], $_POST['senha']) = os DOIS campos existem
        # no formulário enviado? Se não, o acesso foi feito "na mão" pela
        # URL e voltamos para a tela de login.
        if (!isset($_POST['email'], $_POST['senha'])) {
            header("Location: index.php?action=login");
            exit;
        }

        # trim() remove espaços em branco do começo e do fim do texto.
        # Evita que "  admin@x.com " seja tratado como outro e-mail.
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];   # a senha NÃO passa por trim (espaços contam)

        # Campos em branco? Então nem consulta o banco: devolve a view
        # com a mensagem de erro para o usuário preencher de novo.
        if (empty($email) || empty($senha)) {
            $error = "Preencha seu e-mail e senha";   # $error é lido pela view
            include __DIR__ . "/../view/login.php";   # mostra o formulário
            return;   # return = sai do método AGORA (não executa o resto)
        }

        # Procura no banco o usuário com esse e-mail.
        # Retorna o registro (array com id, nome, email, senha) ou
        # null/false se ninguém for encontrado.
        $usuario = $this->userModel->searchByEmail($email);

        # -----------------------------------------------------------------
        # password_verify(senha_digitada, hash_guardado) = compara a senha
        # em texto puro com o HASH salvo no banco. Devolve true apenas se
        # forem a mesma senha. Usamos ele (e não "==") porque a senha fica
        # protegida no banco: não dá para ler, só conferir.
        #
        # O "&&" exige as DUAS condições verdadeiras ao mesmo tempo:
        # o usuário existe E a senha está correta.
        # -----------------------------------------------------------------
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            # ---- Login bem-sucedido: monta a sessão ----
            $_SESSION['id'] = $usuario['id'];       # id do usuário logado
            $_SESSION['nome'] = $usuario['nome'];   # nome (usado no "Olá, ...")

            header("Location: index.php");   # manda para a lista de tarefas
            exit;                           # e encerra o script
        }

        # Chegou aqui = e-mail não encontrado OU senha errada.
        # Usamos a MESMA mensagem para os dois casos de propósito:
        # não convém avisar qual dos dois estava errado (segurança).
        $error = "E-mail ou senha inválidos";
        include __DIR__ . "/../view/login.php";   # mostra o login com o erro
    }

    # -------------------------------------------------------------------
    # Método: logout() - encerra a sessão (desloga)
    # -------------------------------------------------------------------
    # 1) session_destroy() apaga TODOS os dados da sessão atual
    #    (id, nome etc.) - ou seja, a pessoa deixa de estar logada;
    # 2) Redireciona para a tela de login.
    # -------------------------------------------------------------------
    public function logout() {
        session_destroy();   # apaga a sessão -> $_SESSION['id'] deixa de existir
        header("Location: index.php?action=login");   # volta para o login
        exit;
    }
}
?>
