<form class="form-horizontal formvalida" role="form" method="post" id="frmRegistroLote" name="frmRegistroLote" action="/plataform/registrocorredor.php?action=add" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> Estructura del Archivo </td>
                    </tr>
                    <tr>
                        <td align='center' valign='middle'>1</td>
                        <td>*<?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td align='center' valign='middle'>2</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Nombre', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td align='center' valign='middle'>3</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Apellido', LANGSESSION); ?></td>
                    </tr>
                    <tr>
                        <td align='center' valign='middle'>4</td>
                        <td><?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?></td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td valign='middle'><?= SIMUtil::get_traduccion('', '', 'Carrera', LANGSESSION); ?>:</td>
                        <td><?= SIMHTML::formPopupV2('Carrera', 'Nombre', 'Nombre', 'IDCarrera', 'IDCarreraLote', "", SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), "", "onchange = 'changeCategoria(\"Lote\")'", "AND Activo = 'S' AND IDClub = $IDClub") ?></td>
                        <td valign='middle'><?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?>:</td>
                        <td><span id="selectCategoriaLote"></span></td>
                    </tr>
                    <tr>
                        <td>
                            Ingresar Camiseta manual
                        </td>
                        <td>
                            <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["IngresarCamisetaManual"], 'IngresarCamisetaManual', "class='input mandatory'") ?>
                        </td>
                    </tr>
                    <tr>
                        <td valign='middle'><?= SIMUtil::get_traduccion('', '', 'ArchivoExcel', LANGSESSION); ?>:</td>
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