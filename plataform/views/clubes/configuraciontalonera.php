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
                                    $EditConfiguracionTalonera = $dbo->fetchAll("ConfiguracionTalonera", " IDClub = '" . $_GET[id] . "' ", "array");
                                    $action = "ModificarConfiguracionTalonera";
                                ?>
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $EditConfiguracionTalonera[IDClub] ?>" />
                                <?php
                                }
                                if(empty($EditConfiguracionTalonera[IDClub])){
                                           $action = "InsertarConfiguracionTalonera";
                                }
                                ?>
 
    
                                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                                
                                  <tr>
                                       
                                        <td width="26%">Mostrar Boton Planes  </td>
                                        <td width="74%"> 
                                             <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino),  $EditConfiguracionTalonera["MostrarBotonPlanes"], 'MostrarBotonPlanes', "class='input '") ?>

                                        </td>

                                    </tr> 
                                      <tr>
                                       
                                        <td width="26%">Texto De Boton Planes  </td>
                                        <td width="74%">
                                            <input id="FiltroFamiliaresLabel" type="text" size="25" title="FiltroFamiliaresLabel" name="TextoBuscadorBeneficios" class="input mandatory" value="<?php echo $EditConfiguracionTalonera["TextoBotonPlanes"] ?>" />

                                        </td>

                                    </tr> 
                                      <tr>
                                       
                                        <td width="26%">URL Archivo Planes </td>
                                        <td width="74%">
                                            <input id="FiltroFamiliaresLabel" type="text" size="25" title="FiltroFamiliaresLabel" name="TextoBuscadorBeneficios" class="input mandatory" value="<?php echo $EditConfiguracionTalonera["ArchivoPlanes"] ?>" />

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
