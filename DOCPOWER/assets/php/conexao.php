<?php
define('HOST', '127.0.0.1');
define('USUARIO', 'root');
define('SENHA', '');
define('DB', 'docpower');

$conexao = new mysqli(HOST, USUARIO, SENHA, DB);

if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}