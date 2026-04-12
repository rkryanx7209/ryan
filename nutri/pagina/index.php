<?php
session_start();
// Verifica se existe um cliente logado para mostrar o nome
$logado = !empty($_SESSION['cliente_id']);
$nome_exibicao = $logado ? $_SESSION['cliente_nome'] : "Visitante";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu perfil</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">

</head>
<body>

    <div class="login-box">
        <?php if ($logado): ?>
            <div class="perfil-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2>Olá, <?= htmlspecialchars($nome_exibicao) ?>!</h2>
            <p>Acesse suas informações abaixo:</p>
            
            <div class="grid-servicos">
          <a href="../cli/editar_cadrasto.php" class="btn-padrao">
           <i class="fas fa-user-edit"></i> Editar meu cadastro
          </a>
                <a href="../cli/minha_dieta.php" class="btn-padrao">
                    <i class="fas fa-apple-alt"></i> Minha Dieta
                </a>
                <a href="../cli/minha_agenda.php" class="btn-padrao">
                    <i class="fas fa-calendar-alt"></i> Meus Agendamentos
                </a>
                <a href="../cli/minha_avalia.php" class="btn-padrao">
                    <i class="fas fa-star"></i> Minhas Avaliações
                </a>
            </div>

            <div class="botoes-nav">
                <a href="index_logado.php" class="btn-padrao btn-voltar">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="../acesso/logout.php" class="btn-padrao btn-sair">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
                <a href="../acesso/logout.php" class="btn-padrao btn-sair">
                    <i class="fas fa-sign-out-alt"></i> trocar de conta
                </a>
            </div>
            
        <?php else: ?>
        <?php endif; ?>
    </div>

</body>
</html>