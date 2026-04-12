<?php
require_once '../conexão/conexao.php';
session_start();

// Verifica se o cliente está logado
if (empty($_SESSION['cliente_id'])) {
    header("Location: ../acesso/login.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];
$feedback_page = '../pagina/index_logado.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitização dos dados recebidos
    $nome = trim($_POST['nome'] ?? '');
    $comentario = trim($_POST['comentario'] ?? '');
    $nota = (int)($_POST['nota'] ?? 0);

    // 1. Validação de campos obrigatórios
    if (empty($nome) || empty($comentario) || $nota < 1 || $nota > 5) {
        $_SESSION['avaliacao_erro'] = "Preencha todos os campos corretamente.";
        header("Location: $feedback_page");
        exit();
    }


    // 3. Inserção no Banco de Dados
    try {
        $sql = "INSERT INTO avaliacoes (cliente_id, nome, comentario, nota) 
                VALUES (:cliente_id, :nome, :comentario, :nota)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        $stmt->bindParam(':nota', $nota, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['avaliacao_sucesso'] = "Avaliação enviada com sucesso!";
        } else {
            $_SESSION['avaliacao_erro'] = "Erro ao processar sua avaliação no servidor.";
        }
    } catch (PDOException $e) {
        $_SESSION['avaliacao_erro'] = "Erro no banco de dados: " . $e->getMessage();
    }
}

// Redireciona de volta para a página principal
header("Location: $feedback_page");
exit();
?>