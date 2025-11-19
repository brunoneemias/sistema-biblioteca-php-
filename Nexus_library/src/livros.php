<?php
session_start();
include 'db.php';



if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

$busca = $_GET['busca'] ?? '';
$sql = "SELECT * FROM Livro WHERE Titulo LIKE ?";
$stmt = $conn->prepare($sql);
$param = "%$busca%";
$stmt->bind_param("s", $param);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lista de Livros</title>
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
      margin-bottom: 20px;
    }
    form {
      text-align: center;
      margin-bottom: 20px;
    }
    input[type="text"] {
      padding: 10px;
      width: 60%;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      padding: 10px 15px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-left: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
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
      margin-top: 30px;
      text-align: center;
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
    <h2><i class="fas fa-book"></i> Lista de Livros</h2>

    <form method="GET">
      <input type="text" name="busca" placeholder="Buscar por título..." value="<?= htmlspecialchars($busca) ?>">
      <button type="submit"><i class="fas fa-search"></i> Buscar</button>
    </form>

    <table>
      <tr>
        <th>Título</th>
        <th>Autor</th>
        <th>Editora</th>
        <th>Ano</th>
        <th>Qtd</th>
        <th>Disp.</th>
        <th>Ações</th>
        <th>Empréstimo/Reserva</th>
      </tr>
      <?php while ($livro = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($livro['Titulo']) ?></td>
          <td><?= htmlspecialchars($livro['Autor']) ?></td>
          <td><?= htmlspecialchars($livro['editora']) ?></td>
          <td><?= $livro['ano'] ?></td>
          <td><?= $livro['quantidade'] ?></td>
          <td><?= $livro['disponivel'] ?></td>
          <td class="acoes">
            <a href="editar_livro.php?id=<?= $livro['idLivro'] ?>"><i class="fas fa-edit"></i></a>
            <a href="excluir_livro.php?id=<?= $livro['idLivro'] ?>" onclick="return confirm('Tem certeza que deseja excluir este livro?')"><i class="fas fa-trash-alt"></i></a>
          </td>
          <td>
      <?php if ($livro['disponivel'] > 0): ?>
        <form method="POST" action="emprestimos.php">
          <input type="hidden" name="idLivro" value="<?= $livro['idLivro'] ?>">
          <button type="submit">Emprestar</button>
        </form>
      <?php else: ?>
        <form method="POST" action="reservar.php">
          <input type="hidden" name="idLivro" value="<?= $livro['idLivro'] ?>">
          <button type="submit">Reservar</button>
        </form>
      <?php endif; ?>
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