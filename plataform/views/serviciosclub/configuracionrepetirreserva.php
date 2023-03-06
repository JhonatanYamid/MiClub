<? 
    $arrInfo = $dbo->fetchAll("ConfiguracionRepetirReserva", "IDServicio = ".$_GET[ids]." AND IDClub = ".SIMUser::get("club"), "array");
?>

<div id="repetirreserva">
    <form name="frmrepetirreserva" id="frmrepetirreserva" action="?mod=<?php echo SIMReg::get("mod") ?>" method="post" class="formvalida" enctype="multipart/form-data">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
            <tr>
                <td>
                    <table id="simple-table" class="table table-striped table-bordered table-hover">

                        <tr>
                            <td width="26%">¿Permite Repetir Reservas?</td>
                            <td width="74%"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $arrInfo["PermiteSeleccionarRepetir"], 'PermiteSeleccionarRepetir', "class='input'") ?></td>
                        </tr>
                        <tr>
                            <td width="26%">Título de la caja de selección: <br> (Ej.Repetir este horario por un mes)</td>
                            <td width="74%">
                                <input type="text" id="TextoTituloSeleccionarRepetir" name="TextoTituloSeleccionarRepetir" placeholder="" class="form-control" title="Título de la caja de selección" value="<?php echo $arrInfo["TextoTituloSeleccionarRepetir"] ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td width="26%">Mensaje al seleccionar: <br> (Ej.Se agendarán las reservas por un mes)</td>
                            <td width="74%">
                                <input type="text" id="TextoSeleccionarRepetir" name="TextoSeleccionarRepetir" placeholder="" class="form-control" title="Mensaje al seleccionar" value="<?php echo $arrInfo["TextoSeleccionarRepetir"] ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td width="26%">Mensaje al confirmar: <br> (Ej.Va a sobreescribir las siguientes semanas, esta seguro de continuar?)</td>
                            <td width="74%">
                                <input type="text" id="MensajeConfirmacionRepetir" name="MensajeConfirmacionRepetir" placeholder="" class="form-control" title="Mensaje al confirmar" value="<?php echo $arrInfo["MensajeConfirmacionRepetir"] ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td width="26%">Mensaje al eliminar repetición: <br> (Ej.Esta seguro de eliminar la repetición?)</td>
                            <td width="74%">
                                <input type="text" id="MensajeConfirmacionEliminarRepetir" name="MensajeConfirmacionEliminarRepetir" placeholder="" class="form-control" title="Mensaje al eliminar repetición" value="<?php echo $arrInfo["MensajeConfirmacionEliminarRepetir"] ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td width="26%">Mensaje al seleccionar cuando tiene una repeticion activa: <br> (Ej.No es posible seleccionar otras horas, Tiene una repeticion activa)</td>
                            <td width="74%">
                                <input type="text" id="MensajeNoPuedeSeleccionarRepetirActivo" name="MensajeNoPuedeSeleccionarRepetirActivo" placeholder="" class="form-control" title="Mensaje al eliminar repetición" value="<?php echo $arrInfo["MensajeNoPuedeSeleccionarRepetirActivo"] ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td width="26%">Numero de semanas a repetir incluida la primera semana:</td>
                            <td width="74%">
                                <input type="number" id="SemanasSeguidasARepetir" name="SemanasSeguidasARepetir" placeholder="" class="form-control" title="Numero De Semanas a Repetir<" value="<?php echo $arrInfo["SemanasSeguidasARepetir"] ?>" required>
                            </td>
                        </tr>

                        <input type="hidden" name="IDConfiguracionRepetirReserva" id="IDConfiguracionRepetirReserva" value="<?= $arrInfo['IDConfiguracionRepetirReserva'] ?>" />
                        <input type="hidden" name="IDServicio" id="IDServicio" value="<?= $_GET[ids] ?>" />
                        <input type="hidden" name="action" id="action" value="configuracionrepetirreserva" />
                        <input type="hidden" name="IDClub" id="IDClub" value="<?= SIMUser::get("club") ?>" />
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center"><input type="submit" class="submit" value="Guardar"/></td>
            </tr>
        </table>
    </form>
</div>