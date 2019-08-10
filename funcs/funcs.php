<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

	function isNull($usuario, $email, $pass, $con_password){
		if(strlen(trim($usuario)) < 1 || strlen(trim($email)) < 1 || strlen(trim($pass)) < 1 || strlen(trim($con_password)) < 1)
		{
			return true;
			} else {
			return false;
		}		
	}

    function isEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)){
			return true;
			} else {
			return false;
		}
    }

    function validaPassword($var1, $var2)
	{

		if (strcmp($var1, $var2) !== 0){
			return false;
			} else {
			return true;
		}
    }

    function minMax($min, $max, $valor){
		if(strlen(trim($valor)) < $min)
		{
			return true;
		}
		else if(strlen(trim($valor)) > $max)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function archivoExiste($idArchivo_arc, $idUsuario_c){

		global $mysqli;
		$stmt = $mysqli->prepare("SELECT archivoId, usuarioId FROM archivo_usuario WHERE archivoId=? AND usuarioId=? LIMIT 1");
		$stmt->bind_param("ii", $idArchivo_arc, $idUsuario_c);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		$stmt->close();

		if ($num > 0){
			return true;
			} else {
			return false;	
		}
	}
    
    function emailExiste($email)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT idUsuario FROM usuarios WHERE email = ? LIMIT 1");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		$stmt->close();
		
		if ($num > 0){
			return true;
			} else {
			return false;	
		}
	}

	function archivoDuplicado($upload)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT idArchivo, path FROM archivos WHERE path = ? LIMIT 1");
		$stmt->bind_param("s", $upload);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		$stmt->close();
		
		if ($num > 0){
			return true;
			} else {
			return false;	
		}
	}


	function generateToken()
	{
		$gen = md5(uniqid(mt_rand(), false));	
		return $gen;
	}
    
    function hashPassword($password) 
	{
		$hash = password_hash($password, PASSWORD_DEFAULT);
		return $hash;
    }
    
    function resultBlock($errors){
		if(count($errors) > 0)
		{
			echo "<div id='error' class='alert alert-danger' role='alert'>
			<a href='#' onclick=\"showHide('error');\">[X]</a>
			<ul>";
			foreach($errors as $error)
			{
				echo "<li>".$error."</li>";
			}
			echo "</ul>";
			echo "</div>";
		}
    }
    
    function registraUsuario($usuario, $email, $pass_hash, $activo, $token, $tipo_usuario){
		
		global $mysqli;

		$stmt = $mysqli->prepare("INSERT INTO usuarios (usuario, email, contrasena, activacion, token, id_tipo) 
		                          VALUES(?,?,?,?,?,?)");
		$stmt->bind_param('sssisi', $usuario, $email, $pass_hash, $activo, $token, $tipo_usuario);
		if ($stmt->execute()){
			return $mysqli->insert_id;
			} else {
			return 0;
		}		
	}

	function enviarEmail($email, $nombre, $asunto, $cuerpo){
		require 'vendor/autoload.php';

		$mail = new PHPMailer(true);
		try {
		//Server settings
		$mail->SMTPDebug = 0;  //0 para desactivar                  // Enable verbose debug output
		$mail->isSMTP();                                            // Set mailer to use SMTP
		$mail->Host       = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
		$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
		$mail->Port       = '587';                                    // TCP port to connect to
			
		$mail->Username   = 'testwebruthreyes@gmail.com';           // SMTP username
		$mail->Password   = 'Qwerty123.';                           // SMTP password

		//Recipients
		$mail->setFrom('testwebruthreyes@gmail.com', 'Gaston Electrotectia - Gestion de Descargas');
		$mail->addAddress($email, $nombre);                          // Add a recipient
			
		// Content
		$mail->Subject = $asunto;
		$mail->Body    = $cuerpo;
		$mail->IsHTML(true);
	
		if($mail->send())
		return true;
		else
		return false;
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}

function envioMultiple($email_compartir, $nombre_archivo, $url, $archivo, $upload, $file_name, $error){
	global $mysqli;

	require 'vendor/autoload.php';
	
	$mail = new PHPMailer(true);
	try {
	//Server settings
	$mail->SMTPDebug = 0;  //0 para desactivar                  // Enable verbose debug output
	$mail->isSMTP();                                            // Set mailer to use SMTP
	$mail->Host       = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
	$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
	$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
	$mail->Port       = '587';                                  // TCP port to connect to
		
	$mail->Username   = 'testwebruthreyes@gmail.com';           // SMTP username
	$mail->Password   = 'Qwerty123.';                           // SMTP password

	//Recipients
	$mail->setFrom('testwebruthreyes@gmail.com', 'Gaston Electrotectia - Gestion de Descargas');
	
	for($i=0; $i<count($email_compartir); $i++)
    {    
		$q_nombres="SELECT idUsuario, usuario FROM usuarios WHERE email='$email_compartir[$i]'";
		$result_nombres=mysqli_query($mysqli, $q_nombres);
		while($row_nombres=mysqli_fetch_array($result_nombres)){
			$mail->addAddress($email_compartir[$i], $row_nombres['usuario']);
		} 
	}

	if (isset($archivo) && $error == UPLOAD_ERR_OK) { 
		$mail->AddAttachment($upload, $file_name); 
	}
		
	// Content
	$mail->Subject = 'Nuevo archivo disponible para descargar';
	$mail->Body    = "Estimado $nombre, Saludos!<br> acabas de recibir el archivo $nombre_archivo adjunto en este email, 
					si lo deseas también puedes descargarlo ingresando a nuestra plataforma dando click <a href=".$url.">aqui</a>";
	$mail->IsHTML(true);

	if($mail->send())
	return true;
	else
	return false;
	} catch (Exception $e) {
		echo "El mensaje no puede ser enviado: {$mail->ErrorInfo}";
	}
}



	function validaIdToken($id, $token){
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT activacion FROM usuarios WHERE idUsuario = ? AND token = ? LIMIT 1");
		$stmt->bind_param("is", $id, $token);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;
		
		if($rows > 0) {
			$stmt->bind_result($activacion);
			$stmt->fetch();
			
			if($activacion == 1){
				$msg = "La cuenta ya se activo anteriormente.";
				} else {
				if(activarUsuario($id)){
					$msg = 'Cuenta activada.';
					} else {
					$msg = 'Error al Activar Cuenta';
				}
			}
			} else {
			$msg = 'No existe el registro para activar.';
		}
		return $msg;
	}
	function verificaTokenPass($user_id, $token){
		
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT activacion FROM usuarios WHERE idUsuario = ? AND token_password = ? AND password_request = 1 LIMIT 1");
		$stmt->bind_param('is', $user_id, $token);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		
		if ($num > 0)
		{
			$stmt->bind_result($activacion);
			$stmt->fetch();
			if($activacion == 1)
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
		else
		{
			return false;	
		}
	}
	
	function activarUsuario($id)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("UPDATE usuarios SET activacion=1 WHERE idUsuario = ?");
		$stmt->bind_param('s', $id);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}

	function isNullLogin($email, $password){

		if(strlen(trim($email)) < 1 || strlen(trim($password)) < 1)
		{
			return true;
		}
		else
		{
			return false;
		}		
	}
	
	function login($email, $password, $recordar=0)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT idUsuario, id_tipo, contrasena FROM usuarios WHERE email = ? LIMIT 1");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;
		
		if($rows > 0) {
			
			if(isActivo($email)){
				
				$stmt->bind_result($id, $id_tipo, $passwd);
				$stmt->fetch();

				if(validaPassword($passwd, $password)){
				
					//Insertar funcion para generar cookie
					$stmt = $mysqli->prepare("SELECT IdUsuario, email, contrasena, cookie FROM usuarios WHERE email = ? AND contrasena = ? LIMIT 1 ");
					$stmt->bind_param('ss', $email, $passwd);
					$stmt->execute();
					$stmt->store_result();
					$num = $stmt->num_rows;

					if($num>=1){
						$stmt->bind_result($id, $email, $passwd);
							if($recordar>0){
								mt_srand(time());
								$rand = mt_rand(1000000,9999999);
								$stmt = $mysqli->prepare("UPDATE usuarios SET cookie='".$rand."'WHERE idUsuario = ?");
								$stmt->bind_param('i', $id);
								$stmt->execute();
								$stmt->close();								
								setcookie("id_user", $id, time()+(60*60*24*365));
								setcookie("marca", $rand, time()+(60*60*24*365));
			
							}
					}
				//Fin generar cookie
					lastSession($id);
					$_SESSION['id_usuario'] = $id;
					$_SESSION['tipo_usuario'] = $id_tipo;
					
					header("location: profile.php");
					} else {
					
					$errors = "La contrase&ntilde;a es incorrecta";
				}
				} else {
				$errors = 'El usuario no esta activo';
			}
			} else {
			$errors = "El correo electr&oacute;nico no existe";
		}
		return $errors;
	}	

	function lastSession($id)
	{
		global $mysqli;
		// var_dump($id);
		// die();
		$stmt = $mysqli->prepare("UPDATE usuarios SET last_session=NOW(), token_password='', password_request=1 WHERE idUsuario = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->close();
	}

	function isActivo($email)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT activacion FROM usuarios WHERE usuario = ? || email = ? LIMIT 1");
		$stmt->bind_param('ss', $email, $email);
		$stmt->execute();
		$stmt->bind_result($activacion);
		$stmt->fetch();
		
		if ($activacion == 1)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}


	function generaTokenPass($user_id)
	{
		global $mysqli;
		
		$token = generateToken();
		
		$stmt = $mysqli->prepare("UPDATE usuarios SET token_password=?, password_request=1 WHERE idUsuario = ?");
		$stmt->bind_param('ss', $token, $user_id);
		$stmt->execute();
		$stmt->close();
		
		return $token;
	}

	function getValor($campo, $campoWhere, $valor)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT $campo FROM usuarios WHERE $campoWhere = ? LIMIT 1");
		$stmt->bind_param('s', $valor);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		
		if ($num > 0)
		{
			$stmt->bind_result($_campo);
			$stmt->fetch();
			return $_campo;
		}
		else
		{
			return null;	
		}
	}

	function getPasswordRequest($id)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT password_request FROM usuarios WHERE idUsuario = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->bind_result($_id);
		$stmt->fetch();
		
		if ($_id == 1)
		{
			return true;
		}
		else
		{
			return null;	
		}
	}

	function cambiaPassword($password, $user_id, $token){
		
		global $mysqli;
		
		$stmt = $mysqli->prepare("UPDATE usuarios SET contrasena = ?, token_password='', password_request=0 
								  WHERE idUsuario = ? AND token_password = ?");
		$stmt->bind_param('sis',  $password, $user_id, $token);
		
		if($stmt->execute()){
			return true;
			} else {
			return false;		
		}
	}
	


?>