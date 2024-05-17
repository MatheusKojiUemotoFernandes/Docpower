<?php
require_once '../assets/php/config.php';
include("../assets/php/conexao.php");

$email = mysqli_real_escape_string($conexao, htmlentities($_POST['email']));
$senha = mysqli_real_escape_string($conexao, htmlentities($_POST['senha']));

$erros = [];

if (empty($email)) {
    $erros['email'] = 'Você deve preencher este campo.';
}

if (empty($senha)) {
    $erros['senha'] = 'Você deve preencher este campo.';
}

if (empty($erros)) {

    $stmt = $conexao->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nome, $hashed_password);
        $stmt->fetch();
    
        if (password_verify($senha, $hashed_password)) {
            
            $_SESSION['sucesso_login'] = $nome;
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $id;

            $stmt->close();
            $conexao->close();
            header('Location: ../painel/index.php');
            exit;
        } else {
            $_SESSION['erro_login'] = 'Senha incorreta.';
            $stmt->close();
            $conexao->close();
            header('Location: index.php');
            exit;
        }
    } else {
        $_SESSION['erro_login'] = 'Esta conta não existe.';
        $stmt->close();
        $conexao->close();
        header('Location: index.php');
        exit;
    }
} else {
    $_SESSION['erros'] = $erros;
    $conexao->close();
    header('Location: index.php');
    exit;
}