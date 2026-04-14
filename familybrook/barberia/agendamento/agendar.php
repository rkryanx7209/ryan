<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
require_once '../conexão/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'] ?? null;
    $nome       = $_POST['nome'] ?? '';
    $telefone   = $_POST['telefone'] ?? '';
    $servico    = $_POST['nome_servico'] ?? '';
    $barbeiro   = $_POST['barbeiro'] ?? '';
    $data       = $_POST['data_agendamento'] ?? '';
    $horario    = $_POST['horario'] ?? '';

    // Configurações do WhatsApp da Barbearia
    $meu_whatsapp = "5512934858110"; 

    $data_br = date('d/m/Y', strtotime($data));
    $agora = new DateTime();
    $agendamento = new DateTime($data . ' ' . $horario);

    if ($agendamento < $agora) {
        echo "<script>alert('O horário escolhido acabou de passar!'); window.location.href = '../pagina/index_logado.php';</script>";
        exit;
    }

    try {
        // 1. VERIFICA SE O HORÁRIO AINDA ESTÁ DISPONÍVEL
        $sql_check = "SELECT id FROM agenda WHERE data_agendamento = :data AND horario = :horario AND status != 'cancelado'";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([':data' => $data, ':horario' => $horario]);

        if ($stmt_check->rowCount() > 0) {
             echo "<script>alert('Este horário foi ocupado por outro cliente!'); window.location.href = '../pagina/index_logado.php';</script>";
             exit;
        }

        // 2. SALVA O AGENDAMENTO NO BANCO DE DADOS
        $sql = "INSERT INTO agenda (id_cliente, cliente_nome, telefone, servico_nome, barbeiro_nome, data_agendamento, horario, status) 
                VALUES (:id_cliente, :nome, :telefone, :servico, :barbeiro, :data, :horario, 'confirmado')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_cliente' => $id_cliente,
            ':nome'        => $nome,
            ':telefone'    => $telefone,
            ':servico'     => $servico,
            ':barbeiro'    => $barbeiro,
            ':data'        => $data,
            ':horario'     => $horario
        ]);

        // 3. ENVIA E-MAIL DE CONFIRMAÇÃO (RESEND)
        $stmt_email = $pdo->prepare("SELECT email FROM clientes WHERE id = ?");
        $stmt_email->execute([$id_cliente]);
        $email_cliente = $stmt_email->fetchColumn();

        if ($email_cliente) {
            $apiKey = 're_3qV6TRcC_EoVjrZv9TpWbTT3efJTSWxwu'; 
            $postData = [
                'from' => 'Family Brook <onboarding@resend.dev>',
                'to' => [$email_cliente],
                'subject' => "✅ Agendamento Confirmado - $data_br",
                'html' => "<h2>Olá $nome, seu horário na Family Brook está reservado!</h2>
                           <p><strong>Serviço:</strong> $servico</p>
                           <p><strong>Barbeiro:</strong> $barbeiro</p>
                           <p><strong>Data:</strong> $data_br às $horario</p>
                           <p>Até logo!</p>"
            ];
            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer '.$apiKey, 'Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode($postData)
            ]);
            curl_exec($ch);
            curl_close($ch);
        }

        // 4. MENSAGEM DO WHATSAPP OTIMIZADA
        $msg_whatsapp = "*NOVO AGENDAMENTO CONFIRMADO* ✅%0A" .
                        "------------------------------------%0A" .
                        " *Cliente: $nome%0A" .
                        " *Contato: $telefone%0A" .
                        " *Serviço: $servico%0A" .
                        " *Data: $data_br%0A" .
                        " *Horário: $horario%0A" .
                        " *Barbeiro: $barbeiro%0A" .
                        "------------------------------------%0A" .
                        "_Enviado via Site Family Brook_";

        // 5. TELA DE TRANSIÇÃO
        ?>
        <!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Agendamento concluído</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
             <link rel="stylesheet" href="age.css">
           <link rel="icon" href="/barberia/fotos/icon.jpeg">
        </head>
        <body>

        <div class="card">
            <div class="icon"><i class="fas fa-calendar-check"></i></div>
            <h2>Agendamento Realizado!</h2>
            <p>Seu horário foi reservado. Clique abaixo para enviar a confirmação para nossa equipe no WhatsApp.</p>
            
            <a href="https://wa.me/<?= $meu_whatsapp ?>?text=<?= $msg_whatsapp ?>" 
               target="_blank" 
               id="finalizarBtn"
               class="btn-zap">
                <i class="fab fa-whatsapp"></i> Confirmar no WhatsApp
            </a>
        </div>

        <script>
            document.getElementById('finalizarBtn').addEventListener('click', function() {
                setTimeout(function() {
                    window.location.href = "../pagina/index_logado.php";
                }, 1500);
            });
        </script>

        </body>
        </html>
        <?php
        exit;

    } catch (Exception $e) {
        echo "<script>alert('Erro ao processar agendamento.'); window.history.back();</script>";
    }
} else {
    header("Location: ../pagina/index_logado.php");
    exit;
}