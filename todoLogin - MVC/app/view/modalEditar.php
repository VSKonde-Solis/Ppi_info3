<!-- ====================================================================
     view/modalEditar.php - Caixinha (modal) de EDIÇÃO de tarefa
     --------------------------------------------------------------------
     Este HTML fica ESCONDIDO (display: none, definido no CSS da listar.php)
     e só aparece quando o JavaScript abrirModal() é chamado pelo botão
     "Editar" de cada tarefa.

     O formulário envia os dados para index.php?action=editar, que o
     repassa ao tarefaController->editar() para atualizar no banco.
     ==================================================================== -->

<!-- div principal do modal: cobre a tela toda e fica invisível no início -->
<div id="modalEditar" class="modal">

    <!-- Caixa branca com o conteúdo do modal -->
    <div class="modal-conteudo">

        <h2>Editar tarefa</h2>

        <!-- ============================================================
             Formulário de edição
             ------------------------------------------------------------
             method = POST (dados fora da URL);
             action = rota "editar" do index.php.
             ============================================================ -->
        <form action="index.php?action=editar" method="POST">

            <!-- Campo ESCONDIDO: guarda o id da tarefa que será alterada.
                 "hidden" significa que não aparece para o usuário, mas o
                 valor é enviado junto no formulário. O JavaScript preenche
                 este campo com o id clicado (campo com id "editarId"). -->
            <input type="hidden" name="id" id="editarId">

            <!-- Novo texto da tarefa. Também é preenchido pelo JavaScript
                 (campo com id "editarDescricao") com a descrição atual,
                 permitindo editar a partir do que já existe. -->
            <input type="text" name="descricao" id="editarDescricao">

            <!-- Envia o formulário -> o controller atualiza no banco -->
            <button type="submit"> Salvar </button>

            <!-- type="button" NÃO envia o form: só chama a função JS
                 que esconde o modal (cancelar a edição) -->
            <button type="button" onclick="fecharModal()">Cancelar</button>

        </form>

    </div>

</div>
