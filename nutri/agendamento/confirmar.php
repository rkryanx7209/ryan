<?php
session_start();

// 1. PRIMEIRO: Incluir a conexão para a variável $pdo existir
require_once '../conexão/conexao.php'; 

// 2. SEGUNDO: Capturar os dados do POST (necessário para a verificação de horário)
$nome             = $_POST['nome'] ?? '';
$data             = $_POST['data_agendamento'] ?? '';
$horario          = $_POST['horario'] ?? '';
$servico          = $_POST['nome_servico'] ?? '';
$tipo_atendimento = $_POST['tipo_atendimento'] ?? ''; 
$telefone         = $_POST['telefone'] ?? '';
$idade            = $_POST['idade'] ?? '';
$genero           = $_POST['genero'] ?? '';

// 3. TERCEIRO: Verificar se o horário já está ocupado
if (!empty($data) && !empty($horario)) {
    $sql_check = "SELECT id_agenda FROM agenda WHERE data_agendamento = :data AND horario = :horario AND status != 'cancelado'";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([':data' => $data, ':horario' => $horario]);

    if ($stmt_check->rowCount() > 0) {
        echo "<script>
            alert('Ops! Este horário já foi reservado por outro paciente. Por favor, escolha outro.');
            window.location.href = '../pagina/index_logado.php#agenda';
        </script>";
        exit;
    }
}

// 4. QUARTO: Verificar login (sua estrutura visual de aviso)
if (!isset($_SESSION['cliente_id'])) {
    echo "
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css'>
    <link rel='icon' href='./fotos/images-removebg-preview copy.png'>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: #1a1a1a; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .aviso-container { background-color: #2a2a2a; padding: 35px 25px; border-radius: 25px; text-align: center; max-width: 380px; width: 90%; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); }
        .aviso-container i.main-icon { font-size: 55px; color: #8fa081; margin-bottom: 20px; display: block; }
        .aviso-container h2 { color: #ffffff; font-size: 20px; margin-bottom: 12px; }
        .aviso-container p { color: #aaaaaa; font-size: 14px; line-height: 1.5; margin-bottom: 25px; }
        .botoes-aviso { display: flex; flex-direction: column; gap: 10px; align-items: center; }
        .btn-pill { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; border-radius: 50px; font-weight: 600; font-size: 13px; text-decoration: none !important; transition: all 0.3s ease; width: 200px; }
        .btn-pill:hover { transform: translateY(-2px); opacity: 0.9; }
        .verde { background-color: #8fa081; color: #ffffff !important; }
        .cinza { background-color: #4a4a4a; color: #ffffff !important; }
        .escuro { background-color: #333333; color: #ffffff !important; }
    </style>

    <div class='aviso-container'>
        <i class='fas fa-user-circle main-icon'></i>
        <h2>Aviso</h2>
        <p>Faça login ou cadastre-se para poder enviar seu agendamento.</p>
        <div class='botoes-aviso'>
            <a href='../acesso/login.php' class='btn-pill verde'><i class='fas fa-sign-in-alt'></i> Fazer Login</a>
            <a href='../acesso/cadrastocliente.php' class='btn-pill cinza'><i class='fas fa-user-plus'></i> Cadastrar-se</a>
            <a href='javascript:history.back()' class='btn-pill escuro'><i class='fas fa-arrow-left'></i> Voltar</a>
        </div>
    </div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pagina/index_logado.php");
    exit;
}

/* VALIDA DATA RETROATIVA */
$hoje = date('Y-m-d');
if ($data < $hoje) {
    echo "<script>alert('Você não pode agendar para uma data que já passou!'); window.location.href = '../pagina/index_logado.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Agendamento</title>
    <link rel="stylesheet" href="conf.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
</head>
<body>

<section class="secao">
    <div class="conteudo">
        <div class="confirmacao-box">
            <h2>Confirmar Agendamento</h2>
            <h4>Deseja confirmar os dados abaixo?</h4>

            <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
            <p><strong>Data:</strong> <?= htmlspecialchars($data) ?></p>
            <p><strong>Horário:</strong> <?= htmlspecialchars($horario) ?></p>
            <p><strong>Local: </strong> <?= htmlspecialchars($tipo_atendimento) ?></p> 
            <p><strong>Serviço: </strong> <?= htmlspecialchars($servico) ?></p>
            <p><strong>Telefone:</strong> <?= htmlspecialchars($telefone) ?></p>
            <p><strong>Idade:</strong> <?= htmlspecialchars($idade) ?></p>
            <p><strong>Gênero:</strong> <?= htmlspecialchars($genero) ?></p>

            <div class="confirmacao-botoes">
                <form action="agendar.php" method="POST">
                    <input type="hidden" name="nome" value="<?= htmlspecialchars($nome) ?>">
                    <input type="hidden" name="data_agendamento" value="<?= htmlspecialchars($data) ?>">
                    <input type="hidden" name="horario" value="<?= htmlspecialchars($horario) ?>">
                    <input type="hidden" name="nome_servico" value="<?= htmlspecialchars($servico) ?>">
                    <input type="hidden" name="tipo_atendimento" value="<?= htmlspecialchars($tipo_atendimento) ?>"> 
                    <input type="hidden" name="telefone" value="<?= htmlspecialchars($telefone) ?>">
                    <input type="hidden" name="idade" value="<?= htmlspecialchars($idade) ?>">
                    <input type="hidden" name="genero" value="<?= htmlspecialchars($genero) ?>">

                    <button type="submit" class="btn-confirmar">Confirmar</button>
                </form>

                <form action="../pagina/index_logado.php" method="get">
                    <button type="submit" class="btn-voltar">Excluir / Voltar</button>
                </form>
            </div>
        </div>
    </div>
</section>

</body>
</html>