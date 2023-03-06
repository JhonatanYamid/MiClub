<form class="form-horizontal formvalida" role="form" method="post" id="SubModulos<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-sitemap green"></i> Configuracion de Modulos
        </h3>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-12">
            <table id="simple-table" class="table table-striped table-bordered table-hover">
                <tr>
                    <th>Modulo</th>
                    <th>Asociar Reservas</th>
                    <th>Seccion Noticias</th>
                    <th>Seccion Noticias 2</th>
                    <th>Seccion Noticias 3</th>
                    <th>Seccion Noticias Infinitas</th>
                    <th>Seccion Eventos</th>
                    <th>Seccion Galerias</th>
                    <th>Tipo Archivos</th>
                    <th>Modulos</th>
                    <th>Ver boton Mis Reservas?</th>
                </tr>
                <tbody id="listacontactosanunciante"> <?php

                    $r_modulo =& $dbo->all( "ClubModulo" , "IDClub = '".$frm[IDClub]."' and Activo = 'S'");
                    $r_valor_tabla_servicio =& $dbo->all( "ServicioClub" , "IDClub = '".$frm[IDClub]."' and Activo = 'S'");
                    while( $r_valor = $dbo->object( $r_valor_tabla_servicio ) ):
                    $array_servicios[]= $r_valor;
                    endwhile;

                    $r_valor_tabla_not =& $dbo->all( "Seccion" , "IDClub = '".$frm[IDClub]."' and Publicar = 'S'");
                    while( $r_valor_not = $dbo->object( $r_valor_tabla_not ) ):
                        $array_secc_not[]= $r_valor_not;
                    endwhile;

                    $r_valor_tabla_not2 =& $dbo->all( "Seccion2" , "IDClub = '".$frm[IDClub]."' and Publicar = 'S'");
                    while( $r_valor_not2 = $dbo->object( $r_valor_tabla_not2 ) ):
                        $array_secc_not2[]= $r_valor_not2;
                    endwhile;

                    $r_valor_tabla_not3 =& $dbo->all( "Seccion3" , "IDClub = '".$frm[IDClub]."' and Publicar = 'S'");
                    while( $r_valor_not3 = $dbo->object( $r_valor_tabla_not3 ) ):
                        $array_secc_not3[]= $r_valor_not3;
                    endwhile;

                    $r_valor_tabla_notI =& $dbo->all( "SeccionNoticiaInfinita" , "IDClub = '".$frm[IDClub]."' and Publicar = 'S'");
                    while( $r_valor_notI = $dbo->object( $r_valor_tabla_notI ) ):
                        $array_secc_notI[]= $r_valor_notI;
                    endwhile;

                    $r_valor_tabla_eve =& $dbo->all( "SeccionEvento" , "IDClub = '".$frm[IDClub]."' and Publicar = 'S'");
                    while( $r_valor_eve = $dbo->object( $r_valor_tabla_eve ) ):
                    $array_secc_eve[]= $r_valor_eve;
                    endwhile;

                    $r_valor_tabla_gal =& $dbo->all( "SeccionGaleria" , "IDClub = '".$frm[IDClub]."' and Publicar = 'S'");
                    while( $r_valor_gal = $dbo->object( $r_valor_tabla_gal ) ):
                    $array_secc_gal[]= $r_valor_gal;
                    endwhile;

                    $r_valor_tabla_arch =& $dbo->all( "TipoArchivo" , "IDClub = '".$frm[IDClub]."' and Publicar = 'S'");
                    while( $r_valor_arch = $dbo->object( $r_valor_tabla_arch ) ):
                    $array_tipo_arch[]= $r_valor_arch;
                    endwhile;

                    $r_sql_servicio =& $dbo->all( "Servicio" , "IDClub = '".$frm[IDClub]."'");
                    while( $r_valor_servicio = $dbo->object( $r_sql_servicio ) ):
                    $array_datos_servicio[$r_valor_servicio->IDServicioMaestro]= $r_valor_servicio->IDServicio;
                    endwhile;

                    $r_sql_modulo =& $dbo->all( "Modulo" , " IDModulo in (102,8,10,12,44,6,13,14,9,33,98,112,113,7) ");
                    while( $r_valor_modulo = $dbo->object( $r_sql_modulo ) ):
                    $array_datos_modulo[]= $r_valor_modulo;
                    endwhile;



                    while( $r = $dbo->object( $r_modulo ) )
                    {
                        unset($array_valores_guardados);?> 
                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                            <td width="10%" aling="center">
                                <? if (!empty($r->Icono)) {
                                    echo "<img src='".MODULO_ROOT."$r->Icono' width=40 >";
                                }// END if
                                ?> <?php echo "<br>". utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '" . $r->IDModulo . "'" )); ?>
                            </td>
                        <td width="200px"> 
                            <?php $datos_submodulo = $dbo->fetchAll( "SubModulo", "IDClub = '" . $frm[IDClub] . "' and IDModulo = '".$r->IDModulo."'", "array" ); ?> 
                            <select multiple class=" chosen-select form-control" name="Servicio<?php echo $r->IDModulo; ?>[]" id="Servicio<?php echo $r->IDModulo; ?>" data-placeholder="Seleccione servicios..."> <?php

                                foreach( $array_servicios as $id => $r_valor):
                                    //$IDServicio = $dbo->getFields( "Servicio" , "IDServicio" , "IDServicioMaestro='" . $r_valor->IDServicioMaestro . "' and IDClub = '".$frm["IDClub"]."'" );
                                    $IDServicio = $array_datos_servicio[$r_valor->IDServicioMaestro];

                                    $valores_guardados = $datos_submodulo["IDServicio"];
                                    if(!empty($valores_guardados)):
                                        $array_valores_guardados = explode("|",$valores_guardados);
                                    endif;

                                    if(empty($valores_guardados)):
                                        $seleccionar = "";
                                    elseif(in_array($IDServicio,$array_valores_guardados)):
                                        $seleccionar = "selected";
                                    else:
                                        $seleccionar = "";
                                    endif;

                                    ?> <option value="<?php echo $IDServicio ?>" <?php echo $seleccionar; ?>> <?php
                                    if(!empty($r_valor->TituloServicio)):
                                        echo $r_valor->TituloServicio;
                                    else:
                                        echo $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $r_valor->IDServicioMaestro . "'" );
                                    endif;

                                        ?> </option>
                                <? endforeach;?>
                            </select>
                        </td>
                        <td width="200px">
                            <select multiple class=" chosen-select form-control" name="SeccionNoticia<?php echo $r->IDModulo; ?>[]" id="SeccionNoticia<?php echo $r->IDModulo; ?>" data-placeholder="Seleccione secciones noticia..."> <?php
														  unset($array_valores_guardados);

                                foreach( $array_secc_not as $id => $r_valor):
                                    $valores_guardados = $datos_submodulo["IDSeccionNoticia"];
                                    if(!empty($valores_guardados)):
                                        $array_valores_guardados = explode("|",$valores_guardados);
                                    endif;

                                    if(empty($valores_guardados)):
                                        $seleccionar = "";
                                    elseif(in_array($r_valor->IDSeccion,$array_valores_guardados)):
                                        $seleccionar = "selected";
                                    else:
                                        $seleccionar = "";
                                    endif;

                                    ?> <option value="<?php echo $r_valor->IDSeccion ?>" <?php echo $seleccionar; ?>> <?php  echo $r_valor->Nombre; ?> </option>
                                <? endforeach;?>
                            </select>
                        </td>
                        <td width="200px">
                            <select multiple class=" chosen-select form-control" name="SeccionNoticia2<?php echo $r->IDModulo; ?>[]" id="SeccionNoticia2<?php echo $r->IDModulo; ?>" data-placeholder="Seleccione secciones noticia..."> <?php
														  unset($array_valores_guardados);

                                foreach( $array_secc_not2 as $id => $r_valor):
                                    $valores_guardados = $datos_submodulo["IDSeccionNoticia2"];
                                    if(!empty($valores_guardados)):
                                        $array_valores_guardados = explode("|",$valores_guardados);
                                    endif;

                                    if(empty($valores_guardados)):
                                        $seleccionar = "";
                                    elseif(in_array($r_valor->IDSeccion,$array_valores_guardados)):
                                        $seleccionar = "selected";
                                    else:
                                        $seleccionar = "";
                                    endif;

                                    ?> <option value="<?php echo $r_valor->IDSeccion ?>" <?php echo $seleccionar; ?>> <?php  echo $r_valor->Nombre; ?> </option>
                                <? endforeach;?>
                            </select>
                        </td>
                        <td width="200px">
                            <select multiple class=" chosen-select form-control" name="SeccionNoticia3<?php echo $r->IDModulo; ?>[]" id="SeccionNoticia3<?php echo $r->IDModulo; ?>" data-placeholder="Seleccione secciones noticia..."> <?php
														  unset($array_valores_guardados);

                                foreach( $array_secc_not3 as $id => $r_valor):
                                    $valores_guardados = $datos_submodulo["IDSeccionNoticia3"];
                                    if(!empty($valores_guardados)):
                                        $array_valores_guardados = explode("|",$valores_guardados);
                                    endif;

                                    if(empty($valores_guardados)):
                                        $seleccionar = "";
                                    elseif(in_array($r_valor->IDSeccion,$array_valores_guardados)):
                                        $seleccionar = "selected";
                                    else:
                                        $seleccionar = "";
                                    endif;

                                    ?> <option value="<?php echo $r_valor->IDSeccion ?>" <?php echo $seleccionar; ?>> <?php  echo $r_valor->Nombre; ?> </option>
                                <? endforeach;?>
                            </select>
                        </td>
                        <td width="200px">
                            <select multiple class=" chosen-select form-control" name="SeccionNoticiaInfi<?php echo $r->IDModulo; ?>[]" id="SeccionNoticiaInfi<?php echo $r->IDModulo; ?>" data-placeholder="Seleccione secciones noticia..."> <?php
														  unset($array_valores_guardados);

                                foreach( $array_secc_notI as $id => $r_valor):
                                    $valores_guardados = $datos_submodulo["IDSeccionNoticiaInfi"];
                                    if(!empty($valores_guardados)):
                                        $array_valores_guardados = explode("|",$valores_guardados);
                                    endif;

                                    if(empty($valores_guardados)):
                                        $seleccionar = "";
                                    elseif(in_array($r_valor->IDSeccionNoticiaInfinita,$array_valores_guardados)):
                                        $seleccionar = "selected";
                                    else:
                                        $seleccionar = "";
                                    endif;

                                    ?> <option value="<?php echo $r_valor->IDSeccionNoticiaInfinita  ?>" <?php echo $seleccionar; ?>> <?php  echo $r_valor->Nombre; ?> </option>
                                <? endforeach;?>
                            </select>
                        </td>
                        <td width="200px">
                            <select multiple class=" chosen-select form-control" name="SeccionEvento<?php echo $r->IDModulo; ?>[]" id="SeccionEvento<?php echo $r->IDModulo; ?>" data-placeholder="Seleccione secciones eventos..."> <?php
														  unset($array_valores_guardados);
                                                           foreach( $array_secc_eve as $id => $r_valor):
																		$valores_guardados = $datos_submodulo["IDSeccionEvento"];
																		if(!empty($valores_guardados)):
																			$array_valores_guardados = explode("|",$valores_guardados);
																		endif;

                                                                                  if(empty($valores_guardados)):
                                                                                        $seleccionar = "";
                                                                                    elseif(in_array($r_valor->IDSeccionEvento,$array_valores_guardados)):
                                                                                        $seleccionar = "selected";
                                                                                    else:
                                                                                        $seleccionar = "";
                                                                                    endif;

                                                                                  ?> <option value="<?php echo $r_valor->IDSeccionEvento ?>" <?php echo $seleccionar; ?>> <?php  echo $r_valor->Nombre; ?> </option>
                                <? endforeach;?>
                            </select>
                        </td>
                        <td width="200px">
                            <select multiple class=" chosen-select form-control" name="SeccionGaleria<?php echo $r->IDModulo; ?>[]" id="SeccionGaleria<?php echo $r->IDModulo; ?>" data-placeholder="Seleccione secciones galeria..."> <?php
														  unset($array_valores_guardados);
                                                          foreach( $array_secc_gal as $id => $r_valor):
																		$valores_guardados = $datos_submodulo["IDSeccionGaleria"];
																		if(!empty($valores_guardados)):
																			$array_valores_guardados = explode("|",$valores_guardados);
																		endif;

                                                                                  if(empty($valores_guardados)):
                                                                                        $seleccionar = "";
                                                                                    elseif(in_array($r_valor->IDSeccionGaleria,$array_valores_guardados)):
                                                                                        $seleccionar = "selected";
                                                                                    else:
                                                                                        $seleccionar = "";
                                                                                    endif;

                                                                                  ?> <option value="<?php echo $r_valor->IDSeccionGaleria ?>" <?php echo $seleccionar; ?>> <?php  echo $r_valor->Nombre; ?> </option>
                                <? endforeach;?>
                            </select>
                        </td>
                        <td width="200px">
                            <select multiple class=" chosen-select form-control" name="TipoArchivo<?php echo $r->IDModulo; ?>[]" id="TipoArchivo<?php echo $r->IDModulo; ?>" data-placeholder="Seleccione secciones galeria..."> <?php
														  unset($array_valores_guardados);
                                                          foreach( $array_tipo_arch as $id => $r_valor):
																		$valores_guardados = $datos_submodulo["IDTipoArchivo"];
																		if(!empty($valores_guardados)):
																			$array_valores_guardados = explode("|",$valores_guardados);
																		endif;

                                                                                  if(empty($valores_guardados)):
                                                                                        $seleccionar = "";
                                                                                    elseif(in_array($r_valor->IDTipoArchivo,$array_valores_guardados)):
                                                                                        $seleccionar = "selected";
                                                                                    else:
                                                                                        $seleccionar = "";
                                                                                    endif;

                                                                                  ?> <option value="<?php echo $r_valor->IDTipoArchivo ?>" <?php echo $seleccionar; ?>> <?php  echo $r_valor->Nombre; ?> </option>
                                <? endforeach;?>
                            </select>
                        </td>
                        <td width="200px">
                            <select multiple class=" chosen-select form-control" name="ModuloHijo<?php echo $r->IDModulo; ?>[]" id="ModuloHijo<?php echo $r->IDModulo; ?>" data-placeholder="Seleccione modulo..."> <?php
 														  unset($array_valores_guardados);
                                                           foreach( $array_datos_modulo as $id => $r_valor):
 																		$valores_guardados = $datos_submodulo["IDModuloHijo"];
 																		if(!empty($valores_guardados)):
 																			$array_valores_guardados = explode("|",$valores_guardados);
 																		endif;

                                                                                   if(empty($valores_guardados)):
                                                                                         $seleccionar = "";
                                                                                     elseif(in_array($r_valor->IDModulo,$array_valores_guardados)):
                                                                                         $seleccionar = "selected";
                                                                                     else:
                                                                                         $seleccionar = "";
                                                                                     endif;

                                                                                   ?> <option value="<?php echo $r_valor->IDModulo ?>" <?php echo $seleccionar; ?>> <?php  echo $r_valor->Nombre; ?> </option>
                                <? endforeach;?>
                            </select>
                        </td>
                        <td width="200px">
                            <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $datos_submodulo["MostrarMisReservas"] , 'MostrarMisReservas'.$r->IDModulo , "class='input mandatory'" ) ?>
                        </td>
                    </tr> <?php
                                          }
                                          ?> </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="action" id="action" value="updatesubmodulos" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ "IDClub" ]?>" />
            <input type="submit" class="submit" value="Guardar">
        </div>
    </div>
</form>