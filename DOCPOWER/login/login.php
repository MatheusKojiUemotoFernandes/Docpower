<?php
require_once '../assets/php/config.php';
include("../assets/php/conexao.php");
include("../assets/php/functions.php");

$token = $_POST['csrf_token'];
checkToken($token);

// Sanitizar entradas
$email = sanitize($_POST['email']);
$senha = sanitize($_POST['senha']);

$erros = [];

// Validação de campos obrigatórios
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros['email'] = 'Email inválido.';
}

if (empty($senha)) {
    $erros['senha'] = 'Você deve preencher este campo.';
}

if (empty($erros)) {
    // Preparar e executar a consulta
    $stmt = $conexao->prepare("SELECT id, nome, senha FROM usuario WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Verificar se o email existe
    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $usuario['id'];
        $nome = $usuario['nome'];
        $hashed_password = $usuario['senha'];

        // Verificar a senha
        if (password_verify($senha, $hashed_password)) {
            // Sucesso no login
            $_SESSION['sucesso_login'] = $nome;
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $id;

            // Verificar e criar diretório se necessário
            $caminhoArquivo = "../data/termos/{$_SESSION["sucesso_login"]} - {$_SESSION["id"]}/termos_usuario.pdf";
            if (!is_dir(dirname($caminhoArquivo))) {
                mkdir(dirname($caminhoArquivo), 0777, true);
            }
            copy("../data/termos.pdf", $caminhoArquivo);
            $stmt->closeCursor();
            $conexao = null;
            // Redirecionar de acordo com o usuário
            if ($_SESSION['email'] === 'admin@docpower.com.br' && $_SESSION['sucesso_login'] === 'Administrador') {
                header('Location: ../admin');
            } else {
                header('Location: ../painel');
            }
            exit;
        } else {
            // Senha incorreta
            $erros['login'] = 'Senha incorreta.';
        }
    } else {
        // Conta não existe
        $erros['login'] = 'Esta conta não existe.';
    }
}

// Definir erros na sessão e redirecionar em caso de erro
if (!empty($erros)) {
    $conexao = null;
    mensagem_erro($erros, '../login');
    exit;
}