<form class="form-horizontal formvalida" role="form" method="post" id="frmRegistroLote" name="frmRegistroLote" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

  <table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <td>
            <table id="simple-table" class="table table-striped table-bordered table-hover">
                <tr>
                    <td colspan="3"> Estructura del Archivo </td>
                </tr>
                <tr>
                    <td align='center' valign='middle'>1</td>
                    <td>*<?= SIMUtil::get_traduccion('', '', 'Numerodedocumentodelfuncionario', LANGSESSION);?></td>
                </tr>
                <tr>
                    <td align='center' valign='middle'>3</td>
                    <td>
                        <b><?= SIMUtil::get_traduccion('', '', 'Planificaciondia', LANGSESSION);?></b><BR>
                        <b><?= SIMUtil::get_traduccion('', '', 'dianolaboral', LANGSESSION);?></b>:<BR>
                        N-<?= SIMUtil::get_traduccion('', '', 'Codigodeldianolaboral', LANGSESSION);?>(N-D)<BR>
                        <b><?= SIMUtil::get_traduccion('', '', 'turno', LANGSESSION);?></b>:<BR>
                        S-<?= SIMUtil::get_traduccion('', '', 'Codigodelturno', LANGSESSION);?>(S-1)
                    </td>
                </tr>
            </table>
        </td>
        <td valign="top">
            <table id="simple-table" class="table table-striped table-bordered table-hover">
                <tr>
                    <td valign='middle'><?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION);?>:</td>
                    <td><input type="text" id="fechaInicio" name="fechaInicio" class="calendar" required="required" value="<?= $hoy ?>" /></td>
                    <td valign='middle'><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION);?>:</td>
                    <td><input type="text" id="fechaFin" name="fechaFin" class="calendar" required="required" value="<?= $hoy ?>" /></td>
                </tr>
                <tr>
                    <td valign='middle'><?= SIMUtil::get_traduccion('', '', 'ArchivoExcel', LANGSESSION);?>:</td>
                    <td colspan="3"><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                </tr>
                <tr>
                    <td colspan="4" align='center'>
                        <input type="hidden" name="action" id="action" value="cargarlote" />
                        <input type="submit" class="submit" value="Cargar">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
  </table>

</form>
