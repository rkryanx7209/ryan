<?php
require_once '../conexão/conexao.php';
session_start();

if (isset($_GET['id']) && isset($_SESSION['usuario_id'])) {
    $id_avalia = $_GET['id'];
    
    // Deleta o registro pelo ID primário para não falhar com campos NULL
    $sql = "DELETE FROM avaliacoes WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_avalia]);
}

header("Location: minha_avalia.php?sucesso=excluido");
exit;