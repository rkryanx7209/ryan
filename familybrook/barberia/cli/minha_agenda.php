<?php
require_once '../conexão/conexao.php';
session_start();

if (empty($_SESSION['usuario_id'])) {
    header("Location: ../acesso/login.php");
    exit;
}

$cliente_id = $_SESSION['usuario_id'];

$sql = "SELECT * FROM agenda WHERE id_cliente = :cliente ORDER BY data_agendamento ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':cliente' => $cliente_id]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Horários | Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="minha_agenda.css?v=1.1">
    <link rel="icon" href="../fotos/icon.jpeg">
</head>
<body>

    <div class="header">
        <i class="fas fa-calendar-check"></i>
        <h1>Meus Horários</h1>
    </div>

    <div class="container">
        <?php if (empty($agendamentos)): ?>
            <div class="card-agenda" style="text-align: center;">
                <p>Nenhum agendamento encontrado.</p>
            </div>
        <?php else: ?>
            <?php foreach ($agendamentos as $row): ?>
                <div class="card-agenda">
                    <p>
                        <strong>Status:</strong> 
                        <span class="<?= ($row['status'] == 'atendido') ? 'status-atendido' : 'status-pendente' ?>">
                            <?= strtoupper($row['status'] ?? 'PENDENTE') ?>
                        </span>
                    </p>
                    
                    <p><i class="fas fa-cut"></i> <strong>Serviço:</strong> <?= htmlspecialchars($row['servico_nome']) ?></p>
                    <p><i class="fas fa-user-tie"></i> <strong>Profissional:</strong> <?= htmlspecialchars($row['barbeiro_nome']) ?></p>
                    <p><i class="fas fa-clock"></i> <strong>Data e Hora:</strong> <?= date('d/m', strtotime($row['data_agendamento'])) ?> às <?= substr($row['horario'], 0, 5) ?></p>

                    <div class="container-acoes">
                        <a href="editar_agenda.php?id=<?= $row['id'] ?>" class="btn-pill verde-musgo">
                            <i class="fas fa-edit"></i> EDITAR
                        </a>

                        <form action="excluir_agenda.php" method="POST" class="form-excluir">
                            <input type="hidden" name="id_agenda" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn-pill vermelho-cancelar" onclick="return confirm('Deseja realmente cancelar este horário?')">
                                <i class="fas fa-trash"></i> CANCELAR
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
         
        <div class="container-botoes-final">
            <a href="../pagina/index_logado.php#agenda" class="btn-pill verde-musgo">
                <i class="fas fa-calendar-alt"></i> AGENDAR NOVAMENTE
            </a>
            <a href="../pagina/painel_cli.php" class="btn-pill outline-gold">
                <i class="fas fa-arrow-left"></i> VOLTAR
            </a>
        </div>
    </div>

</body>
</html>