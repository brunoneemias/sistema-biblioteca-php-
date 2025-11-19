<?php
include 'db.php';
$id = $_GET['id'] ?? null;

if (!$id) {
  echo "ID inválido.";
  exit;
}

$stmt = $conn->prepare("SELECT nome FROM Usuario WHERE idUsuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $stmt = $conn->prepare("DELETE FROM Usuario WHERE idUsuario = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: usuarios.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Excluir Usuário</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Roboto', sans-serif; background: #f4f6f9; margin: 0; }
    .container {
      max-width: 500px; margin: 80px auto; background: white; padding: 30px;
      border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center;
    }
    h2 { color: #dc3545; margin-bottom: 20px; }
    p { font-size: 1.1em; }
    form { margin-top: 30px; }
    button {
      padding: 10px 20px; margin: 10px; border: none;
      border-radius: 6px; font-weight: bold; cursor: pointer;
    }
    .confirmar { background-color: #dc3545; color: white; }
    .cancelar { background-color: #6c757d; color: white; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Confirmar Exclusão</h2>
    <p>Tem certeza que deseja excluir o usuário <strong><?= htmlspecialchars($usuario['nome']) ?></strong>?</p>

    <form method="POST">
      <button type="submit" class="confirmar">Sim, excluir</button>
      <a href="usuarios.php"><button type="button" class="cancelar">Cancelar</button></a>
    </form>
  </div>
</body>
</html>