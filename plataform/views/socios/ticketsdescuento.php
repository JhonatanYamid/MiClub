<form class="form-horizontal formvalida" role="form" method="post" id="EditTicketDescuento<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data"> <?php
    $action = "InsertarTicketDescuento";

    if( $_GET[IDTicketDescuento] )
    {
            $EditTicketDescuento =$dbo->fetchAll("TicketDescuento"," IDTicketDescuento = '".$_GET[IDTicketDescuento]."' ","array");
            $action = "ModificaTicketDescuento";
            ?> <input type="hidden" name="IDTicketDescuento" id="IDTicketDescuento" value="<?php echo $EditTicketDescuento[IDTicketDescuento]?>" /> <?php
    }
    ?> 
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6" >
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Servicios Club: </label>
            <div class="col-sm-8">
            <select name="IDServicio" id="IDServicio" class="form-control">
                <option value="">SELECCIONE UN SERVICIO</option>
                <?php
                
                foreach ($datos_servicio as $idservicio => $servicio):

                    $id_servicio_mestro_menu = $servicio["IDServicioMaestro"];
                    $servicio["Nombre"] =  $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");
                    $servicio["NombrePersonalizado"] =  $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . SIMUser::get("club") . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");

                    if (!empty($servicio["NombrePersonalizado"]))
                        $Nombre = $servicio["NombrePersonalizado"];
                    else
                        $Nombre = $servicio["Nombre"];
                        
                        ?>
                            <option value="<?php echo $idservicio; ?>"><?php echo $Nombre ; ?></option>
                        <?php

                endforeach;
                
                ?>
            </select>
            <br>
            <a id="agregar_servicioclub" href="#">Agregar Servicio</a> | <a id="borrar_servicio" href="#">Borrar Servicio</a>
            <br>
            <select name="SocioServicioDatos[]" id="SocioServicio" class="col-xs-8" multiple>
                <?php
                $SQLServiciosPermisos = "SELECT TDS.IDServicio FROM TicketDescuento TD, TicketDescuentoServicio TDS WHERE TD.IDTicketDescuento = TDS.IDTicketDescuento AND TD.IDSocio = $_GET[id] AND TD.IDTicketDescuento = $_GET[IDTicketDescuento]";
                $QRYServiciosPermisos = $dbo->query($SQLServiciosPermisos);
                while($DatoServicios = $dbo->fetchArray($QRYServiciosPermisos)):

                    $IDServicioMaestro = $dbo->getFields("Servicio","IDServicioMaestro","IDServicio = $DatoServicios[IDServicio]");               
                    $servicio["Nombre"] =  $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '$IDServicioMaestro'");
                    $servicio["NombrePersonalizado"] =  $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . SIMUser::get("club") . "' and Activo = 'S' and IDServicioMaestro = '$IDServicioMaestro'");

                    if (!empty($servicio["NombrePersonalizado"]))
                        $NombreServicio = $servicio["NombrePersonalizado"];
                    else
                        $NombreServicio = $servicio["Nombre"];                
                    ?>
                        <option value="<?php echo $DatoServicios[IDServicio]; ?>"><?php echo $NombreServicio ; ?></option>
                    <?php                       
                endwhile;
                ?>
                </select>
                <input type="hidden" name="SeleccionServicios" id="SeleccionServicios" value="">
            </div>
        </div>  
    </div>  


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre ticket</label>
                <div class="col-sm-8">
                    <input type="text" id="Nombre" name="Nombre" placeholder="" class="col-xs-12" title="fecha de inicio cortesía" value="<?php echo $EditTicketDescuento["Nombre"]; ?>">
                </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Porcentaje de descuento </label>
                <div class="col-sm-8">
                    <input type="number" id="ValorDescuento" name="ValorDescuento" placeholder="" class="col-xs-12 " title="fecha de inicio cortesía" value="<?php echo $EditTicketDescuento["ValorDescuento"]; ?>">
                </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion</label>
            <div class="col-sm-8">
                <input type="text" id="Descripcion" name="Descripcion" placeholder="Descripcion" class="col-xs-12 " title="Descripcion" value="<?php echo $EditTicketDescuento["Descripcion"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>
            <div class="col-sm-8">
            <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $EditTicketDescuento["Activo"], 'Activo', "class='input'") ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
            <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $frm[ $key ] ?>" />
            <input type="submit" class="submit" value="Guardar">            
            <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
        </div>
    </div>
</form>
<br/>
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64">Editar</th>
        <th>Nombre</th>
        <th>Descripcion</th>
        <th>Porcentaje</th>        
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante"> <?php

        $r_datos =& $dbo->all( "TicketDescuento" , "IDSocio = '" . $frm[$key]  ."'");

        while( $r = $dbo->object( $r_datos ) )
        {
            
            ?>  
            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                <td align="center" width="64">
                    <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDTicketDescuento=".$r->IDTicketDescuento?>&tabsocio=tickets" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $r->Nombre ; ?></td>
                <td><?php echo $r->Descripcion; ?></td>                
                <td><?php echo $r->ValorDescuento; ?></td>
                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaTicketDescuento&id=<?php echo $frm[$key];?>&IDTicketDescuento=<? echo $r->IDTicketDescuento ?>&tabsociodo=tickets"></a>
                </td>
            </tr> <?php
        }
        ?> </tbody>
    <tr>
        <th class="texto" colspan="14"></th>
    </tr>
</table>