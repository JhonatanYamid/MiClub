<? 
	$IDClub = SIMUser::get("club");
	$dbo =& SIMDB::get();

	$sqlGrupo = "SELECT 
					cfs.IDGruposFormularioSocio as IDGrupo, gfs.Nombre as Grupo, COUNT(cs.IDCampoSocioClub) as Cantidad 
				FROM CampoSocioClub as cs 
					JOIN CamposFormularioSocio as cfs ON cs.IDCampoFormularioSocio = cfs.IDCamposFormularioSocio 
					JOIN GruposFormularioSocio as gfs ON cfs.IDGruposFormularioSocio = gfs.IDGruposFormularioSocio
				WHERE cs.IDClub = $IDClub
					GROUP BY cfs.IDGruposFormularioSocio
					ORDER BY gfs.Orden";

	$sqlCampo = "SELECT cfs.* 
				FROM CampoSocioClub as csc
					LEFT JOIN CamposFormularioSocio as cfs ON csc.IDCampoFormularioSocio = cfs.IDCamposFormularioSocio
				WHERE csc.IDClub = $IDClub AND cfs.IDGruposFormularioSocio = ";
?>
<form class="form-horizontal formvalida" role="form" method="post" id="frmSocio" action="<? echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
	<? 		
	try {
		//Inicialmente se hace el recorrido de los grupos 
		$resultGrupo = $dbo->query($sqlGrupo);
		while ($rowGrupo = $dbo->fetchArray($resultGrupo)){ 
			$cantidad = $rowGrupo['Cantidad'];?>

			<div class="widget-header widget-header-large">
				<h3 class="widget-title grey lighter">
					<i class="ace-icon fa fa-users green"></i> <? echo $rowGrupo['Grupo']; ?>
				</h3>
			</div>

			<? if($rowGrupo['Grupo']=='Datos Básicos'){ ?>

				<div class="form-group first ">
					<div class="col-xs-12 col-sm-6">
						<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <? echo $ArrayMensajesWeb[$tipo_club]["TipoSocio"];?> </label>
						<div class="col-sm-8"> 
							<? if (SIMUser::get("IDPerfil") == 7){
								$tipo_socio = array("Canje" => "Canje",	 "Cortesia" => "Cortesia", "Invitado" => "Invitado");
								echo SIMHTML::formPopupArray($tipo_socio,  $frm["TipoSocio"], "TipoSocio",  "Seleccione tipo", "form-control");
							}
							else{
								$sql_tipo_socio = "SELECT TS.IDTipoSocio,Nombre FROM TipoSocio TS, ClubTipoSocio CTS WHERE TS.IDTipoSocio=CTS.IDTipoSocio AND IDClub = '" . SIMUser::get("club") . "' Order by Nombre";
								$result_tipo_socio = $dbo->query($sql_tipo_socio); ?>
							 		
								<select name="TipoSocio" id="TipoSocio" class="form-control" onchange="cambioTipoSocio()">
									<option value="">[Seleccione Tipo Socio]</option> 
									<? while ($row_tipo_soc = $dbo->fetchArray($result_tipo_socio)) { ?> 
										<option value="<? echo $row_tipo_soc["Nombre"];  ?>" <? if ($frm["TipoSocio"] == $row_tipo_soc["Nombre"]) echo "selected"; ?>><? echo $row_tipo_soc["Nombre"];  ?></option> 
									<? } ?>
								</select> 
							<? } ?> 
						</div>
					</div>
				</div>
			<? } 
			
			if($rowGrupo['Grupo']=='Código Carné' && $_GET["action"] == "edit"){?>
				<div class="form-group first ">
					<div class="col-xs-12 col-sm-6">
						<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo Barras </label>
						<div class="col-sm-8">
							<? if (!empty($frm[CodigoBarras])) 
								echo "<img src='" . SOCIO_ROOT . "$frm[CodigoBarras]'>";?>
						</div>
					</div>
				</div>
				<div class="form-group first ">
					<div class="col-xs-12 col-sm-6">
						<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo QR </label>
						<div class="col-sm-8">
							<? if (!empty($frm[CodigoQR]))
								echo "<img src='" . SOCIO_ROOT . "qr/$frm[CodigoQR]'>";?>
						</div>
					</div>
				</div>
			<?}

			//Con el id del grupo se obtienen los campos activos en el club 	
			$sqlCm = $sqlCampo.$rowGrupo['IDGrupo']." ORDER BY cfs.IDCamposFormularioSocio";
			$resultCampo = $dbo->query($sqlCm);
			$i=0; 
			$j=1;

			while ($rowCampo = $dbo->fetchArray($resultCampo)){

				$nombreCampo = $rowCampo['Nombre'];
				$tipoCampo = $rowCampo['Tipo'];
				$idCampo = $rowCampo['CampoKey'];
				$class = "col-xs-12 ".$rowCampo['Clase']; 
				$txtCampo = $rowCampo['TxtCampo'];
				$value = $frm[$idCampo];
				
				$classDiv = '';
				$adDiv = '';

				// if($idCampo == 'AccionPadre'){
				// 	$classDiv = "hide contentAuxiliar contentBeneficiario";
				// 	$adDiv = 'id= "divdatos"';
				// }

				if($nombreCampo == 'NumeroDerecho' || $nombreCampo == 'Predio')
					$nombreCampo = $ArrayMensajesWeb[$tipo_club][$nombreCampo];
				

				//se agrega la clase mandatory para los campos que sean obligatorios
				if($rowCampo['Obligatorio'] == 'S')
					$class .= " mandatory ";

				//valida si existe la clase en el atributo "class" del campo, en caso de que no exista se agrega
				if($rowCampo['IdentificadorClase'] == 'S' && $rowCampo['NombreIdClase'] != ''){
					$valida = strpos($class, $rowCampo['NombreIdClase']);
					if ($valida === false)
						$class .= " ".$rowCampo['NombreIdClase'];
				}
				
				if($i==0)
					echo "<div class='form-group first'>";?>
				
				<div class="col-xs-12 col-sm-6 <? echo $classDiv ?>" <? echo $adDiv ?>>
					<label class="col-sm-4 control-label no-padding-right" id="label_<? echo $idCampo; ?>" for="<? echo $idCampo; ?>"><? echo $nombreCampo; ?></label>
					<div class="col-sm-8" id="div_<?= $idCampo; ?>"><?

						if($tipoCampo == "text" || $tipoCampo == "number" || $tipoCampo == "date" || $tipoCampo == "time" || $tipoCampo == "email" || $tipoCampo == "password"){
							$txtAd = "";
							
							if($tipoCampo == "text"){
								$txtAd = "pattern='[A-Za-z]+'";
							}
							else if($tipoCampo == "number"){
								$txtAd = "onkeypress='return (event.charCode >= 48 && event.charCode <= 57)'";
							}

							if($tipoCampo == "date"){
								$tipoCampo = "text";
								$class .= " calendar";
							}

							echo "<input type='$tipoCampo' id='$idCampo' name='$idCampo' title='$nombreCampo' placeholder='$txtCampo' class='$class' value='$value' $txtAd>";
							
						}
						else if($tipoCampo == "textarea"){
							echo "<textarea id='$idCampo' name='$idCampo' title='$nombreCampo' cols='10' rows='5' placeholder='$txtCampo' class='$class'>$value</textarea>";
						}
						else if($tipoCampo == "file"){
							$file = $frm[$idCampo];
							if (!empty($file)) {
								echo "<img src='" . SOCIO_ROOT . "$file' width=55 >";
								echo "<a href='socio.php?action=delfoto&foto=$file&campo=Foto&id=" . $frm[$key]."' class='ace-icon glyphicon glyphicon-trash'>&nbsp;</a>";
							}

							echo "<input type='$tipoCampo' id='$idCampo' name='$idCampo' title='$nombreCampo' class='$class' size='30' style='font-size: 12px'>";
						}
						else if($tipoCampo == "radio" || $tipoCampo == "checkbox" || $tipoCampo == "select"){
							$tipoOpciones = $rowCampo['TipoOpciones'];
							$arrOpciones = array();
							
							switch($tipoOpciones){
								case 1:
									$opcionesSeleccion = $rowCampo['OpcionesSeleccion'];
									$opciones = explode("|",$opcionesSeleccion);

									foreach($opciones as $valores){
										$valores = explode("=",$valores);
										$cont = count($valores);
										
										if($cont > 0){
											$nombre = $valores[0];
											$valor = $cont == 2 ? $valores[1] : $valores[0];

											$arrOpciones[$valor] = $nombre;
										}
									}
									
									$arrOpciones = array_flip($arrOpciones);								
								break;
								case 2:
									$tablaCampo = $rowCampo['NombreTabla'];
									$campoKey = $rowCampo['CampoName'];
									$campoValue = $rowCampo['CampoValue'];
									$where = $rowCampo['Condicion'] != "" ? "WHERE ".$rowCampo['Condicion'] : "";
									$where = str_replace("?IDClub",SIMUser::get("club"),$where);
									
									if($tablaCampo != '' && $campoKey != '' && $campoValue != ''){
										$qry = "SELECT $campoKey,$campoValue FROM $tablaCampo $where ORDER BY $campoKey";
										$rsta = $dbo->query($qry);

										while ($row = $dbo->fetchArray($rsta)){
											$nom = $row[$campoKey];
											$val = $row[$campoValue];

											$arrOpciones[$val] = $nom;
										}
									}

									$arrOpciones = array_flip($arrOpciones);
								break;
								case 3:
									$qry = $rowCampo['ConsultaBD'];
									if($qry != ''){
										
										$qryNew = str_replace("?IDClub",SIMUser::get("club"),$qry);
										$rsta = $dbo->query($qryNew);

										while ($row = $dbo->fetchArray($rsta)){
											$nom = $row[0];
											$val = isset($row[1]) ? $row[1] : $row[0];

											$arrOpciones[$val] = $nom;
										}
									}

									$arrOpciones = array_flip($arrOpciones);
								break;
								case 4:
									$opClase = $rowCampo['OpcionesClase'];
									if($opClase != ''){
										$validaCl = strpos($opClase, ";");
										if ($validaCl === false)
											$opClase .=";";

										eval('$arrOpciones = '.$opClase);
									}
								break;
							}

							if(!empty($arrOpciones)){
								if($tipoCampo == "radio"){
									$classR = $class;
									if(count($arrOpciones)<5)
										$classR = str_replace("col-xs-12", "", $class);

									echo SIMHTML::formradiogroup($arrOpciones, $value, $idCampo,"",$classR);
								}
								else if($tipoCampo == "checkbox"){
									$attr = "onclick = funcionCheck(\"$idCampo\")";
									$classCh = str_replace("col-xs-12", "", $class);

									$arrValue = array();
									if($value != ''){
										$arrValue = explode(",", $value);
									}

									echo SIMHTML::formCheckGroup2($arrOpciones, $arrValue, $idCampo."List" ,$attr ,$classCh);
									echo "<input type='hidden' id='$idCampo' name='$idCampo' value='$value'>";

								}else if($tipoCampo == "select"){
									$attrs = "";
									if($idCampo == 'IDEstdoZeus')
										$attrs = "disabled";
									
									echo SIMHTML::formPopupArray(array_flip($arrOpciones) ,$value ,$idCampo ,$txtCampo ,$class, $attrs);
								}
							}
						}?>
					</div>
				</div><?

				if($i==1 || $j==$cantidad ){ 
					echo "</div>";
					$i=0;
				}else{
					$i++;
				}
				$j++;
			}

			if($rowGrupo['Grupo']=='Datos Básicos'){ ?>
				<? if ($_GET["action"] == "edit"){ ?> 
					<div class="form-group first ">
						<div class="col-xs-12 col-sm-6">
							<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Registrar huella </label>
							<div class="col-sm-8">
								<a id="huella" class="fancybox" href="<? echo URLROOT . '/admin/lib/huella/huellasocio.php?IDSocio=' . $frm["IDSocio"] . '&Nombre=' . $frm["Nombre"] ?>" data-fancybox-type="iframe">CLIC PARA REGISTRAR HUELLA</a>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Movimientos </label>
							<div class="col-sm-8">
								<a id="huella" class="fancybox" href="facturasocio.php?IDSocio=<? echo $frm["IDSocio"]; ?>&IDClub=<? echo $frm["IDClub"] ?>" data-fancybox-type="iframe">Clic para ver movimientos</a>
							</div>
						</div>
					</div> 
				<?}
			} 
		} 

		if (!empty($frm["IDSocio"])){ ?> 
			<div class="widget-header widget-header-large">
				<h3 class="widget-title grey lighter">
					<i class="ace-icon fa fa-user green"></i> Datos Perfil
				</h3>
			</div> 
			<?php
				$sql_campos = "SELECT SCES.Valor,CED.Nombre FROM CampoEditarSocio CED, SocioCampoEditarSocio SCES
								WHERE SCES.IDCampoEditarSocio=CED.IDCampoEditarSocio AND SCES.IDSocio='" . $frm["IDSocio"] . "'
								Group by SCES.IDCampoEditarSocio
								Order by CED.Orden";
				$r_campos = $dbo->query($sql_campos);
				while ($r = $dbo->object($r_campos)) {
					$array_preguntas[] = $r->Nombre;
					$array_respuesta[] = $r->Valor;
				}
			?> 
			<table id="simple-table" class="table table-striped table-bordered table-hover">
				<tr> 
					<?php foreach ($array_preguntas as $key_pregunta => $value_pregunta) { ?> 
						<th><?php echo $value_pregunta; ?></th> 
					<?php } ?> 
				</tr>
				<tbody id="listacontactosanunciante">
					<tr> 
						<?php foreach ($array_respuesta as $key_respuesta => $value_respuesta) {  ?> 
							<td><?php echo $value_respuesta; ?></td> 
						<?php } ?> 
					</tr>
				</tbody>
			</table>
			<div class="widget-header widget-header-large">
				<h3 class="widget-title grey lighter">
					<i class="ace-icon fa fa-credit-card green"></i> Vinculo Familiar
				</h3>
			</div>
			<div class="form-group first ">
				<div class="col-xs-12 col-sm-12">
					<table id="simple-table" class="table table-striped table-bordered table-hover">
						<tr>
							<th>Foto</th>
							<th>Nombre</th>
							<th>Tipo</th>
						</tr>
						<tbody id="listacontactosanunciante"> 
							<?if (empty($frm["AccionPadre"])) :
								$condicion_nucleo = " and  AccionPadre = '" . $frm["Accion"] . "' ";
							else :
								$condicion_nucleo = " and  (AccionPadre = '" . $frm["AccionPadre"] . "' or Accion = '" . $frm["AccionPadre"] . "')";
							endif;
		
							$r_vinculo = &$dbo->all("Socio", "IDClub = '" . $frm[IDClub] . "' and IDSocio <> '" . $frm["IDSocio"] . "' and IDEstadoSocio <> 2 " . $condicion_nucleo . "");
							while ($r = $dbo->object($r_vinculo)) { ?> 
							
								<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
									<td>
										<? if (!empty($r->Foto)) :
											echo "<img src='" . SOCIO_ROOT . "$r->Foto' width=55 >";
											endif; ?>
									</td>
									<td><a href="socios.php?action=edit&id=<?php echo $r->IDSocio ?>"><?php echo $r->Nombre . " " . $r->Apellido; ?></a></td>
									<td><?php echo $r->TipoSocio; ?></td>
								</tr> 
		
							<? } ?> 
						</tbody>
						<tr>
							<th class="texto" colspan="13"></th>
						</tr>
					</table>
				</div>
			</div> 
		<? }
		
		if ($frm["TipoSocio"] == "Cortesia")
			$oculta_cortesia = "";
		else
			$oculta_cortesia = "hide";?> 
		
		<div class="contentCortesia <?php echo $oculta_cortesia; ?>  contentAuxiliar">
			<h4 class="blue ">
				<i class="ace-icon fa fa-check bigger-110"></i> Cortesía
			</h4>
			<div class="form-group first ">
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Inicio</label>
					<div class="col-sm-8">
						<input type="text" id="FechaInicioCortesia" name="FechaInicioCortesia" placeholder="Fecha de Inicio Cortesía" class="col-xs-12 calendar" title="fecha de inicio cortesía" value="<?php echo $frm["FechaInicioCortesia"]; ?>">
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Fin </label>
					<div class="col-sm-8">
						<input type="text" id="FechaFinCortesia" name="FechaFinCortesia" placeholder="Fecha de Fin Cortesía" class="col-xs-12 calendar" title="fecha de fin cortesía" value="<?php echo $frm["FechaFinCortesia"]; ?>">
					</div>
				</div>
			</div>
		</div> 
		<?php 
			if ($frm["TipoSocio"] == "Canje")
				$oculta_canje = "";
			else
				$oculta_canje = "hide";
		?> 
		<div class="contentCanje <?php echo $oculta_canje; ?>  contentAuxiliar" id="divdatosCanje">
			<h4 class="blue ">
				<i class="ace-icon fa fa-check bigger-110"></i> Canje
			</h4>
			<div class="form-group first ">
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Inicio</label>
					<div class="col-sm-8">
						<input type="text" id="FechaInicioCanje" name="FechaInicioCanje" placeholder="Fecha de Inicio Canje" class="col-xs-12 calendar" title="fecha de inicio canje" value="<?php echo $frm["FechaInicioCanje"]; ?>">
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Fin </label>
					<div class="col-sm-8">
						<input type="text" id="FechaFinCanje" name="FechaFinCanje" placeholder="Fecha de Fin Canje" class="col-xs-12 calendar" title="fecha de fin canje" value="<?php echo $frm["FechaFinCanje"]; ?>">
					</div>
				</div>
			</div>
			<div class="form-group first ">
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Club Canje </label>
					<div class="col-sm-8">
						<select name="ClubCanje" id="ClubCanje">
							<option value=""></option> 
							<?php
								$sql_club = string;
								$sql_club = "Select * From ListaClubes Where Publicar = 'S' order by Nombre";
								$qry_club = $dbo->query($sql_club);
								while ($r_club = $dbo->fetchArray($qry_club)) : ?> 
									<option value="<?php echo $r_club["IDListaClubes"]; ?>" <?php if ($r_club["IDListaClubes"] == $frm["ClubCanje"]) echo "selected";  ?>><?php echo $r_club["Nombre"]; ?></option> 
								<? endwhile; ?>
						</select>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de Canje </label>
					<div class="col-sm-8">
						<?php
						$arreglo = array("Nacional" => "Nacional", "Internacional" => "Internacional");
						echo SIMHTML::formradiogroup(array_flip($arreglo), $frm["TipoCanje"], 'TipoCanje', "class='input'");
						?>
					</div>
				</div>
			</div>			
		</div> 
		<?php if ($frm["TipoSocio"] == "Invitado")
				$oculta_invitado = "";
			else
				$oculta_invitado = "hide";
			?> 
		<div class="contentInvitado <?php echo $oculta_invitado; ?>  contentAuxiliar" id="divdatosInvitado">
			<h4 class="blue ">
				<i class="ace-icon fa fa-check bigger-110"></i> Invitado Fechas
			</h4>
			<div class="form-group first ">
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Inicio</label>
					<div class="col-sm-8">
						<input type="text" id="FechaInicioInvitado" name="FechaInicioInvitado" placeholder="Fecha de Inicio Invitado" class="col-xs-12 calendar" title="fecha de inicio invitado" value="<?php echo $frm["FechaInicioInvitado"]; ?>">
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Fin </label>
					<div class="col-sm-8">
						<input type="text" id="FechaFinInvitado" name="FechaFinInvitado" placeholder="Fecha de Fin Invitado" class="col-xs-12 calendar" title="fecha de fin Invitado" value="<?php echo $frm["FechaFinInvitado"]; ?>">
					</div>
				</div>
			</div>
		</div>
		<?php if ($frm["TipoSocio"] == "Arrendatario")
				$oculta_arrendatario = "";
			else
				$oculta_arrendatario = "hide";
			?> 
		<div class="contentArrendatario <?php echo $oculta_arrendatario; ?>  contentAuxiliar" id="divdatosArrendatario">
			<h4 class="blue ">
				<i class="ace-icon fa fa-check bigger-110"></i> Fechas de Arrendatario 
			</h4>
			<div class="form-group first ">
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Inicio</label>
					<div class="col-sm-8">
						<input type="text" id="FechaInicioArrendatario" name="FechaInicioArrendatario" placeholder="Fecha de Inicio Arrendatario" class="col-xs-12 calendar" title="fecha de inicio arrendatario" value="<?php echo $frm["FechaInicioArrendatario"]; ?>">
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Fin </label>
					<div class="col-sm-8">
						<input type="text" id="FechaFinArrendatario" name="FechaFinArrendatario" placeholder="Fecha de Fin Arrendatario" class="col-xs-12 calendar" title="fecha de fin arrendatario" value="<?php echo $frm["FechaFinArrendatario"]; ?>">
					</div>
				</div>
			</div>
		</div>
	<? } catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
	}?>

	<div class="clearfix form-actions">
		<div class="col-xs-12 text-center">
			<input type="hidden" name="ID" id="ID" value="<? echo $frm[$key] ?>" />
			<input type="hidden" name="action" id="action" value="<? echo $newmode ?>" />
			<input type="hidden" name="IDClub" id="IDClub" value="<? if (empty($frm["IDClub"])) echo SIMUser::get("club");
																	else echo $frm["IDClub"];  ?>" />
			<input type="hidden" name="ClaveAnt" id="ClaveAnt" value="<?= $frm[Clave] ?>" />
			<input type="hidden" name="SegundaClaveAnt" id="SegundaClaveAnt" value="<?= $frm[SegundaClave] ?>" />
			<input type="hidden" name="EmailAnt" id="EmailAnt" value="<?= $frm[Email] ?>" />
			<input type="hidden" name="IDEstadoSocioAnt" id="IDEstadoSocioAnt" value="<?= $frm[IDEstadoSocio] ?>" />
			<input type="hidden" name="NumeroDocumentoAnt" id="NumeroDocumentoAnt" value="<?= $frm[NumeroDocumento] ?>" />
			<button class="btn btn-info btnEnviar" type="button" rel="frmSocio">
				<i class="ace-icon fa fa-check bigger-110"></i> <? echo $titulo_accion; ?> Socio </button>
		</div>
	</div>
