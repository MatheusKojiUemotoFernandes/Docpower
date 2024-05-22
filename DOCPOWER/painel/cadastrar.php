<?php
require_once "../assets/php/config.php";
include "../assets/php/conexao.php";
include("../assets/php/functions.php");
//include "../assets/php/lib/pdfparser-2.10.0/alt_autoload.php";

$token = $_POST['csrf_token'];
checkToken($token);

//function gerarNomeUnico($nomeOriginal) {
//    $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
//    return uniqid() . '.' . $extensao;
//}

$cnpj = sanitize($_POST["cnpj"]);
$razao = sanitize($_POST["razao"]);
$cidade = sanitize($_POST["cidade"]);
$uf = sanitize($_POST["uf"]);
//$contrato_assinado = $_FILES["contrato_assinado"];

$erros = [];

$ufs = [
    'RO',
    'AC',
    'AM',
    'RR',
    'PA',
    'AP',
    'TO',
    'MA',
    'PI',
    'CE',
    'RN',
    'PB',
    'PE',
    'AL',
    'SE',
    'BA',
    'MG',
    'ES',
    'RJ',
    'SP',
    'PR',
    'SC',
    'RS',
    'MS',
    'MT',
    'GO',
    'DF',
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

$stmt = $conexao->prepare("SELECT usuario_id FROM empresas WHERE cnpj = ? AND usuario_id = ?");
$stmt->bind_param("ss", $cnpj, $_SESSION["id"]);
$stmt->execute();
$stmt->store_result();
    
if ($stmt->num_rows > 0) {
    $erros['cnpj'] = 'Esta empresa já esta cadastrada.';
}
$stmt->close();

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

//if (empty($contrato_assinado["name"])) {
//    $erros["contrato_assinado"] = "Contrato é obrigatório.";
//} else {
//    $nomeArquivo = gerarNomeUnico($contrato_assinado["name"]);
//    $caminhoArquivo = "../data/contrato_assinado/{$_SESSION["sucesso_login"]} - {$_SESSION["id"]}/{$cnpj}/{$razao}/{$nomeArquivo}";
//    if (strtolower(pathinfo($contrato_assinado["name"], PATHINFO_EXTENSION)) != "pdf" || $contrato_assinado["type"] != "application/pdf") {
//        $erros["contrato_assinado"] = "O arquivo deve ser do tipo PDF.";
//    } else {
//        try {
//            $parser = new \Smalot\PdfParser\Parser();
//            $parser->parseFile($contrato_assinado["tmp_name"]);
//        } catch (Exception $e) {
//            $erros["contrato_assinado"] = "O arquivo deve ser do tipo PDF.";
//        }
//    }
//}

if (empty($erros)) {
    $stmt = $conexao->prepare("INSERT INTO empresas (cnpj, razao_social, cidade, uf, usuario_id) VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("sssss", $cnpj, $razao, $cidade, $uf, $_SESSION["id"]);

    if ($stmt->execute()) {
        $_SESSION["sucesso_cadastro"] = "Empresa cadastrada!";
        $stmt->close();
        $conexao->close();

        $caminhoArquivo = "../data/contrato_assinado/{$_SESSION["sucesso_login"]} - {$_SESSION["id"]}/{$cnpj}/{$razao}/termos_empresa.pdf";
        // Verifique se o diretório de destino existe e, se não, crie-o
        if (!is_dir(dirname($caminhoArquivo))) {
            mkdir(dirname($caminhoArquivo), 0777, true);
        }
        copy("../data/termos.pdf", $caminhoArquivo);

        header("Location: index.php");
        exit();
    } else {
        $erros["form"] = "Erro ao inserir empresa no banco de dados.";
    }

} else {
    $_SESSION["erros"] = $erros;
    $conexao->close();
    header("Location: index.php");
    exit();
}