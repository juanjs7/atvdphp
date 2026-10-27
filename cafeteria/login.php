<?php
session_start();
require_once "conexao.php";

// Se já está logado, vai direto para o painel
if (isset($_SESSION['usuario_id'])) {
    header("Location: painel.php");
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if ($usuario === '' || $senha === '') {
        $erro = "Preencha usuário e senha.";
    } else {
        // Prepared Statement: protege contra SQL Injection
        $stmt = mysqli_prepare($conn, "SELECT id, nome, senha FROM usuarios WHERE usuario = ?");
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $dados = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);

        // Aceita senha com hash (password_hash) ou a senha em texto puro
        // do script SQL fornecido pelo professor ('admin123')
        if ($dados && (password_verify($senha, $dados['senha']) || $senha === $dados['senha'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id']   = $dados['id'];
            $_SESSION['usuario_nome'] = $dados['nome'];
            header("Location: painel.php");
            exit;
        } else {
            $erro = "Usuário ou senha inválidos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cafeteria</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="tela-login">
    <div class="card login">
        <h1>☕ Cafeteria</h1>
        <p class="sub">Acesso do gerente</p>

        <?php if ($erro): ?>
            <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="usuario">Usuário</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
