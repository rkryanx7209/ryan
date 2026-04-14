<?php
session_start();

// 1. SEGURANÇA: Verifica se o cliente está realmente logado
$logado = !empty($_SESSION['usuario_id']) && $_SESSION['tipo'] === 'cliente';

if (!$logado) {
    header("Location: ../acesso/login.php");
    exit();
}

$nome_exibicao = $_SESSION['usuario_nome'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="painel_cli.css"> 
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
</head>
<body>

    <div class="login-box">
        <div class="perfil-icon">
            <i class="fas fa-user-circle"></i>
        </div>
        
        <h2>Olá, <?= htmlspecialchars(explode(' ', $nome_exibicao)[0]) ?>!</h2>
        <p>Gerencie sua conta na Family Brook</p>
        
        <div class="grid-servicos">
            <a href="../cli/editar_cadrasto.php" class="btn-padrao">
                <i class="fas fa-user-edit"></i> Editar Dados
            </a>
            <a href="../cli/minha_agenda.php" class="btn-padrao">
                <i class="fas fa-calendar-alt"></i> Meus Agendamentos
            </a>
            <a href="../cli/minha_avalia.php" class="btn-padrao">
                <i class="fas fa-star"></i> Minhas Avaliações
            </a>
            <a href="../cli/planos.php" class="btn-padrao">
                <i class="fas fa-crown"></i> Planos
            </a>
        </div>

        <div class="botoes-nav">
            <a href="index_logado.php" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            
            <a href="../index.html" class="btn-sair">
                <i class="fas fa-sign-out-alt"></i> Sair 
            </a>
         <a href="../acesso/logout.php" class="btn-sair">
                <i class="fas fa-sign-out-alt"></i> trocar de conta 
            </a>
        </div>
    </div>

</body>
</html>