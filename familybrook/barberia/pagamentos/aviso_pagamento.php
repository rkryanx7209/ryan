<?php
$plano = $_GET['plano'] ?? 'Cabelo';
$valor = $_GET['valor'] ?? '75';
$seu_numero = "5512999999999"; // COLOQUE SEU NÚMERO AQUI
$mensagem = urlencode("Olá! Enviei o pagamento do plano $plano (R$ $valor). Segue o comprovante:");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento PIX | Family Brook</title>
    <link rel="stylesheet" href="aviso_pagamento.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>
    <div class="box">
        <div class="icon-header">
            <i class="fas fa-qrcode"></i>
        </div>
        <h2>Pagamento via PIX</h2>
        
        <div class="info-plano">
            <p>Plano: <strong><?php echo $plano; ?></strong></p>
            <p>Valor: <strong class="price">R$ <?php echo $valor; ?></strong></p>
        </div>

        <div class="qr-container">
            <img src="../fotos/qr_code_pix.png" alt="QR Code PIX">
            
            <div class="chave-pix-container" onclick="copiarPix()">
                <span><i class="fas fa-copy"></i> Clique para copiar a chave:</span>
                <div class="pix-badge">
                    <strong id="chave-texto">seu-email@pix.com</strong>
                </div>
                <small id="feedback-copia">Copiado!</small>
            </div>
        </div>
        
        <div class="btn-grupo">
            <a href="https://wa.me/<?php echo $seu_numero; ?>?text=<?php echo $mensagem; ?>" target="_blank" class="btn-whats">
                <i class="fab fa-whatsapp"></i> ENVIAR COMPROVANTE
            </a>
            
            <a href="../pagina/index_logado.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar ao início
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