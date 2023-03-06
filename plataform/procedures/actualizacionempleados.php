<?
/*ini_set('display_errors', 1);
 
ini_set('display_startup_errors', 1);
 
error_reporting(E_ALL);*/

function uploadFTP($path, $file)
{
	// FTP server details
	$ftpHost   = 'www.ventasencatalogo.com';
	$ftpUsername = 'archivosluker@ventasencatalogo.com';
	$ftpPassword = 'lukerarch4';

	// open an FTP connection
	$connId = ftp_connect($ftpHost) or die("Couldn't connect to $ftpHost");

	// login to FTP server
	$ftpLogin = ftp_login($connId, $ftpUsername, $ftpPassword);

	// local & server file path
	$localFilePath  = 'index.php';
	$remoteFilePath = 'public_html/index.php';

	// try to upload file
	if (ftp_put($connId, $path, $file, FTP_ASCII)) {
		//echo "File transfer successful - $localFilePath";
	} else {
		//echo "There was an error while uploading $localFilePath";
	}

	// close the connection
	ftp_close($connId);
}

SIMReg::setFromStructure(array(
	"title" => "Empleado",
	"table" => "LukerEmpleado",
	"key" => "IDLukerEmpleado",
	"mod" => "Socio"
));


$script = "actualizacionempleados";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




