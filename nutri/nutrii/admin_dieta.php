<?php
require_once '../conexão/conexao.php'; 

$cliente_id = $_REQUEST['cliente_id'] ?? null;
if (!$cliente_id) { die("Erro: ID do cliente não fornecido."); }

$msg = "";
$dieta = null;

// Lógica de Excluir
if (isset($_POST['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM dietas WHERE cliente_id = ?");
    $stmt->execute([$cliente_id]);
    $msg = "Dieta excluída com sucesso!";
}

// Lógica de Salvar (Insert ou Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    $cafe_manha = $_POST['cafe_manha'];
    $almoco     = $_POST['almoco'];
    $cafe_tarde = $_POST['cafe_tarde'];
    $janta      = $_POST['janta'];

    $check = $pdo->prepare("SELECT id FROM dietas WHERE cliente_id = ?");
    $check->execute([$cliente_id]);
    
    if ($check->rowCount() > 0) {
        $sql = "UPDATE dietas SET cafe_manha = ?, almoco = ?, cafe_tarde = ?, janta = ? WHERE cliente_id = ?";
        $pdo->prepare($sql)->execute([$cafe_manha, $almoco, $cafe_tarde, $janta, $cliente_id]);
        $msg = "Dieta atualizada com sucesso!";
    } else {
        $sql = "INSERT INTO dietas (cliente_id, cafe_manha, almoco, cafe_tarde, janta) VALUES (?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$cliente_id, $cafe_manha, $almoco, $cafe_tarde, $janta]);
        $msg = "Nova dieta cadastrada!";
    }
}

// Busca dados atuais
$stmt = $pdo->prepare("SELECT * FROM dietas WHERE cliente_id = ?");
$stmt->execute([$cliente_id]);
$dieta = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['novo'])) { $dieta = null; }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Dieta | Dra. Daniele França</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="admin_dieta.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
</head>
<body>

<div class="container-form">
    <h2><i class="fas fa-utensils"></i> Cadastrar Dieta</h2>

    <?php if($msg): ?>
        <p class="success-msg"><?= $msg ?></p>
    <?php endif; ?>

    <form method="POST" action="admin_dieta.php?cliente_id=<?= $cliente_id ?>">
        
        <div class="input-group">
            <label><i class="fas fa-coffee"></i> Café da Manhã</label>
            <textarea name="cafe_manha" placeholder="Opções para o café..." required><?= htmlspecialchars($dieta['cafe_manha'] ?? '') ?></textarea>
        </div>

        <div class="input-group">
            <label><i class="fas fa-utensils"></i> Almoço</label>
            <textarea name="almoco" placeholder="Opções para o almoço..." required><?= htmlspecialchars($dieta['almoco'] ?? '') ?></textarea>
        </div>

        <div class="input-group">
            <label><i class="fas fa-apple-alt"></i> Café da Tarde</label>
            <textarea name="cafe_tarde" placeholder="Opções para o lanche..." required><?= htmlspecialchars($dieta['cafe_tarde'] ?? '') ?></textarea>
        </div>

        <div class="input-group">
            <label><i class="fas fa-moon"></i> Janta</label>
            <textarea name="janta" placeholder="Opções para a janta..." required><?= htmlspecialchars($dieta['janta'] ?? '') ?></textarea>
        </div>

        <div class="buttons-group">
            <button type="submit" name="salvar" class="btn-pill">
                <i class="fas fa-save"></i> Salvar dieta
            </button>
            
            <button type="submit" name="novo" class="btn-pill">
                <i class="fas fa-plus"></i> Nova dieta
            </button>
            
            <?php if ($dieta): ?>
                <button type="submit" name="excluir" class="btn-pill btn-danger" onclick="return confirm('Excluir esta dieta?')">
                    <i class="fas fa-trash"></i> Excluir
                </button>
            <?php endif; ?>
            
            <a href="admin_clientes.php" class="btn-pill btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </form>
</div>

</body>
</html>