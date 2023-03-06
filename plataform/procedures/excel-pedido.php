<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
	$texto = str_replace("ñ", "&ntilde;", $texto);
	$texto = str_replace("á", "&aacute;", $texto);
	$texto = str_replace("é", "&eacute;", $texto);
	$texto = str_replace("í", "&iacute;", $texto);
	$texto = str_replace("ó", "&oacute;", $texto);
	$texto = str_replace("ú", "&uacute;", $texto);
	return $texto;
}
$version = $_POST["Version"];

$sql_rest = "SELECT IDRestauranteDomicilio,Nombre FROM RestauranteDomicilio$version WHERE IDClub = '" . $_POST["IDClub"] . "'";
$r_rest = $dbo->query($sql_rest);
while ($row_rest = $dbo->fetchArray($r_rest)) {
	$array_rest[$row_rest["IDRestauranteDomicilio"]] = $row_rest["Nombre"];
}


$sql_tipopag = "SELECT * FROM TipoPago WHERE Publicar = 'S' ";
$r_tipopag = $dbo->query($sql_tipopag);
while ($row_tipopag = $dbo->fetchArray($r_tipopag)) {
	$array_tipopag[$row_tipopag["IDTipoPago"]] = $row_tipopag["Nombre"];
}

$sql_estdom = "SELECT * FROM EstadoDomicilio WHERE 1 ";
$r_estdom = $dbo->query($sql_estdom);
while ($row_estdom = $dbo->fetchArray($r_estdom)) {
	$array_estadodom[$row_estdom["IDEstadoDomicilio"]] = $row_estdom["Nombre"];
}

$sql_socios = "SELECT IDSocio,NumeroDocumento,Accion,Nombre,Apellido,TipoSocio FROM Socio WHERE IDClub = '" . $_POST["IDClub"] . "'";
$r_socios = $dbo->query($sql_socios);
while ($row_socios = $dbo->fetchArray($r_socios)) {
	$array_socios[$row_socios["IDSocio"]] = $row_socios;
}




if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
	$condicion_fecha = " AND D.FechaTrCr >= '" . $_POST["FechaInicio"] . " 00:00:00'  and D.FechaTrCr <= '" . $_POST["FechaFin"] . " 23:59:59'";
}

//saco el IDConfiguracionDomicilios
$IDConfiguracionDomicilios = $dbo->getFields("ConfiguracionDomicilios" . $_POST["Version"], "IDConfiguracionDomicilios", "IDClub = '" . $_POST["IDClub"] . "' AND Activo='S' LIMIT 1");





$sql_reporte = "SELECT D.*,P.Nombre as NombreProducto, P.Proveedor as Proveedor, DD.Cantidad, DD.ValorUnitario, DD.Comentario as ComentarioProducto,D.FormaPago,D.IDTipoPago
									From Domicilio" . $_POST["Version"] . " D, DomicilioDetalle" . $_POST["Version"] . " DD, Producto" . $_POST["Version"] . " P
									Where D.IDDomicilio=DD.IDDomicilio AND DD.IDProducto=P.IDProducto AND D.IDClub = '" . $_POST["IDClub"] . "' AND D.IDSocio > 0 " . $condicion_fecha . "
									ORDER BY D.IDDomicilio DESC";




$result_reporte = $dbo->query($sql_reporte);

