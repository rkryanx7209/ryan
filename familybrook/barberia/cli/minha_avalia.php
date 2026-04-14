<?php 
require_once '../conexão/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../acesso/login.php");
    exit;
}

$id_logado = $_SESSION['usuario_id'];
$nome_logado = $_SESSION['usuario_nome'] ?? '';

try {
    // Busca as avaliações do cliente logado
    $sql = "SELECT * FROM avaliacoes WHERE id_cliente = ? OR nome_cliente = ? ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_logado, $nome_logado]);
    $avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $avaliacoes = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Avaliações | Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="minha_avalia.css">
    <link rel="icon" href="../fotos/icon.jpeg">
</head>
<body>

<div class="container-central">
    <h1>Minhas Avaliações</h1>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="alerta-sucesso" id="msg-sucesso">
            <?= $_GET['sucesso'] == 'excluido' ? 'Avaliação removida com sucesso!' : 'Alterações salvas com sucesso!' ?>
        </div>
    <?php endif; ?>

    <?php if (empty($avaliacoes)): ?>
        <div class="card" style="text-align: center;">
            <p>Nenhuma avaliação encontrada para seu perfil.</p>
        </div>
    <?php else: ?>
        <?php foreach ($avaliacoes as $a): ?>
            <div class="card">
                <div class="nota-estrelas">
                    <?php for($i=1; $i<=5; $i++) echo $i <= $a['nota'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                    <span class="nota-numero"><?= $a['nota'] ?>.0</span>
                </div>

                <p>
                    <i class="fas fa-user-circle"></i> 
                    <strong>Postado como:</strong> <?= htmlspecialchars($a['nome_cliente'] ?? 'Cliente') ?>
                </p>

                <div class="comentario-texto">
                    <i class="fas fa-quote-left"></i>
                    <?= htmlspecialchars($a['comentario']) ?>
                </div>

                <div class="container-acoes">
                    <a href="editar_avalia.php?id=<?= $a['id'] ?>" class="btn-pill verde-musgo">
                        <i class="fas fa-edit"></i> EDITAR
                    </a>
                    
                    <a href="excluir_avalia.php?id=<?= $a['id'] ?>" 
                    class="btn-pill vermelho-cancelar" 
                    onclick="return confirm('Deseja realmente excluir sua avaliação?')">
                        <i class="fas fa-trash-alt"></i> EXCLUIR
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="container-botoes-final">
        <a href="../pagina/index_logado.php#avalia" class="btn-pill verde-musgo">
            <i class="fas fa-star"></i> AVALIAR NOVAMENTE
        </a>
        
        <a href="../pagina/painel_cli.php" class="btn-pill outline-gold">
            <i class="fas fa-arrow-left"></i> VOLTAR
        </a>
    </div>
</div>

<script>
    if (window.location.search.includes('sucesso')) {
        setTimeout(() => {
            const msg = document.getElementById('msg-sucesso');
            if (msg) {
                msg.style.transition = "opacity 0.6s ease";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 600);
            }
            const url = new URL(window.location);
            url.searchParams.delete('sucesso');
            window.history.replaceState({}, document.title, url.pathname);
        }, 3000);
    }
</script>

</body>
</html>