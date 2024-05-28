<?php
require_once '../assets/php/config.php';
include "../assets/php/conexao.php";
include "../assets/php/functions.php";

$token = $_POST['csrf_token'];
checkToken($token);

// Função para verificar duplicatas no banco de dados
function checkDuplicate($conexao, $field, $value) {
    $stmt = $conexao->prepare("SELECT id FROM usuario WHERE $field = ?");
    $stmt->execute([$value]);
    $isDuplicate = $stmt->rowCount() > 0;
    $stmt->closeCursor(); // Fechar cursor
    return $isDuplicate;
}

// Sanitizar e validar entradas
$documento = sanitize($_POST['documento']);
$nome = sanitize($_POST['nome']);
$telefone = sanitize($_POST['telefone']);
$email = sanitize($_POST['email']);
$senha = sanitize($_POST['senha']);
$senha_confirmacao = sanitize($_POST['senha_check']);
$termos = isset($_POST['termos']) ? $_POST['termos'] : null;

// Inicializar array de erros
$erros = [];
// DDD's válidos
$dddsValidos = ['11', '12', '13', '14', '15', '16', '17', '18', '19', '21', '22', '24', '27', '28', '31', '32', '33', '34', '35', '37', '38', '41', '42', '43', '44', '45', '46', '47', '48', '49', '51', '53', '54', '55', '61', '62', '63', '64', '65', '66', '67', '68', '69', '71', '73', '74', '75', '77', '79', '81', '82', '83', '84', '85', '86', '87', '88', '89', '91', '92', '93', '94', '95', '96', '97', '98', '99'];

// Validação do documento
if (empty($documento)) {
    $erros['documento'] = 'Você deve preencher este campo.';
} else {
    // Verificar se o documento contém apenas números, pontos, barras e traços
    if (!preg_match('/^[\d.\-\/]+$/', $documento)) {
        $erros['documento'] = 'Documento inválido. Use apenas números, pontos, barras e traços.';
    } else {
        if (!$documento = formatDOCU($documento)) {
            $erros['documento'] = 'Documento inválido. Deve ser um CPF ou CNPJ válido.';
        }
    }
}

// Validação do telefone
if (empty($telefone)) {
    $erros['telefone'] = 'Telefone é obrigatório.';
} else {
    if (!preg_match('/^[\d().\-]+$/', $telefone)) {
        $erros['telefone'] = 'Telefone inválido. Use apenas números, parênteses e traços.';
    } else {
        $telefone = preg_replace('/\D/', '', $telefone);
        $ddd = substr($telefone, 0, 2);

        if (!in_array($ddd, $dddsValidos)) { // Verifica se o DDD é válido (códigos de DDD válidos no Brasil)
            $erros['telefone'] = 'DDD inválido.';
        } elseif (!$telefone = Telefone($telefone)) {
            $erros['telefone'] = 'Telefone inválido.';
        }
    }
}

// Validação do nome
if (empty($nome)) {
    $erros['nome'] = 'Nome é obrigatório.';
}

// Validação do email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros['email'] = 'Email inválido.';
}

// Validação da senha
if (empty($senha) || strlen($senha) < 8 || strlen($senha) > 32) {
    $erros['senha'] = 'Senha inválida. Deve conter entre 8 e 32 caracteres.';
}

// Validação da confirmação da senha
if ($senha != $senha_confirmacao || empty($senha_confirmacao)) {
    $erros['senha_check'] = 'As senhas não coincidem.';
}

if (empty($termos)) {
    $erros['termos'] = 'Aceite os termos antes de prosseguir.';
}

// Verificação de duplicatas no banco de dados
if (empty($erros)) {
    if (checkDuplicate($conexao, 'email', $email)) {
        $erros['email'] = 'Este email já está em uso.';
    }

    if (checkDuplicate($conexao, 'documento', $documento)) {
        $erros['documento'] = 'Este documento já está em uso.';
    }

    if (checkDuplicate($conexao, 'telefone', $telefone)) {
        $erros['telefone'] = 'Este telefone já está em uso.';
    }
}

// Inserção de dados no banco de dados se não houver erros
if (empty($erros)) {
    $options = ['cost' => 13];
    $hashed_password = password_hash($senha, PASSWORD_ARGON2ID, $options);

    $stmt = $conexao->prepare("INSERT INTO usuario (documento, nome, telefone, email, senha, data_cadastro) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$documento, $nome, $telefone, $email, $hashed_password]);

    if ($stmt->rowCount() > 0) {
        $stmt->closeCursor();
        $conexao = null; // Fechar conexão
        header('Location: ../login/index.php');
        exit;
    } else {
        $erros['erro_cadastro'] = 'Falha ao cadastrar. Por favor, tente novamente.';
        exit;
    }
} 

if (!empty($erros)) {
    $stmt->closeCursor();
    $conexao = null; // Fechar conexão
    mensagem_erro($erros, '../register');
    exit;
}