<?php
require_once '../assets/php/config.php';
require_once '../assets/php/getdata.php';
include("../assets/php/functions.php");

function sanitizeInput($input) {
    return htmlentities($input, ENT_QUOTES, 'UTF-8');
}

function isValidCompany($company, $cnpj) {
    return in_array($company, $_SESSION['nomes_empresas'], true) && in_array($cnpj, $_SESSION['cnpj_empresas'], true);
}

$vars = [
    'empresa_xss' => sanitizeInput($_GET['empresa'] ?? null),
    'cnpj_xss' => sanitizeInput($_GET['cnpj'] ?? null),
    'empresa' => $_GET['empresa'] ?? null,
    'cnpj' => $_GET['cnpj'] ?? null
];

$_SESSION["dados_empresa"] = $vars;

if (!isset($_SESSION['sucesso_login']) ||
    empty($_SESSION['dados_empresa']['empresa']) ||
    empty($_SESSION['dados_empresa']['cnpj']) ||
    !isValidCompany($_SESSION['dados_empresa']['empresa_xss'], $_SESSION['dados_empresa']['cnpj'])) {
    
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['erros']['login'] = 'Movimento suspeito detectado!';
    header('Location: ../login');
    exit;
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
    <?php
        echo "<title>".$_SESSION['dados_empresa']['empresa_xss']."</title>";
    ?>
</head>
<body>
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
                    <a href="../painel/">Incluir cadastro</a>
                    <!-- <a>Alterar cadastro</a> -->
                    <h3><i class='bx bxs-box'></i>Download:</h3>
                    <a href="../files">Ver todos</a>
                    <h3><i class='bx bxs-book'></i>Empresas:</h3>
                        <div class="rolagem">
                        <?php
                            if (isset($_SESSION['nomes_empresas']) && is_array($_SESSION['nomes_empresas'])) {
                                $num_empresas = count($_SESSION['nomes_empresas']);
                                foreach ($_SESSION['nomes_empresas'] as $index => $nome) {
                                    if (isset($_SESSION['cnpj_empresas'][$index])) {
                                        if($_SESSION['dados_empresa']['empresa_xss'] == $nome && $_SESSION['dados_empresa']['cnpj'] == $_SESSION['cnpj_empresas'][$index]){
                                            echo "<a class='opcoes-ativa' href='../empresa/index.php?empresa={$_SESSION['nomes_empresas'][$index]}&cnpj={$_SESSION['cnpj_empresas'][$index]}'>{$_SESSION['nomes_empresas'][$index]} ({$_SESSION['cnpj_empresas'][$index]})</a>";
                                        } else {
                                            echo "<a href='../empresa/index.php?empresa={$_SESSION['nomes_empresas'][$index]}&cnpj={$_SESSION['cnpj_empresas'][$index]}'>{$_SESSION['nomes_empresas'][$index]} ({$_SESSION['cnpj_empresas'][$index]})</a>";
                                        }
                                    }
                                }
                            }
                        ?>
                        </div>
                </div>
            </div>
        </div>

        <!--Formulário-->
        <div class="central">
            <div class="formularioback">
                <form id="formulario" class="formulario" action="enviar.php" method="POST" enctype="multipart/form-data">
                    <?php echo generateToken(); ?>
                    <?php
                    if(isset($_SESSION['envio'])){
                        echo '<h3>'.$_SESSION['envio'].'</h1>';
                        unset($_SESSION['envio']);
                    } else {
                        echo '<h3>'.$_SESSION['dados_empresa']['empresa_xss'].'</h3>';
                        echo '<h3>'.$_SESSION['dados_empresa']['cnpj_xss'].'</h3>';
                    }
                    ?>
                    <p>SELECIONE ALGUM SERVIÇO ABAIXO:</p>
                    <div class="checkboxflex">
                        <label for="servicos">
                            <span class="box">NFC-E</span>
                            <input 
                                type="checkbox" 
                                class="check" 
                                name="nfce"
                            />
                        </label>

                        <label for="servicos">
                            <span class="box">CUPOM FISCAL</span>
                            <input 
                                type="checkbox" 
                                class="check" 
                                name="cupom_fiscal"
                            />
                        </label>

                        <label for="servicos">
                            <span class="box">DANFES</span>
                            <input 
                                type="checkbox" 
                                class="check" 
                                name="danfes"
                            />
                        </label>
                        <label for="servicos">
                            <span class="box">NFS-E</span>
                            <input 
                                type="checkbox" 
                                class="check" 
                                name="nfse"
                            />
                        </label>

                            <?php
                                if(isset($_SESSION['erros']['checkbox'])) {
                                    echo '<label><span id="error">'.$_SESSION['erros']['checkbox'].'</span></label>';
                                    unset($_SESSION['erros']['checkbox']);
                                }
                            ?>
                    </div>
                    <div class="data">
                        <label for="datainicio">
                            <input
                                name="datainicial"
                                type="text"
                                id="datainicial"
                                placeholder="Data inicial"
                                pattern="\d{2}/\d{2}/\d{4}"
                                title="Digite uma data no formato dd/mm/yyyy"
                                
                            />
                        </label>
                        <label for="datafim">
                            <input
                                name="datafinal"
                                type="text"
                                id="datafinal"
                                placeholder="Data final"
                                pattern="\d{2}/\d{2}/\d{4}"
                                title="Digite uma data no formato dd/mm/yyyy"
                                
                            />
                        </label>
                            <?php
                                if(isset($_SESSION['erros']['data'])) {
                                    echo '<label><span id="error">'.$_SESSION['erros']['data'].'</span></label>';
                                    unset($_SESSION['erros']['data']);
                                }
                            ?>
                    </div>
                    <div class="documentoflex">
                        <label class="docuflex" tabindex="0">
                            <input 
                                id="certificado" 
                                name="certificado" 
                                type="file" 
                                class="inputcertificado" 
                                accept=".pfx,application/x-pkcs12,application/pkcs12,application/x-pkcs7-certificates,application/x-x509-ca-cert"
                                
                            />
                            <span id="certificado_arquivo">Inserir CERTIFICADO:</span>
                        </label>

                        <label class="docuflex" tabindex="0">
                            <input 
                                id="sped" 
                                name="sped" 
                                type="file" 
                                class="inputsped"
                                
                            />
                            <span id="sped_arquivo">Inserir SPED:</span>
                        </label>
                    </div>

                    <div class="senhacertificado">
                        <label class="senhac" for="senha">
                            <input 
                                type="text" 
                                id="senhace" 
                                placeholder="Senha CERTIFICADO:" 
                                name="senha_certificado"
                                
                            />
                        </label>
                    </div>
                    
                    <div class="chaveflex">
                        <label class="chaflex" tabindex="0">
                            <input 
                                id="chaves" 
                                type="file" 
                                class="inputcertificado" 
                                name="chaves_acesso" 
                                accept=".rar,.zip,.7z,.tar"
                                
                            />
                            <span id="chaves_arquivo">Inserir CHAVES DE ACESSO:</span>
                        </label>
                    </div>

                    <button class="executarser" type="submit">
                        Executar serviço
                    </button>
                </form>
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
                        <!-- <button type="button">Ver novos</button> -->
                    </div>
                </div>

                <div class="predios">
                    <div class="total-empresa">
                        <i class='bx bxs-business' ></i>
                        <h3>Total empresas</h3>
                        <?php
                            echo '<p>'.$_SESSION['num_empresas'].'</p>';
                        ?>
                        <!-- <button type="button">Ver todos</button> -->
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
    var dataAtual = new Date();

    // Formate a data como dd/mm/yyyy
    var dia = dataAtual.getDate();
    var mes = dataAtual.getMonth() + 1;
    var ano = dataAtual.getFullYear();
    
    if (dia < 10) {
        dia = '0' + dia;
    }
    if (mes < 10) {
        mes = '0' + mes;
    }

    document.getElementById('datainicial').value = dia + '/' + mes + '/' + ano;

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

        const chavesInput = document.getElementById('chaves');
        const spedInput = document.getElementById('sped');
        const certInput = document.getElementById('certificado');

        if (chavesInput) {
            chavesInput.addEventListener('change', function(event) {
                handleFileChange(event, 'chaves_arquivo', 'Inserir CHAVES DE ACESSO:');
            });
        }

        if (spedInput) {
            spedInput.addEventListener('change', function(event) {
                handleFileChange(event, 'sped_arquivo', 'Inserir SPED:');
            });
        }

        if (certInput) {
            certInput.addEventListener('change', function(event) {
                handleFileChange(event, 'certificado_arquivo', 'Inserir SPED:');
            });
        }
    });


    function formatDateInput(event) {
            const input = event.target;
            let value = input.value.replace(/\D/g, ''); // Remove tudo que não for dígito

            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            }
            if (value.length >= 5) {
                value = value.substring(0, 5) + '/' + value.substring(5, 9);
            }

            input.value = value;
        }

        document.getElementById('datainicial').addEventListener('input', formatDateInput);
        document.getElementById('datafinal').addEventListener('input', formatDateInput);
</script>

</body>
</html>