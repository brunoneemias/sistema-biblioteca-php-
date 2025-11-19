<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'bibliotecario') {
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel do Bibliotecário</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: #f4f6f9;
      margin: 0;
    }
    .container {
      max-width: 800px;
      margin: 60px auto;
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
    }
    h2 {
      color: #007BFF;
      margin-bottom: 30px;
    }
    .menu {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
    }
    .menu a {
      display: block;
      padding: 20px;
      background-color: #007BFF;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
    }
    .menu a:hover {
      background-color: #0056b3;
    }
    .menu i {
      margin-right: 8px;
    }
    .logout {
      margin-top: 30px;
    }
    .logout a {
      color: #dc3545;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2><i class="fas fa-user-cog"></i> Painel do Bibliotecário</h2>

    <div class="menu">
      <a href="livros.php"><i class="fas fa-book"></i> Gerenciar Livros</a>
      <a href="cadastrar_livro.php"><i class="fas fa-plus"></i> Cadastrar Livro</a>
      <a href="emprestimos.php"><i class="fas fa-box"></i> Empréstimos</a>
      <a href="historico.php"><i class="fas fa-history"></i> Histórico</a>
    </div>

    <div class="logout">
      <p><a href="logout.php">⏻ Sair</a></p>
    </div>
  </div>
</body>
</html>