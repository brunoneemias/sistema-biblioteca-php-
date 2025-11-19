<?php
session_start();
include 'db.php';
include 'funcoes.php'; // onde está a função registrar_log_atividades()

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $titulo = $_POST['titulo'];
  $autor = $_POST['autor'];
  $isbn = $_POST['isbn'];
  $editora = $_POST['editora'];
  $ano = $_POST['ano'];
  $quantidade = $_POST['quantidade'];
  $disponivel = $_POST['disponivel'];
  $data_cadastro = $_POST['data_cadastro'];

  $stmt = $conn->prepare("INSERT INTO `livro` (Titulo, Autor, isbn, editora, ano, quantidade, disponivel, data_cadastro) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
 $stmt->bind_param("ssssiiis", $titulo, $autor, $isbn, $editora, $ano, $quantidade, $disponivel, $data_cadastro);

  if ($stmt->execute()) {
    $mensagem = "Livro cadastrado com sucesso!";
    registrar_log_atividades($conn, "Cadastro de Livro", "Livro '{$titulo}' cadastrado", $_SESSION['usuario']['idUsuario']);
  } else {
    $mensagem = "Erro ao cadastrar: " . $stmt->error;
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastrar Livro</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 50px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #007BFF;
      margin-bottom: 30px;
    }
    form label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }
    form input {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background-color: #0056b3;
    }
    .mensagem {
      text-align: center;
      margin-top: 20px;
      font-weight: bold;
      color: green;
    }
  </style>
</head>
<body>
  <div class="container">
    <div style="margin-top: 30px; text-align: center;">
  <a href="dashboard.php" style="
    display: inline-block;
    padding: 10px 20px;
    background-color: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: 0.3s;
  ">
    ⬅ Voltar para o Dashboard
  </a>
</div>

    <h2><i class="fas fa-book-medical"></i> Cadastrar Novo Livro</h2>
    <form method="POST">
      <label>Título:</label>
      <input type="text" name="titulo" required>

      <label>Autor:</label>
      <input type="text" name="autor" required>

      <label>ISBN:</label>
      <input type="text" name="isbn">

      <label>Editora:</label>
      <input type="text" name="editora">

      <label>Ano:</label>
      <input type="number" name="ano">

      <label>Quantidade:</label>
      <input type="number" name="quantidade">

      <label>Disponível:</label>
      <input type="number" name="disponivel">

      <label>Data Cadastrado:</label>
      <input type="datetime-local" name="data_cadastro">

      <button type="submit"><i class="fas fa-save"></i> Cadastrar</button>
    </form>

    <?php if (isset($mensagem)): ?>
      <div class="mensagem"><?= $mensagem ?></div>
    <?php endif; ?>
  </div>
</body>
</html>