<?php  
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// 1. SEGURANÇA
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') { 
    header("Location: ../acesso/login.php"); 
    exit(); 
}

require_once '../conexão/conexao.php';

/* ===========================================================
   LÓGICA DE AUTO-CONCLUSÃO (20 MINUTOS DE TOLERÂNCIA)
   =========================================================== */
// Define o fuso horário para garantir precisão
date_default_timezone_set('America/Sao_Paulo');

$agora = date('Y-m-d H:i:s');
$limite = date('Y-m-d H:i:s', strtotime('-20 minutes'));

// Remove agendamentos que já passaram do horário marcado + 20 minutos
$sql_auto_clean = "DELETE FROM agenda WHERE CONCAT(data_agendamento, ' ', horario) < :limite";
$stmt_clean = $pdo->prepare($sql_auto_clean);
$stmt_clean->execute([':limite' => $limite]);


// 2. LÓGICA PARA EXCLUSÃO MANUAL (Botão Concluir)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $sql_del = "DELETE FROM agenda WHERE id = :id";
    $stmt = $pdo->prepare($sql_del);
    $stmt->execute([':id' => (int)$_POST['excluir_id']]);
    header("Location: agendamentos.php");
    exit();
}

// 3. BUSCA DE DADOS PARA O PAINEL
$hoje = date('Y-m-d');

// Busca agendamentos restantes
$sql_agenda = "SELECT * FROM agenda ORDER BY data_agendamento ASC, horario ASC";
$agendamentos = $pdo->query($sql_agenda)->fetchAll(PDO::FETCH_ASSOC);

// Contador de cortes para o dia de hoje
$sql_hoje = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE data_agendamento = :hoje");
$sql_hoje->execute([':hoje' => $hoje]);
$total_hoje = $sql_hoje->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda | Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="agenda.css">
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>

<div class="container-painel">
    <div class="main-card-wrapper">
        
        <header class="header-central">
            <h2><i class="fas fa-calendar-alt"></i> Agenda de Clientes</h2>
            <div class="card-stats">
                <span>Cortes Restantes Hoje</span>
                <strong><?= $total_hoje ?></strong>
            </div>
        </header>

        <div class="busca-container">
            <i class="fas fa-search"></i>
            <input type="text" id="inputBusca" class="busca-input" placeholder="Pesquisar cliente por nome..." onkeyup="filtrarLista()">
        </div>

        <div class="lista-scroll" id="listaAgenda">
            <?php if (count($agendamentos) > 0): ?>
                <?php foreach ($agendamentos as $row): 
                    $data_formatada = date('d/m', strtotime($row['data_agendamento']));
                    $hora_formatada = substr($row['horario'], 0, 5);
                    
                    // Link Dinâmico para WhatsApp
                    $tel_limpo = preg_replace('/\D/', '', $row['telefone']);
                    $primeiro_nome = explode(' ', $row['cliente_nome'])[0];
                    $texto_wa = "Olá $primeiro_nome, aqui é da Family Brook! Confirmamos seu horário de {$row['servico_nome']} às $hora_formatada?";
                    $url_wa = "https://wa.me/55" . $tel_limpo . "?text=" . urlencode($texto_wa);
                ?>
                    <div class="card-agendamento">
                        <div class="info-corpo">
                            <h3 class="paciente-nome"><?= htmlspecialchars($row['cliente_nome']) ?></h3>
                            
                            <div class="info-item">
                                <i class="far fa-clock"></i> 
                                <span><?= $data_formatada ?> às <strong><?= $hora_formatada ?></strong></span>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-cut"></i> 
                                <span><?= htmlspecialchars($row['servico_nome']) ?></span>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-user-tie"></i> 
                                <span>Barbeiro: <?= htmlspecialchars($row['barbeiro_nome']) ?></span>
                            </div>
                        </div>

                        <div class="acoes-agendamento">
                            <a href="<?= $url_wa ?>" target="_blank" class="btn-whatsapp">
                                <i class="fab fa-whatsapp"></i> AVISAR
                            </a>

                            <form method="POST">
                                <input type="hidden" name="excluir_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn-concluir" onclick="return confirm('Confirmar conclusão do atendimento?')">
                                    <i class="fas fa-check"></i> CONCLUIR
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="vazio">
                    <i class="fas fa-calendar-check fa-3x"></i>
                    <p>Tudo limpo! Nenhum agendamento pendente.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer-btn">
            <a href="painel.php" class="btn-pill">
                <i class="fas fa-arrow-left"></i> VOLTAR AO PAINEL
            </a>
        </div>
    </div>
</div>

<script>
function filtrarLista() {
    let input = document.getElementById('inputBusca').value.toLowerCase();
    let cards = document.getElementsByClassName('card-agendamento');
    
    for (let i = 0; i < cards.length; i++) {
        let nome = cards[i].querySelector('.paciente-nome').innerText.toLowerCase();
        cards[i].style.display = nome.includes(input) ? "flex" : "none";
    }
}
</script>

</body>
</html>