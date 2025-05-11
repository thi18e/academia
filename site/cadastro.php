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
    <link rel="stylesheet" href="../assets/css/cadastro.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

    <div class="container">
        <h2>Cadastre-se</h2>

        <?php if (!empty($mensagemErro)): ?>
            <div id="erro-alert" style="background-color: red; color: white; padding: 10px; margin-bottom: 10px; text-align: center;">
                <?php echo $mensagemErro; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="cadastro.php" enctype="multipart/form-data">
            <input type="text" id="nome" name="nome" placeholder="Nome completo:" required>

            <input type="email" id="email" name="email" placeholder="Email:" required>

            <input type="password" id="senha" name="senha" placeholder="Senha:" required>

            <input type="text" id="telefone" name ="telefone" placeholder="Número de telefone (Opcional):">

            <input type="hidden" id="foto" name="foto" accept="image/*">

            <input type="hidden" name="tipo" value="cliente">

            <br>

            <button type="submit">Cadastrar</button>

            <br>

            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
        </form>
    </div>

    <?php
        include('../includes/footer.php');
    ?>
</body>
</html>
