<?php
require_once '../assets/php/config.php';
include("../assets/php/functions.php");
if(!isset($_SESSION['sucesso_login']) || $_SESSION['email'] !== 'admin@docpower.com.br' || $_SESSION['sucesso_login'] !== 'Administrador') {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['erros']['login'] = 'Movimento suspeito detectado!';
    header('Location: ../login');
    exit;
}
require '../assets/php/delete.php';
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
<body id="iau9">
  <header id="ici8f7" class="cabecalho">
    <div class="cabecalho-back">
      <h3 id="nick" class="gjs-heading"><i class="bx bxs-user-rectangle"></i><?php echo $_SESSION['sucesso_login']; ?></h3>
    </div><img src="../assets/images/logo.png" class="logo"/>
    <!--Adicionar Logo-->
    <div class="sair"><i class="bx bxs-door-open"></i><a href="../assets/php/sair.php" class="gjs-link">Sair</a></div>
  </header>
  <section class="container">
    <!--Lado esquerdo-->
    <div class="esquerda">
      <div class="esquerdaback">
        <div class="opcoes">
          <h3 class="gjs-heading"><i class="bx bxs-box"></i>Solicitações:</h3><a class="opcoes-ativa">Ver todos</a>
        </div>
      </div>
    </div>
    <!--Formulário-->
    <div id="iuaorl" class="todos">
      <div id="ij1pdr" class="geral">
        <form method="POST" enctype="multipart/form-data" id="uploadForm" class="download" action="enviar.php">
          <?php 
          echo generateToken(); 

          if(isset($_SESSION["erros"]["upload"])) {
            echo "<h3 id='i2b861' class='gjs-heading'>{$_SESSION["erros"]["upload"]}</h3>";
          } else {
            echo "<h3 id='i2b861' class='gjs-heading'>Novos downloads:</h3>";
          }
          ?>
          <div id="ig9o6t" class="rolagem">
                 <?php require_once 'get_solicitacao.php'; ?>
          </div>
        </form>
        <div id="igy9ha" class="historico">
          <h3 class="gjs-heading">Histórico downloads:</h3>
          <div class="rolagem" id="iayfju">
            <?php require_once 'post_solicitacao.php'; ?>
          </div>
        </div>
      </div>
      <div class="atencao" id="i1u0jn">
        <h3 class="gjs-heading" id="i4wlxf">ATENÇÃO</h3>
        <p id="icb95k">Seus XML’s baixados tem um prazo de até 30 dias para você salvar em seu dispositivo, após o
          período ele será
          excluído automaticamente da plataforma!</p>
      </div>
    </div>
  </section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('uploadButton').addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });

    document.getElementById('fileInput').addEventListener('change', function() {
        document.getElementById('uploadForm').submit();
    });
});

</script>
</body>
</html>