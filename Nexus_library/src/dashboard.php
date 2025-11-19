<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

switch ($_SESSION['usuario']['perfil']) {
  case 'administrador':
    header("Location: painel_administrador.php");
    break;
  case 'bibliotecario':
    header("Location: painel_bibliotecario.php");
    break;
  case 'usuario':
    header("Location: painel_usuario.php");
    break;
  default:
    echo "Perfil inválido.";
}
?>