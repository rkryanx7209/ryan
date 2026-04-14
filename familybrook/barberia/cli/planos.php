<?php
session_start();
require_once '../conexão/conexao.php'; 

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pagina/aviso.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$query = $pdo->prepare("SELECT assinante, data_vencimento FROM clientes WHERE id = :id");
$query->execute([':id' => $id_usuario]);
$usuario = $query->fetch(PDO::FETCH_ASSOC);

$status = $usuario['assinante'] ?? 'Não'; 
$vencimento = $usuario['data_vencimento'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Plano - Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="planos.css?v=2">
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>

<div class="container-central">
    <div class="card">
        <?php 
        if ($vencimento && $status == 'Sim') {
            $hoje = new DateTime();
            $hoje->setTime(0, 0, 0);
            $data_venc = new DateTime($vencimento);
            $data_venc->setTime(0, 0, 0);
            
            $diff = $hoje->diff($data_venc);
            $dias = (int)$diff->format("%r%a");

            if ($dias <= 3 && $dias >= 0) {
                $msg_texto = ($dias == 0) ? "Seu plano vence HOJE!" : (($dias == 1) ? "Seu plano vence AMANHÃ!" : "Seu plano vence em $dias dias!");
                echo "<div class='alerta-vencimento'><i class='fas fa-exclamation-triangle'></i> $msg_texto </div>";
            }
        }
        ?>

        <h1>Meu Plano</h1>
        
        <div class="info-plano">
            <p>Status: <strong class="<?= ($status == 'Sim') ? 'status-ativo' : 'status-inativo' ?>">
                <?= ($status == 'Sim') ? 'ASSINANTE ATIVO' : 'SEM PLANO' ?>
            </strong></p>

            <?php if ($status == 'Sim'): ?>
                <p>Vencimento: <strong><?= date('d/m/Y', strtotime($vencimento)) ?></strong></p>
            <?php endif; ?>
        </div>

        <div class="container-acoes-planos">
            <?php if ($status == 'Sim'): ?>
                <a href="../pagamentos/renovar.php" class="btn-plano gold-full">RENOVAR</a>
            <?php else: ?>
                <a href="../pagamentos/confirmar.php" class="btn-plano gold-full">ASSINAR</a>
            <?php endif; ?>

            <a href="../pagina/painel_cli.php" class="btn-plano gold-border">
                <i class="fas fa-arrow-left"></i> VOLTAR
            </a>
        </div>
    </div>
</div>

</body>
</html>