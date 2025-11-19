<?php
session_start();
include 'db.php';
include 'funcoes.php';

// Verifica se há um usuário logado antes de registrar o log
if (isset($_SESSION['usuario'])) {
  $idUsuario = $_SESSION['usuario']['idUsuario'];
  registrar_log_atividades($conn, "Logout", "Usuário encerrou a sessão", $idUsuario);
}

// Destroi a sessão e redireciona
session_destroy();
header("Location: login.php");
exit;
