<?php 
session_start();
require_once '../conexão/conexao.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $senha    = trim($_POST['senha'] ?? '');
    $idade    = trim($_POST['idade'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $genero   = trim($_POST['genero'] ?? '');
    $cep      = trim($_POST['cep'] ?? '');

    // Validação básica
    if ($nome === '' || $email === '' || $senha === '') {
        $_SESSION['erro'] = "Nome, e-mail e senha são obrigatórios!";
        header("Location: cadrastocliente.php");
        exit;
    }

    // Verifica se o e-mail já existe
    $check = $pdo->prepare("SELECT id FROM clientes WHERE email = :email LIMIT 1");
    $check->execute([':email' => $email]);

    if ($check->fetch()) {
        $_SESSION['erro'] = "Este e-mail já está cadastrado!";
        header("Location: cadrastocliente.php");
        exit;
    }

    // Salva no banco de dados
    $sql = "INSERT INTO clientes (nome, email, senha, idade, telefone, genero, cep) 
            VALUES (:nome, :email, :senha, :idade, :telefone, :genero, :cep)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            ':nome'     => $nome,
            ':email'    => $email,
            ':senha'    => $senha,
            ':idade'    => $idade,
            ':telefone' => $telefone,
            ':genero'   => $genero,
            ':cep'      => $cep
        ]);

        $_SESSION['sucesso'] = "Cadastro realizado! Faça login.";
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao cadastrar. Verifique se as colunas existem no banco.";
        header("Location: cadrastocliente.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro do Cliente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="cadrasto.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
</head>
<body>

<div class="login-box">
    <h2>Cadastro</h2>

    <form method="POST">
        <input type="text" name="nome" placeholder="Nome completo" required>
        <input type="email" name="email" placeholder="E-mail" required>
        
        <div class="input-row">
            <input type="number" name="idade" placeholder="Idade">
            <select name="genero" class="select-pill">
                <option value="">Gênero</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
                <option value="Outro">Outro</option>
            </select>
        </div>

        <input type="text" name="telefone" placeholder="Telefone / WhatsApp">
        <input type="text" name="cep" placeholder="CEP">
        <input type="password" name="senha" placeholder="Crie uma senha" required>

        <?php if (!empty($_SESSION['erro'])): ?>
            <div class="erro-login"><?= $_SESSION['erro']; ?></div>
        <?php unset($_SESSION['erro']); endif; ?>

        <div class="botoes-login">
            <button type="submit" class="btn-pill verde-musgo">
                <i class="fas fa-check"></i> Finalizar Cadastro
            </button>
            <a href="login.php" class="btn-pill outline-verde">
                <i class="fas fa-sign-in-alt"></i> Já tenho conta
            </a>
        </div>
    </form>
</div>

</body>
</html>