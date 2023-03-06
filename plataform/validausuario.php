<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

	// Build POST request:
	$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
	$recaptcha_secret = '6Ld4o5gUAAAAAAfPab-SHcFQbx4Gr5ZYCWEYTYTy';
	$recaptcha_response = $_POST['recaptcha_response'];

	// Make and decode POST request:
	$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
	$recaptcha = json_decode($recaptcha);

	// Take action based on the score returned:
	if ($recaptcha->score >= 0.1) {
		//echo "SI pareces humano, por favor intenta de nuevo: " . $recaptcha->score . " token: " . $recaptcha_response;
		//exit;
	} else {
		//echo "No pareces humano, por favor intenta de nuevo: " . $recaptcha->score . " token: " . $recaptcha_response;
		header("location: login.php?msg=ROBOT");
		exit;
	}
}


require("../admin/config.inc.php");
SIMUtil::cache();

$_POST = SIMUtil::makeSafe($_POST);

//handler de sesion
$simsession = new SIMSession(SESSION_LIMIT);


if (isset($_POST["action"]))
	$action = $_POST["action"];
else
	$action = $_GET["action"];

switch ($action) {

	case 'Iniciar':

		$login = SIMUtil::antiinjection($_POST["Email"]);
		$clave = SIMUtil::antiinjection($_POST["Password"]);
		$origen = SIMNet::req("origen");

		$dbo = &SIMDB::get();


		$cliente_data = $dbo->fetchAll("Usuario", "User = '" . $login . "' AND Password = '" . sha1($clave) . "' AND Autorizado = 'S' ", "object");

		$simsession->clean();

		if ($cliente_data) {

			$usuariosave = addslashes(serialize($cliente_data));

			if ($simsession->crear($cliente_data->IDRegistro, $usuariosave)) {

				//si el usuario es administrador lo deja en la seleccion de clubes
				if ($cliente_data->TipoUsuario == "admin") {
					header("location: clubes.php?ver=t");
					exit;
				} else {
					//si el usuario es club crea la sesion del club

					$_SESSION["club"] = $cliente_data->IDClub;

					/*
							1 Administrador club
							2 Coordinador servicio
							3 Operador reservas
							4 Porteria
						*/
					//traer perfil del man



					switch ($cliente_data->IDPerfil) {
						case '1':
							header("location: socios.php");
							break;
						case '4':
							if ($_SESSION["club"] == 9) :
								header("location: validaporteria.php");
								exit;
							else :
								header("location: invitados.php");
							endif;
							break;
						case '7':
							header("location: socios.php");
							break;
						case '9':
							header("location: socios.php");
							break;
						case '10':
							header("location: socios.php");
							break;
						case '11':
							header("location: pqr.php");
							break;
						case '12':
							header("location: banners.php");
							break;
						default:
							// Comentado para dejar el home como default

							// //traer el primer servicio asociado al usuario
							// $sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $cliente_data->IDUsuario . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
							// $qry_servicios = $dbo->query($sql_servicios);
							// $r_servicio = $dbo->fetchArray($qry_servicios);
							// header("location: reservas.php?ids=" . $r_servicio["IDServicio"]);
							header("location: index.php");
							break;
					} //end sw




					exit;
				} //end if
			}
		} else {
			header("location: login.php?msg=LI");
		}



		break;

	case 'Salir':
		$simsession->eliminar();
		header("location: login.php?msg=EX");

		break;
	default:
		header("location: login.php?msg=LI");
		break;
}
