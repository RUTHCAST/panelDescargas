<?php 
	require 'funcs/conexion.php';
    include 'funcs/funcs.php';
    session_start(); //Iniciar una nueva sesión o reanudar la existente
    if(isset($_SESSION["id_usuario"])){ //En caso de existir la sesión redireccionamos
		header("Location: profile.php");
    }
    if(isset($_COOKIE['id_user']) && isset($_COOKIE['marca'])){
        if($_COOKIE['id_user']!="" || $_COOKIE['marca']!=""){
            $stmt = $mysqli->prepare("SELECT IdUsuario, email, contrasena, cookie FROM usuarios WHERE idUsuario = ? AND cookie = ? LIMIT 1 ");
            $stmt->bind_param('ii', $_COOKIE["id_user"], $_COOKIE["id_user"]);
            $stmt->execute();
            $stmt->store_result();
            $num = $stmt->num_rows;
        }
        if($num>=1){
            $row_c = mysqli_fetch_array($sql_c);
            echo "El usuario ".$row_c['usuario']." se ha identificado correctamente.";
            $user_cookie = mysqli_fetch_array($sql_c);
        }
    }
	
	$errors = array();
	$recordar=0;
	if(!empty($_POST))
	{
		$email = $mysqli->real_escape_string($_POST['email']);
        $password = $mysqli->real_escape_string($_POST['password']);
        
        if(isset($_POST['recordar'])){
            $recordar = $mysqli->real_escape_string($_POST['recordar']);
        }
		
		if(isNullLogin($email, $password))
		{
			$errors[] = "Debe llenar todos los campos";
		}
		
        $errors[] = login($email, $password, $recordar);

	}
	    
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Ingresar | Dashboard de Descargas</title>
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

<body class="login-page" style="background-color:#000">
    <div class="login-box">
        <div class="logo">
            <a href="javascript:void(0);"><img src="images/logo.png" width="300"></a>
            <a href="javascript:void(0);">Dashboard<b>Descargas</b></a>
            <small>Gestion de Archivos Online</small>

        </div>
        <div class="card">
            <div class="body">
                <form id="sign_in" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="msg">Ingresa tus datos para iniciar sesion</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Email" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-8 p-t-5">
                            <input type="checkbox" name="recordar"" id="rememberme" class="filled-in chk-col-pink" value=1>
                            <label for="rememberme">Recordarme</label>
                        </div>
                        <div class="col-xs-4">
                            <button class="btn btn-block bg-red waves-effect" type="submit">INICIAR</button>
                        </div>
                    </div>
                    <!-- <div class="row m-t-15 m-b--20">
                        <div class="col-xs-6">
                            <a href="registrarse.php">Registrarse!</a>
                        </div>
                        <div class="col-xs-6 align-right">
                            <a href="recupera.php">Olvido su contraseña?</a>
                        </div>
                    </div> -->
                </form>
                <?php echo resultBlock($errors); ?>
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
    <script src="js/pages/examples/sign-in.js"></script>
</body>

</html>