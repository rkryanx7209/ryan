<?php
session_start();
require_once '../conexão/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pagina/aviso.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$query = $pdo->prepare("SELECT data_vencimento, assinante FROM clientes WHERE id = :id");
$query->execute([':id' => $id_usuario]);
$user = $query->fetch();

$vencimento_atual = $user['data_vencimento'];
if ($user['assinante'] == 'Sim' && strtotime($vencimento_atual) > time()) {
    $nova_data = date('d/m/Y', strtotime($vencimento_atual . ' + 30 days'));
} else {
    $nova_data = date('d/m/Y', strtotime('+ 30 days'));
}

$telefone_barbearia = "5511999999999"; 
$mensagem = urlencode("Olá! Acabei de pagar o PIX para renovar meu plano. Novo vencimento: " . $nova_data);
$link_whatsapp = "https://wa.me/{$telefone_barbearia}?text={$mensagem}";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renovação PIX - Family Brook</title>
    <link rel="stylesheet" href="renovar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>

<div class="box">
    <div class="icon-header">
        <i class="fas fa-sync-alt"></i>
    </div>
    <h2>Renovação de Plano</h2>
    
    <div class="info-plano">
        <p>Valor da Renovação: <strong class="price">R$ 80,00</strong></p>
        <p>Nova Validade: <strong><?= $nova_data ?></strong></p>
    </div>

    <div class="qr-container">
        <img src="../fotos/qrcode_pix.png" alt="QR Code PIX">
        
        <div class="chave-pix-container" onclick="copiarPix()">
            <span><i class="fas fa-copy"></i> Clique para copiar a chave:</span>
            <div class="pix-badge">
                <strong id="chave-texto">suachavepix@email.com</strong>
            </div>
            <small id="feedback-copia">Copiado!</small>
        </div>
    </div>

    <div class="btn-grupo">
        <a href="<?= $link_whatsapp ?>" target="_blank" class="btn-whats" onclick="setTimeout(function(){ window.location.href='processo_pagamento.php'; }, 2000);">
            <i class="fab fa-whatsapp"></i> ENVIAR COMPROVANTE
        </a>
        
        <a href="../cli/planos.php" class="btn-voltar">
            <i class="fas fa-chevron-left"></i> Voltar aos planos
        </a>
    </div>
</div>

<script>
function copiarPix() {
    const textoChave = document.getElementById('chave-texto').innerText;
    navigator.clipboard.writeText(textoChave).then(() => {
        const feedback = document.getElementById('feedback-copia');
        feedback.style.opacity = '1';
        setTimeout(() => {
            feedback.style.opacity = '0';
        }, 2000);
    });
}
</script>
</body>
</html>