<?php
require '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_tipo = $_SESSION['usuario_tipo'];
$eh_profissional = ($usuario_tipo === 'profissional');

$stmt = $pdo->prepare("
    SELECT a.id, s.nome AS servico, 
           CASE 
               WHEN a.profissional_id = :usuario_id THEN c.nome
               ELSE p.nome 
           END AS envolvido, 
           a.data_hora_inicio, a.data_hora_fim, a.status
    FROM agendamentos a
    JOIN servicos s ON a.servico_id = s.id
    JOIN usuarios p ON a.profissional_id = p.id
    JOIN usuarios c ON a.cliente_id = c.id
    WHERE a.profissional_id = :usuario_id OR a.cliente_id = :usuario_id
    ORDER BY a.data_hora_inicio DESC
");

$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meus Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<?php if ($eh_profissional) {
    include '../includes/navbarprof.php';
} else {
    include '../includes/navbaragendamento.php';
}
?>

<div class="container py-4">
    <h2>Meus Agendamentos</h2>

    <table class="table table-bordered table-striped mt-4">
        <thead class="table-dark">
            <tr>
                <th>Serviço</th>
                <th><?= $eh_profissional ? 'Cliente' : 'Profissional' ?></th>
                <th>Início</th>
                <th>Fim</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($resultados)): ?>
            <?php foreach ($resultados as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['servico']) ?></td>
                    <td><?= htmlspecialchars($row['envolvido']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['data_hora_inicio'])) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['data_hora_fim'])) ?></td>
                    <td><span class="badge bg-secondary"><?= ucfirst(htmlspecialchars($row['status'])) ?></span></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">Nenhum agendamento encontrado.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
