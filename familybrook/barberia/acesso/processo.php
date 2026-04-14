<?php
session_start();
require_once '../conexão/conexao.php';

if (isset($_GET['limpar'])) {
    unset($_SESSION['email_validado']);
    header("Location: recuperar.php");
    exit;
}

$acao = $_POST['acao'] ?? '';

if ($acao === 'verificar') {
    $email = trim($_POST['email'] ?? '');
    
    // Usamos * para evitar erro se o nome da coluna de ID for diferente
    $checkAdmin = $pdo->prepare("SELECT * FROM admin WHERE email = :e");
    $checkAdmin->execute([':e' => $email]);
    $isAdmin = $checkAdmin->fetch();
    
    $checkCliente = $pdo->prepare("SELECT * FROM clientes WHERE email = :e");
    $checkCliente->execute([':e' => $email]);
    $isCliente = $checkCliente->fetch();

    if ($isAdmin || $isCliente) {
        $_SESSION['email_validado'] = $email;
    } else {
        $_SESSION['erro_recuperar'] = "E-mail não encontrado.";
    }
    header("Location: recuperar.php");
    exit;
}

if ($acao === 'mudar_senha') {
    $email = $_SESSION['email_validado'] ?? '';
    $nova = $_POST['nova_senha'] ?? '';
    $conf = $_POST['confirmar_senha'] ?? '';

    if ($nova !== $conf) {
        $_SESSION['erro_recuperar'] = "As senhas não coincidem!";
        header("Location: recuperar.php");
        exit;
    }

    // Atualiza a senha. Nota: Se você usa password_hash no login, deve usar aqui também.
    $sql1 = $pdo->prepare("UPDATE admin SET senha = :s WHERE email = :e");
    $sql1->execute([':s' => $nova, ':e' => $email]);

    $sql2 = $pdo->prepare("UPDATE clientes SET senha = :s WHERE email = :e");
    $sql2->execute([':s' => $nova, ':e' => $email]);

    unset($_SESSION['email_validado']);
    $_SESSION['sucesso_login'] = "Senha alterada com sucesso! Faça login.";
    header("Location: login.php");
    exit;
}