<?php 
	session_start();
	require 'funcs/conexion.php';
	include 'funcs/funcs.php';
	
	if(!isset($_SESSION["id_usuario"])){ //Si no ha iniciado sesión redirecciona a index.php
		header("Location: index.php");
	}
	
	$idUsuario = $_SESSION['id_usuario'];
	
	$sql = "SELECT idUsuario, usuario, email FROM usuarios WHERE idUsuario = '$idUsuario'";
	$result = $mysqli->query($sql);
	
  $row = $result->fetch_assoc();
  $logueado=$row['usuario'];
  $email=$row['email'];

  //Usuarios activos;
  $result_activos=mysqli_query($mysqli,"SELECT idUsuario, activacion FROM usuarios");
  $contador_activos=0;
  $contador_inactivos=0;
  while($row_activos=mysqli_fetch_array($result_activos)){
    if($row_activos['activacion']==1){
        $contador_activos++;
    }else{
        $contador_inactivos++;
    }
  }
//Archivos subidos
$result_archivos=mysqli_query($mysqli,"SELECT count(idArchivo) AS totalArchivos, sum(size) AS espacio FROM archivos");
$row_totalArchivos=mysqli_fetch_row($result_archivos);
$total_archivos=$row_totalArchivos[0];
$espacio=$row_totalArchivos[1];
$espacioMB=round($espacio/1024);

//Documentos por usuario
$result_docUser=mysqli_query($mysqli, "SELECT archivo_usuario.usuarioId, COUNT(archivo_usuario.archivoId) AS totalArchivo,
                                                usuarios.usuario as nombre, usuarios.email as email,
                                                SUM(archivos.size)/1024 as size, archivos.idArchivo as idArchivo
                                    FROM archivo_usuario
                                    INNER JOIN usuarios
                                    INNER JOIN archivos
                                    WHERE archivo_usuario.usuarioId = usuarios.idUsuario
                                    AND archivo_usuario.archivoId = archivos.idArchivo
                                    GROUP BY usuarioId");
//Ultimos usuarios logueados
$ultimos_logueados=mysqli_query($mysqli,"SELECT idUsuario, email, last_session FROM usuarios ORDER BY last_session DESC");

//Archivos subidos por fechas
$query_aubidosFecha=mysqli_query($mysqli, "SELECT count(idArchivo) as totalArchivos, fechaSubida FROM archivos GROUP BY fechaSubida");

  include 'templates/header.php';
?>
<!--  -->
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD DE DESCARGAS</h2>
            </div>

            <!-- Widgets -->
            <div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-green hover-zoom-effect">
                        <div class="icon bg-green">
                            <i class="material-icons">person_add</i>
                        </div>
                        <div class="content">
                            <div class="text">USUARIOS INACTIVOS</div>
                            <div class="number"><?php echo $contador_activos ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-light-blue hover-zoom-effect">
                        <div class="icon bg-light-blue">
                            <i class="material-icons">person_pin</i>
                        </div>
                        <div class="content">
                            <div class="text">USUARIOS ACTIVOS</div>
                            <div class="number"><?php echo $contador_activos ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange hover-zoom-effect">
                        <div class="icon bg-orange">
                            <i class="material-icons">open_in_browser</i>
                        </div>
                        <div class="content">
                            <div class="text">ARCHIVOS SUBIDOS</div>
                            <div class="number"><?php echo $total_archivos?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-red hover-zoom-effect">
                        <div class="icon bg-red">
                            <i class="material-icons">storage</i>
                        </div>
                        <div class="content">
                            <div class="text">MEGABYTES EN DISCO</div>
                            <div class="number"><?php echo $espacioMB?></div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- #END# Widgets -->
            <div class="row clearfix">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="header">
                            <div class="row clearfix">
                                <div class="col-xs-12 col-sm-6">
                                    <h2>COMPARTIDOS CON USUARIOS</h2>
                                </div>
                            </div>
                        </div>
                        <div class="body">
                        <div class="table-responsive">
                                <table class="table table-hover dashboard-task-infos">
                                    <thead>
                                        <tr>
                                            <th width="5%" style="text-align:center">#</th>
                                            <th width="35%" style="text-align:center">Nombre</th>
                                            <th width="5%" style="text-align:center">Cantidad</th>
                                            <th width="35%" style="text-align:center">Email</th>
                                            <th width="20%" style="text-align:center">Espacio</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=0;
                                        while($row_docUser=mysqli_fetch_array($result_docUser)){
                                            $i++;
                                        ?>
                                        <tr>
                                            <td width="5%" style="text-align:center"><?php echo $i; ?></td>
                                            <td width="35%" style="text-align:center"><?php echo $row_docUser['nombre']; ?></td>
                                            <td width="5%" style="text-align:center">
                                                <?php if($row_docUser['totalArchivo']<25) {?>
                                                    <span class="label bg-blue">
                                                        <?php echo $row_docUser['totalArchivo']; ?>
                                                    </span>
                                                <?php }
                                                elseif($row_docUser['totalArchivo']>=25){
                                                ?>
                                                    <span class="label bg-green">
                                                        <?php echo $row_docUser['totalArchivo']; ?>
                                                    </span>
                                                <?php 
                                                }elseif($row_docUser['totalArchivo']>=50){?>
                                                    <span class="label bg-orange">
                                                        <?php echo $row_docUser['totalArchivo']; ?>
                                                    </span>
                                                <?php 
                                                }elseif($row_docUser['totalArchivo']>=100){?>
                                                    <span class="label bg-red">
                                                        <?php echo $row_docUser['totalArchivo']; ?>
                                                    </span>
                                                <?php 
                                                }?>
                                            </td>
                                            <td width="35%" style="text-align:center"><?php echo $row_docUser['email']; ?></td>
                                            <td width="20%" style="text-align:center">
                                                <div class="progress">
                                                    <div class="progress-bar bg-green" role="progressbar" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100" 
                                                        style="width: <?php echo ($row_docUser['size']*100/$espacioMB)?>%;">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>                        
                        </div>
                    </div>
                </div>
                <!-- Browser Usage -->
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="card bg-light-blue">
                        <div class="body bg-light-blue text-center">
                                ULTIMOS USUARIOS LOGUEADOS
                        </div>
                        <div class="body">
                            <table class="table bg-light-blue">
                                <thead>

                                </thead>
                                <tbody>
                                <?php 
                                    for($i=0;$i<=5;$i++){?>
                                        <?php while($row_ultimos_logueados=mysqli_fetch_array($ultimos_logueados)){?>
                                            <tr>
                                                <td>
                                                    <?php echo $row_ultimos_logueados['email']; ?>
                                                </td>
                                                <td>
                                                    <?php echo date($row_ultimos_logueados['last_session']); ?>
                                                </td>
                                            </tr>
                                    <?php 
                                    }?>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- #END# Browser Usage -->

            </div>
            <div class="row clearfix">
            </div>
        </div>
    </section>

  <?php
  include 'templates/footer.php';
  ?>