<?php 
	require 'funcs/conexion.php';
    include 'funcs/funcs.php';

		require 'vendor/autoload.php';		

    $errors = array();

    if(!empty($_POST))
	{
        global $email;
        $usuario = $mysqli->real_escape_string($_POST['nombre']);
        $email = $mysqli->real_escape_string($_POST['email']);
		$password = $mysqli->real_escape_string($_POST['password']);
		$con_password = $mysqli->real_escape_string($_POST['confirm']);
		$activo = 0;
        $tipo_usuario = 2;

        if(isNull($usuario, $email, $password, $con_password))
		{
			$errors[] = "Debe llenar todos los campos";
        }

        if(!isEmail($email))
		{
			$errors[] = "Dirección de correo inválida";
        }

        if(!validaPassword($password, $con_password))
		{
			$errors[] = "Las contraseñas no coinciden";
        }

        if(emailExiste($email))
		{
			$errors[] = "El correo electronico $email ya existe";
        }

        if(count($errors) == 0)
		{
        $pass_hash = hashPassword($password);
        $token = generateToken();
        $registro = registraUsuario($usuario, $email, $pass_hash, $activo, $token, $tipo_usuario);	

            if($registro > 0)
			{				
				$url = 'http://'.$_SERVER["SERVER_NAME"].'/GESTIONDESCARGAS/activar.php?id='.$registro.'&val='.$token;
				$asunto = 'Activar Cuenta - Gestion de Descargas';
				$cuerpo = "Estimado $usuario: <br /><br />Para continuar con el proceso de registro, es indispensable de click en el siguiente link <a href='$url'>Activar Cuenta</a>";
					
					if(enviarEmail($email, $usuario, $asunto, $cuerpo)){
                        trasladarEmail($email);
                        header("Location:instrucciones.php?email=$email");
						exit;
						} else {
						$errors[] = "Error al enviar Email";
					}
					
			} else {
            $errors[] = "Error al Registrar";
			}
		} 
    }


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Registrarse | Dashboard de Descargas</title>
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

<body class="signup-page">
    <div class="signup-box">
        <div class="logo">
            <a href="javascript:void(0);">Dashboard<b>Descargas</b></a>
            <small>Gestion de Archivos Online</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="sign_up" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="msg">Registrar un nuevo usuario</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="nombre" placeholder="Nombre Completo" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" minlength="6" placeholder="Contraseña" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="confirm" minlength="6" placeholder="Confirmar contraseña" required>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <input type="checkbox" name="terms" id="terms" class="filled-in chk-col-pink">
                        <label for="terms">He leído y acepto todos los <a href="javascript:void(0);">terminos de uso</a>.</label>
                    </div> -->

                    <button class="btn btn-block btn-lg bg-pink waves-effect" type="submit">REGISTRARSE</button>

                    <div class="m-t-25 m-b--5 align-center">
                        <a href="index.php">Ya estas registrado?</a>
                    </div>
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
    <script src="js/pages/examples/sign-up.js"></script>
</body>

</html>