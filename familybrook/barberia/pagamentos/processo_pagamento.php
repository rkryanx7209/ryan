<?php
session_start();
require_once '../conexão/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

// 1. Busca dados para o cálculo final
$query = $pdo->prepare("SELECT data_vencimento, assinante FROM clientes WHERE id = :id");
$query->execute([':id' => $id_usuario]);
$user = $query->fetch();

// 2. Lógica de data
if ($user['assinante'] == 'Sim' && strtotime($user['data_vencimento']) > time()) {
    $nova_data_db = date('Y-m-d', strtotime($user['data_vencimento'] . ' + 30 days'));
} else {
    $nova_data_db = date('Y-m-d', strtotime('+ 30 days'));
}

// 3. Atualiza o banco
$update = $pdo->prepare("UPDATE clientes SET assinante = 'Sim', data_vencimento = :data WHERE id = :id");
$update->execute([':data' => $nova_data_db, ':id' => $id_usuario]);

// 4. Redireciona para o painel com aviso de sucesso
header("Location: ../pagina/painel_cli.php?status=renovado");
exit;