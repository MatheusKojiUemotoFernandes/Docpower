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
    $stmt = $conexao->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Verificar se o email existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nome, $hashed_password);
        $stmt->fetch();

        // Verificar a senha
        if (password_verify($senha, $hashed_password)) {
            // Sucesso no login
            $_SESSION['sucesso_login'] = $nome;
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $id;

            // Redirecionar para o painel
            $stmt->close();
            $conexao->close();

            $caminhoArquivo = "../data/contrato_assinado/{$_SESSION["sucesso_login"]} - {$_SESSION["id"]}/termos_usuario.pdf";
            // Verifique se o diretório de destino existe e, se não, crie-o
            if (!is_dir(dirname($caminhoArquivo))) {
                mkdir(dirname($caminhoArquivo), 0777, true);
            }
            copy("../data/termos.pdf", $caminhoArquivo);

            header('Location: ../painel/index.php');
            exit;
        } else {
            // Senha incorreta
            $erros['login'] = 'Senha incorreta.';
        }
    } else {
        // Conta não existe
        $erros['login'] = 'Esta conta não existe.';
    }

    $stmt->close();
}

// Definir erros na sessão e redirecionar em caso de erro
if (!empty($erros)) {
    session_unset();
    $_SESSION['erros'] = $erros;
    $conexao->close();
    header('Location: index.php');
    exit;
}