</form>
<?

	try{
		echo "<script type='text/javascript'> 
		try { ";

		$resultGrupoFun = $dbo->query($sqlGrupo);
		while ($rowGrupoFun = $dbo->fetchArray($resultGrupoFun)){ 

			$resultCampoFun = $dbo->query($sqlCampo.$rowGrupoFun['IDGrupo']);
			while ($rowCampoFun = $dbo->fetchArray($resultCampoFun)){
				$tipoCampo = $rowCampoFun['Tipo'];
				$campoKey = $rowCampoFun['CampoKey'];
				$agregarFuncion = $rowCampoFun['AgregarFuncion'];
				$idClase = $rowCampoFun['IdentificadorClase'];
				$nombreIdClase = $rowCampoFun['NombreIdClase'];

				if($idClase == 'S' && $nombreIdClase != ''){
					$idCampo = "$('.$nombreIdClase')";
				}else if($tipoCampo == 'radio'){
					$idCampo = "$(\"input[name='$campoKey']\")";
				}else if($tipoCampo == 'checkbox'){
					$campoKey .= 'List';
					$idCampo = "$(\"input[name='$campoKey']\")";
				}else{
					$idCampo = "$('#$campoKey')";
				}

				if($agregarFuncion == 'S'){
					$tipoFuncion = $rowCampoFun['TipoFuncion'];
					$funcion = $rowCampoFun['Funcion'];

					switch($tipoFuncion){
						case 1:
							echo "$idCampo.change(function (){
									try{
										$funcion 
									} catch (error) {
										alert('La funcion presenta errores o su contenido no existe. Error: '+error);
										console.error(error);
									}
								}); ";
						break;		
						case 2:
							echo "$idCampo.on('click',function(){ 
								try{
									$funcion 
								} catch (error) {
									alert('La funcion presenta errores o su contenido no existe. Error: '+error);
									console.error(error);
								}
							}); ";
						break;
						case 3:
							echo "$idCampo.on('keyup',function(){ 
								try{
									$funcion 
								} catch (error) {
									alert('La funcion presenta errores o su contenido no existe. Error: '+error);
									console.error(error);
								}
							}); ";
						break;	
					}
				}
			}
		}
		echo " } catch (error) {
				alert('El ingreso de funciones ha sido invalido. Error: '+error);
				console.error(error);
			} 
		</script>";
	}catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
	}
