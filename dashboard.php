<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../site/login.php');
    exit;
}
$nomeUsuarioLogado = $_SESSION['usuario']['nome'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard do Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">Dashboard Admin</a>
    <div class="d-flex align-items-center">
      <span class="navbar-text text-white">
        Olá, <strong><?= htmlspecialchars($nomeUsuarioLogado) ?></strong>
      </span>
      <a href="../config/logout.php" class="btn btn-outline-light btn-sm ms-3">Sair</a>
    </div>
  </div>
</nav>

<div class="container py-5">
    <h1 class="mb-4">Painel do Administrador</h1>

    <div class="list-group shadow-sm">
        <a href="admin.php" class="list-group-item list-group-item-action list-group-item-primary">
            Painel de Administração
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
