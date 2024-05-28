<?php
require_once '../assets/php/config.php';
include("../assets/php/conexao.php");

try {
    $conexao = connect();


    // Preparar a consulta SQL para buscar solicitações onde a coluna 'status' é igual a 0
    $stmt = $conexao->prepare("SELECT id, usuario_id, razao_social, cnpj, data_finalizacao FROM solicitacoes WHERE arquivo = 0");
    $stmt->execute();

    // Buscar todas as linhas resultantes da consulta
    $solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($solicitacoes as $solicitacao) {
        echo "
        <div id='ieytcj' class='divisao'>
            <div id='i07u3f' class='espaco'>
                <div id='ih6rrz' class='dados'>
                    <input type='hidden' name='id_solicitacao' value='{$solicitacao['id']}'/>
                    <input type='hidden' name='usuario_id' value='{$solicitacao['usuario_id']}'/>
                    <input type='hidden' name='razao_social' value='{$solicitacao['razao_social']}'/>
                    <p>Razão social: " . $solicitacao['razao_social'] . "</p>
                    <input type='hidden' name='cnpj' value='{$solicitacao['cnpj']}'/>
                    <p>CNPJ: " . htmlspecialchars($solicitacao['cnpj']) . "</p>
                    <p>Data de finalização: " . htmlspecialchars($solicitacao['data_finalizacao']) . "</p>
                </div>
                <div id='uploadButton' class='botao'>
                    <div id='iukakk' class='icone'>
                        <i id='ik947r' class='bx bxs-cloud-download'></i>
                        <input type='file' id='fileInput' name='xml' accept='.xml' class='input'/>
                        <div class='upload-button'>
                            <h3 id='iaumde' class='gjs-heading'>Upload!</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
    }
} catch (PDOException $e) {
    die('Erro ao buscar solicitações: ' . $e->getMessage());
}