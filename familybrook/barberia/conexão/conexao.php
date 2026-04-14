<?php
// Configurações do Banco de Dados
$host = "localhost";
$db   = "barber"; // Alterado para o nome do banco que criamos no SQL
$user = "root";    
$pass = "";        

try {
    // DSN (Data Source Name) com charset definido para evitar problemas de acentuação
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna os dados como array associativo
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Desativa emulação para maior segurança
    ];

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $options);
    
} catch (PDOException $e) {
    // Em produção, não é recomendado mostrar o erro detalhado, mas para desenvolvimento ajuda muito
    die("Erro crítico na conexão: " . $e->getMessage());
}
?>