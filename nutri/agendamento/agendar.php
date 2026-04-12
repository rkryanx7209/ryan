<?php
require_once '../conexão/conexao.php';
session_start();

// 1. CONFIGURAÇÕES E PROTEÇÃO
date_default_timezone_set('America/Sao_Paulo');

if (empty($_SESSION['cliente_id'])) {
    header("Location: ../acesso/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pagina/index_logado.php");
    exit;
}

// RECEBIMENTO E LIMPEZA DOS DADOS
$id_cliente       = $_SESSION['cliente_id'];
$nome             = htmlspecialchars(trim($_POST['nome'] ?? ''));
$data             = $_POST['data_agendamento'] ?? ''; 
$horario          = $_POST['horario'] ?? '';           
$tipo_atendimento = htmlspecialchars(trim($_POST['tipo_atendimento'] ?? ''));
$telefone         = htmlspecialchars(trim($_POST['telefone'] ?? ''));
$rua              = htmlspecialchars(trim($_POST['rua'] ?? ''));
$bairro           = htmlspecialchars(trim($_POST['bairro'] ?? ''));
$cidade           = htmlspecialchars(trim($_POST['cidade'] ?? ''));

// Captura todos os outros campos do POST para o INSERT
$servico = htmlspecialchars(trim($_POST['nome_servico'] ?? ''));
$idade   = (int)($_POST['idade'] ?? 0);
$genero  = htmlspecialchars(trim($_POST['genero'] ?? ''));
$cep     = htmlspecialchars(trim($_POST['cep'] ?? ''));

try {
    /* ============================================================
       3. REGRAS DE NEGÓCIO (VALIDAÇÕES)
       ============================================================ */
    $erro = null;

    // A. Bloqueio de Finais de Semana (Sábado=6, Domingo=0)
    $dia_semana = date('w', strtotime($data));
    if ($dia_semana == 0 || $dia_semana == 6) {
        $erro = "A Dra. Daniele não realiza atendimentos aos sábados e domingos. Por favor, escolha um dia útil.";
    }

    // B. Verificação de Intervalo de 40 Minutos
    if (!$erro) {
        $novo_ts = strtotime("$data $horario");
        $margem  = 40 * 60; // 40 min em segundos

        $sql_b = "SELECT horario FROM agenda WHERE data_agendamento = :d AND status != 'cancelado'";
        $st_b = $pdo->prepare($sql_b);
        $st_b->execute([':d' => $data]);
        $agendados = $st_b->fetchAll(PDO::FETCH_ASSOC);

        foreach ($agendados as $ag) {
            $check_ts = strtotime("$data " . $ag['horario']);
            if (abs($novo_ts - $check_ts) < $margem) {
                $erro = "Este horário está muito próximo de outro agendamento. É necessário um intervalo de 40 minutos entre as consultas.";
                break;
            }
        }
    }

    // SE HOUVER ERRO, MOSTRA TELA DE AVISO
    if ($erro) {
        ?>
        <!DOCTYPE html>
        <html lang='pt-br'>
        <head>
            <meta charset='UTF-8'>
            <link rel="stylesheet" href="agendar.css">
        </head>
        <body>
            <div class='box box-erro'>
                <div style="font-size: 60px; margin-bottom: 10px;">⚠️</div>
                <h2 class="titulo-erro">Horário Indisponível</h2>
                <p><?php echo $erro; ?></p>
                <a href='javascript:history.back()' class='btn btn-voltar'>VOLTAR E CORRIGIR</a>
            </div>
        </body>
        </html>
        <?php exit;
    }

    /* ============================================================
       4. PERSISTÊNCIA E E-MAIL
       ============================================================ */
    
    // Busca e-mail do cliente
    $st_u = $pdo->prepare("SELECT email FROM clientes WHERE id = :id LIMIT 1");
    $st_u->execute([':id' => $id_cliente]);
    $email_cliente = $st_u->fetch()['email'] ?? 'rkryanx7209@gmail.com';

    // Salva no banco
    $sql_i = "INSERT INTO agenda (nome, data_agendamento, horario, nome_servico, tipo_atendimento, telefone, idade, genero, cep, rua, bairro, cidade, fk_id_cliente, status)
              VALUES (:n, :d, :h, :s, :t, :tel, :i, :g, :cep, :r, :b, :c, :cli, 'pendente')";
    $pdo->prepare($sql_i)->execute([
        ':n'=>$nome, ':d'=>$data, ':h'=>$horario, ':s'=>$servico, ':t'=>$tipo_atendimento, 
        ':tel'=>$telefone, ':i'=>$idade, ':g'=>$genero, ':cep'=>$cep, ':r'=>$rua, ':b'=>$bairro, ':c'=>$cidade, ':cli'=>$id_cliente
    ]);

    // Disparo Resend (API)
    $apiKey = 're_e2pJnoTd_88THTtgyHXpRvy9BkSMdRvJf'; 
    $corpo_email = [
        'from' => 'Daniele França Nutri <onboarding@resend.dev>',
        'to' => [$email_cliente],
        'subject' => 'Agendamento Confirmado - ' . $nome,
        'html' => "<h3>Olá, $nome!</h3><p>Seu agendamento para <strong>".date('d/m/Y',strtotime($data))."</strong> às <strong>".substr($horario,0,5)."</strong> foi realizado!</p>"
    ];
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$apiKey, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($corpo_email));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_exec($ch); curl_close($ch);

    /* ============================================================
       5. WHATSAPP E FINALIZAÇÃO
       ============================================================ */
    $tel_nutri = "5512982155477"; 
    $local = ($tipo_atendimento !== 'Online') ? "\nEndereço: $rua, $bairro" : "\nAtendimento Online";
    $msg_w = "NOVO AGENDAMENTO\n\nPaciente: $nome\nData: ".date('d/m/Y',strtotime($data))."\nHora: ".substr($horario,0,5).$local;
    $url_w = "https://wa.me/".$tel_nutri."?text=".urlencode($msg_w);

    ?>
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Sucesso!</title>
        <link rel="stylesheet" href="agendar.css">
        <link rel="icon" href="../fotos/images-removebg-preview copy.png">
        <script>
            function concluir(url) {
                window.open(url, '_blank'); // Abre Whats em nova aba
                setTimeout(() => { window.location.href = "../pagina/index_logado.php"; }, 2500); // Volta pra home
            }
        </script>
    </head>
    <body>
        <div class='box'>
            <div class='loader'></div>
            <h2>Agendamento Salvo!</h2>
            <p>Tudo pronto, <strong><?php echo explode(' ', $nome)[0]; ?></strong>!<br>Agora avise a Dra. Daniele clicando abaixo.</p>
            <a href="javascript:void(0)" onclick="concluir('<?php echo $url_w; ?>')" class='btn'>ENVIAR PARA WHATSAPP</a>
            <span class="msg-footer">Você voltará ao site automaticamente após o clique.</span>
        </div>
    </body>
    </html>
    <?php

} catch (PDOException $e) {
    echo "<link rel='stylesheet' href='agendar.css'><div class='box box-erro'><h2>Erro Fatal</h2><p>Não conseguimos conectar ao banco de dados.</p></div>";
}
?>