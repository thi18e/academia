<?php
session_start();
$usuarioNome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : null;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">Academia</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#sobre">Sobre</a></li>
        <li class="nav-item"><a class="nav-link" href="#planos">Planos</a></li>
        <li class="nav-item"><a class="nav-link" href="#estrutura">Estrutura</a></li>
        <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>

        <?php if ($usuarioNome): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" id="usuarioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php echo htmlspecialchars($usuarioNome); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="usuarioDropdown">
              <li><a class="dropdown-item" href="../site/perfil.php">Perfil</a></li>
              <li><a class="dropdown-item" href="../site/agendamentos.php">Agendamentos</a></li>
              <li><a class="dropdown-item" href="../config/logout.php">Sair</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="../site/login.php">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
