<?php
session_start();
require_once '../conexão/conexao.php'; // Caminho da sua conexão

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    
    $id_usuario = $_SESSION['usuario_id'];
    $valor_plano = $_POST['plano_valor']; 
    $nome_plano = $_POST['nome_plano'];

    // Define vencimento para exatamente 30 dias a partir de hoje
    $data_vencimento = date('Y-m-d', strtotime('+30 days'));

    try {
        // Atualiza o banco de dados
        $sql = "UPDATE clientes SET assinante = 'Sim', data_vencimento = :venci WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':venci' => $data_vencimento, ':id' => $id_usuario]);

        // Redireciona para a tela do PIX com os dados do plano
        header("Location: ../pagamentos/aviso_pagamento.php?plano=" . urlencode($nome_plano) . "&valor=$valor_plano");
        exit();

    } catch (PDOException $e) {
        die("Erro ao processar: " . $e->getMessage());
    }
}