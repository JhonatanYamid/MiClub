<?php
	require( "config.inc.php" );
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	//require_once(LIBDIR.'nusoap/lib/nusoap.php');
	

	$p = xml_parser_create();
	xml_parse_into_struct($p, $response, $vals, $index);
	xml_parser_free($p);
	$client = new SoapClient("http://wso2dsvs.ucatolica.edu.co:2763/services/Activos_Bach_app?wsdl");
	$response = $client->__soapCall("Activos_Bach_app_OPR", array());
	print_r($response);


	exit;

	$dbhost="localhost:27017";
	$dbname= "LogsApp";
	$dbuser="miclubapp01";
	$dbpass="4P2231e2L3ebcg23";


	//$uri = 'mongodb://miclubapp01:4P2231e2L3ebcg23@localhost:27017/LogsApp';
	$uri = 'mongodb://RegistroLogsOp:9SbWG*rCe3de*PO9jnhH@10.73.117.83:27017/LogsMiClubAppProd';
	$manager = new MongoDB\Driver\Manager($uri); // conectar
	//var_dump($manager);

	//Insertar
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->insert(['Fecha' => $FechaInsertar]);
	$manager->executeBulkWrite('LogsMiClubAppProd.Operacion', $bulk);

	echo "FIN";

	exit;

	/*
	$dbhost="10.73.117.83:27017";
	$dbname= "LogsMiClubAppProd";
	$dbuser="RegistroLogsOp";
	$dbpass="9SbWG*rCe3de*PO9jnhH";
*/

	echo $uri = 'mongodb://'.$dbuser.':"'.$dbpass.'"@'.$dbhost.'/'.$dbname;
	//$uri = 'mongodb://"RegistroLogsOp":"vih/-9SbWGrCe3de*PO9jnhH"@52.117.33.66:27017/?authSource=LogsMiClubAppProd&readPreference=primary&appname=MongoDB%20Compass&ssl=false';
	$conexionmongo = new MongoDB\Driver\Manager($uri); // conectar
	print_r($conexionmongo);
	if(!$conexionmongo->getServers()){
		//echo "Error: Fallo al conectarse a MongoDB \n";
		//exit;
	}

	//Insertar
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->insert(['Fecha' => $FechaInsertar]);
	$manager->executeBulkWrite('LogsMiClubAppProd.Operacion', $bulk);

	echo "fin";
	exit;

/*
	try {

	$mng = new MongoDB\Driver\Manager("mongodb://localhost:2717");
	$query = new MongoDB\Driver\Query([], ['sort' => [ 'name' => 1], 'limit' => 5]);

	$rows = $mng->executeQuery("testdb.cars", $query);

	foreach ($rows as $row) {

			echo "$row->name : $row->price\n";
	}

} catch (MongoDB\Driver\Exception\AuthenticationException $e) {

	echo "Exception:", $e->getMessage(), "\n";
} catch (MongoDB\Driver\Exception\ConnectionException $e) {

	echo "Exception:", $e->getMessage(), "\n";
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {

	echo "Exception:", $e->getMessage(), "\n";
}
*/

	/*
	$_GET["Param1"]=1;
	$_GET["Param2"]=2;
	$_GET["Param3"]=3;
	$_GET["Param4"]=4;
	$_GET["Param5"]=5;
	//$datos=json_encode($_GET);
	$datos=$_GET;
	//$datos=['Nombre' => 'CLASE2','Apellido'=>'CLASE3'];
	$dboMongo->insert($datos,'Operacion');
	echo "Insertado final ";
	exit;
	*/


	$orig_date = new DateTime('2016-01-22  14:00:00');
	# or you can use any other way to construct with (int timestamp)
	echo $mongo_date = new MongoDB\BSON\UTCDateTime($orig_date->getTimestamp());
	echo "<br>".$today = new MongoDB\BSON\UTCDateTime((new DateTime($today))->getTimestamp()*1000);

	$orig_date = new DateTime('2016-06-27 13:03:33.15800');
$orig_date=$orig_date->getTimestamp();
echo "<br>NEW:".$utcdatetime = new MongoDB\BSON\UTCDateTime($orig_date*1000);

$datetime = $utcdatetime->toDateTime();
$time=$datetime->format(DATE_RSS);
/********************Convert time local timezone*******************/

$dateInUTC=$time;
$time = strtotime($dateInUTC.' UTC');
$dateInLocal = date("Y-m-d H:i:s.v", $time);
echo "<br>".$dateInLocal;

//milisegundos php
$now = DateTime::createFromFormat('U.u', microtime(true));
echo "<br>M:".$now->format("Y-m-d H:i:s.u");
$d = new \DateTime();
//fin

$FechaActual=(
  ($FechaInsertar=new MongoDB\BSON\UTCDateTime())->toDateTime()->format('U.u')
);

$FechaActual=(
  ($FechaInsertar=new MongoDB\BSON\UTCDateTime())->toDateTime()->format('U.u')
);

$date = DateTime::createFromFormat( 'Y-m-d H:i:s', "2015-10-08 00:00:00");
$FechaInsertar = new \MongoDB\BSON\UTCDateTime( $date->format('U') * 1000 );

	$uri = 'mongodb://miclubapp01:4P2231e2L3ebcg23@localhost:27017/LogsApp';
	$manager = new MongoDB\Driver\Manager($uri); // conectar
	var_dump($manager);

	//Insertar
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->insert(['Fecha' => $FechaInsertar]);
	$manager->executeBulkWrite('LogsApp.Operacion', $bulk);

	echo "FIN";

	//DELETE
	$bulk->delete(['Nombre' => 'Pedro'], ['limit' => 1]);
	//$result = $manager->executeBulkWrite('LogsApp.Operacion', $bulk);

	$bulk->update(
    ['Nombre' => 'Jorge'],
    ['$set' => ['Apellido' => 'Chirivi Castellanos']],
    ['multi' => false, 'upsert' => false]
);
//$result = $manager->executeBulkWrite('LogsApp.Operacion', $bulk);



	/*
	$filter = ['Nombre' => ['$gt' => 'Chirivi']];

	*/
	$filter = ['Apellido' => 'Chirivi'];
	$options = [
	    'projection' => ['_id' => 0],
	    'sort' => ['Nombre' => -1],
	];

	//$filter = ['id' => ['ObjectId'=> '608b50a91c57f236935b8b06']  ];
	//$filter['_id']=new MongoDB\BSON\ObjectID('608b50a91c57f236935b8b06');
	$options = [
	    'projection' => ['_id' => 0],
	    'sort' => ['Nombre' => -1],
	];

	$query = new MongoDB\Driver\Query($filter, $options);
	$cursor = $manager->executeQuery('LogsApp.Operacion', $query);

	foreach ($cursor as $document) {
		echo "<br>Resultado:" . $document->Nombre .  " " . $document->Apellido;
    //var_dump($document);
	}




	exit;



	if (function_exists('curl_exec')) {
	    echo "Las funciones de curl_exec están disponibles.<br />\n";
	} else {
	    echo "Las funciones de curl_exec no están disponibles.<br />\n";
	}


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.zonapagos.com/Apis_CicloPago/api/VerificacionPago',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
"int_id_comercio": 29995,
"str_usr_comercio": "NADESBA29995",
"str_pwd_Comercio": "NADESBA29995*",
"str_id_pago": "721",
"int_no_pago":-1
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

echo "FIN";
exit;



	/*
	$sql_r="SELECT * FROM ReservaGeneral Where IDClub = 85 and Fecha >='2021-02-05' and NombreSocio =''";
	$r_reserva=$dbo->query($sql_r);
	while($row_reserva=$dbo->fetchArray($r_reserva)){

		$NombreSocioReserva="";
		$AccionSocioReserva="";
		$NombreBenefReserva="";
		$AccionBenefReserva="";

		$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row_reserva["IDSocio"] . "' ", "array" );
		$NombreSocioReserva=$datos_socio["Nombre"]." " . $datos_socio["Apellido"];
		$AccionSocioReserva=$datos_socio["Accion"];

		if(!empty($row_reserva["IDBeneficiario"])){
			$datos_beneficiario = $dbo->fetchAll( "Socio", " IDSocio = '" . $row_reserva["IDBeneficiario"] . "' ", "array" );
			$NombreBenefReserva=$datos_beneficiario["Nombre"]." " . $datos_beneficiario["Apellido"];
			$AccionBenefReserva=$datos_beneficiario["Accion"];
		}

			$sql_actualiza="UPDATE ReservaGeneral
										  SET NombreSocio='".$NombreSocioReserva."',AccionSocio='".$AccionSocioReserva."',NombreBeneficiario='".$NombreBenefReserva."',AccionBeneficiario='".$AccionBenefReserva."'
										  WHERE IDReservaGeneral = '".$row_reserva["IDReservaGeneral"]."'"	;

	    //$dbo->query($sql_actualiza);
			echo "<br>".$sql_actualiza;
			$suma_reserva++;
	}
	echo "<br><br>Total " . $suma_reserva;
	exit;
*/




// Crear un cliente apuntando al script del servidor (Creado con WSDL) -
// Las proximas 3 lineas son de configuracion, y debemos asignarlas a nuestros parametros


//Resumen Entradas
//$sql_ocupacion_entrada = "SELECT * From LogAccesoVista Where Tipo = '' and IDClub = '9' and Entrada = 'S' and FechaIngreso >= '2021-01-13 00:00:00' and FechaIngreso <= '2021-01-13 23:59:59' ";

/*
$sql_ocupacion_entrada = "SELECT  *
					From LogAcceso
					Where Tipo <> '' and IDClub = '9' and Entrada = 'S' and FechaIngreso >= '2021-01-13 00:00:00' and FechaIngreso <= '2021-01-13 23:59:59' ";


$result_ocupacion_entrada = $dbo->query($sql_ocupacion_entrada);
while( $r_ocupacion_entrada = $dbo->fetchArray( $result_ocupacion_entrada ) ):
		$tipo_entrada=$r_ocupacion_entrada["Tipo"];
		if($tipo_entrada=="Contratista"):
			$IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$r_ocupacion_entrada["IDInvitacion"]."'" );
			$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDTipoInvitado" , "IDInvitado = '".$IDInvitado."'" );
			$tipo_invitado=$dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$IDTipoInvitado."'" );
			if(!empty($tipo_invitado))
				$tipo_entrada=$tipo_invitado;
		endif;


		if($tipo_entrada=="EMPLEADOS MDY"){
			echo "<br>" . $r_ocupacion_entrada["IDLogAcceso"];
		}

		$array_ocupacion_entrada[ $tipo_entrada ] ++;
endwhile;
//FIN Resumen Entradas
print_r($array_ocupacion_entrada);
exit;
*/


/*
$sql_ocupacion_salida = "Select *
					From LogAcceso
					Where Tipo <> '' and IDClub = '9' and Salida = 'S' and FechaSalida >= '2021-01-13 00:00:00' and FechaSalida <= '2021-01-13 23:59:59' Group by IDInvitacion ";

$result_ocupacion_salida = $dbo->query($sql_ocupacion_salida);
while( $r_ocupacion_salida = $dbo->fetchArray( $result_ocupacion_salida ) ):
		$tipo_salida=$r_ocupacion_salida["Tipo"];
		if($tipo_salida=="Contratista"):
			$IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$r_ocupacion_salida["IDInvitacion"]."'" );
			$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDTipoInvitado" , "IDInvitado = '".$IDInvitado."'" );
			$tipo_invitado=$dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$IDTipoInvitado."'" );
			if(!empty($tipo_invitado))
				$tipo_salida=$tipo_invitado;
		endif;
		$array_ocupacion_salida[ $tipo_salida ] ++;
		if($tipo_salida=="EMPLEADOS MDY"){
			echo "<br>" . $r_ocupacion_salida["IDLogAcceso"];
		}
endwhile;
exit;
*/






