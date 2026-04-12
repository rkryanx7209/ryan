<?php
require_once '../conexão/conexao.php';
session_start();

if (empty($_SESSION['cliente_id'])) {
    header("Location: ../acesso/login.php");
    exit;
}

$id_cliente = $_SESSION['cliente_id'];
$id = $_GET['id'] ?? 0;

/* BUSCA OS DADOS ATUAIS */
$sql = "SELECT * FROM agenda WHERE id_agenda = :id AND fk_id_cliente = :cliente";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id, ':cliente' => $id_cliente]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    header("Location: minha_agenda.php");
    exit;
}

/* PROCESSA A ATUALIZAÇÃO */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data_agendamento'];
    $hora = $_POST['horario'];
    $servico = $_POST['nome_servico'];
    $tipo = $_POST['tipo_atendimento'];
    $cep = $_POST['cep'] ?? null;
    $rua = $_POST['rua'] ?? null;
    $bairro = $_POST['bairro'] ?? null;
    $cidade = $_POST['cidade'] ?? null;

    $sql = "UPDATE agenda 
            SET data_agendamento = :data, horario = :hora, nome_servico = :servico, tipo_atendimento = :tipo,
                cep = :cep, rua = :rua, bairro = :bairro, cidade = :cidade
            WHERE id_agenda = :id AND fk_id_cliente = :cliente";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':data' => $data, ':hora' => $hora, ':servico' => $servico, ':tipo' => $tipo,
        ':cep' => $cep, ':rua' => $rua, ':bairro' => $bairro, ':cidade' => $cidade,
        ':id' => $id, ':cliente' => $id_cliente
    ]);

    header("Location: minha_agenda.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
    <link rel="stylesheet" href="editar_agenda.css">
</head>
<body>
    <section class="secao">
        <div class="confirmacao-box">
            <h2><i class="fas fa-edit"></i> Editar Consulta</h2>
            
            <form method="POST">
                <div class="row-dupla">
                    <div class="campo-edicao">
                        <label><i class="fas fa-calendar-day"></i> Nova Data</label>
                        <input type="date" name="data_agendamento" value="<?= $agendamento['data_agendamento'] ?>" required>
                    </div>
                    <div class="campo-edicao">
                        <label><i class="fas fa-clock"></i> Novo Horário</label>
                        <input type="time" name="horario" value="<?= $agendamento['horario'] ?>" required>
                    </div>
                </div>

                <div class="campo-edicao">
                    <label><i class="fas fa-hand-holding-medical"></i> Serviço</label>
                    <select name="nome_servico" class="select-editar" required>
                        <option value="Emagrecimento" <?= ($agendamento['nome_servico'] == 'Emagrecimento') ? 'selected' : '' ?>>Emagrecimento</option>
                        <option value="Reeducação alimentar" <?= ($agendamento['nome_servico'] == 'Reeducação alimentar') ? 'selected' : '' ?>>Reeducação alimentar</option>
                        <option value="Tratamento nutricional" <?= ($agendamento['nome_servico'] == 'Tratamento nutricional') ? 'selected' : '' ?>>Tratamento nutricional</option>
                        <option value="Qualidade de vida" <?= ($agendamento['nome_servico'] == 'Qualidade de vida') ? 'selected' : '' ?>>Qualidade de vida</option>
                    </select>
                </div>

                <div class="campo-edicao">
                    <label><i class="fas fa-map-marker-alt"></i> Local do Atendimento</label>
                    <select name="tipo_atendimento" id="tipo_atendimento" required class="select-editar">
                        <option value="Online" <?= ($agendamento['tipo_atendimento'] == 'Online') ? 'selected' : '' ?>>Atendimento Online</option>
                        <option value="Clinica" <?= ($agendamento['tipo_atendimento'] == 'Clinica') ? 'selected' : '' ?>>Presencial (Clínica)</option>
                        <option value="Domiciliar" <?= ($agendamento['tipo_atendimento'] == 'Domiciliar') ? 'selected' : '' ?>>Domiciliar</option>
                    </select>
                </div>

                <div id="sessao-endereco" style="<?= ($agendamento['tipo_atendimento'] == 'Domiciliar') ? 'display:block' : 'none' ?>">
                    <div class="campo-edicao">
                        <label><i class="fas fa-search-location"></i> CEP</label>
                        <input type="text" name="cep" id="cep" value="<?= $agendamento['cep'] ?>" maxlength="8">
                    </div>
                    <div class="campo-edicao">
                        <label><i class="fas fa-road"></i> Rua</label>
                        <input type="text" name="rua" id="rua" class="readonly-style" value="<?= $agendamento['rua'] ?>" readonly>
                    </div>
                    <div class="row-dupla">
                        <div class="campo-edicao">
                            <label><i class="fas fa-map-signs"></i> Bairro</label>
                            <input type="text" name="bairro" id="bairro" class="readonly-style" value="<?= $agendamento['bairro'] ?>" readonly>
                        </div>
                        <div class="campo-edicao">
                            <label><i class="fas fa-city"></i> Cidade</label>
                            <input type="text" name="cidade" id="cidade" class="readonly-style" value="<?= $agendamento['cidade'] ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="confirmacao-botoes">
                    <button type="submit" class="btn-pill btn-confirmar">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="minha_agenda.php" class="btn-pill btn-voltar">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.getElementById('tipo_atendimento').addEventListener('change', function() {
            document.getElementById('sessao-endereco').style.display = (this.value === 'Domiciliar') ? 'block' : 'none';
        });

        document.getElementById('cep').addEventListener('input', function() {
            let cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://brasilapi.com.br/api/cep/v2/${cep}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.errors) {
                            document.getElementById('rua').value = data.street || '';
                            document.getElementById('bairro').value = data.neighborhood || '';
                            document.getElementById('cidade').value = data.city || '';
                        }
                    });
            }
        });
    </script>
</body>
</html>