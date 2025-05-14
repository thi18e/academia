<?php 
require '../config/database.php';

// Busca profissionais do banco
$profissionais = $pdo->query("SELECT id, nome FROM usuarios WHERE tipo = 'profissional'")->fetchAll();

// Busca serviços ativos do banco
$servicos = $pdo->query("SELECT id, nome FROM servicos WHERE ativo = 1")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = $_POST['cliente_id']; // ou pegue da sessão
    $profissional_id = $_POST['profissional_id'];
    $servico_id = $_POST['servico_id'];
    $data_hora_inicio = $_POST['data_hora'];

    // Buscar duração do serviço com base no ID enviado
    $stmt = $pdo->prepare("SELECT duracao FROM servicos WHERE id = ?");
    $stmt->execute([$servico_id]);
    $duracao = $stmt->fetchColumn();

    if ($duracao) {
        $inicio = new DateTime($data_hora_inicio);
        $fim = clone $inicio;
        $fim->modify("+{$duracao} minutes");
        $data_hora_fim = $fim->format("Y-m-d H:i:s");

        // Inserir agendamento
        $stmt = $pdo->prepare("INSERT INTO agendamentos (cliente_id, profissional_id, servico_id, data_hora_inicio, data_hora_fim) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$cliente_id, $profissional_id, $servico_id, $data_hora_inicio, $data_hora_fim])) {
            echo "<div class='alert alert-success'>Agendamento realizado com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao agendar.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Serviço não encontrado.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Marcar Agendamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <h2>Marcar Agendamento</h2>
    <form method="POST" class="mt-4">
        <input type="hidden" name="cliente_id" value="1"> <!-- ajuste conforme login -->

        <div class="mb-3">
            <label for="profissional" class="form-label">Profissional</label>
            <select name="profissional_id" class="form-select" required>
                <?php foreach($profissionais as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="servico" class="form-label">Serviço</label>
            <select name="servico_id" class="form-select" required>
                <?php foreach($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="data_hora" class="form-label">Data e Hora</label>
            <input type="datetime-local" name="data_hora" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Agendar</button>
    </form>
</div>
</body>
</html>