switch (SIMNet::req("action")) {

	case "edit":
		$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
		$view = "views/" . $script . "/form.php";
		$newmode = "update";
		$titulo_accion = "Actualizar";

		break;


	case "view-beneficiarios":
		$view = "views/" . $script . "/beneficiarios.php";
		$newmode = "";
		$titulo_accion = "Ver beneficiarios";

		break;

	case "view-estudios":
		$view = "views/" . $script . "/estudios.php";
		$newmode = "";
		$titulo_accion = "Ver estudios";

		break;


	case "rechazar":

		$id = $dbo->update(["Estado" => "A", "Motivo" => $_POST["Motivo"]], "LukerEmpleado", "IDLukerEmpleado", $_POST["IDLukerEmpleado"]);

		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroRechazadoCorrectamente', LANGSESSION));
		SIMHTML::jsRedirect("actualizacionempleados.php");

		echo json_encode([
			"status" => "OK",
			"message" => "Se ha devuelto el proceso de actualización al empleado con el comentario"
		]);
		exit;

		break;

	case "confirmar":

		$tns = "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = " . HOST_LUKER . ")(PORT = " . PORT_LUKER . ")))
				(CONNECT_DATA = (SERVICE_NAME = " . BASE_LUKER . ")))";
		try {
			$conn = new PDO("oci:dbname=" . $tns, USER_LUKER, PASSWORD_LUKER);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo 'ERROR: ' . $e->getMessage();
		}

		$IDEmpleado = SIMNet::reqInt("id");
		$sql = "SELECT * FROM LukerEmpleado WHERE IDLukerEmpleado=$IDEmpleado LIMIT 1";
		$queryEmpleado = $dbo->query($sql);
		$frm = $dbo->fetch($queryEmpleado);

		$insert["IDLukerEmpleado"] = $frm["IDLukerEmpleado"];
		$insert["EMP_CEDULA"] = $frm["EMP_CEDULA"];
		$insert["EMP_NOMBRE"] = $frm["EMP_NOMBRE"];
		$insert["EMP_APELLIDO1"] = $frm["EMP_APELLIDO1"];
		$insert["EMP_APELLIDO2"] = $frm["EMP_APELLIDO2"];
		$insert["EMP_TIPO_SANGRE"] = $frm["EMP_TIPO_SANGRE"];
		$insert["EMP_SANGRE_RH"] = $frm["EMP_SANGRE_RH"];
		$insert["EMP_ESTADO_CIVIL"] = $frm["EMP_ESTADO_CIVIL"];
		$insert["EMP_PROFESION"] = $frm["EMP_PROFESION"];
		$insert["EMP_MATRICULA_PROFESIONAL"] = $frm["EMP_MATRICULA_PROFESIONAL"];
		$insert["UGN1_CODIGO_RESID"] = $frm["UGN1_CODIGO_RESID"];
		$insert["UGN2_CODIGO_RESID"] = $frm["UGN2_CODIGO_RESID"];
		$insert["UGN3_CODIGO_RESID"] = $frm["UGN3_CODIGO_RESID"];
		$insert["EMP_DIRECCION"] = $frm["EMP_DIRECCION"];
		$insert["EMP_TELEFONO"] = $frm["EMP_TELEFONO"];
		$insert["EMP_BARRIO"] = $frm["EMP_BARRIO"];
		$insert["EMP_VIVIENDA"] = $frm["EMP_VIVIENDA"];
		$insert["EMP_ADQ_EMPRESA"] = $frm["EMP_ADQ_EMPRESA"];
		$insert["EMP_PERSONA_ACUDIENTE"] = $frm["EMP_PERSONA_ACUDIENTE"];
		$insert["EMP_TELEFONO_ACUDIENTE"] = $frm["EMP_TELEFONO_ACUDIENTE"];
		$insert["EMP_DIRECCION_ACUDIENTE"] = $frm["EMP_DIRECCION_ACUDIENTE"];
		$insert["TIEM_CAMPO_ALF1"] = $frm["TIEM_CAMPO_ALF1"];
		$insert["TIEM_CAMPO_ALF2"] = $frm["TIEM_CAMPO_ALF2"];
		$insert["TIEM_CAMPO_ALF3"] = $frm["TIEM_CAMPO_ALF3"];
		$insert["TIEM_CAMPO_ALF4"]	= $frm["TIEM_CAMPO_ALF4"];
		$insert["BENEF_CAMPO_NUM3"] = $frm["BENEF_CAMPO_NUM3"];
		$insert["BENEF_CAMPO_IND1"] = $frm["BENEF_CAMPO_IND1"];
		$insert["BENEF_CAMPO_IND2"] = $frm["BENEF_CAMPO_IND2"];
		$insert["BENEF_CAMPO_IND3"] = $frm["BENEF_CAMPO_IND3"];
		$insert["BENEF_CAMPO_NUM1"] = empty($frm["BENEF_CAMPO_NUM1"]) ? 0 : $frm["BENEF_CAMPO_NUM1"];
		$insert["BENEF_CAMPO_IND5"] = $frm["BENEF_CAMPO_IND5"];
		$insert["BENEF_CAMPO_IND6"] = $frm["BENEF_CAMPO_IND6"];
		$insert["BENEF_CAMPO_ALF1"] = $frm["BENEF_CAMPO_ALF1"];
		$insert["BENEF_CAMPO_ALF2"] = $frm["BENEF_CAMPO_ALF2"];
		$insert["BENEF_CAMPO_ALF3"] = $frm["BENEF_CAMPO_ALF3"];
		$insert["BENEF_CAMPO_ALF5"] = empty($frm["BENEF_CAMPO_ALF5"]) ? "NA" : $frm["BENEF_CAMPO_ALF5"];
		$insert["BENEF_CAMPO_NUM2"] = $frm["BENEF_CAMPO_NUM2"];
		$insert["BENEF_CAMPO_NUM4"] = $frm["BENEF_CAMPO_NUM4"];
		$insert["BENEF_CAMPO_NUM5"] = $frm["BENEF_CAMPO_NUM5"];

		$sql = "INSERT INTO LukerEmpleado (
					IDLukerEmpleado,
					EMP_CEDULA, 
					EMP_NOMBRE, 
					EMP_APELLIDO1, 
					EMP_APELLIDO2, 
					EMP_TIPO_SANGRE,					 
					EMP_SANGRE_RH, 
					EMP_ESTADO_CIVIL,
					EMP_PROFESION, 
					EMP_MATRICULA_PROFESIONAL, 
					UGN1_CODIGO_RESID, 
					UGN2_CODIGO_RESID, 
					UGN3_CODIGO_RESID, 
					EMP_DIRECCION, 
					EMP_TELEFONO, 
					EMP_BARRIO, 
					EMP_VIVIENDA, 
					EMP_ADQ_EMPRESA,
					EMP_PERSONA_ACUDIENTE, 
					EMP_TELEFONO_ACUDIENTE, 
					EMP_DIRECCION_ACUDIENTE, 
					TIEM_CAMPO_ALF1, 
					TIEM_CAMPO_ALF2, 
					TIEM_CAMPO_ALF3, 
					TIEM_CAMPO_ALF4, 
					BENEF_CAMPO_NUM3, 
					BENEF_CAMPO_IND1, 
					BENEF_CAMPO_IND2, 
					BENEF_CAMPO_IND3,
					BENEF_CAMPO_NUM1, 
					BENEF_CAMPO_IND5, 
					BENEF_CAMPO_IND6, 
					BENEF_CAMPO_ALF1, 
					BENEF_CAMPO_ALF2, 
					BENEF_CAMPO_ALF3, 
					BENEF_CAMPO_ALF5,
					BENEF_CAMPO_NUM2,
					BENEF_CAMPO_NUM4,
					BENEF_CAMPO_NUM5
				) 
					VALUES (
						'{$insert["IDLukerEmpleado"]}',
						{$insert["EMP_CEDULA"]},
						'{$insert["EMP_NOMBRE"]}',
						'{$insert["EMP_APELLIDO1"]}',
						'{$insert["EMP_APELLIDO2"]}',
						'{$insert["EMP_TIPO_SANGRE"]}',
						'{$insert["EMP_SANGRE_RH"]}',
						'{$insert["EMP_ESTADO_CIVIL"]}',
						{$insert["EMP_PROFESION"]},
						'{$insert["EMP_MATRICULA_PROFESIONAL"]}',
						{$insert["UGN1_CODIGO_RESID"]},			
						{$insert["UGN2_CODIGO_RESID"]},			
						{$insert["UGN3_CODIGO_RESID"]},			
						'{$insert["EMP_DIRECCION"]}',
						'{$insert["EMP_TELEFONO"]}',
						'{$insert["EMP_BARRIO"]}',
						'{$insert["EMP_VIVIENDA"]}',
 						'{$insert["EMP_ADQ_EMPRESA"]}',
						'{$insert["EMP_PERSONA_ACUDIENTE"]}',
						'{$insert["EMP_TELEFONO_ACUDIENTE"]}',
						'{$insert["EMP_DIRECCION_ACUDIENTE"]}',
						{$insert["TIEM_CAMPO_ALF1"]},
						'{$insert["TIEM_CAMPO_ALF2"]}',
						'{$insert["TIEM_CAMPO_ALF3"]}',
						'{$insert["TIEM_CAMPO_ALF4"]}',	
						{$insert["BENEF_CAMPO_NUM3"]},
						'{$insert["BENEF_CAMPO_IND1"]}',
						'{$insert["BENEF_CAMPO_IND2"]}',
						'{$insert["BENEF_CAMPO_IND3"]}',
						'{$insert["BENEF_CAMPO_NUM1"]}',
						'{$insert["BENEF_CAMPO_IND5"]}',
						'{$insert["BENEF_CAMPO_IND6"]}',
						'{$insert["BENEF_CAMPO_ALF1"]}',
						'{$insert["BENEF_CAMPO_ALF2"]}',
						'{$insert["BENEF_CAMPO_ALF3"]}',
						'{$insert["BENEF_CAMPO_ALF5"]}',
						{$insert["BENEF_CAMPO_NUM2"]},
						{$insert["BENEF_CAMPO_NUM4"]},
						{$insert["BENEF_CAMPO_NUM5"]}
			)";

		//var_dump($sql);

		$stmt = $conn->prepare($sql);
		$stmt->execute();

		/**Subir archivo al ftp */
		if (!empty($frm["Foto"])) {
			uploadFTP("/actualizacionempleado/empleados/" . $frm["Foto"], "/home/http/miempresapp/app/file/luker/actualizacionempleados/empleados/" . $frm["Foto"]);
		}

		$sql = "SELECT * FROM LukerBeneficiario WHERE IDLukerEmpleado=$IDEmpleado";
		$queryBeneficiario = $dbo->query($sql);
		$beneficiarios = $dbo->fetch($queryBeneficiario);
		$beneficiarios = isset($beneficiarios["IDLukerEmpleado"]) ? [$beneficiarios] : $beneficiarios;

		//var_dump($sql);

		foreach ($beneficiarios as $frm) {
			$insert["IDLukerBeneficiario"] = $frm["IDLukerBeneficiario"];
			$insert["IDLukerEmpleado"] = $frm["IDLukerEmpleado"];
			$insert["NOMBRE"] = $frm["NOMBRE"];
			$insert["APELLIDO1"] = $frm["APELLIDO1"];
			$insert["APELLIDO2"] = $frm["APELLIDO2"];
			$insert["RELAC_FAM"] = $frm["RELAC_FAM"];
			$insert["SEXO"] = $frm["SEXO"];
			$insert["TIPO_IDENT"] = $frm["TIPO_IDENT"];
			$insert["IDENT_NUM"] = $frm["IDENT_NUM"];
			$insert["UGN1_CODIGO_IDENT"] = $frm["UGN1_CODIGO_IDENT"];
			$insert["UGN2_CODIGO_IDENT"] = $frm["UGN2_CODIGO_IDENT"];
			$insert["UGN3_CODIGO_IDENT"] = $frm["UGN3_CODIGO_IDENT"];
			$insert["BENE_TIPO_SANGRE"] = $frm["BENE_TIPO_SANGRE"];
			$insert["BENE_SANGRE_RH"] = $frm["BENE_SANGRE_RH"];
			$insert["FEC_NACIO"] = $frm["FEC_NACIO"];
			$insert["UGN1_CODIGO_NACI"] = $frm["UGN1_CODIGO_NACI"];
			$insert["UGN2_CODIGO_NACI"] = $frm["UGN2_CODIGO_NACI"];
			$insert["UGN3_CODIGO_NACI"] = $frm["UGN3_CODIGO_NACI"];
			$insert["EST_CIVIL"] = $frm["EST_CIVIL"];
			$insert["BENEF_CAMPO_IND4"] = $frm["BENEF_CAMPO_IND4"];
			$insert["PROFESION"] = $frm["PROFESION"];
			$insert["DIRECCION"] = $frm["DIRECCION"];
			$insert["TELEFONO"] = $frm["TELEFONO"];
			$insert["BENEF_CAMPO_IND2"] = $frm["BENEF_CAMPO_IND2"];
			$insert["BENEF_CAMPO_ALF5"]	= empty($frm["BENEF_CAMPO_ALF5"]) ? "NA" : $frm["BENEF_CAMPO_ALF5"];
			$insert["BENEF_CAMPO_IND3"] = $frm["BENEF_CAMPO_IND3"];
			$insert["BENEF_CAMPO_NUM1"] = empty($frm["BENEF_CAMPO_NUM1"]) ? 0 : $frm["BENEF_CAMPO_NUM1"];
			$insert["RESIDE_EMPLEADO"] = $frm["RESIDE_EMPLEADO"];
			$insert["BENE_ESTADO"] = $frm["BENE_ESTADO"];
			$insert["DEPENDIENTE"] = $frm["DEPENDIENTE"];
			$insert["OPERACION"] = $frm["OPERACION"];

			$BENE_ESTADO = $insert["BENE_ESTADO"] == "S" ? "ACT" : "INA";

			$sql = "INSERT INTO LukerBeneficiario (
					IDLuckerBeneficiario,
					IDLukerEmpleado, 
					NOMBRE, 
					APELLIDO1, 
					APELLIDO2, 
					RELAC_FAM, 
					SEXO, 
					TIPO_IDENT, 
					IDENT_NUM,
					UGN1_CODIGO_IDENT, 
					UGN2_CODIGO_IDENT,
					UGN3_CODIGO_IDENT, 									
					BENE_TIPO_SANGRE, 
					BENE_SANGRE_RH, 
					FEC_NACIO,
					UGN1_CODIGO_NACI,
					UGN2_CODIGO_NACI,
					UGN3_CODIGO_NACI,
					EST_CIVIL,
					BENEF_CAMPO_IND4,
					PROFESION,
					DIRECCION,
					TELEFONO,
					BENEF_CAMPO_IND2,
					BENEF_CAMPO_ALF5,
					BENEF_CAMPO_IND3,
					BENEF_CAMPO_NUM1,				
					RESIDE_EMPLEADO,
					BENE_ESTADO,
					DEPENDIENTE, 
					OPERACION
				) 
					VALUES (
						{$insert["IDLukerBeneficiario"]},
						'{$insert["IDLukerEmpleado"]}',
						'{$insert["NOMBRE"]}',
						'{$insert["APELLIDO1"]}',
						'{$insert["APELLIDO2"]}',
						{$insert["RELAC_FAM"]},
						'{$insert["SEXO"]}',
						'{$insert["TIPO_IDENT"]}',
						'{$insert["IDENT_NUM"]}',
						{$insert["UGN1_CODIGO_IDENT"]},
						{$insert["UGN2_CODIGO_IDENT"]},
						{$insert["UGN3_CODIGO_IDENT"]},
						'{$insert["BENE_TIPO_SANGRE"]}',
						'{$insert["BENE_SANGRE_RH"]}',
						TO_DATE('{$insert["FEC_NACIO"]}', 'YYYY-MM-DD'),
						{$insert["UGN1_CODIGO_NACI"]},
						{$insert["UGN2_CODIGO_NACI"]},
						{$insert["UGN3_CODIGO_NACI"]},
						'{$insert["EST_CIVIL"]}',
						'{$insert["BENEF_CAMPO_IND4"]}',
						{$insert["PROFESION"]},
						'{$insert["DIRECCION"]}',
						'{$insert["TELEFONO"]}',
						'{$insert["BENEF_CAMPO_IND2"]}',
						'{$insert["BENEF_CAMPO_ALF5"]}',
						'{$insert["BENEF_CAMPO_IND3"]}',
						{$insert["BENEF_CAMPO_NUM1"]},			
						'{$insert["RESIDE_EMPLEADO"]}',
						'{$BENE_ESTADO}',
						'{$insert["DEPENDIENTE"]}',
						'{$insert["OPERACION"]}'
				)";

			//var_dump($sql);				

			$stmt = $conn->prepare($sql);
			$stmt->execute();

			/**Subir archivo al ftp */
			if (!empty($frm["Foto"])) {
				uploadFTP("/actualizacionempleado/beneficiarios/" . $frm["Archivo"], "/home/http/miempresapp/app/file/luker/actualizacionempleados/beneficiarios/" . $frm["Archivo"]);
			}
		}

		$sql = "SELECT * FROM LukerEstudio WHERE IDLukerEmpleado=$IDEmpleado";
		$queryEstudio = $dbo->query($sql);
		$estudios = $dbo->fetch($queryEstudio);
		$estudios = isset($estudios["IDLukerEmpleado"]) ? [$estudios] : $estudios;

		//var_dump($sql);

		foreach ($estudios as $frm) {

			$insert["IDLukerEstudio"] = $frm["IDLukerEstudio"];
			$insert["IDLukerEmpleado"] = $frm["IDLukerEmpleado"];
			$insert["NEST_CODIGO"] = $frm["NEST_CODIGO"];
			$insert["UGN1_CODIGO"] = $frm["UGN1_CODIGO"];
			$insert["UGN2_CODIGO"] = $frm["UGN2_CODIGO"];
			$insert["UGN3_CODIGO"] = $frm["UGN3_CODIGO"];
			$insert["TERC_DOCUMENTO"] = $frm["TERC_DOCUMENTO"];
			$insert["ESXB_DESC_ADIC_INSTITU"] = (empty($frm["ESXB_DESC_ADIC_INSTITU"])) ? "NA" : $frm["ESXB_DESC_ADIC_INSTITU"];
			$insert["ESXB_TITULO"] = $frm["ESXB_TITULO"];
			$insert["ESXB_FECHA_RET"] = $frm["ESXB_FECHA_RET"];
			$insert["ESXB_IDIOMAS"] = empty($frm["ESXB_IDIOMAS"]) ? "NA" : $frm["ESXB_IDIOMAS"];
			$insert["OPERACION"] = $frm["OPERACION"];

			$sql = "INSERT INTO LukerEstudio (
					IDLuckerestudiobenef,
					IDLukerEmpleado, 
					NEST_CODIGO, 
					UGN1_CODIGO, 
					UGN2_CODIGO, 
					UGN3_CODIGO, 
					TERC_DOCUMENTO, 
					ESXB_DESC_ADIC_INSTITU, 
					ESXB_TITULO,
					ESXB_FECHA_RET, 
					ESXB_IDIOMAS,			
					OPERACION
				) 
					VALUES (
						{$insert["IDLukerEstudio"]},
						{$insert["IDLukerEmpleado"]},
						{$insert["NEST_CODIGO"]},
						{$insert["UGN1_CODIGO"]},
						{$insert["UGN2_CODIGO"]},
						{$insert["UGN3_CODIGO"]},
						{$insert["TERC_DOCUMENTO"]},
						'{$insert["ESXB_DESC_ADIC_INSTITU"]}',
						'{$insert["ESXB_TITULO"]}',
						to_date('{$insert["ESXB_FECHA_RET"]}','yyyy-mm-dd'),
						'{$insert["ESXB_IDIOMAS"]}',					
						'{$insert["OPERACION"]}'
				)";

			//var_dump($sql);

			$stmt = $conn->prepare($sql);
			$stmt->execute();

			/**Subir archivo al ftp */
			if (!empty($frm["Foto"])) {
				uploadFTP("/actualizacionempleado/estudios/" . $frm["Archivo"], "/home/http/miempresapp/app/file/luker/actualizacionempleados/estudios/" . $frm["Archivo"]);
			}
		}


		$id = $dbo->update(["Estado" => "C"], "LukerEmpleado", "IDLukerEmpleado", $frm["IDLukerEmpleado"]);

		SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
		SIMHTML::jsRedirect("actualizacionempleados.php");

		break;


	case "search":
		$view = "views/" . $script . "/list.php";
		break;


	default:
		$view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
	$view = "views/" . $script . "/form.php";
