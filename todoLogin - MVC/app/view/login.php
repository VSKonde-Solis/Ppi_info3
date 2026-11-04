<!DOCTYPE html>
<!-- ====================================================================
     view/login.php - Tela de LOGIN
     --------------------------------------------------------------------
     Esta view é exibida de duas formas:
       1) quando o usuário ainda NÃO está logado (index.php mostra ela);
       2) quando o login falhou (o userController a inclui de novo, com
          a variável $error preenchida para mostrar o aviso).
     Ela apenas APRESENTA o formulário - quem valida é o controller.
     ==================================================================== -->
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - To-Do List</title>

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
            background-color: #64007D;      
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
        <h1>To-Do List</h1>

        <?php
        # -----------------------------------------------------------------
        # Bloco condicional PHP (sintaxe alternativa com ":" e endif).
        # !empty($error) = existe e não está vazia a mensagem de erro?
        # Ela só é definida pelo userController quando o login falha
        # (campos vazios ou e-mail/senha errados).
        # -----------------------------------------------------------------
        if (!empty($error)): ?>
            <!-- htmlspecialchars() converte os caracteres especiais em
                 entidades HTML (&lt;, &gt;), impedindo que um texto
                 malicioso seja interpretado como código pela página. -->
            <p class="erro"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- ============================================================
             Formulário de login
             ------------------------------------------------------------
             action  = para onde os dados são enviados (rota authenticate);
             method  = POST deixa os dados fora da URL (mais seguro que GET);
             required = o navegador exige o preenchimento antes de enviar.
             ============================================================ -->
        <form action="index.php?action=authenticate" method="POST">
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>   <!-- type=submit envia o form -->
        </form>

        <!-- Link para a tela de cadastro: abre a rota "cadastro" no index.php
             (um link comum faz uma requisição GET). -->
        <p class="link">Não tem conta? <a href="index.php?action=cadastro">Cadastre-se</a></p>
    </div>

</body>
</html>
