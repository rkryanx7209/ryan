<?php
session_start();
require_once '../conexão/conexao.php';

// Resetar processo caso o usuário cancele
if (isset($_GET['limpar'])) {
    unset($_SESSION['email_validado']);
    header("Location: recuperar.php");
    exit;
}

$acao = $_POST['acao'] ?? '';

if ($acao === 'verificar') {
    $email = trim($_POST['email'] ?? '');
    
    // Busca simples para evitar erro de UNION
    $checkAdmin = $pdo->prepare("SELECT id_admin FROM admin WHERE email = :e");
    $checkAdmin->execute([':e' => $email]);
    
    $checkCliente = $pdo->prepare("SELECT id FROM clientes WHERE email = :e");
    $checkCliente->execute([':e' => $email]);

    if ($checkAdmin->fetch() || $checkCliente->fetch()) {
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

    // Atualiza em ambas (só afetará a que tiver o e-mail correspondente)
    $sql1 = $pdo->prepare("UPDATE admin SET senha = :s WHERE email = :e");
    $sql1->execute([':s' => $nova, ':e' => $email]);

    $sql2 = $pdo->prepare("UPDATE clientes SET senha = :s WHERE email = :e");
    $sql2->execute([':s' => $nova, ':e' => $email]);

    unset($_SESSION['email_validado']);
    $_SESSION['sucesso'] = "Senha alterada com sucesso!";
    header("Location: login.php");
    exit;
}