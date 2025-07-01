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

// Verifica se um ID foi passado
if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id = $_GET['id'];

// Busca os dados do usuário
$stmt = $pdo->prepare("SELECT id, nome, email, telefone, cep, logradouro, bairro, cidade, estado FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}

// Processa o formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $logradouro = $_POST['logradouro'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';

    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, telefone = ?, cep = ?, logradouro = ?, bairro = ?, cidade = ?, estado = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $telefone, $cep, $logradouro, $bairro, $cidade, $estado, $id]);

    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Editar Usuário</h2>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($usuario['telefone']) ?>">
        </div>

        <!-- Campos de endereço -->
        <h5 class="mt-3">Endereço</h5>
        <div class="mb-3">
            <label class="form-label">CEP</label>
            <input type="text" name="cep" id="cep" class="form-control" value="<?= htmlspecialchars($usuario['cep']) ?>" required>
            <button type="button" onclick="buscarCep()" class="btn btn-secondary mt-2">Buscar CEP</button>
        </div>
        <div class="mb-3">
            <label class="form-label">Logradouro</label>
            <input type="text" name="logradouro" id="logradouro" class="form-control" value="<?= htmlspecialchars($usuario['logradouro']) ?>" >
        </div>
        <div class="mb-3">
            <label class="form-label">Bairro</label>
            <input type="text" name="bairro" id="bairro" class="form-control" value="<?= htmlspecialchars($usuario['bairro']) ?>" >
        </div>
        <div class="mb-3">
            <label class="form-label">Cidade</label>
            <input type="text" name="cidade" id="cidade" class="form-control" value="<?= htmlspecialchars($usuario['cidade']) ?>" >
        </div>
        <div class="mb-3">
            <label class="form-label">Estado</label>
            <input type="text" name="estado" id="estado" class="form-control" value="<?= htmlspecialchars($usuario['estado']) ?>">
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="admin.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
function buscarCep() {
    let cep = document.getElementById("cep").value.replace(/\D/g, '');

    if (cep.length !== 8) {
        alert("CEP inválido! Deve conter 8 dígitos.");
        return;
    }

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (!data.erro) {
                document.getElementById("logradouro").value = data.logradouro;
                document.getElementById("bairro").value = data.bairro;
                document.getElementById("cidade").value = data.localidade;
                document.getElementById("estado").value = data.uf;
            } else {
                alert("CEP não encontrado.");
            }
        })
        .catch(error => console.error("Erro ao buscar CEP:", error));
}
</script>

</body>
</html>
