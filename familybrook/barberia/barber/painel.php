<?php  
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Segurança: Verifica se é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../acesso/login.php");
    exit();
}

require_once '../conexão/conexao.php';

// BUSCA DE DADOS
$hoje = date('Y-m-d');
$stmt_hoje = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE data_agendamento = :hoje AND status = 'Pendente'");
$stmt_hoje->execute([':hoje' => $hoje]);
$total_hoje = $stmt_hoje->fetchColumn();

$total_clientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$total_avaliacoes = $pdo->query("SELECT COUNT(*) FROM avaliacoes")->fetchColumn();
$nome_admin = $_SESSION['usuario_nome'] ?? "Barbeiro";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin | Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="painel.css">
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>

    <div class="container-painel">
        <i class="fas fa-cut main-icon"></i>
        
        <h2>Bem-vindo, <?= htmlspecialchars($nome_admin); ?>!</h2>
        <p class="sub">Gestão da Barbearia Family Brook</p>

        <div class="estatisticas-grid">
            <div class="card-mini-info">
                <span>Cortes</span>
                <strong><?= $total_hoje ?></strong>
            </div>
            <div class="card-mini-info">
                <span>Clientes</span>
                <strong><?= $total_clientes ?></strong>
            </div>
            <div class="card-mini-info">
                <span>Avaliações</span>
                <strong><?= $total_avaliacoes ?></strong>
            </div>
        </div>

        <nav class="menu-botoes">
            <a href="agendamentos.php" class="btn-pill gold">
                <i class="fas fa-calendar-check"></i> VER AGENDAMENTOS
            </a>
            
            <a href="avaliacoes.php" class="btn-pill gold">
                <i class="fas fa-star"></i> GERENCIAR AVALIAÇÕES
            </a>

            <a href="admin_planos.php" class="btn-pill gold">
                <i class="fas fa-list"></i> VER PLANOS
            </a>
     <a href="gerenciar_servicos.php" class="btn-pill gold">
                <i class="fas fa-list"></i> GERENCIAR SERVIÇOS 
            </a>
            <a href="../index.html" class="btn-pill outline-red">
                <i class="fas fa-sign-out-alt"></i> SAIR DO SISTEMA
            </a>
        </nav>
    </div>

</body>
</html>