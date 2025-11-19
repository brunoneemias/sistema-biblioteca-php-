<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'bibliotecario') {
  header("Location: login.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $idEmprestimo = $_POST['idEmprestimo'] ?? null;
  $dataHoje = date('Y-m-d');

  if ($idEmprestimo) {
    // Buscar idLivro relacionado
    $stmt = $conn->prepare("SELECT idLivro FROM Emprestimo WHERE idEmprestimo = ?");
    $stmt->bind_param("i", $idEmprestimo);
    $stmt->execute();
    $result = $stmt->get_result();
    $dados = $result->fetch_assoc();
    $idLivro = $dados['idLivro'];

    // Atualizar empréstimo
    $stmt = $conn->prepare("UPDATE Emprestimo SET status = 'devolvido', dataDevolvido = ? WHERE idEmprestimo = ?");
    $stmt->bind_param("si", $dataHoje, $idEmprestimo);
    $stmt->execute();

    // Repor disponibilidade
    $conn->query("UPDATE Livro SET disponivel = disponivel + 1 WHERE idLivro = $idLivro");
  }

  header("Location: emprestimos.php");
  exit;
}
?>