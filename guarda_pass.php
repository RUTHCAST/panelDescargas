<?php
	require 'funcs/conexion.php';
	include 'funcs/funcs.php';
	
	$user_id = $mysqli->real_escape_string($_POST['user_id']);
	$token = $mysqli->real_escape_string($_POST['token']);
	$password = $mysqli->real_escape_string($_POST['password']);
	$con_password = $mysqli->real_escape_string($_POST['con_password']);
    $mensaje='';
    $result=0;
	if(validaPassword($password, $con_password))
	{
		
		$pass_hash = hashPassword($password);
		
		if(cambiaPassword($pass_hash, $user_id, $token))
		{
            $mensaje= 'Contraseña Modificada';
            $result=1;
            header("Location:instrucciones3.php?mensaje=$mensaje&result=1");
		}
		else 
		{
			$mensaje= "Error al modificar contrase&ntilde;a";
		}
	}
	else
	{
		$mensaje= 'Las contraseñas no coinciden';
	}
?>