$nombre = "Pedido_Corte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	//PARA LUKER MUESTRO OTRO NOMBRE
	if ($_POST["IDClub"] == 95 || $_POST["IDClub"] == 96 || $_POST["IDClub"] == 97) {
		$ComentarioClub = "COMENTARIO EMPRESA";
	} else {
		$ComentarioClub = "COMENTARIO CLUB";
	}
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>NUMERO DOMICILIO</th>";
	$html .= "<th>ACCION</th>";
	$html .= "<th>SOCIO</th>";
	$html .= "<th>CATEGORIA SOCIO</th>";
	$html .= "<th>ESTADO</th>";
	$html .= "<th>TOTAL</th>";
	$html .= "<th>FECHA/HORA ENTREGA</th>";
	$html .= "<th>COMENTARIO SOCIO</th>";
	$html .= "<th>" . $ComentarioClub . "</th>";
	$html .= "<th>CELULAR</th>";
	$html .= "<th>DIRECCION</th>";
	$html .= "<th>VALOR DOMICILIO</th>";
	$html .= "<th>PROPINA</th>";
	$html .= "<th>FORMA DE PAGO</th>";
	$html .= "<th>MEDIO DE PAGO</th>";
	$html .= "<th>ESTADO TRANSACCION</th>";
	$html .= "<th>PRODUCTO</th>";
	$html .= "<th>CANTIDAD</th>";
	$html .= "<th>COMENTARIO</th>";
	$html .= "<th>PROVEEDOR</th>";
	$html .= "<th>RESTAURANTE</th>";
	$html .= "<th>VALOR UNITARIO</th>";
	$html .= "<th>TOTAL</th>";

	//Buscamos las preguntas configuradas
	$sqlPreguntas = "SELECT * FROM DomicilioPregunta WHERE IDConfiguracionDomicilio='" . $IDConfiguracionDomicilios . "' AND Publicar='S' ORDER BY IDDomicilioPregunta";

	$QueryPreguntas = $dbo->query($sqlPreguntas);
	while ($DatosPreguntas = $dbo->fetchArray($QueryPreguntas)) {
		$array_preguntas[] = $DatosPreguntas["IDDomicilioPregunta"];
		$html .= "<th>" .  remplaza_tildes($DatosPreguntas["Nombre"]) .  "</th>";
	}


	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {
		unset($array_respuesta_socio);

		if ($Datos["IDEstadoDomicilio"] == 3)
			$color_linea = "#F43125";
		else
			$color_linea = "#000000";



		$bitacora = "";
		unset($array_datos_seguimiento);
		$html .= "<tr style='color: " . $color_linea . "'>";
		$html .= "<td>" . $Datos["Numero"] . "</td>";
		$html .= "<td>" .	$array_socios[$Datos["IDSocio"]]["Accion"] . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($array_socios[$Datos["IDSocio"]]["Nombre"] . " " . 	$array_socios[$Datos["IDSocio"]]["Apellido"]))) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($array_socios[$Datos["IDSocio"]]["TipoSocio"]))) .  "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($array_estadodom[$Datos["IDEstadoDomicilio"]]))) . "</td>";
		$html .= "<td>" . $Datos["Total"] . "</td>";
		$html .= "<td>" . $Datos["HoraEntrega"] . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["ComentariosSocio"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["ComentariosClub"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Celular"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Direccion"]) . "</td>";
		$html .= "<td>" . $Datos["ValorDomicilio"] . "</td>";
		$html .= "<td>" . $Datos["Propina"] . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["FormaPago"]) . "</td>";
		$html .= "<td>" . $array_tipopag[$Datos["IDTipoPago"]] . "</td>";
		$html .= "<td>" . $dbo->getFields("PagoCredibanco", "errorMessage", "NumeroFactura = '" . $Datos["IDDomicilio"] . "'") . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["NombreProducto"]) . "</td>";
		$html .= "<td>" . $Datos["Cantidad"] . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["ComentarioProducto"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Proveedor"]) . "</td>";
		$html .= "<td>" . $array_rest[$Datos["IDRestauranteDomicilio"]] . "</td>";
		$html .= "<td>" . $Datos["ValorUnitario"] . "</td>";
		$html .= "<td>" . $Datos["Total"] . "</td>";


		//Buscamos las respuestas 
		$sqlRespuestas = "SELECT * FROM DomicilioCampo WHERE IDDomicilio='" . $Datos["IDDomicilio"] . "'";

		$QueryRespuestas = $dbo->query($sqlRespuestas);
		while ($DatosRespuesta = $dbo->fetchArray($QueryRespuestas)) {
			$array_respuesta_socio[$DatosRespuesta["IDDomicilioPregunta"]] = $DatosRespuesta["Valor"];
		}
		if (count($array_preguntas) > 0) :
			foreach ($array_preguntas as $id_pregunta) :
				$html .= "<td>" . remplaza_tildes($array_respuesta_socio[$id_pregunta])   . "</td>";
			endforeach;
		endif;

		$html .= "</tr>";
	}
	$html .= "</table>";


	//construimos el excel
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");


	echo $html;
	exit();
}
