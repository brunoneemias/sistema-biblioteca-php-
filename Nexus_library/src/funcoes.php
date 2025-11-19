<?php
define('AES_KEY', '12345678901234567890123456789012'); // precisa ter 32 bytes exatos

function criptografarAES($texto) {
    $iv = openssl_random_pseudo_bytes(16);
    $cifra = openssl_encrypt($texto, 'AES-256-CBC', AES_KEY, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cifra);
}

function descriptografarAES($textoCriptografado) {
    $dados = base64_decode($textoCriptografado);
    $iv = substr($dados, 0, 16);
    $cifra = substr($dados, 16);
    return openssl_decrypt($cifra, 'AES-256-CBC', AES_KEY, OPENSSL_RAW_DATA, $iv);
}

function registrar_log_atividades($conn, $tipoEvento, $descricao, $idUsuario = null) {
  $enderecoIP = $_SERVER['REMOTE_ADDR'];
  $descricaoCriptografada = criptografarAES($descricao);

  if ($idUsuario === null) {
    $stmt = $conn->prepare("
      INSERT INTO logatividade (tipoEvento, descricao, enderecoIP, dataHora)
      VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param("sss", $tipoEvento, $descricaoCriptografada, $enderecoIP);
  } else {
    $stmt = $conn->prepare("
      INSERT INTO logatividade (tipoEvento, descricao, enderecoIP, idUsuario, dataHora)
      VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("sssi", $tipoEvento, $descricaoCriptografada, $enderecoIP, $idUsuario);
  }

  $stmt->execute();
  $stmt->close();
}

?>