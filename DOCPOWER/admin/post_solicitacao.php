<?php
require_once '../assets/php/config.php';
include("../assets/php/conexao.php");

// Constantes para diretórios
$erros = [];

try {
    $conexao = connect();

    $stmt = $conexao->prepare("SELECT id, usuario_id, razao_social, cnpj, data_finalizacao FROM solicitacoes WHERE arquivo = 1");
    $stmt->execute();
    $Solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if $Solicitacoes is empty
    if (empty($Solicitacoes)) {
        echo "Nenhuma solicitação encontrada.";
        return;
    }

    foreach ($Solicitacoes as $solicitacao) {

        $stmt = $conexao->prepare("SELECT * FROM historico WHERE solicitacoes_id = :solicitacao_id");
        $stmt->bindParam(':solicitacao_id', $solicitacao['id'], PDO::PARAM_INT);
        $stmt->execute();
        $VerificarHistorico = $stmt->rowCount();
        $Historico = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$Historico){
            return;
        }

        $stmt = $conexao->prepare("SELECT * FROM usuario");
        $stmt->execute();
        $Usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($Usuario) {
            $directory = ARQUIVOS_DIR . "{$Usuario["nome"]} - {$_SESSION['id']}/{$solicitacao["cnpj"]}/{$solicitacao["razao_social"]}/{$solicitacao["id"]}/";

            // Verifica se o diretório existe antes de tentar acessar
            if (is_dir($directory)) {
                $files = scandir($directory);

                // Verifica se há arquivos no diretório
                if (count($files) > 2) {
                    $fileName = $files[2];
                    $filePath = $directory . $fileName;
                    $a = "
                        <i class='bx bxs-cloud-download'></i>
                        <h3 class='gjs-heading' id='ikoz9j'><a href='../files/baixar.php?solicitacao_id=".$solicitacao["id"]."&arquivo=" . htmlspecialchars(urlencode($filePath)) . "' target='_blank'>Baixar!</a><br/></h3>
                        ";
                } else {
                    $a = " ";
                }
            } else {
                $a = " ";
            }
                echo "
                <div class='divisao' id='if1lwr'>
                <div class='espaco' id='ioyj1z'>
                  <div class='dados' id='i83754'>
                    <p>Razão social: " . $solicitacao['razao_social'] ."</p>
                    <p>CNPJ: " . htmlspecialchars($solicitacao['cnpj']) . "</p>
                    <p>Data de finalização: " . htmlspecialchars($solicitacao['data_finalizacao']) . "</p>
                  </div>
                  <div class='botao' id='i0pji4'>
                    <div class='icone' id='idlcnw'>
                        $a
                    </div>
                  </div>
                </div>
              </div>
                ";
        } else {
            echo "Usuário não encontrado.";
        }
    }

} catch (PDOException $e) {
    die('Erro ao buscar solicitações: ' . $e->getMessage());
}
