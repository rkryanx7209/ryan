<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. VERIFICAÇÃO DE SEGURANÇA
// Certifique-se que o tipo no seu banco é exatamente 'admin'
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../acesso/login.php");
    exit();
}

require_once '../conexão/conexao.php';

// 2. BUSCA DAS AVALIAÇÕES
try {
    // Note que usamos 'nome_cliente' conforme seu código, 
    // certifique-se que essa coluna existe na tabela 'avaliacoes'
    $sql = "SELECT id, nome_cliente, nota, comentario FROM avaliacoes ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $todas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro crítico no banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Avaliações | Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="avalia.css">
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>

<div class="container-painel">
    <h1><i class="fas fa-star"></i> Gerenciar Avaliações</h1>

    <div class="busca-box">
        <input type="text" id="inputBusca" placeholder="🔍 Pesquisar por nome do cliente..." onkeyup="filtrar()">
    </div>

    <div class="colunas-avaliacoes">
        
        <div class="coluna">
            <h2><i class="fas fa-thumbs-up"></i> Positivas</h2>
            <div class="lista-avaliacoes" id="lista-positiva">
                <?php foreach ($todas as $v): if ($v['nota'] >= 4): ?>
                    <div class="card-comentario card-bom item-avalia">
                        <div class="card-header">
                            <strong><?= htmlspecialchars($v['nome_cliente']) ?></strong>
                            <span class="nota-tag">⭐ <?= $v['nota'] ?>/5</span>
                        </div>
                        <p class="texto-comentario">"<?= htmlspecialchars($v['comentario']) ?>"</p>
                        <a href="excluir_avalia_admin.php?id=<?= $v['id'] ?>" 
                           class="btn-excluir" 
                           onclick="return confirm('Deseja apagar esta avaliação permanentemente?')">
                           <i class="fas fa-trash"></i> EXCLUIR
                        </a>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>

        <div class="coluna">
            <h2 style="color: #ff6b6b; border-bottom-color: rgba(255, 107, 107, 0.2);">
                <i class="fas fa-thumbs-down"></i> Críticas
            </h2>
            <div class="lista-avaliacoes" id="lista-critica">
                <?php foreach ($todas as $v): if ($v['nota'] <= 3): ?>
                    <div class="card-comentario card-ruim item-avalia">
                        <div class="card-header">
                            <strong><?= htmlspecialchars($v['nome_cliente']) ?></strong>
                            <span class="nota-tag tag-ruim">⭐ <?= $v['nota'] ?>/5</span>
                        </div>
                        <p class="texto-comentario">"<?= htmlspecialchars($v['comentario']) ?>"</p>
                        <a href="excluir_avalia_admin.php?id=<?= $v['id'] ?>" 
                           class="btn-excluir" 
                           onclick="return confirm('Deseja apagar esta avaliação?')">
                           <i class="fas fa-trash"></i> EXCLUIR
                        </a>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>

    </div>

    <div class="footer-avaliacoes">
        <a href="painel.php" class="btn-pill">
            <i class="fas fa-arrow-left"></i> VOLTAR AO PAINEL
        </a>
    </div>
</div>

<script>
function filtrar() {
    let filtro = document.getElementById('inputBusca').value.toLowerCase();
    let cards = document.getElementsByClassName('item-avalia');
    
    for (let i = 0; i < cards.length; i++) {
        let nome = cards[i].querySelector('strong').innerText.toLowerCase();
        cards[i].style.display = nome.includes(filtro) ? "" : "none";
    }
}
</script>

</body>
</html>