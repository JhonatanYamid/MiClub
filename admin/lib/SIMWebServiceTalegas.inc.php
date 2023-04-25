<?php
class SIMWebServiceTalegas {

	function get_configuracion_talegas($IDClub,$IDSocio,$IDUsuario){

		$dbo =& SIMDB::get();
		$response = array();
		
		$sqlCampo = "SELECT IDCampoTalega, TipoCampo, EtiquetaCampo, Obligatorio, Valores, Orden, ParametroEnvioPost
					FROM CampoTalega
					WHERE IDClub = $IDClub";

		$qryCampo = $dbo->query($sqlCampo);

		$sql = "SELECT IDClub, LabelBotonSolicitarTalega, LabelFechaSolicitarTalega, LabelLugarSolicitarTalega, LabelVerMiHistorialTalega,
					LabelRefrescarTalega, PermiteCancelarTalega, LabelBotonCancelarTalega, PermiteSolicitarMisBeneficiarios, LabelDescripcionSeleccionBeneficiarios,
					PermiteVerInventarioSolicitado, PermiteSolicitarInventario, LabelVerSolicitudInventario, PermiteMostrarLugar, PermiteMostrarFecha, ObligatorioMostrarLugar, ObligatorioMostrarFecha
				FROM ConfiguracionTalegas
				WHERE IDClub = '".$IDClub."' ";
		$qry = $dbo->query( $sql );

		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$datos['IDClub'] = $r['IDClub'];
				$datos['LabelBotonSolicitarTalega'] = $r['LabelBotonSolicitarTalega'];
				$datos['LabelFechaSolicitarTalega'] = $r['LabelFechaSolicitarTalega'];
				$datos['LabelLugarSolicitarTalega'] = $r['LabelLugarSolicitarTalega'];
				$datos['LabelVerMiHistorialTalega'] = $r['LabelVerMiHistorialTalega'];
				$datos['LabelRefrescarTalega'] = $r['LabelRefrescarTalega'];
				$datos['PermiteCancelarTalega'] = $r['PermiteCancelarTalega'];
				$datos['LabelBotonCancelarTalega'] = $r['LabelBotonCancelarTalega'];
				$datos['PermiteSolicitarMisBeneficiarios'] = $r['PermiteSolicitarMisBeneficiarios'];
				$datos['LabelDescripcionSeleccionBeneficiarios'] = $r['LabelDescripcionSeleccionBeneficiarios'];
				$datos['PermiteVerInventarioSolicitado'] = $r['PermiteVerInventarioSolicitado'];
				$datos['PermiteSolicitarInventario'] = $r['PermiteSolicitarInventario'];
				$datos['LabelVerSolicitudInventario'] = $r['LabelVerSolicitudInventario'];
				$datos['PermiteMostrarLugar'] = $r['PermiteMostrarLugar'];
				$datos['PermiteMostrarFecha'] = $r['PermiteMostrarFecha'];
				$datos['ObligatorioMostrarLugar'] = $r['ObligatorioMostrarLugar'];
				$datos['ObligatorioMostrarFecha'] = $r['ObligatorioMostrarFecha'];

				if($dbo->rows($qryCampo) > 0)
				{
					$campos = array();
					
					while($rCampo = $dbo->fetchArray($qryCampo)){
						$campo['IDCampoSolicitarTalega'] = $rCampo['IDCampoTalega'];
						$campo['TipoCampo'] = $rCampo['TipoCampo'];
						$campo['EtiquetaCampo'] = $rCampo['EtiquetaCampo'];
						$campo['Obligatorio'] = $rCampo['Obligatorio'];
						$campo['Valores'] = $rCampo['Valores'];
						$campo['Orden'] = $rCampo['Orden'];
						$campo['ParametroEnvioPost'] = $rCampo['ParametroEnvioPost'];

						array_push($campos,$campo);
					}
				}

				$datos['CamposSolicitarTalega'] = $campos;

				array_push($response,$datos);
			}//ednw hile
			
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		}//End if
		else
		{
				$respuesta["message"] = "T1. No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
		}//end else

		return $respuesta;

	}

	function get_listalugares_talega($IDClub,$IDSocio,$IDUsuario){
		$dbo =& SIMDB::get();
		$response = array();

		$sql = "SELECT *
						FROM ConfiguracionTalegasLugar
						WHERE IDClub = '".$IDClub."' ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
					$datos["IDLugarTalega"] = $r["IDConfiguracionTalegasLugar"];
					$datos["Nombre"] = $r["Nombre"];
					array_push($response, $datos);
			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;
		}//End if
		else
		{
				$respuesta["message"] = "T2. No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
		}//end else

		return $respuesta;

	}

	function get_talegas($IDClub,$IDSocio,$IDUsuario,$IDBeneficiario = 0){

		$dbo =& SIMDB::get();
		$response = array();
		
		$configuracion = SIMWebServiceTalegas::get_configuracion_talegas($IDClub,$IDSocio,$IDUsuario);

		$PermiteCancelarTalega = $configuracion[response][0][PermiteCancelarTalega];
		$PermiteSolicitarInventario = $configuracion[response][0][PermiteSolicitarInventario];
		$PermiteVerInventario = $configuracion[response][0][PermiteVerInventarioSolicitado];

		$idSocio = $IDBeneficiario > 0 ? $IDBeneficiario : $IDSocio;

		$sql = "SELECT *
				FROM Talega
				WHERE Activo = 'S' AND IDSocio = $idSocio";
	
		$qry = $dbo->query($sql);
		
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$datos_lugar = $dbo->fetchAll("ConfiguracionTalegasLugar", " IDConfiguracionTalegasLugar = '" . $r["IDConfiguracionTalegasLugar"] . "' ", "array");
				$datos_lugarEntrega = $dbo->fetchAll("ConfiguracionTalegasLugar", " IDConfiguracionTalegasLugar = '" . $r["IDConfiguracionTalegasLugarEntrega"] . "' ", "array");
				
				$datos["IDTalega"] = $r["IDTalega"];
				$datos["Nombre"] = $r["nombre"];
				$datos["IDLugarTalega"] = $datos_lugar["IDConfiguracionTalegasLugar"];
				$datos["NombreLugar"] = $datos_lugar["Nombre"];
				$datos["IDLugarEntrega"] = $datos_lugarEntrega["IDConfiguracionTalegasLugar"];
				$datos["NombreLugarEntrega"] = $datos["IDLugarEntrega"] > 0 ? $datos_lugarEntrega["Nombre"] : '-';
				$datos["FechaEntrega"] = $datos["IDLugarEntrega"] > 0 ? $r["FechaEntrega"] : '-';
				$datos["Estado"] = SIMResources::$estado_talega[$r["estado"]];
				$datos["PermiteSolicitarInventario"] = $PermiteSolicitarInventario;

				//Detalle Talega
				$html_detalle = "<center><b>". SIMUtil::get_traduccion('', '', 'DetalleTalega', LANG) . "</b></center>";
				
				$sqlProp = "SELECT IDPropiedadesTalega, nombre
						FROM PropiedadesTalega 
						WHERE IDClub = '" . $IDClub . "' ";

				$resultProp = $dbo->query($sqlProp);
				
				$aPropiedades = [];
				while ($rowProp = $dbo->fetchArray($resultProp)) {
					$aPropiedades[] = $rowProp;
				}
				
				$sqlDet = "SELECT IDPropiedadesTalega as IDObjeto,IDTalegaAdministracion, IF(valor = '','0',valor) as CantidadInventariada, Observacion,
							CantidadSolicitada, IF(Estado = 0,'En inventario','Solicitado') as Estado, Estado as EstadoNum
						FROM TalegaDetalle
						WHERE IDTalega = ".$r["IDTalega"];

				$resultDet = $dbo->query($sqlDet);
				$aPropiedadesTalega = [];
				$ObjetosInventario = [];

				while ($rowDet = $dbo->fetchArray($resultDet)) {
					$keyPr = array_search($rowDet['IDObjeto'], array_column($aPropiedades, 'IDPropiedadesTalega'));

					if($keyPr !== false){
						$rowDet['Nombre'] = $aPropiedades[$keyPr]['nombre'];

						if(is_numeric($rowDet["CantidadInventariada"]) && $rowDet["CantidadInventariada"] > 0){
							$Objetos['IDObjeto'] = $rowDet['IDObjeto'];
							$Objetos['Nombre'] = $rowDet['Nombre'];
							$Objetos['CantidadInventariada'] = $rowDet['CantidadInventariada'];
							$Objetos['Observacion'] = $rowDet['Observacion'];
							$Objetos['CantidadSolicitada'] = $rowDet['CantidadSolicitada'];
							$Objetos['Estado'] = $rowDet['Estado'];

							array_push($ObjetosInventario, $Objetos);
						}
					}
					
					$aPropiedadesTalega[] = $rowDet;
				}
				$datos["ObjetosInventario"] = $ObjetosInventario;

				$indexFinal = count($aPropiedades) - 1;
				$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Nombre', LANG) . ":</b> " . $datos["Nombre"];
				$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Estado', LANG) . ":</b> " . $datos["Estado"];
				
				if($r["estado"] == 4){

					if($configuracion[response][0]['PermiteMostrarFecha'] == 'S' && strtotime($r["FechaSolicitud"]) > 0)
						$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Fechasolicitud', LANG) . ":</b> " . $r["FechaSolicitud"];
					
					if($configuracion[response][0]['PermiteMostrarLugar'] == 'S' && $r["IDConfiguracionTalegasLugar"] > 0)
						$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Lugarsolicitud', LANG) . ":</b> " . $datos["NombreLugar"];
					
					$sqlCampos = "SELECT EtiquetaCampo, Valor, TipoCampo
							FROM CampoTalegaValor as ctv, CampoTalega as ct
							WHERE ctv.IDCampoTalega = ct.IDCampoTalega AND IDTalegaAdministracion = " . $aPropiedadesTalega[0]["IDTalegaAdministracion"]." ORDER BY Orden ASC";

					$resultCampos = $dbo->query($sqlCampos);
					
					while ($rowCampos = $dbo->fetchArray($resultCampos)) {
						$html_detalle .= "<br><b>" .$rowCampos['EtiquetaCampo']. ":</b> ";

						if($rowCampos['TipoCampo'] == 'imagen' || $rowCampos['TipoCampo'] == 'imagenarchivo'){
							if($rowCampos["Valor"] != "")
								$html_detalle .= '<a title="Ver archivo" href="'.TALEGA_ROOT.$rowCampos["Valor"].'" download="'.$rowCampos['EtiquetaCampo'].'">Ver archivo</a>';
						}
						else {
							$html_detalle .= $rowCampos["Valor"];
						}
					}
				}

				if($r["estado"] == 3){
					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Lugarentrega', LANG) . ":</b> " .$datos["NombreLugarEntrega"];
					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Fechaentrega', LANG) . ":</b> " .$datos["FechaEntrega"];
				}

				if(count($aPropiedades) > 0){

					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'PropiedadesTalega', LANG) . ":</b><br>";

					$td = 1;
					$td2 = 1;
					$html_detalle .= "<table style='width:100%; border:1px solid white; border-collapse: collapse; margin: 5px;' rules='all'>";

					foreach ($aPropiedades AS $index => $propiedad) {
						$keyPrTal = array_search($propiedad["IDPropiedadesTalega"], array_column($aPropiedadesTalega, 'IDObjeto'));
		
						if($keyPrTal === false){
							$aPropiedadTalega = array(
								"Nombre" => $propiedad['nombre'],
								"CantidadInventariada" => 0,
								"Observacion" => "",
								"CantidadSolicitada" => 0,
								"Estadonum" => 0,
							);
						}
						else{
							$aPropiedadTalega = $aPropiedadesTalega[$keyPrTal];
						}

						//array_push($arrPropPr,$aPropiedadTalega);

						$cantidad = $aPropiedadTalega['CantidadInventariada'];
						$solicitado = $aPropiedadTalega['CantidadSolicitada'];

						if($td == 1)
							$html_detalle .= "<tr>";

						$html_detalle.= "<td style = 'width:50%; background-color: #efecf3; vertical-align:top; padding: 5px;'><b>".$aPropiedadTalega["Nombre"] .":</b>";
						
						if(!is_numeric($cantidad) && (strlen($aPropiedadTalega["Nombre"])+strlen($cantidad)) > 15)
							$html_detalle.=	"<br>";

						$html_detalle.=	" $cantidad";
							
						if($solicitado > 0 && $aPropiedadTalega['EstadoNum'] == 1)
							$html_detalle.=	"<br><span style = 'color: #ecac87;'><b>" . SIMUtil::get_traduccion('', '', 'Solicitado', LANG) . ":</b> $solicitado</span>";

						if($aPropiedadTalega['Observacion'] != '')
							$html_detalle.= "<br><b>" . SIMUtil::get_traduccion('', '', 'observacion', LANG) . ":</b><br>".$aPropiedadTalega['Observacion'];

						$html_detalle .= "</td>";
						
						if($td2 == count($aPropiedades) && $td == 1)
								$html_detalle .= "<td style = 'width:50%; background-color: #efecf3; vertical-align:top; padding: 5px;'></td>";

						if($td == 2){
							$html_detalle .= "</tr>";
							$td = 1;
						}
						else{
							$td++;
						}	
						
						$td2++;
					}

					$html_detalle.= "</table>";
				}

				$sqlPalos = "SELECT NombrePalo, Marca, Color, Estado FROM TalegaPalos WHERE IDTalega = ".$r["IDTalega"];
				$resultPalos = $dbo->query($sqlPalos);
				$NumPalos = $dbo->rows($resultPalos);

				if($NumPalos > 0){

					$html_detalle .= "<b>" . SIMUtil::get_traduccion('', '', 'palos', LANG) . ":</b><br>";
					
					$tdPalos = 1;
					$td2Palos = 1;
					$html_detalle .= "<table style='width:100%; border:1px solid white; border-collapse: collapse; margin: 5px;' rules='all'>";
	
					while ($rowPalos = $dbo->fetchArray($resultPalos)) {
						
						if($tdPalos == 1)
							$html_detalle .= "<tr>";
	
						$html_detalle .= "<td style = 'width:50%; background-color: #efecf3; vertical-align:top; padding: 5px;'>
											<b>".$rowPalos['NombrePalo']."</b>(".$rowPalos['Marca']."-".$rowPalos['Color']."): ";
											
						if(strlen($rowPalos['NombrePalo']) + strlen($rowPalos['Estado']) > 15)
							$html_detalle .= "<br>";

						$html_detalle .= $rowPalos['Estado']."</td>";
						
						if($td2Palos == $NumPalos && $tdPalos == 1)
							$html_detalle .= "<td style = 'width:50%; background-color: #efecf3; vertical-align:top; padding: 5px;'></td>";
	
						if($tdPalos == 2){
							$html_detalle .= "</tr>";
							$tdPalos = 1;
						}
						else{
							$tdPalos++;
						}
						
						$td2Palos++;
					}
	
					$html_detalle.= "</table>";
					
				}
					
				$html_detalle.= "<br><img src='".TALEGA_ROOT . $r["codigoArchivo"]."'><br>";

				///HISTORIAL
				$sql_h ="SELECT ta.IDTalegaAdministracion,ta.fechaRegistro,IF(ta.IDConfiguracionTalegasLugar > 0,l.Nombre,'') as nombrelugar,t.nombre, ta.observaciones, ta.nombreTercero,
						 IF(ta.estado = 1,'Ingresa', IF(ta.estado = 2, 'En campo', IF(ta.estado = 3,'Entregada', IF(ta.estado = 4,'Solicitada', 'Editada')))) AS estado, ta.IdSocioRegistra
						FROM TalegaAdministracion ta
							INNER JOIN Talega t ON(ta.IDTalega = t.IDTalega) 
							LEFT JOIN ConfiguracionTalegasLugar l ON(ta.IDConfiguracionTalegasLugar = l.IDConfiguracionTalegasLugar) 
						WHERE t.IDTalega = " . $r["IDTalega"]." ORDER BY ta.fechaRegistro DESC";

				$result_h = $dbo->query($sql_h);

				$html_detalle .= "<br><center><b> -----------------------</b></center>" ;
				$html_detalle .= "<center><b>" . SIMUtil::get_traduccion('', '', 'Historial', LANG) . "</b></center>" ;
				
				$i = 0;
				while ($row1= $dbo->fetchArray($result_h)) {

					$nmSocio = "";

					$infoS = $dbo->getFields("Socio", "CONCAT_WS(' ',Nombre, Apellido)", "IDSocio = ".$row1['IdSocioRegistra']);
					$infoI = $dbo->getFields("Invitado", "CONCAT_WS(' ',Nombre, Apellido)", "IDInvitado = ".$row1['IdSocioRegistra']);
					$infoSi =$dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = ".$row1['IdSocioRegistra']);
					
					if(!is_null($infoS) && $infoS != ''){
						$nmSocio = $infoS;
					} 
					else if(!is_null($infoI) && $infoI != ''){
						$nmSocio = $infoI;
					}
					else if(!is_null($infoSi) && $infoSi != ''){
						$nmSocio = $infoSi;
					}

					$nmSocio = $row1["nombreTercero"] != "" ? $row1["nombreTercero"] : $nmSocio;

					if($i>0)
						$html_detalle .= "<br><br><center><b> -----------------------</b></center>" ;
					
					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'nombre', LANG) . ":</b> " . $r["nombre"];
					$html_detalle .= "<br><b>" .  SIMUtil::get_traduccion('', '', 'Estado', LANG) . ":</b> " . $row1["estado"];
					
					if($row1["nombrelugar"] != ''){
						$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Lugar', LANG) . ":</b> " .$row1["nombrelugar"] ;
					}

					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Procesa', LANG) . ":</b> ".$nmSocio;
					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Fechamodificación', LANG) . ":</b> " . $row1["fechaRegistro"];
					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Observaciones', LANG) . ":</b> " .$row1["observaciones"];	

					if($row1["estado"] == 'Solicitada'){
						$sqlCamposH = "SELECT EtiquetaCampo, Valor, TipoCampo
							FROM CampoTalegaValor as ctv, CampoTalega as ct
							WHERE ctv.IDCampoTalega = ct.IDCampoTalega AND IDTalegaAdministracion = " . $row1["IDTalegaAdministracion"]." ORDER BY Orden ASC";

						$resultCamposH = $dbo->query($sqlCamposH);
						
						while ($rowCamposH = $dbo->fetchArray($resultCamposH)) {
							$html_detalle .= "<br><b>" .$rowCamposH['EtiquetaCampo']. ":</b> ";

							if($rowCamposH['TipoCampo'] == 'imagen' || $rowCamposH['TipoCampo'] == 'imagenarchivo'){
								if($rowCamposH["Valor"] != "")
									$html_detalle .= '<a title="Ver archivo" href="'.TALEGA_ROOT.$rowCamposH["Valor"].'" download="'.$rowCamposH['EtiquetaCampo'].'">Ver archivo</a>';
							}
							else {
								$html_detalle .= $rowCamposH["Valor"];
							}
						}
					}

					$i++;
				}

				//FIN HSTORIAL

				$datos["DetalleHtml"] = $html_detalle;
				$datos["Fecha"] = $r["fechaRegistro"];

				$datos["PermiteCancelarTalega"] = "N";
				$datos["PermiteVerInventarioSolicitado"] = "N";
				$PermiteSolicitar = "N";

				if($r["estado"] == 1){
					$PermiteSolicitar = "S";
				}
				else if($r['estado'] == 4){
					$datos["PermiteCancelarTalega"] = $PermiteCancelarTalega;
					$datos["PermiteVerInventarioSolicitado"] = $PermiteVerInventario;
				}

				if($IDClub == 7){
					if($r["estado"] == 1){
						$sqlCuantos = "SELECT COUNT(*) as cuantos 
										FROM Talega 
										WHERE (estado = 4 OR estado = 3) AND IdSocioRegistra = $IDSocio AND Activo = 'S'";

						$resCuantos = $dbo->query($sqlCuantos);
						$rowCuantos = $dbo->fetchArray($resCuantos);
						
						if($rowCuantos['cuantos'] > 0){
							$PermiteSolicitar="N";
							$datos["NombreLugar"] = $datos["NombreLugar"]." - (Tiene una talega en gestión)";
						}
					}
				}
				
				$datos["PermiteSolicitarTalega"] = $PermiteSolicitar;

				array_push($response, $datos);
			}//ednw while
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;
		}//End if
		else
		{
				$respuesta["message"] = "T2. No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
		}//end else

		return $respuesta;

	}

	function set_talega($IDClub,$IDSocio,$IDUsuario,$IDTalega, $IDLugarTalega = 0, $Fecha = "",$ObjetosInventario,$CamposTalega,$files=""){

		$dbo =& SIMDB::get();
		
		if( !empty( $IDClub ) && ( !empty( $IDSocio ) ||  !empty( $IDUsuario )) && !empty( $IDTalega ) ){

			$configuracion = SIMWebServiceTalegas::get_configuracion_talegas($IDClub,$IDSocio,$IDUsuario);

			$tiempoMinimo =$dbo->getFields("ConfiguracionTalegas", "TiempoMinimo", "IDClub = $IDClub");
			$hoy = date("Y-m-d H:i:s"); 
	
			if($configuracion[response][0]['PermiteMostrarFecha'] == 'S' && strtotime($Fecha) > 0){
	
				$minutos = (strtotime($Fecha) - strtotime($hoy))/60;
				$minutos = floor($minutos);
	
				if($minutos < $tiempoMinimo){
					$respuesta["message"] = "El tiempo minimo para solicitar una talega es de: ".$tiempoMinimo." minutos";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
	
					return $respuesta;
				}
			}
		
			if(!empty($IDSocio)){
				$Campo="IDSocio";
				$Valor=$IDSocio;
			}
			else{
				$Campo="IDUsuario";
				$Valor=$IDUsuario;
			}
			
			$sql_update = "UPDATE Talega
							SET estado='4',
								IDConfiguracionTalegasLugar='".$IDLugarTalega."',
								FechaSolicitud='".$Fecha."',
								fechaRegistro='".$hoy."',
								UsuarioTrEd='".$IDUsuario."',
								FechaTrEd='".$hoy."',
								IdSocioRegistra = $IDSocio
							WHERE IDTalega = '".$IDTalega."' ";
			$dbo->query($sql_update);

			$talegaAdministracion = array(
				"IDTalega" => $IDTalega,
				"IDConfiguracionTalegasLugar" => $IDLugarTalega,
				"estado" => '4',
				"fechaRegistro" => $hoy,
				"idUsuarioRegistra" => $IDSocio,
				"IdSocioRegistra" => $IDSocio
			);

			$idTalegaAdministracion = $dbo->insert($talegaAdministracion, "TalegaAdministracion", "IDTalegaAdministracion");

			$dbo->update(array("IDTalegaAdministracion" => $idTalegaAdministracion), 'TalegaDetalle', 'IDTalega', $IDTalega);

			if($CamposTalega != ""){
				$arrCampos = json_decode($CamposTalega, true);
				
				foreach($arrCampos as $campo){
					
					$idCampo = $campo['IDCampoSolicitarTalega'];
					$valor = $campo['Valor'];

					$arrCampoValor =  array(
						"IDCampoTalega" => $idCampo,
						"IDTalegaAdministracion" => $idTalegaAdministracion,
						"IDTalega" => $IDTalega,
						"Valor" => $valor,
					);

					$campoTalega = $dbo->getFields("CampoTalega", array("TipoCampo","ParametroEnvioPost"), "IDCampoTalega = $idCampo");

					if($campoTalega['TipoCampo'] == 'imagen' || $campoTalega['TipoCampo'] == 'imagenarchivo'){

						$nmArchivo = $campoTalega['ParametroEnvioPost'];
						
						if(isset($files[$nmArchivo]) && !empty($files[$nmArchivo])){
							$arrArchivo = $files[$nmArchivo];

							$tamano_archivo = $arrArchivo["size"];
							if ($tamano_archivo >= 6000000) {
								$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
								$respuesta["success"] = false;
								$respuesta["response"] = null;
								return $respuesta;
							}

							$file = SIMFile::upload($arrArchivo, TALEGA_DIR);
							if (empty($file) && !empty($arrArchivo["name"])){
								$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacarga.Verifiquequeelarchivonocontengaerroresyqueeltipodearchivoseapermitido', LANG);
								$respuesta["success"] = false;
								$respuesta["response"] = null;
								return $respuesta;
							};
						}
						$arrCampoValor['Valor'] = $file[0]["innername"];

					}
					
					$idCampoValor = $dbo->insert($arrCampoValor, "CampoTalegaValor", "IDCampoTalegaValor");

				}
			}
			
			if($ObjetosInventario != ""){
				$arrPropiedades = json_decode($ObjetosInventario, true);
				$rsta = "";
				foreach($arrPropiedades as $Objeto){
					$cantidadSol = $Objeto['CantidadSolicitada'];
					$estadoSol = $cantidad != '0' ? 1 : 0;
						
					$sqlUpDetalle = "UPDATE TalegaDetalle 
									SET Estado = $estadoSol, CantidadSolicitada = $cantidadSol 
									WHERE IDTalegaAdministracion = $idTalegaAdministracion AND IDPropiedadesTalega = ".$Objeto['IDObjeto'];
					$qryUpDetalle = $dbo->query($sqlUpDetalle);

					$aPropiedadHistorico = array(
						"IDTalegaAdministracion" => $idTalegaAdministracion,
						"IDTalega" => $IDTalega,
						"IDPropiedadesTalega" => $Objeto['IDObjeto'],
						"valor" => $cantidadSol,
						"fechaRegistro" => $hoy,
						"idUsuarioRegistra" => SIMUser::get("IDUsuario"),
					);

					$dbo->insert($aPropiedadHistorico, "TalegaHistorico", "IDTalegaHistorico");
				}
			}

			$respuesta["message"] = "Registrado con exito";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;			
		}
		else{
			$respuesta["message"] = "T5. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
		return $respuesta;

	}// fin function

	public function set_cancelar_talega($IDClub,$IDSocio,$IDUsuario,$IDTalega)
	{
		$dbo = SIMDB::get();

		if( !empty( $IDClub ) && ( !empty( $IDSocio ) ||  !empty( $IDUsuario )) && !empty( $IDTalega ) ):

			$SQLActualizaEstado = "UPDATE Talega SET estado = 1 WHERE IDTalega = $IDTalega";
			$QRYActualizaEstado = $dbo->query($SQLActualizaEstado);

			$talegaAdministracion = array(
				"IDTalega" => $IDTalega,
				"estado" => '6',
				"fechaRegistro" => date("Y-m-d H:i:s"),
				"idUsuarioRegistra" => $IDSocio,
				"IdSocioRegistra" => $IDSocio
			);

			$idTalegaAdministracion = $dbo->insert($talegaAdministracion, "TalegaAdministracion", "IDTalegaAdministracion");

			$arrUpdate = [
				"IDTalegaAdministracion" => $idTalegaAdministracion,
				"Estado" => 0,
				"CantidadSolicitada" => 0
			];
			$dbo->update($arrUpdate, 'TalegaDetalle', 'IDTalega', $IDTalega);


			$sql = "SELECT IDPropiedadesTalega, valor
					FROM TalegaDetalle
					WHERE IDTalega = $IDTalega";

			$result = $dbo->query($sql);
			while ($row = $dbo->fetchArray($result)) {

				$aPropiedadHistorico = array(
					"IDTalegaAdministracion" => $idTalegaAdministracion,
					"IDTalega" => $IDTalega,
					"IDPropiedadesTalega" => $row['IDPropiedadesTalega'],
					"valor" => $row['valor'],
					"fechaRegistro" => date("Y-m-d H:i:s")
				);

				$dbo->insert($aPropiedadHistorico, "TalegaHistorico", "IDTalegaHistorico");
			}

			$respuesta["message"] = "La talega ya se encuentra cancelada y esta disponible para uso de otro socio";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;
			
		else:
			$respuesta["message"] = "Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		endif;

		return $respuesta;
	}

	public function get_mis_talegas_historial($IDClub, $IDSocio, $IDUsuario, $Fecha)
	{
		$dbo = SIMDB::get();
		$response = array();

		$SQLTalegas = "SELECT * FROM Talega WHERE IDSocio = $IDSocio";
		$QRYTalegas = $dbo->query($SQLTalegas);

		if(!empty($Fecha)):
			$CondicionFecha = " AND date(TA.fechaRegistro) = '$Fecha'";
		endif;

		if($dbo->rows($QRYTalegas) > 0):

			while($Talega = $dbo->fetchArray($QRYTalegas)):

				$SQLTalegaAdmin = "SELECT TA.IDTalegaAdministracion, TA.fechaRegistro, T.Nombre, IF(TA.estado = 1,'Ingresa', IF(TA.estado = 2, 'En campo', IF(TA.estado = 3,'Entregada', 'Solicitada'))) AS Estado
									FROM TalegaAdministracion TA, Talega T WHERE TA.IDTalega = T.IDTalega AND T.IDTalega = $Talega[IDTalega] AND T.IDSocio = $IDSocio $CondicionFecha";
				
				$QRYTalegaAdmin = $dbo->query($SQLTalegaAdmin);			

				while($DatosAdmin = $dbo->fetchArray($QRYTalegaAdmin)):

					$Datos[IDTalega] = $Talega[IDTalega];

					$Fecha = substr($DatosAdmin[fechaRegistro], 0, 10);
					$Hora  = substr($DatosAdmin[fechaRegistro], 11, 19);

					$Datos[Fecha] = $Fecha;
					$Datos[Hora] = $Hora;
					$Datos[Texto] = $DatosAdmin[Nombre];

					// HISTORICO

					$SQLHistorico = "SELECT * FROM TalegaHistorico TH, PropiedadesTalega PT WHERE TH.IDTalegaAdministracion = $DatosAdmin[IDTalegaAdministracion] AND TH.IDPropiedadesTalega = PT.IDPropiedadesTalega";
					$QRYHistorico = $dbo->query($SQLHistorico);

					$Descripcion = "<h3><b>Historial</b></h3>";
					$Descripcion .= "<br><b>Estado:</b> $DatosAdmin[Estado]";
					$Descripcion .= "<br><b>Fecha Registro:</b> $Fecha";
					$Descripcion .= "<br><b>Hora Registro:</b> $Hora";

					while($Historico = $dbo->fetchArray($QRYHistorico)):
						$Descripcion .= "<br><b>$Historico[nombre]:</b> $Historico[valor]";
					endwhile;

					$Datos[Descripcion] = $Descripcion;

					array_push($response, $Datos);

				endwhile;			
			
			endwhile;

			$respuesta["message"] = "Historico";
			$respuesta["success"] = true;
			$respuesta["response"] = $response;

		else:

			$respuesta["message"] = "No hay talegas";
			$respuesta["success"] = false;
			$respuesta["response"] = "";

		endif;

		return $respuesta;
		
	}


} //end class
?>
