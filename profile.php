<?php
	session_start();
	require 'funcs/conexion.php';
    include 'funcs/funcs.php';
	//Vericacion de session
	if(!isset($_SESSION["id_usuario"])){ //Si no ha iniciado sesión redirecciona a index.php
		header("Location: index.php");
	}
	
	$idUsuario = $_SESSION['id_usuario'];
	$sql = "SELECT idUsuario, usuario, email, last_session FROM usuarios WHERE idUsuario = '$idUsuario'";
	$result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $logueado=$row['usuario'];
    $email=$row['email'];
    $idUsuario=$row['idUsuario'];
    $last_session=$row['last_session'];

   //Vericacion de cookie
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
    $fecha_actual=date("d/m/Y");

//Subir archivo al servidor
    if(!empty($_POST)){
        if(is_uploaded_file($_FILES['archivo']['tmp_name'])){
            
            if(!is_dir('upload')){
                mkdir('upload', 0777);
            }else{
                $ruta = "upload/";    
            }

            $archivo=$_FILES['archivo'];
            $nombre_archivo= basename($_FILES['archivo']['name']);
            $error=$_FILES['archivo']['error'];
            $tipo=$archivo['type'];
            $size=$_FILES['archivo']['size'];
            $upload= $ruta.$nombre_archivo;

            $url = 'http://'.$_SERVER["SERVER_NAME"].'/GESTIONDESCARGAS/index.php';

            if(move_uploaded_file($_FILES['archivo']['tmp_name'], $upload)) { //movemos el archivo a su ubicacion 
                $file_name=$_FILES['archivo']['name'];
                $file_path=$_FILES['archivo']['tmp_name'];

                    //Insertar archivo en la base de datos
                    $descripcion = $mysqli->real_escape_string($_POST['descripcion']);
                    $contenido=mysqli_real_escape_string ($mysqli, file_get_contents($upload));

                    if(!archivoDuplicado($upload)){
                        $query = "INSERT INTO archivos (nombre, descripcion, fechaSubida, tipo, size, contenido, path)  
                        VALUES('$nombre_archivo','$descripcion',NOW(),'$tipo','$size','$contenido','$upload')";
                        $resultado_insertar=mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                        
                        if($resultado_insertar){
                            //Sacamos el ID del ultimo archivo insertado
                            $id_ultimo=mysqli_query($mysqli,"SELECT MAX(idArchivo) AS id_ultimoArchivo FROM archivos");
                                if($row=mysqli_fetch_row($id_ultimo)){
                                $id_archivoInsertado=trim($row[0]);
                                }
                            //Insertamos los ID del archivo y el usuario en la tabla que los relaciona:
                            //Sacamos los id de cada usuario seleccionado e insertamos el id del archivo en la tabla de relaciones:
                            if(isset($_POST['email_compartido'])){
                                $email_compartir=$_POST['email_compartido'];
                                for($i=0;$i<count($email_compartir);$i++){
                                    //extraer el id segun email recibido
                                    $q_compartir="SELECT idUsuario, email FROM usuarios WHERE email='$email_compartir[$i]'";
                                    $result_compartir=mysqli_query($mysqli, $q_compartir);
                                    while($row_compartir=mysqli_fetch_array($result_compartir)){
                                    //insertar archivo en la base de datos
                                    $result_arch_user = mysqli_query($mysqli,"INSERT INTO archivo_usuario (archivoId, usuarioId)
                                                                                VALUES($id_archivoInsertado, ".$row_compartir['idUsuario'].")") 
                                                        or die('Error en la consulta'.mysqli_error($mysqli));
                                    }

                                //Enviar Email con archivo adjunto a usuario
                                if(envioMultiple($email_compartir, $nombre_archivo, $url, $archivo, $upload, $file_name, $error) && $result_arch_user){
                                    $resultado=1;
                                    $mensaje="Archivo guardado y compartido exitosamente.";
                                    header("Location:uploadPage.php?resultado=$resultado&mensaje=$mensaje");
                                    }
                                } 
                            }//Fin compartir archivo
                        } //Fin insertar archivo en la base de datos
                        else{
                            $mensaje="Error al guardar en la base de datos";
                            $resultado=6;                    
                            header("Location:uploadPage.php?resultado=$resultado&mensaje=$mensaje");    
                        }
                    }
                    else
                    {
                        $mensaje="Ya existe un archivo con este nombre en la base de datos, cambie el nombre del mismo y vuelva a intentarlo";
                        $resultado=2;                    
                        header("Location:uploadPage.php?resultado=$resultado&mensaje=$mensaje");
                    }
                                
            }//Fin subir archivos al directorio
            else{
                $mensaje="Error al cargar archivos al directorio";
                $resultado=3;                    
                header("Location:uploadPage.php?resultado=$resultado&mensaje=$mensaje");
            }
		
        }//Fin cargar archivo
        else{
            $mensaje="Error al subir archivo";
            $resultado=4;                    
            header("Location:uploadPage.php?resultado=$resultado&mensaje=$mensaje");
        }
    }//Fin post
    //Listar archivos del usuario logueado
    $query_archivos = "SELECT archivo_usuario.id as id, archivo_usuario.archivoId as archivoId, 
                      archivo_usuario.usuarioId as usuarioId, archivos.idArchivo as idArchivo, 
                      archivos.nombre as nombre, archivos.descripcion as descripcion, archivos.fechaSubida as fecha ,
                      archivos.tipo as tipo, archivos.size as size, archivos.contenido as contenido, 
                      archivos.path as path
                      FROM archivo_usuario
                      INNER JOIN archivos
                      WHERE archivo_usuario.archivoId=archivos.idArchivo
                      AND archivo_usuario.usuarioId='$idUsuario'
                      ORDER BY fecha DESC";
    

    $resultado_archivos=mysqli_query($mysqli,$query_archivos);

    //Descargar archivos
    if(!empty($_GET)){
        $idArchivo=$_GET['id'];
        $query_descarga="SELECT nombre, tipo, contenido FROM archivos WHERE idArchivo= $idArchivo ";
        $resultado_descarga=$mysqli->query($query_descarga);
        $row_descarga = $resultado_descarga->fetch_assoc();
        $nombre_archivo=$row_descarga['nombre'];
        $tipo_archivo=$row_descarga['tipo'];
        $contenido_archivo=$row_descarga['contenido'];
        header("Content-type: $tipo_archivo");
        header("Content-Disposition: attachment; filename=".$nombre_archivo);
        echo $contenido;

    }
    //Compartir archivo
    $query_compartir="SELECT idUsuario, usuario, email FROM usuarios";
    $resultado_compartir=mysqli_query($mysqli, $query_compartir);

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

    <section class="content">
        <div class="container-fluid">
        <?php if($_SESSION['tipo_usuario']==1){?>

        <div class="block-header">
            <!-- Widgets -->
            <div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-green hover-zoom-effect">
                        <div class="icon bg-green">
                            <i class="material-icons">person_add</i>
                        </div>
                        <div class="content">
                            <div class="text">USUARIOS INACTIVOS</div>
                            <div class="number"><?php echo $contador_inactivos ?></div>
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
        </div>
        <?php } ?>
        <!-- <div class="block-header">
            <div class="row clearfix"> -->
                <!-- <div class="col-xs-12 col-sm-3">
                    <div class="card profile-card">
                        <div class="profile-header">&nbsp;</div>
                        <div class="profile-body">
                            <div class="image-area">
                                <img src="images/user-lg.jpg" alt="AdminBSB - Profile Image" />
                            </div>
                            <div class="content-area">
                                <h3><?php echo $logueado?></h3>
                                <p>Tipo Usuario</p>
                            </div>
                        </div>
                        <div class="profile-footer">
                            <ul>
                                <li>
                                    <span>Subidas</span>
                                    <span>1.234</span>
                                </li>
                                <li>
                                    <span>Descargas</span>
                                    <span>1.201</span>
                                </li>
                            </ul>
                            <button class="btn btn-primary btn-lg waves-effect btn-block">FOLLOW</button>
                        </div>
                    </div> -->

                    <!-- <div class="card card-about-me">
                        <div class="header">
                            <h2>SUBIR ARCHIVOS </h2>
                        </div>
                        <div class="body">
                            <ul>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">library_books</i>
                                        Education
                                    </div>
                                    <div class="content">
                                        B.S. in Computer Science from the University of Tennessee at Knoxville
                                    </div>
                                </li>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">location_on</i>
                                        Location
                                    </div>
                                    <div class="content">
                                        Malibu, California
                                    </div>
                                </li>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">edit</i>
                                        Skills
                                    </div>
                                    <div class="content">
                                        <span class="label bg-red">UI Design</span>
                                        <span class="label bg-teal">JavaScript</span>
                                        <span class="label bg-blue">PHP</span>
                                        <span class="label bg-amber">Node.js</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">notes</i>
                                        Description
                                    </div>
                                    <div class="content">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>  -->
                <!-- </div> -->
                <div class="col-xs-12 col-sm-12">
                    <div class="card">
                        <div class="body">
                            <div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">ARCHIVOS DISPONIBLES</a></li>
                                    <?php 
                                    if($_SESSION['tipo_usuario']==1){?>
                                    <li role="presentation"><a href="#profile_settings" aria-controls="settings" role="tab" data-toggle="tab">SUBIR ARCHIVOS</a></li>
                                    <?php }?>
                                </ul>

                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="home">
                                        <div class="panel panel-default panel-post">
                                            <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable" id='miTabla'>
                                                    <thead>
                                                        <tr>
                                                            <th width="15%" style="text-align:center">FECHA SUBIDA</th>
                                                            <th width="" style="text-align:center">NOMBRE</th>
                                                            <th width="%" style="text-align:center">DESCRIPCION</th>
                                                            <th width="5%" style="text-align:center">PESO</th>
                                                            <th></th>
                                                            <?php 
                                                            if($_SESSION['tipo_usuario']==1)
                                                            {?>
                                                            <th></th>
                                                            <?php
                                                            }?>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while($row=mysqli_fetch_array($resultado_archivos)){ 
                                                            $datos=$row[0];
                                                            ?>
                                                        <tr>
                                                            <td width="15%" style="text-align:center"><?php echo $row['fecha']?></td>
                                                            <td width="" style="text-align:left"><?php echo $row['nombre']?></td>
                                                            <td width="" style="text-align:left"><?php echo $row['descripcion']?></td>
                                                            <td width="5%" style="text-align:center"><?php echo $row['size']?></td>
                                                            <td width="5%" style="text-align:center">                                                                  
                                                            <a type="button" class="btn btn-default waves-effect"
                                                            href="profile.php?id=<?php echo $row['idArchivo']?>" _blank
                                                            >
                                                                <i class="material-icons">archive</i>
                                                            </a>
                                                            </td>
                                                            <!-- <td>
                                                            <a type="button"
                                                            title="Compartir"
                                                            data-color="light-blue" class="btn bg-light-blue waves-effect"
                                                            data-toggle="modal" data-target="#compartir" 
                                                            onclick="agregarDatos('<?php echo $datos ?>')">
                                                                <i class="material-icons">email</i>
                                                            </a>
                                                            </td> -->
                                                            <?php 
                                                            if($_SESSION['tipo_usuario']==1)
                                                            {?>

                                                            <td width="5%" style="text-align:center">
                                                            <button type="button" 
                                                            data-color="blue" 
                                                            class="btn bg-red waves-effect"
                                                            data-toggle="modal" data-target="#eliminarArchivo"
                                                            data-href="eliminar.php?idArchivo=<?php echo $row['idArchivo']?>"
                                                            >
                                                                <i class="material-icons">delete</i>
                                                            </button>                                                             
                                                            </td>
                                                            <?php }?>
                                                        </tr>
                                                    <?php }?>
                                                    </tbody>
                                                </table>

                                            </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane " id="profile_settings">
                                    <!-- <form action="/" id="frmFileUpload" class="dropzone" method="post" enctype="multipart/form-data">
                                        <div class="dz-message">
                                            <div class="drag-icon-cph">
                                                <i class="material-icons">touch_app</i>
                                            </div>
                                            <h3>Drop files here or click to upload.</h3>
                                            <em>(This is just a demo dropzone. Selected files are <strong>not</strong> actually uploaded.)</em>
                                        </div>
                                        <div class="fallback">
                                            <input name="file" type="file" multiple />
                                        </div>
                                    </form> -->
                                        <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" id="frmFileUpload" class="dropzone" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="NameSurname" class="col-sm-2 control-label">Descripcion</label>
                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" id="" name="descripcion" placeholder="Nombre del archivo" value="" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 control-label">Contenido</label>
                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                        <input type="file" class="form-control" id="InputExperience" name="archivo" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 control-label">Destinatarios: </label>
                                                <div class="col-sm-10">
                                                    <select class="form-control" name="email_compartido[]" multiple>
                                                    <?php while($row_c=mysqli_fetch_array($resultado_compartir)){?>
                                                    <option value='<?php echo $row_c['email']?>'
                                                    <?php if($row_c['email']==$email) echo 'selected'?>
                                                    ><?php echo $row_c['email'];?></option>
                                                    <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <input type="submit" class="btn btn-primary mr-5" value="SUBIR">
                                                    <!-- <input type="button" class="btn btn-danger" cancel" id="cancelar" value="CANCELAR"> -->
                                                </div>
                                            </div>
                                            <!-- <div class="form-group">
                                                <label for="" class="col-sm-2 control-label"></label>
                                                <div class="col-sm-10">
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
                                                            75%
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- fIn compartir archivos -->
                <?php if($_SESSION['tipo_usuario']==1){?>
                <div class="row clearfix">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="header">
                            <div class="row clearfix">
                                <div class="col-xs-12 col-sm-6">
                                    COMPARTIDOS CON USUARIOS
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
                    <div class="card">
                        <div class="body bg-light text-center">
                                ULTIMOS USUARIOS LOGUEADOS
                        </div>
                        <div class="body">
                            <table class="table ">
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

            </div>
        </div>
    </section>
    <!-- Enviar Correo -->
    <div class="modal fade" id="compartir" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="defaultModalLabel">COMPARTIR ARCHIVO</h4>
                </div>
                <div class="modal-body">
                    <form action="compartir.php" method="POST">
                    <div class="form-group">
                        <div class="form-line">
                            <select class="form-control show-tick bg-light" name="email">
                                <option value="0">Seleccione</option>
                                <?php while($row_compartir =mysqli_fetch_array($resultado_compartir)){?>
                                <option value="<?php echo $row_compartir['idUsuario']?>"><?php echo $row_compartir['email']?></option>
                                <?php }?>
                            </select>
                        <input type="text" name='idArchivo' id='idArchivo' hidden>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect btn-ok">COMPARTIR</button>                
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CANCELAR</button>
                </div>
                </form>
            </div>  
        </div>
    </div>
    <!-- Modal Eliminar -->
    <div class="modal fade" id="eliminarArchivo" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="defaultModalLabel">Eliminar Archivo</h4>
                        </div>
                        <div class="modal-body">
                            ¿Esta seguro de querer eliminar este archivo?
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-danger btn-ok">ELIMINAR</a>
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CANCELAR</button>
                        </div>
                    </div>
            </div>
    </div>
    <?php }?>

<?php 
  include 'templates/footer.php';
?>
