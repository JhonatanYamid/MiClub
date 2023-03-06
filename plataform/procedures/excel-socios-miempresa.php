<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");
session_start();
//handler de sesion
$simsession = new SIMSession(SESSION_LIMIT);

//traemos lo datos de la session
$datos = $simsession->verificar();

if (!is_object($datos)) {
	SIMHTML::jsTopRedirect("login.php?msg=NSA");
	exit;
} //ebd if

//veriificamos el club de la sesion
if (!empty($_SESSION["club"]))
	$datos->club = $_SESSION["club"];
else
	$datos->club = $datos->IDClub;

//encapsulamos los parammetros
SIMUser::setFromStructure($datos);

$nombre = "Listado_Usuarios_" . date("Y_m_d");

$array_columnas = array(
	"S.IDSocio" => "",
	"EST.Nombre AS Estado" => "",
	"S.Accion" => "",
	/* "S.AccionPadre"=>"", */
	/* "P.Nombre AS Parentesco"=>"", */
	/* "S.Genero"=>"", */
	"S.NumeroDocumento" => "",
	"S.Nombre" => "",
	"S.Apellido" => "",
	/* "ES.Nombre AS EstadoSalud" => "", */
	"S.FechaNacimiento" => "",
	"S.Email AS UsuarioAPP" => "",
	"S.CorreoElectronico" => "",
	"S.Telefono" => "",
	"S.Direccion" => "",
	"S.Celular" => "",
	"S.Dispositivo" => "",
	"S.TipoSocio" => "",
	"S.Empresa" => "",
	"S.Cargo" => "",
	"S.Area" => "",
	"S.Division" => "",
	"S.Departamento" => "",
	"S.Agencia" => "",
	"S.Cargo" => "",
	"S.TipoContrato" => "",
	"S.NombreJefe" => "",
	"S.NombreSupervisor" => "",
	"S.NombrePuestoSupervisor" => "",
	/* "C.Nombre AS Categoria"=>"", */
	"S.NumeroInvitados" => "",
	"S.NumeroAccesos" => "",
	"S.PermiteReservar" => "",
	"S.FechaRegistroIngreso" => "",
	/* "S.Predio"=>"", */
);

$columReport = implode(",", array_keys($array_columnas));

/* 	echo $sql_reporte = "SELECT $columReport
					From Socio S
					LEFT JOIN EstadoSocio EST ON EST.IDEstadoSocio = S.IDEstadoSocio
					LEFT JOIN EstadoSalud ES ON ES.IDEstadoSalud = S.IDEstadoSalud
					LEFT JOIN Parentesco P ON P.IDParentesco = S.IDParentesco
					LEFT JOIN Categoria C ON C.IDCategoria = S.IDCategoria				

					WHERE S.IDClub = '".SIMUser::get("club")."'" ; */

/*$sql_reporte = "SELECT $columReport
					From Socio S, EstadoSocio EST, EstadoSalud ES					
					WHERE EST.IDEstadoSocio = S.IDEstadoSocio AND ES.IDEstadoSalud = S.IDEstadoSalud AND S.IDClub = '".SIMUser::get("club")."'" ;*/

if (!empty($_POST["IDEstadoSocio"])) {
	$IDEstadoSocio = " AND S.IDEstadoSocio='" . $_POST["IDEstadoSocio"] . "'";
}

$IDClub = $_POST["IDClub"];
$sql_reporte = "SELECT $columReport
From Socio S, EstadoSocio EST				
WHERE EST.IDEstadoSocio = S.IDEstadoSocio AND S.IDClub = " . $IDClub .  $IDEstadoSocio . " Order By S.IDSocio ASC";



$result_reporte = $dbo->query($sql_reporte);


$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios <= 0) exit;


header("Content-Type: application/vnd.ms-excel; charset=UTF-8;");
header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
header("Pragma: no-cache");
header("Expires: 0");



$sep = "\t"; // separador

// Nombre Columnas segun datos q retorna SQL
for ($i = 0; $i < $result_reporte->field_count; $i++) {
	$column = $dbo->fieldName($result_reporte, $i);
	echo $column->name . "\t";
}

// Columna adicional 
echo "Ultimo Diagnostico \t";


//Consulto los campos dinamicos
$sql_campos = "SELECT IDCampoEditarSocio, CED.Nombre
			FROM CampoEditarSocio CED
			WHERE CED.IDClub='" . $IDClub . "'
			ORDER BY CED.Orden";


$r_campos = $dbo->query($sql_campos);
while ($r = $dbo->object($r_campos)) {
	$nombre = utf8_decode($r->Nombre); // mb_convert_encoding ($r->Nombre, 'UTF-8', 'ISO-8859-1'); 
	$array_preguntas[$r->IDCampoEditarSocio] =  preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $nombre);
}

echo $columReport = implode("$sep", $array_preguntas);


print("\n");


while ($row = $dbo->fetchArray($result_reporte)) {

	$schema_row = "";
	for ($j = 0; $j < $result_reporte->field_count; $j++) {
		if (!isset($row[$j]))
			$schema_row .= "" . $sep;
		elseif (($j == 40 || $j == 41) && $row[$j] != "") // para poder descargar el certificado de vacunación
			$schema_row .= VACUNA_ROOT . "$row[$j]" . $sep;
		elseif ($row[$j] != "")
			$schema_row .= "$row[$j]" . $sep;
		else
			$schema_row .= "" . $sep;
	}
	$schema_row = str_replace($sep . "$", "", $schema_row);

	unset($array_respuesta);

	$sql_diag = "SELECT FechaTrCr FROM DiagnosticoRespuesta WHERE IDSocio = " . $row["IDSocio"] . " ORDER BY IDDiagnosticoRespuesta DESC Limit 1";
	$r_diag = $dbo->query($sql_diag);
	$row_diag = $dbo->fetchArray($r_diag);

	$schema_row .=  $row_diag["FechaTrCr"] . "\t";

	//Consulto los campos dinamicos .. ,CED.Nombre
	$sql_campos = "SELECT CED.IDCampoEditarSocio, SCES.Valor
					FROM CampoEditarSocio CED, SocioCampoEditarSocio SCES
					 WHERE SCES.IDCampoEditarSocio=CED.IDCampoEditarSocio
					 AND SCES.IDSocio= " . $row["IDSocio"] . "
					 Group by SCES.IDCampoEditarSocio
					 Order by CED.Orden";

	$r_campos = $dbo->query($sql_campos);
	while ($r = $dbo->object($r_campos)) {
		$r->Valor = str_replace("false", "", $r->Valor);
		$array_respuesta[$r->IDCampoEditarSocio] = $r->Valor; //($r->Valor != 'false')? $r->Valor : '';
	}

	foreach ($array_preguntas as $key_pregunta => $value_pregunta) {
		$schema_row .= preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $array_respuesta[$key_pregunta]) . $sep;
	}

	//Eliminar saltos de linea en Datos \n or \r

	$schema_row = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_row);
	$schema_row .= "\t";
	$schema_row = utf8_decode($schema_row);
	print(trim($schema_row));
	print "\n";
}

exit;
