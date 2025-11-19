<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'administrador') {
  header("Location: login.php");
  exit;
}

// Contadores
$totalLivros = $conn->query("SELECT COUNT(*) AS total FROM Livro")->fetch_assoc()['total'];
$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM Usuario")->fetch_assoc()['total'];
$totalEmprestados = $conn->query("SELECT COUNT(*) AS total FROM Livro WHERE emprestado = 1")->fetch_assoc()['total'];
$totalDisponiveis = $conn->query("SELECT SUM(disponivel) AS total FROM Livro")->fetch_assoc()['total'];

// Livros mais emprestados (exemplo fictício)
$dados = $conn->query("SELECT Titulo, quantidade FROM Livro ORDER BY quantidade DESC LIMIT 5");
$livros = [];
$quantidades = [];
while ($row = $dados->fetch_assoc()) {
  $livros[] = $row['Titulo'];
  $quantidades[] = $row['quantidade'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatórios</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
      margin-bottom: 30px;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }
    .card {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 0 5px rgba(0,0,0,0.05);
    }
    .card h3 {
      margin: 0;
      font-size: 1.2em;
      color: #333;
    }
    .card p {
      font-size: 2em;
      color: #007BFF;
      margin: 10px 0 0;
    }
    canvas {
      max-width: 100%;
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
    <h2><i class="fas fa-chart-bar"></i> Relatórios da Biblioteca</h2>

    <div class="cards">
      <div class="card">
        <h3>Total de Livros</h3>
        <p><?= $totalLivros ?></p>
      </div>
      <div class="card">
        <h3>Disponíveis</h3>
        <p><?= $totalDisponiveis ?></p>
      </div>
      <div class="card">
        <h3>Emprestados</h3>
        <p><?= $totalEmprestados ?></p>
      </div>
      <div class="card">
        <h3>Usuários</h3>
        <p><?= $totalUsuarios ?></p>
      </div>
    </div>

    <h3 style="text-align:center; margin-bottom:20px;">Livros com mais exemplares</h3>
    <canvas id="graficoLivros"></canvas>

    <div class="voltar">
      <p><a href="dashboard.php">⬅ Voltar para o Dashboard</a></p>
    </div>
  </div>

  <script>
    const ctx = document.getElementById('graficoLivros').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($livros) ?>,
        datasets: [{
          label: 'Quantidade',
          data: <?= json_encode($quantidades) ?>,
          backgroundColor: '#007BFF'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>
</body>
</html>