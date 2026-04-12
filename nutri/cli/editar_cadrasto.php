<?php
session_start();
require_once '../conexão/conexao.php';

// Proteção: Apenas clientes logados acessam
if (!isset($_SESSION['cliente_id'])) {
    header("Location: index.html");
    exit;
}

$id = $_SESSION['cliente_id'];
$msg = "";

// Lógica de Atualização ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']);
    $email    = trim($_POST['email']);
    $cep      = trim($_POST['cep']);
    $telefone = trim($_POST['telefone']);
    $idade    = (int)$_POST['idade'];
    $genero   = $_POST['genero'];
    $senha_nova = $_POST['senha'];

    try {
        // Query base para dados comuns
        $sql = "UPDATE clientes SET nome = ?, email = ?, cep = ?, telefone = ?, idade = ?, genero = ? WHERE id = ?";
        $params = [$nome, $email, $cep, $telefone, $idade, $genero, $id];

        // Se o usuário digitou uma nova senha, incluímos no UPDATE
        if (!empty($senha_nova)) {
            $senha_hash = password_hash($senha_nova, PASSWORD_DEFAULT);
            $sql = "UPDATE clientes SET nome = ?, email = ?, cep = ?, telefone = ?, idade = ?, genero = ?, senha = ? WHERE id = ?";
            $params = [$nome, $email, $cep, $telefone, $idade, $genero, $senha_hash, $id];
        }

        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute($params)) {
            $_SESSION['cliente_nome'] = $nome; // Atualiza nome na barra de navegação
            $msg = "Cadastro atualizado com sucesso!";
        } else {
            $msg = "Erro ao atualizar os dados.";
        }
    } catch (PDOException $e) {
        $msg = "Erro no banco de dados: " . $e->getMessage();
    }
}

// Busca dados atuais para preencher os campos automaticamente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cadastro | Daniele França</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="editar_cadrasto.css"> 
    <link rel="icon" href="../fotos/images-removebg-preview copy.png">
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
                <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
            </div>
            
            <div class="campo-grupo">
                <label>E-mail:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="form-row">
                <div class="campo-grupo">
                    <label>CEP:</label>
                    <input type="text" name="cep" value="<?= htmlspecialchars($user['cep'] ?? '') ?>" placeholder="00000-000">
                </div>
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
                        <option value="Masculino" <?= ($user['genero'] == 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                        <option value="Feminino" <?= ($user['genero'] == 'Feminino') ? 'selected' : '' ?>>Feminino</option>
                        <option value="Outro" <?= ($user['genero'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
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
            
            <a href="../pagina/index_logado.php" class="btn-padrao btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar ao perfil
            </a>
        </form>
    </div>
</section>

</body>
</html>