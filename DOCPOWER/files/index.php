<?php
require_once '../assets/php/config.php';
//include("../assets/php/functions.php");
if(!isset($_SESSION['sucesso_login'])) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['erros']['login'] = 'Movimento suspeito detectado!';
    header('Location: ../login');
    exit;
} else {
    require_once '../assets/php/getdata.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="https://docpower.com.br/wp-content/uploads/2024/05/Design-sem-nome-6-150x150.png" sizes="32x32">
    <link rel="icon" href="https://docpower.com.br/wp-content/uploads/2024/05/Design-sem-nome-6-300x300.png" sizes="192x192">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>DOCPOWER</title>
</head>

    <!--Cabeçalho-->
    <header class="cabecalho">
        <div class="cabecalho-back">
            <h3 id="nick"><i class='bx bxs-user-rectangle'></i><?php echo $_SESSION['sucesso_login']; ?></h3>
        </div>

        <img class="logo" src="../assets/images/logo.png"/><!--Adicionar Logo-->
        <div class="sair">
            <i class='bx bxs-door-open'></i>
            <a href="../assets/php/sair.php">Sair</a>
        </div>
    </header>

    <section class="container">
        <!--Lado esquerdo-->
        <div class="esquerda">
            <div class="esquerdaback">
                <div class="opcoes">
                    <h3><i class='bx bxs-school'></i>Cadastro:</h3>
                    <a href="../painel">Incluir cadastro</a>
                    <h3><i class='bx bxs-box'></i>Download:</h3>
                    <a class='opcoes-ativa'>Ver todos</a>
                    <h3><i class='bx bxs-book'></i>Empresas:</h3>
                    <div class="rolagem">
                        <?php
                            if (isset($_SESSION['nomes_empresas']) && is_array($_SESSION['nomes_empresas'])) {
                                $num_empresas = count($_SESSION['nomes_empresas']);
                                foreach ($_SESSION['nomes_empresas'] as $index => $nome) {
                                    if (isset($_SESSION['cnpj_empresas'][$index])) {
                                        echo "<a href='../empresa/index.php?empresa={$_SESSION['nomes_empresas'][$index]}&cnpj={$_SESSION['cnpj_empresas'][$index]}'>{$_SESSION['nomes_empresas'][$index]} ({$_SESSION['cnpj_empresas'][$index]})</a>";
                                    }
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!--Formulário-->
        <div class="todos">
            <div class="geral">
                <div class="download">
                    <h3>Novos downloads:</h3>
                    <div class="rolagem">
                        <div class="divisao">
                            <div class="espaco">
                                <div class="dados">
                                    <p>Razão social:</p>
                                    <p>CNPJ:</p>
                                    <p>Data de finalização:</p>
                                </div>
                                <div class="botao">
                                    <div class="icone">
                                        <i class='bx bxs-cloud-download'></i>
                                        <h3>Baixar!</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>

                <div class="historico">
                    <h3>Histórico downloads:</h3>
                    <div class="rolagem">
                        <div class="divisao">
                            <div class="espaco">
                                <div class="dados">
                                    <p>Razão social:</p>
                                    <p>CNPJ:</p>
                                    <p>Data de finalização:</p>
                                </div>
                                <div class="botao">
                                    <div class="icone">
                                        <i class='bx bxs-cloud-download'></i>
                                        <h3>Baixar!</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
            <div class="atencao">
                <h3>ATENÇÃO</h3>
                <p>Seus XML’s baixados tem um prazo de até 30 dias para você salvar em seu dispositivo, após o período ele será excluído automaticamente da plataforma!</p>
            </div>
        </div>

    </div>

    </section>