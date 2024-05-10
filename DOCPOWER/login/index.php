<?php
require_once '../assets/php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/style.css">
    
    <title>Login</title>
</head>

<body>
    <div class="container">
        <section class="header">
            <?php
                if(isset($_SESSION['sucesso_login'])) {
                    echo '<h2 id="status">'.$_SESSION['sucesso_login'].'</h2>';
                    unset($_SESSION['sucesso_login']);
                } elseif (isset($_SESSION['erro_login'])){
                    echo '<h2 id="status">'.$_SESSION['erro_login'].'</h2>';
                    unset($_SESSION['erro_login']);
                } else {
                    echo '<h2>Login:</h2>';
                }
                ?>
        </section>
        <form action="login.php" method="POST" class="form" id="form">
            <div class="form-content">
                <label for="email"></label>
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
                } else {
                    echo '<a>Aqui vai a mensagem de erro</a>';
                }
                ?>
            </div>

            <div class="form-content">
                <label for="password"></label>
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
                } else {
                    echo '<a>Aqui vai a mensagem de erro</a>';
                }
                ?>
            </div>
            <button type="submit"><span>Entrar</span></button>
        </form>
        <div class="register-link">
            <p>Não possui uma conta? <a href="../register">Criar Conta</a></p>
        </div>
    </div>
</body>
</html>