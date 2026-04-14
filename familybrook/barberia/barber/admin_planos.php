<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. VERIFICAÇÃO DE SEGURANÇA
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../acesso/login.php");
    exit();
}

require_once '../conexão/conexao.php';

// 2. BUSCA DE TODOS OS CLIENTES
try {
    $hoje = date('Y-m-d');
    $sql = "SELECT id, nome, email, telefone, assinante, data_vencimento FROM clientes ORDER BY nome ASC";
    $stmt = $pdo->query($sql);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar clientes: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes | Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="admin_planos.css"> 
    
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>

<div class="container-painel">
    <h1><i class="fas fa-users"></i> Gestão de Contatos</h1>

    <div class="busca-box">
        <input type="text" id="inputBusca" placeholder="🔍 Pesquisar por nome..." onkeyup="filtrar()">
    </div>

    <div class="grade-clientes">
        <?php foreach ($clientes as $c): 
            $planoAtivo = ($c['assinante'] == 'Sim' && !empty($c['data_vencimento']) && $c['data_vencimento'] >= $hoje);
            $telefoneLimpo = preg_replace('/[^0-9]/', '', $c['telefone']);

            // Mensagem Inteligente
            $msgTexto = $planoAtivo 
                ? "Olá " . $c['nome'] . "! 💈 Passando para desejar um ótimo dia na Family Brook!" 
                : "Olá " . $c['nome'] . "! 💈 Notamos que seu plano venceu. Bora renovar?";
            
            $linkWhats = "https://wa.me/55" . $telefoneLimpo . "?text=" . urlencode($msgTexto);
        ?>
            <div class="card-cliente item-avalia <?= $planoAtivo ? 'card-bom' : 'card-ruim' ?>">
                <div>
                    <div class="info-linha nome-cliente">
                        <?= htmlspecialchars($c['nome']) ?>
                    </div>
                    
                    <div class="info-linha">
                        <i class="fas fa-phone"></i> <?= htmlspecialchars($c['telefone']) ?>
                    </div>

                    <div class="info-linha">
                        <i class="fas fa-star"></i> Status: 
                        <span class="badge <?= $planoAtivo ? 'badge-sim' : 'badge-nao' ?>">
                            <?= $planoAtivo ? 'ATIVO' : 'INATIVO' ?>
                        </span>
                    </div>

                    <div class="info-linha">
                        <i class="fas fa-calendar-alt"></i> Vencimento: 
                        <?= $c['data_vencimento'] ? date('d/m/Y', strtotime($c['data_vencimento'])) : '---' ?>
                    </div>
                </div>

                <a href="<?= $linkWhats ?>" target="_blank" class="btn-whatsapp">
                    <i class="fab fa-whatsapp"></i> MANDAR WHATSAPP
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="footer-avaliacoes">
        <a href="painel.php" class="btn-pill">
            <i class="fas fa-arrow-left"></i> VOLTAR
        </a>
    </div>
</div>

<script>
function filtrar() {
    let filtro = document.getElementById('inputBusca').value.toLowerCase();
    let cards = document.getElementsByClassName('item-avalia');
    for (let i = 0; i < cards.length; i++) {
        let nome = cards[i].querySelector('.nome-cliente').innerText.toLowerCase();
        cards[i].style.display = nome.includes(filtro) ? "" : "none";
    }
}
</script>
</body>
</html>