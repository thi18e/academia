<?php
session_start();
require_once '../config/database.php'; 

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];

// Busca informações do usuário
$stmt = $pdo->prepare("SELECT id, nome, email, telefone, tipo, cep, logradouro, bairro, cidade, estado FROM usuarios WHERE id = ?");
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

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/perfil.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

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

            <!-- Seção de Endereço agrupada -->
            <div class="mb-3 border rounded p-3">
                <h5><strong>Endereço Cadastrado</strong></h5>
                <?php if (!empty($usuario['cep']) && !empty($usuario['logradouro']) && !empty($usuario['bairro'])): ?>
                    <p class="card-text"><strong>CEP:</strong> <?= htmlspecialchars($usuario['cep']) ?></p>
                    <p class="card-text"><strong>Logradouro:</strong> <?= htmlspecialchars($usuario['logradouro']) ?></p>
                    <p class="card-text"><strong>Bairro:</strong> <?= htmlspecialchars($usuario['bairro']) ?></p>
                    <p class="card-text"><strong>Cidade:</strong> <?= htmlspecialchars($usuario['cidade']) ?></p>
                    <p class="card-text"><strong>Estado:</strong> <?= htmlspecialchars($usuario['estado']) ?></p>
                <?php else: ?>
                    <p class="text-muted">Nenhum endereço cadastrado.</p>
                <?php endif; ?>
            </div>

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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
