<?php 
	session_start();
    	require 'funcs/conexion.php';
        include 'funcs/funcs.php';
        $errors = array();
        $mensaje="";

        if(!isset($_SESSION["id_usuario"])){ //Si no ha iniciado sesión redirecciona a index.php
            header("Location: index.php");
        }
        
        $idUsuario = $_SESSION['id_usuario'];
        $sql = "SELECT idUsuario, usuario, email FROM usuarios WHERE idUsuario = '$idUsuario'";
        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        $logueado=$row['usuario'];
        $email=$row['email'];
        $idUsuario=$row['idUsuario'];

    if(!empty($_POST))
	{
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
 
        if(count($errors) ==0)

		{
        // $pass_hash = hashPassword($password);
        $token = generateToken();

        $registro = registraUsuario($usuario, $email, $password, $activo, $token, $tipo_usuario);


            if($registro > 0)
			{		
	
				$url = 'http://'.$_SERVER["SERVER_NAME"].'/GESTIONDESCARGAS/activar.php?id='.$registro.'&val='.$token;
				$asunto = 'Registro de Usuario - Gestion de Descargas';
                $cuerpo = "Estimado $usuario: <br/><br />
                        Usted ha sido registrado exitosamente en nuestra plataforma con los siguientes datos:<br>
                            Email: $email<br>
                            Clave: $password<br>
                        Para continuar con el proceso de registro, debe activar su cuenta haciendo click en el siguiente link: <a href='$url'>Activar Cuenta</a>";
					
					if(enviarEmail($email, $usuario, $asunto, $cuerpo, $password)){
                        $mensaje="Usuario registrado exitosamente";
                        $resultado=1;
						} else {
                        $resultado=2;    
                        $errors[] = "Error al enviar Email";
                        $mensaje= "Error al enviar Email";
					}
					
			} else {
                $resultado=3;
                $errors[] = "Error al Registrar";
                $mensaje= "Error al Registrar";

                }
		} 

if(isset($_GET['resultado'])){
$resultado=$_GET['resultado'];
}
	} 
    include 'templates/header.php';
?>
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
            </div>
            <!-- Basic Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                REGISTRAR USUARIOS
                            </h2>
                        </div>
                        <div class="body">
                            <?php 
                            if(isset($resultado) && $resultado==1){?>
                                <div class="alert alert-info">
                                <strong>FELICIDADES!</strong> <?php echo  $mensaje ?>.
                                <a type="button" class="btn btn-default" href="usuarios.php">ACEPTAR</a>
                                </div>
                            <?php } 
                            elseif(isset($resultado) && $resultado==2 ||isset($resultado) && $resultado==3){?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong> <?php echo  $mensaje ?>.
                                <a type="button" class="btn btn-default" href="usuarios.php">ACEPTAR</a>
                                </div>
                            <?php }
                            elseif(!isset($resultado)){?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong><br> <?php foreach($errors as $e){echo $e.'<br>';}?>
                                <br>
                                <a type="button" class="btn btn-default" href="usuarios.php">ACEPTAR</a>
                                </div>
                            <?php }
                            
                            ?>

                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Table -->
        </div>
    </section>

<?php 
include 'templates/footer.php' 
?>