<?php
require_once '../conexão/conexao.php';

// Busca os serviços com os nomes reais das colunas do seu banco
$stmt = $pdo->query("SELECT id, categoria, nome, preco, duracao_minutos, descricao FROM servicos ORDER BY categoria DESC");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Serviços | Family Brook</title>
    <link rel="stylesheet" href="gerenciar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="container-painel">
    <h1><i class="fas fa-edit"></i> Editar Serviços do Site</h1>

    <form action="processo_servicos.php" method="POST">
        <div class="card-unico-servicos">
            
            <?php foreach ($servicos as $s): ?>
                <div class="linha-edicao-servico">
                    <input type="hidden" name="id[]" value="<?= $s['id'] ?>">
                    
                    <div class="coluna-input principal">
                        <i class="fas fa-cut"></i>
                        <input type="text" name="nome[]" value="<?= htmlspecialchars($s['nome']) ?>" placeholder="Nome">
                        <input type="text" name="descricao[]" value="<?= htmlspecialchars($s['descricao'] ?? '') ?>" placeholder="Descrição">
                    </div>

                    <div class="coluna-input lateral">
                        <div class="mini-input">
                            <i class="fas fa-coins"></i>
                            <input type="number" step="0.01" name="preco[]" value="<?= $s['preco'] ?>">
                        </div>
                        
                        <div class="mini-input">
                            <i class="fas fa-clock"></i>
                            <input type="number" name="duracao[]" value="<?= $s['duracao_minutos'] ?>">
                        </div>

                        <select name="categoria[]">
                            <option value="Populares" <?= $s['categoria'] == 'Populares' ? 'selected' : '' ?>>Populares ⭐</option>
                            <option value="Outros" <?= $s['categoria'] == 'Outros' ? 'selected' : '' ?>>Outros Serviços</option>
                            <option value="Planos" <?= $s['categoria'] == 'Planos' ? 'selected' : '' ?>>Planos Mensais 👑</option>
                        </select>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="footer-acoes">
            <button type="submit" class="btn-pill">
                <i class="fas fa-check-circle"></i> ATUALIZAR TUDO
            </button>
            <br>
            <a href="painel.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar ao Painel</a>
        </div>
    </form>
</div>
</body>
</html>