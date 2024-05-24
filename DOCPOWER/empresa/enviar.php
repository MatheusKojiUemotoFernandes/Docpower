<?php
require_once '../assets/php/config.php';
require '../assets/php/lib/vendor/autoload.php';
include("../assets/php/functions.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$token = $_POST['csrf_token'];
checkToken($token);

$datainicial = $_POST['datainicial'] ?? null;
$datafinal = $_POST['datafinal'] ?? null;
$senha_certificado = $_POST['senha_certificado'] ?? null;

$nfce = $_POST['nfce'] ?? null;
$cupom_fiscal = $_POST['cupom_fiscal'] ?? null;
$danfes = $_POST['danfes'] ?? null;
$nfse = $_POST['nfse'] ?? null;

$certificado = $_FILES['certificado'] ?? null;
$sped = $_FILES['sped'] ?? null;
$chaves_acesso = $_FILES['chaves_acesso'] ?? null;
$dataInicialFormatada = DateTime::createFromFormat('d/m/Y', $datainicial);
$dataFinalFormatada = DateTime::createFromFormat('d/m/Y', $datafinal);
$erros = [];

if (empty($nfce) && empty($cupom_fiscal) && empty($danfes) && empty($nfse)) {
    $erros["checkbox"] = "Selecione pelo menos uma opção.";
}

if ($dataInicialFormatada && $dataFinalFormatada) {
    $interval = $dataInicialFormatada->diff($dataFinalFormatada);
    if ($interval->y > 5 || ($interval->y == 5 && ($interval->m > 0 || $interval->d > 0))) {
        $erros["data"] = "A diferença entre as datas não pode ser maior que 5 anos.";
    } 
} else {
    $erros["data"] = "Data inválida.";
}

if (empty($certificado)) {
    $erros["certificado"] = "O certificado é obrigatório.";
} elseif (empty($senha_certificado)) {
    $erros["senha_certificado"] = "Você precisa escrever a senha!";
}

if (empty($chaves_acesso)) {
    $erros["chaves_acesso"] = "Você deve postar a chave de acesso!";
}

if (!empty($erros)) {
    $_SESSION["erros"] = $erros;
    header("Location: index.php?empresa={$_SESSION['dados_empresa']['empresa']}&cnpj={$_SESSION['dados_empresa']['cnpj']}");
    exit();
}

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = 'smtp.titan.email';
    $mail->Username = 'comercial@docpower.com.br';
    $mail->Password = 'comercial@123';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('comercial@docpower.com.br', "DOCPOWER - {$_SESSION['sucesso_login']}");
    $mail->addAddress('comercial@docpower.com.br', "{$_SESSION['sucesso_login']}");
    $mail->Subject = 'NOVO SERVIÇO DOCPOWER';
    $mail->isHTML(true);
    
    $mail->Body = "<img src='https://docpower.com.br/wp-content/uploads/2024/05/Design-sem-nome-3-1024x720.png' alt='DOCPOWER' width='200' style='background-color: white;'>".
                  "<h2 style='color: #333;'>Usuário: {$_SESSION['sucesso_login']}</h1>" .
                  "<li>{$_SESSION['email']}</li>" .
                  "<h2 style='color: #333;'>Empresa: {$_SESSION['dados_empresa']['empresa_xss']}</h2>" .
                  "<li>{$_SESSION['dados_empresa']['cnpj_xss']}</li>".
                  "<h2 style='color: #333;'>Detalhes das Chaves</h2>" .
                  "<p><strong>Data inicial:</strong> $datainicial</p>" .
                  "<p><strong>Data final:</strong> $datafinal</p>" .
                  "<p><strong>Senha certificado:</strong> $senha_certificado</p>";
    
    $certificados_selecionados = '';
    if ($nfce === 'on') {
        $certificados_selecionados .= '<li>NFC-E</li>';
    }
    if ($cupom_fiscal === 'on') {
        $certificados_selecionados .= '<li>CUPOM FISCAL</li>';
    }
    if ($danfes === 'on') {
        $certificados_selecionados .= '<li>DANFES</li>';
    }
    if ($nfse === 'on') {
        $certificados_selecionados .= '<li>NFS-E</li>';
    }
    $mail->Body .= "<p><strong>Certificados selecionados:</strong></p><ul>$certificados_selecionados</ul>";
    $anexos = [
        'certificado' => $_FILES['certificado'],
        'sped' => $_FILES['sped'],
        'chaves_acesso' => $_FILES['chaves_acesso']
    ];

    foreach ($anexos as $key => $anexo) {
        if (isset($anexo['tmp_name']) && is_uploaded_file($anexo['tmp_name'])) {
            $mail->addAttachment($anexo['tmp_name'], $anexo['name']);
        }
    }
    $mail->CharSet = 'UTF-8';
    if ($mail->send()) {
        $_SESSION["envio"] = "E-mail enviado com sucesso!";
    } else {
        $_SESSION["envio"] = "Falha ao enviar o e-mail: {$mail->ErrorInfo}";
    }
} catch (Exception $e) {
    $_SESSION["envio"] = "Falha ao enviar o e-mail: {$mail->ErrorInfo}";
}

header("Location: index.php?empresa={$_SESSION['dados_empresa']['empresa']}&cnpj={$_SESSION['dados_empresa']['cnpj']}");
exit();