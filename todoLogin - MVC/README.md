# todoLogin - MVC

Integração do **Login-MVC** com o **todoList - MVC**: para ver as tarefas é preciso estar logado.

## Como rodar

**Opção 1 — XAMPP (Apache):**
1. Copie a pasta `todoLogin - MVC` para `C:\xampp\htdocs\`
2. Inicie o Apache e o MySQL no painel do XAMPP
3. Acesse `http://localhost/todoLogin - MVC/index.php`

**Opção 2 — servidor PHP embutido:**
```
C:\xampp\php\php.exe -S localhost:8000 -t "caminho\todoLogin - MVC"
```
Depois acesse `http://localhost:8000/index.php`

## Banco de dados

Tudo usa o banco **`todo_list`**. Rode o arquivo **`banco.sql`** uma única vez no phpMyAdmin
(cria a tabela `usuarios` e o usuário de teste; a tabela `tarefas` já existente não é alterada).

## Usuário de teste

| Campo  | Valor            |
|--------|------------------|
| E-mail | admin@todo.com   |
| Senha  | 123456           |

## Estrutura

```
todoLogin - MVC/
├── index.php                    ← roteador + controle de sessão (protege as tarefas)
├── banco.sql                    ← cria tabela usuarios + usuário de teste
└── app/
    ├── config/database.php      ← conexão única com o banco todo_list
    ├── controller/
    │   ├── userController.php   ← login (authenticate) e logout
    │   └── controller.php       ← tarefas (inalterado)
    ├── model/
    │   ├── user.php             ← busca usuário por e-mail
    │   └── tarefa.php           ← inalterado
    └── view/
        ├── login.php            ← tela de login
        ├── listar.php           ← lista de tarefas + "Olá, Admin! / Sair"
        └── modalEditar.php      ← inalterado
```

## O que foi integrado

- `index.php` virou o roteador único: as rotas `login`, `authenticate` e `logout` são do login;
  as demais (`criar`, `excluir`, `editar`, lista) só funcionam com `$_SESSION['id']` definido —
  sem login, a pessoa é levada para a tela de login.
- `userController` completado: valida e-mail/senha no banco com `password_verify` e cria a sessão.
- `login.php` e o botão **Sair** (`action=logout`) criados.
- As duas classes `Database` foram unificadas em uma só, apontando para o banco `todo_list`.
