<?php
require_once '../assets/php/config.php';
include("../assets/php/conexao.php");

// Obter as chaves do usuário
$stmt = $conexao->prepare("SELECT chaves FROM usuario WHERE id = ?");
$stmt->execute([$_SESSION['id']]);
$_SESSION['chaves'] = $stmt->fetchColumn();
$stmt->closeCursor();

// Contar o número de empresas do usuário
$stmt = $conexao->prepare("SELECT COUNT(*) AS num_empresas FROM empresas WHERE usuario_id = ?");
$stmt->execute([$_SESSION['id']]);
$_SESSION['num_empresas'] = $stmt->fetchColumn();
$stmt->closeCursor();

// Obter os nomes das empresas do usuário
$stmt = $conexao->prepare("SELECT razao_social FROM empresas WHERE usuario_id = ?");
$stmt->execute([$_SESSION['id']]);
$_SESSION['nomes_empresas'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
$stmt->closeCursor();

// Obter os CNPJs das empresas do usuário
$stmt = $conexao->prepare("SELECT cnpj FROM empresas WHERE usuario_id = ?");
$stmt->execute([$_SESSION['id']]);
$_SESSION['cnpj_empresas'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
$stmt->closeCursor();

$stmt = $conexao->prepare("SELECT COUNT(*) AS total_solicitacoes FROM solicitacoes WHERE arquivo = 1 AND usuario_id = :usuario_id");
$stmt->bindParam(':usuario_id', $_SESSION['id'], PDO::PARAM_INT);
$stmt->execute();
$_SESSION['novos_arquivos'] = $stmt->fetch(PDO::FETCH_ASSOC);

$conexao = null; // Fechar conexão
