<?php
require_once '../conexão/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['id'];
    $nomes = $_POST['nome'];
    $precos = $_POST['preco'];
    $duracoes = $_POST['duracao'];
    $descricoes = $_POST['descricao'];
    $categorias = $_POST['categoria'];

    try {
        $pdo->beginTransaction();
        
        // Query ajustada para as colunas reais: id, categoria, nome, preco, duracao_minutos, descricao
        $sql = "UPDATE servicos SET nome = ?, preco = ?, duracao_minutos = ?, descricao = ?, categoria = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);

        for ($i = 0; $i < count($ids); $i++) {
            $stmt->execute([
                $nomes[$i],
                $precos[$i],
                $duracoes[$i],
                $descricoes[$i],
                $categorias[$i],
                $ids[$i]
            ]);
        }

        $pdo->commit();
        echo "<script>alert('Serviços atualizados com sucesso!'); window.location.href='gerenciar_servicos.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao atualizar banco: " . $e->getMessage());
    }
}