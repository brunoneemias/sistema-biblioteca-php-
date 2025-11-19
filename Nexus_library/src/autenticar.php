<?php
session_start();
include 'db.php';
include 'funcoes.php';

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email']) || !isset($_POST['senha'])) {
  echo "<script>alert('Acesso inválido.'); window.location.href='login.php';</script>";
  exit;
}

// Captura os dados do formulário
$email = $_POST['email'];
$senha = $_POST['senha'];

// Consulta o usuário no banco
$sql = "SELECT * FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

// Verifica se o usuário existe e compara a senha
if ($usuario && password_verify($_POST['senha'], $usuario['senha_hash'])) {
  $_SESSION['usuario'] = $usuario;
  registrar_log_atividades($conn, 'Login', 'Usuário autenticado com sucesso', $usuario['idUsuario']);
  header("Location: dashboard.php");
  exit;
} else {
  $mensagem = $usuario ? 'Tentativa de login com senha incorreta' : 'Tentativa de login com email não cadastrado';
  registrar_log_atividades($conn, 'Login', $mensagem, null);
  echo "<script>alert('Usuário ou senha inválidos!'); window.location.href='login.php';</script>";
}
