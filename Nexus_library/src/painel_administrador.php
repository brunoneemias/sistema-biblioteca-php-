<?php
session_start();
include 'db.php';
include 'funcoes.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'administrador') {
  header("Location: login.php");
  exit;
}

// Total de eventos
$totalEventos = $conn->query("SELECT COUNT(*) AS total FROM logatividade")->fetch_assoc()['total'];

// Eventos nas últimas 24h
$ultimas24h = $conn->query("
  SELECT COUNT(*) AS total 
  FROM logatividade 
  WHERE dataHora >= NOW() - INTERVAL 1 DAY
")->fetch_assoc()['total'];

// Eventos por tipo
$eventosPorTipo = $conn->query("
  SELECT tipoEvento, COUNT(*) AS total 
  FROM logatividade 
  GROUP BY tipoEvento
");

// Eventos por dia (últimos 7 dias)
$eventosPorDia = $conn->query("
  SELECT DATE(dataHora) AS dia, COUNT(*) AS total
  FROM logatividade
  WHERE dataHora >= NOW() - INTERVAL 7 DAY
  GROUP BY DATE(dataHora)
  ORDER BY dia ASC
");

// Top 5 usuários mais ativos
$topUsuarios = $conn->query("
  SELECT U.nome, COUNT(*) AS total
  FROM logatividade L
  JOIN usuario U ON L.idUsuario = U.idUsuario
  GROUP BY U.nome
  ORDER BY total DESC
  LIMIT 5
");

// Tentativas de login falhas
$loginFalhos = $conn->query("
  SELECT COUNT(*) AS total
  FROM logatividade
  WHERE tipoEvento = 'Login' AND descricao LIKE '%falha%'
")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel do Administrador</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Roboto', sans-serif; background: #f4f6f9; margin: 0; }
    header { background-color: #007BFF; color: white; padding: 20px; text-align: center; }
    .container { max-width: 1100px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h2 { margin: 30px 0 20px; color: #333; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
    .card { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; transition: 0.3s; box-shadow: 0 0 5px rgba(0,0,0,0.05); }
    .card:hover { background: #e9ecef; }
    .card i { font-size: 2em; color: #007BFF; margin-bottom: 10px; }
    .card a { text-decoration: none; color: #007BFF; font-weight: bold; }
    .log-cards { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
    .log-card { background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1; min-width: 220px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .charts { display: grid; grid-template-columns: 1fr; gap: 25px; }
    @media (min-width: 992px) { .charts { grid-template-columns: repeat(3, 1fr); } }
    .table thead tr { background: #343a40; color: #fff; }
  </style>
</head>
<body>
  <header>
    <h1><i class="fas fa-user-shield"></i> Painel do Administrador</h1>
    <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?>!</p>
  </header>

  <div class="container">
    <h2>Funções disponíveis</h2>
    <div class="grid">
      <div class="card"><i class="fas fa-book-medical"></i><p><a href="cadastrar_livro.php">Cadastrar Livro</a></p></div>
      <div class="card"><i class="fas fa-book"></i><p><a href="livros.php">Gerenciar Livros</a></p></div>
      <div class="card"><i class="fas fa-users-cog"></i><p><a href="usuarios.php">Gerenciar Usuários</a></p></div>
      <div class="card"><i class="fas fa-chart-line"></i><p><a href="relatorios.php">Relatórios</a></p></div>
      <div class="card"><i class="fas fa-clipboard-list"></i><p><a href="log_atividades.php">Logs de Atividades</a></p></div>
    </div>

    <h2>Análise de Logs</h2>
    <div class="log-cards">
      <div class="log-card"><h3>Total de Eventos</h3><p class="fs-3"><?= $totalEventos ?></p></div>
      <div class="log-card"><h3>Últimas 24h</h3><p class="fs-3"><?= $ultimas24h ?></p></div>
      <div class="log-card">
        <h3>Login Falhos</h3>
        <p class="fs-3" style="color:<?= $loginFalhos>0?'#dc3545':'#198754' ?>"><?= $loginFalhos ?></p>
        <small><?= $loginFalhos>0?'Atenção: investigar tentativas.':'Sem falhas registradas.' ?></small>
      </div>
    </div>

    <div class="charts">
      <div>
        <h5 class="text-muted">Eventos por Tipo</h5>
        <canvas id="graficoEventos" height="200"></canvas>
      </div>
      <div>
        <h5 class="text-muted">Evolução (últimos 7 dias)</h5>
        <canvas id="graficoLinha" height="200"></canvas>
      </div>
      <div>
        <h5 class="text-muted">Proporção por Tipo</h5>
        <canvas id="graficoPizza" height="200"></canvas>
      </div>
    </div>

    <h2>Top 5 Usuários Mais Ativos</h2>
    <table class="table table-striped table-hover">
      <thead><tr><th>Usuário</th><th>Total de Eventos</th></tr></thead>
      <tbody>
        <?php while($row=$topUsuarios->fetch_assoc()){ ?>
          <tr><td><?= htmlspecialchars($row['nome']) ?></td><td><?= $row['total'] ?></td></tr>
        <?php } ?>
      </tbody>
    </table>

    <div class="logout mt-4 text-center">
  <a href="logout.php" class="btn btn-danger btn-lg">
    <i class="fas fa-sign-out-alt"></i> Sair do sistema
  </a>
</div>


<script>
  // Helpers para cores
  const palette = [
    'rgba(54, 162, 235, 0.6)',
    'rgba(255, 99, 132, 0.6)',
    'rgba(255, 206, 86, 0.6)',
    'rgba(75, 192, 192, 0.6)',
    'rgba(153, 102, 255, 0.6)',
    'rgba(255, 159, 64, 0.6)'
  ];
  const paletteBorder = palette.map(c => c.replace('0.6', '1'));

  // PHP -> JS: dados por tipo
  const labelsTipo = [<?php $eventosPorTipo->data_seek(0); while($row=$eventosPorTipo->fetch_assoc()){echo "'".addslashes($row['tipoEvento'])."',";} ?>];
  const dadosTipo = [<?php $eventosPorTipo->data_seek(0); while($row=$eventosPorTipo->fetch_assoc()){echo (int)$row['total'].",";} ?>];

  // Gráfico de barras por tipo
  new Chart(document.getElementById('graficoEventos').getContext('2d'), {
    type: 'bar',
    data: {
      labels: labelsTipo,
      datasets: [{
        label: 'Eventos por Tipo',
        data: dadosTipo,
        backgroundColor: labelsTipo.map((_, i) => palette[i % palette.length]),
        borderColor: labelsTipo.map((_, i) => paletteBorder[i % paletteBorder.length]),
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });

  // PHP -> JS: dados por dia
  const labelsDia = [<?php while($row=$eventosPorDia->fetch_assoc()){echo "'".$row['dia']."',";} ?>];
  <?php $eventosPorDia->data_seek(0); ?>
  const dadosDia = [<?php while($row=$eventosPorDia->fetch_assoc()){echo (int)$row['total'].",";} ?>];

  // Gráfico de linha últimos 7 dias
  new Chart(document.getElementById('graficoLinha').getContext('2d'), {
    type: 'line',
    data: {
      labels: labelsDia,
      datasets: [{
        label: 'Eventos por Dia',
        data: dadosDia,
        borderColor: 'rgba(255,99,132,1)',
        backgroundColor: 'rgba(255,99,132,0.2)',
        tension: 0.2,
        fill: true,
        pointRadius: 3
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });

  // Gráfico de pizza proporção por tipo
  new Chart(document.getElementById('graficoPizza').getContext('2d'), {
    type: 'pie',
    data: {
      labels: labelsTipo,
      datasets: [{
        data: dadosTipo,
        backgroundColor: labelsTipo.map((_, i) => palette[i % palette.length]),
        borderColor: labelsTipo.map((_, i) => paletteBorder[i % paletteBorder.length]),
        borderWidth: 1
      }]
    },
    options: { responsive: true }
  });
</script>

</body>
</html>