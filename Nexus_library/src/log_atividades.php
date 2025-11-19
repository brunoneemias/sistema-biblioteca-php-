<?php
include 'db.php';
include 'funcoes.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Log de Atividades</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h2 class="mb-4 text-primary">📜 Log de Atividades</h2>

  <!-- Filtros -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
      <label class="form-label">Tipo de Evento:</label>
      <input type="text" class="form-control" name="tipo" value="<?= htmlspecialchars($_GET['tipo'] ?? '') ?>" placeholder="Login, Cadastro, etc.">
    </div>
    <div class="col-md-3">
      <label class="form-label">Usuário:</label>
      <input type="text" class="form-control" name="usuario" value="<?= htmlspecialchars($_GET['usuario'] ?? '') ?>" placeholder="Nome do usuário">
    </div>
    <div class="col-md-3">
      <label class="form-label">Data Inicial:</label>
      <input type="date" class="form-control" name="data_inicio" value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Data Final:</label>
      <input type="date" class="form-control" name="data_fim" value="<?= htmlspecialchars($_GET['data_fim'] ?? '') ?>">
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-primary">Filtrar</button>
      <a href="log_atividades.php" class="btn btn-secondary">Limpar</a>
    </div>
    <div style="margin-top:20px; text-align:center;">
        <button onclick="window.history.back()" class="btn btn-secondary">
            ⬅ Voltar
        </button>
    </div>
  </form>

  <!-- Tabela -->
  <div class="card shadow-sm">
    <div class="card-body">
      <table id="tabelaLogs" class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Data/Hora</th>
            <th>Tipo de Evento</th>
            <th>Descrição</th>
            <th>IP</th>
            <th>Usuário</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // (sua lógica de filtros e consulta permanece igual)
          $filtros = [];
          $params = [];
          $tipos = "";

          if (!empty($_GET['tipo'])) {
            $filtros[] = "L.tipoEvento LIKE ?";
            $params[] = "%" . $_GET['tipo'] . "%";
            $tipos .= "s";
          }
          if (!empty($_GET['usuario'])) {
            $filtros[] = "U.nome LIKE ?";
            $params[] = "%" . $_GET['usuario'] . "%";
            $tipos .= "s";
          }
          if (!empty($_GET['data_inicio'])) {
            $filtros[] = "L.dataHora >= ?";
            $params[] = $_GET['data_inicio'] . " 00:00:00";
            $tipos .= "s";
          }
          if (!empty($_GET['data_fim'])) {
            $filtros[] = "L.dataHora <= ?";
            $params[] = $_GET['data_fim'] . " 23:59:59";
            $tipos .= "s";
          }

          $sql = "
            SELECT L.idLog, L.dataHora, L.tipoEvento, L.descricao, L.enderecoIP, U.nome
            FROM logatividade L
            LEFT JOIN usuario U ON L.idUsuario = U.idUsuario
          ";

          if ($filtros) {
            $sql .= " WHERE " . implode(" AND ", $filtros);
          }

          $sql .= " ORDER BY L.dataHora DESC";

          $stmt = $conn->prepare($sql);
          if ($params) {
            $stmt->bind_param($tipos, ...$params);
          }
          $stmt->execute();
          $resultado = $stmt->get_result();

          while ($linha = $resultado->fetch_assoc()) {
            $dataHora = date('d/m/Y H:i:s', strtotime($linha['dataHora']));
            $tipoEvento = htmlspecialchars($linha['tipoEvento']);
            $descricao = htmlspecialchars(descriptografarAES($linha['descricao']));
            $enderecoIP = htmlspecialchars($linha['enderecoIP']);
            $nomeUsuario = htmlspecialchars($linha['nome'] ?? '---');

            echo "<tr>";
            echo "<td>$dataHora</td>";
            echo "<td>$tipoEvento</td>";
            echo "<td>$descricao</td>";
            echo "<td>$enderecoIP</td>";
            echo "<td>$nomeUsuario</td>";
            echo "</tr>";
          }
          $stmt->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Bootstrap JS + DataTables JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  new DataTable('#tabelaLogs');
</script>

</body>
</html>