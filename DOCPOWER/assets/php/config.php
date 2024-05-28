<?php

//if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//    // Se não for HTTPS, redireciona para a versão HTTPS da página
//    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//    header('Location: ' . $redirect);
//    exit();
//}
// Configurações de segurança para cookies de sessão
ini_set('session.cookie_secure', 1); // Envia o cookie apenas via HTTPS
ini_set('session.cookie_httponly', 1); // Torna o cookie inacessível via JavaScript
ini_set('session.use_only_cookies', 1); // Utiliza apenas cookies para armazenar o ID da sessão
ini_set('session.use_strict_mode', 1); // Proíbe a aceitação de IDs de sessão não inicializados pelo servidor
ini_set('display_errors', 1); // Exibe erros (remover em produção)
error_reporting(E_ALL); // Relatando todos os erros (remover em produção)

// Definição de parâmetros para cookies de sessão
session_set_cookie_params([
    'lifetime' => 1800, // Tempo de vida do cookie em segundos
    //'domain' => 'localhost', // Defina o domínio do cookie
    'path' => '/', // Caminho do cookie
    'secure' => true, // Cookie apenas transmitido via HTTPS
    'httponly' => true // Cookie inacessível via JavaScript
]);

// Inicia a sessão
session_start();

// Regeneração do ID da sessão para prevenir fixação de sessão
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} else {
    $interval = 60 * 30; // Intervalo de 30 minutos

    if (time() - $_SESSION['last_regeneration'] >= $interval) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Verifica a integridade do agente de usuário e endereço IP
if (isset($_SESSION['user_agent']) && isset($_SESSION['ip_address'])) {
    if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] || $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        session_unset(); // Limpa todas as variáveis de sessão
        session_destroy(); // Destrói a sessão
        // Redireciona para a página de login
        header('Location: ../login/index.php');
        exit(); // Garante que o script pare de executar após o redirecionamento
    }
} else {
    // Inicializa a sessão com as informações do agente de usuário e endereço IP
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
}

// Exemplo de implementação de tempo limite de sessão
$timeout_duration = 1800; // 30 minutos

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset(); // Remove todas as variáveis de sessão
    session_destroy(); // Destrói a sessão
    // Redireciona para a página de login
    header('Location: ../login/index.php');
    exit(); // Garante que o script pare de executar após o redirecionamento
}
$_SESSION['LAST_ACTIVITY'] = time(); // Atualiza o timestamp da última atividade