<?php
// Configuração da conexão com o MySQL (XAMPP)
$host  = "127.0.0.1";
$user  = "root";
$pass  = "";              // no XAMPP a senha do root vem vazia
$banco = "gestao_cafeteria";
$porta = 3307;            // porta do MySQL mostrada no XAMPP (padrão seria 3306)

$conn = mysqli_connect($host, $user, $pass, $banco, $porta);

if (!$conn) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");