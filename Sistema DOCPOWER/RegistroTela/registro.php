<?php
session_start();

define('HOST', '127.0.0.1');
define('USUARIO', 'root');
define('SENHA', '');
define('DB', 'docpower');

$conexao = mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('Não foi possível conectar');

$docu = mysqli_real_escape_string($conexao, trim($_POST['docu']));
$nome = mysqli_real_escape_string($conexao, trim($_POST['nome']));
$tele = mysqli_real_escape_string($conexao, trim($_POST['tele']));
$email = mysqli_real_escape_string($conexao, trim($_POST['email']));
$senha = mysqli_real_escape_string($conexao, trim(md5($_POST['senha'])));

function emailExists($conexao, $email) {
    $sql = "SELECT * FROM usuario WHERE email = '$email'";
    $resultado = mysqli_query($conexao, $sql);
    return mysqli_num_rows($resultado) > 0;
}
function docuExists($conexao, $docu) {
    $sql = "SELECT * FROM usuario WHERE docu = '$docu'";
    $resultado = mysqli_query($conexao, $sql);
    return mysqli_num_rows($resultado) > 0;
}
function teleExists($conexao, $tele) {
    $sql = "SELECT * FROM usuario WHERE tele = '$tele'";
    $resultado = mysqli_query($conexao, $sql);
    return mysqli_num_rows($resultado) > 0;
}

if(empty($docu)){
    $_SESSION['documento'] = 'Você deve preencher esse campo!';
} if (empty($nome)){
    $_SESSION['nome'] = 'Você deve preencher esse campo!';
} if (empty($tele)){
    $_SESSION['tele'] = 'Você deve preencher esse campo!';
} if (empty($email)){
    $_SESSION['email'] = 'Você deve preencher esse campo!';
} if (empty($_POST['senha'])){
    $_SESSION['senha'] = 'Você deve preencher esse campo!';
} else {
    $campo = 1;
}

if ($campo == 1) {
    if (strlen($_POST['senha']) < 8){
        $_SESSION['senha'] = 'A senha não pode ser menor que 8 caracteres.';
    } elseif (strlen($_POST['senha']) > 32) {
        $_SESSION['senha'] = 'A senha não pode ser maior que 32 caracteres.';
    } elseif (strlen($docu) < 14 || (strlen($docu) < 11)) {
        $_SESSION['documento'] = 'Documento inválido.';
    } elseif (emailExists($conexao, $email)) {
        $_SESSION['email'] = 'Este email já está em uso.';
    } elseif (docuExists($conexao, $docu)) {
        $_SESSION['docu'] = 'Este documento já está em uso.';
    } elseif (teleExists($conexao, $tele)) {
        $_SESSION['tele'] = 'Este telefone já está em uso.';
    } else {
        $sql = "INSERT INTO usuario (docu, nome, tele, email, senha, data_cadastro) VALUES ('$docu', '$nome', '$tele', '$email', '$senha', NOW())";
        if($conexao->query($sql) === TRUE) {
            $_SESSION['sucesso_cadastro'] = 'Cadastro realizado com sucesso!';
        } else {
            $_SESSION['erro_cadastro'] = 'Falha ao cadastrar. Por favor, tente novamente.';
        }
    }
}


$conexao->close();

header('Location: index.php');
exit;
?>
