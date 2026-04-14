<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
require_once '../conexão/conexao.php'; 

// Captura os dados
$id_cliente = $_POST['id_cliente'] ?? '';
$nome       = $_POST['nome'] ?? '';
$data       = $_POST['data_agendamento'] ?? '';
$horario    = $_POST['horario'] ?? '';
$servico    = $_POST['nome_servico'] ?? '';
$barbeiro   = $_POST['barbeiro'] ?? ''; 
$telefone   = $_POST['telefone'] ?? '';

// --- VALIDAÇÕES DE TEMPO REAL ---

// Criar objetos de tempo para comparação precisa
$agora = new DateTime(); 
$agendamento = new DateTime($data . ' ' . $horario);
$dia_semana = $agendamento->format('w'); // 0 = Domingo

// 1. Bloquear Domingos
if ($dia_semana == 0) {
    echo "<script>alert('A Barbearia não abre aos domingos!'); window.location.href = '../pagina/index_logado.php';</script>";
    exit;
}

// 2. Bloquear se o momento do agendamento já passou (Data + Hora)
if ($agendamento < $agora) {
    echo "<script>alert('Este horário já passou! Escolha um horário futuro.'); window.location.href = '../pagina/index_logado.php';</script>";
    exit;
}

// 3. Validar intervalo de 25 em 25 minutos
$minutos = (int)$agendamento->format('i');
if ($minutos % 25 !== 0 && $minutos !== 0) {
    echo "<script>alert('Erro: Horários permitidos apenas em intervalos de 25 minutos (ex: :00, :25, :50).'); window.location.href = '../pagina/index_logado.php';</script>";
    exit;
}

// 4. Verificar se o horário já está ocupado no banco
$sql_check = "SELECT id FROM agenda WHERE data_agendamento = :data AND horario = :horario AND status != 'cancelado'";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([':data' => $data, ':horario' => $horario]);

if ($stmt_check->rowCount() > 0) {
    echo "<script>alert('Ops! Este horário já foi reservado por outro cliente.'); window.location.href = '../pagina/index_logado.php#agenda';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Agendamento</title>
    <link rel="stylesheet" href="conf.css">
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>
<section class="secao">
    <div class="conteudo">
        <div class="confirmacao-box">
            <h2>Confirmar Dados</h2>
            <h4>Verifique as informações abaixo:</h4>

            <p><strong>CLIENTE:</strong> <span><?= htmlspecialchars($nome) ?></span></p>
            <p><strong>DATA:</strong> <span><?= date('d/m/Y', strtotime($data)) ?></span></p>
            <p><strong>HORÁRIO:</strong> <span><?= htmlspecialchars($horario) ?></span></p>
            <p><strong>SERVIÇO:</strong> <span><?= htmlspecialchars($servico) ?></span></p>
            <p><strong>BARBEIRO:</strong> <span><?= htmlspecialchars($barbeiro) ?></span></p>
            <p><strong>WHATSAPP:</strong> <span><?= htmlspecialchars($telefone) ?></span></p>

            <div class="confirmacao-botoes">
                <form action="agendar.php" method="POST" style="display: flex; gap: 15px; width: 100%;">
                    <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($id_cliente) ?>">
                    <input type="hidden" name="nome" value="<?= htmlspecialchars($nome) ?>">
                    <input type="hidden" name="data_agendamento" value="<?= htmlspecialchars($data) ?>">
                    <input type="hidden" name="horario" value="<?= htmlspecialchars($horario) ?>">
                    <input type="hidden" name="nome_servico" value="<?= htmlspecialchars($servico) ?>">
                    <input type="hidden" name="barbeiro" value="<?= htmlspecialchars($barbeiro) ?>">
                    <input type="hidden" name="telefone" value="<?= htmlspecialchars($telefone) ?>">

                    <button type="button" class="btn-acao btn-cancelar" onclick="window.location.href='../pagina/index_logado.php'">Cancelar</button>
                    <button type="submit" class="btn-acao btn-confirmar">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</section>
</body>
</html>