<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['perfil'] !== 'bibliotecario') {
  header("Location: login.php");
  exit;
}

// Listar usuários e livros disponíveis
$usuarios = $conn->query("SELECT idUsuario, nome FROM Usuario");
$livros = $conn->query("SELECT idLivro, Titulo FROM Livro WHERE disponivel > 0");

// Listar empréstimos ativos
$emprestimos = $conn->query("
  SELECT E.idEmprestimo, U.nome AS usuario, L.Titulo AS livro, E.dataEmprestimo, E.dataDevolucao
  FROM Emprestimo E
  JOIN Usuario U ON E.idUsuario = U.idUsuario
  JOIN Livro L ON E.idLivro = L.idLivro
  WHERE E.status = 'ativo'
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Empréstimos</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Roboto', sans-serif; background: #f4f6f9; margin: 0; }
    .container {
      max-width: 1000px; margin: 40px auto; background: white; padding: 30px;
      border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; color: #007BFF; margin-bottom: 30px; }
    form {
      display: grid; grid-template-columns: 1fr 1fr 1fr auto;
      gap: 15px; margin-bottom: 30px;
    }
    select, input[type="date"] {
      padding: 10px; border: 1px solid #ccc; border-radius: 6px;
    }
    button {
      padding: 10px 20px; background-color: #007BFF; color: white;
      border: none; border-radius: 6px; font-weight: bold; cursor: pointer;
    }
    table {
      width: 100%; border-collapse: collapse;
    }
    th, td {
      padding: 12px; border-bottom: 1px solid #ddd; text-align: left;
    }
    th { background-color: #007BFF; color: white; }
    .voltar {
      text-align: center; margin-top: 30px;
    }
    .voltar a {
      color: #6c757d; text-decoration: none; font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Registrar Empréstimo</h2>

    <form method="POST" action="emprestar.php">
      <select name="usuario" required>
        <option value="">Selecione o usuário</option>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
          <option value="<?= $u['idUsuario'] ?>"><?= $u['nome'] ?></option>
        <?php endwhile; ?>
      </select>

      <select name="livro" required>
        <option value="">Selecione o livro</option>
        <?php while ($l = $livros->fetch_assoc()): ?>
          <option value="<?= $l['idLivro'] ?>"><?= $l['Titulo'] ?></option>
        <?php endwhile; ?>
      </select>

      <input type="date" name="data_devolucao" required>

      <button type="submit">Emprestar</button>
    </form>

    <h3>Empréstimos Ativos</h3>
    <table>
      <tr>
        <th>Usuário</th>
        <th>Livro</th>
        <th>Data Empréstimo</th>
        <th>Data Devolução</th>
        <th>Ação</th>
      </tr>
      <?php while ($e = $emprestimos->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($e['usuario']) ?></td>
          <td><?= htmlspecialchars($e['livro']) ?></td>
          <td><?= $e['dataEmprestimo'] ?></td>
          <td><?= $e['dataDevolucao'] ?></td>
            <td>
            <form method="POST" action="devolver.php" style="display:inline;">
                <input type="hidden" name="idEmprestimo" value="<?= $e['idEmprestimo'] ?>">
                <button type="submit">Devolver</button>
            </form>
            </td>

        </tr>
      <?php endwhile; ?>
    </table>

    <div class="voltar">
      <p><a href="painel_bibliotecario.php">⬅ Voltar ao Painel</a></p>
    </div>
  </div>
</body>
</html>