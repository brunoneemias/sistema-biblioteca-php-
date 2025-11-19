<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'administrador') {
  header("Location: login.php");
  exit;
}

$usuarios = $conn->query("SELECT * FROM Usuario");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Gerenciar Usuários</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: #f4f6f9;
      margin: 0;
    }
    .container {
      max-width: 1000px;
      margin: 40px auto;
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
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #007BFF;
      color: white;
    }
    .acoes a {
      margin-right: 10px;
      color: #007BFF;
      text-decoration: none;
    }
    .acoes a:hover {
      text-decoration: underline;
    }
    .voltar {
      text-align: center;
      margin-top: 30px;
    }
    .voltar a {
      color: #6c757d;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2><i class="fas fa-users-cog"></i> Gerenciar Usuários</h2>
    <div style="text-align: right; margin-bottom: 20px;">
     <a href="cadastrar_usuario.php" style="
        display: inline-block;
        padding: 10px 20px;
        background-color: #007BFF;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: 0.3s;
     ">
    <i class="fas fa-user-plus"></i> Adicionar Usuário
        </a>
    </div>
    <table>
      <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Perfil</th>
        <th>Ações</th>
      </tr>
      <?php while ($usuario = $usuarios->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($usuario['nome']) ?></td>
          <td><?= htmlspecialchars($usuario['email']) ?></td>
          <td><?= htmlspecialchars($usuario['perfil']) ?></td>
          <td class="acoes">
            <a href="editar_usuario.php?id=<?= $usuario['idUsuario'] ?>"><i class="fas fa-edit"></i></a>
            <a href="excluir_usuario.php?id=<?= $usuario['idUsuario'] ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?')"><i class="fas fa-trash-alt"></i></a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>

    <div class="voltar">
      <p><a href="dashboard.php">⬅ Voltar para o Dashboard</a></p>
    </div>
  </div>
</body>
</html>