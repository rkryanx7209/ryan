<?php
require_once '../conexão/conexao.php';
session_start();

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Puxa o nome diretamente da sessão de login
    $nome_cliente = $_SESSION['usuario_nome'] ?? 'Cliente'; 
    $nota         = (int)($_POST['nota'] ?? 0);
    $comentario   = trim($_POST['comentario'] ?? '');

    // Validação de segurança
    if ($nota < 1 || empty($comentario)) {
        echo "<script>alert('Por favor, preencha todos os campos!'); window.history.back();</script>";
        exit;
    }

    try {
        // O SQL agora inclui o nome_cliente
        $sql = "INSERT INTO avaliacoes (nome_cliente, nota, comentario) 
                VALUES (:nome, :nota, :comentario)";
        
        $stmt = $pdo->prepare($sql);
        
        // Vinculamos as variáveis aos parâmetros do SQL
        $stmt->bindParam(':nome', $nome_cliente);
        $stmt->bindParam(':nota', $nota);
        $stmt->bindParam(':comentario', $comentario);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Obrigado, $nome_cliente! Sua avaliação foi enviada.'); 
                    window.location.href='../pagina/index_logado.php';
                  </script>";
        }
    } catch (PDOException $e) {
        // Caso ocorra erro de coluna ou tabela, ele avisará aqui
        echo "Erro ao salvar no banco: " . $e->getMessage();
    }
} else {
    // Se tentarem acessar o arquivo sem enviar o formulário, volta para a index
    header("Location: ../pagina/index_logado.php");
    exit;
}
?>