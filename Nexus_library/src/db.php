<?php
$host = "localhost";       // Endereço do servidor MySQL (localhost no XAMPP)
$user = "root";            // Usuário padrão do MySQL no XAMPP
$pass = "";                // Senha padrão (vazia, a menos que você tenha definido uma)
$db   = "nexus_library";   // Nome do banco de dados que você criou

$conn = new mysqli($host, $user, $pass, $db); // Cria a conexão com o banco

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error); // Exibe erro se a conexão falhar
}

// Define charset para evitar problemas com acentuação
$conn->set_charset("utf8mb4");


?>
