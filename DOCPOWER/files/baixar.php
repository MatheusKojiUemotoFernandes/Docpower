<?php
require_once '../assets/php/config.php';
include("../assets/php/conexao.php");
include("../assets/php/functions.php");

$arquivo = $_GET["arquivo"];
$solicitacao_id = $_GET["solicitacao_id"];

$stmt = $conexao->prepare("INSERT INTO historico (solicitacoes_id, validade) VALUES (?, NOW())");
$stmt->execute([$solicitacao_id]);

echo "<script>window.open('$arquivo');</script>";