<div id="CampoCantidadReservasTipoSocio">
    <form name="frmproCantidadReservasTipoSocio" id="frmproCantidadReservasTipoSocio" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>


                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                        <tr>
                            <td valign="top">


                                <?php
                                $action = "InsertarCantidadReservasTipoSocio";

                                if ($_GET[IDCantidadReservasTipoSocio]) {
                                    $EditCantidadReservasTipoSocio = $dbo->fetchAll("CantidadReservasTipoSocio", " IDCantidadReservasTipoSocio = '" . $_GET[IDCantidadReservasTipoSocio] . "' ", "array");
                                    $action = "ModificarCantidadReservasTipoSocio";
                                ?>
                                    <input type="hidden" name="IDCantidadReservasTipoSocio" id="IDCantidadReservasTipoSocio" value="<?php echo $EditCantidadReservasTipoSocio[IDCantidadReservasTipoSocio] ?>" />
                                <?php
                                }
                                ?>
                                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                                    <tr>
                                        <td width="26%">Tipo Socio </td>
                                        <td width="74%"> 
                                            <?php
                                            $sql_tipo_socio = "SELECT TS.IDTipoSocio,Nombre FROM TipoSocio TS, ClubTipoSocio CTS WHERE TS.IDTipoSocio=CTS.IDTipoSocio AND IDClub = '$_GET[id]' Order by Nombre";
								            $result_tipo_socio = $dbo->query($sql_tipo_socio); ?>
							 		
                                            <select name="TipoSocio" id="TipoSocio" class="form-control" onchange="cambioTipoSocio()">
                                                <option value="">[Seleccione Tipo Socio]</option> 
                                                <? while ($row_tipo_soc = $dbo->fetchArray($result_tipo_socio)) { ?> 
                                                    <option value="<? echo $row_tipo_soc["Nombre"];  ?>" <? if ($EditCantidadReservasTipoSocio["TipoSocio"] == $row_tipo_soc["Nombre"]) echo "selected"; ?>><? echo $row_tipo_soc["Nombre"];  ?></option> 
                                                <? } ?>
                                            </select> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><br></td>
                                    </tr>
                                    <tr>
                                        <td>Numero de reservas al mes</td>
                                        <td>
                                            <input type="text" id="NumeroReservasMes" name="NumeroReservasMes" placeholder="" class="col-xs-12" title="NumeroReservasMes" value="<?php echo $EditCantidadReservasTipoSocio["NumeroReservasMes"]; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Numero de reservas al dia</td>
                                        <td>
                                            <input type="text" id="NumeroReservaDia" name="NumeroReservaDia" placeholder="" class="col-xs-12" title="NumeroReservaDia" value="<?php echo $EditCantidadReservasTipoSocio["NumeroReservaDia"]; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Numero de reservas al año</td>
                                        <td>
                                            <input type="text" id="NumeroReservasAnno" name="NumeroReservasAnno" placeholder="" class="col-xs-12" title="NumeroReservasAnno" value="<?php echo $EditCantidadReservasTipoSocio["NumeroReservasAnno"]; ?>">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center">&nbsp;</td>
                                    </tr>
                                </table>
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $_GET[id] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $action ?>" />

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


    <br />
    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <th align="center" valign="middle" width="64">Editar</th>
            <th>Tipo Socio</th>
            <th>Reservas al dia</th>
            <th>Reservas al mes</th>
            <th>Reservas al año</th>

            <th align="center" valign="middle" width="64">Eliminar</th>
        </tr>
        <tbody id="listacontactosanunciante">
            <?php

            $r_documento = &$dbo->all("CantidadReservasTipoSocio", "IDClub = '$_GET[id]'");

            while ($r = $dbo->object($r_documento)) {
            ?>

                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                    <td align="center" width="64">
                        <a href="<?php echo $script . ".php" . "?action=edit&id=" . $_GET[id] . "&IDCantidadReservasTipoSocio=" . $r->IDCantidadReservasTipoSocio ?>&tabclub=parametros&tabparametro=CantidadReservasTipoSocio" class="ace-icon glyphicon glyphicon-pencil"></a>
                    </td>
                    <td><?php echo $r->TipoSocio; ?></td>
                    <td><?php echo $r->NumeroReservaDia; ?></td>
                    <td><?php echo $r->NumeroReservasMes; ?></td>
                    <td><?php echo $r->NumeroReservasAnno; ?></td>

                    <td align="center" width="64">
                        <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get("mod") ?>&action=EliminaCantidadReservasTipoSocio&id=<?php echo $_GET[id]; ?>&IDCantidadReservasTipoSocio=<? echo $r->IDCantidadReservasTipoSocio ?>&tabclub=parametros&tabparametro=CantidadReservasTipoSocio"></a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
        <tr>
            <th class="texto" colspan="15"></th>
        </tr>
    </table>



</div>