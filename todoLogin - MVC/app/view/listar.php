<!DOCTYPE html>
<!-- ====================================================================
     view/listar.php - Tela da LISTA DE TAREFAS
     --------------------------------------------------------------------
     Exibida pelo tarefaController->index() DEPOIS do login (o index.php
     só chega aqui com $_SESSION['id'] definido).

     Ela recebe do controller a variável $tarefas (array vindo do model)
     e desenha cada tarefa na tela.
     ==================================================================== -->
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>To-Do List (MVC)</title>

    <style>
        /* Modal = caixinha flutuante usada para EDITAR a tarefa */
        .modal {
            display: none;              /* escondido por padrão; o JS mostra */

            position: fixed;            /* fica em cima de todo o conteúdo */
            top: 0;
            left: 0;

            width: 100%;
            height: 100%;               /* cobre a tela inteira */

            background-color: rgba(0, 0, 0, 0.5);   /* fundo escurecido */
        }

        /* Conteúdo interno do modal (a caixa branca) */
        .modal-conteudo {
            background-color: white;

            width: 400px;

            margin: 150px auto;         /* centraliza horizontalmente */
            padding: 20px;

            border-radius: 10px;
        }
    </style>
</head>
<body>

    <h1>TO-DO List (MVC)</h1>

    <!-- ============================================================
         Saudação + botão de sair
         ------------------------------------------------------------
         $_SESSION['nome'] foi guardado no login (userController).
         htmlspecialchars() protege a saída contra HTML/JS injetado.
         O link "Sair" aponta para a rota logout, que destrói a sessão.
         ============================================================ -->
    <p>
        Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!
        <a href="index.php?action=logout">Sair</a>
    </p>

    <!-- ============================================================
         Formulário de CRIAÇÃO de tarefa
         ------------------------------------------------------------
         Envia por POST a descrição para index.php?action=criar.
         O campo precisa se chamar "descricao" (é o que o controller lê).
         ============================================================ -->
    <form action="index.php?action=criar" method="POST">
        <input type="text" name="descricao" placeholder="Descrição da tarefa">
        <button type="submit">Adicionar</button>
    </form>

    <h2>Suas tarefas</h2>

    <?php
    # -----------------------------------------------------------------
    # !empty($tarefas) = o array existe e não está vazio?
    # (ou seja: o model encontrou tarefas no banco)
    # -----------------------------------------------------------------
    if (!empty($tarefas)): ?>
        <ul>
            <?php
            # foreach percorre CADA tarefa do array, guardando-a em $tarefa.
            # Na prática, roda uma vez para cada linha vinda do banco.
            foreach ($tarefas as $tarefa): ?>
                <li>
                    <!-- Mostra a descrição da tarefa atual -->
                    <?php echo ($tarefa['descricao']); ?>

                    <!-- ============================================================
                         Link de EXCLUIR - é um GET com o id da tarefa na URL.
                         O index.php repassa para o tarefaController->excluir(),
                         que apaga no banco e redireciona de volta.
                         ============================================================ -->
                    <a href="index.php?action=excluir&id=<?php echo $tarefa['id']; ?>">
                        Excluir
                    </a>

                    <!-- ============================================================
                         Botão de EDITAR - não envia formulário: chama a função
                         JavaScript abrirModal() passando o id e a descrição
                         desta tarefa. O JS preenche o modal e o mostra.
                         ============================================================ -->
                    <button
                        onclick="abrirModal(<?php echo $tarefa['id']; ?>,
                            '<?php echo ($tarefa['descricao']); ?>')">
                        Editar
                    </button>
            <?php endforeach; /* fecha o laço das tarefas */ ?>
        </ul>
    <?php else: ?>
        <!-- Nenhuma tarefa encontrada no banco -->
        <p>Não há tarefas ainda!</p>
    <?php endif; ?>

    <?php
    # -----------------------------------------------------------------
    # Inclui o modal de edição (caixinha que fica oculta até o JS mostrar).
    # __DIR__ = pasta desta view; o include "joga" o HTML do modal aqui.
    # -----------------------------------------------------------------
    include __DIR__ . '\modalEditar.php'; ?>

    <!-- ============================================================
         JavaScript do modal (roda no NAVEGADOR, não no servidor)
         ============================================================ -->
    <script>
        // -----------------------------------------------------------
        // abrirModal(id, descricao) - abre a caixinha de edição
        // -----------------------------------------------------------
        // Chamada pelo botão "Editar" de cada tarefa, recebendo o id
        // e a descrição da tarefa clicada.
        // -----------------------------------------------------------
        function abrirModal(id, descricao){
            // Preenche o campo escondido "id" com o id da tarefa
            document.getElementById("editarId").value = id
            // Preenche o campo de texto com a descrição atual
            document.getElementById("editarDescricao").value = descricao
            // display = "block" torna o modal visível (era "none")
            document.getElementById("modalEditar").style.display = "block";
        }

        // fecharModal() - esconde o modal (botão "Cancelar")
        function fecharModal(){
            // Volta a esconder o modal, sem alterar nada no banco
            document.getElementById("modalEditar").style.display = "none";
        }
    </script>

</body>
</html>
