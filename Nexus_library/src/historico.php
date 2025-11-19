<?php
session_start();
include 'db.php';

include 'db.php';

// Atualiza status de empréstimos vencidos
$hoje = date('Y-m-d');
$conn->query("
  UPDATE Emprestimo
  SET status = 'atrasado'
  WHERE status = 'ativo'
    AND dataDevolucao < '$hoje'
    AND dataDevolvido IS NULL
");

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'bibliotecario') {
  header("Location: login.php");
  exit;
}

$usuario = $_GET['usuario'] ?? '';
$inicio = $_GET['inicio'] ?? '';
$fim = $_GET['fim'] ?? '';

$where = "E.status IN ('devolvido', 'atrasado')";
$params = [];
$types = '';

$where = "1=1"; // inclui todos os status
$params = [];
$types = '';

if ($usuario !== '') {
  $where .= " AND U.nome LIKE ?";
  $params[] = "%$usuario%";
  $types .= 's';
}
if ($inicio !== '') {
  $where .= " AND E.dataEmprestimo >= ?";
  $params[] = $inicio;
  $types .= 's';
}
if ($fim !== '') {
  $where .= " AND E.dataEmprestimo <= ?";
  $params[] = $fim;
  $types .= 's';
}

$sql = "
  SELECT E.idEmprestimo, U.nome AS usuario, L.Titulo AS livro,
         E.dataEmprestimo, E.dataDevolucao, E.dataDevolvido, E.status
  FROM Emprestimo E
  JOIN Usuario U ON E.idUsuario = U.idUsuario
  JOIN Livro L ON E.idLivro = L.idLivro
  WHERE $where
  ORDER BY E.dataDevolvido DESC
";

$stmt = $conn->prepare($sql);
if ($stmt) {
  if ($params) {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $historico = $stmt->get_result();
} else {
  die("Erro na preparação da consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Histórico de Empréstimos</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: #eef2f7;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1100px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
      font-weight: 600;
    }
    form {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-bottom: 30px;
      justify-content: center;
    }
    input[type="text"], input[type="date"], button {
      padding: 10px 14px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }
    button {
      background-color: #3498db;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: 600;
    }
    button:hover {
      background-color: #2980b9;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #e0e0e0;
      text-align: left;
    }
    th {
      background-color: #3498db;
      color: white;
      font-weight: 600;
    }
    tr.atrasado {
      background-color: #ffe6e6;
      color: #c0392b;
      font-weight: bold;
    }
    .voltar {
      text-align: center;
      margin-top: 30px;
    }
    .voltar a {
      text-decoration: none;
      color: #34495e;
      font-weight: 600;
      padding: 10px 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: #ecf0f1;
      transition: background 0.3s;
    }
    .voltar a:hover {
      background-color: #d0d7de;
    }
    @media (max-width: 768px) {
      form { flex-direction: column; align-items: center; }
      table { font-size: 13px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Histórico de Empréstimos</h2>

    <form method="GET">
      <input type="text" name="usuario" placeholder="Nome do usuário" value="<?= $_GET['usuario'] ?? '' ?>">
      <input type="date" name="inicio" value="<?= $_GET['inicio'] ?? '' ?>">
      <input type="date" name="fim" value="<?= $_GET['fim'] ?? '' ?>">
      <button type="submit">Filtrar</button>
      <a href="historico.php"><button type="button">Limpar</button></a>
    </form>

    <table>
      <tr>
        <th>Usuário</th>
        <th>Livro</th>
        <th>Data Empréstimo</th>
        <th>Prev. Devolução</th>
        <th>Data Devolvido</th>
        <th>Status</th>
      </tr>
      <?php while ($h = $historico->fetch_assoc()): ?>
        <tr class="<?= $h['status'] === 'atrasado' ? 'atrasado' : ($h['status'] === 'ativo' ? 'ativo' : '') ?>">
          <td><?= htmlspecialchars($h['usuario']) ?></td>
          <td><?= htmlspecialchars($h['livro']) ?></td>
          <td><?= $h['dataEmprestimo'] ?></td>
          <td><?= $h['dataDevolucao'] ?></td>
          <td><?= $h['dataDevolvido'] ?? '—' ?></td>
          <td><?= ucfirst($h['status']) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <div class="voltar">
      <a href="painel_bibliotecario.php">⬅ Voltar ao Painel</a>
    </div>
  </div>
</body>
</html>