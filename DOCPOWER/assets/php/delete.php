<?php
include("conexao.php");

// Constante para o diretório de arquivos
define("ARQUIVOS_DIR", "../data/arquivos/");

function rmdir_recursive($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!rmdir_recursive($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}

try {
    $conexao = connect();

    // Consulta SQL para obter as solicitações fora da validade (mais de 1 minuto) e com valido = 1
    $stmt = $conexao->prepare("SELECT solicitacoes_id FROM historico WHERE TIMESTAMPDIFF(MINUTE, validade, NOW()) > 2 AND valido = 1");
    $stmt->execute();
    $solicitacoes_fora_validade = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Percorre as solicitações fora da validade
    foreach ($solicitacoes_fora_validade as $solicitacao) {
        // Consulta SQL para obter o usuario_id, cnpj, e razao_social da solicitação
        $stmt_soli = $conexao->prepare("SELECT usuario_id, cnpj, razao_social FROM solicitacoes WHERE id = :solicitacao_id");
        $stmt_soli->bindParam(':solicitacao_id', $solicitacao['solicitacoes_id'], PDO::PARAM_INT);
        $stmt_soli->execute();
        $solicitacao_info = $stmt_soli->fetch(PDO::FETCH_ASSOC);

        // Verifica se a solicitação foi encontrada
        if (!$solicitacao_info) {
            continue; // Pula para a próxima iteração se a solicitação não foi encontrada
        }

        // Consulta SQL para obter o nome do usuário
        $stmt_usuario = $conexao->prepare("SELECT nome FROM usuario WHERE id = :usuario_id");
        $stmt_usuario->bindParam(':usuario_id', $solicitacao_info['usuario_id'], PDO::PARAM_INT);
        $stmt_usuario->execute();
        $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

        // Verifica se o usuário foi encontrado
        if (!$usuario) {
            continue; // Pula para a próxima iteração se o usuário não foi encontrado
        }

        // Atualiza o campo valido para 0 na tabela historico
        $stmt = $conexao->prepare("UPDATE historico SET valido = 0 WHERE solicitacoes_id = :id");
        $stmt->bindParam(':id', $solicitacao['solicitacoes_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Define o caminho do diretório a ser excluído
        $directory = ARQUIVOS_DIR . "{$usuario['nome']} - {$solicitacao_info['usuario_id']}/{$solicitacao_info['cnpj']}/{$solicitacao_info['razao_social']}/{$solicitacao['solicitacoes_id']}/";

        // Verifica se o diretório existe antes de excluir
        if (is_dir($directory)) {
            // Exclui o diretório e todo o seu conteúdo
            if (!rmdir_recursive($directory)) {
                echo "Falha ao excluir o diretório: $directory";
            }
        } else {
            echo "Diretório não encontrado: $directory";
        }
    }
} catch (PDOException $e) {
    die('Erro ao buscar solicitações: ' . $e->getMessage());
}