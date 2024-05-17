<?php
require_once "../assets/php/config.php";
include "../assets/php/conexao.php";
include "../assets/php/lib/pdfparser-2.10.0/alt_autoload.php";

function gerarNomeUnico($nomeOriginal) {
    $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
    return uniqid() . '.' . $extensao;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cnpj = mysqli_real_escape_string($conexao, htmlentities($_POST["cnpj"]));
    $razao = mysqli_real_escape_string($conexao, htmlentities($_POST["razao"]));
    $cidade = mysqli_real_escape_string($conexao, htmlentities($_POST["cidade"]));
    $uf = mysqli_real_escape_string($conexao, htmlentities($_POST["uf"]));
    $contrato_assinado = $_FILES["contrato_assinado"];

    $erros = [];

    $ufs = [
        "RO",
        "AC",
        "AM",
        "RR",
        "PA",
        "AP",
        "TO",
        "MA",
        "PI",
        "CE",
        "RN",
        "PB",
        "PE",
        "AL",
        "SE",
        "BA",
        "MG",
        "ES",
        "RJ",
        "SP",
        "PR",
        "SC",
        "RS",
        "MS",
        "MT",
        "GO",
        "DF",
    ];

    if (empty($cnpj)) {
        $erros["cnpj"] = "É necessário por o CNPJ.";
    } elseif (strlen($cnpj) != 14) {
        $erros["cnpj"] = "CNPJ inválido.";
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

    if (empty($contrato_assinado["name"])) {
        $erros["contrato_assinado"] = "Contrato é obrigatório.";
    } else {
        $nomeArquivo = gerarNomeUnico($contrato_assinado["name"]);
        $caminhoArquivo = "../data/contrato_assinado/{$_SESSION["sucesso_login"]} - {$_SESSION["id"]}/{$razao}/{$cnpj}/{$nomeArquivo}";

        if (strtolower(pathinfo($contrato_assinado["name"], PATHINFO_EXTENSION)) != "pdf" || $contrato_assinado["type"] != "application/pdf") {
            $erros["contrato_assinado"] = "O arquivo deve ser do tipo PDF.";
        } else {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $parser->parseFile($contrato_assinado["tmp_name"]);
            } catch (Exception $e) {
                $erros["contrato_assinado"] = "O arquivo deve ser do tipo PDF.";
            }
        }
    }

    if (empty($erros)) {

        $stmt = $conexao->prepare(
            "INSERT INTO empresas (cnpj, razao_social, cidade, uf, usuario_id) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssss",
            $cnpj,
            $razao,
            $cidade,
            $uf,
            $_SESSION["id"]
        );

        if ($stmt->execute()) {
            $_SESSION["sucesso_cadastro"] = "Empresa cadastrada!";
            $stmt->close();
            $conexao->close();

            if (!is_dir(dirname($caminhoArquivo))) {
                mkdir(dirname($caminhoArquivo), 0777, true);
            }
            move_uploaded_file($contrato_assinado["tmp_name"], $caminhoArquivo);

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
}