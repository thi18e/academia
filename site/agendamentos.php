<?php
require '../config/database.php'; // conexão com banco (deve definir $pdo)

// Exemplo com cliente_id fixo = 1
$cliente_id = 1;

$stmt = $pdo->prepare("
    SELECT a.id, s.nome AS servico, u.nome AS profissional, a.data_hora_inicio, a.data_hora_fim, a.status
    FROM agendamentos a
    JOIN servicos s ON a.servico_id = s.id
    JOIN usuarios u ON a.profissional_id = u.id
    WHERE a.cliente_id = ?
    ORDER BY a.data_hora_inicio DESC
");
$stmt->execute([$cliente_id]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meus Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <h2>Meus Agendamentos</h2>
    <table class="table table-bordered table-striped mt-4">
        <thead class="table-dark">
            <tr>
                <th>Serviço</th>
                <th>Profissional</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($resultados) > 0): ?>
            <?php foreach($resultados as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['servico']) ?></td>
                    <td><?= htmlspecialchars($row['profissional']) ?></td>
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
</body>
</html>
