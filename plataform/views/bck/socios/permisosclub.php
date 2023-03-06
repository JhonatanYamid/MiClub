<form class="form-horizontal formvalida" role="form" method="post" name="SocioClubPermiso" id="SocioClubPermiso" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data"> <?php
        $action = "InsertarSocioClubPermiso";

        if( $_GET[IDSocioClubPermiso] )
        {
                $EditSocioClubPermiso =$dbo->fetchAll("SocioClubPermiso"," IDSocioClubPermiso = '".$_GET[IDSocioClubPermiso]."' ","array");
                $action = "ModificaSocioClubPermiso";
                ?> <input type="hidden" name="IDSocioClubPermiso" id="IDSocioClubPermiso" value="<?php echo $EditSocioClubPermiso[IDSocioClubPermiso]?>" /> <?php
        }
        ?> 
        <div class="form-group first ">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clubes con permiso: </label>
                <div class="col-sm-8">

                    <select name="IDClubPermiso" id="IDClubPermiso" class="form-control">
                        <option value=""><?= SIMUtil::get_traduccion('', '', 'SeleccionGrupo', LANGSESSION); ?></option>
                        <?php
                        $ClubPadre = $datos_club[IDClubPadre];
                        $SQLClubes = "SELECT IDClub, Nombre FROM Club WHERE IDClubPadre = '$ClubPadre'";
                        $QRYClubes = $dbo->query($SQLClubes);
                        while ($Datos = $dbo->fetchArray($QRYClubes)) : ?>
                            <option value="<?php echo $Datos["IDClub"]; ?>" <?php if($EditSocioClubPermiso[IDClub] == $Datos["IDClub"]) echo "selected"; ?>><?php echo $Datos["Nombre"]; ?></option>
                        <?php endwhile; ?>
                    </select>
                    
                </div>
            </div>
            <div class="col-xs-12 col-sm-6" >
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Servicios Club: </label>
                <div class="col-sm-8">
                <select name="IDServicio" id="IDServicio" class="form-control">
                    <option value="">SELECCIONE UN SERVICIO</option>
                </select>
                <br>
                <a id="agregar_servicioclub" href="#">Agregar Servicio</a> | <a id="borrar_servicio" href="#">Borrar Servicio</a>
                <br>
                <select name="SocioServicioDatos[]" id="SocioServicio" class="col-xs-8" multiple>
                    <?php
                    $SQLServiciosPermisos = "SELECT * FROM SocioPermisoReserva WHERE IDSocio = $_GET[id] AND IDClub = $EditSocioClubPermiso[IDClub]";
                    $QRYServiciosPermisos = $dbo->query($SQLServiciosPermisos);
                    while($DatoServicios = $dbo->fetchArray($QRYServiciosPermisos)):
        
                        $IDServicioMaestro = $dbo->getFields("Servicio","IDServicioMaestro","IDServicio = $DatoServicios[IDServicio]");
                        $NombreServicio = $dbo->getFields("ServicioClub","TituloServicio","IDServicioMaestro = $IDServicioMaestro AND IDClub = $DatoServicios[IDClub]");
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
       
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $_GET[id] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
            <input type="submit" class="submit" value="Guardar">
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club")?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
        </div>
    </div>
</form>
<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64">Editar</th>
        <th>Club</th>       
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante"> <?php

        $r_datos = $dbo->all( "SocioClubPermiso" , "IDSocio = '$_GET[id]'");

        while( $r = $dbo->object( $r_datos ) )
        {
            $NombreClub = $dbo->getFields("Club","Nombre","IDClub = $r->IDClub");
            ?> 
            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                <td align="center" width="64">
                    <a href="<?php echo $script.".php" . "?action=edit&id=$_GET[id]&IDSocioClubPermiso=".$r->IDSocioClubPermiso?>&tabsocio=permisosclub" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $NombreClub; ?></td>               
                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaSocioClubPermiso&id=<?php echo$_GET[id];?>&IDSocioClubPermiso=<? echo $r->IDSocioClubPermiso ?>&tabsocio=permisosclub"></a>
                </td>
            </tr> <?php
                      }
                      ?> </tbody>
    <tr>
        <th class="texto" colspan="15"></th>
    </tr>
</table>