/*
RECUPERAR DATOS DE EVENTOS
//$sql_log="SELECT * FROM LogServicioDiario WHERE Servicio = 'setformularioevento'";
$sql_log="SELECT * FROM LogServicioQuincenal WHERE Servicio = 'setformularioevento'";
//$sql_log="SELECT * FROM LogServicioDiario WHERE LogServicioDiario = '4166401'";
$r_log=$dbo->query($sql_log);
while($row=$dbo->fetchArray($r_log)){
	$Parametros=$row["Parametros"];


	$Parametros=str_replace("null",'',$Parametros);
	$Parametros= trim(preg_replace('/\s+/', '', $Parametros));
	$Parametros= str_replace("\"[{","[{",$Parametros);
	$Parametros= str_replace("}]\"","}]",$Parametros);

	$Parametros= trim(preg_replace('/\s+/', ' ', $Parametros));
	$datos_log= json_decode($Parametros, true);

	$IDEvento=$datos_log["IDEvento"];

	$sql_er="SELECT * FROM EventoRegistro WHERE IDSocio = '".$row["IDSocio"]."' and IDEvento='".$IDEvento."' ";
	$r_er=$dbo->query($sql_er);
	while($row_er=$dbo->fetchArray($r_er)){
			echo "<br>" . $sql_erd="SELECT * FROM EventoRegistroDatos WHERE IDEventoRegistro = '".$row_er["IDEventoRegistro"]."'  ";
			$r_erd=$dbo->query($sql_erd);
			$row_erd=$dbo->fetchArray($r_erd);
			if((int)$row_erd["IDEventoRegistroDatos"]<=0){
				foreach($datos_log["ValoresFormulario"] as $id_valor => $valor){
					//echo $valor["IDCampoFormularioEvento"] . "=".$valor["Valor"];
					//print_r($valor);
					$sql_inserta="INSERT INTO EventoRegistroDatos (IDEventoRegistro, IDCampoFormularioEvento, Valor) Values ('". $row_er["IDEventoRegistro"] ."','". $valor["IDCampoFormularioEvento"]."','".$valor["Valor"]."')";
					$dbo->query($sql_inserta);
					echo "<br>Insertado";
				}
			}
	}


}

echo "<br>Fin";
exit;
*/




	/*
	//Reporte Log Separacion reserva
	//$Servicio='"IDServicio":"1043"';
	$Servicio='"IDServicio":"549"';
	$Fecha='"Fecha":"2020-10-03"';
	//$IDClub=23;
	$IDClub=20;
	$sql_socios="SELECT IDSocio,NumeroDocumento,Accion,Nombre,Apellido FROM Socio WHERE IDClub = '".$IDClub."'";
	$r_socios=$dbo->query($sql_socios);
	while($row_socios=$dbo->fetchArray($r_socios)){
		$array_socios[$row_socios["IDSocio"]]=$row_socios;
	}

	//$sql_separa_reserva="SELECT * FROM `LogServicioQuincenal` WHERE `Servicio` = 'setseparareserva' and (Parametros like '%".$Fecha."%'  and Parametros like '%".$Servicio."%' ) ORDER BY LogServicioDiario";
	//$sql_separa_reserva="SELECT * FROM `LogServicioDiario` WHERE `IDSocio` = '152549' ORDER BY LogServicioDiario";
	$sql_separa_reserva="SELECT * FROM `LogServicioQuincenal` WHERE (`Servicio` = 'set_reserva_generalV2' or `Servicio` = 'setseparareserva') and (Parametros like '%".$Fecha."%'  and Parametros like '%".$Servicio."%' ) ORDER BY LogServicioDiario";
	//$sql_separa_reserva="SELECT * FROM `LogServicioQuincenal` WHERE LogServicioDiario = 2303411";

	$r_separa_servicio=$dbo->query($sql_separa_reserva);
	?>
	<table border=1>
		<tr>
			<td>Movimiento</td>
			<td>Servicio</td>
			<td>Elemento</td>
			<td>Tee</td>
			<td>Socio</td>
			<td>Fecha del turno que intenta separar</td>
			<td>Hora del turno que intenta separar</td>
			<td>Dispositivo</td>
			<td>Respuesta</td>
			<td>Fecha Peticion</td>
		</tr>
					<?php
					while($row_separa_servicio=$dbo->fetchArray($r_separa_servicio)){
						$Parametros=$row_separa_servicio["Parametros"];
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"null"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"null"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67", "Nombre":"Necesita Caddie?", "Valor":"Si"},{"IDCampo":"218", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"2"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"Si"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"1"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67", "Nombre":"Necesita Caddie?", "Valor":"Si"},{"IDCampo":"218", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"1"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"Si"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"2"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67", "Nombre":"Necesita Caddie?", "Valor":"No"},{"IDCampo":"218", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"0"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"Optional("67")", "Nombre":"Necesita Caddie?", "Valor":"No"},{"IDCampo":"Optional("218")", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"0"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"No"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"0"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"Si"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"3"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"Si"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"0"}]",','',$Parametros);
						$Parametros=str_replace('"Campos":"[{"IDCampo":"67", "Nombre":"Necesita Caddie?", "Valor":"Si"},{"IDCampo":"218", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"3"}]",','',$Parametros);







						$Parametros=str_replace("null",'',$Parametros);
						$Parametros= trim(preg_replace('/\s+/', '', $Parametros));
						$Parametros= str_replace("\"[{","{",$Parametros);
						$Parametros= str_replace("}]\"","}",$Parametros);

							//$Parametros='{"action":"setseparareserva","IDSocio":"50771","IDServicio":"289","IDElemento":"349","Fecha":"2020-10-31","Hora":"08:10:00","Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"null"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"null"}]","Invitados":"[]","Tee":"Tee1","NumeroTurnos":"1","IDClub":"15","AppVersion":"31","Dispositivo":"Android","TipoApp":"Socio","TokenID":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJhdWQiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJpYXQiOjE2MDQwNDE1MDUsIm5iZiI6MTYwNDA0MTUwNSwiZXhwIjoxNjA0MTAxNTA1LCJkYXRhIjp7IklEVXN1YXJpb1dTIjoiMSIsIk5vbWJyZSI6IlJ1YmksIEpob24iLCJFbXByZXNhIjoiRGluZ28ifX0.fgRAK-u5ePu9rn_hL-GG7UI72LcIDhAx3Wl-9pEqaUs"}';

						$array_parametros=json_decode($Parametros,true);
						$Respuesta= trim(preg_replace('/\s+/', ' ', $Respuesta));
						$Respuesta=$row_separa_servicio["Respuesta"];
						$array_respuesta=json_decode($Respuesta,true);
						?>
						<tr>
								<td><?php echo $row_separa_servicio["Servicio"];?></td>
								<td><?php

								$NombreSer=$dbo->getFields( "Servicio", "Nombre", "IDServicio = '" . $row_separa_servicio["IDServicio"] . "'" );
								if(empty($NombreSer)){
									$IDMaestro = $dbo->getFields( "Servicio", "IDServicioMaestro", "IDServicio = '" . $row_separa_servicio["IDServicio"] . "'" );
									$NombreSer=$dbo->getFields( "ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDMaestro . "'" );
								}
								echo $NombreSer;
								?>

							</td>
								<td></td>
								<td><?php echo $array_parametros["Tee"]; ?></td>
								<td><?php
								echo $array_socios[$array_parametros["IDSocio"]]["Nombre"] . " " . $array_socios[$array_parametros["IDSocio"]]["Apellido"]; ?></td>
								<td><?php echo $array_parametros["Fecha"]; ?></td>
								<td><?php echo $array_parametros["Hora"]; ?></td>
								<td><?php echo $array_parametros["Dispositivo"]; ?></td>
								<td><?php echo $array_respuesta["message"]; ?></td>
								<td><?php echo $row_separa_servicio["FechaPeticion"]; ?></td>
						</tr>
					<?php }	?>

	</table>
	<?php
	exit;
	//Fin reporte
	exit;
	*/







	/*
	//Reporte Log Separacion reserva
	//$Servicio='"IDServicio":"1043"';
	$Servicio='"IDServicio":"289"';
	$Fecha='"Fecha":"2020-10-02"';
	//$IDClub=23;
	$IDClub=15;
	$sql_socios="SELECT IDSocio,NumeroDocumento,Accion,Nombre,Apellido FROM Socio WHERE IDClub = '".$IDClub."'";
	$r_socios=$dbo->query($sql_socios);
	while($row_socios=$dbo->fetchArray($r_socios)){
		$array_socios[$row_socios["IDSocio"]]=$row_socios;
	}

	//$sql_separa_reserva="SELECT * FROM `LogServicioQuincenal` WHERE `Servicio` = 'setseparareserva' and (Parametros like '%".$Fecha."%'  and Parametros like '%".$Servicio."%' ) ORDER BY LogServicioDiario";
	//$sql_separa_reserva="SELECT * FROM `LogServicioDiario` WHERE `IDSocio` = '152549' ORDER BY LogServicioDiario";
	$sql_separa_reserva="SELECT * FROM `LogServicioReporte` WHERE  (Parametros like '%".$Fecha."%'  and Parametros like '%".$Servicio."%' ) ORDER BY LogServicioDiario";
	//$sql_separa_reserva="SELECT * FROM `LogServicioReporte` WHERE  LogServicioDiario = 2303365 ORDER BY LogServicioDiario";
	//$sql_separa_reserva="SELECT * FROM `LogServicio` WHERE `Servicio` = 'set_reserva_generalV2' and (Parametros like '%".$Fecha."%'  and Parametros like '%".$Servicio."%' ) ORDER BY IDLogServicio";

	$r_separa_servicio=$dbo->query($sql_separa_reserva);
	?>
	<table>
		<tr>
			<td>Movimiento</td>
			<td>Servicio</td>
			<td>Elemento</td>
			<td>Socio</td>
			<td>Fecha del turno que intenta separar</td>
			<td>Hora del turno que intenta separar</td>
			<td>Dispositivo</td>
			<td>Respuesta</td>
			<td>Fecha Peticion</td>
		</tr>
					<?php
					while($row_separa_servicio=$dbo->fetchArray($r_separa_servicio)){
						$Parametros=$row_separa_servicio["Parametros"];
						$Parametros= trim(preg_replace('/\s+/', '', $Parametros));
						$Parametros= str_replace("\"[{","{",$Parametros);
						$Parametros= str_replace("}]\"","}",$Parametros);
						$array_parametros=json_decode($Parametros,true);
						$Respuesta= trim(preg_replace('/\s+/', ' ', $Respuesta));
						$Respuesta=$row_separa_servicio["Respuesta"];
						$array_respuesta=json_decode($Respuesta,true);
						?>
						<tr>
								<td><?php echo $row_separa_servicio["Servicio"];?></td>
								<td><?php

								$NombreSer=$dbo->getFields( "Servicio", "Nombre", "IDServicio = '" . $row_separa_servicio["IDServicio"] . "'" );
								if(empty($NombreSer)){
									$IDMaestro = $dbo->getFields( "Servicio", "IDServicioMaestro", "IDServicio = '" . $row_separa_servicio["IDServicio"] . "'" );
									$NombreSer=$dbo->getFields( "ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDMaestro . "'" );
								}
								echo $NombreSer;
								?>

							</td>
								<td></td>
								<td><?php
								if( !empty($row_separa_servicio["IDSocio"]))
									echo $array_socios[$row_separa_servicio["IDSocio"]]["Nombre"] . " " . $array_socios[$array_parametros["IDSocio"]]["Apellido"];
								else
									echo $array_socios[$array_parametros["IDSocio"]]["Nombre"] . " " . $array_socios[$array_parametros["IDSocio"]]["Apellido"];

									?>

								</td>
								<td><?php echo $array_parametros["Fecha"]; ?></td>
								<td><?php echo $array_parametros["Hora"]; ?></td>
								<td><?php echo $array_parametros["Dispositivo"]; ?></td>
								<td><?php echo $array_respuesta["message"]; ?></td>
								<td><?php echo $row_separa_servicio["FechaPeticion"]; ?></td>
						</tr>
					<?php }	?>

	</table>
	<?php
	exit;
	//Fin reporte
*/




	/*
	//Reporte Log Separacion reserva
	//$Servicio='"IDServicio":"1043"';
	$Servicio='"IDServicio":"3888"';
	$Fecha='"Fecha":"2020-10-10"';
	//$IDClub=23;
	$IDClub=44;
	$sql_socios="SELECT IDSocio,NumeroDocumento,Accion,Nombre,Apellido FROM Socio WHERE IDClub = '".$IDClub."'";
	$r_socios=$dbo->query($sql_socios);
	while($row_socios=$dbo->fetchArray($r_socios)){
		$array_socios[$row_socios["IDSocio"]]=$row_socios;
	}

	$sql_separa_reserva="SELECT * FROM `LogServicioQuincenal` WHERE `Servicio` = 'setseparareserva' and (Parametros like '%".$Fecha."%'  and Parametros like '%".$Servicio."%' ) ORDER BY LogServicioDiario";
	//$sql_separa_reserva="SELECT * FROM `LogServicioDiario` WHERE `IDSocio` = '152549' ORDER BY LogServicioDiario";
	//$sql_separa_reserva="SELECT * FROM `LogServicio` WHERE `Servicio` = 'set_reserva_generalV2' and (Parametros like '%".$Fecha."%'  and Parametros like '%".$Servicio."%' ) ORDER BY IDLogServicio";

	$r_separa_servicio=$dbo->query($sql_separa_reserva);
	?>
	<table>
		<tr>
			<td>Movimiento</td>
			<td>Servicio</td>
			<td>Elemento</td>
			<td>Socio</td>
			<td>Fecha del turno que intenta separar</td>
			<td>Hora del turno que intenta separar</td>
			<td>Dispositivo</td>
			<td>Respuesta</td>
			<td>Fecha Peticion</td>
		</tr>
					<?php
					while($row_separa_servicio=$dbo->fetchArray($r_separa_servicio)){
						$Parametros=$row_separa_servicio["Parametros"];
						$Parametros= trim(preg_replace('/\s+/', '', $Parametros));
						$Parametros= str_replace("\"[{","{",$Parametros);
						$Parametros= str_replace("}]\"","}",$Parametros);
						$array_parametros=json_decode($Parametros,true);
						$Respuesta= trim(preg_replace('/\s+/', ' ', $Respuesta));
						$Respuesta=$row_separa_servicio["Respuesta"];
						$array_respuesta=json_decode($Respuesta,true);
						?>
						<tr>
								<td><?php echo $row_separa_servicio["Servicio"];?></td>
								<td><?php

								$NombreSer=$dbo->getFields( "Servicio", "Nombre", "IDServicio = '" . $row_separa_servicio["IDServicio"] . "'" );
								if(empty($NombreSer)){
									$IDMaestro = $dbo->getFields( "Servicio", "IDServicioMaestro", "IDServicio = '" . $row_separa_servicio["IDServicio"] . "'" );
									$NombreSer=$dbo->getFields( "ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDMaestro . "'" );
								}
								echo $NombreSer;
								?>

							</td>
								<td></td>
								<td><?php
								echo $array_socios[$array_parametros["IDSocio"]]["Nombre"] . " " . $array_socios[$array_parametros["IDSocio"]]["Apellido"]; ?></td>
								<td><?php echo $array_parametros["Fecha"]; ?></td>
								<td><?php echo $array_parametros["Hora"]; ?></td>
								<td><?php echo $array_parametros["Dispositivo"]; ?></td>
								<td><?php echo $array_respuesta["message"]; ?></td>
								<td><?php echo $row_separa_servicio["FechaPeticion"]; ?></td>
						</tr>
					<?php }	?>

	</table>
	<?php
	exit;
	//Fin reporte
*/





	/*
	$server = '201.236.240.245';
	try {
			$hostname = $server;
			$port = "";
			$dbname = "IntegracionAppMiClub";
			$username = "appmiclub";
			$pw = "123/appmiclub.*";
			$dbh = new PDO ("dblib:host=$hostname;dbname=$dbname","$username","$pw");
		} catch (PDOException $e) {
			//echo "Failed to get DB handle: " . $e->getMessage() . "\n";
			echo $respuesta["message"] = "Lo sentimos no hay conexion a la base";
			exit;
		}




		$FechaActual=date("Y-m-d H:i:s");
		$sql_actualiza_fac="UPDATE Consumos
											 SET Factura='10',Prefijo='APP',FormaPago='tarjeta',FormaPagoId='1',Propina='1000'
											 WHERE ConsumoId = '1273834' ";
		//$sql_consumo=$dbh->query($sql_actualiza_fac);



	$Documento="";
	//$sql = $dbh->query("SELECT  * FROM SYSOBJECTS");
	//$sql=$dbh->query("SELECT TOP 1 MAX(Factura) as NumeroMayor FROM Consumos WHERE ConsumoId > 0 ");
	//$sql = $dbh->query("SELECT * FROM [INFORMATION_SCHEMA].[TABLES]");
	//$sql = $dbh->query("SELECT * FROM [ConsumosFacturados]");
	//$sql = $dbh->query("SELECT  * FROM [IntegracionAppMiClub] ");
	$sql = $dbh->query("SELECT  * FROM [Consumos] ");
	//$sql = $dbh->query("SELECT  * FROM [Producto] ");
	//$sql=$dbh->query("SELECT C.*,P.Nombre FROM Consumos C,Producto P WHERE C.ProductoId=P.ProductoId and C.TerceroId = '42128795' ");
	while ($row =$sql->fetch()){
		print_r($row);
		$Numerofac=(int)$row["NumeroMayor"]+1;
	}
	//echo $Numerofac;
	//exit;

	$sql_consumo = $dbh->query("SELECT C.*,P.Nombre FROM Consumos C,Producto P WHERE C.ProductoId=P.ProductoId" );
	//$sql_consumo = $dbh->query("SELECT C.* FROM Consumos C,   ");

	while ($row_consumo =$sql_consumo->fetch()){
		$array_consumo[$row_consumo["ConsumoId"]][]=$row_consumo;
	}



	unset($array_consumo);
	$IDSocio=52995;
	$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDSocio . "' ", "array" );
	$sql_consumo = $dbh->query("SELECT C.*,P.Nombre FROM Consumos C,Producto P WHERE C.ProductoId=P.ProductoId and TerceroId = '".$datos_socio["NumeroDocumento"]."' ");
	echo "SELECT C.*,P.Nombre FROM Consumos C,Producto P WHERE C.ProductoId=P.ProductoId and TerceroId = '".$datos_socio["NumeroDocumento"]."' ";
	$valor_mayor=0;
	while ($row_consumo =$sql_consumo->fetch()){
		//$array_consumo[$row_consumo["ConsumoId"]][]=$row_consumo;
		$array_consumo[$row_consumo["AccionId"]][]=$row_consumo;
	}
	//Resumen
	foreach($array_consumo as $id_consumo => $datos_consumo ){
		$detalle_producto="";
		foreach($datos_consumo as $id_detalle => $datos_detalle ){
			$NumeroConsumo=$datos_detalle["ConsumoId"];
			$FechaConsumo=$datos_detalle["FechaRegis"];
			$Accion=$datos_detalle["AccionId"];
			$Documento=$datos_detalle["TerceroId"];
			echo "<br>".$Subtotal=$datos_detalle["Subtotal"];
			echo "<br>" . $Subtotal.">=".$valor_mayor;
			if((int)$Subtotal>=$valor_mayor){
				$ValorTotal=$Subtotal;
				$valor_mayor=$Subtotal;
			}
			//$ValorTotal+=$Subtotal;
		}
	}

	echo "El mayor es: ". $ValorTotal;




	//Resumen
	foreach($array_consumo as $id_consumo => $datos_consumo ){
		$detalle_producto="";
		foreach($datos_consumo as $id_detalle => $datos_detalle ){
			$NumeroConsumo=$datos_detalle["ConsumoId"];
			$FechaConsumo=$datos_detalle["FechaRegis"];
			$Accion=$datos_detalle["AccionId"];
			$Documento=$datos_detalle["TerceroId"];
			$Subtotal=$datos_detalle["Subtotal"];
			$ValorTotal+=$Subtotal;
		}
		$resumen="<tr>
									<td>Numero:</td><td>".$NumeroConsumo."</td>
									<td>Fecha:</td><td>".$FechaConsumo."</td>
									<td>Accion:</td><td>".$Accion."</td>
									<td>Valor:</td><td>".$ValorTotal."</td>
									</tr>";

		?>
		<br>
		<table border="1">
			<?php echo $resumen?>
		</table>
	<?php
	}



	foreach($array_consumo as $id_consumo => $datos_consumo ){
		$detalle_producto="";
		foreach($datos_consumo as $id_detalle => $datos_detalle ){
			$NumeroConsumo=$datos_detalle["ConsumoId"];
			$FechaConsumo=$datos_detalle["FechaRegis"];
			$Accion=$datos_detalle["AccionId"];
			$Documento=$datos_detalle["TerceroId"];
			$Producto=$datos_detalle["Nombre"];
			$Cantidad=$datos_detalle["Cantidad"];
			$Valor=$datos_detalle["Valor"];
			$Descuento=$datos_detalle["Descuento"];
			$Iva=$datos_detalle["Iva"];
			$Subtotal=$datos_detalle["Subtotal"];
			$ValorTota+=$Subtotal;
			$detalle_producto.="<tr>
									<td>".$Producto."</td>
									<td>".$Cantidad."</td>
									<td>".$Valor."</td>
									<td>".$Descuento."</td>
									<td>".$Iva."</td>
									<td>".$Subtotal."</td>
								</tr>";
		}
		$encabezado="<tr>
									<td>Numero:</td><td>".$NumeroConsumo."</td>
									<td>Fecha:</td><td>".$FechaConsumo."</td>
									<td>Accion:</td><td>".$Accion."</td>
									</tr>";

		?>
		<br>
		<table border="1">
			<?php echo $encabezado?>
			<tr>
				<td colspan="6">
				<table border="1" width="100%">
					<tr>
						<td>Producto</td>
						<td>Cantidad</td>
						<td>Valor</td>
						<td>Descuento</td>
						<td>Iva</td>
						<td>Subtotal</td>
					</tr>
					<?php echo $detalle_producto; ?>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>Total</td>
						<td><?php echo $ValorTota; ?></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
	<?php
	}

		$sql = $dbh->query("SELECT  * FROM [Consumos] ");
	?>
	<table border="1">
			<tr>
				<td>Id</td>
				<td>ConsumoId</td>
				<td>Fecha</td>
				<td>Accion</td>
				<td>Documento</td>
				<td>Producto</td>
				<td>Cantidad</td>
				<td>Valor</td>
				<td>Descuento</td>
				<td>Iva ID</td>
				<td>Iva</td>
				<td>Subtotal</td>
				<td>Total</td>
				<td>Factura</td>
				<td>Prefijo</td>
				<td>FormaPago</td>
				<td>FormaPagoId</td>
				<td>Propina</td>

			</tr>
			<?php
			while ($row =$sql->fetch()){ ?>
				<tr>
					<td><?php echo $row["Id"]; ?></td>
					<td><?php echo $row["ConsumoId"]; ?></td>
					<td><?php echo $row["FechaRegis"]; ?></td>
					<td><?php echo $row["AccionId"]; ?></td>
					<td><?php echo $row["TerceroId"]; ?></td>
					<td><?php echo $row["ProductoId"]; ?></td>
					<td><?php echo $row["Cantidad"]; ?></td>
					<td><?php echo $row["Valor"]; ?></td>
					<td><?php echo $row["Descuento"]; ?></td>
					<td><?php echo $row["IvaId"]; ?></td>
					<td><?php echo $row["Iva"]; ?></td>
					<td><?php echo $row["Subtotal"]; ?></td>
					<td><?php echo $row["Total"]; ?></td>
					<td><?php echo $row["Factura"]; ?></td>
					<td><?php echo $row["Prefijo"]; ?></td>
					<td><?php echo $row["FormaPago"]; ?></td>
					<td><?php echo $row["FormaPagoId"]; ?></td>
					<td><?php echo $row["Propina"]; ?></td>
				</tr>
			<?php }	?>
	</table>

	<?php
	exit;

*/




	/*
	//Socios COUNTRY barranquilla
	require_once(LIBDIR.'nusoap/lib/nusoap.php');
	$params = array(
		"club" => "country"
	);
	$client = new SoapClient("http://190.242.128.108/webserviceappcountry/Service1.asmx?WSDL");
	$response=$client->__soapCall("GetEstadoSocios", array($params));
	$xml = $response->GetEstadoSociosResult;
	//print_r($xml);
	//print_r($response);

	$parser = xml_parser_create();
						xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
						xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
						xml_parse_into_struct($parser, $xml, $valores, $indices);
						xml_parser_free($parser);

//print_r($valores);
foreach ($valores as $idx => $posicion) {
		echo "<br>";
		print_r($posicion);
		echo "<br>";
}
exit;
//FIN Socios COUNTRY
*/




	/*
	//Encriptar y desencriptar
	$param['key']=KEY_API;
	$param['nonce']=sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES));
	$param['msg']=1;
	$result=SIMUtil::cryptSodium($param);
	$texto_encrip=$param['nonce'].sodium_bin2hex($result["cryptedText"]);

	//print_r($texto_encrip);
	//exit;

	$Data=$texto_encrip;
	$Data="4a96745287115cfb1263a8824bbdcf9edc85cad2224a0fdd49196f362aefc5b6596b5080d7f6a680008fa44a6937dd8ffe54ab9e852c673bd7ebdac18203c1e3645426b8266e110818970f0597e4a4a5aa42cf83a597cab886bee80ead7f33e758815ee3a21ec926c3b3776390ff5975d001422168bcfe6b9bdca84b68d2162304f6cb03aa4f18a5f967deecc67c7fe6f5d37c4effc99f55c6078cc14e2b264ba90bfa31d13b7f1cdb612929fe35a0af6642103801ae7cd131535b48ca161d81c5af0b0da9ac447bbfbb1093e46b69884e99dcbbc661da8b3ff493a96aafc9514267ae23ada3dec0edfc4b62c829c1e7147bb5d9c2a2b1583dbd434121cf8a40c8ac5af2f6a79d6b63988dd74259b824e13a06548670499c3a42f576601b8ee88c6d2ca849f848325c0f9627c3ad805d56d0fe6f29db99fa665050ac1d113d5d3b9e98f04a8774605379fd074fba098dc85cc19594ada1e8eea8b6a96e41d1b9fa8a55cf7e4108c4fa05f87e42fe03fae9db103d06b0bbdfe41b2719b2dd06a0981c84ed093380c0acd820cd12b8b26712355802217b58e59d92638325b75686961901e695ef25d80cb67475deb824834f10c6fa7982a4d16e511c31cadf2a3da75090171d1dfc8cc1abd6633c7721c67ea6749f1c282e9223b16fdb8f07c81bc0f39a6dd3858b4bcd4a060c438148b9dda41a42fe02998ae1cad480ac7a67c3d4a4e9e29f92ebb00375fd6fc9d1d8c8514bbb84ff9727175a15bd051df277c59d965d12fb2504ebadcfb0423cea2b3b95a83d279b8f22f8f677940a132cbd78607d762336df872c024c1b4bbf06eccf594db25b87730cc01b9be1c38c14bea99d343235fa9e5ff55b3a2de100f1b8beb6dfe88be2623cded301c3bce3d6e4f48fe8508d27c49723121c78772e30b84f897ce9e399032100914c9d9a7727fe0cc5ea8faeaa8315af0dd79c74ef04dd23481592bf606b4e2e99dc928b4f293c93567fdbf941861dbd5ea1ce1919f217c604f9aff9e638db13c61f48473f0a8e5b93aaa96c5bbd164fc3d63a100f25fbf752b004393ceede88baa90ee5aa90fe62bde6bdebe11853dd142dec62271ffaa71b060bff8ec59f6fffb576c5a73cb987deb4c08f6e14ab6914847297467a1920d672cfdb5b3e7119f8890de2d5b8d19f7ba4826bb7b0bcf715fec67bcb528578f14ba38c350d3f7cd895fcf210e4bc7fff763ce65d5b562606c8a07060fb0a6865f9cb0267a709d55cef958860c2c8bd7addb928f84e63e0672b5df845c7a34d5617605269c7ee3e98807a131627e7c99ebce157c7e0986214404121f44e48cf0e42001ebb41ff8771b903d605484f4873cf857736281cee581fdee4844c90";
	$valornonce=substr($Data,0,48);
	$valorencrip=substr($Data,48);
	$param['key']=KEY_API;
	$param['chiper']=$valorencrip;
	$param['nonce']=$valornonce;
	$result_decrypt=SIMUtil::decryptSodium($param);
		if($result_decrypt["decryptedText"]=="nodecrypt"){
			$respuesta[ "message" ] = "ENCRIPT. No";
			$respuesta[ "success" ] = false;
			$respuesta[ "response" ] = NULL;
			return $respuesta;
		}
		else{
			$result_decrypt["decryptedText"];
			$array_datos=json_decode($result_decrypt["decryptedText"]);
			print_r($array_datos);
			$IDSocio=$array_datos->ax;
		}
		//FIN Encriptar y desencriptar

	exit;
*/


	/*
	//Actulizar acciones san andres
	$IDClub=70;
	$array_accion_cambio=array("20","40","60");
	//$sql_soc = "SELECT * FROM Socio WHERE IDClub = '".$IDClub."' and Accion='0302' or AccionPadre='0302' ";
	$sql_soc = "SELECT * FROM Socio WHERE IDClub = '".$IDClub."'";
	$result_soc = $dbo->query($sql_soc);
	while($row_soc = $dbo->fetchArray($result_soc)){
		$actualizar="N";
		$array_accion=explode("-",$row_soc["Accion"]);
		$secuencia=$array_accion[1];

		if($secuencia>=20 && $secuencia<=29){
				$actualizar="S";
				if($secuencia==20){ //Es el titular
					$NuevaAccion=$array_accion[0]."-".$secuencia;
				$AccionPadre=$NuevaAccion;
				}
				else{
					$NuevaAccion=$row_soc["Accion"];
					$AccionPadre=$array_accion[0]."-20";
				}
		}
		elseif($secuencia>=40 && $secuencia<=49){
			$actualizar="S";
			if($secuencia==40){ //Es el titular
				$NuevaAccion=$array_accion[0]."-".$secuencia;
				$AccionPadre=$NuevaAccion;
			}
			else{
				$NuevaAccion=$row_soc["Accion"];
				$AccionPadre=$array_accion[0]."-40";
			}
		}
		elseif($secuencia>=60 && $secuencia<=69){
			$actualizar="S";
			if($secuencia==60){ //Es el titular
				$NuevaAccion=$array_accion[0]."-".$secuencia;
				$AccionPadre=$NuevaAccion;
			}
			else{
				$NuevaAccion=$row_soc["Accion"];
				$AccionPadre=$array_accion[0]."-60";
			}
		}
		if($actualizar=="S"){
			echo "<br>" . $update_soc="UPDATE Socio set Accion= '".$NuevaAccion."', AccionPadre = '".$AccionPadre."' Where IDSocio = '".$row_soc["IDSocio"]."'";
			//$dbo->query($update_soc);
		}

	}

echo "<br>Fin Actualizar san andres";
exit;



	$id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada( 151, "2020-07-10", "", "" );
	print_r($id_disponibilidad);

	exit;

	function get_agenda($IDClub,$IDUsuario,$Fecha){
		$dbo =& SIMDB::get();
		if(empty($Fecha)):
			$Fecha=date("Y-m-d");
		endif;

		if( !empty( $IDUsuario ) ){
			//Consulto el servicio que tiene permiso y el elemnto para consultar la agenda
			$sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $IDUsuario . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
			$qry_servicios = $dbo->query( $sql_servicios );
			$response_agenda = array();
			$response = array();
			$agenda_dia = false;
			while( $r_servicio = $dbo->fetchArray( $qry_servicios  ) )
			{
					//Consulto solo los elementos al los que tiene permiso de ver
					//$sql_elementos = "Select * From UsuarioServicioElemento Where IDUsuario = '".$IDUsuario."'";
					$sql_elementos = "Select *
										From UsuarioServicioElemento USES, ServicioElemento SE
										Where SE.IDServicioElemento = USES.IDServicioElemento
										and IDServicio = '".$r_servicio["IDServicio"]."'
										and IDUsuario = '".$IDUsuario."'";


					$qry_elementos = $dbo->query($sql_elementos);
					while($row_elemento = $dbo->fetchArray($qry_elementos)):
						//Si el elemnto pertenece al servicio lo consulto
						$horas = SIMWebService::get_disponiblidad_elemento_servicio( $IDClub, $r_servicio["IDServicio"], $Fecha, $row_elemento["IDServicioElemento"],"Agenda","","","","S","",$IDUsuario);

						if($horas["response"][0]):
							if(count($horas["response"][0]["Disponibilidad"][0])>0):
								$agenda_dia = true;
								array_push($response, $horas["response"][0]);
							endif;
						endif;
					endwhile;

			}//end while

			//Para los auxiliares monitores muestro los elemtos donde esten reservados
			$sql_aux="SELECT A.IDAuxiliar, IDServicio FROM UsuarioAuxiliar UA, Auxiliar A WHERE UA.IDAuxiliar=A.IDAuxiliar and UA.IDUsuario='".$IDUsuario."' ";
			$result_aux=$dbo->query($sql_aux);
			while($row_aux=$dbo->fetchArray($result_aux)){
				// Consulto las reserva en esta fecha de este usuario
				$sql_reserva="SELECT IDServicioElemento From ReservaGeneral Where IDClub = '".$IDClub."' and Fecha='".$Fecha."' and IDAuxiliar like '".$row_aux["IDAuxiliar"].",%' ";
				$r_reserva=$dbo->query($sql_reserva);
				while($row_reserva=$dbo->fetchArray($r_reserva)){
						$array_elemento[$row_reserva["IDServicioElemento"]]=$row_reserva["IDServicioElemento"];
				}
				if(count($array_elemento>0)){
					foreach($array_elemento as $id_elemento_aux){
						unset($array_disponibilidad);
						$horas = SIMWebService::get_disponiblidad_elemento_servicio( $IDClub, $row_aux["IDServicio"], $Fecha, $id_elemento_aux,"Agenda","","","","S");
						print_r($horas);
						if($horas["response"][0]):
							if(count($horas["response"][0]["Disponibilidad"][0])>0):
								$agenda_dia = true;
								// Solo muestro donde este reservado el auxiliar
								foreach($horas["response"][0]["Disponibilidad"][0] as $datos_disponibilidad){
											$array_id_aux=explode(",",$datos_disponibilidad["IDAuxiliar"]);
											if(in_array($row_aux["IDAuxiliar"],$array_id_aux)){
													$array_disponibilidad[]=$datos_disponibilidad;
													//print_r($datos_disponibilidad["IDAuxiliar"]);
													//echo "<br>";
											}
								}
								if(count($array_disponibilidad)<=0){
									$array_disponibilidad=array();
								}
								$horas["response"][0]["Disponibilidad"][0]=$array_disponibilidad;
								array_push($response, $horas["response"][0]);
							endif;
						endif;
					}
				}
			}


			if($agenda_dia):
				//$response["Agenda"] = $response_agenda;
				$respuesta["message"] = "ok";
				$respuesta["success"] = true;
				$respuesta["response"] = $response;
			else:
				//$response["Agenda"] = $response_agenda;
				$respuesta["message"] = "No tiene reservas para hoy.";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			endif;




		}
		else{
				$respuesta["message"] = "28. Atencion faltan parametros en agenda";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
		return $respuesta;
	}

	$resp=get_agenda("20","9396","2020-07-09");
	print_r($resp);
	exit;


$IDClub=8;
$NumeroDocumento="80160";
$Fecha="2020-07-08";
$resp=SIMWebServiceApp::get_diagnostico_persona($IDClub,$NumeroDocumento,$Fecha);
print_r($resp);
exit;



$ch = curl_init("https://www.miclubapp.com/services/club.php");
//$ch = curl_init("https://colegiosdev.miclubapp.com/services/club.php");


// Envía solicitud
curl_exec($ch);

// Valida si se ha producido errores y muestra el mensaje de error
if($errno = curl_errno($ch)) {
    $error_message = curl_strerror($errno);
    echo "cURL error conectar:  ({$errno}):\n {$error_message}";
}
else{
	echo "ok conectar";
}

// Cierra el gestor
curl_close($ch);

exit;


	error_reporting();

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://colegiosdev.miclubapp.com/services/club.php",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => array('IDClub' => '18','key' => 'CEr0CLUB','action' => 'setpropietario','Nombre' => 'Jorge','Apellido' => 'Chirivi','NumeroDocumento' => '80160','CorreoElectronico' => 'jorgechirivi@gmail.com','Telefono' => '3118868003','Celular' => '3118868003','Portal' => 'PRUEBA','Casa' => 'PRUEBA','Llave' => 'asd123','AccionRegistro' => 'update'),
	  CURLOPT_HTTPHEADER => array(
	    "Cookie: PHPSESSID=104d691e27ee1cdda92549db1c967465"
	  ),
	));

	echo "a2";
	$respu=curl_error($curl);
	print_r($respu);
	$response = curl_exec($curl);

	print_r($response);

	curl_close($curl);
	echo $response;

exit;


$email="39776614";
$clave="pablo2";

//$email="65733327";
//$clave="e49b3";


$llave_fija = "63380fcfe2bf3c3d2cb3ec089c3c521b";

echo  md5( $email . md5( $llave_fija . "-" . $clave ));
exit;


$clave_especial = md5( $email . md5( $llave_fija . "-" . $clave ) );
//$clave_especial =  md5($email."_".md5($clave));
echo $otra_condicion_clave = " or Clave = '" . $clave_especial . "'";
exit;



	function get_disponiblidad_elemento_servicioTEST( $IDClub, $IDServicio, $Fecha, $IDElemento = "", $Admin = "", $UnElemento = "", $NumeroTurnos = "", $IDTipoReserva = "", $Agenda = "", $MostrarTodoDia = "", $IDUsuario="" ) {
		$dbo = & SIMDB::get();

		$datos_servicio_actual=$dbo->fetchAll( "Servicio", " IDServicio = '" . $IDServicio . "' ", "array" );

		if(!empty($IDUsuario)){
			$PermiteReservarUsuario=$dbo->getFields( "Usuario", "PermiteReservar", "IDUsuario = '" . $IDUsuario . "'" );
		}

		$CuposporHora = $datos_servicio_actual["CupoMaximoBloque"];


		//Consulto el servicio maestro si es golf lo envio al metodo de horas de campos de golf que es especial
		$id_servicio_maestro = $datos_servicio_actual["IDServicioMaestro"];
		$datos_servicio_mestro=$dbo->fetchAll( "ServicioMaestro", " IDServicioMaestro = '" . $id_servicio_maestro . "' ", "array" );
		$id_servicio_cancha = $datos_servicio_mestro["IDServicioMaestroReservar"];

		if ( $id_servicio_maestro == 15 || $id_servicio_maestro == 27 || $id_servicio_maestro == 28 || $id_servicio_maestro == 30 ): //Golf
			//$respuesta = SIMWebService::get_disponibilidad_campo($IDClub,$IDCampo,$Fecha, $IDServicio);
			$respuesta = SIMWebService::get_disponibilidad_campo( $IDClub, "", $Fecha, $IDServicio, $Admin, $NumeroTurnos );
		return $respuesta;
		endif;



		$fecha_disponible = 0;

		$verifica_disponibilidad_especifica = 0;
		$verifica_disponibilidad_general = 0;
		$verificacion = "";
		// Verifico que el club y servicio este disponible en la fecha consultada
		$verificacion = SIMWebService::verifica_club_servicio_abierto( $IDClub, $Fecha, $IDServicio );

		if ( !empty( $verificacion ) ):
			$respuesta[ "message" ] = $verificacion;
		$respuesta[ "success" ] = false;
		$respuesta[ "response" ] = NULL;
		return $respuesta;
		endif;


		/*
		//if($IDClub==10):
			//valido si el servicio tiene opciones para seleccionar la opcion por ejemplo dobles o sencillos
			$sql_tipo_reserva_servicio = "Select * From ServicioTipoReserva Where IDServicio = '".$IDServicio."' and Activo = 'S'";
			$result_tipo_reserva_servicio = $dbo->query($sql_tipo_reserva_servicio);
			$total_tipo_reserva = $dbo->rows($result_tipo_reserva_servicio);
			if((int)$total_tipo_reserva>0 && empty($IDTipoReserva)):
				$respuesta["message"] = "Debe seleccionar una opcion en el paso anterior, por favor verifique";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				return $respuesta;
			endif;

		//endif;
		*/







