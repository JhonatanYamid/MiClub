<?php
class SIMWebServiceBicicletas
{

	function get_configuracion_bicicleta($IDClub, $IDSocio, $IDUsuario)
	{
		$dbo = &SIMDB::get();
		$response = array();

		$sqlCampo = "SELECT IDCampoBicicleta, TipoCampo, EtiquetaCampo, Obligatorio, Valores, Orden, ParametroEnvioPost
					FROM CampoBicicleta
					WHERE IDClub = $IDClub";

		$qryCampo = $dbo->query($sqlCampo);

		$sql = "SELECT IDClub, LabelBotonSolicitarBicicleta, LabelFechaSolicitarBicicleta,LabelLugarSolicitarBicicleta, LabelVerMiHistorialBicicleta,
					LabelRefrescarBicicleta, PermiteCancelarBicicleta,LabelBotonCancelarBicicleta, PermiteSolicitarMisBeneficiarios,LabelDescripcionSeleccionBeneficiarios, 
					PermiteVerInventarioSolicitado, PermiteSolicitarInventario, LabelVerSolicitudInventario, PermiteMostrarLugar, PermiteMostrarFecha, ObligatorioMostrarLugar, ObligatorioMostrarFecha
				FROM ConfiguracionBicicletas
				WHERE IDClub = '$IDClub' AND Activo = 'S'";
		$qry = $dbo->query($sql);

		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			while ($r = $dbo->fetchArray($qry)) 
			{
				$datos['IDClub'] = $r['IDClub'];
				$datos['LabelBotonSolicitarTalega'] = $r['LabelBotonSolicitarBicicleta'];
				$datos['LabelFechaSolicitarTalega'] = $r['LabelFechaSolicitarBicicleta'];
				$datos['LabelLugarSolicitarTalega'] = $r['LabelLugarSolicitarBicicleta'];
				$datos['LabelVerMiHistorialTalega'] = $r['LabelVerMiHistorialBicicleta'];
				$datos['LabelRefrescarTalega'] = $r['LabelRefrescarBicicleta'];
				$datos['PermiteCancelarTalega'] = $r['PermiteCancelarBicicleta'];
				$datos['LabelBotonCancelarTalega'] = $r['LabelBotonCancelarBicicleta'];
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
						$campo['IDCampoSolicitarTalega'] = $rCampo['IDCampoBicicleta'];
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
				
				array_push($response, $datos);

			} //ednw hile

			

			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "T1." . SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	}

