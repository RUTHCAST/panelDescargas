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

    if(!empty($_GET)){
        $idUsuario=$_GET['idUsuario'];
            $query_delete="DELETE FROM usuarios WHERE idUsuario='$idUsuario'";
            $resultado_delete=mysqli_query($mysqli, $query_delete) or die(mysqli_error($mysqli));
            if($resultado_delete){
                $mensaje="Usuario eliminado exitosamente";
                $resultado=1;
            }
    }else{
        $mensaje="No se ha recibido ningun usuario para eliminar";
        $resultado=0;
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
                                ELIMINAR USUARIOS
                            </h2>
                        </div>
                        <div class="body">
                            <?php 
                            if($resultado==1){?>
                                <div class="alert alert-info">
                                <strong>FELICIDADES!</strong> <?php echo  $mensaje ?>.
                                <a type="button" class="btn btn-default" href="usuarios.php">ACEPTAR</a>
                                </div>
                            <?php } 
                            elseif($resultado==0){?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong> <?php echo  $mensaje ?>.
                                <a type="button" class="btn btn-default" href="usuarios.php">ACEPTAR</a>

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