/*
if(function_exists("mcrypt"))
	echo "S";
else
	echo "N";

exit;

	$server = '201.236.240.245';

	try {
			$hostname = $server;
			$port = "";
			$dbname = "IntegracionAppMiClub";
			$username = "appmiclub";
			$pw = "123/appmiclub.*";
			$dbh = new PDO ("dblib:host=$hostname;dbname=$dbname","$username","$pw");
		} catch (PDOException $e) {
			//echo "Failed to get DB handle: " . $e->getMessage() . "\n";
			echo $respuesta["message"] = "Lo sentimos no hay conexion a la base";
			exit;
		}


	//$sql = $dbh->query("SELECT  * FROM SYSOBJECTS");
	$sql = $dbh->query("SELECT * FROM [INFORMATION_SCHEMA].[TABLES]");
	$sql = $dbh->query("SELECT * FROM [ConsumosFacturados]");
	//$sql = $dbh->query("SELECT  * FROM [IntegracionAppMiClub] ");
	//$sql = $dbh->query("SELECT  * FROM [Consumos] ");

	while ($row =$sql->fetch()){
		print_r($row);
	}

	exit;



	function validar_turnos_seguidos( $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDBeneficiario = "", $TipoBeneficiario = "", $PermiteReservaSeguidaNucleo ) {
		$dbo = & SIMDB::get();
		$flag_turno_seguido = 0;
		$array_confirmado = array();
		// Consulto los turnos reservados y confirmados del socio para no tomar los separados
		if ( !empty( $IDBeneficiario ) ):
			$condicion_beneficiario = " and  (IDSocioBeneficiario = '" . $IDBeneficiario . "' or IDInvitadoBeneficiario = '" . $IDBeneficiario . "' or IDInvitadoBeneficiario = '0')";
		else :
			$condicion_beneficiario = " and  IDSocioBeneficiario = '0' and IDInvitadoBeneficiario = '0'";
		endif;


		// Valido tambien para que los de la misma acción no puedan tomar turnos seguidos
		//Si en la configuracion esta marcada como "No" de lo contrario se permite turnos seguios asi sean de la misma accion
		if ( $PermiteReservaSeguidaNucleo == "N" ):
			$accion_padre = $dbo->getFields( "Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'" );
			$accion_socio = $dbo->getFields( "Socio", "Accion", "IDSocio = '" . $IDSocio . "'" );
			if ( empty( $accion_padre ) ): // Es titular
				$array_socio[] = $IDSocio;
				$sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
				$result_nucleo = $dbo->query( $sql_nucleo );
				while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
					$array_socio[] = $row_nucleo[ "IDSocio" ];
				endwhile;
			else :
				$sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "' and IDClub = '" . $IDClub . "' ";
				$result_nucleo = $dbo->query( $sql_nucleo );
				while ( $row_nucleo = $dbo->fetchArray( $result_nucleo ) ):
					$array_socio[] = $row_nucleo[ "IDSocio" ];
				endwhile;
		endif;
		if ( count( $array_socio ) > 0 ):
			$id_socio_nucleo = implode( ",", $array_socio );
		endif;
		else :
			$id_socio_nucleo = $IDSocio;
		endif;




		//$sql_confirmado="Select * From  ReservaGeneral Where IDSocio = '".$IDSocio."' and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva	= 1 " . $condicion_beneficiario;
		echo $sql_confirmado = "Select * From  ReservaGeneral Where IDSocio in (" . $id_socio_nucleo . ")  and IDServicio  = '" . $IDServicio . "' and Fecha = '" . $Fecha . "' and IDEstadoReserva	= 1 " . $condicion_beneficiario;
		$qry_confirmado = $dbo->query( $sql_confirmado );
		while ( $r_confirmado = $dbo->fetchArray( $qry_confirmado ) ):
			$array_confirmado[] = $r_confirmado[ "Hora" ];
		endwhile;


		$array_horarios = SIMWebService::get_disponiblidad_elemento_servicio( $IDClub, $IDServicio, $Fecha, "", "" );
		foreach ( $array_horarios[ "response" ][ 0 ][ "Disponibilidad" ][ 0 ] as $id_horario => $datos_horario ):
			if ( in_array( $IDSocio, $array_socio ) && in_array( $datos_horario[ "Hora" ], $array_confirmado ) ):
				$id_socio_turno = $IDSocio;
			elseif ( empty( $array_turnos_dia[ $datos_horario[ "Hora" ] ] ) ):
				$id_socio_turno = "";
		endif;
		if ( empty( $array_turnos_dia[ $datos_horario[ "Hora" ] ] ) ):
			$array_turnos_dia[ $datos_horario[ "Hora" ] ] = $id_socio_turno;
		endif;
		endforeach;


		for ( $i = 1; $i <= count( $array_turnos_dia ); $i++ ):
			current( $array_turnos_dia );
		//Primer Posicion
		if ( $i == 1 && key( $array_turnos_dia ) == $Hora && current( $array_turnos_dia ) == $IDSocio ): // Es el primer horario y lo valido
			$flag_turno_seguido = 1;
		endif;
		if ( key( $array_turnos_dia ) == $Hora ):
			// me devuelvo al turno anterior
			prev( $array_turnos_dia );
		if ( current( $array_turnos_dia ) == $IDSocio ):
			$flag_turno_seguido = 2;
		endif;
		//Adelanto dos turnos, si es el final solo uno
		next( $array_turnos_dia );
		if ( current( $array_turnos_dia ) == $IDSocio ):
			$flag_turno_seguido = 3;
		endif;
		if ( $i != count( $array_turnos_dia ) ):
			next( $array_turnos_dia );
		endif;
		if ( current( $array_turnos_dia ) == $IDSocio ):
			$flag_turno_seguido = 4;
		endif;
		endif;
		next( $array_turnos_dia );
		endfor;

		return $flag_turno_seguido;

	}

	$Fecha="2020-06-27";
	$Hora="10:00:00";
	$IDSocio=5533;
	$IDServicio=273;
	$IDClub=8;
	$IDBeneficiario = "5556";
	$TipoBeneficiario = "Socio";
	$PermiteReservaSeguidaNucleo="N";
	$resp=validar_turnos_seguidos( $Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDBeneficiario, $TipoBeneficiario, $PermiteReservaSeguidaNucleo );
	print_r($resp);
	exit;



	/*
	$FormaPago="Zona 1: Desde cerros orientales hasta Av. NQS; entre calle 26 y calle 85: Junio 10,";
	$pos = strpos($FormaPago, "Zona 1");

	if ($pos !== false) {
		echo "SI " . $HoraEntrega="2020-06-10";
		echo "La cadena '$findme' fue encontrada en la cadena '$mystring'";
	}
	else
		echo "NO fue";


exit;
*/






/*


	//$key previously generated safely, ie: openssl_random_pseudo_bytes
	$plaintext = "Hola mundo";
	$key="90325F38F77BA5B60C2AA637DB78281C";
	$iv = "0000000000000000";


		$secret = '90325F38F77BA5B60C2AA637DB78281C';
    echo "AAA " . base64_encode(openssl_encrypt("Hola mundo", 'aes-128-ecb', $secret, OPENSSL_RAW_DATA));
		exit;

	$plaintext = "Hola mundo";
$cipher = "aes-128-cbc";
if (in_array($cipher, openssl_get_cipher_methods()))
{
    echo $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv, $tag);
    //store $cipher, $iv, and $tag for decryption later
    $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv, $tag);
    //$original_plaintext."\n";
}


	exit;



	$msg = 'Hola mundo';

// Generating an encryption key and a nonce
$key   = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES); // 256 bit
$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES); // 24 bytes

// Encrypt
echo $ciphertext = sodium_crypto_secretbox($msg, $nonce, $key);
// Decrypt
$plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);

echo $plaintext === $msg ? 'Success' : 'Error';

	exit;



	echo "A" . phpversion();
	exit;

echo LIBDIR . '/pushok/vendor/autoload.php';
if (file_exists(LIBDIR . '/pushok/vendor/autoload.php'))
	echo "s";
else
	echo "n";


	require LIBDIR . '/pushok/vendor/autoload.php';

	use Pushok\AuthProvider;
	use Pushok\Client;
	use Pushok\Notification;
	use Pushok\Payload;
	use Pushok\Payload\Alert;

	$options = [
	    'key_id' => 'AAAABBBBCC', // The Key ID obtained from Apple developer account
	    'team_id' => 'DDDDEEEEFF', // The Team ID obtained from Apple developer account
	    'app_bundle_id' => 'com.app.Test', // The bundle ID for app obtained from Apple developer account
	    'private_key_path' => __DIR__ . '/private_key.p8', // Path to private key
	    'private_key_secret' => null // Private key secret
	];

	$authProvider = AuthProvider\Token::create($options);

	$alert = Alert::create()->setTitle('Hello!');
	$alert = $alert->setBody('First push notification');

	$payload = Payload::create()->setAlert($alert);

	//set notification sound to default
	$payload->setSound('default');

	//add custom value to your notification, needs to be customized
	$payload->setCustomValue('key', 'value');

	$deviceTokens = ['<device_token_1>', '<device_token_2>', '<device_token_3>'];

	$notifications = [];
	foreach ($deviceTokens as $deviceToken) {
	    $notifications[] = new Notification($payload,$deviceToken);
	}

	$client = new Client($authProvider, $production = false);
	$client->addNotifications($notifications);



	$responses = $client->push(); // returns an array of ApnsResponseInterface (one Response per Notification)

	foreach ($responses as $response) {
	    $response->getApnsId();
	    $response->getStatusCode();
	    $response->getReasonPhrase();
	    $response->getErrorReason();
	    $response->getErrorDescription();
	}


echo "ok";
exit;



	class MCrypt
        {
                private $iv = '0000000000000000'; #Same as in JAVA
                private $key = '90325F38F77BA5B60C2AA637DB78281C'; #Same as in JAVA


								function __construct()
                {
                }

                function encrypt($str) {

                  //$key = $this->hex2bin($key);
                  $iv = $this->iv;

                  $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

                  mcrypt_generic_init($td, $this->key, $iv);
                  $encrypted = mcrypt_generic($td, $str);

                  mcrypt_generic_deinit($td);
                  mcrypt_module_close($td);

                  return bin2hex($encrypted);
                }

                function decrypt($code) {
                  //$key = $this->hex2bin($key);
                  $code = $this->hex2bin($code);
                  $iv = $this->iv;

                  $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

                  mcrypt_generic_init($td, $this->key, $iv);
                  $decrypted = mdecrypt_generic($td, $code);

                  mcrypt_generic_deinit($td);
                  mcrypt_module_close($td);

                  return trim($decrypted);
                }

                protected function hex2bin($hexdata) {
                  $bindata = '';

                  for ($i = 0; $i < strlen($hexdata); $i += 2) {
                        $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
                  }

                  return $bindata;
                }

        }

	$ApiCrypter = new MCrypt();


	echo "<br>Texto: " . $str="mi tío hect3·$%&/78() es un muchachó bonito";
	echo "<br>VALOR ENCRIPTADO: " . $encrypted = $ApiCrypter->encrypt($str);
	echo "<br>VALOR ENCRIPTADO BASE 64: ".base64_encode($encrypted);

	echo "<br>DESENCRIPTADO " . $encrypted = $ApiCrypter->decrypt($encrypted);


exit;

*/

/*
	function safeEncrypt($message, $key)
	{
	    $nonce = random_bytes(
	        SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
	    );

	    $cipher = base64_encode(
	        $nonce.
	        sodium_crypto_secretbox(
	            $message,
	            $nonce,
	            $key
	        )
	    );
	    sodium_memzero($message);
	    sodium_memzero($key);
	    return $cipher;
	}
	*/




 /*

//Cambiar Tipo y clasificacion

$array_tipo_cambios = array(5=>31,3=>30,25=>30,16=>30,17=>30,18=>30,20=>32,24=>30,15=>30,19=>31,21=>31,10=>32,1=>32,11=>32,8=>32,2=>31,9=>32,4=>31,26=>31,34=>31,7=>31,27=>31);
$array_clasif_cambios = array(3=>487,25=>489,15=>496,16=>496,17=>496,18=>496,24=>497,20=>499,19=>486,21=>486,1=>379,11=>378,8=>421,2=>502,9=>499,4=>486,26=>486,34=>486,7=>468,0=>486,27=>486);
/*
$sql_tipo="SELECT * FROM TipoInvitado WHERE IDClub = 9 ";
$r_tipo=$dbo->query($sql_tipo);
while($row_tipo=$dbo->fetchArray($r_tipo)){
	echo "<br>" . $row_tipo["IDTipoInvitado"] . " : " . $row_tipo["Nombre"];
	if(array_key_exists($row_tipo["IDTipoInvitado"], $array_tipo_cambios))	{
		//Paso la clasificacion de esa a la que debe quedar
		echo "<br>" . $sql_clasif="UPDATE ClasificacionInvitado SET IDTipoInvitado = '".$array_tipo_cambios[$row_tipo["IDTipoInvitado"]]."' WHERE IDTipoInvitado = '".$row_tipo["IDTipoInvitado"]."' ";
		$dbo->query($sql_clasif);
		echo "<br>" . $sql_inv="UPDATE Invitado
														SET IDTipoInvitado = '".$array_tipo_cambios[$row_tipo["IDTipoInvitado"]]."',
																IDClasificacionInvitado = '".$array_clasif_cambios[$row_tipo["IDTipoInvitado"]]."'
														WHERE IDTipoInvitado = '".$row_tipo["IDTipoInvitado"]."' ";
		$dbo->query($sql_inv);
	}
}

exit;
*/

/*
//Otros
$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '487'
					WHERE IDTipoInvitado = '30' and IDClasificacionInvitado = 370 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '489'
					WHERE IDTipoInvitado = '30' and IDClasificacionInvitado = 369 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '496'
					WHERE IDTipoInvitado = '30' and IDClasificacionInvitado = 367 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '497'
					WHERE IDTipoInvitado = '30' and IDClasificacionInvitado = 371 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '487'
					WHERE IDTipoInvitado = '30' and IDClasificacionInvitado = 0 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '498'
					WHERE IDTipoInvitado = '30' and IDClasificacionInvitado = 372 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '498'
					WHERE IDTipoInvitado = '30' and IDClasificacionInvitado = 372 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '499'
					WHERE IDTipoInvitado = '32' and IDClasificacionInvitado = 0 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '501'
					WHERE IDTipoInvitado = '31' and IDClasificacionInvitado = 483 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '486'
					WHERE IDTipoInvitado = '31' and IDClasificacionInvitado = 419 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '468'
					WHERE IDTipoInvitado = '31' and IDClasificacionInvitado = 374 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '486'
					WHERE IDTipoInvitado = '31' and IDClasificacionInvitado = 0 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '499'
					WHERE IDTipoInvitado = '32' and IDClasificacionInvitado = 396 ";
$dbo->query($sql_inv);

$sql_inv="UPDATE Invitado
					SET IDClasificacionInvitado = '499'
					WHERE IDTipoInvitado = '32' and IDClasificacionInvitado = 386 ";
$dbo->query($sql_inv);
exit;
*/


/*
$sql_clasif_inv="SELECT IDClasificacionInvitado From Invitado,IDClasificacionInvitado, TipoInvitado Where IDClub = 9 Group by IDClasificacionInvitado";
$r_clasif_inv=$dbo->query($sql_invitado);
while($row_tipo=$dbo->fetchArray($r_tipo)){
	$array_id_clasif_utilizados[]=$row_tipo["IDClasificacionInvitado"];
}

print_r($array_id_clasif_utilizados);
exit;
//Borrar los que no tengan nada clasificacion
$sql_tipo="SELECT * FROM `TipoInvitado` WHERE `IDClub` = 9";
$r_tipo=$dbo->query($sql_tipo);
while($row_tipo=$dbo->fetchArray($r_tipo)){
		$sql_clas="SELECT * FROM `ClasificacionInvitado` WHERE `IDTipoInvitado` = '".$row_tipo["IDTipoInvitado"]."' ";
		$r_clas=$dbo->query($sql_clas);
		while($row_clas=$dbo->fetchArray($r_clas)){
			$sql_invitado="SELECT IDInvitado From Invitado Where IDClasificacionInvitado = '".$row_clas["IDClasificacionInvitado"]."' Limit 1";
			$r_invitado=$dbo->query($sql_invitado);
			$total=$dbo->rows($r_invitado);
			if($total<=0){
				//Borro la clasificacion
				echo "<br>Borro ".$row_clas["Nombre"];
				echo "<br>".$sql_borra="DELETE FROM ClasificacionInvitado WHERE IDClasificacionInvitado = '".$row_clas["IDClasificacionInvitado"]."'";
				//$dbo->query($sql_inv);
			}

		}
}
exit;

$sql_clas="SELECT * FROM `ClasificacionInvitado` WHERE `IDClub` = 9";
$r_tipo=$dbo->query($sql_tipo);
while($row_tipo=$dbo->fetchArray($r_tipo)){
	//$sql_invitado="SELECT * From Invitado Where IDTipoInvitado = '".$row_tipo["IDTipoInvitado"]."' Limit 1";
	//$r_invitado=$dbo->query($sql_invitado);
	$total=$dbo->rows($r_invitado);
	if($total<=0){
		//Borro la clasificacion
		echo "<br>Borro ".$row_tipo["Nombre"];
		echo "<br>". $sql_borra="DELETE FROM TipoInvitado WHERE IDTipoInvitado = '".$row_tipo["IDTipoInvitado"]."'";
		//$dbo->query($sql_inv);
	}
}


echo "<br>Fin.";
exit;
*/




/*
$array_adentro = array("79126302",
"1070976336",
"80357465",
"1070600122",
"1075319259",
"1070604046",
"1070600122",
"1000505643",
"1000236253");

$IDClub=9;
foreach($array_adentro as $ident){
	$datos_inv = $dbo->fetchAll( "Invitado", " NumeroDocumento = '" . $ident . "' ", "array" );

	if($datos_inv["IDInvitado"]>0){
					$TipoInvitacion="Contratista";
					$sql_registramov="SELECT IDSocioAutorizacion FROM SocioAutorizacion WHERE IDInvitado='".$datos_inv["IDInvitado"]."' and IDClub = '".$IDClub."' ";
					$r_resgitramov=$dbo->query($sql_registramov);
					while($row_registramov=$dbo->fetchArray($r_resgitramov)){
						$sql_inserta_historial_otro = "INSERT INTO LogAcceso (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('".$row_registramov["IDSocioAutorizacion"]."','".$IDClub."', '".$TipoInvitacion."','S','Salida Aut MY 2020-04-06', NOW(),NOW(),'".$IDUsuario."')";
						//$dbo->query($sql_inserta_historial_otro);
					}
	}

}
echo "<br><br>Terminado";
exit;
*/
/*
$sql_ingreso="SELECT  * From LogAccesoDiario Where IDClub=9 and Entrada = 'S' and Tipo='Contratista' ";
$r_ingreso=$dbo->query($sql_ingreso);
while($row_ingreso=$dbo->fetchArray($r_ingreso)){
	//reviso si ya salió
	$sql_salida="SELECT  * From LogAcceso Where IDClub=9 and IDInvitacion = '".$row_ingreso["IDInvitacion"]."' Order by IDLogAcceso DESC Limit 1";
	$r_salida=$dbo->query($sql_salida);
	$row_salida=$dbo->fetchArray($r_salida);

	unset($array_id_inv);
	switch($row_ingreso["Tipo"]){
		case "Contratista":
			$sql_aut="SELECT * From SocioAutorizacion WHERE IDSocioAutorizacion = '".$row_ingreso["IDInvitacion"]."'";
			$r_aut=$dbo->query($sql_aut);
			while($row_aut=$dbo->fetchArray($r_aut)){
					//echo "Aut " . $row_aut["IDInvitado"];
					$array_id_inv[]=$row_aut["IDInvitado"];
			}
			if(count($array_id_inv)>0){
				$id_consulta=implode(",",$array_id_inv);
				//$sql_inv="SELECT * FROM Invitado WHERE IDInvitado in (".$id_consulta.") and IDTipoInvitado = 31";
				$sql_inv="SELECT * FROM Invitado WHERE IDInvitado in (".$id_consulta.")";
				$r_inv=$dbo->query($sql_inv);
				while($row_inv=$dbo->fetchArray($r_inv)){
						echo "<br>Invitado " . $row_inv["Nombre"] .  " " . $row_inv["NumeroDocumento"];
				}


			}
			//$datos_inv = $dbo->fetchAll( "Invitado", " IDPropietario = '" . $row_pqr[ "IDPropietario" ] . "' ", "array" );
		break;
	}


	if($row_salida["Salida"]<>"S"){
			echo "<br>no ha salido";
			$nosalio++;

}
else{
	echo "<br>Ya salio";
	$sisalio++;
}




}

echo "<br>Salieron " . $sisalio . " No salio:".$nosalio;

exit;
*/

