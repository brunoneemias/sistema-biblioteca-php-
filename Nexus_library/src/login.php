<?php
include 'db.php';
session_start();
include 'funcoes.php';
if (isset($_SESSION['usuario'])) {
  header("Location: dashboard.php");
  exit;
  
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - Biblioteca Nexus</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(to right, #007BFF, #00BFFF);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-box {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
      width: 90%;
      max-width: 400px;
      text-align: center;
    }
    .login-box h2 {
      margin-bottom: 20px;
      color: #007BFF;
    }
    .input-group {
      margin-bottom: 20px;
      text-align: left;
    }
    .input-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }
    .input-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .btn {
      background-color: #007BFF;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      width: 100%;
      transition: 0.3s;
    }
    .btn:hover {
      background-color: #0056b3;
    }
    .icon {
      margin-right: 8px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2><i class="fas fa-lock icon"></i> Login da Biblioteca</h2>
    <form action="autenticar.php" method="POST">
      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Digite seu email" required>
      </div>
      <div class="input-group">
        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha" placeholder="Digite sua senha" required>
      </div>
      <button type="submit" class="btn"><i class="fas fa-sign-in-alt icon"></i> Entrar</button>
    </form>
  </div>
</body>
</html>