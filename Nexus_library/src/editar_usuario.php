<?php
include 'db.php';
include 'funcoes.php';  
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
  echo "ID inválido.";
  exit;
}

$stmt = $conn->prepare("SELECT * FROM Usuario WHERE idUsuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $perfil = $_POST['perfil'];

  $stmt = $conn->prepare("UPDATE Usuario SET nome=?, email=?, perfil=? WHERE idUsuario=?");
  $stmt->bind_param("sssi", $nome, $email, $perfil, $id);

  if ($stmt->execute()) {
    header("Location: usuarios.php");
    registrar_log_atividades($conn, "Edição de Perfil", "Usuário alterou seus dados", $idUsuario);
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
  <title>Editar Usuário</title>
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
      background-color: #28a745; color: white;
      border: none; border-radius: 6px; font-weight: bold; cursor: pointer;
    }
    .voltar { text-align: center; margin-top: 20px; }
    .voltar a { color: #6c757d; text-decoration: none; font-weight: bold; }
    .erro { color: red; text-align: center; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Editar Usuário</h2>

    <?php if (isset($erro)): ?>
      <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Nome:</label>
      <input type="text" name="nome" value="<?= $usuario['nome'] ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?= $usuario['email'] ?>" required>

      <label>Perfil:</label>
      <select name="perfil" required>
        <option value="administrador" <?= $usuario['perfil'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
        <option value="bibliotecario" <?= $usuario['perfil'] === 'bibliotecario' ? 'selected' : '' ?>>Bibliotecário</option>
        <option value="usuario" <?= $usuario['perfil'] === 'usuario' ? 'selected' : '' ?>>Usuário</option>
      </select>

      <button type="submit">Salvar Alterações</button>
    </form>

    <div class="voltar">
      <p><a href="usuarios.php">⬅ Voltar para a lista</a></p>
    </div>
  </div>
</body>
</html>