/*

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "http://186.96.100.195:804/wsZeusMyclub/ServiceWS.asmx",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS =>"<Envelope xmlns=\"http://schemas.xmlsoap.org/soap/envelope/\">\n    <Body>\n        <StandardCommunicationSOAP xmlns=\"http://www.w3.org/XML/1998/namespace:lang\">\n            <!-- Optional -->\n            <Request>\n                <TypeSQL></TypeSQL>\n                <DynamicProperty></DynamicProperty>\n                <SessionID>d8c11169-2ee5-474b-9ce6-d503df2d8c60</SessionID>\n                <Action>ConsultarEstadoSocio</Action>\n                <Body><![CDATA[\n\t        <Interfaz_ZeusClubes>\n\t  <EstadoSocio>\n\t<vchidentificacion></vchidentificacion> <vchaccion></vchaccion> <vchsecuencia></vchsecuencia>\n\t  </EstadoSocio>\n\t</Interfaz_ZeusClubes>\n\t      ]]></Body>\n            </Request>\n        </StandardCommunicationSOAP>\n    </Body>\n</Envelope>",
	  CURLOPT_HTTPHEADER => array(
	    "Content-Type: text/xml"
	  ),
	));

	$response = curl_exec($curl);
	curl_close($curl);
	print_r($response);
	echo "FIN.";
	exit;



	$response=str_replace("&lt;","<",$response);
	$response=str_replace("&gt;",">",$response);
	$response=str_replace('<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body>',"",$response);
	$response=str_replace('<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body>',"",$response);
	$response=str_replace('</soap:Body></soap:Envelope>',"",$response);
	$response=str_replace('<StandardCommunicationSOAPResponse xmlns="http://www.w3.org/XML/1998/namespace:lang"><StandardCommunicationSOAPResult><SessionID>d8c11169-2ee5-474b-9ce6-d503df2d8c60</SessionID><Body><?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>',"",$response);
	$response=str_replace('<status><code>SUCESS</code><message /></status>',"",$response);
	$response=str_replace('</Body><Status>SUCCESS</Status><Message /><Code /></StandardCommunicationSOAPResult></StandardCommunicationSOAPResponse>',"",$response);
	//echo $response;


	//echo $response;
	$carga_xml = simplexml_load_string($response);
	//print_r($carga_xml);

	foreach($carga_xml->item as $datos){
	  echo $datos->identificacion;
	}

	exit;


	$urlendpoint="http://186.96.100.195:804/wsZeusMyclub/ServiceWS.asmx?WSDL";
	$usuariuozeus="wszeusclubes";
	$clavezeus="zeusclubes";

	$Token= SIMWebServiceZeus::obtener_token_club($urlendpoint,$usuariuozeus,$clavezeus);
	print_r($Token);
	echo "<br>ESTADO SOCIO<br>";
	$array_estado = SIMWebServiceZeus::estado_socio($urlendpoint,$Token,"","","");
	//$array_estado = SIMWebServiceZeus::estado_socio($urlendpoint,$Token,"79935679","","");
	print_r($array_estado);

	echo "<br>FIN<br>";
	exit;
*/



	/*
	$IDClub=70;
	$FechaInicio="2019-11-15";
	$FechaFin="2019-11-16";
	$sql_fecha="SELECT ReservaHotel.IDHabitacion
	FROM ReservaHotel WHERE IDClub = '".$IDClub."' and
	(
			(ReservaHotel.FechaInicio <= '".$FechaInicio."' AND ReservaHotel.FechaFin > '".$FechaInicio."')	OR
			(ReservaHotel.FechaInicio < '".$FechaInicio."' AND ReservaHotel.FechaFin > '".$FechaInicio."')	OR
			(ReservaHotel.FechaInicio < '".$FechaFin."' AND ReservaHotel.FechaFin > '".$FechaFin."')				OR
			(ReservaHotel.FechaInicio < '".$FechaFin."' AND ReservaHotel.FechaFin > '".$FechaFin."')				OR
			(ReservaHotel.FechaInicio < '".$FechaFin."' AND ReservaHotel.FechaFin > '".$FechaFin."')			OR
			( '".$FechaInicio."' <= ReservaHotel.FechaInicio AND '".$FechaFin."' > ReservaHotel.FechaInicio )	OR
			( '".$FechaInicio."' < ReservaHotel.FechaInicio AND '".$FechaFin."' > ReservaHotel.FechaInicio )	OR
			( '".$FechaInicio."' < ReservaHotel.FechaFin AND '".$FechaFin."' > ReservaHotel.FechaFin )	OR
			( '".$FechaInicio."' < ReservaHotel.FechaFin AND '".$FechaFin."' > ReservaHotel.FechaFin )
	)
	AND ReservaHotel.Estado IN ( 'pendiente' , 'enfirme' )";

	echo $sql_fecha . "<br>";
	$r_fecha=$dbo->query($sql_fecha);
	while($row_fecha=$dbo->fetchArray($r_fecha)){
		echo "<br>" . $row_fecha["IDHabitacion"];
		echo " " . $dbo->getFields( "Habitacion" , "NumeroHabitacion" , "IDHabitacion = '".$row_fecha["IDHabitacion"]."'" );
	}
	exit;
*/


//$resp=SIMUtil::sonda_place_to_pay();
//$resp=SIMUtil::enviar_ws_lote();
//echo "listo";
//exit;



/*
$IDClub = 88;
$sql_soc = "SELECT * FROM Socio WHERE IDClub = '".$IDClub."' ";
$result_soc = $dbo->query($sql_soc);
while($row_soc = $dbo->fetchArray($result_soc)):

	//Seccion Noticias
	$sql_secc_club = "SELECT * From Seccion Where IDClub = '".$IDClub."'";
	$result_secc_club = $dbo->query($sql_secc_club);
	while($row_secc = $dbo->fetchArray($result_secc_club)){
		//Verifico si ya el socio la tiene si no se la creo
		$sql_soci_secc = "SELECT * From SocioSeccion Where IDSeccion = '".$row_secc["IDSeccion"]."' and IDSocio = '".$row_soc["IDSocio"]."'";
		$result_soci_secc = $dbo->query($sql_soci_secc);
		if($dbo->rows($result_soci_secc)<=0):
			echo "<br>" . $insert_secc = "Insert into SocioSeccion (IDSocio, IDSeccion) Values ('".$row_soc["IDSocio"]."','".$row_secc["IDSeccion"]."')";
			$dbo->query($insert_secc);
			$count_noticia++;
		endif;
	}
	//Fin Seccion Noticias

	//Seccion Galerias
	$sql_secc_club = "SELECT * From SeccionGaleria Where IDClub = '".$IDClub."'";
	$result_secc_club = $dbo->query($sql_secc_club);
	while($row_secc = $dbo->fetchArray($result_secc_club)){
		//Verifico si ya el socio la tiene si no se la creo
		$sql_soci_secc = "SELECT * From SocioSeccionGaleria Where IDSeccionGaleria = '".$row_secc["IDSeccionGaleria"]."' and IDSocio = '".$row_soc["IDSocio"]."'";
		$result_soci_secc = $dbo->query($sql_soci_secc);
		if($dbo->rows($result_soci_secc)<=0):
			echo "<br>" . $insert_secc = "Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('".$row_soc["IDSocio"]."','".$row_secc["IDSeccionGaleria"]."')";
			$dbo->query($insert_secc);

		endif;
	}
	//FIN Seccion Galerias

	//Seccion Eventos
	$sql_secc_club = "SELECT * From SeccionEvento Where IDClub = '".$IDClub."'";
	$result_secc_club = $dbo->query($sql_secc_club);
	while($row_secc = $dbo->fetchArray($result_secc_club)){
		//Verifico si ya el socio la tiene si no se la creo
		$sql_soci_secc = "SELECT * From SocioSeccionEvento Where IDSeccionEvento = '".$row_secc["IDSeccionEvento"]."' and IDSocio = '".$row_soc["IDSocio"]."'";
		$result_soci_secc = $dbo->query($sql_soci_secc);
		if($dbo->rows($result_soci_secc)<=0):
			echo "<br>" . $insert_secc = "Insert into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('".$row_soc["IDSocio"]."','".$row_secc["IDSeccionEvento"]."')";
			$dbo->query($insert_secc);
		$count_evento++;
		endif;
	}
	//FIN Seccion Galerias

	//Seccion Eventos
	$sql_secc_club = "SELECT * From SeccionEvento2 Where IDClub = '".$IDClub."'";
	$result_secc_club = $dbo->query($sql_secc_club);
	while($row_secc = $dbo->fetchArray($result_secc_club)){
		//Verifico si ya el socio la tiene si no se la creo
		$sql_soci_secc = "SELECT * From SocioSeccionEvento2 Where IDSeccionEvento2 = '".$row_secc["IDSeccionEvento2"]."' and IDSocio = '".$row_soc["IDSocio"]."'";
		$result_soci_secc = $dbo->query($sql_soci_secc);
		if($dbo->rows($result_soci_secc)<=0):
			echo "<br>" . $insert_secc = "Insert into SocioSeccionEvento2 (IDSocio, IDSeccionEvento2) Values ('".$row_soc["IDSocio"]."','".$row_secc["IDSeccionEvento2"]."')";
			$dbo->query($insert_secc);
		endif;
	}
	//FIN Seccion Galerias

	//Seccion Clasificado
	$sql_secc_club = "SELECT * From SeccionClasificados Where IDClub = '".$IDClub."'";
	$result_secc_club = $dbo->query($sql_secc_club);
	while($row_secc = $dbo->fetchArray($result_secc_club)){
		//Verifico si ya el socio la tiene si no se la creo
		$sql_soci_secc = "SELECT * From SocioSeccionClasificados Where IDSeccionClasificados = '".$row_secc["IDSeccionClasificados"]."' and IDSocio = '".$row_soc["IDSocio"]."'";
		$result_soci_secc = $dbo->query($sql_soci_secc);
		if($dbo->rows($result_soci_secc)<=0):
			echo "<br>" . $insert_secc = "Insert into SocioSeccionClasificados (IDSocio, IDSeccionClasificados) Values ('".$row_soc["IDSocio"]."','".$row_secc["IDSeccionClasificados"]."')";
			$dbo->query($insert_secc);
		endif;
	}
	//FIN Seccion Galerias


endwhile;


echo $count_noticia;
echo "even " . $count_evento;
exit;


exit;
*/

	/*
	$sql_rese="SELECT * from ReservaGeneral WHERE Fecha >= '2019-09-26' and IDClub = 44";
	$r_reser=$dbo->query($sql_rese);
	while($row_reser=$dbo->fetchArray($r_reser)){
			$row_reser["IDReservaGeneral"];
			$sql_soc="SELECT IDSocio from Socio WHERE IDSocio = '".$row_reser["IDSocio"]."' and IDClub = 44";
			$r_soc=$dbo->query($sql_soc);
			if($dbo->rows($r_soc)<=0){
					echo "<br>No existe este socio en esta reserva " . $row_reser["IDReservaGeneral"] . " IDSocio " . $row_reser["IDSocio"];
			}
			else{
				//echo "<br>Si existe este socio en esta reserva " . $row_reser["IDReservaGeneral"];
			}
	}
	exit;

	$IDClub=13;
	$datos_club = $dbo->fetchAll( "Club", " IDClub = '" . $IDClub . "' ", "array" );
	$valor=320000;
	$IDSocio=5533;
	$result=SIMPasarelaPagos::zona_pagos_pagar($datos_club,$IDSocio,$valor);
	SIMPasarelaPagos::zona_pagos_pagar($datos_club,$IDSocio,$valor);
	$IDPago=1;
	//$result=SIMPasarelaPagos::zona_pagos_verificar_pago($IDPago);
	print_r($result);
	exit;

	$FechaInicio="2019-09-20";
	$FechaFin= date("Y-m-d",strtotime ( '+1 day' , strtotime ( $FechaInicio ) )) ;
	$dia_actual=date("d");
	$dia=20;
	if((int)$dia>=$dia_actual){
		$resp=SIMWebServiceHotel::get_valida_fecha( "8", $FechaInicio, $FechaFin);
		$habitaciones="N";
		foreach ($resp["response"] as $key => $resultado) {
			foreach ($resultado["Habitaciones"] as $key2 => $habitacion) {
				foreach ($habitacion["HabitacionTorre"] as $key3 => $hab_torre) {
				$habitaciones="S";
			}
		}
	}

	echo $habitaciones;

		exit;

		if(count($resp["response"])>0){
				echo "si";
		}
		print_r($resp["response"]);
	}
echo "FIN";
exit;

	//$resp=SIMUtil::push_notifica_libera_reserva("34","3164256");
	//print_r($resp);
	//exit;
*/


	/*
	$urlendpoint="http://192.168.1.125:804/MiClub/ServiceWS.asmx?WSDL";
	$usuariuozeus="zeusclub";
	$clavezeus="zeusclub";

	$Token= SIMWebServiceZeus::obtener_token_club($urlendpoint,$usuariuozeus,$clavezeus);
	print_r($Token);
	echo "<br>ESTADO SOCIO<br>";
	$array_estado = SIMWebServiceZeus::estado_socio($urlendpoint,$Token,"80422263","80422263","00");
	echo "SALDO";
	//$xml->Respuesta_accion->item->resultado->extracto->accion->titular->nombre
	print_r($array_estado->item->saldocartera);
	echo "<br><br>";
	print_r($array_estado);
	echo "<br>CARTERA SOCIO<br>";
	$array_cartera = SIMWebServiceZeus::cartera_socio($urlendpoint,$Token,"80422263","00");
	print_r($array_cartera);
	print_r($total);
	echo "<br>FIN<br>";
	exit;
*/




	/*
	$message = "Galeria";
	$custom["tipo"] = "gallery";
	$custom["idseccion"] = (string)142;
	$custom["iddetalle"] = (string)28;
	$custom["idmodulo"] = (string)5;
	$custom["titulo"] = "Notificacion Club";

	$message = "Restaurante";
	$custom["tipo"] = "Restaurante";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"8";
	$custom["idmodulo"] = (string)7;
	$custom["titulo"] = "Notificacion Club";


	$message = "Factura";
	$custom["tipo"] = "Factura";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"1";
	$custom["idmodulo"] = (string)27;
	$custom["titulo"] = "Notificacion Club";


	$message = "Correspondencia";
	$custom["tipo"] = "Correspondencia";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"145960";
	$custom["idmodulo"] = (string)59;
	$custom["titulo"] = "Notificacion Club";

	$message = "Encuesta";
	$custom["tipo"] = "Encuesta";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"73";
	$custom["idmodulo"] = (string)58;
	$custom["titulo"] = "Notificacion Club";


	$message = "Votaciones";
	$custom["tipo"] = "Votaciones";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"9";
	$custom["idmodulo"] = (string)70;
	$custom["titulo"] = "Notificacion Club";

	$message = "PQR SOCIO";
	$custom["tipo"] = "Pqr";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"3849";
	$custom["idmodulo"] = (string)15;
	$custom["titulo"] = "Notificacion Club";

	$message = "PQR FUNCIONARIO	";
	$custom["tipo"] = "Pqr";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"17";
	$custom["idmodulo"] = (string)15;
	$custom["titulo"] = "Notificacion Club";


	$message = "Beneficios	";
	$custom["tipo"] = "Beneficios";
	$custom["idseccion"] = (string)"98";
	$custom["iddetalle"] = (string)"798";
	$custom["idmodulo"] = (string)72;
	$custom["titulo"] = "Notificacion Club";


	$message = "Reservas	";
	$custom["tipo"] = "Reservas";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"2734813";
	$custom["idmodulo"] = (string)2;
	$custom["titulo"] = "Notificacion Club";


	$message = "Domicilios	";
	$custom["tipo"] = "Domicilios";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"813";
	$custom["idmodulo"] = (string)33;
	$custom["titulo"] = "Notificacion Club";

	$message = "Canjes	";
	$custom["tipo"] = "Canjes";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"1995";
	$custom["idmodulo"] = (string)34;
	$custom["titulo"] = "Notificacion Club";


	$message = "Clasificado	";
	$custom["tipo"] = "Clasificado";
	$custom["idseccion"] = (string)"28";
	$custom["iddetalle"] = (string)"800";
	$custom["idmodulo"] = (string)46;
	$custom["titulo"] = "Notificacion Club";



	$message = "Hotel	";
	$custom["tipo"] = "Hotel";
	$custom["idseccion"] = (string)"";
	$custom["iddetalle"] = (string)"9812";
	$custom["idmodulo"] = (string)43;
	$custom["titulo"] = "Notificacion Club";




	$users = array( array( "id" => "5533",
		"idclub"=>"8",
		"registration_key"=>"0618d0c42ad245ed5e1ec7cdea8a5af28a137ee62a044ae544e81883ee0f1d68",
		"deviceType"=>"iOS" ));

	print_r($users)	;

	///enviar notificación
	SIMUtil::sendAlerts($users, $message, $custom);
	echo "Enviado";
	exit;
*/



/*
$server = '190.0.53.38';
// Connect to MSSQL CASMPRESTRE MEDELLIN
$link = mssql_connect($server, 'miclub', '#miclub**');
if (!$link) {
	//die('Algo fue mal mientras se conectaba a MSSQL.');
		//mssql_get_last_message();
		die('MSSQL error: ' . mssql_get_last_message());
		var_dump($link);
}


mssql_select_db('COMANDA', $link);

$TablaExterna="vapp_pag_documento";
//Verificar cual fue el ultimo id guardado

$sql = mssql_query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME = 'vapp_pag_documento'") or die ("Error al consultar" . mssql_get_last_message());
while ($row = mssql_fetch_array($sql)){
	print_r($row);
}
echo "<br><br><br>";


$sql = mssql_query("SELECT TOP 1000000
				CAST(numero_factura AS TEXT) AS numero_factura
				FROM vapp_pag_documento   ") or die ("Error al consultar" . mssql_get_last_message());

echo "TOTAL" . mssql_num_rows($sql);

$tabla="<table>
					<tr>
						<td>numero_factura</td>
						<td>codigo_cliente</td>
						<td>desc tipo pago</td>
						<td>consec pago</td>
						<td>valor</td>

					</tr>";

while ($row = mssql_fetch_array($sql)){
$tabla.="<tr>
		<td>$row[numero_factura]</td>
		<td>$row[tso_codigo_cliente]</td>
		<td>$row[desc_tipo_pago]</td>
		<td>$row[valor]</td>
		<td>$row[consec_pago_cuenta]</td>
		<td>$row[valor]</td>
	</tr>";
}
$tabla.="</table>";

echo $tabla;
echo "FIN";



	exit;


/*


	$array_conjunto=array("");
	$sql_soc="SELECT * FROM Socio Where IDClub = 18";
	$r_soc=$dbo->query($sql_soc);
	while($row_soc=$dbo->fetchArray($r_soc)){
			$array_nombre_conjunto=explode(" ",$row_soc["Predio"]);
			$array_predio[$array_nombre_conjunto["0"]]=$array_nombre_conjunto["0"];
			switch(	$array_nombre_conjunto["0"]){
				case "Arce":
					$IDClub="";
				break;
				case "Santa":
					$IDClub="";
				break;
				case "Aliso":
					$IDClub="66";
				break;
				case "Acacia":
					$IDClub="58";
				break;
				case "Nogal":
					$IDClub="59";
				break;
				case "Samán":
				case "Saman":
					$IDClub="62";
				break;
				case "Ciprés":
				case "Cipres":
					$IDClub="60";
				break;
				case "Cedro":
					$IDClub="61";
				break;
				case "Guayacan":
				case "Guayacán":
					$IDClub="63";
				break;
				case "Roble":
					$IDClub="";
				break;
				case "Cerezo":
					$IDClub="";
				break;
				case "Alcaparro":
					$IDClub="67";
				break;
				case "Alamo":
					$IDClub="";
				break;
				case "Sauce":
					$IDClub="64";
				break;
				case "Castaño":
				case "Castaño":
					$IDClub="69";
				break;
				case "Almendro":
					$IDClub="65";
				break;
				case "Canelo":
					$IDClub="68";
				break;
				case "Tagua":
					$IDClub="";
				break;
				case "Ficus":
					$IDClub="";
				break;
				case "Pimiento":
					$IDClub="";
				break;
				default:
					$nombre_codificado=utf8_encode($array_nombre_conjunto["0"]);
					switch(	$nombre_codificado){
						case "Castaño":
							$IDClub="69";
						break;
						case "Guayacán":
							$IDClub="63";
						break;
						default:
							if(!empty($nombre_codificado)){
								echo "<br>No existe: " .$array_nombre_conjunto["0"];
								$array_no_existe[$array_nombre_conjunto["0"]]=$array_nombre_conjunto["0"];
							}
					}
			}

			if($IDClub!=""){
					$Documento=$row_soc["NumeroDocumento"];
					//Consulto si existe
					$sql_socio = "Select *
									From Socio
									Where IDClub = '".$IDClub."'
									and NumeroDocumento = '$Documento' ";
					$result_socio = $dbo->query($sql_socio);
					$total_socio = $dbo->rows($result_socio);
					if((int)$total_socio<=0){
						$Clave="sha1('".$row_soc["NumeroDocumento"]."')";
						$siguiente="SELECT MAX(IDSocio) as Siguiente FROM Socio";
						$r_siguiente=$dbo->query($siguiente);
						$row_siguiente=$dbo->fetchArray($r_siguiente);
						$consecutivo=(int)$row_siguiente["Siguiente"]+1;
						$sql_inserta = "INSERT INTO Socio
														SELECT $consecutivo,IDSocioSistemaExterno,'".$IDClub."',IDNacionalidad,IDPais,IDDepartamento,IDCiudad,IDCategoria,IDParentesco,IDParentescoZeus,IDTipoSocioZeus,IDEstadoSocio,IDSocioPresalida,Accion,AccionPadre,Parentesco,NumeroDerecho,Genero,Nombre,Apellido,FechaNacimiento,NumeroDocumento,Email,$Clave,ClaveSistemaExterno,CorreoElectronico,Telefono,Celular,Direccion,DireccionOficina,TelefonoOficina,NombreBeneficiario,CodigoBarras,CodigoQR,Dispositivo,Token,Foto,FotoActualizadaSocio,FechaActualizacionFoto,ObservacionGeneral,ObservacionEspecial,FechaTrCr,UsuarioTrCr,FechaTrEd,UsuarioTrEd,Password,TipoSocio,FechaInicioCortesia,FechaFinCortesia,FechaInicioCanje,FechaFinCanje,ClubCanje,FechaInicioInvitado,FechaFinInvitado,Categoria,NumeroInvitados,NumeroAccesos,PermiteReservar,Predio,CambioClave,SegundaClave,CambioSegundaClave,SolicitarCierreSesion,FechaCierreSesion,FechaPrimerIngreso,Handicap,PushIngresoInvitado,PushIngresoContratista,ValorCartera,Presalida,FehaPresalida
														FROM Socio WHERE IDSocio = '".$row_soc["IDSocio"]."'";
						echo "<br>" . $sql_inserta;
						//$dbo->query($sql_inserta);
					}
					else{
						echo "<br>Ya existe " . $Documento;
					}
			}
			else{
				echo "<br>SIN CONJUNTO " .$array_nombre_conjunto["0"];
				$array_no_existe[$array_nombre_conjunto["0"]]=$array_nombre_conjunto["0"];

			}
	}

	echo "<br>Terminado<br><br>";
	print_r($array_no_existe);
	exit;




/*
$sql_club="SELECT * FROM Club WHERE 1 ";
$result_club=$dbo->query($sql_club);
while($row_club=$dbo->fetchArray($result_club)){
	$sql_tipo_socio="SELECT * FROM Parentesco WHERE 1 ";
	$result_tipo_socio=$dbo->query($sql_tipo_socio);
	while($row_tipo_socio=$dbo->fetchArray($result_tipo_socio)){
			$inserta_tipo="INSERT INTO ClubParentesco (IDClub,IDParentesco) VALUES('".$row_club["IDClub"]."','".$row_tipo_socio["IDParentesco"]."')";
			//$dbo->query($inserta_tipo);
	}
}
echo "Terminado";
exit;
*/







	/*
	place to pay
	$accion_socio = 3480;
	if(!empty($accion_socio)){
		//Verifico si la membresia existe
		$endpoint = ENDPOINT_CONDADO;
		$wsdlFile= ENDPOINT_CONDADO;
		try {
		    $client = new SoapClient($wsdlFile, array('exceptions' => 0));
				$parameters = array( AplicarCabecera => array( FacturaCabecera => array(
					 Comentario=>"1",
					 Fecha=>"2019-05-24",
					 Id_Cliente=>"123",
					 Id_Recibo=>"124",
					 Monto=>"50",
					 Observacion=>"prueba",
					 Papeleta=>"",
					 Tipo=>"TC"
				 )),
				 AplicarDetalle => array( FacturaDetalle => array(
					 Id_Cliente=>"123",
					 Id_Recibo=>"123",
					 Monto_Documento=>"50",
					 Numero_Documento=>"123",
					 Observacion=>"12",
					 Tipo_Documento=>"1",
				 ))
			 );




		    $result = $client->Pago_Factura($parameters);
				print_r($result->Pago_FacturaResult);
		} catch (SoapFault $fault) {
		    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
		}

}
exit;
*/





	/*
	$server = '190.0.53.38';
	// Connect to MSSQL CASMPRESTRE MEDELLIN
	$link = mssql_connect($server, 'miclub', '#miclub**');
	if (!$link) {
		//die('Algo fue mal mientras se conectaba a MSSQL.');
	 		//mssql_get_last_message();
			die('MSSQL error: ' . mssql_get_last_message());
			var_dump($link);
	}


	mssql_select_db('COMANDA', $link);

	$id_domicilio = '1130';



	$sql = mssql_query("INSERT INTO vapp_enc_pedido (ident_cliente,fecha_envio,comentario,id_pedido)
											VALUES('900293023-0','2019-10-11','prueba',1101) " ) or die ("Error al consultar: " . mssql_get_last_message());

	echo "INSERTA";
	exit;



	$sql = mssql_query("SELECT CAST(id_pedido  AS INTEGER) AS id_pedido, CAST(codigo_producto_pos  AS INTEGER) AS codigo_producto_pos
								 FROM vapp_pedidos
								 WHERE codigo_producto_pos > 0  ") or die ("Error al consultar: " . mssql_get_last_message());
	while ($row = mssql_fetch_array($sql)){
		print_r($row);
	}
	echo "<br><br><br>FIN.";

	exit;

	$TablaExterna="vapp_estados_cuenta";
	//Verificar cual fue el ultimo id guardado


	$sql = mssql_query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME = 'vapp_estados_cuenta'") or die ("Error al consultar" . mssql_get_last_message());
	while ($row = mssql_fetch_array($sql)){
		print_r($row);
	}
	echo "<br><br><br>";



	$sql = mssql_query("SELECT
					CAST(numero_factura AS TEXT) AS numero_factura,
					CAST(fecha AS DATETIME) AS fecha,
					CAST(lugar AS TEXT) AS lugar,
					CAST(valor  AS INTEGER) AS valor,
					CAST(codigo_derecho AS INTEGER) AS codigo_derecho,
					CAST(tso_codigo_cliente AS INTEGER) AS tso_codigo_cliente,
					CAST(codigo_cliente  AS INTEGER) AS codigo_cliente,
					CAST(nombre_cajero AS TEXT) AS nombre_cajero
				   FROM vapp_enc_documento WHERE numero_factura = 'TES54635' ") or die ("Error al consultar" . mssql_get_last_message());



	$tabla="<table>
						<tr>
							<td>numero_factura</td>
							<td>fecha</td>
							<td>lugar</td>
							<td>valor</td>
							<td>codigo_derecho</td>
							<td>tso_codigo_cliente</td>
							<td>codigo_cliente</td>
							<td>nombre_cajero</td>
						</tr>";

	while ($row = mssql_fetch_array($sql)){
	$tabla.="<tr>
			<td>$row[numero_factura]</td>
			<td>$row[fecha]</td>
			<td>$row[lugar]</td>
			<td>$row[valor]</td>
			<td>$row[codigo_derecho]</td>
			<td>$row[tso_codigo_cliente]</td>
			<td>$row[codigo_cliente]</td>
			<td>$row[nombre_cajero]</td>
		</tr>";
	}
	$tabla.="</table>";

	echo $tabla;
*/

	/*
	$sql = mssql_query("SELECT
							 CAST(codigo_cliente AS INTEGER) AS codigo_cliente,
						   CAST(nombre_cliente AS TEXT) AS nombre_cliente,
							 CAST(ident_cliente AS TEXT) AS ident_cliente,
							 CAST(seccion AS INTEGER) AS seccion,
							 CAST(concepto AS TEXT) AS concepto,
						   CAST(nombre_pagador AS TEXT) AS nombre_pagador,
							 CAST(nit_pagador  AS TEXT) AS nit_pagador,
							 CAST(cargo AS INTEGER) AS cargo,
							 CAST(documento AS TEXT) AS documento,
							 CAST(fecha AS DATETIME) AS fecha,
							 CAST(area AS TEXT) AS area,
							 CAST(cargo AS INTEGER) AS cargo
						   FROM [vapp_estados_cuenta]
							 WHERE codigo_derecho = '469' ") or die ("Error al consultar" . mssql_get_last_message());

	$tabla="<table>
						<tr>
							<td>codigo_cliente</td>
							<td>nombre_cliente</td>
							<td>ident_cliente</td>
							<td>seccion</td>
							<td>concepto</td>
							<td>nombre_pagador</td>
							<td>nit_pagador</td>
							<td>cargo</td>
							<td>documento</td>
							<td>fecha</td>
							<td>area</td>
							<td>titular</td>
							<td>codigo_derecho</td>
						</tr>";

	while ($row = mssql_fetch_array($sql)){
	$tabla.="<tr>
			<td>$row[codigo_cliente]</td>
			<td>$row[nombre_cliente]</td>
			<td>$row[ident_cliente]</td>
			<td>$row[seccion]</td>
			<td>$row[concepto]</td>
			<td>$row[nombre_pagador]</td>
			<td>$row[nit_pagador]</td>
			<td>$row[cargo]</td>
			<td>$row[documento]</td>
			<td>$row[fecha]</td>
			<td>$row[area]</td>
			<td>$row[cargo]</td>
			<td>$row[titular]</td>
			<td>$row[codigo_derecho]</td>
		</tr>";
	}
	$tabla.="</table>";

	echo $tabla;

	echo "<br>terminado";
*/



	/*

	//LEER UNA BASE DE UN ARCHIVO PLANO
	ini_set('max_execution_time', 0);
  $FIELD_TEMINATED = "TAB";
	$file = "basesocio/CaddieMayo2019.txt";
  $IDClub = 7;

	if($fp = fopen($file,"r")):


		ini_set('auto_detect_line_endings', true);
		while(!feof($fp)):

		$row = fgets($fp,4096);
		if(!empty($FIELD_TEMINATED))
			if($FIELD_TEMINATED == "TAB")
				$row_data = explode("\t",$row);
			else
				$row_data = explode($FIELD_TEMINATED,$row);



			$Documento=trim($row_data[0]);
      $Nombre=trim($row_data[2] );
      $Apellido=trim($row_data[1]);
      $Codigo=trim($row_data[3] );
      $IDCategoria=trim($row_data[4] );


			switch($IDCategoria){
				case "1":
					$IDCategoria=11;
				break;
				case "2":
					$IDCategoria=12;
				break;
				case "3":
					$IDCategoria=13;
				break;
			}

      $Telefono = trim($row_data[5]);


			if(!empty($Documento) && $Documento!=0 &&  !empty($Nombre) ):

				if($contador>=1): //Para no tomar el encabezado

						//Consulto si existe
						$sql_caddie = "Select *
									  From Caddie
									  Where NumeroDocumento = '".$Documento."'";
						$result_caddie = $dbo->query($sql_caddie);
						$total_caddie = $dbo->rows($result_caddie);
						if((int)$total_caddie>0):
							$datos_caddie = $dbo->fetchArray($result_caddie);

							$sql_update = "Update Caddie
										  Set Codigo = '".$Codigo."', nombre = '".$Nombre."',
										  apellido = '".$Apellido."', IDCategoriaCaddie = '".$IDCategoria."'
										  Where IDCaddie = '".$datos_caddie["IDCaddie"]."' and IDClub = '".$IDClub."'";

							echo "<br>".$sql_update;
							//exit;
							$dbo->query($sql_update);
						else:

							$sql_inserta = "Insert into Caddie (IDClub,numeroDocumento,Codigo,nombre,apellido,IDCategoriaCaddie)
											Values ('".$IDClub."','".$Documento."','".$Codigo."','".$Nombre."','".$Apellido."','".$IDCategoria."')";

							echo "<br>" . $sql_inserta;
							$dbo->query($sql_inserta);
						endif;
				endif;

			else:
				echo "<br>Faltan Datos " . 	$Accion;
			endif;
			$contador++;


		endwhile;
	else:
		echo "No se pudo abrir";
	endif;
  echo "Terminado";
exit;
//FIN LEER UNA BASE DE UN ARCHIVO PLANO
*/



