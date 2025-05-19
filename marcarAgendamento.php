<?php 
require '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Busca profissionais do banco
$profissionais = $pdo->query("SELECT id, nome FROM usuarios WHERE tipo = 'profissional'")->fetchAll();

// Busca serviços ativos do banco
$servicos = $pdo->query("SELECT id, nome FROM servicos WHERE ativo = 1")->fetchAll();

// Variável para mensagem
$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cliente_id = $usuario_id;
    $profissional_id = $_POST['profissional_id'];
    $servico_id = $_POST['servico_id'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];

    $data_hora_inicio = $data . ' ' . $hora . ':00';
    $inicio = new DateTime($data_hora_inicio);
    $agora = new DateTime();

    if ($inicio < $agora) {
        $mensagem = "<div class='alert alert-danger mt-3'>Você não pode agendar para o passado.</div>";
    } else {
        // Busca duração do serviço
        $stmt = $pdo->prepare("SELECT duracao FROM servicos WHERE id = ?");
        $stmt->execute([$servico_id]);
        $duracao = $stmt->fetchColumn();

        if ($duracao) {
            $fim = clone $inicio;
            $fim->modify("+{$duracao} minutes");

            $data_hora_fim = $fim->format("Y-m-d H:i:s");
            $data_hora_inicio = $inicio->format("Y-m-d H:i:s");

            // Verifica conflito com o cliente
            $stmtCliente = $pdo->prepare("
                SELECT COUNT(*) FROM agendamentos
                WHERE cliente_id = ? AND status IN ('confirmado', 'concluido')
                AND data_hora_inicio < ? AND data_hora_fim > ?
            ");
            $stmtCliente->execute([$cliente_id, $data_hora_fim, $data_hora_inicio]);
            $conflitoCliente = $stmtCliente->fetchColumn();

            // Verifica conflito com o profissional
            $stmtProf = $pdo->prepare("
                SELECT COUNT(*) FROM agendamentos
                WHERE profissional_id = ? AND status IN ('confirmado', 'concluido')
                AND data_hora_inicio < ? AND data_hora_fim > ?
            ");
            $stmtProf->execute([$profissional_id, $data_hora_fim, $data_hora_inicio]);
            $conflitoProf = $stmtProf->fetchColumn();

            if ($conflitoCliente > 0) {
                $mensagem = "<div class='alert alert-warning mt-3'>Você já tem um agendamento nesse horário.</div>";
            } elseif ($conflitoProf > 0) {
                $mensagem = "<div class='alert alert-warning mt-3'>O profissional já tem um atendimento nesse horário.</div>";
            } else {
                // Inserção do agendamento
                $stmt = $pdo->prepare("
                    INSERT INTO agendamentos (cliente_id, profissional_id, servico_id, data_hora_inicio, data_hora_fim)
                    VALUES (?, ?, ?, ?, ?)
                ");
                if ($stmt->execute([$cliente_id, $profissional_id, $servico_id, $data_hora_inicio, $data_hora_fim])) {
                    $mensagem = "<div class='alert alert-success mt-3'>Agendamento realizado com sucesso!</div>";
                } else {
                    $mensagem = "<div class='alert alert-danger mt-3'>Erro ao agendar. Tente novamente.</div>";
                }
            }
        } else {
            $mensagem = "<div class='alert alert-warning mt-3'>Serviço inválido.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Marcar Agendamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

<?php include '../includes/navbaragendamento.php'; ?>

<div class="container flex-grow-1 py-4">
    <h2>Marcar Agendamento</h2>

    <?= $mensagem ?>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="profissional" class="form-label">Profissional</label>
            <select name="profissional_id" class="form-select w-50" required>
                <option value="" disabled selected>Selecione</option>
                <?php foreach($profissionais as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="servico" class="form-label">Serviço</label>
            <select name="servico_id" class="form-select w-50" required>
                <option value="" disabled selected>Selecione</option>
                <?php foreach($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" name="data" class="form-control w-50" required>
        </div>

        <div class="mb-3">
            <label for="hora" class="form-label">Hora</label>
            <input type="time" name="hora" class="form-control w-50" required>
        </div>

        <button type="submit" class="btn btn-primary">Agendar</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
