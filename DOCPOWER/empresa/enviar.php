<?php
require_once '../assets/php/config.php';
require '../assets/php/lib/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $datainicial = $_POST['datainicial'] ?? null;
    $datafinal = $_POST['datafinal'] ?? null;
    $senha_certificado = $_POST['senha_certificado'] ?? null;

    $nfce = $_POST['nfce'] ?? null;
    $cupom_fiscal = $_POST['cupom_fiscal'] ?? null;
    $danfes = $_POST['danfes'] ?? null;

    // Arquivos anexados
    $certificado = $_FILES['certificado'] ?? null;
    $sped = $_FILES['sped'] ?? null;
    $chaves_acesso = $_FILES['chaves_acesso'] ?? null;

    $dataInicialFormatada = DateTime::createFromFormat('d/m/Y', $datainicial);
    $dataFinalFormatada = DateTime::createFromFormat('d/m/Y', $datafinal);

    $erros = [];

    // Validação de campos obrigatórios
    if (empty($nfce) && empty($cupom_fiscal) && empty($danfes)) {
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

    // Se houver erros, redirecione de volta com os erros
    if (!empty($erros)) {
        $_SESSION["erros"] = $erros;
        header("Location: index.php?empresa={$_SESSION['empresa']}");
        exit();
    }

    // Inicialização do PHPMailer
    $mail = new PHPMailer(true);
    try {
        
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.titan.email';
        $mail->SMTPAuth = true;
        $mail->Username = 'comercial@docpower.com.br';
        $mail->Password = 'comercial@123';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Remetente e destinatário
        $mail->setFrom('comercial@docpower.com.br', "DOCPOWER - {$_SESSION['sucesso_login']}");
        $mail->addAddress('comercial@docpower.com.br', "{$_SESSION['sucesso_login']}");

        // Construção do corpo do e-mail
        $mail->Subject = 'NOVO SERVIÇO DOCPOWER';
        $mail->isHTML(true); // Define o tipo de conteúdo do e-mail como HTML
        
        $mail->Body = "<img src='https://docpower.com.br/wp-content/uploads/2024/05/Design-sem-nome-3-1024x720.png' alt='DOCPOWER' width='200' style='background-color: white;'>".
                      "<h2 style='color: #333;'>Usuário: {$_SESSION['sucesso_login']}</h1>" .
                      "<li>{$_SESSION['email']}</li>" .
                      "<h2 style='color: #333;'>Empresa: {$_SESSION['empresa']}</h2>" .
                      "<li>{$_SESSION['cnpj']}</li>".
                      "<h2 style='color: #333;'>Detalhes das Chaves</h2>" .
                      "<p><strong>Data inicial:</strong> $datainicial</p>" .
                      "<p><strong>Data final:</strong> $datafinal</p>" .
                      "<p><strong>Senha certificado:</strong> $senha_certificado</p>";
        
        // Adiciona certificados selecionados ao corpo do e-mail
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
        $mail->Body .= "<p><strong>Certificados selecionados:</strong></p><ul>$certificados_selecionados</ul>";

        $extencao_certificado = pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION);
        $extencao_sped = pathinfo($_FILES['sped']['name'], PATHINFO_EXTENSION);
        $extencao_chaves_acesso = pathinfo($_FILES['chaves_acesso']['name'], PATHINFO_EXTENSION);

        // Adiciona anexos ao e-mail
        $mail->addAttachment($_FILES['certificado']['tmp_name'], "certificado.$extencao_certificado");
        $mail->addAttachment($_FILES['sped']['tmp_name'], "sped.$extencao_sped");
        $mail->addAttachment($_FILES['chaves_acesso']['tmp_name'], "chaves_acesso.$extencao_chaves_acesso");

        $mail->CharSet = 'UTF-8';
        // Envio do e-mail
        if ($mail->send()) {
            $_SESSION["envio"] = "E-mail enviado com sucesso!";
        } else {
            $_SESSION["envio"] = "Falha ao enviar o e-mail: {$mail->ErrorInfo}";
        }
    } catch (Exception $e) {
        $_SESSION["envio"] = "Falha ao enviar o e-mail: {$mail->ErrorInfo}";
    }

    header("Location: index.php?empresa={$_SESSION['empresa']}&cnpj={$_SESSION['cnpj']}");
    exit();
}
