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
 *   action = cadastro      ->  cadastro()       mostra o formulário de cadastro
 *   action = registrar     ->  registrar()      valida e grava o novo usuário
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
    # Método: cadastro() - mostra a tela de cadastro
    # -------------------------------------------------------------------
    # Igual ao index() do login: só EXIBE o formulário (view cadastro.php).
    # Quem já está logado é mandado de volta para a lista de tarefas,
    # pois não precisa criar outra conta.
    # -------------------------------------------------------------------
    public function cadastro() {
        if (isset($_SESSION['id'])) {
            header("Location: index.php");   # quem já está logado não precisa se cadastrar
            exit;
        }

        include __DIR__ . "/../view/cadastro.php";   # só mostra o formulário
    }

    # -------------------------------------------------------------------
    # Método: registrar() - valida e grava o novo usuário
    # -------------------------------------------------------------------
    # Executada quando o formulário de cadastro é enviado para
    # index.php?action=registrar (método POST).
    #
    # Fluxo passo a passo:
    #   a) confere se os 4 campos chegaram no formulário;
    #   b) limpa os valores (trim) e guarda em variáveis;
    #   c) aplica as VALIDAÇÕES, uma por vez, até achar um erro;
    #   d) se achou erro, mostra o formulário de novo com a mensagem;
    #   e) se está tudo certo, grava no banco (model User->create);
    #   f) cria a sessão (login automático) e vai para a lista.
    # -------------------------------------------------------------------
    public function registrar() {
        # Os quatro campos existem no POST? Se não, a pessoa acessou a
        # URL "na mão" (sem enviar o formulário): volta para o cadastro.
        if (!isset($_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['confirmar_senha'])) {
            header("Location: index.php?action=cadastro");
            exit;
        }

        # trim() tira espaços do começo/fim. A senha (e a confirmação)
        # NÃO passam por trim, pois espaços fazem parte dela.
        $nome  = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];
        $confirmar = $_POST['confirmar_senha'];

        # ---- validações: se falhar, mostra o formulário de novo com a mensagem ----
        # $error começa como null ("nenhum erro até agora"). Cada validação
        # abaixo só roda se a anterior passou, por causa do elseif: a
        # primeira que falhar define a mensagem e as demais são ignoradas.
        $error = null;

        if ($nome === '' || $email === '' || $senha === '' || $confirmar === '') {
            $error = "Preencha todos os campos";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            # filter_var + FILTER_VALIDATE_EMAIL confere se o texto tem
            # formato de e-mail (algo@dominio.com). Devolve false se não tiver.
            $error = "E-mail inválido";
        } elseif (strlen($senha) < 6) {
            # strlen() = quantidade de caracteres da senha.
            $error = "A senha precisa ter pelo menos 6 caracteres";
        } elseif ($senha !== $confirmar) {
            # !== compara valor E tipo; aqui, senha e confirmação diferentes.
            $error = "As senhas não conferem";
        } elseif ($this->userModel->searchByEmail($email)) {
            # Reaproveita o método do login: se ele devolver um usuário
            # (e não null), esse e-mail já está em uso.
            $error = "Este e-mail já está cadastrado";
        }

        # Houve algum erro? Mostra o formulário de novo. Como o include
        # acontece neste método, a view enxerga $error, $nome e $email
        # (por isso o formulário volta com nome/e-mail preenchidos).
        if ($error !== null) {
            include __DIR__ . "/../view/cadastro.php";   # a view lê $error e $nome/$email
            return;   # sai do método: nada é gravado
        }

        # ---- tudo certo: grava e já deixa a pessoa logada ----
        # create() devolve true se o INSERT funcionou.
        if ($this->userModel->create($nome, $email, $senha)) {
            # Busca o usuário recém-criado só para descobrir o id que o
            # banco gerou (AUTO_INCREMENT).
            $usuario = $this->userModel->searchByEmail($email);   # busca para pegar o id novo

            # session_regenerate_id(true) troca o identificador da sessão
            # e descarta o antigo. Boa prática de segurança ao mudar o
            # estado de "visitante" para "logado" (evita session fixation).
            session_regenerate_id(true);                          # novo id de sessão (segurança)

            # Mesma sessão que o authenticate() cria no login.
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];

            header("Location: index.php");   # vai direto para a lista
            exit;
        }

        # Chegou aqui = o INSERT falhou (ex.: erro no banco). Avisa a pessoa.
        $error = "Não foi possível cadastrar. Tente novamente";
        include __DIR__ . "/../view/cadastro.php";
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
