<?php
session_start();
require_once '../conexão/conexao.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }

$plano_valor = $_GET['plano'] ?? 75;
$nome_plano = ($plano_valor == 100) ? "Cabelo e Barba" : "Cabelo";

$stmt = $pdo->prepare("SELECT nome, email, telefone FROM clientes WHERE id = :id");
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Dados | Family Brook</title>
    <link rel="stylesheet" href="confirmar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>

<div class="box">
    <div class="icon-header">
        <i class="fas fa-shield-alt"></i>
    </div>
    <h2>Confirmar Dados</h2>
    <span class="subtitle">Plano: <strong><?php echo $nome_plano; ?></strong></span>

    <form action="pagamento.php" method="POST">
        <input type="hidden" name="plano_valor" value="<?php echo $plano_valor; ?>">
        <input type="hidden" name="nome_plano" value="<?php echo $nome_plano; ?>">
        
        <div class="input-group">
            <label><i class="fas fa-user-circle"></i> Nome</label>
            <input type="text" value="<?php echo $cliente['nome']; ?>" readonly>
        </div>

        <div class="input-group">
            <label><i class="fab fa-whatsapp"></i> WhatsApp</label>
            <input type="text" value="<?php echo $cliente['telefone']; ?>" readonly>
        </div>

        <div class="input-group">
            <label><i class="fas fa-address-card"></i> CPF (Obrigatório)</label>
            <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" required autocomplete="off">
        </div>
        
        <div class="btn-grupo">
            <button type="submit" class="btn-gerar">
                <i class="fas fa-qrcode"></i> GERAR PIX AGORA
            </button>
            
            <a href="javascript:history.back()" class="btn-voltar">
                <i class="fas fa-chevron-left"></i> Voltar e alterar plano
            </a>
        </div>
    </form>
</div>

<script>
    // Máscara de CPF automática
    document.getElementById('cpf').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });
</script>
</body>
</html>