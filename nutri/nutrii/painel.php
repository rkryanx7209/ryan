<?php  
// 1. Configurações de Sessão e Segurança
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../acesso/login.php");
    exit();
}

require_once '../conexão/conexao.php';

// 2. Variáveis de Tempo
$hoje = date('Y-m-d');
$agora = date('H:i:s');
$vinte_minutos_depois = date('H:i:s', strtotime('+20 minutes'));

/* ===========================================================
   3. BUSCA DE ESTATÍSTICAS
   =========================================================== */
// Total de pacientes pendentes para hoje
$stmt_hoje = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE data_agendamento = :hoje AND status = 'pendente'");
$stmt_hoje->execute([':hoje' => $hoje]);
$total_hoje = $stmt_hoje->fetchColumn();

// Total de clientes na base de dados
$total_clientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();

/* ===========================================================
   4. LÓGICA DA DICA DE OURO: ALERTA DE CONSULTA ONLINE PRÓXIMA
   =========================================================== */
$stmt_alerta = $pdo->prepare("
    SELECT COUNT(*) FROM agenda 
    WHERE data_agendamento = :hoje 
    AND tipo_atendimento = 'Online' 
    AND status = 'pendente' 
    AND horario BETWEEN :agora AND :depois
");
$stmt_alerta->execute([
    ':hoje' => $hoje,
    ':agora' => $agora,
    ':depois' => $vinte_minutos_depois
]);
$tem_consulta_proxima = $stmt_alerta->fetchColumn() > 0;

$nome_admin = isset($_SESSION['admin_nome']) ? $_SESSION['admin_nome'] : "Dra. Daniele";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | Dra. Daniele França</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="painel.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
    
    <style>
        /* ESTILOS EXCLUSIVOS DO ALERTA INTELIGENTE */
        .resumo-estatisticas {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
            width: 100%;
        }
        .card-mini {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            backdrop-filter: blur(5px);
        }
        .card-mini span { display: block; font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 1px; }
        .card-mini strong { font-size: 22px; color: #8fa081; }

        /* Efeito de brilho para consulta online próxima */
        .alerta-pulsante {
            border: 1px solid #007bff !important;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.4);
            animation: pulsar 1.5s infinite;
            position: relative;
        }

        @keyframes pulsar {
            0% { transform: scale(1); }
            50% { transform: scale(1.01); box-shadow: 0 0 30px rgba(0, 123, 255, 0.6); }
            100% { transform: scale(1); }
        }

        .badge-alerta {
            position: absolute;
            top: -10px;
            right: 10px;
            background: #007bff;
            color: white;
            font-size: 9px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

    <div class="container-painel">
        <i class="fas fa-user-shield main-icon"></i>
        <h2>Bem-vinda, <?php echo htmlspecialchars($nome_admin); ?>!</h2>
        <p>Gestão de Consultório e Pacientes</p>

        <div class="resumo-estatisticas">
            <div class="card-mini">
                <span>Pacientes Hoje</span>
                <strong><?= $total_hoje ?></strong>
            </div>
            <div class="card-mini">
                <span>Total Clientes</span>
                <strong><?= $total_clientes ?></strong>
            </div>
        </div>

        <nav class="menu-painel">
            <a href="agendamentos.php" class="btn-pill verde <?= $tem_consulta_proxima ? 'alerta-pulsante' : '' ?>">
                <i class="fas fa-calendar-alt"></i> 
                <span>Agendamentos</span>
                <?php if ($tem_consulta_proxima): ?>
                    <span class="badge-alerta"><i class="fas fa-video"></i> Aviso Pendente</span>
                <?php endif; ?>
            </a>
            
            <a href="avaliacoes.php" class="btn-pill verde">
                <i class="fas fa-star"></i> 
                <span>Avaliações</span>
            </a>
         
            <a href="admin_clientes.php" class="btn-pill verde">
                <i class="fas fa-apple-whole"></i> 
                <span>Gerenciar Dietas</span>
            </a>

            <a href="../index.html" class="btn-pill vermelho">
                <i class="fas fa-sign-out-alt"></i> 
                <span>Sair do Sistema</span>
            </a>
        </nav>
    </div>

</body>
</html>