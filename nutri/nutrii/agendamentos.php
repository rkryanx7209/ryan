<?php  
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: ../acesso/login.php"); exit(); }

require_once '../conexão/conexao.php';

// Lógica de Exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $sql_del = "DELETE FROM agenda WHERE id_agenda = :id AND status = 'atendido'";
    $pdo->prepare($sql_del)->execute([':id' => (int)$_POST['excluir_id']]);
    header("Location: agendamentos.php");
    exit();
}

$hoje = date('Y-m-d');

// BUSCA PENDENTES
$sql_pendentes = "SELECT a.*, c.* FROM agenda a 
                  LEFT JOIN clientes c ON a.nome COLLATE utf8mb4_general_ci = c.nome COLLATE utf8mb4_general_ci
                  WHERE a.status = 'pendente' 
                  ORDER BY a.data_agendamento ASC, a.horario ASC";
$pendentes = $pdo->query($sql_pendentes)->fetchAll(PDO::FETCH_ASSOC);

$atendidos = $pdo->query("SELECT * FROM agenda WHERE status = 'atendido' ORDER BY data_agendamento DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);

$cont_hoje_sql = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE data_agendamento = :hoje AND status = 'pendente'");
$cont_hoje_sql->execute([':hoje' => $hoje]);
$total_hoje = $cont_hoje_sql->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Agenda | Nutri</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="agenda.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
    
</head>
<body>

<div class="container-painel">
    
    <div class="main-card-wrapper">
        
        <div class="header-central">
            <h2><i class="fas fa-calendar-check" style="color: #4CAF50;"></i> Central de Consultas</h2>
            <div class="card-stats" style="display: inline-block; width: auto; background: rgba(76, 175, 80, 0.1); border: 1px solid #4CAF50;">
                <span>Pendentes Hoje: </span>
                <strong style="color: #4CAF50;"><?= $total_hoje ?></strong>
            </div>
        </div>

        <div class="flex-agendamentos">
            <div class="secao-container">
                <h3>PRÓXIMOS</h3>
                <div class="busca-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscaP" class="busca-input" placeholder="Filtrar paciente..." onkeyup="filtrar('buscaP', 'card-p', '.paciente-nome')">
                </div>
                
                <div class="lista-scroll">
                    <?php foreach ($pendentes as $a): 
                        $tipo = trim($a['tipo_atendimento']);
                        $is_online = ($tipo === 'Online');
                        $show_maps = ($tipo === 'Domiciliar' || $tipo === 'Clinica');
                        
                        // Lógica de endereço Rua, Número e Bairro
                        $local_exibir = "";
                        if (!empty($a['rua'])) {
                            $local_exibir = $a['rua'];
                            if (!empty($a['numero'])) $local_exibir .= ", " . $a['numero'];
                            if (!empty($a['bairro'])) $local_exibir .= " - " . $a['bairro'];
                        } elseif (!empty($a['endereco'])) {
                            $local_exibir = $a['endereco'];
                        } else {
                            $local_exibir = "Endereço não localizado";
                        }

                        // Mensagens Profissionais
                        $telefone_limpo = preg_replace('/\D/', '', $a['telefone']);
                        $data_f = date('d/m/Y', strtotime($a['data_agendamento']));
                        $hora_f = substr($a['horario'], 0, 5);
                        $nome_p = explode(' ', trim($a['nome']))[0]; 

                        if ($is_online) {
                            $msg = "Olá, $nome_p. Tudo bem?\n\nEstou disponível para iniciarmos sua *consulta online via videochamada aqui pelo WhatsApp*. Podemos começar?";
                        } elseif ($tipo === 'Domiciliar') {
                            $msg = "Olá, $nome_p. Como vai?\n\nPassando para confirmar nosso *atendimento domiciliar* agendado para o dia $data_f às $hora_f. Confirmada a sua disponibilidade?";
                        } else { 
                            $msg = "Olá, $nome_p. Tudo bem?\n\nGostaria de confirmar sua *consulta em nossa clínica* agendada para o dia $data_f às $hora_f. Podemos confirmar?";
                        }
                        
                        $link_whatsapp = "https://wa.me/55" . $telefone_limpo . "?text=" . urlencode($msg);
                    ?>
                    <div class="card-agendamento card-p">
                        <strong class="paciente-nome"><?= htmlspecialchars($a['nome']) ?></strong> 
                        
                        <div class="info-item"><i class="fas fa-calendar"></i> <?= $data_f ?> | <i class="fas fa-clock"></i> <?= $hora_f ?></div>
                        <div class="info-item">
                            <i class="<?= ($tipo === 'Online') ? 'fas fa-video' : (($tipo === 'Domiciliar') ? 'fas fa-car' : 'fas fa-building') ?>"></i> 
                            <?= htmlspecialchars($tipo) ?>
                        </div>

                        <?php if ($show_maps): ?>
                            <div class="endereco-box">
                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($local_exibir) ?>
                                <br>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($local_exibir) ?>" target="_blank" class="btn-mapa">
                                    <i class="fas fa-directions"></i> Ver Rota
                                </a>
                            </div>
                        <?php endif; ?>

                        <a href="<?= $link_whatsapp ?>" target="_blank" class="btn-whatsapp <?= $is_online ? 'btn-azul' : 'btn-verde' ?>">
                            <i class="<?= $is_online ? 'fas fa-video' : 'fab fa-whatsapp' ?>"></i>
                            <?= $is_online ? 'Iniciar Videochamada' : 'Confirmar Presença' ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="secao-container">
                <h3>HISTÓRICO</h3>
                <div class="busca-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscaH" class="busca-input" placeholder="Filtrar histórico..." onkeyup="filtrar('buscaH', 'card-h', '.paciente-nome-hist')">
                </div>

                <div class="lista-scroll">
                    <?php foreach ($atendidos as $a): ?>
                    <div class="card-agendamento card-h">
                        <strong class="paciente-nome-hist"><?= htmlspecialchars($a['nome']) ?></strong> 
                        <div class="info-item"><i class="fas fa-calendar-check"></i> <?= date('d/m/Y', strtotime($a['data_agendamento'])) ?></div>
                        
                        <a href="https://wa.me/55<?= preg_replace('/\D/', '', $a['telefone']) ?>?text=Olá, passando para saber como você está se sentindo com o plano alimentar!" target="_blank" class="btn-acompanhamento">
                            <i class="fas fa-comment-dots"></i> Pós-Consulta
                        </a>

                        <form method="POST" onsubmit="return confirm('Excluir este registro?');">
                            <input type="hidden" name="excluir_id" value="<?= $a['id_agenda'] ?>">
                            <button type="submit" class="btn-excluir-txt"><i class="fas fa-trash-alt"></i> Excluir</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div> <div class="footer-btn">
        <a href="painel.php" class="btn-pill">
            <i class="fas fa-arrow-left"></i> Voltar ao Painel Principal
        </a>
    </div>
</div>
      
<script>
function filtrar(inputId, cardClass, nameSelector) {
    let input = document.getElementById(inputId).value.toLowerCase();
    let cards = document.getElementsByClassName(cardClass);
    for (let i = 0; i < cards.length; i++) {
        let nome = cards[i].querySelector(nameSelector).innerText.toLowerCase();
        cards[i].style.display = nome.includes(input) ? "" : "none";
    }
}
</script>
</body>
</html>