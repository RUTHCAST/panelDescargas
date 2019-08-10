<?php
	require 'funcs/config.php';

	$mysqli=new mysqli(SERVIDOR,USUARIO,PASSWORD,BD); 
	
	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}

	
?>