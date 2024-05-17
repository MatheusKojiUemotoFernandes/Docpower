<?php
require_once '../assets/php/config.php';
if(!isset($_SESSION['sucesso_login'])) {
    header('Location: ../login/index.php');
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="https://docpower.com.br/wp-content/uploads/2024/05/Design-sem-nome-6-150x150.png" sizes="32x32">
    <link rel="icon" href="https://docpower.com.br/wp-content/uploads/2024/05/Design-sem-nome-6-300x300.png" sizes="192x192">
    <title>DOCPOWER</title>
</head>

<body>
    <!--Cabeçalho-->
    <header class="cabecalho">
        <div class="cabecalho-back">
            <h3 class="nick"><i class='bx bxs-user-rectangle'></i><?php echo $_SESSION['sucesso_login']; ?></h3>
        </div>

        <img class="logo" src="../assets/images/logo.png"/><!--Adicionar Logo-->
        <div class="ok"></div>
    </header>
    <!--Corpo-->
    <section class="container">
        <!--Lado esquerdo-->
        <div class="esquerda">
            <div class="esquerdaback">
                <div class="opcoes">
                    <h3><i class='bx bxs-school'></i>Cadastro:</h3>
                    <a class='opcoes-ativa'>Incluir cadastro</a>
                    <a>Alterar cadastro</a>
                    <h3><i class='bx bxs-box'></i>Download:</h3>
                    <a>Novos arquivos</a>
                    <a>Ver todos...</a>
                    <h3><i class='bx bxs-book'></i>Empresas:</h3>
                        <div class="rolagem">
                            <?php
                                if(isset($_SESSION['nomes_empresas']) && is_array($_SESSION['nomes_empresas'])) {
                                    $num_empresas = count($_SESSION['nomes_empresas']);
                                    for ($i = 0; $i < $num_empresas; $i++) {
                                        echo "<a href='../empresa/index.php?empresa={$_SESSION['nomes_empresas'][$i]}'>{$_SESSION['nomes_empresas'][$i]} ({$_SESSION['cnpj_empresas'][$i]})</a>";
                                    }
                                }
                            ?>
                        </div>
                </div>
            </div>
        </div>
        <!--Formulário e vídeo-->
        <div class="central">
            <div class="formularioback">
                <form id="formulario" class="formulario" action="cadastrar.php" method="POST" enctype="multipart/form-data">
                    <?php
                        if(isset($_SESSION['erros']['form'])) {
                            echo '<h3 id="error">'.$_SESSION['erros']['form'].'</h3>';
                        } elseif (isset($_SESSION['sucesso_cadastro'])){
                            echo '<h3>'.$_SESSION['sucesso_cadastro'].'</h3>';
                            unset($_SESSION['sucesso_cadastro']);
                        } else {
                            echo '<h3>Cadastre novas empresas aqui:</h3>';
                        }
                    ?>
                        <div class="formularioflex">
                            <label for="cnpj">
                                <input name="cnpj" type="text" id="informacao" placeholder="CNPJ:">
                                <?php
                                    if(isset($_SESSION['erros']['cnpj'])) {
                                        echo '<span id="error">'.$_SESSION['erros']['cnpj'].'</span>';
                                        unset($_SESSION['erros']['cnpj']);
                                    }
                                ?>
                            </label>
    
                            <label for="razao">
                                <input name="razao" type="text" id="informacao" placeholder="RAZÃO SOCIAL:">
                                <?php
                                    if(isset($_SESSION['erros']['razao'])) {
                                        echo '<span id="error">'.$_SESSION['erros']['razao'].'</span>';
                                        unset($_SESSION['erros']['razao']);
                                    }
                                ?>
                            </label>
                        </div>

                        <div class="formularioflex">
                            <label for="cidade">
                                <input name="cidade" type="text" id="informacao" placeholder="CIDADE:">
                                <?php
                                    if(isset($_SESSION['erros']['cidade'])) {
                                        echo '<span id="error">'.$_SESSION['erros']['cidade'].'</span>';
                                        unset($_SESSION['erros']['cidade']);
                                    }
                                ?>
                            </label>

                            <label for="uf">
                                <input name="uf" type="text" id="uf" placeholder="UF:">
                                <?php
                                    if(isset($_SESSION['erros']['uf'])) {
                                        echo '<span id="error">'.$_SESSION['erros']['uf'].'</span>';
                                        unset($_SESSION['erros']['uf']);
                                    }
                                ?>
                            </label>
                        </div>

                        <div class="formularioflexarq">
                            <div class="baixar">
                                <a class="baixarcontrato" href="../data/contrato.pdf" target="_blank">Baixar contrato</a>
                            </div>

                            <div class="subir">
                                <div class="subircontrato">
                                    <label>
                                        <input id="inputsubirarquivo" type="file" name="contrato_assinado" accept="application/pdf"/>
                                        <span id="contrato_arquivo">Subir Contrato</span>
                                    </label>
                                </div>
                                <?php
                                    if(isset($_SESSION['erros']['contrato_assinado'])) {
                                        echo '<span id="error">'.$_SESSION['erros']['contrato_assinado'].'</span>';
                                        unset($_SESSION['erros']['contrato_assinado']);
                                    }
                                ?>
                            </div>
                        </div>

                    <button class="cadastrarcliente" type="submit">
                        Cadastrar cliente
                    </button>

                </form>
            </div>
            <div class="video">

            </div>
        </div>
        <!--Lado direito-->
        <div class="direita">
            <div class="flexdireita">

                <div class="nuvem">
                    <div class="total-xml">
                        <i class='bx bxs-archive-in'></i>
                        <h3>Novos XML's</h3>
                        <p>0</p>
                        <button type="button">Ver novos</button>
                    </div>
                </div>

                <div class="predios">
                    <div class="total-empresa">
                        <i class='bx bxs-business' ></i>
                        <h3>Total empresas</h3>
                            <?php
                                echo '<p>'.$_SESSION['num_empresas'].'</p>';
                            ?>
                        <button type="button">Ver todos</button>
                    </div>
                </div>

            </div>
            <div class="chaves">
                <div class="icone"><i class='bx bxs-key'></i></div>
                <div class="total-chaves">
                    <?php
                        echo '<p>'.$_SESSION['chaves'].'</p>';
                    ?>
                    <h3>Total chaves</h3>
                    <button type="button">Comprar chaves</button>
                </div>
            </div>

            <div class="atencao">
                <h3>ATENÇÃO!</h3>
                <p>Seus XML’s baixados tem um prazo de até 30 dias para você salvar em seu dispositivo, após o período ele será excluído automaticamente da plataforma!</p>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function handleFileChange(event, outputElementId, defaultText) {
                const file = event.target.files[0];
                const outputElement = document.getElementById(outputElementId);
                if (file) {
                    outputElement.textContent = file.name;
                } else {
                    outputElement.textContent = defaultText;
                }
            }

            const contratoInput = document.getElementById('inputsubirarquivo');

            if (contratoInput) {
                contratoInput.addEventListener('change', function(event) {
                    handleFileChange(event, 'contrato_arquivo', 'Subir Contrato');
                });
            }
        });
    </script>
</body>
</html>