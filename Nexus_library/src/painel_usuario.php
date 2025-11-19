<?php
session_start();
include 'db.php';


if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'usuario') {
  header("Location: login.php");
  exit;
}



$idUsuario = $_SESSION['usuario']['idUsuario'];
$hoje = date('Y-m-d');

// Atualiza status para atrasado
$conn->query("
  UPDATE Emprestimo
  SET status = 'atrasado'
  WHERE status = 'ativo'
    AND dataDevolucao < '$hoje'
    AND dataDevolvido IS NULL
");

// Buscar empréstimos do usuário
$stmt = $conn->prepare("
  SELECT E.idEmprestimo, L.Titulo AS livro, E.dataEmprestimo, E.dataDevolucao, E.dataDevolvido, E.status
  FROM Emprestimo E
  JOIN Livro L ON E.idLivro = L.idLivro
  WHERE E.idUsuario = ?
  ORDER BY E.dataEmprestimo DESC
");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$emprestimos = $stmt->get_result();

// Buscar reservas ativas do usuário
$reservasStmt = $conn->prepare("
  SELECT R.dataReserva, R.dataExpiracao, R.status, L.Titulo
  FROM Reserva R
  JOIN Livro L ON R.idLivro = L.idLivro
  WHERE R.idUsuario = ? AND R.status = 'ativa'
  ORDER BY R.dataReserva DESC
");
$reservasStmt->bind_param("i", $idUsuario);
$reservasStmt->execute();
$reservas = $reservasStmt->get_result();


// Buscar livros com disponibilidade
$livros = $conn->query("
  SELECT idLivro, Titulo, disponivel
  FROM Livro
  ORDER BY Titulo
");

?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Meu Painel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1000px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #e0e0e0;
      text-align: left;
    }
    th {
      background-color: #3498db;
      color: white;
    }
    tr.atrasado {
      background-color: #ffe6e6;
      color: #c0392b;
      font-weight: bold;
    }
    tr.ativo {
      background-color: #f0f8ff;
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
  </style>
</head>
<body>
  <div class="container">
  <h2>Meus Empréstimos</h2>
  <table>
    <tr>
      <th>Livro</th>
      <th>Data Empréstimo</th>
      <th>Prev. Devolução</th>
      <th>Data Devolvido</th>
      <th>Status</th>
    </tr>
    <?php while ($e = $emprestimos->fetch_assoc()): ?>
      <?php
        $classe = '';
        if ($e['status'] === 'atrasado') $classe = 'atrasado';
        elseif ($e['status'] === 'ativo') $classe = 'ativo';
      ?>
      <tr class="<?= $classe ?>">
        <td><?= htmlspecialchars($e['livro']) ?></td>
        <td><?= $e['dataEmprestimo'] ?></td>
        <td><?= $e['dataDevolucao'] ?></td>
        <td><?= $e['dataDevolvido'] ?? '—' ?></td>
        <td><?= ucfirst($e['status']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <h2 style="margin-top:50px;">Minhas Reservas</h2>
  <table>
    <tr>
      <th>Livro</th>
      <th>Data da Reserva</th>
      <th>Expira em</th>
      <th>Status</th>
    </tr>
    <?php while ($r = $reservas->fetch_assoc()): ?>
      <tr class="<?= ($r['dataExpiracao'] < date('Y-m-d')) ? 'atrasado' : '' ?>">
        <td><?= htmlspecialchars($r['Titulo']) ?></td>
        <td><?= $r['dataReserva'] ?></td>
        <td><?= $r['dataExpiracao'] ?></td>
        <td><?= ucfirst($r['status']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <h2 style="margin-top:50px;">Livros Disponíveis</h2>
  <table>
    <tr>
      <th>Título</th>
      <th>Disponível</th>
      <th>Ação</th>
    </tr>
    <?php while ($livro = $livros->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($livro['Titulo']) ?></td>
        <td><?= $livro['disponivel'] ?></td>
        <td>
          <form method="POST" action="<?= $livro['disponivel'] > 0 ? 'emprestar.php' : 'reservar.php' ?>" style="display:inline;">
            <input type="hidden" name="idLivro" value="<?= $livro['idLivro'] ?>">
            <button type="submit" style="
              padding: 8px 16px;
              background-color: <?= $livro['disponivel'] > 0 ? '#2ecc71' : '#f39c12' ?>;
              color: white;
              border: none;
              border-radius: 6px;
              font-weight: bold;
              cursor: pointer;
            ">
              <?= $livro['disponivel'] > 0 ? 'Emprestar' : 'Reservar' ?>
            </button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <div class="voltar">
    <a href="logout.php">Sair</a>
  </div>
</div>

</body>
</html>