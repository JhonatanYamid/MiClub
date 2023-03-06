<form class="form-horizontal formvalida" role="form" method="post" id="EditSocioHabitacion<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data"> <?php
    $action = "InsertarSocioHabitacion";

    if( $_GET[IDSocioHabitacion] )
    {
            $EditSocioHabitacion =$dbo->fetchAll("SocioHabitacion"," IDSocioHabitacion = '".$_GET[IDSocioHabitacion]."' ","array");
            $action = "ModificaSocioHabitacion";
            ?> <input type="hidden" name="IDSocioHabitacion" id="IDSocioHabitacion" value="<?php echo $EditSocioHabitacion[IDSocioHabitacion]?>" /> <?php
    }
    ?> 
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habitación: </label>
            <div class="col-sm-8">
                <select name=IDHabitacion>
                    <option value=0>[ESCOGE LA HABITACIÓN]</option>
                <?php
                    $sql = "SELECT * FROM Habitacion WHERE IDClub = ". SIMUser::get("club");
                    $qry = $dbo->query($sql);
                    while($Datos = $dbo->fetchArray($qry)):
                        $NombreTipo = $dbo->getFields("TipoHabitacion","Nombre","IDTipoHabitacion = $Datos[IDTipoHabitacion]");
                        ?>
                            <option value="<?php echo $Datos[IDHabitacion]; ?>" <?php if($Datos[IDHabitacion] == $EditSocioHabitacion[IDHabitacion]) echo "selected"; ?>><?php echo $NombreTipo; ?></option>
                        <?php
                    endwhile;
                ?>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero de Fracciones</label>
            <div class="col-sm-8">
                <input type="text" id="NumeroFracciones" name="NumeroFracciones" placeholder="Numero de Fracciones" class="col-xs-12 mandatory" title="fecha de inicio cortesía" value="<?php echo $EditSocioHabitacion["NumeroFracciones"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Inicio Fraccion</label>
            <div class="col-sm-8">
                <input type="text" id="FechaInicioFraccion" name="FechaInicioFraccion" placeholder="Fecha Inicio Fraccion" class="col-xs-12 calendar" title="fecha de inicio cortesía" value="<?php echo $EditSocioHabitacion["FechaInicioFraccion"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin Fraccion </label>
            <div class="col-sm-8">
                <input type="text" id="FechaFinFraccion" name="FechaFinFraccion" placeholder="Fecha Fin Fraccion" class="col-xs-12 calendar" title="fecha de inicio cortesía" value="<?php echo $EditSocioHabitacion["FechaFinFraccion"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Noches disponibles</label>
            <div class="col-sm-8">
                <input type="text" id="Noches" name="Noches" placeholder="Noches" class="col-xs-12 " title="Noches" value="<?php echo $EditSocioHabitacion["Noches"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estadias disponibles </label>
            <div class="col-sm-8">
                <input type="text" id="Estadias" name="Estadias" placeholder="Estadias" class="col-xs-12" title="Estadias" value="<?php echo $EditSocioHabitacion["Estadias"]; ?>">
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
        <th>Habitacion</th>
        <th>Fracciones</th>
        <th>Inicio</th>
        <th>Fin</th>
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante"> <?php

        $r_datos =& $dbo->all( "SocioHabitacion" , "IDSocio = '" . $frm[$key]  ."'");

        while( $r = $dbo->object( $r_datos ) )
        {
            $TipoHabitacion = $dbo->getFields("Habitacion","IDTipoHabitacion","IDHabitacion = $r->IDHabitacion");
            $NombreTipo = $dbo->getFields("TipoHabitacion","Nombre","IDTipoHabitacion = $TipoHabitacion");
            ?>  
            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                <td align="center" width="64">
                    <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDSocioHabitacion=".$r->IDSocioHabitacion?>&tabsocio=habitaciones" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $NombreTipo; ?></td>
                <td><?php echo $r->NumeroFracciones; ?></td>
                <td><?php echo $r->FechaInicioFraccion; ?></td>
                <td><?php echo $r->FechaFinFraccion; ?></td>
                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaSocioHabitacion&id=<?php echo $frm[$key];?>&IDSocioHabitacion=<? echo $r->IDSocioHabitacion ?>&tabsociodo=habitaciones"></a>
                </td>
            </tr> <?php
        }
        ?> </tbody>
    <tr>
        <th class="texto" colspan="14"></th>
    </tr>
</table>