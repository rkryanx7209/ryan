<?php
session_start();
// Caminho dinâmico para garantir que encontre o conexao.php na raiz do projeto
require_once '../conexão/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($email) || empty($senha)) {
    $_SESSION['erro_login'] = "Preencha todos os campos.";
    header("Location: login.php");
    exit;
}

try {
    // 1. BUSCA NA TABELA ADMIN
    $sqlAdmin = "SELECT id, nome, senha FROM admin WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sqlAdmin);
    $stmt->execute([':email' => $email]);
    $userAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se é Admin (Comparando senha em texto simples conforme seu padrão atual)
    if ($userAdmin && $senha === $userAdmin['senha']) {
        $_SESSION['usuario_id']   = $userAdmin['id'];
        $_SESSION['usuario_nome'] = $userAdmin['nome'];
        $_SESSION['tipo']         = 'admin';
        
        header("Location: ../barber/painel.php");
        exit();
    }

    // 2. SE NÃO FOR ADMIN, BUSCA NA TABELA CLIENTES
    $sqlCli = "SELECT id, nome, senha FROM clientes WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sqlCli);
    $stmt->execute([':email' => $email]);
    $userCli = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se é Cliente
    if ($userCli && $senha === $userCli['senha']) {
        $_SESSION['usuario_id']   = $userCli['id'];
        $_SESSION['usuario_nome'] = $userCli['nome'];
        $_SESSION['tipo']         = 'cliente';
        
        // REDIRECIONAMENTO CORRETO PARA CLIENTE LOGADO
        header("Location: ../pagina/index_logado.php");
        exit();
    }

    // 3. SE NÃO ENCONTRAR NENHUM DOS DOIS
    $_SESSION['erro_login'] = "E-mail ou senha incorretos.";
    header("Location: login.php");
    exit();

} catch (PDOException $e) {
    die("Erro no sistema: " . $e->getMessage());
}