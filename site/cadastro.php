<?php
session_start(); // Inicia a sessão
require '../config/database.php';

$mensagemErro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'] ?? 'cliente';
    $foto = !empty($_FILES['foto']['name']) ? $_FILES['foto']['name'] : null;

    // Verifica se o email já está cadastrado
    $sqlVerifica = "SELECT email FROM usuarios WHERE email = :email";
    $stmtVerifica = $pdo->prepare($sqlVerifica);
    $stmtVerifica->bindParam(':email', $email);
    $stmtVerifica->execute();

    if ($stmtVerifica->rowCount() > 0) {
        $mensagemErro = "Esse email já está cadastrado!";
    } else {
        // Insere o usuário no banco de dados
        $sql = "INSERT INTO usuarios (nome, email, telefone, senha, tipo, foto) VALUES (:nome, :email, :telefone, :senha, :tipo, :foto)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':foto', $foto);

        if ($stmt->execute()) {
            // **Armazena informações do usuário na sessão**
            $_SESSION['usuario_id'] = $pdo->lastInsertId();
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_email'] = $email;

            // **Redireciona para a página inicial logado**
            header("Location: ../public/home.php");
            exit();
        } else {
            $mensagemErro = "Erro ao cadastrar usuário.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>

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
        <div class="card shadow p-4 w-100" style="max-width: 500px;">
            <h2 class="text-center mb-4">Cadastre-se</h2>

            <?php if (!empty($mensagemErro)): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo $mensagemErro; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="cadastro.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome completo:</label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Digite seu nome" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Digite seu email" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha:</label>
                    <input type="password" id="senha" name="senha" class="form-control" placeholder="Crie uma senha" required>
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label">Número de telefone (Opcional):</label>
                    <input type="text" id="telefone" name="telefone" class="form-control" placeholder="(00) 00000-0000">
                </div>

                <input type="hidden" id="foto" name="foto" accept="image/*">
                <input type="hidden" name="tipo" value="cliente">

                <button type="submit" class="btn btn-primary w-100">Cadastrar</button>


                <p class="mt-3 text-center">
                    Já tem uma conta? <a href="login.php">Faça login</a>
                </p>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>