	function get_listalugares_bicicleta($IDClub, $IDSocio, $IDUsuario)
	{
		$dbo = &SIMDB::get();
		$response = array();

		$sql = "SELECT * FROM ConfiguracionBicicletaLugar	WHERE IDClub = '$IDClub' ";
		$qry = $dbo->query($sql);

		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

			while ($r = $dbo->fetchArray($qry)) {
				$datos["IDLugarTalega"] = $r["IDConfiguracionBicicletaLugar"];
				$datos["Nombre"] = $r["Nombre"];
				array_push($response, $datos);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "T2." .  SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	}

	function get_bicicleta($IDClub, $IDSocio, $IDUsuario,$IDBeneficiario = 0)
	{

		$dbo = &SIMDB::get();
		$response = array();

		$configuracion = SIMWebServiceBicicletas::get_configuracion_bicicleta($IDClub,$IDSocio,$IDUsuario);

		$PermiteCancelarBicicleta = $configuracion[response][0][PermiteCancelarTalega];
		$PermiteSolicitarInventario = $configuracion[response][0][PermiteSolicitarInventario];
		$PermiteVerInventario = $configuracion[response][0][PermiteVerInventarioSolicitado];

		$idSocio = $IDBeneficiario > 0 ? $IDBeneficiario : $IDSocio;

		$sql = "SELECT *
				FROM Bicicleta
				WHERE IDSocio = $idSocio";
	
		$qry = $dbo->query($sql);

		if ($dbo->rows($qry) > 0) {
			$message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
			
			while ($r = $dbo->fetchArray($qry)) {
				
				$datos_lugar = $dbo->fetchAll("ConfiguracionBicicletaLugar", " IDConfiguracionBicicletaLugar = '" . $r["IDConfiguracionBicicletaLugar"] . "' ", "array");
				$datos_lugarEntrega = $dbo->fetchAll("ConfiguracionBicicletaLugar", " IDConfiguracionBicicletaLugar = '" . $r["IDConfiguracionBicicletaLugarEntrega"] . "' ", "array");
				
				$datos["IDTalega"] = $r["IDBicicleta"];
				$datos["Nombre"] = $r["Nombre"];
				$datos["IDLugarTalega"] = $datos_lugar["IDConfiguracionBicicletaLugar"];
				$datos["NombreLugar"] = $datos_lugar["Nombre"];
				$datos["IDLugarEntrega"] = $datos_lugarEntrega["IDConfiguracionBicicletaLugar"];
				$datos["NombreLugarEntrega"] = $datos["IDLugarEntrega"] > 0 ? $datos_lugarEntrega["Nombre"] : '-';
				$datos["FechaEntrega"] = $datos["IDLugarEntrega"] > 0 ? $r["FechaEntrega"] : '-';
				$datos["Estado"] = SIMResources::$estado_talega[$r["Estado"]];
				$datos["PermiteSolicitarInventario"] = $PermiteSolicitarInventario;

				//Detalle Bicicleta
				$html_detalle = "<center><b>". SIMUtil::get_traduccion('', '', 'DetalleBicicleta', LANG) . "</b></center>";
				
				$sqlProp = "SELECT IDPropiedadesBicicleta, Nombre
						FROM PropiedadesBicicleta 
						WHERE IDClub = '" . $IDClub . "' ";

				$resultProp = $dbo->query($sqlProp);
				
				$aPropiedades = [];
				while ($rowProp = $dbo->fetchArray($resultProp)) {
					$aPropiedades[] = $rowProp;
				}
				
				$sqlDet = "SELECT IDPropiedadesBicicleta as IDObjeto,IDBicicletaAdministracion, IF(Valor = '','0',Valor) as CantidadInventariada, Observacion,
							CantidadSolicitada, IF(Estado = 0,'En inventario','Solicitado') as Estado, Estado as EstadoNum
						FROM BicicletaDetalle
						WHERE IDBicicleta = ".$r["IDBicicleta"];

				$resultDet = $dbo->query($sqlDet);
				$aPropiedadesBicicleta = [];
				$ObjetosInventario = [];

				while ($rowDet = $dbo->fetchArray($resultDet)) {
					$keyPr = array_search($rowDet['IDObjeto'], array_column($aPropiedades, 'IDPropiedadesBicicleta'));
					
					if($keyPr !== false){
						$rowDet['Nombre'] = $aPropiedades[$keyPr]['Nombre'];

						if(is_numeric($rowDet["CantidadInventariada"]) && $rowDet["CantidadInventariada"] != '0'){
							$Objetos['IDObjeto'] = $rowDet['IDObjeto'];
							$Objetos['Nombre'] = $rowDet['Nombre'];
							$Objetos['CantidadInventariada'] = $rowDet['CantidadInventariada'];
							$Objetos['Observacion'] = $rowDet['Observacion'];
							$Objetos['CantidadSolicitada'] = $rowDet['CantidadSolicitada'];
							$Objetos['Estado'] = $rowDet['Estado'];

							array_push($ObjetosInventario,$Objetos);
						}
					}

					$aPropiedadesBicicleta[] = $rowDet;
				}
				$datos["ObjetosInventario"] = $ObjetosInventario;

				$indexFinal = count($aPropiedades) - 1;
				$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'nombre', LANG) . ":</b> " . $datos["Nombre"];
				$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Estado', LANG) . ":</b> " . $datos["Estado"];
				
				if($r["Estado"] == 4){

					if($configuracion[response][0]['PermiteMostrarFecha'] == 'S' && strtotime($r["FechaSolicitud"]) > 0)
						$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Fechasolicitud', LANG) . ":</b> " . $r["FechaSolicitud"];
				
					if($configuracion[response][0]['PermiteMostrarLugar'] == 'S' && $r["IDConfiguracionBicicletaLugar"] > 0)
						$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Lugarsolicitud', LANG) . ":</b> " . $datos["NombreLugar"];
					
					$sqlCampos = "SELECT EtiquetaCampo, Valor, TipoCampo
									FROM CampoBicicletaValor as ctv, CampoBicicleta as ct
									WHERE ctv.IDCampoBicicleta = ct.IDCampoBicicleta AND IDBicicletaAdministracion = " . $aPropiedadesBicicleta[0]["IDBicicletaAdministracion"]." ORDER BY Orden ASC";

					$resultCampos = $dbo->query($sqlCampos);
					
					while ($rowCampos = $dbo->fetchArray($resultCampos)) {
						$html_detalle .= "<br><b>" .$rowCampos['EtiquetaCampo']. ":</b> ";

						if($rowCampos['TipoCampo'] == 'imagen' || $rowCampos['TipoCampo'] == 'imagenarchivo'){
							if($rowCampos["Valor"] != "")
								$html_detalle .= '<a title="Ver archivo" href="'.BICICLETA_ROOT.$rowCampos["Valor"].'" download="'.$rowCampos['EtiquetaCampo'].'">Ver archivo</a>';
						}
						else {
							$html_detalle .= $rowCampos["Valor"];
						}
					}
				}

				if($r["Estado"] == 3){
					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Lugarentrega', LANG) . ":</b> " .$datos["NombreLugarEntrega"];
					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Fechaentrega', LANG) . ":</b> " .$datos["FechaEntrega"];
				}

				if(count($aPropiedades) > 0){

					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'PropiedadesBicicleta', LANG) . ":</b><br>";

					$td = 1;
					$td2 = 1;
					$html_detalle .= "<table style='width:100%; border:1px solid white; border-collapse: collapse; margin: 5px;' rules='all'>";

					foreach ($aPropiedades AS $index => $propiedad) {
						$keyPrTal = array_search($propiedad["IDPropiedadesBicicleta"], array_column($aPropiedadesBicicleta, 'IDObjeto'));
						
						if($keyPrTal === false){
							$aPropiedadBicicleta = array(
								"Nombre" => $propiedad['nombre'],
								"CantidadInventariada" => 0,
								"Observacion" => "",
								"CantidadSolicitada" => 0,
								"Estadonum" => 0,
							);
						}
						else{
							$aPropiedadBicicleta = $aPropiedadesBicicleta[$keyPrTal];
						}

						$cantidad = $aPropiedadBicicleta['CantidadInventariada'];
						$solicitado = $aPropiedadBicicleta['CantidadSolicitada'];

						if($td == 1)
							$html_detalle .= "<tr>";

						$html_detalle.= "<td style = 'width:50%; background-color: #efecf3; vertical-align:top; padding: 5px;'><b>".$aPropiedadBicicleta["Nombre"] .":</b>";
						
						if(!is_numeric($cantidad) && (strlen($aPropiedadBicicleta["Nombre"])+strlen($cantidad)) > 15)
							$html_detalle.=	"<br>";

						$html_detalle.=	" $cantidad";
							
						if($solicitado > 0 && $aPropiedadBicicleta['EstadoNum'] == 1)
							$html_detalle.=	"<br><span style = 'color: #ecac87;'><b>" . SIMUtil::get_traduccion('', '', 'Solicitado', LANG) . ":</b> $solicitado</span>";

						if($aPropiedadBicicleta['Observacion'] != '')
							$html_detalle.= "<br><b>" . SIMUtil::get_traduccion('', '', 'observacion', LANG) . ":</b><br>".$aPropiedadBicicleta['Observacion'];

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

				$sqlAccesorios = "SELECT Nombre, Marca, Color, Estado FROM AccesoriosBicicleta WHERE IDBicicleta = ".$r["IDBicicleta"];
				$resultAccesorios = $dbo->query($sqlAccesorios);
				$NumAccesorios = $dbo->rows($resultAccesorios);

				if($NumAccesorios > 0){

					$html_detalle .= "<b>" . SIMUtil::get_traduccion('', '', 'Accesorios', LANG) . ":</b><br>";
					
					$tdAccesorios = 1;
					$td2Accesorios = 1;
					$html_detalle .= "<table style='width:100%; border:1px solid white; border-collapse: collapse; margin: 5px;' rules='all'>";
	
					while ($rowAccesorios = $dbo->fetchArray($resultAccesorios)) {
						
						if($tdAccesorios == 1)
							$html_detalle .= "<tr>";
	
						$html_detalle .= "<td style = 'width:50%; background-color: #efecf3; vertical-align:top; padding: 5px;'>
											<b>".$rowAccesorios['Nombre']."</b>(".$rowAccesorios['Marca']."-".$rowAccesorios['Color']."): ";
											
						if(strlen($rowAccesorios['Nombre']) + strlen($rowAccesorios['Estado']) > 15)
							$html_detalle .= "<br>";

						$html_detalle .= $rowAccesorios['Estado']."</td>";
						
						if($td2Accesorios == $NumAccesorios && $tdAccesorios == 1)
							$html_detalle .= "<td style = 'width:50%; background-color: #efecf3; vertical-align:top; padding: 5px;'></td>";
	
						if($tdAccesorios == 2){
							$html_detalle .= "</tr>";
							$tdAccesorios = 1;
						}
						else{
							$tdAccesorios++;
						}
						
						$td2Accesorios++;
					}
	
					$html_detalle.= "</table>";
					
				}

				$html_detalle .= "<img src='" . BICICLETA_ROOT . $r["CodigoArchivo"] . "' >";

				///HISTORIAL
				$sql_h ="SELECT ba.IDBicicletaAdministracion,ba.FechaRegistro,IF(ba.IDConfiguracionBicicletaLugar > 0,l.Nombre,'') as NombreLugar, ba.Observaciones, ba.NombreTercero,
						 IF(ba.Estado = 1,'Ingresa', IF(ba.Estado = 2, 'En Uso', IF(ba.Estado = 3,'Entregada', IF(ba.Estado = 4,'Solicitada', 'Editada')))) AS Estado, ba.IDSocioRegistra
						FROM BicicletaAdministracion ba
							LEFT JOIN ConfiguracionBicicletaLugar l ON(ba.IDConfiguracionBicicletaLugar = l.IDConfiguracionBicicletaLugar) 
						WHERE ba.IDBicicleta = " . $r["IDBicicleta"]." ORDER BY ba.FechaRegistro DESC";

				$result_h = $dbo->query($sql_h);

				$html_detalle .= "<br><center><b> -----------------------</b></center>" ;
				$html_detalle .= "<center><b>" . SIMUtil::get_traduccion('', '', 'Historial', LANG) . "</b></center>" ;
				
				$i = 0;
				while ($row1 = $dbo->fetchArray($result_h)) {

					$nmSocio = "";

					$infoS = $dbo->getFields("Socio", "CONCAT_WS(' ',Nombre, Apellido)", "IDSocio = ".$row1['IDSocioRegistra']);
					$infoI = $dbo->getFields("Invitado", "CONCAT_WS(' ',Nombre, Apellido)", "IDInvitado = ".$row1['IDSocioRegistra']);
					$infoSi =$dbo->getFields("SocioInvitado", "Nombre", "IDSocioInvitado = ".$row1['IDSocioRegistra']);
					
					if(!is_null($infoS) && $infoS != ''){
						$nmSocio = $infoS;
					} 
					else if(!is_null($infoI) && $infoI != ''){
						$nmSocio = $infoI;
					}
					else if(!is_null($infoSi) && $infoSi != ''){
						$nmSocio = $infoSi;
					}
					$nmSocio = $row1["NombreTercero"] != "" ? $row1["NombreTercero"] : $nmSocio;

					if($i>0)
						$html_detalle .= "<br><br><center><b> -----------------------</b></center>" ;

					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'nombre', LANG) . ":</b> " . $r["Nombre"];
					$html_detalle .= "<br><b>" .  SIMUtil::get_traduccion('', '', 'Estado', LANG) . ":</b> " . $row1["Estado"];
					
					if($row1["NombreLugar"] != ''){
						$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Lugar', LANG) . ":</b> " .$row1["NombreLugar"] ;
					}

					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Procesa', LANG) . ":</b> ".$nmSocio;
					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Fechamodificación', LANG) . ":</b> " . $row1["FechaRegistro"];
					$html_detalle .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Observaciones', LANG) . ":</b> " .$row1["Observaciones"];

					if($row1["Estado"] == 'Solicitada'){
						$sqlCamposH = "SELECT EtiquetaCampo, Valor, TipoCampo
							FROM CampoBicicletaValor as ctv, CampoBicicleta as ct
							WHERE ctv.IDCampoBicicleta = ct.IDCampoBicicleta AND IDBicicletaAdministracion = " . $row1["IDBicicletaAdministracion"]." ORDER BY Orden ASC";

						$resultCamposH = $dbo->query($sqlCamposH);
						
						while ($rowCamposH = $dbo->fetchArray($resultCamposH)) {
							$html_detalle .= "<br><b>" .$rowCamposH['EtiquetaCampo']. ":</b> ";

							if($rowCamposH['TipoCampo'] == 'imagen' || $rowCamposH['TipoCampo'] == 'imagenarchivo'){
								if($rowCamposH["Valor"] != "")
									$html_detalle .= '<a title="Ver archivo" href="'.BICICLETA_ROOT.$rowCamposH["Valor"].'" download="'.$rowCamposH['EtiquetaCampo'].'">Ver archivo</a>';
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
				$datos["Fecha"] = $r["FechaRegistro"];

				$datos["PermiteCancelarTalega"] = "N";
				$datos["PermiteVerInventarioSolicitado"] = "N";
				$PermiteSolicitar = "N";

				if($r["Estado"] == 1){
					$PermiteSolicitar = "S";
				}
				else if($r['Estado'] == 4){
					$datos["PermiteCancelarTalega"] = $PermiteCancelarBicicleta;
					$datos["PermiteVerInventarioSolicitado"] = $PermiteVerInventario;
				}
				//$datos['estadonum'] = $r["Estado"];
				$datos["PermiteSolicitarTalega"] = $PermiteSolicitar;

				array_push($response, $datos);
			} //ednw hile
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;
		} //End if
		else {
			$respuesta["message"] = "T2." .  SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		} //end else

		return $respuesta;
	}

	function set_bicicleta($IDClub, $IDSocio, $IDUsuario, $IDBicicleta, $IDLugarBicicleta = 0, $Fecha = "", $ObjetosInventario,$CamposBicicleta,$files="")
	{

		$dbo = &SIMDB::get();
		
		if (!empty($IDClub) && (!empty($IDSocio) ||  !empty($IDUsuario)) && !empty($IDBicicleta)) {
			
			$configuracion = SIMWebServiceBicicletas::get_configuracion_bicicleta($IDClub,$IDSocio,$IDUsuario);
			
			$tiempoMinimo =$dbo->getFields("ConfiguracionBicicletas", "TiempoMinimo", "IDClub = $IDClub");
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

			if (!empty($IDSocio)) {
				$Campo = "IDSocio";
				$Valor = $IDSocio;
			} else {
				$Campo = "IDUsuario";
				$Valor = $IDUsuario;
			}

			$sql_update = "UPDATE Bicicleta
							SET Estado='4',
								IDConfiguracionBicicletaLugar='".$IDLugarBicicleta."',
								FechaSolicitud='".$Fecha."',
								FechaRegistro='".$hoy."',
								UsuarioTrEd='".$IDUsuario."',
								FechaTrEd='".$hoy ."',
								IDSocioRegistra = $IDSocio
							WHERE IDBicicleta = '".$IDBicicleta."' ";

			$dbo->query($sql_update);

			$bicicletaAdministracion = array(
				"IDBicicleta" => $IDBicicleta,
				"IDConfiguracionBicicletaLugar" => $IDLugarBicicleta,
				"Estado" => '4',
				"FechaRegistro" => $hoy,
				"IDUsuarioRegistra" => $IDSocio,
				"IDSocioRegistra" => $IDSocio
			);

			$idBicicletaAdministracion = $dbo->insert($bicicletaAdministracion, "BicicletaAdministracion", "IDBicicletaAdministracion");

			$dbo->update(array("IDBicicletaAdministracion" => $idBicicletaAdministracion), 'BicicletaDetalle', 'IDBicicleta', $IDBicicleta);
			
			if($CamposBicicleta != ""){
				$arrCampos = json_decode($CamposBicicleta, true);
				
				foreach($arrCampos as $campo){
					
					$idCampo = $campo['IDCampoSolicitarTalega'];
					$valor = $campo['Valor'];

					$arrCampoValor =  array(
						"IDCampoBicicleta" => $idCampo,
						"IDBicicletaAdministracion" => $idBicicletaAdministracion,
						"IDBicicleta" => $IDBicicleta,
						"Valor" => $valor,
					);

					$campoBicicleta = $dbo->getFields("CampoBicicleta", array("TipoCampo","ParametroEnvioPost"), "IDCampoBicicleta = $idCampo");

					if($campoBicicleta['TipoCampo'] == 'imagen' || $campoBicicleta['TipoCampo'] == 'imagenarchivo'){

						$nmArchivo = $campoBicicleta['ParametroEnvioPost'];
						
						if(isset($files[$nmArchivo]) && !empty($files[$nmArchivo])){
							$arrArchivo = $files[$nmArchivo];

							$tamano_archivo = $arrArchivo["size"];
							if ($tamano_archivo >= 6000000) {
								$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
								$respuesta["success"] = false;
								$respuesta["response"] = null;
								return $respuesta;
							}

							$file = SIMFile::upload($arrArchivo, BICICLETA_DIR);
							if (empty($file) && !empty($arrArchivo["name"])){
								$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacarga.Verifiquequeelarchivonocontengaerroresyqueeltipodearchivoseapermitido', LANG);
								$respuesta["success"] = false;
								$respuesta["response"] = null;
								return $respuesta;
							};
						}
						$arrCampoValor['Valor'] = $file[0]["innername"];

					}
					
					$idCampoValor = $dbo->insert($arrCampoValor, "CampoBicicletaValor", "IDCampoBicicletaValor");

				}
			}

			if($ObjetosInventario != ""){
				$arrPropiedades = json_decode($ObjetosInventario, true);
				$rsta = "";
				foreach($arrPropiedades as $Objeto){
					$cantidadSol = $Objeto['CantidadSolicitada'];
					$estadoSol = $cantidad != '0' ? 1 : 0;
						
					$sqlUpDetalle = "UPDATE BicicletaDetalle 
									SET Estado = $estadoSol, CantidadSolicitada = $cantidadSol 
									WHERE IDBicicletaAdministracion = $idBicicletaAdministracion AND IDPropiedadesBicicleta = ".$Objeto['IDObjeto'];
					$qryUpDetalle = $dbo->query($sqlUpDetalle);

					$aPropiedadHistorico = array(
						"IDBicicletaAdministracion" => $idBicicletaAdministracion,
						"IDBicicleta" => $IDBicicleta,
						"IDPropiedadesBicicleta" => $Objeto['IDObjeto'],
						"Valor" => $cantidadSol,
						"FechaRegistro" => $hoy,
						"IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
					);

					$dbo->insert($aPropiedadHistorico, "BicicletaHistorico", "IDBicicletaHistorico");
				}
			}

			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Registradoconexito', LANG);
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;

		} else {
			$respuesta["message"] = "T5." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		return $respuesta;
	} // fin function

	public function get_mis_bicicleta_historial($IDClub, $IDSocio, $IDUsuario, $Fecha)
	{
		$dbo = SIMDB::get();
		$response = array();

		$SQLBicicleta = "SELECT * FROM Bicicleta WHERE IDSocio = $IDSocio";
		$QRYBicicleta = $dbo->query($SQLBicicleta);

		if (!empty($Fecha)) :
			$CondicionFecha = " AND date(BA.FechaRegistro) = '$Fecha'";
		endif;

		if ($dbo->rows($QRYBicicleta) > 0) :

			while ($Bicicleta = $dbo->fetchArray($QRYBicicleta)) :

				$SQLBicicletaAdmin = "SELECT BA.IDBicicletaAdministracion, BA.FechaRegistro, B.Nombre, IF(BA.Estado = 1,'Ingresa', IF(BA.Estado = 2, 'En Uso', IF(BA.Estado = 3,'Entregada', IF(BA.Estado = 4,'Solicitada', 'Editada')))) AS Estado
									FROM BicicletaAdministracion BA, Bicicleta B WHERE BA.IDBicicleta = B.IDBicicleta AND B.IDBicicleta = $Bicicleta[IDBicicleta] AND B.IDSocio = $IDSocio $CondicionFecha";

				$QRYBicicletaAdmin = $dbo->query($SQLBicicletaAdmin);

				while ($DatosAdmin = $dbo->fetchArray($QRYBicicletaAdmin)) :

					$Datos[IDBicicleta] = $Bicicleta[IDBicicleta];

					$Fecha = substr($DatosAdmin[FechaRegistro], 0, 10);
					$Hora  = substr($DatosAdmin[FechaRegistro], 11, 19);

					$Datos[Fecha] = $Fecha;
					$Datos[Hora] = $Hora;
					$Datos[Texto] = $DatosAdmin[Nombre];

					// HISTORICO

					$SQLHistorico = "SELECT * FROM BicicletaHistorico BH, PropiedadesBicicleta PB WHERE BH.IDBicicletaAdministracion = $DatosAdmin[IDBicicletaAdministracion] AND BH.IDPropiedadesBicicleta = PB.IDPropiedadesBicicleta";
					$QRYHistorico = $dbo->query($SQLHistorico);

					$Descripcion = "<h3><b>" . SIMUtil::get_traduccion('', '', ' Historial', LANG) . "</b></h3>";
					$Descripcion .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Estado', LANG) . ":" . "</b> $DatosAdmin[Estado]";
					$Descripcion .= "<br><b>" . SIMUtil::get_traduccion('', '', 'FechaRegistro', LANG) . ":" . "</b> $Fecha";
					$Descripcion .= "<br><b>" . SIMUtil::get_traduccion('', '', 'HoraRegistro', LANG) . ":" . "</b> $Hora";

					while ($Historico = $dbo->fetchArray($QRYHistorico)) :
						$Descripcion .= "<br><b>$Historico[Nombre]:</b> $Historico[Valor]";
					endwhile;

					$Datos[Descripcion] = $Descripcion;

					array_push($response, $Datos);

				endwhile;

			endwhile;

			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'Historico', LANG);
			$respuesta["success"] = true;
			$respuesta["response"] = $response;

		else :

			$respuesta["message"] = SIMUtil::get_traduccion('', '', 'NohayBicicletas', LANG);
			$respuesta["success"] = false;
			$respuesta["response"] = "";

		endif;

		return $respuesta;
	}

	public function set_cancelar_bicicleta($IDClub,$IDSocio,$IDUsuario,$IDBicicleta)
	{
		$dbo = SIMDB::get();

		if( !empty( $IDClub ) && ( !empty( $IDSocio ) ||  !empty( $IDUsuario )) && !empty( $IDBicicleta ) ):

			$SQLActualizaEstado = "UPDATE Bicicleta SET Estado = 1 WHERE IDBicicleta = $IDBicicleta";
			$QRYActualizaEstado = $dbo->query($SQLActualizaEstado);

			$bicicletaAdministracion = array(
				"IDBicicleta" => $IDBicicleta,
				"Estado" => '6',
				"FechaRegistro" => date("Y-m-d H:i:s"),
				"IDUsuarioRegistra" => $IDSocio,
				"IDSocioRegistra" => $IDSocio
			);

			$idBicicletaAdministracion = $dbo->insert($bicicletaAdministracion, "BicicletaAdministracion", "IDBicicletaAdministracion");

			$arrUpdate = [
				"IDBicicletaAdministracion" => $idBicicletaAdministracion,
				"Estado" => 0,
				"CantidadSolicitada" => 0
			];
			$dbo->update($arrUpdate, 'BicicletaDetalle', 'IDBicicleta', $IDBicicleta);


			$sql = "SELECT IDPropiedadesBicicleta, Valor
					FROM BicicletaDetalle
					WHERE IDBicicleta = $IDBicicleta";

			$result = $dbo->query($sql);
			while ($row = $dbo->fetchArray($result)) {

				$aPropiedadHistorico = array(
					"IDBicicletaAdministracion" => $idBicicletaAdministracion,
					"IDBicicleta" => $IDBicicleta,
					"IDPropiedadesBicicleta" => $row['IDPropiedadesBicicleta'],
					"Valor" => $row['Valor'],
					"FechaRegistro" => date("Y-m-d H:i:s")
				);

				$dbo->insert($aPropiedadHistorico, "BicicletaHistorico", "IDBicicletaHistorico");
			}


			$respuesta["message"] = "La bicicleta ya se encuentra cancelada y esta disponible para uso de otro socio";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;
			
		else:
			$respuesta["message"] = "Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		endif;

		return $respuesta;
	}
} //end class
