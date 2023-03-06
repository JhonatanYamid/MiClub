<div id="CampoConfiguracionReservas">
    <form name="frmproConfiguracionReservas" id="frmproConfiguracionReservas" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>


                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <td valign="top">


                                <?php
 
 
                                if ($_GET[id]) {
                                    $EditConfiguracionFacturacion = $dbo->fetchAll("ConfiguracionFacturacion", " IDClub = '" . $_GET[id] . "' ", "array");
                                    $action = "ModificarConfiguracionFacturacion";
                                ?>
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $EditConfiguracionFacturacion[IDClub] ?>" />
                                <?php
                                }
                                if(empty($EditConfiguracionFacturacion[IDClub])){
                                           $action = "InsertarConfiguracionFacturacion";
                                }
                                ?>
 
    
                                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                                
                                  <tr>
                                        <td width="26%"> Imagen Lateral </td>
                                      
                <? if (!empty($EditConfiguracionFacturacion[ImagenLateral])) {
													echo "<img src='".CLUB_ROOT."$EditConfiguracionFacturacion[ImagenLateral]' width=55 >";
													?>
                
                <?
												}// END if
												?>
                <input name="ImagenLateral" id=file class="col-xs-12" title="Foto" type="file" size="25" style="font-size: 10px">
            
            

                                        </td>

                                    </tr>
                                    
<tr>
                                        
                                        <td width="10%">Mostrar Filtro Familiares</td>
                                     <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $_GET["id"]  ?>" />
                                        <td width="50%">    <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionFacturacion["MostrarFiltroFamiliares"], 'MostrarFiltroFamiliares', "class='input mandatory'") ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                    </tr>
    <tr>
                                        <td width="26%">Texto Boton Filtro Familiares  </td>
                                        <td width="74%">
                                            <input id="FiltroFamiliaresLabel" type="text" size="25" title="FiltroFamiliaresLabel" name="FiltroFamiliaresLabel" class="input mandatory" value="<?php echo $EditConfiguracionFacturacion["FiltroFamiliaresLabel"] ?>" />

                                        </td>

                                    </tr>
                                    <tr>
                                    
                                    <tr>
                                        <td width="26%"> Mostrar Secciones Historial</td>
                                        <td width="74%">
                                            <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionFacturacion["MostrarSeccionesHistorial"], 'MostrarSeccionesHistorial', "class='input mandatory'") ?>
                                        </td>


                                    </tr>
                                    <tr>
                                        <td><br></td>
                                    </tr>
                                    <tr>
                                        <td width="26%">Texto Boton Historial  </td>
                                        <td width="74%">
                                            <input id="SeccionesHistorialLabel" type="text" size="25" title="SeccionesHistorialLabel" name="SeccionesHistorialLabel" class="input mandatory" value="<?php echo $EditConfiguracionFacturacion["SeccionesHistorialLabel"] ?>" />

                                        </td>

                                    </tr>
                                       <tr>
                                        <td width="26%">Texto Boton Cuentas Abiertas  </td>
                                        <td width="74%">
                                            <input id="SeccionesPendientesPagoLabel" type="text" size="25" title="SeccionesPendientesPagoLabel" name="SeccionesPendientesPagoLabel" class="input mandatory" value="<?php echo $EditConfiguracionFacturacion["SeccionesPendientesPagoLabel"] ?>" />

                                        </td>

                                    </tr>
                                    <tr>
                                        
                                        <td width="10%">Mostrar Paginacion</td>
                                        <td width="50%">    <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionFacturacion["PermitePaginar"], 'PermitePaginar', "class='input mandatory'") ?>
                                        </td>
                                    </tr>
                                      
                                     <tr>
                                        
                                        <td width="10%">Activar Buscador De Fechas</td>
                                        <td width="50%">    <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionFacturacion["BuscadorFechas"], 'BuscadorFechas', "class='input mandatory'") ?>
                                        </td>
                                    </tr>
                                     <tr>
                                        
                                        <td width="10%">Ocultar Buscador De Fechas Cuentas Por Pagar</td>
                                        <td width="50%">    <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionFacturacion["OcultarBuscadorFechasPendientesPago"], 'OcultarBuscadorFechasPendientesPago', "class='input mandatory'") ?>
                                        </td>
                                    </tr>
                                     <tr>
                                        
                                        <td width="10%">Ocultar Buscador De Fechas Historico Consumos</td>
                                        <td width="50%">    <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionFacturacion["OcultarBuscadorFechasHistorico"], 'OcultarBuscadorFechasHistorico', "class='input mandatory'") ?>
                                        </td>
                                    </tr>
                                   
                                       <tr>
                                        
                                        <td width="10%">Precargar Fecha Hoy En Buscador</td>
                                        <td width="50%">    <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionFacturacion["PrecargarFechaHoyBuscador"], 'PrecargarFechaHoyBuscador', "class='input mandatory'") ?>
                                        </td>
                                    </tr>
                                       
                                     <tr>
                                        
                                        <td width="10%">Permite Seleccionar Varias</td>
                                        <td width="50%">    <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionFacturacion["PermiteSeleccionarVarias"], 'PermiteSeleccionarVarias', "class='input mandatory'") ?>
                                        </td>
                                    </tr>
                                     
                                   <tr>
                                        
                                        <td width="10%">Mostrar Decimal</td>
                                        <td width="50%">    <? echo SIMHTML::formradiogroup(SIMResources::$sino, $EditConfiguracionFacturacion["MostrarDecimal"], 'MostrarDecimal', "class='input mandatory'") ?>
                                        </td>
                                    </tr>
  <tr>
                                        <td width="26%">Texto Seleccionar / Deseleccionar  </td>
                                        <td width="74%">
                                            <input id="TextoSeleccionarDeseleccionar" type="text" size="25" title="TextoSeleccionarDeseleccionar" name="TextoSeleccionarDeseleccionar" class="input mandatory" value="<?php echo $EditConfiguracionFacturacion["TextoSeleccionarDeseleccionar"] ?>" />

                                        </td>

                                    </tr>
                                     <tr>
                                        <td width="26%">Texto Pagar Varias </td>
                                        <td width="74%">
                                            <input id="TextoIntroSeleccionarVariasPago" type="text" size="25" title="TextoIntroSeleccionarVariasPago" name="TextoIntroSeleccionarVariasPago" class="input mandatory" value="<?php echo $EditConfiguracionFacturacion["TextoIntroSeleccionarVariasPago"] ?>" />

                                        </td>

                                    </tr>
                                    
                                </table>
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[$key] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />

                            </td>
                            <td valign="top">

                                <?php
                                //$action = "InsertarDisponibilidadElemento";
                                ?>

                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
            <tr>
                <td align="center"><input type="submit" class="submit" value="Agregar"></td>
            </tr>

        </table>
    </form>
 


</div>
