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
        $idArchivo=$_GET['idArchivo'];

        $query_archivo="SELECT idArchivo, nombre, path FROM archivos WHERE idArchivo ='$idArchivo'";
        $result_archivo = $mysqli->query($query_archivo);
        $row_archivo = $result_archivo->fetch_assoc();
        $nombre_archivo=$row_archivo['nombre'];
        $path=$row_archivo['path'];

        //Eliminar del directorio
        if(file_exists($path)){
            unlink('upload/'.$nombre_archivo) or die(mysqli_error($mysqli));
            //Eliminar de la base de datos.
            $query_delete="DELETE FROM archivos WHERE idArchivo='$idArchivo'";
            $resultado_delete=mysqli_query($mysqli, $query_delete) or die(mysqli_error($mysqli));
            if($resultado_delete){
                $mensaje="Archivo eliminado exitosamente";
                $resultado=1;
            }
        }else{
            $mensaje='No se encontro el archivo en el directorio';
            $resultado=2;
        }
    }else{
        $mensaje="No se ha enviado ningun archivo para eliminar";
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
                                ELIMINAR ARCHIVOS
                            </h2>
                        </div>
                        <div class="body">
                            <?php 
                            if($resultado==1){?>
                                <div class="alert alert-success">
                                <strong>FELICIDADES!</strong> <?php echo  $mensaje ?>.
                                <a type="button" class="btn btn-default" href="profile.php">ACEPTAR</a>
                                </div>
                            <?php } 
                            elseif($resultado==0){?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong> <?php echo  $mensaje ?>.
                                <a type="button" class="btn btn-default" href="profile.php">ACEPTAR</a>

                                </div>
                            <?php }
                            elseif($resultado==2){?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong> <?php echo  $mensaje ?>.
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
