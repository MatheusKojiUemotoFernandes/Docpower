<?php
require_once '../assets/php/config.php';
include "../assets/php/conexao.php";
include "../assets/php/functions.php";

// Verificar se o formulário foi enviado com o método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar o token CSRF
    $token = $_POST['csrf_token'];
    checkToken($token);

    // Sanitizar e receber os dados
    $id_solicitacao = sanitize($_POST['id_solicitacao']);
    $usuario_id = sanitize($_POST['usuario_id']);
    $razao_social = sanitize($_POST['razao_social']);
    $cnpj = sanitize($_POST['cnpj']);
    
    // Verificar se o arquivo foi enviado e se não houve erros
    if (isset($_FILES['xml']) && $_FILES['xml']['error'] === UPLOAD_ERR_OK) {
        $xml = $_FILES['xml'];

        // Preparar consulta para obter dados do usuário
        $stmt = $conexao->prepare("SELECT * FROM usuario WHERE id = :id");
        $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $Usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Definir o caminho do diretório
        $caminhoArquivo = "../data/arquivos/{$Usuario["nome"]} - {$Usuario['id']}/{$cnpj}/{$razao_social}/{$id_solicitacao}/";
        
        // Verificar e criar o diretório se não existir
        if (!is_dir($caminhoArquivo)) {
            mkdir($caminhoArquivo, 0777, true); // Criar diretório recursivamente
        }

        // Definir o caminho completo do arquivo
        $caminhoCompleto = $caminhoArquivo . "documento.xml";
        
        // Mover o arquivo para o diretório de destino
        if (move_uploaded_file($xml['tmp_name'], $caminhoCompleto)) {
            echo "Arquivo enviado e salvo com sucesso.";

            // Atualizar a tabela solicitacoes
            $stmt = $conexao->prepare("UPDATE solicitacoes SET arquivo = 1 WHERE id = :id_solicitacao");
            $stmt->bindParam(':id_solicitacao', $id_solicitacao, PDO::PARAM_INT);
            $stmt->execute();

            echo "Solicitação atualizada com sucesso.";
        } else {
            echo "Erro ao salvar o arquivo.";
        }
    } else {
        echo "Erro no upload do arquivo: " . ($_FILES['xml']['error'] ?? 'Nenhum arquivo enviado');
    }
} else {
    echo "Método de requisição inválido.";
}