<?php
session_start();
require_once '../conexão/conexao.php';

// Verifica se o cliente está logado
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// Busca a dieta do cliente
$sql = "SELECT * FROM dietas WHERE cliente_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$cliente_id]);
$dieta = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha Dieta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="minha_dieta.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
</head>
<body>

<div class="container-dieta">
    <h2><i class="fas fa-apple-alt"></i> Minha Dieta</h2>

    <?php if ($dieta): ?>
        <div class="lista-dieta">
            <div class="card-refeicao">
                <strong><i class="fas fa-mug-hot"></i> Café da manhã</strong>
                <p><?= htmlspecialchars($dieta['cafe_manha']) ?></p>
            </div>
            <div class="card-refeicao">
                <strong><i class="fas fa-utensils"></i> Almoço</strong>
                <p><?= htmlspecialchars($dieta['almoco']) ?></p>
            </div>
            <div class="card-refeicao">
                <strong><i class="fas fa-cookie"></i> Café da tarde</strong>
                <p><?= htmlspecialchars($dieta['cafe_tarde']) ?></p>
            </div>
            <div class="card-refeicao">
                <strong><i class="fas fa-moon"></i> Janta</strong>
                <p><?= htmlspecialchars($dieta['janta']) ?></p>
            </div>
        </div>
    <?php else: ?>
        <p class="sem-dieta">Você ainda não tem uma dieta cadastrada.</p>
    <?php endif; ?>

    <div class="container-botoes">
        <a href="../pagina/index.php" class="btn-pill escuro">
            <i class="fas fa-arrow-left"></i> Voltar ao menu
        </a>
    </div>
</div>

</body>
</html>