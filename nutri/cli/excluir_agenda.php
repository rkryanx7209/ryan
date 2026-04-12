<?php
require_once '../conexão/conexao.php';
session_start();

if (empty($_SESSION['cliente_id'])) {
    header("Location: ../acesso/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$id_cliente = $_SESSION['cliente_id'];

// Alterado de 'id' para 'id_agenda' e verificado 'fk_id_cliente'
$sql = "DELETE FROM agenda WHERE id_agenda = :id AND fk_id_cliente = :cliente";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $id,
    ':cliente' => $id_cliente
]);

header("Location: minha_agenda.php");
exit;
?>