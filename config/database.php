<?php
$host = 'localhost';
$dbname = 'gymdb'; // Substitua pelo nome do seu banco
$username = 'root'; // O padrão do XAMPP é "root"
$password = ''; // O padrão do XAMPP é senha vazia

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>