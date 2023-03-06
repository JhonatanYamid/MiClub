<form class="form-horizontal formvalida" role="form" method="post" id="EditTipoSocioModulo<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data"> <?php
                  $action = "InsertarModuloTipoSocio";

                  if( $_GET["IDTipoSocioModulo"] )
                  {
                          $EditTipoSocioModulo =$dbo->fetchAll("TipoSocioModulo"," IDTipoSocioModulo = '".$_GET["IDTipoSocioModulo"]."' ","array");
                          $action = "ModificaModuloTipoSocio";
                          ?> <input type="hidden" name="IDTipoSocioModulo" id="IDTipoSocioModulo" value="<?php echo $EditTipoSocioModulo["IDTipoSocioModulo"]?>" /> <?php
                  }
                  ?> 
	<div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Socio: </label>
            <div class="col-sm-8">
                <select class="TipoSocio" name="TipoSocio" class="form-control">
                    <option value=""></option> <?php
													$sql_tipò="SELECT TS.Nombre Nombre
																			FROM ClubTipoSocio CTS, TipoSocio TS
																			WHERE CTS.IDTipoSocio=TS.IDTipoSocio
																			AND CTS.IDClub='".$_GET["id"]."'";
													$r_tipo	= $dbo->query($sql_tipò);
													while ($row_tipo=$dbo->fetchArray($r_tipo)){ ?> <option value="<?php echo $row_tipo["Nombre"]; ?>" <?php if($row_tipo["Nombre"]==$EditTipoSocioModulo["TipoSocio"]) echo "selected"; ?>><?php echo $row_tipo["Nombre"]; ?></option> <?php } ?>
                </select>
                <?
													//$tipo_socio = SIMResources::$tipo_socio;
													//echo SIMHTML::formPopupArray( $tipo_socio  ,  $EditTipoSocioModulo["TipoSocio"] , "TipoSocio" ,  "Seleccione tipo" , "form-control"  );
											?>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modulos </label>
            <div class="col-sm-8">
                <div style="width:300px;">
                    <select multiple class="chosen-select form-control" name="IDModulo[]" id="IDModulo" data-placeholder="Selecciones valores..."> <?php
																		$r_valor_tabla = $dbo->all( "ClubModulo" , "IDClub = '".$frm[ $key ]."' and Activo = 'S' Order By Titulo");

																		$valores_guardados = $EditTipoSocioModulo["IDModulo"] ;
																		if(!empty($valores_guardados)):
																			$array_valores_guardados = explode("|",$valores_guardados);
																		endif;


					                        while( $r_valor = $dbo->object( $r_valor_tabla ) ){


																			if(empty(trim($r_valor->Titulo))){
																					$NombreModulo = $dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '" . $r_valor->IDModulo . "'" );
																			}
																			else{
																					$NombreModulo = $r_valor->TituloLateral;
																			}

																		  if(empty($valores_guardados)):
																				$seleccionar = "";
																			elseif(in_array($r_valor->IDModulo,$array_valores_guardados)):
																				$seleccionar = "selected";
																			else:
																				$seleccionar = "";
																			endif;

																		  ?> <option value="<?php echo $r_valor->IDModulo ?>" <?php echo $seleccionar; ?>><?php echo $NombreModulo; ?></option>
                        <? }	?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Servicios Reservar </label>
            <div class="col-sm-8">
                <div style="width:300px;">
                    <select multiple class="chosen-select form-control" name="IDServicioMaestro[]" id="IDServicioMaestro" data-placeholder="Selecciones valores..."> <?php
																		unset($array_valores_guardados);
																		$r_valor_tabla = $dbo->all( "ServicioClub" , "IDClub = '".$frm[ $key ]."' and Activo='S' Order By TituloServicio");
																		$valores_guardados = $EditTipoSocioModulo[IDServicioMaestro] ;
																		if(!empty($valores_guardados)):
																			$array_valores_guardados = explode("|",$valores_guardados);
																		endif;

					                          while( $r_valor = $dbo->object( $r_valor_tabla ) ){

																			if(empty($r_valor->TituloServicio)){
																					$NombreServicio = utf8_encode($dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $r_valor->IDServicioMaestro . "'" ));
																			}
																			else{
																				$NombreServicio = $r_valor->TituloServicio;
																			}


																		  if(empty($valores_guardados)):
																				$seleccionar = "";
																			elseif(in_array($r_valor->IDServicioMaestro,$array_valores_guardados)):
																				$seleccionar = "selected";
																			else:
																				$seleccionar = "";
																			endif;

																		  ?> <option value="<?php echo $r_valor->IDServicioMaestro ?>" <?php echo $seleccionar; ?>><?php echo $NombreServicio; ?></option>
                        <? }	?>
                    </select>
                </div>
            </div>
        </div>
    </div>

	<div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar estos modulos o servicios: </label>
            <div class="col-sm-8">
				<?php echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["Ocultar"] , 'Ocultar' , "class='input'" ) ?>

            </div>
        </div>
    </div>

    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
            <input type="submit" class="submit" value="Guardar">
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ $key ]?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
        </div>
    </div>
</form>
<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64">Editar</th>
        <th>TipoSocio</th>
        <th>Modulos</th>
        <th>Servicios</th>
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante"> <?php

                              $r_documento =& $dbo->all( "TipoSocioModulo" , "IDClub = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?> <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
            <td align="center" width="64">
                <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDTipoSocioModulo=".$r->IDTipoSocioModulo?>&tabclub=tiposociomodulo" class="ace-icon glyphicon glyphicon-pencil"></a>
            </td>
            <td><?php echo $r->TipoSocio; ?></td>
            <td><?php
															$array_id_modulo=explode("|",$r->IDModulo);
															$nombre_modulo="";
															if(count($array_id_modulo)>0){
																		foreach($array_id_modulo as $id_club_modulo){
																				$nombre_modulo = $dbo->getFields( "ClubModulo" , "Titulo" , "IDModulo = '" . $id_club_modulo . "' and IDClub = '".$r->IDClub."'" );
																				if(empty(trim($nombre_modulo))){
																						$nombre_modulo = $dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '" . $id_club_modulo . "'" );
																				}
																				echo utf8_encode($nombre_modulo)."<br>";
																		}
															}

															?></td>
            <td> <?php
																$array_id_servicio=explode("|",$r->IDServicioMaestro);
																$nombre_servicio="";
																if(count($array_id_servicio)>0){
																			foreach($array_id_servicio as $id_servicio){
																					$nombre_servicio = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDServicioMaestro = '" . $id_servicio . "' and IDClub = '".$frm[$key]."'" );
																					if(empty($nombre_servicio)){
																							$nombre_servicio = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio . "'" );
																					}
																					echo utf8_encode($nombre_servicio)."<br>";
																			}
																}

																?> </td>
            <td align="center" width="64">
                <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaModuloTipoSocio&id=<?php echo $frm[$key];?>&IDTipoSocioModulo=<? echo $r->IDTipoSocioModulo ?>&tabclub=tiposociomodulo"></a>
            </td>
        </tr> <?php
                      }
                      ?> </tbody>
    <tr>
        <th class="texto" colspan="16"></th>
    </tr>
</table>