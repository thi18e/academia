<?php
require '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensagemErro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $sql = "SELECT id, nome, senha, tipo FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $email;
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        header("Location: ../public/home.php");
        exit();
    } else {
        $mensagemErro = "Email ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Seus estilos personalizados -->
    <link rel="stylesheet" href="../assets/css/cadastro.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

    <?php
    include '../includes/navbarlogin.php';
    ?>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">Login</h2>

            <?php if (!empty($mensagemErro)): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo $mensagemErro; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Digite seu email" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha:</label>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Digite sua senha" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Entrar</button>

                <p class="mt-3 text-center">
                    Não possui conta? <a href="cadastro.php">Cadastre-se</a>
                </p>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap JS (opcional, para componentes como modais) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
