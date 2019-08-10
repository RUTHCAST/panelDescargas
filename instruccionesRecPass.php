<?php
    require 'funcs/conexion.php';
    $email=$_GET['email']; 
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Recuperar Contraseña | Dashboard de Descargas</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fp-page" style="background-color:#000">
    <div class="fp-box">
        <div class="logo">
            <a href="javascript:void(0);">Dashboard<b>Descargas</b></a>
            <small>Gestion de Archivos Online</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="forgot_password">
                    <div class="msg">
                    <?php   
                    echo "Para reestablecer tu contraseña hemos enviado un correo electronico a la siguiente direcion: ";
                    ?>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div  class="form-line aline text-center">
                            <label><?php echo $email?></label>
                        </div>
                    </div>
                    <a class="btn btn-block btn-lg bg-red waves-effect" href='index.php'>INICIAR SESSION</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/examples/forgot-password.js"></script>
</body>

</html>