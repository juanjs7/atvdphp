<?php
// Incluído no topo de toda página protegida
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
