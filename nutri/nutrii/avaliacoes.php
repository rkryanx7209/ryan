<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../acesso/login.php");
    exit();
}

require_once '../conexão/conexao.php';

// Busca todas as avaliações (Removido o ORDER BY id que causava erro)
$sql = "SELECT * FROM avaliacoes";
$stmt = $pdo->query($sql);
$todas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações - Área Restrita</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="avalia.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
</head>
<body>

    <div class="container-painel" style="max-width: 900px;">
        <h1>Avaliações Recebidas</h1>

        <div class="busca-container" style="margin-bottom: 25px; position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #7b8d72;"></i>
            <input type="text" id="inputBusca" placeholder="Pesquisar por nome do paciente..." onkeyup="filtrarAvaliacoes()" 
            style="width: 100%; padding: 12px 15px 12px 45px; border-radius: 25px; border: 1px solid rgba(255,255,255,0.1); background: #2a2a2a; color: #fff; outline: none; font-family: 'Poppins', sans-serif;">
        </div>

        <div class="colunas-avaliacoes">
            
            <div class="coluna">
                <h2 style="color: #7b8d72;"><i class="fas fa-smile"></i> Feedback Positivo:</h2>
                <div class="lista-avaliacoes">
                    <?php 
                    $tem_boa = false;
                    foreach ($todas as $v): 
                        if ($v['nota'] >= 4): 
                            $tem_boa = true;
                    ?>
                        <div class="card-comentario card-bom">
                            <div class="card-header">
                                <strong class="nome-paciente"><?= htmlspecialchars($v['nome']); ?></strong>
                                <span class="nota-tag"> <?= $v['nota']; ?>/5</span>
                            </div>
                            <p class="texto-comentario"><?= htmlspecialchars($v['comentario']); ?></p>
                        </div>
                    <?php endif; endforeach; ?>
                    <?php if (!$tem_boa): ?>
                        <p class="msg-vazia" style="opacity:0.5; font-size:13px;">Sem avaliações positivas.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="coluna">
                <h2 style="color: #ff6b6b;"><i class="fas fa-frown"></i> Feedback Negativo:</h2>
                <div class="lista-avaliacoes">
                    <?php 
                    $tem_ruim = false;
                    foreach ($todas as $v): 
                        if ($v['nota'] <= 3): 
                            $tem_ruim = true;
                    ?>
                        <div class="card-comentario card-ruim">
                            <div class="card-header">
                                <strong class="nome-paciente"><?= htmlspecialchars($v['nome']); ?></strong>
                                <span style="background: #ff6b6b; color: white; padding: 1px 6px; border-radius: 8px; font-size: 11px; font-weight: bold;"> <?= $v['nota']; ?>/5</span>
                            </div>
                            <p class="texto-comentario"><?= htmlspecialchars($v['comentario']); ?></p>
                        </div>
                    <?php endif; endforeach; ?>
                    <?php if (!$tem_ruim): ?>
                        <p class="msg-vazia" style="opacity:0.5; font-size:13px;">Sem críticas registradas.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer-avaliacoes">
            <a href="painel.php" class="btn-pill" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </div>

    <script>
    function filtrarAvaliacoes() {
        // Pega o valor digitado
        const termoBusca = document.getElementById('inputBusca').value.toLowerCase();
        // Seleciona todos os cards de comentário
        const cards = document.getElementsByClassName('card-comentario');
        
        for (let i = 0; i < cards.length; i++) {
            // Busca o nome dentro de cada card (na classe .nome-paciente que adicionei)
            const nomePaciente = cards[i].querySelector('.nome-paciente').innerText.toLowerCase();
            
            // Se o nome incluir o que foi digitado, mostra o card, senão esconde
            if (nomePaciente.includes(termoBusca)) {
                cards[i].style.display = "";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
    </script>

</body>
</html>