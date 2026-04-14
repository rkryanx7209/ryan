<?php 
session_start();
require_once '../conexão/conexao.php'; 

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../acesso/login.php");
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

// 1. BUSCA DE DADOS (CLIENTE E SERVIÇOS)
try {
    // Dados do Cliente
    $stmt = $pdo->prepare("SELECT nome, email, genero, idade, telefone, assinante, data_vencimento FROM clientes WHERE id = ?");
    $stmt->execute([$id_cliente]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    $nome_exibir   = $dados['nome'] ?? 'Cliente';
    $email_cliente = $dados['email'] ?? '';
    $genero         = $dados['genero'] ?? '';   
    $idade          = $dados['idade'] ?? '';    
    $telefone      = $dados['telefone'] ?? ''; 
    $status        = $dados['assinante'] ?? 'Não';
    $vencimento    = $dados['data_vencimento'] ?? null;

    // BUSCA OS SERVIÇOS DO BANCO PARA DEIXAR O SITE DINÂMICO
    $stmtServ = $pdo->query("SELECT * FROM servicos ORDER BY categoria DESC, nome ASC");
    $servicos_db = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro no banco: " . $e->getMessage());
}

// 2. LÓGICA DA NOTIFICAÇÃO (TOAST) - ATUALIZADA
$html_notification = "";
if ($status == 'Sim' && !empty($vencimento) && $vencimento != '0000-00-00') {
    $hoje = new DateTime(date('Y-m-d')); 
    $venc = new DateTime($vencimento);
    
    // Calcula a diferença real
    $diff = $hoje->diff($venc);
    $dias = (int)$diff->format("%r%a"); // %r traz o sinal de menos se já venceu

    // Se faltar 3 dias OU se já estiver vencido
    if ($dias <= 3) {
        if ($dias > 0) {
            // Ainda vai vencer
            $texto_vencimento = ($dias == 1) ? "vence AMANHÃ!" : "vence em $dias dias!";
            $titulo_toast = "Assinatura Próxima do Fim";
            $classe_toast = "toast-alerta"; // Cor padrão (amarelo/dourado)
            $icone_toast = "fa-bell";
        } elseif ($dias == 0) {
            // Vence hoje
            $texto_vencimento = "vence HOJE!";
            $titulo_toast = "Atenção: Vence Hoje";
            $classe_toast = "toast-perigo"; // Pode criar uma classe vermelha no CSS
            $icone_toast = "fa-exclamation-triangle";
        } else {
            // Já venceu (dias negativos)
            $dias_atraso = abs($dias); // Transforma -5 em 5
            $texto_vencimento = "está VENCIDA há $dias_atraso dia(s)!";
            $titulo_toast = "Assinatura Expirada";
            $classe_toast = "toast-vencido"; // Classe para destaque de erro
            $icone_toast = "fa-times-circle";
        }
        
        $html_notification = "
        <div id='toast-container' class='toast-alerta $classe_toast'>
            <div class='toast-content'>
                <i class='fas $icone_toast toast-icon'></i>
                <div class='toast-text'>
                    <span class='toast-title'>$titulo_toast</span>
                    <p>Olá $nome_exibir, sua assinatura $texto_vencimento Renove para continuar usando os benefícios.</p>
                </div>
                <button onclick=\"fecharToast()\" class='toast-close'>&times;</button>
            </div>
        </div>";

        // Lógica de E-mail (Opcional: enviar apenas se não venceu ainda ou se acabou de vencer)
        if (!isset($_SESSION['email_resend_enviado']) && $dias >= -1) {
            $apiKey = 're_3qV6TRcC_EoVjrZv9TpWbTT3efJTSWxwu'; 
            $postData = [
                'from'=>'Family Brook <onboarding@resend.dev>',
                'to'=>[$email_cliente],
                'subject'=>"⚠️ Status da Assinatura: $texto_vencimento",
                'html'=>"<p>Olá <strong>$nome_exibir</strong>, sua assinatura $texto_vencimento</p><p>Acesse o site para renovar.</p>"
            ];
            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$apiKey,'Content-Type: application/json'],CURLOPT_POSTFIELDS=>json_encode($postData)]);
            curl_exec($ch); curl_close($ch);
            $_SESSION['email_resend_enviado'] = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Barbearia Family Brook</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index_logado.css">
    <link rel="icon" href="fotos/icon.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
  <?= $html_notification ?>

    <div class="background-overlay">
        <span class="bg-icon icon-1">✂️</span>
        <span class="bg-icon icon-2">💈</span>
        <span class="bg-icon icon-3">🧔‍♂️</span>
        <span class="bg-icon icon-4">🧴</span>
    </div>

<nav class="menu">
    <div class="menu-centro">
        <div class="menu-wrapper">
            <a href="#home">Home</a>
            <a href="#sobre">Sobre mim</a>
            <a href="#servicos">Serviços</a>
            <a href="#agenda">Agendamentos</a>
            <a href="#avalia">Avaliações</a>
            <a href="#rede">Redes sociais</a>
            
            <div class="menu-direita">
                <a href="painel_cli.php" class="btn-menu-padrao">
                    <i class="fas fa-user-circle"></i> Olá, <?= htmlspecialchars($nome_exibir) ?> veja seu perfil
                </a>
                <a href="logout_index.php" class="btn-menu-padrao">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </div>
</nav>

<section id="home" class="secao home-hero">
    <div class="conteudo">
        <div class="hero-container">
            <div class="hero-texto">
                <span class="hero-badge">💈 A melhor barbearia da região</span>
                <h1 class="hero-titulo">Bem-vindo(a), <?= htmlspecialchars($nome_exibir) ?>!</h1>
                <p>Seu estilo em boas mãos. Agende seu horário agora e viva a experiência de um corte de precisão.</p>
                <div class="hero-botoes">
                    <a href="#agenda" class="btn-hero">Agendar Horário</a>
                    <a href="#servicos" class="btn-secundario">Ver Serviços</a>
                </div>
            </div>
            <div class="hero-imagem">
                <div class="card-foto">
                    <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=2070&auto=format&fit=crop" alt="Ambiente Barbearia">
                </div>
            </div>
        </div>
    </div>
</section>

<section id="sobre" class="secao">
    <div class="conteudo">
        <div class="sobre-card">
            <div class="sobre-container">
                <div class="sobre-texto">
                    <p>Prazer, eu sou</p>
                    <span class="cargo">Barbeiro Especialista</span>
                    <h2>Barbearia Family Brook</h2>
                    <p><strong>Barbeiro por vocação e mestre na arte da navalha.</strong> Ofereço um atendimento personalizado para que cada cliente saia da cadeira renovado.</p>
                </div>
                <div class="sobre-imagem">
                    <div class="card-foto">
                        <img src="../fotos/icon.jpeg" alt="Logo">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="servicos" class="secao">
    <div class="conteudo">
        <div class="sobre-card booksy-style"> 
            
            <div class="servicos-header-booksy">
                <h2>Serviços populares</h2>
                <i class="fas fa-star" style="color: #c5a059;"></i>
            </div>
            <div class="lista-servicos-booksy">
                <?php foreach ($servicos_db as $s): if ($s['categoria'] == 'Populares'): ?>
                <div class="servico-row">
                    <div class="servico-detalhes">
                        <h3><?= htmlspecialchars($s['nome']) ?></h3>
                        <span class="servico-tempo"><?= $s['duracao_minutos'] ?>min</span>
                    </div>
                    <div class="servico-preco-acao">
                        <span class="valor">R$ <?= number_format($s['preco'], 2, ',', '.') ?></span>
                        <a href="#agenda" class="btn-reservar" onclick="selecionarServico('<?= htmlspecialchars($s['nome']) ?>')">Agendar</a>
                    </div>
                </div>
                <?php endif; endforeach; ?>
            </div>

            <div class="servicos-header-booksy" style="margin-top: 30px;">
                <h2>Outros serviços</h2>
                <i class="fas fa-cut" style="color: #c5a059;"></i>
            </div>
            <div class="lista-servicos-booksy">
                <?php foreach ($servicos_db as $s): if ($s['categoria'] == 'Outros'): ?>
                <div class="servico-row">
                    <div class="servico-detalhes">
                        <h3><?= htmlspecialchars($s['nome']) ?></h3>
                        <?php if(!empty($s['descricao'])): ?>
                            <p class="servico-desc"><?= htmlspecialchars($s['descricao']) ?></p>
                        <?php endif; ?>
                        <span class="servico-tempo"><?= $s['duracao_minutos'] ?>min</span>
                    </div>
                    <div class="servico-preco-acao">
                        <span class="valor">R$ <?= number_format($s['preco'], 2, ',', '.') ?></span>
                        <a href="#agenda" class="btn-reservar" onclick="selecionarServico('<?= htmlspecialchars($s['nome']) ?>')">Agendar</a>
                    </div>
                </div>
                <?php endif; endforeach; ?>
            </div>

            <div class="servicos-header-booksy" style="margin-top: 30px;">
                <h2>Planos Mensais</h2>
                <i class="fas fa-crown" style="color: #c5a059;"></i>
            </div>
            <div class="lista-servicos-booksy">
                <?php foreach ($servicos_db as $s): 
                    // Identifica planos pelo nome ou se você tiver uma categoria 'Planos'
                    if (strpos(strtolower($s['nome']), 'pacote') !== false || strpos(strtolower($s['nome']), 'mensal') !== false): 
                ?>
                <div class="servico-row plan-highlight">
                    <div class="servico-detalhes">
                        <h3><?= htmlspecialchars($s['nome']) ?></h3>
                        <p class="servico-desc"><?= htmlspecialchars($s['descricao'] ?? 'Assine e economize!') ?></p>
                        <span class="servico-tempo"><?= $s['duracao_minutos'] ?>min</span>
                    </div>
                    <div class="servico-preco-acao">
                        <span class="valor">R$ <?= number_format($s['preco'], 2, ',', '.') ?></span>
                        <a href="../pagamentos/confirmar.php?plano=<?= $s['preco'] ?>" class="btn-assinar">Assinar</a>
                    </div>
                </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section id="agenda" class="secao">
    <div class="conteudo centralizado">
        <h2>Reserve seu Horário</h2>
        <p>Selecione o serviço e o barbeiro de sua preferência.</p>
        
        <div class="form-container">
            <form class="form-estilizado" action="../agendamento/confirmar_agenda.php" method="POST">
                <input type="hidden" name="id_cliente" value="<?= $id_cliente ?>">
                <input type="text" name="nome" value="<?= htmlspecialchars($nome_exibir) ?>" readonly required title="Nome do perfil">

                <div class="input-grupo">
                    <input type="date" name="data_agendamento" required>
                    <input type="time" name="horario" required>
                </div>

                <select name="nome_servico" id="servico-select" required>
                    <option value="" disabled selected hidden>Qual serviço deseja agendar?</option>
                    <?php foreach ($servicos_db as $s): ?>
                        <option value="<?= htmlspecialchars($s['nome']) ?>">
                            <?= htmlspecialchars($s['nome']) ?> - R$ <?= number_format($s['preco'], 2, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="barbeiro" required>
                    <option value="" disabled selected hidden>Escolha o Barbeiro</option>
                    <option value="Qualquer">Qualquer Barbeiro disponível</option>
                    <option value="Pires">Pires</option>
                    <option value="Joãozinho">Joãozinho</option>
                    <option value="Francist">Francist</option>
                    <option value="Lucas">Lucas</option>
                </select>

                <div class="input-grupo">
                    <input type="text" name="genero" value="<?= htmlspecialchars($genero) ?>" placeholder="Gênero" readonly>
                    <input type="text" name="idade" value="<?= htmlspecialchars($idade) ?>" placeholder="Idade" readonly>
                </div>
                
                <input type="tel" name="telefone" value="<?= htmlspecialchars($telefone) ?>" placeholder="WhatsApp" required maxlength="11">

                <button type="submit" class="btn-enviar">Confirmar Reserva</button>
            </form>
        </div>
    </div>
</section>

<section id="avalia" class="secao">
    <div class="conteudo centralizado">
        <h2>Avaliações</h2>
           <p>Deixe sua opinião sobre o atendimento ou sistema</p>
        <div class="form-container">
            <form class="form-estilizado" action="../avaliações/confima.php" method="POST">
                <input type="text" name="nome" value="<?= htmlspecialchars($nome_exibir) ?>" readonly required>
                <textarea name="comentario" placeholder="Escreva sua avaliação" required></textarea>
                <select name="nota" required>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2 </option>
                    <option value="1">1</option>
                </select>
                <button type="submit" class="btn-enviar">Enviar avaliação</button>
            </form>
        </div>
    </div>
</section>

<section id="rede" class="secao">
    <div class="conteudo centralizado">
        <h2>Para mais informações entre em contato</h2>
        <p class="social-descricao">Acompanhe-nos no Instagram para ver nossos últimos trabalhos!</p>
        <div class="redes-container">
            <div class="social-wrapper">
                <a href="https://www.instagram.com/familybrook_/" target="_blank" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
                <p>@familybrook_</p>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <p>&copy; 2026 Family Brook. Todos os direitos reservados.</p>
    <p class="creditos">Desenvolvido por Ryan Carlos</p>
</footer>

<script>
// Função para marcar o serviço no select ao clicar no botão 'Agendar' da lista
function selecionarServico(nomeServico) {
    const select = document.getElementById('servico-select');
    if (select) {
        select.value = nomeServico;
        // Scroll suave até o formulário
        document.getElementById('agenda').scrollIntoView({ behavior: 'smooth' });
        
        // Efeito visual no select
        select.style.borderColor = "#c5a059";
        select.style.boxShadow = "0 0 10px rgba(197, 160, 89, 0.5)";
        setTimeout(() => {
            select.style.borderColor = "";
            select.style.boxShadow = "";
        }, 2000);
    }
}

// Função para fechar o Toast
function fecharToast() {
    const toast = document.getElementById('toast-container');
    if (toast) {
        toast.style.transition = "opacity 0.8s ease, transform 0.8s ease";
        toast.style.opacity = "0";
        toast.style.transform = "translateY(20px)";
        setTimeout(() => { toast.style.display = 'none'; }, 800);
    }
}

window.onload = function() {
    const toastExists = document.getElementById('toast-container');
    if (toastExists) {
        setTimeout(fecharToast, 15000);
    }
};
</script>
</body>
</html>