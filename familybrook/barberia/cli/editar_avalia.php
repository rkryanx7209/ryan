<?php
require_once '../conexão/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) { header("Location: ../acesso/login.php"); exit; }

$id_logado = $_SESSION['usuario_id'];
$id_avalia = $_GET['id'] ?? 0;

// Busca a avaliação pelo ID primário (coluna 'id' do seu SQL)
$sql = "SELECT * FROM avaliacoes WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_avalia]);
$avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$avaliacao) { header("Location: minha_avalia.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nota = $_POST['nota'];
    $comentario = $_POST['comentario'];

    $update = "UPDATE avaliacoes SET nota = ?, comentario = ? WHERE id = ?";
    $stmt_up = $pdo->prepare($update);
    $stmt_up->execute([$nota, $comentario, $id_avalia]);

    header("Location: minha_avalia.php?sucesso=editado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Avaliação</title>
    <link rel="stylesheet" href="editar_avalia.css">
        <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>
    <div class="confirmacao-box">
        <h2>Editar Avaliação</h2>
        <form method="POST">
            <label>Nota (1 a 5):</label>
            <input type="number" name="nota" min="1" max="5" value="<?= $avaliacao['nota'] ?>" style="width: 100%; padding: 10px; margin-bottom: 10px;">
            <label>Comentário:</label>
            <textarea name="comentario" required style="width: 100%; padding: 10px;"><?= htmlspecialchars($avaliacao['comentario']) ?></textarea>
            <button type="submit" class="btn-pill verde-musgo" style="width: 100%; margin-top: 15px;">SALVAR ALTERAÇÕES</button>
            <a href="minha_avalia.php" style="display: block; text-align: center; margin-top: 10px; color: white;">Cancelar</a>
        </form>
    </div>
</body>
</html>