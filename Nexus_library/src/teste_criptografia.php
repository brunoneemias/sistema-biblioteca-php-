<?php
define('AES_KEY', 'sua-chave-secreta-32bytes');

function criptografarAES($texto) {
  $iv = openssl_random_pseudo_bytes(16);
  $cifra = openssl_encrypt($texto, 'AES-256-CBC', AES_KEY, 0, $iv);
  return base64_encode($iv . $cifra);
}

function descriptografarAES($textoCriptografado) {
  $dados = base64_decode($textoCriptografado);
  $iv = substr($dados, 0, 16);
  $cifra = substr($dados, 16);
  return openssl_decrypt($cifra, 'AES-256-CBC', AES_KEY, 0, $iv);
}

$senhaOriginal = 'minhaSenha123';
$criptografada = criptografarAES($senhaOriginal);
$revelada = descriptografarAES($criptografada);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Teste de Criptografia AES</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 800px;
      margin: 60px auto;
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h1 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
    }
    .resultado {
      background-color: #ecf0f1;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .resultado strong {
      color: #2980b9;
    }
    .footer {
      text-align: center;
      margin-top: 30px;
      font-size: 14px;
      color: #7f8c8d;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🔐 Teste de Criptografia AES</h1>

    <div class="resultado">
      <p><strong>Texto Original:</strong> <?= $senhaOriginal ?></p>
    </div>

    <div class="resultado">
      <p><strong>Criptografado (Base64):</strong><br><?= $criptografada ?></p>
    </div>

    <div class="resultado">
      <p><strong>Descriptografado:</strong> <?= $revelada ?></p>
    </div>

    <div class="footer">
      Este teste demonstra a criptografia simétrica AES-256-CBC com IV dinâmico.
    </div>
  </div>
</body>
</html>