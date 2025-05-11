<?php
session_start(); // Inicia a sessão
require '../config/database.php'; // Conectando ao banco

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Busca o usuário pelo e-mail
    $sql = "SELECT id, nome, senha FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // **Armazena informações na sessão**
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $email;

        // **Redireciona para a página inicial logado**
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
    <link rel="stylesheet" href="../assets/css/cadastro.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <?php if (!empty($mensagemErro)): ?>
            <div style="color: red; text-align: center; margin-bottom: 10px;">
                <?php echo $mensagemErro; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" enctype="multipart/form-data">
            <input type="email" name="email" placeholder="Email:" required>
            <input type="password" name="senha" placeholder="Senha:" required>
            <button type="submit">Entrar</button>
            <p>Não possui conta? <a href="cadastro.php">Cadastre-se</a></p>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