?>
<!-- zona para colocar funciones estaticas -->
<script>
	try {
		cambioTipoSocio();
		selDepartamentos();

		function cambioTipoSocio(){
			var tiposocio = $('#TipoSocio').val();
			var label = '<?= $ArrayMensajesWeb[$tipo_club]['AccionSocioTitular']; ?>';
			
			if(tiposocio == 'Beneficiario')
				label = '<?= $ArrayMensajesWeb[$tipo_club]['AccionPadre']; ?>';


			var $idLabel = $("#label_AccionPadre");
			$idLabel.html($idLabel.text().trim().replace($idLabel.text(), label));

			$(".contentAuxiliar input").removeClass("mandatory");
			$(".contentAuxiliar").addClass("hide");
			$(".content"+tiposocio).removeClass("hide");
			$(".content"+tiposocio+" input").addClass("mandatory");
		
			if(tiposocio == 'Canje' || tiposocio == 'Invitado' || tiposocio == 'Cortesia'){
				$(".contentBeneficiario").removeClass("hide");
				$(".contentBeneficiario input").addClass("mandatory");
			}
		}

		function funcionCheck(idCampo){
			var arrValues = new Array();
			var txtCam = "#"+idCampo+"List:checked";
			var txtIn = "#"+idCampo;

			$(txtCam).each( function () {
				if($(this).is(':checked')){
					arrValues.push($(this).val());
				}
			});
			
			var stringValues = arrValues.toString();
			$(txtIn).val(stringValues);
		}

		function selDepartamentos(){
			if($("#IDCiudadDian").length){
				let idPaisDian = $('#IDPaisDian').val();
				let idDepartamentoDian = '<?= $frm['IDDepartamentoDian'];?>';

				jQuery.ajax({
					type: "GET",
					data: {
						oper: "form",
						proceso: "departamentos",
						idPais: idPaisDian,
						idDepartamento: idDepartamentoDian
					},
					dataType: "html",
					url: "includes/async/socios.async.php",
					success: function (data) {
						$("#div_IDDepartamentoDian").html(data);
						selCiudades();
					}
				}); 
			}
		}

		function selCiudades(){
			if($("#IDCiudadDian").length){
				let idDepartamentoDian = $('#IDDepartamentoDian').val();
				let idCiudadDian = '<?= $frm['IDCiudadDian'];?>';

				jQuery.ajax({
					type: "GET",
					data: {
						oper: "form",
						proceso: "ciudades",
						idDepartamento: idDepartamentoDian,
						idCiudad: idCiudadDian
					},
					dataType: "html",
					url: "includes/async/socios.async.php",
					success: function (data) {
						$("#div_IDCiudadDian").html(data);
					}
				}); 
			}
		}
	} catch (error) {
		console.error(error);
	}
</script>