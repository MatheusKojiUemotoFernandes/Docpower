<?php
define('HOST', '127.0.0.1');
define('USUARIO', 'docpow00_root');
define('SENHA', 'p4s4J~yREmdS');
define('DB', 'docpow00_painel');

$conexao = new mysqli(HOST, USUARIO, SENHA, DB);

if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}