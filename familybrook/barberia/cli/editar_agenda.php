<?php
require_once '../conexão/conexao.php';
session_start();

if (empty($_SESSION['usuario_id'])) {
    header("Location: ../acesso/login.php");
    exit;
}

$id_cliente = $_SESSION['usuario_id'];
$id_agenda = $_GET['id'] ?? 0;

$sql = "SELECT * FROM agenda WHERE id = :id AND id_cliente = :id_cliente";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_agenda, ':id_cliente' => $id_cliente]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    header("Location: minha_agenda.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servico = $_POST['servico_nome'];
    $data = $_POST['data_agendamento'];
    $hora = $_POST['horario'];

    $sql = "UPDATE agenda 
            SET servico_nome = :servico, data_agendamento = :data, horario = :hora
            WHERE id = :id AND id_cliente = :id_cliente";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':servico' => $servico,
        ':data' => $data,
        ':hora' => $hora,
        ':id' => $id_agenda,
        ':id_cliente' => $id_cliente
    ]);

    header("Location: minha_agenda.php?sucesso=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horário | Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="editar_agenda.css">
        <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>
    <div class="confirmacao-box">
        <h2><i class="fas fa-cut"></i> Alterar Agendamento</h2>
        
        <p style="text-align:center; color:#fff; margin-bottom:20px; font-size:14px;">
            Profissional selecionado: <strong style="color:#c5a059;"><?= htmlspecialchars($agendamento['barbeiro_nome']) ?></strong>
        </p>

        <form method="POST">
            <div class="campo-edicao">
                <label><i class="fas fa-scissors"></i> Serviço</label>
                <select name="servico_nome" class="select-editar" required>
                    <option value="Cabelo e barba" <?= ($agendamento['servico_nome'] == 'Cabelo e barba') ? 'selected' : '' ?>>Cabelo e barba</option>
                    <option value="Limpeza de pele" <?= ($agendamento['servico_nome'] == 'Limpeza de pele') ? 'selected' : '' ?>>Limpeza de pele</option>
                    <option value="Corte Social" <?= ($agendamento['servico_nome'] == 'Corte Social') ? 'selected' : '' ?>>Corte Social</option>
                    <option value="Degradê" <?= ($agendamento['servico_nome'] == 'Degradê') ? 'selected' : '' ?>>Degradê</option>
                </select>
            </div>

            <div class="row-dupla">
                <div class="campo-edicao">
                    <label><i class="fas fa-calendar-alt"></i> Data</label>
                    <input type="date" name="data_agendamento" value="<?= $agendamento['data_agendamento'] ?>" required>
                </div>
                <div class="campo-edicao">
                    <label><i class="fas fa-clock"></i> Horário</label>
                    <input type="time" name="horario" value="<?= $agendamento['horario'] ?>" required>
                </div>
            </div>

            <div class="confirmacao-botoes">
                <button type="submit" class="btn-pill btn-confirmar">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
                <a href="minha_agenda.php" class="btn-pill btn-voltar" style="text-decoration: none;">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>