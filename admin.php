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

// Pega o nome do usuário logado para mostrar na tela
$nomeUsuarioLogado = $_SESSION['usuario']['nome'];

// Buscar clientes
$clientes = $pdo->query("SELECT id, nome, email, telefone FROM usuarios WHERE tipo = 'cliente'")->fetchAll(PDO::FETCH_ASSOC);

// Buscar profissionais
$profissionais = $pdo->query("SELECT id, nome, email, telefone FROM usuarios WHERE tipo = 'profissional'")->fetchAll(PDO::FETCH_ASSOC);

// Buscar agendamentos em andamento
$agendamentos = $pdo->query("
    SELECT 
        a.id, u.nome AS cliente, p.nome AS profissional, s.nome AS servico, 
        a.data_hora_inicio, a.data_hora_fim, a.status
    FROM agendamentos a
    JOIN usuarios u ON a.cliente_id = u.id
    JOIN usuarios p ON a.profissional_id = p.id
    JOIN servicos s ON a.servico_id = s.id
    WHERE a.status = 'confirmado'
    ORDER BY a.data_hora_inicio ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Área do Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <!-- Exibe o nome do usuário logado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Painel do Administrador</h2>
        <div>
            <span>Olá, <strong><?= htmlspecialchars($nomeUsuarioLogado) ?></strong>!</span>
            <a href="../config/logout.php" class="btn btn-sm btn-outline-secondary ms-3">Sair</a>
        </div>
    </div>

    <!-- Clientes -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Clientes</div>
        <div class="card-body">
            <?php if ($clientes): ?>
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= $cliente['id'] ?></td>
                                <td><?= htmlspecialchars($cliente['nome']) ?></td>
                                <td><?= htmlspecialchars($cliente['email']) ?></td>
                                <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                                <td>
                                    <a href="editar_usuario.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                                    <a href="excluir_usuario.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este cliente?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum cliente cadastrado.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Profissionais -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Profissionais</div>
        <div class="card-body">
            <?php if ($profissionais): ?>
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($profissionais as $prof): ?>
                            <tr>
                                <td><?= $prof['id'] ?></td>
                                <td><?= htmlspecialchars($prof['nome']) ?></td>
                                <td><?= htmlspecialchars($prof['email']) ?></td>
                                <td><?= htmlspecialchars($prof['telefone']) ?></td>
                                <td>
                                    <a href="editar_usuario.php?id=<?= $prof['id'] ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                                    <a href="excluir_usuario.php?id=<?= $prof['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este profissional?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum profissional cadastrado.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Agendamentos em andamento -->
    <div class="card mb-4">
        <div class="card-header bg-warning">Agendamentos em Andamento</div>
        <div class="card-body">
            <?php if ($agendamentos): ?>
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th><th>Cliente</th><th>Profissional</th>
                            <th>Serviço</th><th>Início</th><th>Fim</th><th>Status</th><th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agendamentos as $ag): ?>
                            <tr>
                                <td><?= $ag['id'] ?></td>
                                <td><?= htmlspecialchars($ag['cliente']) ?></td>
                                <td><?= htmlspecialchars($ag['profissional']) ?></td>
                                <td><?= htmlspecialchars($ag['servico']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($ag['data_hora_inicio'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($ag['data_hora_fim'])) ?></td>
                                <td><?= ucfirst(htmlspecialchars($ag['status'])) ?></td>
                                <td>
                                    <a href="editar_agendamento.php?id=<?= $ag['id'] ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                                    <a href="excluir_agendamento.php?id=<?= $ag['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este agendamento?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Sem agendamentos confirmados.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
