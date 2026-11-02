<?php
/**
 * =====================================================================
 * index.php - Ponto de entrada da aplicação (front controller)
 * =====================================================================
 * TODA a aplicação começa neste arquivo. Ele tem três funções:
 *
 *   1) Abrir a sessão do usuário (session_start).
 *   2) Escolher qual controller executar conforme o parâmetro "action"
 *      da URL (ex.: index.php?action=criar).
 *   3) Proteger as tarefas: quem não está logado só consegue acessar
 *      as rotas de login.
 *
 * Fluxo resumido:
 *   action = login / authenticate / logout  ->  UserController
 *   qualquer outra action SEM sessão        ->  tela de login (bloqueado)
 *   qualquer outra action COM sessão        ->  tarefaController
 */

# -------------------------------------------------------------------
# session_start() - inicia/retorna a sessão do usuário.
# Precisa ser a PRIMEIRA coisa do arquivo (antes de qualquer HTML).
# É ele que permite guardar dados entre páginas, como $_SESSION['id']
# (que prova que a pessoa está logada) e $_SESSION['nome'].
# -------------------------------------------------------------------
session_start();

# -------------------------------------------------------------------
# require_once - carrega outro arquivo PHP, mas apenas UMA vez mesmo
# que seja chamado várias vezes (evita o erro "class already defined").
# __DIR__ = pasta onde este arquivo está; "/../" = sobe um nível.
# -------------------------------------------------------------------
require_once __DIR__ . '/app/controller/userController.php';  # controlador do login
require_once __DIR__ . '/app/controller/controller.php';      # controlador das tarefas

# Cria o objeto do controller de login. No construtor dele já é
# instanciado o model User, que por sua vez abre a conexão com o banco.
$userController = new UserController();

# -------------------------------------------------------------------
# $_GET['action'] lê o parâmetro enviado na URL.
# O "?? " é o operador de coalescência: se "action" não existir na URL,
# usa o valor padrão 'index' (aí a lista de tarefas é exibida).
# -------------------------------------------------------------------
$action = $_GET['action'] ?? 'index';

# ========================= ROTAS DO LOGIN ===========================
# in_array($valor, $lista) = verifica se $action está dentro da lista.
# Estas três rotas são as únicas permitidas SEM estar logado.
if (in_array($action, ['login', 'authenticate', 'logout'])) {
    # switch = escolhe o bloco "case" que corresponde ao valor de $action
    switch ($action) {
        case 'login':           # index.php?action=login -> mostra o formulário
            $userController->index();
            break;              # break encerra o switch (não cai no próximo case)
        case 'authenticate':    # index.php?action=authenticate -> valida e-mail/senha
            $userController->authenticate();
            break;
        case 'logout':          # index.php?action=logout -> desloga
            $userController->logout();
            break;
    }
    exit;   # encerra o script aqui: rotas de login NUNCA chegam às tarefas
}

# =================== CONTROLE DE ACESSO (GUARD) =====================
# Se não existe $_SESSION['id'], a pessoa NÃO está logada.
# Em vez de deixar passar, mostramos a tela de login.
if (!isset($_SESSION['id'])) {
    $userController->index();   # inclui a view login.php (formulário)
    exit;                       # para o script: as tarefas ficam protegidas
}

# ======================= ROTAS DAS TAREFAS ==========================
# Só chega aqui quem tem sessão ativa (logado).
$controller = new tarefaController();

switch ($action) {
    case 'criar':           # POST com "descricao" -> cadastra a tarefa
        $controller->criar();
        break;
    case 'excluir':         # GET com "?id=" -> apaga a tarefa
        $controller->excluir();
        break;
    case 'editar':          # POST com "id" + "descricao" -> atualiza
        $controller->editar();
        break;
    default:                # qualquer outra action (ou nenhuma) -> lista
        $controller->index();
        break;
}
?>
