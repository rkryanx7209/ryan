<?php
session_start();
require_once '../conexão/conexao.php'; 

// Verifica se a requisição veio de um formulário POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro_login'] = "Acesso inválido.";
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

// 1. Validação de campos vazios
if (empty($email) || empty($senha)) {
    $_SESSION['erro_login'] = "Preencha e-mail e senha.";
    header("Location: login.php");
    exit;
}

// 2. Valida formato do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['erro_login'] = "E-mail inválido.";
    header("Location: login.php");
    exit;
}

// 3. Valida tamanho mínimo da senha (conforme seu padrão de 4 caracteres)
if (strlen($senha) < 4) {
    $_SESSION['erro_login'] = "A senha deve ter pelo menos 4 caracteres.";
    header("Location: login.php");
    exit;
}

// --- TENTATIVA DE LOGIN: ADMIN ---
$sqlAdmin = "SELECT id_admin, nome, senha FROM admin WHERE email = :email LIMIT 1";
$stmtAdmin = $pdo->prepare($sqlAdmin);
$stmtAdmin->bindParam(':email', $email);
$stmtAdmin->execute();
$admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

if ($admin && $senha === $admin['senha']) {
    $_SESSION['admin_id'] = $admin['id_admin'];
    $_SESSION['admin_nome'] = $admin['nome'];
    header("Location: ../nutrii/painel.php");
    exit;
}

// --- TENTATIVA DE LOGIN: CLIENTE ---
// Aqui buscamos os novos campos: idade, telefone, genero e cep
$sqlCliente = "SELECT id, nome, senha, idade, telefone, genero, cep FROM clientes WHERE email = :email LIMIT 1";
$stmtCliente = $pdo->prepare($sqlCliente);
$stmtCliente->bindParam(':email', $email);
$stmtCliente->execute();
$cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

if ($cliente && $senha === $cliente['senha']) {
    // Dados de Identificação
    $_SESSION['cliente_id']   = $cliente['id'];
    $_SESSION['cliente_nome'] = $cliente['nome'];
    
    // Dados para preenchimento automático (Perfil)
    $_SESSION['cliente_idade']    = $cliente['idade'];
    $_SESSION['cliente_telefone'] = $cliente['telefone'];
    $_SESSION['cliente_genero']   = $cliente['genero'];
    $_SESSION['cliente_cep']      = $cliente['cep'];

    // Redireciona para a área logada
    header("Location: ../pagina/index_logado.php"); 
    exit;
}

// Se não encontrou em nenhuma tabela ou a senha estiver errada
$_SESSION['erro_login'] = "E-mail ou senha inválidos!";
header("Location: login.php");
exit;