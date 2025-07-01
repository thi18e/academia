<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../config/database.php';

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../site/login.php');
    exit;
}

// Pega ID do agendamento
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Busca dados do agendamento
$stmt = $pdo->prepare("
    SELECT * FROM agendamentos WHERE id = :id
");
$stmt->execute([':id' => $id]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    echo "Agendamento não encontrado.";
    exit;
}

// Buscar dados para os selects
$clientes = $pdo->query("SELECT id, nome FROM usuarios WHERE tipo = 'cliente'")->fetchAll(PDO::FETCH_ASSOC);
$profissionais = $pdo->query("SELECT id, nome FROM usuarios WHERE tipo = 'profissional'")->fetchAll(PDO::FETCH_ASSOC);
$servicos = $pdo->query("SELECT id, nome FROM servicos")->fetchAll(PDO::FETCH_ASSOC);

// Processa envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $profissional_id = intval($_POST['profissional_id']);
    $servico_id = intval($_POST['servico_id']);
    $inicio = $_POST['data_hora_inicio'];
    $fim = $_POST['data_hora_fim'];
    $status = $_POST['status'];

    // Validações básicas
    if (strtotime($inicio) >= strtotime($fim)) {
        $erro = "A data/hora de início deve ser anterior à data/hora de fim.";
    } else {
        // Verifica conflitos de agendamento
        $verifica = $pdo->prepare("
            SELECT COUNT(*) FROM agendamentos 
            WHERE id != :id AND profissional_id = :prof_id AND cliente_id = :cli_id 
            AND servico_id = :serv_id AND (
                (data_hora_inicio < :fim AND data_hora_fim > :inicio)
            )
        ");
        $verifica->execute([
            ':id' => $id,
            ':prof_id' => $profissional_id,
            ':cli_id' => $cliente_id,
            ':serv_id' => $servico_id,
            ':inicio' => $inicio,
            ':fim' => $fim
        ]);

        $conflitos = $verifica->fetchColumn();

        if ($conflitos > 0) {
            $erro = "Conflito detectado: já existe um agendamento com esses dados nesse horário.";
        } else {
            // Atualiza agendamento
            $stmt = $pdo->prepare("
                UPDATE agendamentos SET 
                    cliente_id = :cliente_id, 
                    profissional_id = :profissional_id,
                    servico_id = :servico_id,
                    data_hora_inicio = :inicio,
                    data_hora_fim = :fim,
                    status = :status
                WHERE id = :id
            ");
            $stmt->execute([
                ':cliente_id' => $cliente_id,
                ':profissional_id' => $profissional_id,
                ':servico_id' => $servico_id,
                ':inicio' => $inicio,
                ':fim' => $fim,
                ':status' => $status,
                ':id' => $id
            ]);

            header('Location: admin.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Agendamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2>Editar Agendamento</h2>

    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <div class="ms-0 text-start" style="width: 60%;">
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Cliente</label>
                <select name="cliente_id" class="form-select" required>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $agendamento['cliente_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Profissional</label>
                <select name="profissional_id" class="form-select" required>
                    <?php foreach ($profissionais as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $p['id'] == $agendamento['profissional_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Serviço</label>
                <select name="servico_id" class="form-select" required>
                    <?php foreach ($servicos as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $agendamento['servico_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Data e Hora de Início</label>
                <input type="datetime-local" name="data_hora_inicio" class="form-control" required
                       value="<?= date('Y-m-d\TH:i', strtotime($agendamento['data_hora_inicio'])) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Data e Hora de Fim</label>
                <input type="datetime-local" name="data_hora_fim" class="form-control" required
                       value="<?= date('Y-m-d\TH:i', strtotime($agendamento['data_hora_fim'])) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <?php foreach (['confirmado', 'cancelado', 'concluido'] as $st): ?>
                        <option value="<?= $st ?>" <?= $agendamento['status'] == $st ? 'selected' : '' ?>>
                            <?= ucfirst($st) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Salvar Alterações</button>
                <a href="admin.php" class="btn btn-secondary btn-sm">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>