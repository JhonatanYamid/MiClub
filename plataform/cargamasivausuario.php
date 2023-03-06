<?
include("procedures/general.php");
include("procedures/usuarios.php");
include("cmp/seo.php");


?>
</head>

<body class="no-skin">
    <div class="main-container" id="main-container">
        <script type="text/javascript">
            try {
                ace.settings.check('main-container', 'fixed')
            } catch (e) {}
        </script>
        <div class="main-content">
            <div class="main-content-inner">
                <div class="page-content">
                    <?
                    SIMNotify::each();


                    ?>
                    <div class="page-header">
                        <h1> Home <small>
                                <i class="ace-icon fa fa-angle-double-right"></i> <?= $array_clubes[SIMUser::get("club")]["Nombre"] ?> <i class="ace-icon fa fa-angle-double-right"></i> Carga de Invitaciones </small>
                        </h1>
                    </div><!-- /.page-header -->
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <form class="form-horizontal formvalida" role="form" method="post" id="frmUsuarioPlano" name="frmUsuarioPlano" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td>
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <td colspan="2"> Estructura del Archivo </td>
                                                        </tr>
                                                        <tr>
                                                            <td>1</td>
                                                            <td>Documento</td>
                                                        </tr>
                                                        <tr>
                                                            <td>2</td>
                                                            <td>Nombre</td>
                                                        </tr>
                                                        <tr>
                                                            <td>3</td>
                                                            <td>Telefono</td>
                                                        </tr>
                                                        <tr>
                                                            <td>4</td>
                                                            <td>Usuario</td>
                                                        </tr>
                                                        <tr>
                                                            <td>5</td>
                                                            <td>Clave</td>
                                                        </tr>
                                                        <tr>
                                                            <td>6</td>
                                                            <td>Correo</td>
                                                        </tr>
                                                        <tr>
                                                            <td>7</td>
                                                            <td>Autorizado (S/N)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>8</td>
                                                            <td>Pemite Hacer reservas (S/N)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>9</td>
                                                            <td>Cargo</td>
                                                        </tr>
                                                        <tr>
                                                            <td>10</td>
                                                            <td>Perfil (Admin=1,Porteria=4,Basico=3)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>11</td>
                                                            <td>Código de empleado</td>
                                                        </tr>
                                                        <tr>
                                                            <td>12</td>
                                                            <td>Permitir modificar foto (S/N)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>13</td>
                                                            <td>Solicitar editar perfil (S/N)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>14</td>
                                                            <td>Tipo Usuario</td>
                                                        </tr>
                                                        <tr>
                                                            <td>15</td>
                                                            <td>Fecha Nacimiento (yyyy-mm-dd)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>16</td>
                                                            <td>Area</td>
                                                        </tr>
                                                        <tr>
                                                            <td>17</td>
                                                            <td>Nombre Jefe</td>
                                                        </tr>
                                                        <tr>
                                                            <td>18</td>
                                                            <td>Correo Jefe</td>
                                                        </tr>
                                                        <tr>
                                                            <td>19</td>
                                                            <td>Documento Jefe</td>
                                                        </tr>
                                                        <tr>
                                                            <td>20</td>
                                                            <td>Nombre Especialista/Aprobador</td>
                                                        </tr>
                                                        <tr>
                                                            <td>21</td>
                                                            <td>Correo Especialista/Aprobado</td>
                                                        </tr>
                                                        <tr>
                                                            <td>22</td>
                                                            <td>Documento Especialista/Aprobador</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td valign="top">
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <td>Archivo Excel</td>
                                                            <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                                                        </tr>
                                                        <!--
      <tr>
        <td>Separador de campo</td>
        <td>
        <select name="FIELD_TEMINATED" id="FIELD_TEMINATED" class="form-control" size="1">
            <option value="TAB">Tabulador</option>
            <option value=",">Coma (,)</option>
            <option value="|">Pie (|)</option>
        </select>
        </td>
      </tr>
      <tr>
        <td>Encabezados en la primera Fila?</td>
        <td>
         Si
<input type="radio" name="IGNORELINE" value="1" border="0"/>
No
<input type="radio" name="IGNORELINE" value="0" checked="" border="0"/>

        </td>
      </tr>
		-->
                                                        <tr>
                                                            <td colspan="2">
                                                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                                        else echo $frm["IDClub"];  ?>" />
                                                                <input type="hidden" name="action" id="action" value="cargarplano" />
                                                                <input type="submit" class="submit" value="Cargar">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                    </form>
                                    </table>
                                    <!--Barras-->
                                    <form class="form-horizontal formvalida" role="form" method="post" id="frmCodigoBarras" name="frmCodigoBarras" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td valign="top"> Actualizar a todos los usuarios el codigo de barras ?": <input type="radio" name="CodigoBarras" value="S">SI <input type="radio" name="CodigoBarras" value="N">No <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                                                                                                                                                                                                            else echo $frm["IDClub"];  ?>" />
                                                    <input type="hidden" name="action" id="action" value="crearbarras" />
                                                    <input type="submit" class="submit" value="Actualizar">
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    <!--QR-->
                                    <form class="form-horizontal formvalida" role="form" method="post" id="frmCodigoQr" name="frmCodigoQr" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td valign="top"> Actualizar a todos los usuarios el codigo de QR ?": <input type="radio" name="CodigoQr" value="S">SI <input type="radio" name="CodigoQr" value="N">No <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                                                                                                                                                                                                                else echo $frm["IDClub"];  ?>" />
                                                    <input type="hidden" name="action" id="action" value="crearqr" />
                                                    <input type="submit" class="submit" value="Actualizar">
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                            <!-- PAGE CONTENT ENDS -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->
        <?
        include("cmp/footer.php");
        ?>
    </div><!-- /.main-container -->
</body>

</html>