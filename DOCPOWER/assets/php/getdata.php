<?php
require_once '../assets/php/config.php';
include("../assets/php/conexao.php");

$stmt = $conexao->prepare("SELECT chaves FROM usuario WHERE id = ?");
$stmt->bind_param("s", $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($_SESSION['chaves']);
$stmt->fetch();
$stmt->close();

$stmt = $conexao->prepare("SELECT COUNT(*) AS num_empresas FROM empresas WHERE usuario_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($_SESSION['num_empresas']);
$stmt->fetch();
$stmt->close();

$_SESSION['nomes_empresas'] = array();

$stmt = $conexao->prepare("SELECT razao_social FROM empresas WHERE usuario_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($nome_empresa);

while ($stmt->fetch()) {
  $_SESSION['nomes_empresas'][] = $nome_empresa;
}

$stmt->close();


$_SESSION['cnpj_empresas'] = array();

$stmt = $conexao->prepare("SELECT cnpj FROM empresas WHERE usuario_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($cnpj_empresa);

while ($stmt->fetch()) {
  $_SESSION['cnpj_empresas'][] = $cnpj_empresa;
}

$stmt->close();

$conexao->close();
