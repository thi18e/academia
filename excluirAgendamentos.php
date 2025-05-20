<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'profissional') {
    header('Location: ../site/login.php');
    exit();
}

$id_profissional = $_SESSION['usuario']['id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Agendamento inválido.";
    exit();
}

$id_agendamento = (int) $_GET['id'];

// Verifica se o agendamento pertence ao profissional logado
$stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE id = ? AND profissional_id = ?");
$stmt->execute([$id_agendamento, $id_profissional]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    echo "Agendamento não encontrado ou sem permissão.";
    exit();
}

// Exclui o agendamento
$stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ? AND profissional_id = ?");
if ($stmt->execute([$id_agendamento, $id_profissional])) {
    header('Location: agendamentos.php?excluido=1');
    exit();
} else {
    echo "Erro ao excluir o agendamento.";
}
?>
