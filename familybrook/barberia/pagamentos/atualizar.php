<?php
session_start();
require_once '../conexão/conexao.php'; // Ajuste conforme seu arquivo de conexão

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_data'])) {
    $nova_data = $_POST['nova_data'];
    $id_usuario = $_SESSION['usuario_id'];

    try {
        $sql = "UPDATE clientes SET data_vencimento = :nova_data WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nova_data' => $nova_data,
            ':id' => $id_usuario
        ]);

        // Volta para a página de planos para ver o aviso funcionando
        header("Location: /barberia/cli/planos.php"); 
        exit;
    } catch (PDOException $e) {
        echo "Erro ao atualizar: " . $e->getMessage();
    }
}