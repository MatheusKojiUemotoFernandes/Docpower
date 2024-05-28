<?php
require_once '../assets/php/config.php';
include("../assets/php/functions.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="icon" href="https://docpower.com.br/wp-content/uploads/2024/05/Design-sem-nome-6-150x150.png" sizes="32x32">
    <link rel="icon" href="https://docpower.com.br/wp-content/uploads/2024/05/Design-sem-nome-6-300x300.png" sizes="192x192">
    <link rel="stylesheet" href="../assets/style.css">
    
    <title>Registro</title>
</head>

<body>
    <div class="container">
        <section class="header">
            <?php
                if (isset($_SESSION['erros']['erro_cadastro'])){
                    echo '<h2 id="status">'.$_SESSION['erros']['erro_cadastro'].'</h2>';
                    unset($_SESSION['erros']['erro_cadastro']);
                } else {
                    echo '<h2>Cadastrar:</h2>';
                }
            ?>
        </section>
        <form action="registro.php" method="POST" class="form" id="form">
            <?php echo generateToken(); ?>
            <div class="form-content">
                <label></label>
                    <input
                        name="documento"
                        type="text"
                        id="docu"
                        placeholder="CNPJ/CPF:"
                    />
                    <?php
                        if(isset($_SESSION['erros']['documento'])) {
                            echo '<a id="error">'.$_SESSION['erros']['documento'].'</a>';
                            unset($_SESSION['erros']['documento']);
                        }
                    ?>
            </div>
            <div class="form-content">
                <label></label>
                    <input
                        name="nome"
                        type="text"
                        id="nome"
                        placeholder="Nome completo:"
                        
                    />
                    <?php
                        if(isset($_SESSION['erros']['nome'])) {
                            echo '<a id="error">'.$_SESSION['erros']['nome'].'</a>';
                            unset($_SESSION['erros']['nome']);
                        }
                    ?>
            </div>

            <div class="form-content">
                <label></label>
                    <input
                        name="telefone"
                        type="text"
                        id="tele"
                        placeholder="Telefone:"
                        
                    />
                    <?php
                        if(isset($_SESSION['erros']['telefone'])) {
                            echo '<a id="error">'.$_SESSION['erros']['telefone'].'</a>';
                            unset($_SESSION['erros']['telefone']);
                        }
                    ?>
            </div>

            <div class="form-content">
                <label></label>
                    <input
                        name="email"
                        type="email"
                        id="email"
                        placeholder="E-mail:"
                        
                    />
                    <?php
                        if(isset($_SESSION['erros']['email'])) {
                            echo '<a id="error">'.$_SESSION['erros']['email'].'</a>';
                            unset($_SESSION['erros']['email']);
                        }
                    ?>
            </div>

            <div class="form-content">
                <label></label>
                    <input
                        name="senha"
                        type="password"
                        id="password"
                        placeholder="Senha:"
                        
                    />
                    <?php
                        if(isset($_SESSION['erros']['senha'])) {
                            echo '<a id="error">'.$_SESSION['erros']['senha'].'</a>';
                            unset($_SESSION['erros']['senha']);
                        }
                    ?>
            </div>

            <div class="form-content">
                <label></label>
                    <input
                        name="senha_check"
                        type="password"
                        id="password-confirmation"
                        placeholder="Confirme sua senha:"
                        
                    />
                    <?php
                        if(isset($_SESSION['erros']['senha_check'])) {
                            echo '<a id="error">'.$_SESSION['erros']['senha_check'].'</a>';
                            unset($_SESSION['erros']['senha_check']);
                        }
                    ?>
            </div>
            <div class="form-content">
                <label>
                    <div class="termos">
                        <input 
                            type="checkbox" 
                            class="check" 
                            name="termos" 
                            
                        />
                        <p>Eu aceito os <a id="termos" href="../data/termos.pdf" target="_blank">termos</a></p>
                    </div>
                </label>
                    <?php
                        if(isset($_SESSION['erros']['termos'])) {
                            echo '<a id="error">'.$_SESSION['erros']['termos'].'</a>';
                            unset($_SESSION['erros']['termos']);
                        }
                    ?>
            </div>
            <button type="submit"><span>Criar conta</span></button>
        </form>
        <div class="register-link">
            <p>Já cadastrado? <a href="../login">Acessar conta</a></p>
        </div>
    </div>
</body>
</html>