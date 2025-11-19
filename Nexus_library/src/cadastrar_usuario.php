<?php
include 'db.php';
include 'funcoes.php'; // onde está a função criptografarAES()
session_start();
// Verifica se o usuário é administrador
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $senha_hash = password_hash($_POST['senha'], PASSWORD_BCRYPT);
  $perfil = $_POST['perfil'];

  $stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha_hash, perfil) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $nome, $email, $senha_hash, $perfil);

  if ($stmt->execute()) {
    header("Location: usuarios.php");
    registrar_log_atividades($conn, "Cadastro de Usuário", "Usuário $nome cadastrado", $idAdmin);
    exit;
  } else {
    $erro = "Erro ao cadastrar: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastrar Usuário</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Roboto', sans-serif; background: #f4f6f9; margin: 0; }
    .container {
      max-width: 500px; margin: 50px auto; background: white; padding: 30px;
      border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; color: #007BFF; margin-bottom: 20px; }
    label { display: block; margin-top: 15px; font-weight: bold; }
    input, select {
      width: 100%; padding: 10px; margin-top: 5px;
      border: 1px solid #ccc; border-radius: 6px;
    }
    button {
      margin-top: 25px; width: 100%; padding: 12px;
      background-color: #007BFF; color: white;
      border: none; border-radius: 6px; font-weight: bold; cursor: pointer;
    }
    .voltar { text-align: center; margin-top: 20px; }
    .voltar a { color: #6c757d; text-decoration: none; font-weight: bold; }
    .erro { color: red; text-align: center; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Cadastrar Novo Usuário</h2>

    <?php if (isset($erro)): ?>
      <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Nome:</label>
      <input type="text" name="nome" required>

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Senha:</label>
      <input type="password" name="senha" required>

      <label>Perfil:</label>
      <select name="perfil" required>
        <option value="administrador">Administrador</option>
        <option value="bibliotecario">Bibliotecário</option>
        <option value="usuario">Usuário</option>
      </select>

      <button type="submit">Cadastrar</button>
    </form>

    <div class="voltar">
      <p><a href="usuarios.php">⬅ Voltar para a lista</a></p>
    </div>
  </div>
</body>
</html>