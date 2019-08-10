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

    if(!empty($_POST)){
        $idReceptor=$_POST['email'];
        $idArchivo=$_POST['idArchivo'];

        $query_compartir="SELECT idUsuario, usuario, email FROM usuarios WHERE idUsuario ='$idReceptor'";
        $result_compartir = $mysqli->query($query_compartir);
        $row_compartir = $result_compartir->fetch_assoc();
        $idUsuario_c=$row_compartir['idUsuario'];
        $usuario_c=$row_compartir['usuario'];
        $email_c=$row_compartir['email'];
        $url = 'http://'.$_SERVER["SERVER_NAME"].'/GESTIONDESCARGAS/index.php';

        $query_arc="SELECT idArchivo, nombre, path FROM archivos WHERE idArchivo ='$idArchivo'";
        $result_arc = $mysqli->query($query_arc);
        $row_arc = $result_arc->fetch_assoc();
        $idArchivo_arc=$row_arc['idArchivo'];
        $nombre_arc=$row_arc['nombre'];
        $path_arc=$row_arc['path'];

        if(!archivoExiste($idArchivo_arc, $idUsuario_c)){

            $result_reg=mysqli_query($mysqli,"INSERT INTO archivo_usuario(archivoId, usuarioId) 
                        VALUES($idArchivo_arc, $idUsuario_c)") or die(mysqli_error($mysqli));

            if($result_reg){

                if(compartirArchivo($email_c, $logueado, $usuario_c, $nombre_arc, $url, $path_arc))
                {
                    $mensaje="Archivo compartido con exito";
                    $resultado=1;
                }else{
                    $mensaje='Error al enviar correo';
                    $resultado=0;
                }
            }else{
                $mensaje="Error al incluir archivo en el directorio del usuario";
                $resultado=2;
            }
        }else{
            $mensaje="El usuario $email_c ya cuenta con este archivo en su directorio";
            $resultado=3;
        }
        header('Refresh:3;URL=profile.php');
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
                                COMPARTIR ARCHIVOS
                                <small>Resultado del envio del archivo <?php echo $nombre_arc; ?> a <?php echo $email_c;?> </small>
                            </h2>
                        </div>
                        <div class="body">
                            <?php if($resultado==1){?>
                                <div class="alert alert-success">
                                <strong>FELICIDADES!</strong> <?php echo  $mensaje ?>.
                                </div>
                            <?php } elseif($resultado==0){?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong> <?php echo  $mensaje ?>.
                                </div>
                            <?php } elseif($resultado==2){?>
                                <div class="alert alert-danger">
                                <strong>ERROR</strong> <?php echo  $mensaje ?>.
                                </div>
                            <?php } elseif($resultado==3){?>
                                <div class="alert alert-warning">
                                <strong>ERROR</strong> <?php echo  $mensaje ?>.
                                </div>
                            <?php } ?>
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
