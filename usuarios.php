<?php
	session_start();
	require 'funcs/conexion.php';
    include 'funcs/funcs.php';
	//Vericacion de session
	if(!isset($_SESSION["id_usuario"])){ //Si no ha iniciado sesión redirecciona a index.php
		header("Location: index.php");
    }
    $idUsuario = $_SESSION['id_usuario'];
	$sql = "SELECT idUsuario, usuario, email, last_session, activacion FROM usuarios WHERE idUsuario = '$idUsuario'";
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

    //Listar Usuarios
    $query_usuarios="SELECT * FROM usuarios";
    $resultado_usuarios=mysqli_query($mysqli,$query_usuarios);

    //
    if(!empty($_GET)){
        $mensaje=$_GET['mensaje'];
    }else{
        $mensaje='';
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
                                ADMINISTRACION DE USUARIOS
                            </h2>
                        </div>
                        <div class="body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#registrar" data-toggle="tab">
                                        <i class="material-icons">person_add</i> REGISTRAR
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#administrar" data-toggle="tab">
                                        <i class="material-icons">settings</i> ADMINISTRAR
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade in active" id="registrar">
                                    <form action="registroUsuarios.php" method="POST">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" name="nombre" id="" class="form-control" placeholder="Nombre Completo">
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="email" name="email" id="" class="form-control" placeholder="Email">
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="password" name="password" id="" class="form-control" placeholder="Contraseña">
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="password" name="confirm" id="" class="form-control" placeholder="Confirmar contraseña">
                                            </div>
                                        </div>
                                        <br>
                                        <button type="submit" class="btn btn-primary m-t-15 waves-effect">REGISTRAR</button>
                                    </form>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="administrar">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th width="20%" style="text-align:center">NOMBRE</th>
                                                <th width="20%" style="text-align:center">EMAIL</th>
                                                <th width="10%" style="text-align:center">CONTRASEÑA</th>
                                                <th style="text-align:center">TIPO</th>
                                                <th style="text-align:center">ULTIMA SESSION</th>
                                                <th style="text-align:center">ACTIVACION</th>
                                                <th width="5%" style="text-align:center"></th>
                                                <th width="5%" style="text-align:center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row_usuarios=mysqli_fetch_array($resultado_usuarios)){
                                                $datos=$row_usuarios[0].'||'.$row_usuarios[1].'||'.$row_usuarios[2].'||'.
                                                       $row_usuarios[3].'||'.$row_usuarios[4].'||'.$row_usuarios[5];
                                                ?>
                                            <tr>
                                                <th scope="row" style="text-align:center"><?php echo $row_usuarios['idUsuario']?></th>
                                                <td width="20%" style="text-align:left"><?php echo $row_usuarios['usuario']?></td>
                                                <td width="20%" style="text-align:center"><?php echo $row_usuarios['email']?></td>
                                                <td width="10%" style="text-align:center"><?php echo $row_usuarios['contrasena']?></td>
                                                <td width="10%" style="text-align:center"><?php if($row_usuarios['id_tipo']==1)
                                                    { 
                                                        echo "ADMINISTRADOR";
                                                    }
                                                    else
                                                    {
                                                        echo "INVITADO";
                                                    }
                                                    ?></td>
                                                <td style="text-align:center"><?php echo $row_usuarios['last_session']?></td>
                                                <td width="10%" style="text-align:center"><?php 
                                                                                                if( $row_usuarios['activacion']==1){
                                                                                                    echo "ACTIVADO";
                                                                                                }else{
                                                                                                    echo "DESACTIVADO";
                                                                                                }
                                                                                                ?>
                                                </td>
                                                <td width="5%" style="text-align:center">
                                                    <button 
                                                        type="button" 
                                                        data-color="blue"
                                                        title='Editar' 
                                                        class="btn btn-primary waves-effect"
                                                        data-toggle="modal" data-target="#editarUsuario"
                                                        data-href="eliminarUsuario.php?idUsuario=<?php echo $row_usuarios['idUsuario']?>"
                                                        onclick="agregarDatos2('<?php echo $datos ?>')"
                                                        >
                                                        <i class="material-icons">create</i>
                                                    </button>                                                
                                                </td>
                                                <td width="5%" style="text-align:center">
                                                    <button 
                                                        type="button" 
                                                        data-color="blue"
                                                        title='Eliminar' 
                                                        class="btn bg-red waves-effect"
                                                        data-toggle="modal" data-target="#eliminarUsuario"
                                                        data-href="eliminarUsuario.php?idUsuario=<?php echo $row_usuarios['idUsuario']?>">
                                                        <i class="material-icons">delete</i>
                                                    </button> 
                                                </td>
                                            </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Table -->
        </div>
    </section>
        <!-- Modal Eliminar -->
        <div class="modal fade" id="eliminarUsuario" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="defaultModalLabel">Eliminar Archivo</h4>
                        </div>
                        <div class="modal-body">
                            ¿Esta seguro de querer eliminar este usuario?
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-danger btn-ok">ELIMINAR</a>
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CANCELAR</button>
                        </div>
                    </div>
            </div>
    </div>
    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editarUsuario" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">EDITAR DATOS DE USUARIO</h4>
                </div>
                <div class="modal-body">
                    <form action="modificarUsuarios.php" method="POST">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="hidden" name="idUsuarioUI" id="idUI" class="form-control">
                                <input type="text" name="nombreUI" id="nombreUI" class="form-control" placeholder="Nombre Completo">
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="email" name="emailUI" id="emailUI" class="form-control" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="password" name="passwordUI" id="passwordUI" class="form-control" placeholder="Contraseña">
                            </div>
                        </div>
                        <!-- <div class="form-group form-float">
                            <div class="form-line">
                                <select name="activacionUI" id="activaciondUI" class="form-control">
                                    <option value="1" selected>ACTIVADO</option>
                                    <option value="0" selected>DESACTIVADO</option>
                                </select>
                            </div>
                        </div> -->
                        <br>
                
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-primary waves-effect">MODIFICAR</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CANCELAR</button>
            </div>
            </form>

        </div>
    </div>

<?php 
include 'templates/footer.php' 
?>