/*
$sql="SELECT * FROM ServicioDisponibilidad WHERE 1";
$result_sql=$dbo->query($sql);
while($row_dispo=$dbo->fetchArray($result_sql)){
	$elementos=$row_dispo["IDServicioElemento"];
	if(substr($elementos,0,1)=="|"){
		echo "<br>No se hace nada";
	}
	else{
		$sql_update="UPDATE ServicioDisponibilidad SET IDServicioElemento = concat('|',IDServicioElemento) WHERE IDServicioDisponibilidad = '".$row_dispo["IDServicioDisponibilidad"]."'";
		//$dbo->query($sql_update);
		echo "<br>".$sql_update;
	}
}
echo "Terminado";
exit;
*/

/*
//Prof. Enrique
$array_prof_clinicas_e[165]=96;
$array_prof_clinicas_f[165]=144;
//Prof Natalia
$array_prof_clinicas_e[166]=96;
$array_prof_clinicas_f[166]=48;
//Prof. Jaime
$array_prof_clinicas_e[168]=96;
$array_prof_clinicas_f[168]=48;
//Prof. John Fredy
$array_prof_clinicas_e[169]=98;
$array_prof_clinicas_f[169]=46;
//Prof. Alfonso Sosa
$array_prof_clinicas_e[170]=96;
$array_prof_clinicas_f[170]=48;
//Mon. Carlos Portes
$array_prof_clinicas_e[171]=96;
$array_prof_clinicas_f[171]=48;
//Mon. Dumar Jaime
$array_prof_clinicas_e[172]=96;
$array_prof_clinicas_f[172]=48;
//Mon. Jorge Bayona
$array_prof_clinicas_e[173]=96;
$array_prof_clinicas_f[173]=48;
//Mon. Sergio Calderon
$array_prof_clinicas_e[174]=96;
$array_prof_clinicas_f[174]=48;
//Mon. Cristian Martinez
$array_prof_clinicas_e[175]=96;
$array_prof_clinicas_f[175]=48;
//Prof Luisa Obando
$array_prof_clinicas_e[210]=96;
$array_prof_clinicas_f[210]=48;
//Prof Ivan Joya
$array_prof_clinicas_e[211]=96;
$array_prof_clinicas_f[211]=48;
//Prof Eduardo Babativa
$array_prof_clinicas_e[212]=0;
$array_prof_clinicas_f[212]=0;
//Prof Luis Gonzalez
$array_prof_clinicas_e[213]=0;
$array_prof_clinicas_f[213]=0;
//Prof Josue Guzman
$array_prof_clinicas_e[214]=0;
$array_prof_clinicas_f[214]=0;
//Bol. JosÃ© RodrÃ­guez
$array_prof_clinicas_e[215]=0;
$array_prof_clinicas_f[215]=0;
//Prof Roberto Ortiz
$array_prof_clinicas_e[216]=0;
$array_prof_clinicas_f[216]=0;
//Bol. Royer Benavides
$array_prof_clinicas_e[217]=0;
$array_prof_clinicas_f[217]=0;

echo "<br><br>ENTRE SEMANA<br><br>";
$sql_profesor="SELECT * FROM Auxiliar WHERE IDServicio='3941'";
$r_profesor=$dbo->query($sql_profesor);
while($row_profesor=$dbo->fetchArray($r_profesor)){
	$sql_reserva="SELECT count(IDReservaGeneral) as TotalReservas FROM ReservaGeneralReporte WHERE IDAuxiliar LIKE '%".$row_profesor["IDAuxiliar"]."%' And (DAYOFWEEK(Fecha) <> 1 and DAYOFWEEK(Fecha) <> 7)";
	$r_reserva=$dbo->query($sql_reserva);
	$row_reserva=$dbo->fetchArray($r_reserva);
	$TotalReservas=(int)$row_reserva["TotalReservas"]+(int)$array_prof_clinicas_e[$row_profesor["IDAuxiliar"]];
	echo "<br>" . $row_profesor["Nombre"] . "|" .$TotalReservas;
}

echo "<br><br>FIN SEMANA<br><br>";
$sql_profesor="SELECT * FROM Auxiliar WHERE IDServicio='3941'";
$r_profesor=$dbo->query($sql_profesor);
while($row_profesor=$dbo->fetchArray($r_profesor)){
	$sql_reserva="SELECT count(IDReservaGeneral) as TotalReservas FROM ReservaGeneralReporte WHERE IDAuxiliar LIKE '%".$row_profesor["IDAuxiliar"]."%' And (DAYOFWEEK(Fecha) = 1 or DAYOFWEEK(Fecha) = 7)";
	$r_reserva=$dbo->query($sql_reserva);
	$row_reserva=$dbo->fetchArray($r_reserva);
	$TotalReservas=(int)$row_reserva["TotalReservas"]+(int)$array_prof_clinicas_f[$row_profesor["IDAuxiliar"]];
	echo "<br>" . $row_profesor["Nombre"] . "|" .$TotalReservas;
}
exit;
*/

/*
//Duplicar disponibilidades club comercio
//$sql="Select * From Disponibilidad Where IDServicio = 4433 and IDDisponibilidad = 2082 ";
$sql="Select * From Disponibilidad Where IDServicio = 5035";
$result=$dbo->query($sql);
while($row_dispo=$dbo->fetchArray($result)){
		$NombreDisponibilidad=$row_dispo["Nombre"];
		//duplico esta disponibilidad
		$sql_duplicar="INSERT INTO Disponibilidad (IDServicio,Nombre,Anticipacion,MedicionTiempoAnticipacion,AnticipacionTurno,MedicionTiempoAnticipacionTurno,TiempoCancelacion,MedicionTiempo,Intervalo,MaximoPersonaTurno,NumeroInvitadoClub,NumeroInvitadoExterno,MinimoInvitados,MaximoInvitados,PermiteRepeticion,MedicionRepeticion,NumeroRepeticion,MaximoReservaDia,PermiteReservaSeguida,PermiteReservaSeguidaNucleo,PermiteReservaCumplirTurno,TiempoDespues,MedicionTiempoDespues,PermiteEliminarCreadaStarter,Cupos, Georeferenciacion,MinutoPosteriorTurno,MaximoInvitadosSalon,Activo,UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd)
		SELECT '7973','$NombreDisponibilidad',Anticipacion,MedicionTiempoAnticipacion,AnticipacionTurno,MedicionTiempoAnticipacionTurno,TiempoCancelacion,MedicionTiempo,Intervalo,MaximoPersonaTurno,NumeroInvitadoClub,NumeroInvitadoExterno,MinimoInvitados,MaximoInvitados,PermiteRepeticion,MedicionRepeticion,NumeroRepeticion,MaximoReservaDia,PermiteReservaSeguida,PermiteReservaSeguidaNucleo,PermiteReservaCumplirTurno,TiempoDespues,MedicionTiempoDespues,PermiteEliminarCreadaStarter,Cupos, Georeferenciacion,MinutoPosteriorTurno,MaximoInvitadosSalon,'N',UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd
		FROM Disponibilidad
		Where IDDisponibilidad = '".$row_dispo["IDDisponibilidad"]."'";
		//$dbo->query($sql_duplicar);
		$ultimo_id = $dbo->lastID();
		//Copio el detalle
		$detalle="INSERT INTO ServicioDisponibilidad (IDDisponibilidad,IDServicio,IDDia,IDServicioElemento,Nombre,HoraDesde,HoraHasta,HoraPar,Intervalo,Tee1,Tee10)
		SELECT $ultimo_id,'7973',IDDia,IDServicioElemento,'$NombreDisponibilidad',HoraDesde,HoraHasta,HoraPar,Intervalo,Tee1,Tee10
		FROM ServicioDisponibilidad
		WHERE IDDisponibilidad = '".$row_dispo["IDDisponibilidad"]."'";
		//$dbo->query($detalle);
		echo "<br><br>";
		echo $detalle;
}

exit;
*/

/*
	$url = 'http://179.49.25.110:8082/WS_APPMOVIL.Service1.svc?wsdl';
	$endpoint = $url;
	$wsdlFile= $url;
	//Creación del cliente SOAP
	$clienteSOAP = new SoapClient($wsdlFile,array(
	‘location’=>$endpoint,
	‘trace’=>true,
	‘exceptions’=>false));
	 //Incluye los parámetros que necesites en tu función
	$parameters= array(
	membresia=>1260
	);
	  //Invocamos a una función del cliente, devolverá el resultado en formato array.

	$valor = $clienteSOAP->DeudaSocio($parameters);

	print_r($valor);
	exit;
	foreach($valor->SocioResult->Usuario as $datos_membresia){
		print_r($datos_membresia->CI);
		echo "<br>";
	}

	exit;
		if((int)$valor->ID>0){
				echo "S";
		}
		else{
			echo "N";
		}
	print_r($valor);
	exit;

*/









/*
//Actualiza codigo talega

$IDClub=70;
$sql_talega="Select * From Talega Where IDClub  = '".$IDClub."' and IDSocio <> '' and CodigoArchivo =''";
$r_talega=$dbo->query($sql_talega);
while($row_talega=$dbo->fetchArray($r_talega)){
  $rand = rand(0, 1000);


    $codigo = $IDClub . "-" . $row_talega["IDSocio"] . "-" . $rand;
    if($row_talega["tipoCodigo"] == 1)
      $codigoArchivo= SIMUtil::generar_codigo_barras_talega($codigo,$IDClub);
    else
      $codigoArchivo= SIMUtil::generar_codigo_qr_talega($codigo,$IDClub);


      echo "<br>" . $actualiza_talega="UPDATE Talega SET codigoArchivo = '".$codigoArchivo."' Where  IDTalega = '".$row_talega["IDTalega"]."' and IDClub = '" . $IDClub . "'";
      $dbo->query($actualiza_talega);
      //exit;

}
exit;
*/

/*
//Actualiza el IDSocio de talega con el codigo
$IDClub=70;
$sql_talega="Select * From Talega Where IDClub  = '".$IDClub."'";
$r_talega=$dbo->query($sql_talega);
while($row_talega=$dbo->fetchArray($r_talega)){
    //Consulto al socio
    $datos_socio = $dbo->fetchAll( "Socio", " (Accion = '" . $row_talega["codigo"] . "' or Accion = '" . $row_talega["codigo"] . "-1') and IDClub = '" . $IDClub . "'", "array" );
    if(!empty($datos_socio["IDSocio"])){
      $actualiza_talega="UPDATE Talega SET IDSocio = '".$datos_socio["IDSocio"]."' Where  IDTalega = '".$row_talega["IDTalega"]."' and IDClub = '" . $IDClub . "'";
      $dbo->query($actualiza_talega);
    }
    else{
      echo "<br>No se encuentra el socio con accion : " . $row_talega["codigo"];
    }

}
echo "Fin Talega";
exit;
*/



/*
function redimensionarJPEG ($origen, $destino, $ancho_max, $alto_max, $fijar,$extension)
{

    $info_imagen= getimagesize($origen);
    $ancho=$info_imagen[0];
    $alto=$info_imagen[1];
    if ($ancho>=$alto)
    {
        $nuevo_alto= round($alto * $ancho_max / $ancho,0);
        $nuevo_ancho=$ancho_max;
    }
    else
    {
        $nuevo_ancho= round($ancho * $alto_max / $alto,0);
        $nuevo_alto=$alto_max;
    }
    switch ($fijar)
    {
        case "ancho":
            $nuevo_alto= round($alto * $ancho_max / $ancho,0);
            $nuevo_ancho=$ancho_max;
            break;
        case "alto":
            $nuevo_ancho= round($ancho * $alto_max / $alto,0);
            $nuevo_alto=$alto_max;
            break;
        default:
            $nuevo_ancho=$nuevo_ancho;
            $nuevo_alto=$nuevo_alto;
            break;
    }

	switch($extension):
		case"jpg":
		case"jpeg":
		case"JPG":
		case"JPEG":
			$imagen_nueva= imagecreatetruecolor($nuevo_ancho,$nuevo_alto);
			$imagen_vieja= imagecreatefromjpeg($origen);
			imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0,$nuevo_ancho, $nuevo_alto, $ancho, $alto);
			imagejpeg($imagen_nueva,$destino);
		break;
		case"png":
		case"PNG":

			$imagen_nueva= imagecreatetruecolor($nuevo_ancho,$nuevo_alto);
			$imagen_vieja= imagecreatefrompng($origen);
			imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0,$nuevo_ancho, $nuevo_alto, $ancho, $alto);
			imagepng($imagen_nueva,$destino);
		break;
		default:
			$imagen_nueva= imagecreatetruecolor($nuevo_ancho,$nuevo_alto);
			$imagen_vieja= imagecreatefromjpeg($origen);
			imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0,$nuevo_ancho, $nuevo_alto, $ancho, $alto);
			imagejpeg($imagen_nueva,$destino);
	endswitch;




    imagedestroy($imagen_nueva);
    imagedestroy($imagen_vieja);
}




$sql_fedeg="Select * from Socio Where IDClub = '38' and Foto <> ''";
$result_fedeg=$dbo->query($sql_fedeg);
while($row_fedeg=$dbo->fetchArray($result_fedeg)):
	$path = "../file/socio/".$row_fedeg["Foto"];
	$array_extension=explode(".",$row_fedeg["Foto"]);
	$extension=$array_extension["1"];
	$path_destino = "../file/sociofotoexporta/".$row_fedeg["NumeroDocumento"].".".$extension;

	redimensionarJPEG ($path, $path_destino, "340", "350", "ancho",$extension);


	if(file_exists($path)):
		if(copy($path, $path_destino)):
			echo "<br>Copiado";
		else:
			echo "<br>No se pudo copiar";
		endif;
	else:
		echo "<br>No existe el archivo";
	endif;

endwhile;
exit;
*/

/*
$path = "../file/galeria/b/ruta/";
$dir = opendir($path);
    $files = array();
    while ($current = readdir($dir)){
        if( $current != "." && $current != "..") {
			  if(copy($path.$current, $path."../../ruta".$current))
			  	echo "<br>Copiado";
        }
    }

echo "Terminado";
exit;
*/

/*
$DiaApertura = 5;
$r["HoraApertura"] = "08:00:00";

$dias_semana_eng = array("1"=>"Monday","2"=>"Tuesday","3"=>"Wednesday","4"=>"Thursday","5"=>"friday","6"=>"Saturday","7"=>"Sunday");
$proximo_dia_apertura= strtotime( date ("Y-m-d",strtotime("next ".$dias_semana_eng[$DiaApertura])) . " " . $r["HoraApertura"]);

$proximo_dia_apertura= date("2018-03-01",strtotime("next ".$dias_semana_eng[$DiaApertura]));
echo date("Y-m-d",$proximo_dia_apertura);

echo $dias_semana_eng[$DiaApertura];
$fecha_hoy = new DateTime('2018-03-01');
echo $siguiente_dia = "'next ". $dias_semana_eng[$DiaApertura]."'";
//$fecha_hoy->modify($siguiente_dia);
$prox="friday";
$fecha_hoy->modify('next '.$dias_semana_eng[$DiaApertura]);
//$fecha_hoy->modify('next friday');
echo $proximo_dia_apertura = $fecha_hoy->format('Y-m-d');


exit;

$fecha_minima_apertura = strtotime ('-6 day' , strtotime ( $fecha_validar . " " . $r["HoraApertura"]));
echo "desde " . date("Y-m-d H:i:s",$fecha_minima_apertura);
exit;


$proximo_dia_apertura= strtotime( "2018-03-09 08:00:00");
//calculo cuantos minutos falta para la hora de apertura
$fechahora_actual =  strtotime ( date("Y-m-d H:i:s") ) ;
$diff =  $proximo_dia_apertura - $fechahora_actual;
$dias = (int)($diff/(60*60*24));
if($dias<7): // esta en la misma semana

endif;
exit;

$fecha_actual = date("Y-m-d H:i:s");
echo "<br>MIN " .$minutos = (strtotime($proximo_dia_apertura)-strtotime($fecha_actual))/60;
echo "<br>DIAS " . $dias = (int)($minutos/1440);

exit;

 $DiaApertura = 5;
 switch($DiaApertura):
 	case "1":
		$dia_semana =  "Monday";
	break;
	case "2":
		$dia_semana =  "Tuesday";
	break;
	case "3":
		$dia_semana =  "Wednesday";
	break;
	case "4":
		$dia_semana =  "Thursday";
	break;
	case "5":
		$dia_semana =  "Friday";
	break;
	case "6":
		$dia_semana =  "Saturday";
	break;
	case "7":
		$dia_semana =  "Sunday";
	break;
 endswitch;

  echo $proximo_dia_apertura=date ("Y-m-d",strtotime("next ".$dia_semana)) . " 08:00:00";
  //calculo cuantos minutos falta para la hora de apertura
  $fecha_actual = date("Y-m-d H:i:s");

  $minutos = (strtotime($proximo_dia_apertura)-strtotime($fecha_actual))/60;
 echo "<br>Falta" . $minutos = abs($minutos);
 echo "<br>Falta" . $minutos = floor($minutos);




  exit;



/*
$connectionInfo = array( "Database"=> "Factura", "UID"=>"22cero2", "PWD"=>"advanced.2016");
$conexion = sqlsrv_connect( "190.146.236.43,2220", $connectionInfo) or
die( "<h2>No se puede conectar a 190.146.236.43 como 22cero2</h2><p><b>Error</b>: " .print_r(sqlsrv_errors()));
echo "ok";
*/

/*
$enlace =  mysql_connect('190.146.236.43:2220', '22cero2', 'advanced.2016');
if (!$enlace) {
    die('No pudo conectarse: ' . mysql_error());
}
echo 'Conectado satisfactoriamente';
mysql_close($enlace);
*/
?>


<?php





/*
//Validacion Especial Medellin entre las 10pm y 5:30am no se puede reservar tenis medellin
$fecha_hora_actual = date("Y-m-d H:i:s");
$fecha_inicio_nopermitido = date("Y-m-d 22:00:00");
$fecha_fin_nopermitido = strtotime ( '+450 minute' , strtotime ( $fecha_inicio_nopermitido ) ) ;

echo date("Y-m-d H:i:s",$fecha_fin_nopermitido);
exit;

if(strtotime($fecha_hora_actual)>=strtotime($fecha_inicio_nopermitido) &&  strtotime($fecha_hora_actual)<=$fecha_fin_nopermitido):
	echo "No permite reservar";
	//Calculo tiempo restante para poder reservar
	$fecha_final = $fecha_fin_nopermitido;
	$fecha_actual = date("Y-m-d H:i:s");
	//$diff = strtotime($fechahora_actual) - strtotime($hora_inicio_reserva);





	$diff =  $fecha_fin_nopermitido - strtotime($fecha_hora_actual);

	echo date("Y-m-d H:i:s",$fecha_fin_nopermitido) ."-".$fecha_hora_actual;

	$dias = $diff/(60*60*24);
	$horas = ($dias-intval($dias))*24;
	$min = ($horas-intval($horas))*60;
	$seg = ($min-intval($min))*60;

	echo "Dia " . intval($dias) . " Hora " . intval($horas) . "mint " . intval($min) . " seg " . intval($seg);
	exit;

endif;
exit;
*/



//$respuesta = SIMWebService::set_reserva_generalV2(SIMUser::get("club"),$frm["IDSocio"],$frm["idelemento"],$frm["ids"],$frm["fecha"],$frm["hora"],"",$array_invitados,$frm["Observaciones"],"Admin",$frm["tee"],"","","","",$frm["IDTipoModalidadEsqui"],$frm["IDAuxiliar"],$frm["IDTipoReserva"],"","","","",SIMUser::get( "IDUsuario" ),$frm["CantidadInvitadoSalon"],"");

