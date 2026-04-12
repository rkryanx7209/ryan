<?php
require_once '../conexão/conexao.php'; 

$cliente_id = $_GET['cliente_id'] ?? null;
if (!$cliente_id) die("Cliente inválido");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cafe_manha = $_POST['cafe_manha'];
    $almoco = $_POST['almoco'];
    $cafe_tarde = $_POST['cafe_tarde'];
    $janta = $_POST['janta'];

    // Apaga dieta antiga e insere nova (simples)
    $pdo->prepare("DELETE FROM dietas WHERE cliente_id = ?")->execute([$cliente_id]);

    $sql = "INSERT INTO dietas (cliente_id, cafe_manha, almoco, cafe_tarde, janta)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cliente_id, $cafe_manha, $almoco, $cafe_tarde, $janta]);

    echo "Dieta salva com sucesso!";
}
?>

<h2>Cadastrar Dieta</h2>

<form method="POST">
    <textarea name="cafe_manha" placeholder="Café da manhã" required></textarea><br>
    <textarea name="almoco" placeholder="Almoço" required></textarea><br>
    <textarea name="cafe_tarde" placeholder="Café da tarde" required></textarea><br>
    <textarea name="janta" placeholder="Janta" required></textarea><br>
    <button type="submit">Salvar dieta</button>
</form>
