<?php
session_start();
// Proteção extra para que apenas admin acesse esse arquivo diretamente
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../acesso/login.php");
    exit();
}

require_once '../conexão/conexao.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // ATENÇÃO: Verifique se o nome da sua tabela é 'avaliacoes'
        $sql = "DELETE FROM avaliacoes WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Avaliação removida com sucesso!');
                    window.location.href = 'avaliacoes.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Erro ao tentar remover.');
                    window.location.href = 'avaliacoes.php';
                  </script>";
        }
    } catch (PDOException $e) {
        die("Erro ao excluir: " . $e->getMessage());
    }
} else {
    header("Location: avaliacoes.php");
    exit();
}