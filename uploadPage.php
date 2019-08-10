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

//recibimos los datos de la operacion anterior
if(!empty($_GET)){
    $resultado=$_GET['resultado'];
    $mensaje=$_GET['mensaje'];
}else{
    $mensaje="No se recibieron datos para archivar";
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
                                RESULTADO SUBIR ARCHIVOS
                            </h2>
                        </div>
                        <div class="body">
                            <?php if(isset($resultado) && $resultado==1){ ?>
                            <div class="alert alert-info">
                                <strong>FELICIDADES!</strong> <?php echo $mensaje;?>
                                <a type="button" class="btn btn-default" href="profile.php">ACEPTAR</a>
                            </div>
                            <?php }
                            elseif(isset($resultado) && $resultado==2){ ?>
                            <div class="alert alert-danger">
                                <strong>ERROR</strong> <?php echo $mensaje;?>
                                <a type="button" class="btn btn-default" href="profile.php">ACEPTAR</a>
                            </div>
                            <?php }
                            elseif (isset($resultado) && $resultado==3){ ?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong> <?php echo $mensaje;?>
                                <a type="button" class="btn btn-default" href="profile.php">ACEPTAR</a>
                                </div>
                            <?php }
                            elseif(isset($resultado) && $resultado==4){ ?>
                                <div class="alert alert-danger"> 
                                <strong>ERROR</strong> <?php echo $mensaje;?>
                                <a type="button" class="btn btn-default" href="profile.php">ACEPTAR</a>
                                </div>                            
                            <?php }
                            elseif(isset($resultado) && $resultado==5){ ?>
                                <div class="alert alert-danger"> 
                                <strong>ERROR</strong> <?php echo $mensaje;?>
                                <a type="button" class="btn btn-default" href="profile.php">ACEPTAR</a>
                                </div> 
                            <?php } 
                            elseif(isset($resultado) && $resultado==6){?>
                                <div class="alert alert-danger">
                                <strong>ATENCION</strong> <?php echo $mensaje;?>
                                <a type="button" class="btn btn-default" href="profile.php">ACEPTAR</a>
                                </div>                             
                            <?php }?>
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
