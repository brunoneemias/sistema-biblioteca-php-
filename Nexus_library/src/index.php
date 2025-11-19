<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Biblioteca Nexus</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(to right, #007BFF, #00BFFF);
      color: #fff;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .container {
      background: #ffffff10;
      padding: 40px;
      border-radius: 12px;
      text-align: center;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }
    h1 {
      font-size: 2em;
      margin-bottom: 10px;
    }
    p {
      font-size: 1.1em;
      margin-bottom: 30px;
    }
    .button {
      display: inline-block;
      margin: 10px;
      padding: 12px 24px;
      background-color: #fff;
      color: #007BFF;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      transition: 0.3s;
    }
    .button:hover {
      background-color: #f0f0f0;
    }
    .icon {
      font-size: 1.2em;
      margin-right: 8px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1><i class="fas fa-book-open icon"></i> Biblioteca Nexus</h1>
    <p>Bem-vindo ao sistema de gerenciamento de livros e empréstimos.</p>

    <?php if (isset($_SESSION['usuario'])): ?>
      <p>Logado como <strong><?= $_SESSION['usuario']['nome'] ?></strong> (<?= $_SESSION['usuario']['perfil'] ?>)</p>
      <a class="button" href="dashboard.php"><i class="fas fa-tachometer-alt icon"></i> Ir para o painel</a>
      <a class="button" href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt icon"></i> Sair</a>
    <?php else: ?>
      <a class="button" href="login.php"><i class="fas fa-sign-in-alt icon"></i> Entrar no sistema</a>
    <?php endif; ?>
  </div>
</body>
</html>