<?php 
	session_start();
	require 'funcs/conexion.php';
    include 'funcs/funcs.php';
	//Vericacion de session
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
    

    $errors = array();
    if(!empty($_POST)){
        $idUI=$mysqli->real_escape_string($_POST['idUsuarioUI']);
        $nombreUI = $mysqli->real_escape_string($_POST['nombreUI']);
        $emailUI = $mysqli->real_escape_string($_POST['emailUI']);
        $passwordUI = $mysqli->real_escape_string($_POST['passwordUI']);
        $mensaje="";

        if(empty($nombreUI) || !is_string($nombreUI)){
            $errors[]="Nombre vacio o incorrecto <br/>";
        }
        if(!is_string($emailUI) || !filter_var($emailUI,FILTER_VALIDATE_EMAIL)){
            $errors[]="Email Invalido <br/>";
        }
        if(empty($passwordUI) || strlen(($passwordUI)<6)){
            $errors[]="Contraseña incorrecta, debe tener minimo 6 caracteres <br/>";
        }
        if(count($errors) == 0){
            $result_actualizar=mysqli_query($mysqli,"UPDATE usuarios SET usuario='$nombreUI', 
                                                                         email='$emailUI', 
                                                                         contrasena='$passwordUI'
                                                                        WHERE idUsuario='$idUI'")
                                or die("Error en la consulta: ".mysqli_error($mysqli));
            if($result_actualizar){
                $query_newDatos="SELECT * FROM usuarios WHERE idUsuario='$idUI'";
                $result_newDatos = $mysqli->query($query_newDatos);
                $row_newDatos = $result_newDatos->fetch_assoc();
                $nombre_new=$row_newDatos['usuario'];
                $email_new=$row_newDatos['email'];
                $pass_new=$row_newDatos['contrasena'];

                $url = 'http://'.$_SERVER["SERVER_NAME"].'/GESTIONDESCARGAS/index.php';
				$asunto = 'Modificacion de Datos de Usuario - Gestion de Descargas';
                $cuerpo = "Estimado $nombre_new: <br/>Sus datos han sido modificados por el administrador exitosamente, sus nuevos datos de acceso son los siguientes:<br/>
                           Nombre:<b>$nombre_new</b><br>
                           Email:<b>$email_new</b><br>
                           Clave:<b>$pass_new</b><br>
                           
                           Puede ingresar con sus datos haciendo click aqui: <a href='$url'>Iniciar Session</a>";
                           if(enviarEmail($email_new, $nombre_new, $asunto, $cuerpo, $pass_new)){
                                $resultado=1;
                                $msn="Datos Modificados Exitosamente, se ha enviado un email de notificación al usuario!";
                           }else{
                                $resultado=3;
                                $msn="Datos Modificados, problemas al enviar email. ";
                           }
            }else{
                $resultado=2;
                $msn="Error al Modificar datos del usuario.";
            }
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
                                MODIFICAR DATOS DE USUARIOS
                            </h2>
                        </div>
                        <div class="body">
                            <?php 
                            if(isset($resultado) && $resultado==1){?>
                                <div class="alert alert-info">
                                <strong>FELICIDADES!</strong> <?php echo  $msn ?>.
                                <a type="button" class="btn btn-default" href="usuarios.php">ACEPTAR</a>
                                </div>
                            <?php } 
                            elseif(isset($resultado) && $resultado==2){?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong> <?php echo  $msn ?>.
                                <a type="button" class="btn btn-default" href="usuarios.php">ACEPTAR</a>
                                </div>
                            <?php }
                            elseif(isset($resultado) && $resultado==3){?>
                                <div class="alert alert-warning">
                                <strong>ERROR</strong> <?php echo  $msn ?>.
                                <?php echo resultBlock($errors); ?>
                                <a type="button" class="btn btn-default" href="usuarios.php">ACEPTAR</a>
                                </div>
                            <?php 
                            }elseif(!isset($resultado)){?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong>
                                <?php echo resultBlock($errors); ?>
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