//$result_reserva = SIMWebServiceApp::set_reserva_generalV2_test("20","66756","1238","774","2018-03-23","17:30:00","","","Admin","S", "","1192", "","","","","","413","","","","","1718","","");






/*
//Repetidos Medellin
$select_soc="SELECT count(Accion), NumeroDocumento, IDSocio, Accion FROM `Socio` WHERE IDClub = 20 Group by Accion Having count(Accion) > 1";
$result_soc = $dbo->query($select_soc);
while($row_soc = $dbo->fetchArray($result_soc)):
	echo "<br><br>Esta accion esta repetida " . $row_soc["Accion"];
	$sql_acc = "Select * From Socio Where IDClub = 20 and Accion = '".$row_soc["Accion"]."'";
	$result_acc = $dbo->query($sql_acc);
	$borrado_accion="";
	while($row_acc = $dbo->fetchArray($result_acc)):
		//Verifico si tiene reserva
		$sql_reserva = "Select * From ReservaGeneral Where IDSocio = '".$row_acc["IDSocio"]."' Limit 1";
		$result_reserva=$dbo->query($sql_reserva);
		$row_reserva = $dbo->fetchArray($result_reserva);
		if(!empty($row_acc["Token"])):
			echo "<br>De la accion " . $row_soc["Accion"] . " no se puede borrar ingreso sistema ". $row_acc["IDSocio"];
		elseif(!empty($row_reserva["IDReservaGeneral"])):
			echo "<br>De la accion " . $row_soc["Accion"] . " no se puede borrar tiene reserva ". $row_acc["IDSocio"];
		elseif($borrado_accion==""):
			echo "<br>" . $borra_reg="Delete From Socio Where IDClub = '20' and IDSocio = '".$row_acc["IDSocio"]."'";
			//$dbo->query($borra_reg);
			$borrado_accion="S";
		else:
			echo "<br>De la accion " . $row_soc["Accion"] . " queda ". $row_acc["IDSocio"];
			//echo "<br>De la accion " . $row_soc["Accion"] . " si se puede borrar ". $row_acc["IDSocio"];
		endif;
	endwhile;
endwhile;
exit;
*/


/*
$IDClub = 37;
$sql_aut = "Select * From SocioAutorizacion Where IDClub = '".$IDClub."'";
$result_aut = $dbo->query($sql_aut);
while($row_aut = $dbo->fetchArray($result_aut)):
	$sql_soc = "Select * From Socio Where Accion = '".$row_aut["IDSocio"]."' and IDClub = '".$IDClub."' limit 1";
	$resul_soc = $dbo->query($sql_soc);
	$row_soc = $dbo->fetchArray($resul_soc);
	if((int)$row_soc["IDSocio"]>0):
		echo "<br>" . $update_aut = "Update SocioAutorizacion set IDSocio = '".$row_soc["IDSocio"]."' Where IDSocioAutorizacion = '".$row_aut["IDSocioAutorizacion"]."'";
		//$dbo->query($update_aut);
	else:
		echo "<br>Atencion el socio con accion ".$row_aut["IDSocio"]." no existe";
	endif;
endwhile;
exit;
*/


//Informe vehiculos MY
$IDClub = 9;
/*
$sql_aut = "Select * From LogAcceso Where IDClub = '".$IDClub."' and Tipo = 'Contratista' and Mecanismo like '%Vehiculo%' and FechaIngreso >= '2018-01-01 00:00:00' and FechaIngreso <= '2018-07-31 23:59:59' Group by IDInvitacion";
$result_aut = $dbo->query($sql_aut);
while($row_aut = $dbo->fetchArray($result_aut)):
	$id_invitado = $dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$row_aut["IDInvitacion"]."'" );
	$id_tipo_vehiculo = $dbo->getFields( "Vehiculo" , "IDTipoVehiculo" , "IDInvitado = '".$id_invitado."'" );
	$tipo_vehiculo = $dbo->getFields( "TipoVehiculo" , "Nombre" , "IDTipoVehiculo = '".$id_tipo_vehiculo."'" );
	$tipo_licencia = $dbo->getFields( "LicenciaInvitado" , "Categoria" , "IDInvitado = '".$id_invitado."'" );
	$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $id_invitado . "' ", "array" );
	$tipo_invitado = $dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$datos_invitado["IDTipoInvitado"]."'" );
	echo "<br>" . $datos_invitado["NumeroDocumento"] . "|" . $datos_invitado["Nombre"] ." " . $datos_invitado["Apellido"] . "|" . $tipo_invitado . "|".$row_aut["Mecanismo"] . "|".$tipo_vehiculo."|".$tipo_licencia;
	endwhile;
*/

/*
$sql_aut = "Select * From LogAcceso Where IDClub = '".$IDClub."' and Tipo = 'Contratista' and Mecanismo like '%Vehiculo%' and FechaIngreso >= '2018-01-01 00:00:00' and FechaIngreso <= '2018-07-31 23:59:59' and Entrada = 'S'";
$result_aut = $dbo->query($sql_aut);
while($row_aut = $dbo->fetchArray($result_aut)):
	$mes = (int)substr($row_aut["FechaIngreso"],5,2);
	$ingreso_mes[$mes]++;
endwhile;
foreach($ingreso_mes as $identificador => $total):
	echo "<br>Mes " .$identificador . " "  .$total;
endforeach;
exit;
*/

//$resultado = SIMWebServiceApp::set_calificacion_pqr("8","5533", "1149", "gracias", "5");
//$resultado = SIMWebServiceApp::valida_pago_reserva("28","126765","995455");
//print_r($resultado);
//$resultado = SIMUtil::noticar_nuevo_pqr("1377");
//echo "Fin Envio";
//exit;



//Cargar Secciones a todos los socios
/*
$IDClub = 23;
$sql_soc = "SELECT * FROM Socio WHERE IDClub = '".$IDClub."' ";
$result_soc = $dbo->query($sql_soc);
while($row_soc = $dbo->fetchArray($result_soc)):
	$sql_secc_club = "Select * From SeccionGaleria Where IDClub = '".$IDClub."'";
	$result_secc_club = $dbo->query($sql_secc_club);
	while($row_secc = $dbo->fetchArray($result_secc_club)):
		//Verifico si ya el socio la tiene si no se la creo
		$sql_soci_secc = "Select * From SocioSeccionGaleria Where IDSeccionGaleria = '".$row_secc["IDSeccionGaleria"]."' and IDSocio = '".$row_soc["IDSocio"]."'";
		$result_soci_secc = $dbo->query($sql_soci_secc);
		if($dbo->rows($result_soci_secc)<=0):
			echo "<br>" . $insert_secc = "Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('".$row_soc["IDSocio"]."','".$row_secc["IDSeccionGaleria"]."')";
			//$dbo->query($insert_secc);
			exit;
		endif;
	endwhile;
endwhile;
exit;
*/




//SIMWebService::set_token("7","23","Android","aaaaaabbbbc");
//Arreglo token
/*
$sql_soc = "SELECT * FROM Socio WHERE Dispositivo = 'IOS' and Token like '%>' ";
$result_soc = $dbo->query($sql_soc);
while($row_soc = $dbo->fetchArray($result_soc)):
	$Token = str_replace(">","",$row_soc[Token]);
	$sql_actualiza = "Update Socio set Token = '".$Token."' Where IDSocio = '".$row_soc[IDSocio]."'";
	$dbo->query($sql_actualiza);
endwhile;
*/

//SIMUtil::notifica_recibo_domicilio(336);

/*
//$sql_inv = "Select * From Invitado Where NumeroDocumento like '%Â%'";
$sql_inv = "Select * From Invitado Where NumeroDocumento not like '%MenorEdad%'";
$result_inv = $dbo->query($sql_inv);
while($row_inv = $dbo->fetchArray($result_inv)):

	$NumeroDocumento = $row_inv["NumeroDocumento"];

	if(!is_numeric($NumeroDocumento)):
		$NumeroDocumento = str_replace(".","",$NumeroDocumento);
		$NumeroDocumento = str_replace(",","",$NumeroDocumento);
		$NumeroDocumento = str_replace(" ","", $NumeroDocumento);
		$NumeroDocumento = str_replace("-","", $NumeroDocumento);
			echo "<br>NumeroDoc " . $row_inv["NumeroDocumento"];
			echo "<br>" . $actualiza_doc = "Update Invitado Set NumeroDocumento = '".$NumeroDocumento."' Where IDInvitado = '".$row_inv["IDInvitado"]."'";
			//$dbo->query($actualiza_doc);
	endif;
endwhile;
exit;
*/


/*
	$server = '181.48.188.77:1433';
	// Connect to MSSQL CASMPRESTRE MEDELLIN
	$link = mssql_connect($server, 'applagartos', 'L4g4rt0$.Admin');
	if (!$link) {
		//die('Algo fue mal mientras se conectaba a MSSQL.');
	 		//mssql_get_last_message();
			die('MSSQL error:: ' . mssql_get_last_message());
			var_dump($link);
	}

*/


//Calcular tarif

/*
$respuesta = SIMUtil::push_notifica_codigo_pago("793132");
print_r($respuesta);
exit;
*/


//$respuesta = SIMWebService::set_invitado("7","1175","1020840337","Juanita moreno","2018-07-27");
//print_r($respuesta);
//exit;

/*
//Imagenes horizontales a vertical
$sql_foto="Select * From Socio Where IDClub = 34 and Foto <> ''";
$result_foto=$dbo->query($sql_foto);
while($row_foto = $dbo->fetchArray($result_foto)):
	$ruta_foto = SOCIO_ROOT . $row_foto["Foto"];
	echo "<img src='".$ruta_foto."' width='50' height='50'><br>";
	echo "<br>".$row_foto["IDSocio"]."<br>";
endwhile;
*/


/*
//Imagen inicial horizontal
$image = 'ciudad.jpg';
//Destino de la nueva imagen vertical
$image_rotate = 'ciudad_rotate.jpg';

//Definimos los grados de rotacion
$degrees = 90;

//Creamos una nueva imagen a partir del fichero inicial
$source = imagecreatefromjpeg($image);

//Rotamos la imagen 90 grados
$rotate = imagerotate($source, $degrees, 0);

//Creamos el archivo jpg vertical
imagejpeg($rotate, $image_rotate);

*/



/*
$sql_club_tipoarch ="Select * From ClubTipoArchivo Where IDClub <> 8";
$result_club_tipoarch = $dbo->query($sql_club_tipoarch);
while($r = $dbo->fetchArray($result_club_tipoarch)):
	if(!empty($r["NombreTipoArchivo"])):
		$nombre_archivo = $r["NombreTipoArchivo"];
	else:
		$nombre_archivo = $dbo->getFields( "TipoArchivo" , "Nombre" , "IDTipoArchivo = '".$r["IDTipoArchivo"]."'" );
	endif;

	if(!empty($r["Icono"])):
		$icono_archivo = $r["Icono"];
	else:
		$icono_archivo = $dbo->getFields( "TipoArchivo" , "Icono" , "IDTipoArchivo = '".$r["IDTipoArchivo"]."'" );
	endif;

	$tipo_original = $dbo->getFields( "TipoArchivo" , "Nombre" , "IDTipoArchivo = '".$r["IDTipoArchivo"]."'" );
	if($tipo_original=="Estatutos"):
		$tipo = "DescargaDirecta";
	elseif($tipo_original=="Reglamento")	:
		$tipo = "Icono";
	elseif($tipo_original=="Resoluciones"):
		$tipo = "Lista";
	endif;


	echo "<br>" . $sql_tipo_arch =  "insert into TipoArchivo (IDClub,Nombre,Tipo, Icono, Publicar )Values ('".$r["IDClub"]."','".$nombre_archivo."','".$tipo."','".$icono_archivo."','".$r["Activo"]."')";
	$dbo->query($sql_tipo_arch);
	$ultimo_id = $dbo->lastID();
	//Actualizo los documentos
	echo "<br>" . $sql_docs = "Update Documento set IDTipoArchivo = '".$ultimo_id."' Where IDTipoArchivo = '".$r["IDTipoArchivo"]."' and IDClub = '".$r["IDClub"]."' ";
	$dbo->query($sql_docs);
endwhile;
*/




/*
$sql_soc = "select COUNT(IDSocio), Socio.* from Socio Where IDClub = 34 group by NumeroDocumento HAVING  COUNT(IDSocio) >1";
$result_soc = $dbo->query($sql_soc);
while($row_soc = $dbo->fetchArray($result_soc)):
	$sql_soc_rep = "select * from Socio Where IDClub = 34 and NumeroDocumento = '".$row_soc["NumeroDocumento"]."'";
	$result_soc_rep = $dbo->query($sql_soc_rep);
	while($row_soc_rep = $dbo->fetchArray($result_soc_rep)):
		$sql_reserva = "Select * From ReservaGeneral Where IDClub = 34 and IDSocio = '".$row_soc["IDSocio"]."'";
		$result_reserva = $dbo->query($sql_reserva);
		$row_reserva = $dbo->rows($result_reserva);
		if(empty($row_reserva)<=0)
			echo "<br>Sin reservas " . $row_soc["IDSocio"] . " " . $row_soc["NumeroDocumento"];
		else
			echo "<br>Con reservas " . $row_soc["IDSocio"] . " " . $row_soc["NumeroDocumento"];;

	endwhile;

endwhile;
exit;
*/




/*
$sql_reserva = "Select * From ReservaGeneral Where IDClub = 34 and Fecha >= '2018-03-21'";
$result_reserva = $dbo->query($sql_reserva);
while($row_reserva = $dbo->fetchArray($result_reserva)):
	$sql_soc = "Select * From Socio Where IDClub = 34 and IDSocio = '".$row_reserva["IDSocio"]."'";
	$result_soc = $dbo->query($sql_soc);
	$row_soc = $dbo->fetchArray($result_soc);
	if(empty($row_soc["IDSocio"]))
		echo "<br>Sin socio " . $row_reserva["IDSocio"] . " " . $row_reserva["FechaTrCr"];

endwhile;

exit;
*/


//Envio Lista de espera
//$resultado = SIMUtil::push_notifica_libera_reserva("8","659287");
//exit;


//$resultado = SIMUtil::noticar_nuevo_pqr("1071");

/*
$sql_soc = "Select * From Socio Where IDClub = 26 ";
$result_soc = $dbo->query($sql_soc);
while($row_soc = $dbo->fetchArray($result_soc)):
	$nombre= ucwords(strtolower($row_soc["Nombre"]));
	$apellido= ucwords(strtolower($row_soc["Apellido"]));
	$update_campo = "Update Socio set Nombre = '".$nombre."', Apellido = '".$apellido."' Where IDSocio = '".$row_soc["IDSocio"]."' ";
	$dbo->query($update_campo);
endwhile;
echo "Terminado";
exit;
*/

//$resultadosms = SIMWebServiceSMS::enviar_sms("3102349993","Su invitado Pedro Perez ha ingresado a las instalaciones por la porteria: Porteria Principal");
//$resultadosms = SIMWebServiceSMS::enviar_sms("3203495740","Su invitado Pedro Perez ha ingresado a las instalaciones por la porteria: Porteria Principal");
//print_r($resultadosms);
//exit;


/*
$insert_reserva = "Insert Into ReservaGeneral (IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, Fecha, Hora, Tee, NumeroInscripcion)
				  Values ('1','5533','141','1078','1','2018-02-28','11:00:00','','0')";
$result = $dbo->query($insert_reserva);
if (!$result)
    echo "Error al guardar";
else
	echo "Guardado";


echo "<br>Terminado";
exit;
*/
/*
//Actualizar como llave reserva general
$sql_llave = "SELECT count(IDReservaGeneral),ReservaGeneral.* FROM `ReservaGeneral` WHERE 1 Group by IDClub,IDEstadoReserva,Fecha,Hora,IDServicio,IDServicioElemento,Tee,NumeroInscripcion Having count(IDReservaGeneral) >1";
$result_llave = $dbo->query($sql_llave);
while($row_llave = $dbo->fetchArray($result_llave)):
	$sql_repetido = "SELECT * FROM `ReservaGeneral` WHERE IDClub= '".$row_llave["IDClub"]."' and IDEstadoReserva= '".$row_llave["IDEstadoReserva"]."'and Fecha= '".$row_llave["Fecha"]."' and Hora= '".$row_llave["Hora"]."'
				 and IDServicio= '".$row_llave["IDServicio"]."' and IDServicioElemento= '".$row_llave["IDServicioElemento"]."' and Tee= '".$row_llave["Tee"]."' and NumeroInscripcion= '".$row_llave["NumeroInscripcion"]."'";
	$result_repetido = $dbo->query($sql_repetido);
	$consecutivo=1;
	while($row_repetido = $dbo->fetchArray($result_repetido)):
		$row_repetido["IDReservaGeneral"];
		$update_repetido = "Update ReservaGeneral Set NumeroInscripcion = '".$consecutivo."' Where IDReservaGeneral = '".$row_repetido["IDReservaGeneral"]."'";
		$dbo->query($update_repetido);
		$consecutivo++;
	endwhile;

endwhile;
exit;
*/

//SIMUtil::notificar_elimina_reserva(595293);
//echo "Enviado";
//exit;

//$respuesta = SIMWebServiceApp::get_disponiblidad_elemento_servicio_v2("8","178","2018-02-07",$IDElemento,$Admin,$UnElemento,$NumeroTurnos,$IDTipoReserva, $Agenda);
//$respuesta = SIMWebServiceApp::verifica_club_servicio_abierto_v2("8","2018-02-07","177","281","13:00:00");

//echo "<br>Fin";
//print_r($respuesta);


/*
//Si es admin valido si el auxiliar boleador esta disponible de nuevo
if(!empty($Admin)):
	$flag_aux_disp=0;
	$response_dispo_aux = SIMWebService::get_auxiliares("1","14","2018-02-03","10:00:00");
	$response_dispo_aux["success"];
	if($response_dispo_aux["success"]==0):
		$flag_aux_disp=1;
	else:
		$flag_aux_disp=1;
		foreach($response_dispo_aux["response"] as $datos_conf_aux):
			foreach($datos_conf_aux["Auxiliares"] as $datos_aux):
				if($IDAuxiliar==$datos_aux["IDAuxiliar"]):
					$flag_aux_disp=0;
				endif;
			endforeach;

		endforeach;
	endif;

	if($flag_aux_disp==1):
		$respuesta["message"] = "Lo sentimos, el auxiliar no esta disponible en ese horario";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
		return $respuesta;
	endif;
endif;

//print_r($response_dispo_aux);
exit;
*/

/*
$grupo_familiar = SIMWebService::get_beneficiarios("8","5533");
if(count($grupo_familiar["response"]["Beneficiarios"])>0):
	foreach($grupo_familiar["response"]["Beneficiarios"] as $datos_nucleo):
		if($datos_nucleo["TipoBeneficiario"]=="Socio"):
			$array_id_benef[]= $datos_nucleo["IDBeneficiario"];
		endif;
	endforeach;
endif;
if(count($array_id_benef)>0):
	$condicion_benef = " and IDSocio in (".implode(",",$array_id_benef).") ";
endif;
echo $sql_eliminada="Select * From ReservaGeneralEliminada Where IDSocio = '5533' and Fecha = '".$Fecha."' and IDServicio = '".$IDServicio."' and IDEstadoReserva = '1' " . $condicion_benef;
exit;
*/


/*

//Quitar invitados duplicados
$sql_invitado = "SELECT count(IDInvitado),Invitado.* FROM `Invitado` WHERE IDClub = 9 and NumeroDocumento >10000 Group by NumeroDocumento having count(IDInvitado)>1";
$result_invitado = $dbo->query($sql_invitado);
while($row_invitado = $dbo->fetchArray($result_invitado)):
	$sql_datos_invitado = "Select * From Invitado Where NumeroDocumento ='".$row_invitado["NumeroDocumento"]."' order by IDInvitado";
	$result_datos_invitado = $dbo->query($sql_datos_invitado);
	$contador=0;
	while($row_datos_invitado = $dbo->fetchArray($result_datos_invitado)):
		if($contador==0)	:
			$id_principal = $row_datos_invitado["IDInvitado"];
		else:
			$sql_autorizacion = "Update SocioAutorizacion Set IDInvitado = '".$id_principal."' Where IDClub = 9 and IDSocioAutorizacion ='".$row_datos_invitado["IDInvitado"]."'";
			$dbo->query($sql_autorizacion );
			$sql_autorizacion = "Update SocioInvitadoEspecial Set IDInvitado = '".$id_principal."' Where IDClub = 9 and IDSocioInvitadoEspecial ='".$row_datos_invitado["IDInvitado"]."'";
			$dbo->query($sql_autorizacion );
			$sql_autorizacion = "Update Vehiculo Set IDInvitado = '".$id_principal."' Where IDInvitado ='".$row_datos_invitado["IDInvitado"]."'";
			$dbo->query($sql_autorizacion );
			$sql_autorizacion = "Update LicenciaInvitado Set IDInvitado = '".$id_principal."' Where IDInvitado ='".$row_datos_invitado["IDInvitado"]."'";
			$dbo->query($sql_autorizacion );
			$sql_autorizacion = "Update ObservacionInvitado Set IDInvitado = '".$id_principal."' Where IDInvitado ='".$row_datos_invitado["IDInvitado"]."'";
			$dbo->query($sql_autorizacion );
			//Borro el duplicado
			$delete_duplicado = "Delete From Invitado Where IDClub = 9 and IDInvitado = '".$row_datos_invitado["IDInvitado"]."'";
			$dbo->query($delete_duplicado );
		endif;
		$contador++;
	endwhile;
endwhile;
echo "Terminado";
exit;

*/


/*
//Pasar log legible
//$cadena_buscar= '"IDClub":"1"';
$cadena_buscar= '"IDSocio":"3491"';
//$cadena_buscar= '"IDSocio":"37672"';
$sql_log="Select * From LogServicio Where Parametros like '%".$cadena_buscar."%'";
//$sql_log="Select * From LogServicio Where 1";
$result_log = $dbo->query($sql_log);
while($row_log = $dbo->fetchArray($result_log)):
	//Quito las comillas que dañan el json
	if(strpos($row_log["Parametros"],'IDAuxiliar')):
		$pos1 = strpos($row_log["Parametros"],'[{"');
		$pos2 = strpos($row_log["Parametros"],'"}]');
		$cantidad_caracter= ((int)$pos2 - (int)$pos1)+3;
		$string_reemplazar=substr($row_log["Parametros"],$pos1,$cantidad_caracter);
		$row_log["Parametros"] = str_replace($string_reemplazar,'',$row_log["Parametros"]);
	endif;

	$array_parametros = json_decode($row_log["Parametros"]);
	$array_respuesta = json_decode($row_log["Respuesta"]);

	$FechaHoraReserva = $array_parametros->Fecha . " " . $array_parametros->Hora;
	//inserto en la tabla de Log para reportes
	$sql_logr = "Insert into LogReporte (Accion,IDSocio, IDClub, IDServicio, Mensaje,FechaReserva,Fecha) Values ('".$array_parametros->action."','".$array_parametros->IDSocio."','".$array_parametros->IDClub."','".$array_parametros->IDServicio."','".$array_respuesta->message."','".$FechaHoraReserva."','".$row_log["FechaPeticion"]."')";
	$dbo->query($sql_logr);
endwhile;
echo "Terminado";
exit;
*/


//$resultado = SIMWebService::buscar_elemento_disponible("16","341","2017-12-02","12:15:00","681");
//exit;


//$resultado = SIMWebService::buscar_elemento_disponible($IDClub,$IDServicio,$Fecha,$Hora,$IDElementoPadre="",$IDTipoReserva="");
//$resultado = SIMWebService::buscar_elemento_disponible("1","45","2017-11-23","07:00:00","904","");
//print_r($resultado);

/*
$resultado = SIMWebService::get_disponiblidad_elemento_servicio("8","615","2017-12-01","","","","",76, "");
print_r($resultado);
exit;
*/



/*
SIMUtil::notificar_nueva_reserva("417754","184");
exit;
*/


/*
$resultado = SIMWebService::get_reservas_servicio('25', "1392" ,"2017-11-03", "788", $idsocio );
print_r($resultado);
exit;
*/

/*
//Socio Nuevos en el mes
$sql_reserva = "Select * From ReservaGeneral Where IDClub = 11 and  FechaTrCr >= '2017-10-01 00:00:00' and FechaTrCr <= '2017-10-31 23:59:59' and UsuarioTrCr = 'Socio'";
$result_reserva = $dbo->query($sql_reserva);
$total_socio_mes=0;
while($row_reserva = $dbo->fetchArray($result_reserva)):
	//verifico si el socio ya habia realizado reservas antes
	$sql_reserva_ant = "Select * From ReservaGeneral Where IDClub = 11 and IDSocio = '".$row_reserva["IDSocio"]."' and FechaTrCr <= '2017-09-30 23:59:59' and UsuarioTrCr = 'Socio'";
	$result_reserva_ant = $dbo->query($sql_reserva_ant);
	$total_reservas = (int)$dbo->rows($result_reserva_ant);
	if($total_reservas<=0):
		echo "<br>Socio no habia reservado " . $row_reserva["IDSocio"];
		$total_socio_mes++;
	endif;
endwhile;

echo "<br><br>Total Socios nuevos: " .$total_socio_mes;
exit;
*/



