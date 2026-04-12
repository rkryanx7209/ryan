<?php
require_once '../conexão/conexao.php';
session_start();

$id = $_GET['id'];
$cliente_id = $_SESSION['cliente_id'];

// ALTERAÇÃO AQUI: Troquei 'id' por 'id_avaliacao'
$sql = "DELETE FROM avaliacoes WHERE id_avaliacao = :id AND cliente_id = :cliente_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id'=>$id, 'cliente_id'=>$cliente_id]);

header("Location: minha_avalia.php");
exit;
?>