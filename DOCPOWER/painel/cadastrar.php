<?php
require_once "../assets/php/config.php";
include "../assets/php/conexao.php";
include("../assets/php/functions.php");

$token = $_POST['csrf_token'];
checkToken($token);

$cnpj = sanitize($_POST["cnpj"]);
$razao = sanitize($_POST["razao"]);
$cidade = sanitize($_POST["cidade"]);
$uf = sanitize($_POST["uf"]);

$erros = [];

$ufs = [
    'RO', 'AC', 'AM', 'RR', 'PA', 'AP', 'TO', 'MA', 'PI', 'CE', 'RN', 'PB', 'PE', 'AL', 'SE', 'BA', 
    'MG', 'ES', 'RJ', 'SP', 'PR', 'SC', 'RS', 'MS', 'MT', 'GO', 'DF',
];

if (empty($cnpj)) {
    $erros["cnpj"] = 'É necessário por o CNPJ.';
} else {
    // Verificar se o documento contém apenas números, pontos, barras e traços
    if (!preg_match('/^[\d.\-\/]+$/', $cnpj)) {
        $erros["cnpj"] = 'CNPJ inválido. Use apenas números, pontos, barras e traços.';
    } else {
        if (!$cnpj = formatDOCU($cnpj)) {
            $erros["cnpj"] = 'CNPJ inválido.';
        }
    }
}

if (empty($erros)) {
    $stmt = $conexao->prepare("SELECT usuario_id FROM empresas WHERE cnpj = :cnpj AND usuario_id = :usuario_id");
    $stmt->bindParam(':cnpj', $cnpj);
    $stmt->bindParam(':usuario_id', $_SESSION["id"]);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $erros['cnpj'] = 'Esta empresa já está cadastrada.';
    }
}

if (empty($razao)) {
    $erros["razao"] = "Razão social é obrigatório.";
}

if (empty($cidade)) {
    $erros["cidade"] = "É necessário por o nome da cidade.";
}

if (empty($uf)) {
    $erros["uf"] = "UF é obrigatório.";
} elseif (!in_array($uf, $ufs)) {
    $erros["uf"] = "UF inválido.";
}

if (empty($erros)) {
    $stmt = $conexao->prepare("INSERT INTO empresas (cnpj, razao_social, cidade, uf, usuario_id) VALUES (:cnpj, :razao, :cidade, :uf, :usuario_id)");
    $stmt->bindParam(':cnpj', $cnpj);
    $stmt->bindParam(':razao', $razao);
    $stmt->bindParam(':cidade', $cidade);
    $stmt->bindParam(':uf', $uf);
    $stmt->bindParam(':usuario_id', $_SESSION["id"]);

    if ($stmt->execute()) {
        $_SESSION["sucesso_cadastro"] = "Empresa cadastrada!";
        
        $caminhoArquivo = "../data/termos/{$_SESSION["sucesso_login"]} - {$_SESSION["id"]}/{$cnpj}/{$razao}/termos_empresa.pdf";
        // Verifique se o diretório de destino existe e, se não, crie-o
        if (!is_dir(dirname($caminhoArquivo))) {
            mkdir(dirname($caminhoArquivo), 0777, true);
        }
        copy("../data/termos.pdf", $caminhoArquivo);
        $stmt->closeCursor();
        $conexao = null;
        header("Location: index.php");
        exit();
    } else {
        $erros["form"] = "Erro ao inserir empresa no banco de dados.";
    }
}

// Definir erros na sessão e redirecionar em caso de erro
if (!empty($erros)) {
    $conexao = null;
    mensagem_erro($erros, '../painel');
    exit();
}