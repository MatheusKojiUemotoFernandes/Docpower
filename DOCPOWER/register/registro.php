<?php
require_once '../assets/php/config.php';
include("../assets/php/conexao.php");

$docu = mysqli_real_escape_string($conexao, htmlentities($_POST['docu']));
$nome = mysqli_real_escape_string($conexao, htmlentities($_POST['nome']));
$tele = mysqli_real_escape_string($conexao, htmlentities($_POST['tele']));
$email = mysqli_real_escape_string($conexao, htmlentities($_POST['email']));
$senha = mysqli_real_escape_string($conexao, htmlentities($_POST['senha']));
$senha_confirmacao = htmlentities($_POST['senha_check']);

$erros = [];

if (empty($docu) || strlen($docu) < 11 || strlen($docu) > 14) {
    $erros['documento'] = 'Documento inválido.';
}

if (empty($nome)) {
    $erros['nome'] = 'Nome é obrigatório.';
}

if (empty($tele) || strlen($tele) < 11) {
    $erros['tele'] = 'Telefone inválido.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros['email'] = 'Email inválido.';
}

if (empty($senha) || strlen($senha) < 8 || strlen($senha) > 32) {
    $erros['senha'] = 'Senha inválida. Deve conter entre 8 e 32 caracteres.';
}

if ($senha !== $senha_confirmacao) {
    $erros['senha_check'] = 'As senhas não coincidem.';
}

if (empty($erros)) {
    $stmt = $conexao->prepare("SELECT id FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $erros['email'] = 'Este email já está em uso.';
    }

    $stmt->close();

    $stmt = $conexao->prepare("SELECT id FROM usuario WHERE docu = ?");
    $stmt->bind_param("s", $docu);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $erros['documento'] = 'Este documento já está em uso.';
    }

    $stmt->close();

    $stmt = $conexao->prepare("SELECT id FROM usuario WHERE tele = ?");
    $stmt->bind_param("s", $tele);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $erros['tele'] = 'Este telefone já está em uso.';
    }

    $stmt->close();

    if (empty($erros)) {
        $options = [
            'cost' => 13,
        ];
        $hashed_password = password_hash($senha, PASSWORD_ARGON2ID);

        $stmt = $conexao->prepare("INSERT INTO usuario (docu, nome, tele, email, senha, data_cadastro) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $docu, $nome, $tele, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['sucesso_cadastro'] = 'Cadastro realizado com sucesso!';
            $stmt->close();
            $conexao->close();
            header('Location: ../login/index.php');
        } else {
            $_SESSION['erro_cadastro'] = 'Falha ao cadastrar. Por favor, tente novamente.';
            $stmt->close();
            $conexao->close();
            header('Location: index.php');
        }
    }
} else {
    $_SESSION['erros'] = $erros;
    $conexao->close();
    header('Location: index.php');
}