/*
//Actualiza Claves gun
$sql_socio_gun="Select * From Socio Where IDClub = 25 and IDSocio >=84888 and Dispositivo = ''";
$result_socio_gun=$dbo->query($sql_socio_gun);
while($row_socio_gun = $dbo->fetchArray($result_socio_gun)):
	//Si el primero caracter es 0 lo omito
	/*
	$primer_caracter=substr($row_socio_gun["Accion"],0,1);
	if($primer_caracter==0):
		$clave_socio = substr($row_socio_gun["Accion"],1);
	else:
		$clave_socio = $row_socio_gun["Accion"];
	endif;
	*/
	/*
	$clave_socio = substr($row_socio_gun["Accion"],1);
	//Actualizao clave
	echo "<br>" . $update_socio_gun = "Update Socio set Clave = sha1('".$clave_socio."') Where IDClub = 25 and IDSocio = '".$row_socio_gun["IDSocio"]."'";
	//$dbo->query($update_socio_gun);
endwhile;

exit;
*/




/*
$sql_invitado = "Select * From Invitado Where IDClub = 9 and FechaTrCr >= '2017-08-11' Order by FechaTrCr Desc Limit 500 ";
$result_invitado = $dbo->query($sql_invitado);
while($row_invitado = $dbo->fetchArray($result_invitado)):
	$sql_invitacion = "Select * From SocioInvitadoEspecial Where IDInvitado = '".$row_invitado["IDInvitado"]."'";
	$result_invitacion = $dbo->query($sql_invitacion);
	$total_invitacion_especial = $dbo->rows($result_invitacion);

	$sql_invitacion = "Select * From SocioAutorizacion Where IDInvitado = '".$row_invitado["IDInvitado"]."'";
	$result_invitacion = $dbo->query($sql_invitacion);
	$total_invitacion_autorizacion = $dbo->rows($result_invitacion);

	if((int)$total_invitacion_especial==0 && (int)$total_invitacion_autorizacion==0):
		echo "<br>El invitado no tiene invitaciones " . $row_invitado["NumeroDocumento"];
	endif;

endwhile;

echo "<br>Terminado";
*/

/*
$array_datos_token = SIMWebServiceZeus::obtener_token();
if(!empty($array_datos_token["SessionIDTokenResult"]["SessionID"]) && $array_datos_token["SessionIDTokenResult"]["Status"]=="SUCCESS" ):
	$result_envia_invitacion=SIMWebServiceZeus::envia_invitacion($array_datos_token["SessionIDTokenResult"]["SessionID"],134,"90909080901","PruebaApp","2017-06-29");
endif;
exit;
*/

/*
//copiar socios faltantes
$conexion2=mysql_connect("localhost","desarrollo",'+kEd7S!4K]"Kt^="') or die("Problemas en la conexion2");
mysql_select_db("desarrollo",$conexion2) or die("Problemas en la seleccion de la base de datos");

$sql_socio = "Select * From Socio Where 1";
$result_socio = mysql_query($sql_socio);
while($row_socio = mysql_fetch_array($result_socio)):
	$sql_socio_real = "Select * From Socio Where IDSocio = '".$row_socio["IDSocio"]."'";
	$result_socio_real = $dbo->query($sql_socio_real);
	if($dbo->rows($result_socio_real)<=0):
		echo "<br>crear el". $row_socio["IDSocio"];
		echo "<br>" . $sql_inserta="insert into Socio (IDSocio, IDClub,IDCategoria, IDParentesco, IDEstadoSocio, Accion, AccionPadre, Parentesco, NumeroDerecho, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, Email, Clave, CorreoElectronico,
		Telefono, Celular, NombreBeneficiario, CodigoBarras, CodigoQR, Dispositivo, Token, Foto, FotoActualizadaSocio, FechaActualizacionFoto,ObservacionGeneral, ObservacionEspecial,FechaTrCr,UsuarioTrCr,FechaTrEd,UsuarioTrEd, Password,TipoSocio,FechaInicioCortesia,FechaFinCortesia, FechaInicioCanje,FechaFinCanje,ClubCanje,FechaInicioInvitado,
		FechaFinInvitado, Categoria, NumeroInvitados, NumeroAccesos, PermiteReservar, Predio, CambioClave )
		Values(

		'$row_socio[IDSocio]',
		'$row_socio[IDClub]',
		'$row_socio[IDCategoria]',
		'$row_socio[IDParentesco]',
		'$row_socio[IDEstadoSocio]',
		'$row_socio[Accion]',
		'$row_socio[AccionPadre]',
		'$row_socio[Parentesco]',
		'$row_socio[NumeroDerecho]',
		'$row_socio[Genero]',
		'$row_socio[Nombre]',
		'$row_socio[Apellido]',
		'$row_socio[FechaNacimiento]',
		'$row_socio[NumeroDocumento]',
		'$row_socio[Email]',
		'$row_socio[Clave]',
		'$row_socio[CorreoElectronico]',
		'$row_socio[Telefono]',
		'$row_socio[Celular]',
		'$row_socio[NombreBeneficiario]',
		'$row_socio[CodigoBarras]',
		'$row_socio[CodigoQR]',
		'$row_socio[Dispositivo]',
		'$row_socio[Token]',
		'$row_socio[Foto]',
		'$row_socio[FotoActualizadaSocio]',
		'$row_socio[FechaActualizacionFoto]',
		'$row_socio[ObservacionGeneral]',
		'$row_socio[ObservacionEspecial]',
		'$row_socio[FechaTrCr]',
		'$row_socio[UsuarioTrCr]',
		'$row_socio[FechaTrEd]',
		'$row_socio[UsuarioTrEd]',
		'$row_socio[Password]',
		'$row_socio[TipoSocio]',
		'$row_socio[FechaInicioCortesia]',
		'$row_socio[FechaFinCortesia]',
		'$row_socio[FechaInicioCanje]',
		'$row_socio[FechaFinCanje]',
		'$row_socio[ClubCanje]',
		'$row_socio[FechaInicioInvitado]',
		'$row_socio[FechaFinInvitado]',
		'$row_socio[Categoria]',
		'$row_socio[NumeroInvitados]',
		'$row_socio[NumeroAccesos]',
		'$row_socio[PermiteReservar]',
		'$row_socio[Predio]',
		'$row_socio[CambioClave]'

		)";
		$dbo->query($sql_inserta);
	endif;
endwhile;



exit;
*/

/*
$clave = "miclave01";
$clave_especial =  md5($email."_".md5($llave_fija."-".$clave));
$otra_condicion_clave = " Clave = '".$clave_especial."'";
echo $sql_verifica = "SELECT * FROM im_usuarios_accesos WHERE im_usuario_key = '".$clave_especial ."' ";
$qry_verifica = $dbo->query( $sql_verifica );
if( $dbo->rows( $qry_verifica ) == 0 ):
	echo "No existe";
else:
	echo "Si existe";
endif;
exit;
*/

//$resultado = SIMWebService::set_separa_reserva("11","45115","333","180",$Tee,"2017-05-16","15:30:00","54",$NumeroTurnos="");
//print_r($resultado);
//exit;

/*
$datos = SIMWebServiceApp::get_publicidad("8","","");
print_r($datos["response"]);
exit;
*/

/*
echo date("Y-m-d H:i:s");
echo "<br>Enviando...";
SIMUtil::push_notifica_reserva("11","128685","Empleado");
echo "<br>Enviado";
exit;
*/

/*
$IDClub=11;
$IDSocio=45115;
$IDServicio=352;
$Fecha="2017-02-10";
$Hora="13:00:00";
$IDElemento="363";
$cantidad_turnos="2";


$Fecha="2017-02-18";
$Hora="09:30:00";
$IDSocio="5556";
$IDServicio="86";
$IDClub="8";
$IDBeneficiario = "";
$TipoBeneficiario = "";

$valida = SIMWebServiceTest::validar_turnos_seguidos_test($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDBeneficiario = "", $TipoBeneficiario = "" );

print_r($valida);

exit;


echo "<br>";
echo count($array_disponible)."!=".(int)($cantidad_turnos-1);

if(count($array_disponible)!=(int)($cantidad_turnos-1) && $cantidad_turnos>1):
	echo "No Disponible";
else:
	echo "Si disponible";
endif;


exit;

$IDClub=8;
$IDSocio=5519;
$IDServicio=31;
$respuesta = SIMWebServiceApp::verifica_sancion_socio($IDClub,$IDSocio, $IDServicio);
print_r($respuesta);
echo "Fin";
exit;
*/

/*
$sql_invitado = $dbo->query("Select * From Invitado Where IDClub = 9 and IDInvitado >= 909 and IDInvitado <= 1737");
while($row_invitado = $dbo->fetchArray($sql_invitado)):
	//averiguo la accion que invita
	$sql_socio = $dbo->query("Select * From Socio Where Accion = '".$row_invitado["Email3"]."' and IDClub = 9 Limit 1");
	$row_socio = $dbo->fetchArray($sql_socio);
	if(!empty($row_socio["IDSocio"])):
		$sql_crea_auto = "Insert Into SocioAutorizacion (IDClub, IDSocio, IDInvitado, TipoAutorizacion, FechaInicio, FechaFin)
					  Values ('9','".$row_socio["IDSocio"]."','".$row_invitado["IDInvitado"]."','$row_invitado','2016-12-26','2017-12-31')";
		$dbo->query($sql_crea_auto);
	else:
		echo "<br>Atencion el Socio no existe: " . 	$row_invitado["Email3"];
	endif;
endwhile;

echo "<br>Terminado";
exit;
*/


/*
echo "Inicio";
SIMUtil::push_socio_invitado("8","48225","5533");
echo "ok";
exit;
*/


/*
//COPIAR FOTOS
//Copiar fotos a carpeta principal
//A continuación le pasamos a la función el nombre de la carpeta "padre" donde queremos comenzar a leer
//listar_carpetas(CarpetaInicial);
function listar_carpetas($carpeta) {

	$dbo =& SIMDB::get();
//le añadimos la barra a la carpeta que le hemos pasado
$ruta = $carpeta . "/";

//pasamos a minúsculas (opcional)
$ruta = strtolower($ruta) ;

//comprueba si la ruta que le hemos pasado es un directorio
if(is_dir($ruta)) {
//fijamos la ruta del directorio que se va a abrir
if($dir = opendir($ruta)) {
//si el directorio se puede abrir
while(($archivo = readdir($dir)) !== false) {
//le avisamos que no lea el "." y los dos ".."
if($archivo != '.' && $archivo != '..') {

//echo "<br>".$carpeta . $archivo . " a " .IMGINVITADO_DIR . $archivo;

if(!copy($carpeta . $archivo, IMGINVITADO_DIR . $archivo)){
	echo "Error";
	exit;
}
else{
	$array_archivo = explode(".",$archivo);
	//Busco el socio cn esta documento
	//$dbo->query("Update Socio Set Foto = '".$archivo."' Where NumeroDocumento = '".$array_archivo[0]."' and IDClub = 9;");
	//Busco Invitado
	$dbo->query("Update Invitado Set FotoFile = '".$archivo."' Where NumeroDocumento = '".$array_archivo[0]."' and IDClub = 9;");
}

//comprobramos que se trata de un directorio
if (is_dir($ruta.$archivo)) {
//si efectivamente es una carpeta saltará a nuestra próxima función
leer_carpeta($ruta.$archivo);
} } }
//cerramos directorio abierto anteriormente
closedir($dir);
} } }

//recogemos  la ruta para entrar en el segundo nivel
function leer_carpeta($leercarpeta) {

	$dbo =& SIMDB::get();
//le añadimos la barra final
$leercarpeta = $leercarpeta . "/";

if(is_dir($leercarpeta)){
if($dir = opendir($leercarpeta)){
while(($archivo = readdir($dir)) !== false){
if($archivo != '.' && $archivo != '..') {
 //imprimimos el nombre del archivo, si quisieramos podriamos poner en este punto por ejemplo un enlace
//al archivo para que se abriera una imagen o un PDF al hacer click encima del nombre.
//echo "<br>".$leercarpeta . $archivo;
if(!copy($leercarpeta . $archivo, SOCIO_DIR . $archivo)){
	echo "<br>Error " . $leercarpeta . $archivo;
}
else{
	$array_archivo = explode(".",$archivo);
	//Busco el socio cn esta documento
	//$dbo->query("Update Socio Set Foto = '".$archivo."' Where NumeroDocumento = '".$array_archivo[0]."' and IDClub = 9;");
}

} }

closedir($dir);
} } }

//listar_carpetas(SOCIO_DIR."/fotosmayordomo/");
//listar_carpetas(IMGINVITADO_DIR."/fotosmayordomo/");
//echo "Terminado";
//exit;





/*
$clubes = SIMWebServiceFedegolf::get_clubes();
print_r($clubes);
exit;
*/

/*
$parametros_codigo_qr = "https://www.miclubapp.com/plataform/invitadosespeciales.php?IDInvitacion=293&Placa=MNR345";
SIMUtil::enviar_codigo_qr(293,$parametros_codigo_qr);
echo "enviado";
*/

//verificar sancion socio
/*
$result_sancion = SIMWebServiceApp::verifica_sancion_socio("8","5533","1");
if($result_sancion):
	echo "<br>SI tiene una sancion vigente";
else:
	echo "<br>NO tiene una sancion vigente";
endif;
*/


//Clave Fontanar
/*
$usuario = "79793972";
$llave_fija = "63380fcfe2bf3c3d2cb3ec089c3c521b";
$clave="miclave01";

echo md5($usuario."_".md5($llave_fija."-".$clave));

exit;
*/

//Borrar socios repetidos
/*
$sql_socio_repetido = "SELECT NumeroDocumento, count(`NumeroDocumento`) Total FROM `Socio` WHERE IDClub = 22 Group by NumeroDocumento having count(`NumeroDocumento`) >1";
$result_socio_repetido = $dbo->query($sql_socio_repetido);
while ($row_socio_repetido = $dbo->fetchArray($result_socio_repetido)):
	$total_borrar = (int)$row_socio_repetido["Total"] -1;
	$borra_repetido = "Delete From Socio Where IDClub = 22 and NumeroDocumento = '".$row_socio_repetido["NumeroDocumento"]."' limit $total_borrar";
	$dbo->query($borra_repetido);
endwhile;
exit;
*/



//Crear codigo de barras para todos
/*
$sql_socio = "Select * From Socio Where IDClub = 44 and CodigoBarras = '' ";
//$sql_socio = "Select * From Socio Where IDClub = 44";
$result_socio = $dbo->query($sql_socio);
while ($row_socio = $dbo->fetchArray($result_socio)):
	//Generar Codigo de barras
	//$parametros_codigo_barras = $row_socio[Accion]."-".$row_socio[NumeroDocumento];

	//if(!empty($row_socio["AccionPadre"])):
		//$parametros_codigo_barras = $row_socio["AccionPadre"];
	//else:
		//$parametros_codigo_barras = $row_socio["Accion"];
	//endif;
	$parametros_codigo_barras = $row_socio[NumeroDocumento];
	//$parametros_codigo_barras = $row_socio["Accion"];
	//$alto_barras=30;
	$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras($parametros_codigo_barras,$row_socio["IDSocio"],$alto_barras);
	//actualizo codigo barras
	echo "<br>update Socio set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDSocio = '".$row_socio["IDSocio"]."'";
	$update_codigo=$dbo->query("update Socio set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDSocio = '".$row_socio["IDSocio"]."'");
endwhile;

echo "Terminado Codigo Barras";
exit;
*/

/*
//Crear codigo de barras para Funcionarios Empleados Usuarios
$sql_usuario = "Select * From Usuario Where IDClub = 44 ";
$result_usuario = $dbo->query($sql_usuario);
while ($row_usuario = $dbo->fetchArray($result_usuario)):
	//Generar Codigo de barras
	//$parametros_codigo_barras = $row_socio[Accion]."-".$row_socio[NumeroDocumento];

	//if(!empty($row_socio["AccionPadre"])):
		//$parametros_codigo_barras = $row_socio["AccionPadre"];
	//else:
		//$parametros_codigo_barras = $row_socio["Accion"];
	//endif;

	$parametros_codigo_barras = $row_usuario["NumeroDocumento"];
	//$parametros_codigo_barras = $row_usuario[NumeroDocumento].";";
	$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras_empleado($parametros_codigo_barras,$row_usuario["IDUsuario"]);
	//actualizo codigo barras
	echo $sql_actualiza="update Usuario set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDUsuario = '".$row_usuario["IDUsuario"]."'";
	$update_codigo=$dbo->query($sql_actualiza);
endwhile;

echo "Terminado Codigo Barras";
exit;
*/








//SIMUtil::notificar_nueva_reserva("17302");
//SIMWebService::buscar_elemento_disponible($IDClub,$IDServicioCanchaClub,$Fecha,$horaInicial);
//SIMUtil::noticar_nuevo_pqr(105);

?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Documento sin título</title>
<meta http-equiv="refresh" content="2" />
</head>

<body>

<?php

/*
//Crear codigo de barras para Funcionarios Empleados Usuarios
$sql_usuario = "Select * From Usuario Where IDClub = 108 and CodigoBarras = '' Limit 1";
$result_usuario = $dbo->query($sql_usuario);
while ($row_usuario = $dbo->fetchArray($result_usuario)):
	//Generar Codigo de barras
	//$parametros_codigo_barras = $row_socio[Accion]."-".$row_socio[NumeroDocumento];

	//if(!empty($row_socio["AccionPadre"])):
		//$parametros_codigo_barras = $row_socio["AccionPadre"];
	//else:
		//$parametros_codigo_barras = $row_socio["Accion"];
	//endif;

	$parametros_codigo_barras = $row_usuario["NumeroDocumento"];
	//$parametros_codigo_barras = $row_usuario[NumeroDocumento].";";
	$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras_empleado($parametros_codigo_barras,$row_usuario["IDClub"]);
	//actualizo codigo barras
	echo $sql_actualiza="update Usuario set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDUsuario = '".$row_usuario["IDUsuario"]."'";
	$update_codigo=$dbo->query($sql_actualiza);
endwhile;

echo "Terminado Codigo Barras";
exit;
*/

/*
//Crear codigo QR para todos
//$sql_socio = "Select * From Socio Where IDClub = 9 and CodigoQR2='' and Dispositivo <> '' Limit 1";
$sql_socio = "Select * From Socio Where IDClub = 93 and CodigoQR='' Limit 1";
//$sql_socio = "Select * From Socio Where IDClub = 9 and IDSocio = 19056 Limit 1";

$result_socio = $dbo->query($sql_socio);
while ($row_socio = $dbo->fetchArray($result_socio)):
	//Generar Codigo de barras
	//$row_socio["CodigoQR"]=SIMUtil::generar_carne_qr($row_socio[IDSocio],$row_socio[NumeroDocumento]."\r\n");
	$row_socio["CodigoQR"]=SIMUtil::generar_carne_qr($row_socio[IDSocio],$row_socio[NumeroDocumento]);
	//$row_socio["CodigoQR2"]=SIMUtil::generar_carne_qr($row_socio[IDSocio],substr($row_socio["Accion"],0,3));
	//actualizo codigo barras
	echo "update Socio set CodigoQR = '".$row_socio["CodigoQR"]."' Where IDSocio = '".$row_socio["IDSocio"]."'";
	$update_codigo=$dbo->query("update Socio set CodigoQR = '".$row_socio["CodigoQR"]."' Where IDSocio = '".$row_socio["IDSocio"]."'");
	echo "<br>".$row_socio["IDSocio"];
endwhile;
echo "Terminado";
exit;
*/

//Funcionarios

echo $sql_socio = "Select * From Usuario Where IDClub = 108 and CodigoQR = '' Limit 1";
$result_socio = $dbo->query($sql_socio);
while ($row_socio = $dbo->fetchArray($result_socio)):
	//Generar Codigo de barras
	//$row_socio["CodigoQR"]=SIMUtil::generar_carne_qr($row_socio[IDSocio],$row_socio[NumeroDocumento]."\r\n");
	$row_socio["CodigoQR"]=SIMUtil::generar_carne_qr_empleado($row_socio[IDUsuario],$row_socio[NumeroDocumento]);
	//actualizo codigo barras
	$update_codigo=$dbo->query("UPDATE Usuario set CodigoQR = '".$row_socio["CodigoQR"]."' Where IDUsuario = '".$row_socio["IDUsuario"]."'");
	echo "<br>".$row_socio["IDSocio"];
endwhile;
echo "Terminado";
exit;



?>

<br>
Pruebas comillas
<form name="token" id="token" action="setreserva.php" method="post" enctype="multipart/form-data">
    <input name="Usuario" id="Usuario" value="elusuario">
    <input type="password" name="Clave" id="Clave" value="laclave">
    <input name="action" id="action" value="gettoken">
    <input type="submit" name="enviar" value="Enviar">
</form>

<br>
Place to pay
<form name="token" id="token" action="https://www.miclubapp.com/confirmacion_pagos_ptp.php?a=55" method="post" enctype="multipart/form-data">
    <input name="Usuario" id="Usuario" value="elusuario">
    <input type="password" name="Clave" id="Clave" value="laclave">
    <input name="action" id="action" value="gettoken">
    <input type="submit" name="enviar" value="Enviar">
</form>


<br>
Token
<form name="token" id="token" action="/services/club.php" method="post" enctype="multipart/form-data">
    <input name="Usuario" id="Usuario" value="dingoapp">
    <input type="password" name="Clave" id="Clave" value="appdingo1">
    <input name="action" id="action" value="gettoken">
    <input type="submit" name="enviar" value="Enviar">
</form>


<!--
Guardar Reserva
<form name="reserva" id="reserva" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="5533">
    <input name="IDElemento" id="IDElemento" value="">
    <input name="IDServicio" id="IDServicio" value="36">
    <input name="Fecha" id="Fecha" value="2016-04-20">
    <input name="Hora" id="Hora" value="18:00:00">
    <input name="IDClub" id="IDClub" value="8">
    <input name="action" id="action" value="setreservageneral">
    <input name="key" id="key" value="CEr0CLUB">

    <input type="submit" name="enviar" value="Enviar">


</form>
-->

Socio Colombia
<form name="reserva" id="reserva" action="http://clubcolombia.tavotrend.com/api/user_information.php" method="post" enctype="application/x-www-form-urlencoded">
	<input name="grant_type" id="grant_type" value="jwt_bearer">
    <input name="access_token" id="access_token" value="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpZCI6IjA3NzJlMjA1NWI2MzE2MDQ1ZGE4Mjk5MWE3YmVlOWY0YWVjZjlmMzAiLCJqdGkiOiIwNzcyZTIwNTViNjMxNjA0NWRhODI5OTFhN2JlZTlmNGFlY2Y5ZjMwIiwiaXNzIjoiIiwiYXVkIjoiY2x1YmNvbG9fYXBwIiwic3ViIjoicHJ1ZWJhIiwiZXhwIjoxNTM1NDc1NzU0LCJpYXQiOjE1MzU0NzIxNTQsInRva2VuX3R5cGUiOiJiZWFyZXIiLCJzY29wZSI6bnVsbH0.DI_cEHIuSQe9zHAhk70J8vvfSEAFt-RojPceyRedv-0Rtpm0dzeabLveA1wW3F76Vqb0VwZAPh8KKzwMZGWZJ5hzrx60fwdj6CLNqYrpyC-E8GM6S8YyzYJxVhzRCEXuX2jO--lV0v_g7BUbzj7J8JhFJ7bBzBYIwQwFcjiTC5BQOjrXayLMmePB_UzIjhX-38eR4TreZGClaU3lOEcUI_n2uN-81fxVZ6oRWzWS5jQL2E6_uwUW4p1qnw-sanINECWoLb5jByHvgRP68IrPcLEUlRrLN_oJMRVmUF7o6Eagw96Xbp16_cH3UDxD3BbFYY5_XkG5CCo4EzLU3m0_iw">
    <input type="submit" name="enviar" value="Enviar">
</form>

<br>
<br>

Guardar Reserva Hotel
<form name="reserva" id="reserva" action="../services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="5533">
    <input name="IDHabitacion" id="IDHabitacion" value="92">
    <input name="IDPromocion" id="IDPromocion" value="">
    <input name="IDTemporadaAlta" id="IDTemporadaAlta" value="0">
    <input name="Temporada" id="Temporada" value="Baja">
    <input name="CabezaReserva" id="CabezaReserva" value="Socio">
    <input name="Estado" id="Estado" value="pendiente">
    <input name="FechaInicio" id="FechaInicio" value="2017-12-21">
    <input name="FechaFin" id="FechaFin" value="2017-12-22">
    <input name="Ninera" id="Ninera" value="N">
    <input name="Corral" id="Corral" value="N">
    <input name="IVA" id="IVA" value="19">
    <input name="NumeroPersonas" id="NumeroPersonas" value="2">
    <input name="Adicional" id="Adicional" value="N">
    <input name="Pagado" id="Pagado" value="N">
    <input name="FechaReserva" id="FechaReserva" value="2017-12-07">
    <input name="AcompananteSocio" id="AcompananteSocio" value='[{"IDSocio":"","IDInvitado":"765","Nombre":"Alberto"}]'>
    <input name="IDClub" id="IDClub" value="8">
    <input name="action" id="action" value="setreservahotel">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>

