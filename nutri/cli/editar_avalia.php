<?php
require_once '../conexão/conexao.php';
session_start();

$id = $_GET['id'];
$cliente_id = $_SESSION['cliente_id'];

// Busca a avaliação garantindo que pertence ao cliente logado
$sql = "SELECT * FROM avaliacoes WHERE id_avaliacao = :id AND cliente_id = :cliente_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id'=>$id,'cliente_id'=>$cliente_id]);
$avaliacao = $stmt->fetch();

if (!$avaliacao) {
    header("Location: minha_avalia.php");
    exit;
}

if ($_POST) {
    $comentario = $_POST['comentario'];
    $nota = $_POST['nota'];

    $sql = "UPDATE avaliacoes 
            SET comentario = :comentario, nota = :nota 
            WHERE id_avaliacao = :id AND cliente_id = :cliente_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'comentario'=>$comentario,
        'nota'=>$nota,
        'id'=>$id,
        'cliente_id'=>$cliente_id
    ]);

    header("Location: minha_avalia.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Avaliação</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="editar_avalia.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
</head>
<body>
    <section class="secao">
        <div class="confirmacao-box">
            <h2><i class="fas fa-star-half-alt"></i> Editar Avaliação</h2>
            <p class="texto-centro">Altere sua opinião sobre o atendimento abaixo:</p>

            <form method="post">
                <div class="campo-edicao">
                    <label><i class="fas fa-comment-dots"></i> Comentário:</label>
                    <textarea name="comentario" required><?= htmlspecialchars($avaliacao['comentario']) ?></textarea>
                </div>

                <div class="campo-edicao">
                    <label><i class="fas fa-award"></i> Sua Nota:</label>
                    <select name="nota" required>
                        <?php for($i=1;$i<=5;$i++): ?>
                            <option value="<?= $i ?>" <?= $avaliacao['nota']==$i?'selected':'' ?>>
                                <?= $i ?> Estrela<?= $i > 1 ? 's' : '' ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="confirmacao-botoes">
                    <button type="submit" class="btn-pill btn-confirmar">
                        <i class="fas fa-check"></i> Salvar Alterações
                    </button>
                    <a href="minha_avalia.php" class="btn-pill btn-voltar">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </section>
</body>
</html>