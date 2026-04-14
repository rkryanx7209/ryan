<?php
session_start();
require_once '../conexão/conexao.php';

// CORREÇÃO: Sincronizado com usuario_id do seu sistema de login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../acesso/login.php");
    exit;
}

$id = $_SESSION['usuario_id'];
$msg = "";

// Lógica de Atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']);
    $email    = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $idade    = (int)$_POST['idade'];
    $genero   = $_POST['genero'];
    $senha_nova = $_POST['senha'];

    try {
        // Query base
        $sql = "UPDATE clientes SET nome = ?, email = ?, telefone = ?, idade = ?, genero = ? WHERE id = ?";
        $params = [$nome, $email, $telefone, $idade, $genero, $id];

        // Se mudar a senha
        if (!empty($senha_nova)) {
            // Se o seu sistema de login NÃO usa password_hash, mude para $senha_nova simples
            $senha_hash = password_hash($senha_nova, PASSWORD_DEFAULT);
            $sql = "UPDATE clientes SET nome = ?, email = ?, telefone = ?, idade = ?, genero = ?, senha = ? WHERE id = ?";
            $params = [$nome, $email,$telefone, $idade, $genero, $senha_hash, $id];
        }

        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute($params)) {
            // CORREÇÃO: Atualiza as chaves corretas da sessão para refletir no site todo
            $_SESSION['usuario_nome'] = $nome; 
            $msg = "Cadastro atualizado com sucesso!";
        } else {
            $msg = "Erro ao atualizar os dados.";
        }
    } catch (PDOException $e) {
        $msg = "Erro no banco de dados: " . $e->getMessage();
    }
}

// Busca dados atuais
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cadastro | Family Brook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="editar_cadrasto.css"> 
    <link rel="icon" href="/barberia/fotos/icon.jpeg">
    <style>
        /* Garantindo que o fundo combine com sua barbearia caso o CSS falhe */
        body { background-color: #121212; color: white; font-family: 'Segoe UI', sans-serif; }
        .msg-sucesso { background: rgba(0, 255, 0, 0.1); color: #00ff00; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 20px; border: 1px solid #00ff00; }
        .btn-padrao { display: block; width: 100%; padding: 12px; margin-top: 10px; border-radius: 8px; border: none; cursor: pointer; text-align: center; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .btn-padrao:first-of-type { background-color: #d4af37; color: black; }
        .btn-voltar { background-color: transparent; color: #888; border: 1px solid #444; }
        .btn-padrao:hover { opacity: 0.8; transform: translateY(-2px); }
    </style>
</head>
<body>

<section class="secao">
    <div class="sobre-card">
        <h2 class="titulo-dieta"><i class="fas fa-user-cog"></i> Editar Cadastro</h2>

        <?php if($msg): ?>
            <p class="msg-sucesso"><?= $msg ?></p>
        <?php endif; ?>

        <form class="form-estilizado" method="POST">
            
            <div class="campo-grupo">
                <label>Nome Completo:</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($user['nome'] ?? '') ?>" required>
            </div>
            
            <div class="campo-grupo">
                <label>E-mail:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>
            
            <div class="form-row">
                <div class="campo-grupo">
                    <label>Telefone:</label>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($user['telefone'] ?? '') ?>" placeholder="(00) 00000-0000">
                </div>
            </div>

            <div class="form-row">
                <div class="campo-grupo">
                    <label>Idade:</label>
                    <input type="number" name="idade" value="<?= htmlspecialchars($user['idade'] ?? '') ?>">
                </div>
                <div class="campo-grupo">
                    <label>Gênero:</label>
                    <select name="genero">
                        <option value="Masculino" <?= (($user['genero'] ?? '') == 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                        <option value="Feminino" <?= (($user['genero'] ?? '') == 'Feminino') ? 'selected' : '' ?>>Feminino</option>
                        <option value="Outro" <?= (($user['genero'] ?? '') == 'Outro') ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>
            </div>

            <div class="campo-grupo">
                <label>Nova Senha (deixe em branco para não mudar):</label>
                <input type="password" name="senha" placeholder="Digite apenas se quiser mudar">
            </div>
            
            <button type="submit" class="btn-padrao">
                <i class="fas fa-check"></i> Salvar Alterações
            </button>
            
            <a href="../pagina/painel_cli.php" class="btn-padrao btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar ao perfil
            </a>
        </form>
    </div>
</section>

</body>
</html>