<br>
<br>

<br><br>
Guardar Nuevo Invitado
<form name="reserva" id="reserva" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="1869">
    <input name="Documento" id="Documento" value="808090">
    <input name="Nombre" id="Nombre" value="Alberto">
    <input name="Apellido" id="Apellido" value="Sanchez">
    <input name="Email" id="Email" value="alberto@gmail.com">
     <input name="IDClub" id="IDClub" value="27">
    <input name="action" id="action" value="setinvitadoshotel">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>
<br><br>


Crear Propietario
<form name="reserva" id="reserva" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="Nombre" id="Nombre" value="Lucia">
    <input name="Apellido" id="Apellido" value="Rueda R.">
    <input name="NumeroDocumento" id="NumeroDocumento" value="530506">
    <input name="CorreoElectronico" id="CorreoElectronico" value="lucyruro@yahoo.com">
    <input name="Telefono" id="Telefono" value="2671467">
    <input name="Celular" id="Celular" value="3118868003">
    <input name="Portal" id="Portal" value="ACACIA">
    <input name="Casa" id="Casa" value="casaquinta 1">
    <input name="Llave" id="Llave" value="aaa123">
    <input name="AccionRegistro" id="AccionRegistro" value="insert">
    <input name="action" id="action" value="setpropietario">
    <input name="key" id="key" value="CEr0CLUB">
   	<input name="IDClub" id="IDClub" value="18">
    <input type="submit" name="enviar" value="Enviar">
</form>

<br>
<br>
Guardar Socio
<form name="reserva" id="reserva" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="Accion" id="Accion" value="53050">
    <input name="AccionPadre" id="AccionPadre" value="80160">
    <input name="Parentesco" id="Parentesco" value="ESPOSO">
    <input name="Genero" id="Genero" value="F">
    <input name="Nombre" id="Nombre" value="Lucia">
    <input name="Apellido" id="Apellido" value="Rueda">
    <input name="FechaNacimiento" id="FechaNacimiento" value="1985-10-01">
    <input name="NumeroDocumento" id="NumeroDocumento" value="53050608">
    <input name="CorreoElectronico" id="CorreoElectronico" value="lucyruro@yahoo.com">
    <input name="Telefono" id="Telefono" value="2671467">
    <input name="Celular" id="Celular" value="3118868003">
    <input name="Direccion" id="Direccion" value="Cr 119">
    <input name="TipoSocio" id="TipoSocio" value="Socio">
    <input name="EstadoSocio" id="EstadoSocio" value="A">
    <input name="InvitacionesPermitidasMes" id="InvitacionesPermitidasMes" value="20">
    <input name="action" id="action" value="setsocio">
    <input name="key" id="key" value="CEr0CLUB">
    	<input name="IDClub" id="IDClub" value="29">
    <input type="submit" name="enviar" value="Enviar">
</form>

<br>




<br>
Guardar Reserva V2
<form name="reserva" id="reserva" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="7">
    <input name="IDElemento" id="IDElemento" value="1699">
    <input name="IDServicio" id="IDServicio" value="789">
    <input name="Fecha" id="Fecha" value="2018-10-20">
    <input name="Hora" id="Hora" value="08:00:00">
    <input name="IDClub" id="IDClub" value="1">
    <input name="IDDisponibilidad" id="IDDisponibilidad" value="">
    <input name="Repetir" id="Repetir" value="N">
    <input name="Periodo" id="Periodo" value="">
    <input name="IDTipoModalidadEsqui" id="IDTipoModalidadEsqui" value="">
    <input name="IDAuxiliar" id="IDAuxiliar" value="">
    <input name="Tee" id="Tee" value="">
     <input name="NumeroTurnos" id="NumeroTurnos" value="">
    <input name="IDTipoReserva" id="IDTipoReserva" value="728">
    <input name="action" id="action" value="setreservageneraltest">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>

<br>
Acceso Invitados V2
<form name="accesoinvitado" id="accesoinvitado" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="9">
    <input name="IDSocio" id="IDSocio" value="61287">
    <input name="FechaIngreso" id="FechaIngreso" value="2017-08-05">
    <input name="FechaSalida" id="FechaSalida" value="2017-08-07">
   <!-- <input name="DatosInvitado" id="DatosInvitado" value='[{"IDTipoDocumento":"2","NumeroDocumento":"809089", "Nombre":"Pedro", "Apellido":"Cabeza", "Email":"jorgechirivi@gmail.com", "TipoInvitado":"Invitacion","Placa":"MNR154","CabezaInvitacion":"S" },{"IDTipoDocumento":"2","NumeroDocumento":"53050", "Nombre":"Alba Lucia", "Apellido":"Rueda", "Email":"lucyruro.com", "TipoInvitado":"Invitacion","Placa":"","CabezaInvitacion":"" },{"IDTipoDocumento":"","NumeroDocumento":"", "Nombre":"Tomas", "Apellido":"Chirivi", "Email":"", "TipoInvitado":"Invitacion","Placa":"","CabezaInvitacion":"","MenorEdad":"S" }]'> -->
   <!-- <input name="DatosInvitado" id="DatosInvitado" value='[{"IDTipoDocumento":"2","NumeroDocumento":"787890", "Nombre":"Jose", "Apellido":"Individual", "Email":"jorgechirivi@gmail.com", "TipoInvitado":"Invitacion","Placa":"MNR154","CabezaInvitacion":"" },{"IDTipoDocumento":"2","NumeroDocumento":"53456", "Nombre":"Marta", "Apellido":"Perez", "Email":"lucyruro@yahoo.com", "TipoInvitado":"Invitacion","Placa":"","CabezaInvitacion":"" }]'>-->
    <!-- <input name="DatosInvitado" id="DatosInvitado" value='[{"IDTipoDocumento":"2","NumeroDocumento":"80803214","Nombre":"Mario ","Apellido":"casteblanco","Email":"mario@mario.com","TipoInvitado":"Invitacion","Placa":"kjh871","MenorEdad":"N","CabezaInvitacion":"S"}, {"IDTipoDocumento":"0","NumeroDocumento":"","Nombre":"angel","Apellido":"castel","Email":"","TipoInvitado":"","Placa":"","MenorEdad":"S","CabezaInvitacion":"N"}, {"IDTipoDocumento":"2","NumeroDocumento":"53214008","Nombre":"eric","Apellido":"casti","Email":"","TipoInvitado":"","Placa":"","MenorEdad":"N","CabezaInvitacion":"N"}]'> -->
    <input name="DatosInvitado" id="DatosInvitado" value='[{"IDTipoDocumento":2,"NumeroDocumento":1020767107,"Nombre":"Pablo","Apellido":"Esguerra ","Email":"","TipoInvitado":"","Placa":"ZZV012","MenorEdad":"N","CabezaInvitacion":"N"}]'>

    <input name="action" id="action" value="setautorizacioninvitado">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>


<br>
Actualiza Acceso Invitados V2
<form name="accesoinvitado" id="accesoinvitado" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="8">
    <input name="IDSocio" id="IDSocio" value="5533">
    <input name="FechaIngreso" id="FechaIngreso" value="2016-10-12">
    <input name="FechaSalida" id="FechaSalida" value="2016-10-12">
    <input name="IDInvitacion" id="IDInvitacion" value="182">
    <!-- <input name="DatosInvitado" id="DatosInvitado" value='[{"IDInvitacion":"2","IDTipoDocumento":"2","NumeroDocumento":"80160", "Nombre":"Jorge A.", "Apellido":"Chirivi C", "Email":"jorge@gmail.com", "TipoInvitado":"Invitacion","Placa":"MNR154","CabezaInvitacion":"S" },{"IDInvitacion":"3","IDTipoDocumento":"2","NumeroDocumento":"53050", "Nombre":"Alba Lucia", "Apellido":"Rueda R.", "Email":"lucyruro@yahoo.com", "TipoInvitado":"Invitacion","Placa":"","CabezaInvitacion":"N" }]'> -->
    <input name="DatosInvitado" id="DatosInvitado" value='[{"IDTipoDocumento":2,"NumeroDocumento":80160,"Nombre":"Jorge Alberto","Apellido":"Chirivi C","Email":"jorgechirivi@gmail.com","TipoInvitado":"","Placa":"MNR154","MenorEdad":"N","CabezaInvitacion":"N"}, {"IDTipoDocumento":2,"NumeroDocumento":53050,"Nombre":"Alba Lucia","Apellido":"Rueda R.","Email":"lucyruro@yahoo.com","TipoInvitado":"Invitacion","Placa":"","MenorEdad":"N","CabezaInvitacion":"N"}, {"IDTipoDocumento":2,"NumeroDocumento":101101,"Nombre":"Tomas","Apellido":"Chirivi","Email":"tomaschirivi@gmail.com","TipoInvitado":"Invitacion","Placa":"","MenorEdad":"N","CabezaInvitacion":"N"}, {"IDTipoDocumento":2,"NumeroDocumento":53890502,"Nombre":"Alicia","Apellido":"Rodriguez","Email":"alicia@gmail.com","TipoInvitado":"Invitacion","Placa":"","MenorEdad":"N","CabezaInvitacion":"N"}]'>
    <input name="action" id="action" value="setautorizacioninvitadoupdate">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>


<br>
Contratista
<form name="accesocontratista" id="accesocontratista" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="9">
    <input name="IDSocio" id="IDSocio" value="5572">
    <input name="FechaIngreso" id="FechaIngreso" value="2016-12-11">
    <input name="FechaSalida" id="FechaSalida" value="2016-12-11">
     <input name="TipoAutorizacion" id="TipoAutorizacion" value="Diaria">
    <input name="TipoDocumento" id="TipoDocumento" value="2">
    <input name="NumeroDocumento" id="NumeroDocumento" value="53123456">
    <input name="Nombre" id="Nombre" value="Alicia">
    <input name="Apellido" id="Apellido" value="Rodriguez">
    <input name="Email" id="Email" value="jorgechirivi@gmail.com">
    <input name="Placa" id="Placa" value="ASD123">
    <input name="action" id="action" value="setautorizacioncontratista">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>

<br>
Actualiza Invitacion Contratista
<form name="accesocontratista" id="accesocontratista" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="8">
    <input name="IDInvitacion" id="IDInvitacion" value="2">
    <input name="IDSocio" id="IDSocio" value="5533">
    <input name="FechaIngreso" id="FechaIngreso" value="2016-09-20">
    <input name="FechaSalida" id="FechaSalida" value="2016-09-21">
     <input name="TipoAutorizacion" id="TipoAutorizacion" value="Permanente">
    <input name="TipoDocumento" id="TipoDocumento" value="2">
    <input name="NumeroDocumento" id="NumeroDocumento" value="101012">
    <input name="Nombre" id="Nombre" value="Pedro A.">
    <input name="Apellido" id="Apellido" value="Perez C.">
    <input name="Email" id="Email" value="pedro@perez1.com">
    <input name="Placa" id="Placa" value="ABC567">
    <input name="action" id="action" value="setcontratistaupdateautorizacion">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>



<br>
<form name="token" id="token" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="7">
    <input name="Dispositivo" id="Dispositivo" value="Windows Phone">
    <input name="Token" id="Token" value="12344hhgfhgfghf5667">
    <input name="IDClub" id="IDClub" value="1">
    <input name="action" id="action" value="settoken">
    <input name="key" id="key" value="CEr0CLUB">

    <input type="submit" name="enviar" value="Enviar">


</form>

<br>
ELimina Reserva
<form name="token" id="token" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="5533">
    <input name="IDReserva" id="IDReserva" value="26280">
    <input name="IDClub" id="IDClub" value="8">
    <input name="action" id="action" value="eliminareservageneral">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">


</form>

<br>Agregar Favoritos
	<form name="token" id="token" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="5533">
    <input name="SocioFavorito" id="SocioFavorito" value="5515,5516,5519">
    <input name="EstadoFavorito" id="EstadoFavorito" value="S,S,N">
    <input name="IDClub" id="IDClub" value="8">
    <input name="action" id="action" value="setsociofavorito">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">


</form>

<br>Agregar Invitado
<form name="token" id="token" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="5533">
    <input name="NumeroDocumento" id="NumeroDocumento" value="123123">
    <input name="Nombre" id="Nombre" value="Pedro Perez Rivera">
    <input name="FechaIngreso" id="FechaIngreso" value="2016-06-22">
    <input name="IDClub" id="IDClub" value="8">
    <input name="action" id="action" value="setinvitado">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">


</form>

<br>Separar reserva
<form name="separa" id="separa" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="5533">
    <input name="IDElemento" id="IDElemento" value="376">
    <input name="IDServicio" id="IDServicio" value="32">
    <input name="Fecha" id="Fecha" value="2018-02-25">
    <input name="Hora" id="Hora" value="16:00:00">
    <input name="Tee" id="Tee" value="">
    <input name="IDTipoReserva" id="IDTipoReserva" value="">
    <input name="NumeroTurnos" id="NumeroTurnos" value="">
    <input name="IDClub" id="IDClub" value="8">
    <input name="action" id="action" value="setseparareserva">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">


</form>
<br>Libera reserva
<form name="libera" id="libera" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="7">
    <input name="IDElemento" id="IDElemento" value="2">
    <input name="IDServicio" id="IDServicio" value="3">
    <input name="Fecha" id="Fecha" value="2016-02-18">
    <input name="Hora" id="Hora" value="09:00:00">
    <input name="IDClub" id="IDClub" value="1">
    <input name="action" id="action" value="setliberareserva">
    <input name="key" id="key" value="CEr0CLUB">

    <input type="submit" name="enviar" value="Enviar">


</form>


<br>Crear PQR
<form name="pqr" id="pqr" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="8"><br>
	<input name="IDSocio" id="IDSocio" value="5533"><br>
    <input name="IDArea" id="IDArea" value="1"><br>
    <!-- <input name="TipoPqr" id="TipoPqr" value="R"><br> -->
    <input name="IDTipoPqr" id="IDTipoPqr" value="13"><br>
    <input name="Asunto" id="Asunto" value="Prueba Pqr app Nov 2016"><br>
    <input name="Comentario" id="Comentario" value="Comentario del pqr app nov"><br>
    Imagen <input type="file" name="Archivo" id="Archivo"><br>
    <input name="action" id="action" value="setpqr"><br>
    <input name="key" id="key" value="CEr0CLUB"><br>
    <input type="submit" name="enviar" value="Enviar"><br>
</form>

<br>Crear Solicitud Canje
<form name="pqr" id="pqr" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="8"><br>
	<input name="IDSocio" id="IDSocio" value="5533"><br>
    <input name="IDListaClubes" id="IDListaClubes" value="2"><br>
    <input name="FechaInicio" id="FechaInicio" value="2017-06-05"><br>
    <input name="CantidadDias" id="CantidadDias" value="4"><br>
    <input name="Beneficiarios" id="Beneficiarios" value='[{"IDSocio":5556},{"IDSocio":5557} ]'><br>
    <input name="action" id="action" value="setsolicitudcanje"><br>
    <input name="key" id="key" value="CEr0CLUB"><br>
    <input type="submit" name="enviar" value="Enviar"><br>
</form>


<br>Edita Clasificado
<form name="clasif" id="clasif" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="8"><br>
	<input name="IDSocio" id="IDSocio" value="5533"><br>
    <input name="IDClasificado" id="IDClasificado" value="1"><br>
    <input name="IDEstadoClasificado" id="IDEstadoClasificado" value="2017-06-05"><br>
    <input name="IDCategoria" id="IDCategoria" value="4"><br>
    <input name="Nombre" id="Nombre" value='[{"IDSocio":5556},{"IDSocio":5557} ]'><br>
    <input name="Telefono" id="Telefono" value='[{"IDSocio":5556},{"IDSocio":5557} ]'><br>
    <input name="Descripcion" id="Descripcion" value='[{"IDSocio":5556},{"IDSocio":5557} ]'><br>
    <input name="Valor" id="Valor" value='[{"IDSocio":5556},{"IDSocio":5557} ]'><br>
    <input name="FechaInicio" id="FechaInicio" value='[{"IDSocio":5556},{"IDSocio":5557} ]'><br>
    <input name="FechaFin" id="FechaFin" value='[{"IDSocio":5556},{"IDSocio":5557} ]'><br>
    <input name="action" id="action" value="seteditaclasificado"><br>
    <input name="key" id="key" value="CEr0CLUB"><br>
    <input type="submit" name="enviar" value="Enviar"><br>
</form>



<br>Respuesta PQR
<form name="pqr" id="pqr" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="8">
	<input name="IDSocio" id="IDSocio" value="5533">
    <input name="IDPqr" id="IDPqr" value="1">
    <input name="Comentario" id="Comentario" value="Respuesta del socio app">
    <input name="action" id="action" value="setpqrrespuesta">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>



<br>Cambiar Clave
<form name="libera" id="libera" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDSocio" id="IDSocio" value="7">
    <input name="Clave" id="IDElemento" value="jorgechirivi">
    <input name="IDClub" id="IDClub" value="1">
    <input name="action" id="action" value="setcambiarclave">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>


<br>Actualizar Foto Socio
<form name="fotosocio" id="fotosocio" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="8"><br>
	<input name="IDSocio" id="IDSocio" value="5533"><br>
    Imagen <input type="file" name="Archivo" id="Archivo"><br>
    <input name="action" id="action" value="setfotosocio"><br>
    <input name="key" id="key" value="CEr0CLUB"><br>
    <input type="submit" name="enviar" value="Enviar"><br>
</form>

<br>Registra Entrada Invitado/Contratista
<form name="entradainvitado" id="entradainvitado" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDInvitacion" id="IDInvitacion" value="82690">
    <input name="TipoInvitacion" id="TipoInvitacion" value="Contratista">
    <input name="IDClub" id="IDClub" value="18">
    <input name="action" id="action" value="setentradainvitado">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>


<br>Registra Salida Invitado/Contratista
<form name="entradainvitado" id="entradainvitado" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDInvitacion" id="IDInvitacion" value="22966">
    <input name="TipoInvitacion" id="TipoInvitacion" value="InvitadoAcceso">
    <input name="IDClub" id="IDClub" value="18">
     <input name="IDUsuario" id="IDUsuario" value="1518">
    <input name="action" id="action" value="setsalidainvitado">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>

<br>Borrar como Invitado
<form name="entradainvitado" id="entradainvitado" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDReserva" id="IDReserva" value="66136">
    <input name="IDReservaGeneralInvitado" id="IDReservaGeneralInvitado" value="16222">
    <input name="IDClub" id="IDClub" value="8">
    <input name="action" id="action" value="delinvitadoservicio">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>


<br>Login Alfa
<form name="entradainvitado" id="entradainvitado" action="http://181.49.177.177/services/alfaservices.php" method="post" enctype="multipart/form-data">
	<input name="NumeroDocumento" id="NumeroDocumento" value="80210961">
    <input name="Password" id="Password" value="jescobar83">
    <input name="action" id="action" value="login">
    <input name="key" id="key" value="CErOMIL4LF">
     <input name="AppVersion" id="AppVersion" value="1">
    <input type="submit" name="enviar" value="Enviar">
</form>


<br>PAGO RESERVA PAYU
<?php

$llave_encripcion = "1251323d47d"; //llave de encripciÛn que se usa para generar la fima
$usuarioId = "49759"; //cÛdigo ?nico del cliente
$ValorTotalReserva = 27000;
$ArrayParametro["Iva"] = 0;
$refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
$iva = 0; //impuestos calculados de la transacciÛn
$baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
$valor = $ValorTotalReserva + (( $ValorTotalReserva * $ArrayParametro["Iva"] ) / 100 ) ; //el valor ; //el valor total
$valorIva =  $ValorTotalReserva * $ArrayParametro["Iva"]  / 100  ; //el valor ; //el valor total
$moneda ="COP"; //la moneda con la que se realiza la compra
$prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba
$descripcion = "Transaccion para pagar las reservas Mi Club"; //descripciÛn de la transacciÛn
$url_respuesta = "https://www.miclubapp.com/"."respuesta_transaccion.php";//Esta es la p·gina a la que se direccionar· al final del pago
$url_confirmacion = "https://www.miclubapp.com/"."confirmacion_pagos.php";
$emailComprador = "jorgechirivi@gmail.com"; //email al que llega confirmaciÛn del estado final de la transacciÛn, forma de identificar al comprador
$firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
$firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha
$extra1 = "735019";
?>


<form name="frm_POL" method="post" action="https://checkout.payulatam.com/ppp-web-gateway-payu/" target="_self">
	<input id="moneda" type="text" value="<?php echo $moneda ?>">
	<input id="ref" type="text" value="<?php echo $refVenta ?>">
	<input id="llave" type="text" value="<?php echo $llave_encripcion  ?>">
	<input id="userid" type="text" value="<?php echo $usuarioId  ?>">
	<input name="usuarioId" type="text" value="<?php echo $usuarioId?>">
    <input name="accountId"     type="hidden"  value="53793" >
	<input name="descripcion" type="text" value="<?php echo $descripcion ?>" >
	<input name="extra1" type="text" value="<?php echo $extra1 ?>" >
	<input name="extra2" type="text" value="28" >
	<input name="refVenta" type="text" value="<?php echo $refVenta ?>">
	<input name="valor" type="text" id="vlrPagosOnline" placeholder="valor" value="<?php echo $valor; ?>">
	<input name="iva" type="text" id="iva" value="<?php echo $valorIva; ?>" placeholder="iva">
	<input name="baseDevolucionIva" type="text" id="baseDevolucionIva" value="<?php echo $baseDevolucionIva; ?>" placeholder="baseDevolucionIva" >
	<input name="firma" type="text" id="firma" placeholder="firma" value="<?php echo $firma; ?>">
	<input name="emailComprador" type="text" value="jorgechirivi@gmail.com">
	<input name="prueba" type="text" value="0">
	<input name="url_respuesta" type="text" value="<?php echo $url_respuesta?>">
	<input name="url_confirmacion" type="text" value="<?php echo $url_confirmacion?>">
	<input name="Submit" type="submit" class="ver2" value="Realizar Pago en linea">
</form>

<!--
<form name="frm_POL" method="post" action="https://checkout.payulatam.com/ppp-web-gateway-payu/" target="_self">
	<input id="moneda" type="text" value="COP">
	<input id="ref" type="text" value="<?php echo $refVenta ?>">
	<input id="llave" type="text" value="4Vj8eK4rloUd272L48hsrarnUA">
	<input id="userid" type="text" value="508029">
	<input name="usuarioId" type="text" value="508029">
    <input name="accountId"     type="hidden"  value="512321" >
	<input name="descripcion" type="text" value="Pago+Reserva+Mi+Club" >
	<input name="extra1" type="text" value="758238" >
	<input name="extra2" type="text" value="8" >
	<input name="refVenta" type="text" value="<?php echo $refVenta ?>">
	<input name="valor" type="text"  placeholder="valor" value="21000">
	<input name="iva" type="text" id="iva" value="0" placeholder="iva">
	<input name="baseDevolucionIva" type="text" id="baseDevolucionIva" value="0" placeholder="baseDevolucionIva" >
	<input name="firma" type="text" id="firma" placeholder="firma" value="<?php echo $firma; ?>">
	<input name="emailComprador" type="text" value="">
	<input name="prueba" type="text" value="1">
	<input name="url_respuesta" type="text" value="https%3A%2F%2Fwww.miclubapp.com%2Frespuesta_pagos.php">
	<input name="url_confirmacion" type="text" value="https%3A%2F%2Fwww.miclubapp.com%2Fconfirmacion_pagos.php">
	<input name="Submit" type="submit" class="ver2" value="Realizar Pago en linea">
</form>
-->


<br>
Formulario Evento
<form name="accesoinvitado" id="accesoinvitado" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="8">
    <input name="IDEvento" id="IDEvento" value="18" placeholder="IDEvento">
    <input name="IDSocio" id="IDSocio" value="5533" placeholder="IDSocio">
    <input name="IDSocioBeneficiario" id="IDSocioBeneficiario" value="" placeholder="IDSocioBeneficiario">
    <input name="ValoresFormulario" id="ValoresFormulario" value='[{"IDCampoFormularioEvento":"1","Valor":"A1"},{"IDCampoFormularioEvento":"2","Valor":"B2"}]'>
    <input name="action" id="action" value="setformularioevento">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>

<br>
Presalida
<form name="accesoinvitado" id="accesoinvitado" action="/services/club.php" method="post" enctype="multipart/form-data">
	<input name="IDClub" id="IDClub" value="8">
    <input name="IDSocio" id="IDSocio" value="5533" placeholder="IDSocio">
    <input name="IDInvitacion" id="IDInvitacion" value="51980" placeholder="IDInvitacion">
    <input name="action" id="action" value="setpresalida">
    <input name="key" id="key" value="CEr0CLUB">
    <input type="submit" name="enviar" value="Enviar">
</form>


</body>
</html>
