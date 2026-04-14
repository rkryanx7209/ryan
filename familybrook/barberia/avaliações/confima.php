<?php
session_start();

// Função para validar palavras impróprias
function contemPalavrao($texto, $lista) {
    $texto = mb_strtolower($texto, 'UTF-8');
    foreach ($lista as $palavrao) {
        if (preg_match('/\b' . preg_quote($palavrao, '/') . '\b/u', $texto)) {
            return true;
        }
    }
    return false;
}

// Captura dados do formulário
$nome       = $_POST['nome'] ?? '';
$comentario = $_POST['comentario'] ?? '';
$nota       = $_POST['nota'] ?? '';

// Lista de palavrões para filtro
$palavroes = ['porra','caralho','puta','merda','bosta','foder','fodase','fdp','viado','desgraça'];
$temPalavrao = contemPalavrao($comentario, $palavroes);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Avaliação - Family Brook</title>
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
    <link rel="stylesheet" href="ava.css">
</head>
<body>

<section class="secao">
    <div class="conteudo">
        <div class="confirmacao-box">
            <h2>Sua Avaliação</h2>

            <?php if ($temPalavrao): ?>
                <div class="erro-msg">
                    <strong>Conteúdo Impróprio!</strong><br>
                    Por favor, remova palavras ofensivas do seu comentário.
                </div>
                <p class="comentario-preview">"<?= htmlspecialchars($comentario) ?>"</p>
                
                <div class="confirmacao-botoes">
                    <button onclick="window.history.back()" class="btn-voltar">Voltar e Editar</button>
                </div>

            <?php else: ?>
                <h4>Confirme os detalhes da sua mensagem:</h4>

                <div class="info-review">
                    <p><strong>NOME:</strong> <span><?= htmlspecialchars($nome) ?></span></p>
                    <p><strong>NOTA:</strong> <span><?= htmlspecialchars($nota) ?> / 5 Estrelas</span></p>
                    <p><strong>MENSAGEM:</strong> <span>"<?= htmlspecialchars($comentario) ?>"</span></p>
                </div>

                <div class="confirmacao-botoes">
                    <form action="avaliar.php" method="POST" style="display: flex; gap: 15px; width: 100%;">
                        <input type="hidden" name="nome" value="<?= htmlspecialchars($nome) ?>">
                        <input type="hidden" name="nota" value="<?= htmlspecialchars($nota) ?>">
                        <input type="hidden" name="comentario" value="<?= htmlspecialchars($comentario) ?>">
                        
                        <button type="button" class="btn-voltar" onclick="window.location.href='../pagina/index_logado.php'">Cancelar</button>
                        <button type="submit" class="btn-confirmar">Enviar Agora</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

</body>
</html>