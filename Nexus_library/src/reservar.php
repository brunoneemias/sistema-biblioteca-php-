<?php
session_start();
include 'db.php';
include 'funcoes.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'usuario') {
  header("Location: login.php");
  exit;
}

$idUsuario = $_SESSION['usuario']['idUsuario'];
$idLivro = $_POST['idLivro'] ?? null;

if ($idLivro) {
  // Verifica se o usuário já tem uma reserva ativa para esse livro
  $verifica = $conn->prepare("SELECT idReserva FROM Reserva WHERE idUsuario = ? AND idLivro = ? AND status = 'ativa'");
  $verifica->bind_param("ii", $idUsuario, $idLivro);
  $verifica->execute();
  $verifica->store_result();

  if ($verifica->num_rows === 0) {
    $dataReserva = date('Y-m-d');
    $dataExpiracao = date('Y-m-d', strtotime('+3 days'));

    $stmt = $conn->prepare("
      INSERT INTO Reserva (idUsuario, idLivro, dataReserva, dataExpiracao, status)
      VALUES (?, ?, ?, ?, 'ativa')
    ");
    $stmt->bind_param("iiss", $idUsuario, $idLivro, $dataReserva, $dataExpiracao);
    $stmt->execute();
  }
  registrarLog($conn, $idUsuario, 'Reserva', "Livro ID $idLivro reservado");
}

header("Location: painel_usuario.php");
exit;
?>