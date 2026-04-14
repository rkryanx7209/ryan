<?php
require_once '../conexão/conexao.php';
session_start();

// Verifica se o usuário está logado e se o ID foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['usuario_id']) && !empty($_POST['id_agenda'])) {
    
    $id_agenda = $_POST['id_agenda'];
    $id_cliente = $_SESSION['usuario_id'];

    // Deleta apenas se o agendamento pertencer ao cliente logado (Segurança extra)
    $sql = "DELETE FROM agenda WHERE id = :id AND id_cliente = :id_cliente";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([':id' => $id_agenda, ':id_cliente' => $id_cliente])) {
  
        header("Location: minha_agenda.php?msg=cancelado");
    } else {
        
        header("Location: minha_agenda.php?msg=erro");
    }
    exit;
}

header("Location: minha_agenda.php");
exit;