<?php
// Carrega variáveis de ambiente
require_once '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (!defined('DSN')) {
    define('DSN', 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME']);
}
if (!defined('USERNAME')) {
    define('USERNAME', $_ENV['DB_USER']);
}
if (!defined('PASSWORD')) {
    define('PASSWORD', $_ENV['DB_PASS']);
}

// Verifica se a função connect() já foi definida
if (!function_exists('connect')) {
    function connect(){
        try {
            $pdo = new PDO(DSN, USERNAME, PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Conexão falhou: ' . $e->getMessage());
        }
        return $pdo;
    }
}

// Verifica se a conexão já foi estabelecida antes de criar uma nova
if (!isset($conexao)) {
    $conexao = connect();
}
