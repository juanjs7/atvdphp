<?php
require_once "verifica_login.php";
require_once "conexao.php";

// ---------- D - DELETE ----------
$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM cardapio WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: painel.php?msg=excluido");
exit;
