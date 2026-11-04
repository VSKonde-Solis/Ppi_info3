<!DOCTYPE html>
<!-- ====================================================================
     view/cadastro.php - Tela de CADASTRO de usuário
     --------------------------------------------------------------------
     Esta view é exibida de duas formas:
       1) quando a pessoa clica em "Cadastre-se" (userController->cadastro());
       2) quando o cadastro falhou (userController->registrar() a inclui de
          novo, com $error preenchida e com $nome e $email para o
          formulário voltar preenchido).
     Ela apenas APRESENTA o formulário - quem valida é o controller.
     Reaproveita o mesmo visual (CSS) da tela de login.
     ==================================================================== -->
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - To-Do List</title>

    <!-- CSS puro da página (sem framework) para deixar o login centralizado -->
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;                  /* flexbox: permite centralizar... */
            justify-content: center;        /* ...na horizontal */
            align-items: center;            /* e na vertical */
            height: 100vh;                  /* ocupa 100% da altura da janela */
            margin: 0;                      /* remove a margem padrão do body */
            background-color: #f4f4f4;      /* cinza claro de fundo */
        }

        /* Caixa branca onde ficam título, campos e botão */
        .login-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;            /* cantos arredondados */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);  /* sombra suave */
            width: 320px;
        }

        .login-box h1 {
            margin-top: 0;
            text-align: center;             /* título centralizado */
        }

        /* Todos os campos de texto ocupam a largura toda da caixa */
        .login-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;            /* espaço entre um campo e outro */
            box-sizing: border-box;         /* padding entra na largura calculada */
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Botão "Entrar" em destaque */
        .login-box button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;      /* verde */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;                /* muda o mouse para "mãozinha" */
            font-size: 15px;
        }
        /* Texto pequeno com link ("Não tem conta? Cadastre-se") */
        .link {
            text-align: center;
            font-size: 14px;
            margin-bottom: 0;
        }

        /* Mensagem de erro (quando o login dá errado) */
        .erro {
            color: red;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h1>Criar conta</h1>

        <?php
        # Mostra a mensagem de erro só se o controller definiu $error
        # (campo vazio, e-mail inválido, senha curta, senhas diferentes,
        # e-mail já cadastrado...).
        if (!empty($error)): ?>
            <p class="erro"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- ============================================================
             Formulário de cadastro
             ------------------------------------------------------------
             action = rota "registrar" (userController->registrar());
             method = POST (os dados, principalmente a senha, não vão na URL).
             Os "name" de cada campo são as chaves lidas em $_POST[...].
             ============================================================ -->
        <form action="index.php?action=registrar" method="POST">
            <!-- value="..." devolve o que a pessoa digitou quando há erro.
                 "$nome ?? ''" = usa $nome se existir, senão texto vazio
                 (na primeira visita a variável ainda não existe).
                 htmlspecialchars() protege contra código malicioso. -->
            <input type="text" name="nome" placeholder="Nome"
                   value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
            <input type="email" name="email" placeholder="E-mail"
                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            <!-- Senhas NUNCA voltam preenchidas: sem "value" de propósito. -->
            <input type="password" name="senha" placeholder="Senha (mín. 6 caracteres)" required>
            <input type="password" name="confirmar_senha" placeholder="Confirmar senha" required>
            <button type="submit">Cadastrar</button>
        </form>

        <!-- Volta para a tela de login (rota "login"). -->
        <p class="link">Já tem conta? <a href="index.php?action=login">Entrar</a></p>
    </div>

</body>
</html>
