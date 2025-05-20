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

$stmt = $pdo->prepare("
    SELECT * FROM agendamentos 
    WHERE id = ? AND profissional_id = ?
");
$stmt->execute([$id_agendamento, $id_profissional]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    echo "Agendamento não encontrado ou sem permissão.";
    exit();
}

// Buscar serviços e clientes
$servicos = $pdo->query("SELECT * FROM servicos")->fetchAll(PDO::FETCH_ASSOC);
$clientes = $pdo->query("SELECT * FROM usuarios WHERE tipo = 'cliente'")->fetchAll(PDO::FETCH_ASSOC);

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $data = $_POST['data']; // yyyy-mm-dd
    $hora_inicio = $_POST['hora_inicio']; // HH:MM
    $hora_fim = $_POST['hora_fim']; // HH:MM
    $servico_id = $_POST['servico_id'];
    $cliente_id = $_POST['cliente_id'];

    // Montar data completa
    $data_hora_inicio = "$data $hora_inicio:00";
    $data_hora_fim = "$data $hora_fim:00";

    $stmt = $pdo->prepare("
        UPDATE agendamentos 
        SET status = ?, data_hora_inicio = ?, data_hora_fim = ?, servico_id = ?, cliente_id = ?
        WHERE id = ? AND profissional_id = ?
    ");

    if ($stmt->execute([$status, $data_hora_inicio, $data_hora_fim, $servico_id, $cliente_id, $id_agendamento, $id_profissional])) {
        $mensagem = "<div class='alert alert-success mt-3'>Agendamento atualizado com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger mt-3'>Erro ao atualizar.</div>";
    }
}

// Separar a data e horários para os campos input
$data_formatada = date('Y-m-d', strtotime($agendamento['data_hora_inicio']));
$hora_inicio_formatada = date('H:i', strtotime($agendamento['data_hora_inicio']));
$hora_fim_formatada = date('H:i', strtotime($agendamento['data_hora_fim']));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Agendamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<?php include '../includes/navbarprof.php'; ?>

<div class="container py-5">
    <h2>Editar Agendamento</h2>

    <?= $mensagem ?>

    <form method="POST">
    <div class="mb-3">
        <label for="cliente_id" class="form-label">Cliente</label>
        <select name="cliente_id" id="cliente_id" class="form-select w-50" required>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id'] ?>" <?= $cliente['id'] == $agendamento['cliente_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cliente['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="hora_inicio" class="form-label">Hora Início</label>
        <input type="time" name="hora_inicio" id="hora_inicio" class="form-control w-50" value="<?= $hora_inicio_formatada ?>" required>
    </div>

    <div class="mb-3">
        <label for="hora_fim" class="form-label">Hora Fim</label>
        <input type="time" name="hora_fim" id="hora_fim" class="form-control w-50" value="<?= $hora_fim_formatada ?>" required>
    </div>

    <div class="mb-3">
        <label for="data" class="form-label">Data</label>
        <input type="date" name="data" id="data" class="form-control w-50" value="<?= $data_formatada ?>" required>
    </div>

    <div class="mb-3">
        <label for="servico_id" class="form-label">Serviço</label>
        <select name="servico_id" id="servico_id" class="form-select w-50" required>
            <?php foreach ($servicos as $servico): ?>
                <option value="<?= $servico['id'] ?>" <?= $servico['id'] == $agendamento['servico_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($servico['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status do Agendamento</label>
        <select name="status" id="status" class="form-select w-50" required>
            <option value="confirmado" <?= $agendamento['status'] === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
            <option value="concluido" <?= $agendamento['status'] === 'concluido' ? 'selected' : '' ?>>Concluído</option>
            <option value="cancelado" <?= $agendamento['status'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    <a href="agendamentos.php" class="btn btn-secondary">Voltar</a>
</form>

</div>
</body>
</html>
