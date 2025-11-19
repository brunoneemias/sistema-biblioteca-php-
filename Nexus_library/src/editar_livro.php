<?php
include 'db.php';
$id = $_GET['id'] ?? null;

if (!$id) {
  echo "ID inválido.";
  exit;
}

$stmt = $conn->prepare("SELECT * FROM Livro WHERE idLivro = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$livro = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $titulo = $_POST['titulo'];
  $autor = $_POST['autor'];
  $isbn = $_POST['isbn'];
  $editora = $_POST['editora'];
  $ano = $_POST['ano'];
  $quantidade = $_POST['quantidade'];
  $disponivel = $_POST['disponivel'];

  $stmt = $conn->prepare("UPDATE Livro SET Titulo=?, Autor=?, isbn=?, editora=?, ano=?, quantidade=?, disponivel=? WHERE idLivro=?");
  $stmt->bind_param("ssssiiii", $titulo, $autor, $isbn, $editora, $ano, $quantidade, $disponivel, $id);

  if ($stmt->execute()) {
    header("Location: livros.php");
    exit;
  } else {
    $erro = "Erro ao atualizar: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Livro</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: #f4f6f9;
      margin: 0;
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
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    .voltar {
      text-align: center;
      margin-top: 20px;
    }
    .voltar a {
      color: #6c757d;
      text-decoration: none;
      font-weight: bold;
    }
    .erro {
      color: red;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2><i class="fas fa-edit"></i> Editar Livro</h2>

    <?php if (isset($erro)): ?>
      <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Título:</label>
      <input type="text" name="titulo" value="<?= $livro['Titulo'] ?>" required>

      <label>Autor:</label>
      <input type="text" name="autor" value="<?= $livro['Autor'] ?>" required>

      <label>ISBN:</label>
      <input type="text" name="isbn" value="<?= $livro['ISBN'] ?>">

      <label>Editora:</label>
      <input type="text" name="editora" value="<?= $livro['editora'] ?>">

      <label>Ano:</label>
      <input type="number" name="ano" value="<?= $livro['ano'] ?>">

      <label>Quantidade:</label>
      <input type="number" name="quantidade" value="<?= $livro['quantidade'] ?>">

      <label>Disponível:</label>
      <input type="number" name="disponivel" value="<?= $livro['disponivel'] ?>">

      <button type="submit"><i class="fas fa-save"></i> Salvar Alterações</button>
   