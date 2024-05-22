<?php

//implementação de proteção contra CSRF
function generateToken(){
  if(isset($_SESSION['csrf_token'])) {
      unset($_SESSION['csrf_token']);
  }

  $_SESSION['token'] = bin2hex(random_bytes(32));

  $options = ['cost' => 13];
  $_SESSION['csrf_token'] = password_hash($_SESSION['token'], PASSWORD_ARGON2ID, $options);

  return "<input type='hidden' name='csrf_token' value='{$_SESSION['csrf_token']}'/>";
}

function checkToken($token) {

  if(!isset($_SESSION['csrf_token']) || !password_verify($_SESSION['token'], $token)){
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['erros']['login'] = 'Movimento suspeito detectado!';
    header('Location: ../login/index.php');
    exit();
  }
}

// Função para sanitizar entradas
function sanitize($data) {
  global $conexao;
  return mysqli_real_escape_string($conexao, htmlentities($data, ENT_QUOTES, 'UTF-8'));
}

// Função para formatar Documento
function formatDOCU($docu) {
  $docu = preg_replace('/\D/', '', $docu);

  if (strlen($docu) === 11) { // CPF
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $docu);
  } else if (strlen($docu) === 14) { //CNPJ
    return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $docu);
  }

  return false;
}

function Telefone($telefone) {

  // Verifica se o telefone tem 11 caracteres
  if (strlen($telefone) !== 11) {
      return false;
  }
  // Formata o telefone
  return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
}