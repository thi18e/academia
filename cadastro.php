<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../config/database.php';

$mensagemErro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'] ?? 'cliente';
    $plano = $_POST['plano'];

    $precos = ["Básico" => 49.90, "Premium" => 79.90];
    $precoPlano = $precos[$plano];

    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $uploadDir = '../uploads/';
        $foto = $uploadDir . basename($_FILES['foto']['name']);
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $foto)) {
            $mensagemErro = "Erro ao fazer upload da foto.";
        }
    }

    $sqlVerifica = "SELECT id FROM usuarios WHERE email = :email";
    $stmtVerifica = $pdo->prepare($sqlVerifica);
    $stmtVerifica->bindParam(':email', $email);
    $stmtVerifica->execute();

    if ($stmtVerifica->rowCount() > 0) {
        $mensagemErro = "Esse email já está cadastrado!";
    } else {
        try {
            $pdo->beginTransaction();

            $sqlUsuario = "INSERT INTO usuarios (nome, email, telefone, senha, tipo, foto) VALUES (:nome, :email, :telefone, :senha, :tipo, :foto)";
            $stmtUsuario = $pdo->prepare($sqlUsuario);
            $stmtUsuario->bindParam(':nome', $nome);
            $stmtUsuario->bindParam(':email', $email);
            $stmtUsuario->bindParam(':telefone', $telefone);
            $stmtUsuario->bindParam(':senha', $senha);
            $stmtUsuario->bindParam(':tipo', $tipo);
            $stmtUsuario->bindParam(':foto', $foto);
            $stmtUsuario->execute();

            $usuarioId = $pdo->lastInsertId();

            $sqlPlano = "INSERT INTO planos (cliente_id, nome, preco) VALUES (:cliente_id, :nome, :preco)";
            $stmtPlano = $pdo->prepare($sqlPlano);
            $stmtPlano->bindParam(':cliente_id', $usuarioId);
            $stmtPlano->bindParam(':nome', $plano);
            $stmtPlano->bindParam(':preco', $precoPlano);
            $stmtPlano->execute();

            $pdo->commit();

            $_SESSION['usuario'] = [
                'id' => $usuarioId,
                'nome' => $nome,
                'email' => $email
            ];
;

            header("Location: ../public/home.php");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $mensagemErro = "Erro ao cadastrar usuário: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/cadastro.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

    <?php include '../includes/navbarlogin.php'; ?>

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
                    <input type="text" id="nome" name="nome" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha:</label>
                    <input type="password" id="senha" name="senha" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label">Número de telefone (Opcional):</label>
                    <input type="text" id="telefone" name="telefone" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="plano" class="form-label">Escolha seu plano:</label>
                    <select id="plano" name="plano" class="form-control" required>
                        <option value="Básico">Básico - R$49,90</option>
                        <option value="Premium">Premium - R$79,90</option>
                    </select>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="souProfissional" onchange="atualizarTipo()">
                    <label class="form-check-label" for="souProfissional">Sou um profissional</label>
                </div>

                <input type="hidden" name="tipo" id="tipoUsuario" value="cliente">
                <input type="hidden" id="foto" name="foto" accept="image/*">

                <button type="submit" class="btn btn-primary w-100">Cadastrar</button>

                <p class="mt-3 text-center">
                    Já tem uma conta? <a href="login.php">Faça login</a>
                </p>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function atualizarTipo() {
        let checkbox = document.getElementById("souProfissional");
        let tipoUsuario = document.getElementById("tipoUsuario");
        tipoUsuario.value = checkbox.checked ? "profissional" : "cliente";
    }
    </script>

</body>
</html>
