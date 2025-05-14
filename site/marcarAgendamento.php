=<?php 
require '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    // Redireciona para a página de login
    header('Location: login.php');
    exit();
}

// Busca profissionais do banco
$profissionais = $pdo->query("SELECT id, nome FROM usuarios WHERE tipo = 'profissional'")->fetchAll();

// Busca serviços ativos do banco
$servicos = $pdo->query("SELECT id, nome FROM servicos WHERE ativo = 1")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = $_POST['cliente_id']; // ou pegue da sessão
    $profissional_id = $_POST['profissional_id'];
    $servico_id = $_POST['servico_id'];
    $data = $_POST['data'];  // Data separada
    $hora = $_POST['hora'];  // Hora separada

    // Combina data e hora em um único valor para o agendamento
    $data_hora_inicio = $data . ' ' . $hora . ':00';

    // Criar um objeto DateTime para a data/hora enviada
    $data_hora_inicio_obj = new DateTime($data_hora_inicio);
    $data_hora_atual = new DateTime();  // Obtém a data e hora atual

    // Verifica se a data/hora escolhida é no passado
    if ($data_hora_inicio_obj < $data_hora_atual) {
        echo "<div class='alert alert-danger'>Não é possível agendar para uma data/hora no passado.</div>";
    } else {
        // Buscar duração do serviço com base no ID enviado
        $stmt = $pdo->prepare("SELECT duracao FROM servicos WHERE id = ?");
        $stmt->execute([$servico_id]);
        $duracao = $stmt->fetchColumn();

        if ($duracao) {
            $inicio = $data_hora_inicio_obj;
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
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Marcar Agendamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light p-4">

<?php include '../includes/navbaragendamento.php'; ?>

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
            <label for="data" class="form-label">Data</label>
            <input type="date" name="data" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="hora" class="form-label">Hora</label>
            <input type="time" name="hora" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Agendar</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>