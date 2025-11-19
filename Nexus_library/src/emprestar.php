<?php
session_start();
include 'db.php';
registrarLog($conn, 'Login', "Usuário logado com sucesso", $idUsuario);


if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'bibliotecario') {
  header("Location: login.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $idUsuario = $_POST['usuario'] ?? null;
  $idLivro = $_POST['livro'] ?? null;
  $dataDevolucao = $_POST['data_devolucao'] ?? null;
  $dataEmprestimo = date('Y-m-d');

  if (!$idUsuario || !$idLivro || !$dataDevolucao) {
    header("Location: emprestimos.php?erro=campos");
    exit;
  }

  // Verifica disponibilidade
  $stmt = $conn->prepare("SELECT disponivel FROM Livro WHERE idLivro = ?");
  $stmt->bind_param("i", $idLivro);
  $stmt->execute();
  $result = $stmt->get_result();
  $livro = $result->fetch_assoc();

  if (!$livro || $livro['disponivel'] <= 0) {
    header("Location: emprestimos.php?erro=indisponivel");
    exit;
  }

  // Registrar empréstimo
    $stmt = $conn->prepare("
    INSERT INTO Emprestimo (idUsuario, idLivro, dataEmprestimo, dataDevolucao, status)
    VALUES (?, ?, ?, ?, 'ativo')
    ");
    $stmt->bind_param("iiss", $idUsuario, $idLivro, $dataEmprestimo, $dataDevolucao);
  $stmt->execute();

    // depois disso:
    registrarLog($conn, $_SESSION['usuario']['idUsuario'], 'Empréstimo', "Livro ID $idLivro emprestado para usuário ID $idUsuario");

  // Atualizar disponibilidade
    $conn->query("UPDATE Livro SET disponivel = disponivel - 1 WHERE idLivro = $idLivro");

  header("Location: emprestimos.php?sucesso=1");
  exit;
} else {
  header("Location: emprestimos.php");
  exit;
}
?>