<?php
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();


$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );
//$id = SIMNet::reqInt( "IDContacto" );

$columns = array();
$origen = SIMNet::req("origen");

$ids = SIMNet::req("idservicio");
$idelemento = SIMNet::req("idelemento");
$fecha = SIMNet::req("fecha");

//Si es coordinador tenemos que mirar que servicio se trae primero
//si es un usuario normal se traen las horas
if( empty( $fecha ) )
	$fecha = date("Y-m-d");


$oper = SIMNet::req("oper");
if( SIMNet::req("_search") == "true" )
	$oper = "search";

switch( $oper )
{

	case "del":




	break;

	case "search":

		$filters = stripslashes( stripslashes( htmlspecialchars_decode( SIMNet::req("filters") ) ) );
		$array_buqueda = json_decode($filters);
		foreach( $array_buqueda->rules as $key_busqueda => $search_object )
		{
			switch ($search_object->field) {
				case 'Socio':

					$idsocio = $dbo->getFields("Socio", "IDSocio", " IDClub = '" . SIMUser::get("IDClub") . "' AND ( Accion = '" . $search_object->data . "' OR NumeroDocumento = '" . $search_object->data . "' OR AccionPadre = '" . $search_object->data . "' ) ");

				break;


				case 'IDElemento':
					$IDServicioElemento = $search_object->data;
				break;

				case 'Fecha':
					$fecha = $search_object->data;
				break;
			}

		}//end for

		$str_limit = "";


	break;

	case "searchurl":
		$accion = $_GET["Accion"];
		$idsocio =  $accion ;

		$fecha = "";

		if( empty( $idsocio ) && !empty( $_GET["fecha"] ) )
			$fecha = $_GET["fecha"];

	break;

}//end switch



$reserva = SIMWebService::get_reservas_servicio(SIMUser::get("club"), $ids ,$fecha, $idelemento, $idsocio );



$result = $dbo->query( $sql );

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$hoy = date('Y-m-d');
foreach( $reserva["response"] as $key => $row  )
{


	$responce->rows[$i]['id'] = $row["IDReserva"];

	if (!empty($row["Tee"]))
		$Tee = " - " .$row["Tee"];
	else
		$Tee = "";

	$btn_eliminar = string;

	//Para el Rancho todos pueden cancelar reserva
	if(SIMUser::get("IDPerfil") <= 2 ||  SIMUser::get("IDClub")=="12" ||  SIMUser::get("IDPerfil")=="21" ||  SIMUser::get("IDPerfil")=="22" ||  SIMUser::get("IDPerfil")=="31" ||  SIMUser::get("IDPerfil")=="32"):
			$btn_eliminar = '<a id="detalle_eliminar'.$row["IDReserva"].'"  class="fancybox" href="detalle_reserva_eliminar.php?idr='.$row["IDReserva"].'" data-fancybox-type="iframe" ><i class="ace-icon fa fa-trash-o bigger-130"/></a>';
		else:
			$btn_eliminar = '';
		endif;

	$class = "a-edit-modal btnAddReg";
	$attr = "rev=\"reload_grid\"";

	$contador ++;

	if($contador<=500):

	if( $origen <> "mobile" ){

		// Si la reserva fue tomada para algun beneficiario muestro el nombre del beneficiario
		$IDBeneficiario = $dbo->getFields( "ReservaGeneral" , "IDSocioBeneficiario" , "IDReservaGeneral = '" . $row["IDReserva"] . "'" );

		if($row["IDClub"]==35) // En puerto penalisa debe ser la casa
			$Accion = utf8_encode($row["Socio"]["Predio"]);
		else
			$Accion = utf8_encode($row["Socio"]["Accion"]);

		if($IDBeneficiario):
			$nombre_reserva = "Benef. " . utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $IDBeneficiario . "'" )	. " "  . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $IDBeneficiario . "'" ));
		else:
			$nombre_reserva = utf8_encode($row["Socio"]["Nombre"]) . " " . utf8_encode($row["Socio"]["Apellido"]);
		endif;


		if($row["IDClub"]==37 && $row["IDServicio"]==3575):  // especial polo club se debe actualizar cancha y equipo
			$Handicap = "<br><b>Handicap:</b>" . $dbo->getFields( "Socio" , "Handicap" , "IDSocio = '".$row["IDSocio"]."'" );
			$canchas=SIMHTML::formPopupArray( SIMResources::$canchas_polo  ,  $detalle_reserva["Cancha"] , "Cancha_".$row["IDSocio"]."_".$row["IDReserva"] ,  "Seleccione cancha" , "form-control canchapolo"  );
            $equipo=SIMHTML::formPopupArray( SIMResources::$equipos_polo  ,  $detalle_reserva["Equipo"] , "Equipo_".$row["IDSocio"]."_".$row["IDReserva"] ,  "Seleccione equipo" , "form-control equipopolo"  );
		endif;




			$responce->rows[$i]['cell'] = array(
											"IDReservaGeneral" => $row["IDReserva"],
											"Detalle" => '<a id="detalle'.$row["IDReserva"].'"  class="fancybox" href="detalle_reserva.php?idr='.$row["IDReserva"].'" data-fancybox-type="iframe" ><i class="ace-icon fa fa-file-text-o bigger-130"/></a>',
											"Fecha" => SIMUtil::tiempo( $row["Fecha"] ) . " " . $Tee,
											"Hora" => $row["Hora"],
											"Socio" => $nombre_reserva .  $row["IDSocioBeneficiario"],
											"Accion" => $Accion . $Handicap . $canchas . $equipo,
											"IDElemento" => $row["NombreElemento"],
											//"Cancelar" => '<a class="red" href="#"><i class="ace-icon fa fa-trash-o bigger-130"/></a>'
											"Cancelar" => $btn_eliminar
										);
	}

	endif;

	$i++;
}

echo json_encode($responce);

?>
