<?php

//traer parametro id 1
$parametros = $dbo->fetchById("ParametroCMS", "IDParametroCMS", 1, "array");


switch ($_POST["action"]) {

	case "insert":


		require_once "recaptchalib.php";
		// tu clave secreta
		$secret = "6LdtvCYTAAAAAJKmrTc2Pak5PR2D_Uf-2c82yFsW";

		// respuesta vacía
		$response = null;

		// comprueba la clave secreta
		$reCaptcha = new ReCaptcha($secret);

		// si se detecta la respuesta como enviada
		if ($_POST["g-recaptcha-response"]) {
			$response = $reCaptcha->verifyResponse(
				$_SERVER["REMOTE_ADDR"],
				$_POST["g-recaptcha-response"]
			);
		}

		if ($response != null && $response->success) {
			$_SESSION["validcaptcha"] = "okcaptcha" . date("Y-m-d H:i:s");
			//echo $_SESSION["validcaptcha"]="okcaptcha".date("Y-m-d H:i:s");	
			//echo "Hi " .  ", thanks for submitting the form!";
			$robot_verificacion = "S";
		} else {
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Debesverificarquenoeresunrobot', LANGSESSION));
			SIMHTML::jsRedirect("index.php");
			exit;
		}


		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Nombre"]) && !empty($_POST["Asunto"]) && !empty($_POST["Email"]) && !empty($_POST["Comentario"]) && $robot_verificacion == "S") :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			//insertamos los datos del contacto
			$id = $dbo->insert($_POST, "ContactoCMS", "IDContactoCMS");


			$dest = trim($parametros["Email"]);

			$head  = "From: " . $_POST["Email"] . "\r\n";
			$head .= "To: " . $dest . " \r\n";

			// Ahora creamos el cuerpo del mensaje
			$msg  = "Mensaje Enviado a Mi Club App \n\n";
			foreach ($_POST as $key => $value)
				$msg .= $key . " : " . $value . " \n";

			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Datosenviadoconexito,prontonospondremosencontacto', LANGSESSION));
			// Finalmente enviamos el mensaje
			if (mail($dest, "Contacto Mi Club App", $msg, $head))
				SIMHTML::jsRedirect("index.php?msg=1");
			else
				SIMHTML::jsRedirect("index.php?msg=2");
		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("index.php");
			exit;
		endif;
		break;
} //end switch



//traer servicios del usuario
$sql_servicios = "SELECT  * FROM ServicioCMS WHERE Publicar = 'S' ";
$qry_servicios = $dbo->query($sql_servicios);
$i = 0;
while ($r_servicios = $dbo->fetchArray($qry_servicios)) {
	$servicios[$i] =  $r_servicios;
	$i++;
} //end while


//traer banners
$sql_banners = "SELECT * FROM BannerCMS WHERE Publicar = 'S' ORDER BY FechaTrCr DESC ";
$qry_banners = $dbo->query($sql_banners);
while ($r_banners = $dbo->fetchArray($qry_banners)) {
	$banners[] = $r_banners;
} //end while

//seguridad para post y get
foreach ($_GET as $clave => $valor) {
	$_GET[$clave] = SIMUtil::antiinjection($valor);
}

foreach ($_POST as $clave => $valor) {
	if (!array($valor))
		$_POST[$clave] = SIMUtil::antiinjection($valor);
	else
		foreach ($_POST[$clave] as $key_clave => $valor_array)
			$_POST[$clave][$key_clave] = SIMUtil::antiinjection($valor_array);
}//end for
