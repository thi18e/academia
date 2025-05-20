<?php
session_start();
require '../config/database.php'; // Sua conexão PDO aqui

// Verifica se está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../site/login.php');
    exit;
}

// Define usuário logado e tipo
$idUsuarioLogado = $_SESSION['usuario']['id'];
$tipoUsuario = $_SESSION['usuario']['tipo'];
$nomeUsuarioLogado = $_SESSION['usuario']['nome'];

// Define se é profissional ou não
$eh_profissional = ($tipoUsuario === 'profissional');

// Busca agendamentos conforme o tipo do usuário
if ($eh_profissional) {
    // Busca agendamentos onde o profissional é o usuário logado
    $stmt = $pdo->prepare("
        SELECT a.id, s.nome AS servico, 
               c.nome AS cliente, 
               p.nome AS profissional,
               a.data_hora_inicio, a.data_hora_fim, a.status
        FROM agendamentos a
        JOIN usuarios c ON a.cliente_id = c.id
        JOIN usuarios p ON a.profissional_id = p.id
        JOIN servicos s ON a.servico_id = s.id
        WHERE a.profissional_id = :idUsuario
        ORDER BY a.data_hora_inicio ASC
    ");
} else {
    // Busca agendamentos onde o cliente é o usuário logado
    $stmt = $pdo->prepare("
        SELECT a.id, s.nome AS servico, 
               c.nome AS cliente, 
               p.nome AS profissional,
               a.data_hora_inicio, a.data_hora_fim, a.status
        FROM agendamentos a
        JOIN usuarios c ON a.cliente_id = c.id
        JOIN usuarios p ON a.profissional_id = p.id
        JOIN servicos s ON a.servico_id = s.id
        WHERE a.cliente_id = :idUsuario
        ORDER BY a.data_hora_inicio ASC
    ");
}

$stmt->execute(['idUsuario' => $idUsuarioLogado]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Meus Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<?php
if ($eh_profissional) {
    include '../includes/navbarprof.php';
} else {
    include '../includes/navbaragendamento.php';
}
?>

<div class="container py-4">
    <h2 class="mb-4">Meus Agendamentos</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Serviço</th>
                    <th><?= $eh_profissional ? 'Cliente' : 'Profissional' ?></th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Status</th>
                    <?php if ($eh_profissional): ?>
                    <th>Ações</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($agendamentos): ?>
                    <?php foreach ($agendamentos as $ag): ?>
                    <tr>
                        <td><?= htmlspecialchars($ag['servico']) ?></td>
                        <td><?= htmlspecialchars($eh_profissional ? $ag['cliente'] : $ag['profissional']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($ag['data_hora_inicio'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($ag['data_hora_fim'])) ?></td>
                        <td><?= ucfirst(htmlspecialchars($ag['status'])) ?></td>
                        <?php if ($eh_profissional): ?>
                        <td>
                            <a href="editar_agendamento.php?id=<?= $ag['id'] ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                            <a href="excluir_agendamento.php?id=<?= $ag['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este agendamento?');">Excluir</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $eh_profissional ? 6 : 5 ?>" class="text-center">Nenhum agendamento encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer class="mt-auto bg-dark text-white text-center py-3">
    &copy; <?= date('Y') ?> Minha Academia
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
