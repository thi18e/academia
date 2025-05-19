<?php
session_start();
require_once '../config/database.php'; // Aqui você deve garantir que $pdo está definido

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Busca informações do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Busca agendamentos do cliente
$agendamentos = [];
if ($usuario['tipo'] === 'cliente') {
    $stmt = $pdo->prepare("
        SELECT a.*, s.nome AS servico, s.descricao, s.duracao, s.preco
        FROM agendamentos a
        INNER JOIN servicos s ON a.servico_id = s.id
        WHERE a.cliente_id = ?
        ORDER BY a.data_hora_inicio DESC
    ");
    $stmt->execute([$usuario_id]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Busca planos do cliente
$planos = [];
if ($usuario['tipo'] === 'cliente') {
    $stmt = $pdo->prepare("SELECT nome, descricao, preco, criado_em FROM planos WHERE cliente_id = ?");
    $stmt->execute([$usuario_id]);
    $planos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include '../includes/navbarperfil.php'; ?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Perfil do Usuário</h2>
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($usuario['nome']) ?></h4>
            <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p class="card-text"><strong>Tipo:</strong> <?= htmlspecialchars($usuario['tipo']) ?></p>
            <?php if (!empty($usuario['telefone'])): ?>
                <p class="card-text"><strong>Telefone:</strong> <?= htmlspecialchars($usuario['telefone']) ?></p>
            <?php endif; ?>
            <a href="../config/logout.php" class="btn btn-danger mt-2">Sair da Conta</a>
        </div>
    </div>

    <?php if ($usuario['tipo'] === 'cliente'): ?>

        <h4>Meus Planos</h4>
        <?php if (count($planos) > 0): ?>
            <div class="row">
                <?php foreach ($planos as $plano): ?>
                    <div class="col-md-6">
                        <div class="card mb-3 border-success">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($plano['nome']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($plano['descricao']) ?></p>
                                <p class="card-text">
                                    <strong>Preço:</strong> R$ <?= number_format($plano['preco'], 2, ',', '.') ?><br>
                                    <small class="text-muted">Adquirido em <?= date('d/m/Y', strtotime($plano['criado_em'])) ?></small>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Você ainda não possui nenhum plano ativo.</p>
        <?php endif; ?>

        <h4 class="mt-5">Meus Agendamentos</h4>
        <?php if (count($agendamentos) > 0): ?>
            <div class="row">
                <?php foreach ($agendamentos as $ag): ?>
                    <div class="col-md-6">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($ag['servico']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($ag['descricao']) ?></p>
                                <p class="card-text">
                                    <strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($ag['data_hora_inicio'])) ?><br>
                                    <strong>Duração:</strong> <?= $ag['duracao'] ?> min<br>
                                    <strong>Preço:</strong> R$ <?= number_format($ag['preco'], 2, ',', '.') ?>
                                </p>
                                <span class="badge bg-<?= $ag['status'] === 'confirmado' ? 'success' : ($ag['status'] === 'cancelado' ? 'danger' : 'secondary') ?>">
                                    <?= htmlspecialchars($ag['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Você ainda não possui agendamentos.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
