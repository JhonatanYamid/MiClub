<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlano" name="frmSocioPlano" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

  <table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
      <td>
        <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
            <td colspan="2"> <?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
          </tr>
          <tr>
            <td>1</td>
            <td>*<?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>2</td>
            <td><?= SIMUtil::get_traduccion('', '', 'AccionPadre', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>3</td>
            <td>*<?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>4</td>
            <td>*<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>5</td>
            <td>*<?= SIMUtil::get_traduccion('', '', 'Apellido', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>6</td>
            <td>*<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>7</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Telefono', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>8</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Direccion', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>9</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Parentesco', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>10</td>
            <td><?= SIMUtil::get_traduccion('', '', 'FechaNacimiento', LANGSESSION); ?> (yyyy-mm-dd)</td>
          </tr>
          <tr>
            <td>11</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Lote', LANGSESSION); ?>(Predio)</td>
          </tr>
          <tr>
            <td>12</td>
            <td><?= SIMUtil::get_traduccion('', '', 'NumeroInvitaciones', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>13</td>
            <td><?= SIMUtil::get_traduccion('', '', 'NumeroAccesos', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>14</td>
            <td><?= SIMUtil::get_traduccion('', '', 'PermiteReservar', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>15</td>
            <td>*<?= SIMUtil::get_traduccion('', '', 'Estado(A-Activo|I-Inactivo|MCA-MorosoconaccesoalApp|MSA-MorososinaccesoalApp|ASC-ActivosinCargoaSocio|SPJ-SuspendidoPorJunta)', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>16</td>
            <td><?= SIMUtil::get_traduccion('', '', 'UsuarioApp', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>17</td>
            <td><?= SIMUtil::get_traduccion('', '', 'ClaveApp', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>18</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>19</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Celular', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>20</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Tipo(Titular,Bebef,etc)', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>21</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Ausente', LANGSESSION); ?> (S/N) </td>
          </tr>
          <tr>
            <td>22</td>
            <td><?= SIMUtil::get_traduccion('', '', 'TipoAusencia(Ausenteporestudio/Ausentenormal)', LANGSESSION); ?> </td>
          </tr>
          <tr>
            <td>23</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Cantidaddeingresos', LANGSESSION); ?> </td>
          </tr>
          <tr>
            <td>24</td>
            <td><?= SIMUtil::get_traduccion('', '', 'FechaInicioAusente', LANGSESSION); ?> (yyyy-mm-dd)</td>
          </tr>
          <tr>
            <td>25</td>
            <td><?= SIMUtil::get_traduccion('', '', 'CodigoCarne', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>26</td>
            <td><?= SIMUtil::get_traduccion('', '', 'FechaInicioCanje(SoloparaTipoCanje)', LANGSESSION); ?> (yyyy-mm-dd)</td>
          </tr>
          <tr>
            <td>27</td>
            <td><?= SIMUtil::get_traduccion('', '', 'FechaFinCanje(SoloparaTipoCanje)', LANGSESSION); ?> (yyyy-mm-dd)</td>
          </tr>
          <tr>
            <td>28</td>
            <td><?= SIMUtil::get_traduccion('', '', 'ObservacionesGeneral', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>29</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Codigo Documento', LANGSESSION); ?>(CI,CE....)</td>
          </tr>

        </table>
      </td>
      <td valign="top">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
            <td><?= SIMUtil::get_traduccion('', '', 'ArchivoExcel', LANGSESSION); ?></td>
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
    -->
          <!--
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
              <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

</form>


<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPermite" name="frmSocioPermite" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

  <br>
  <?= SIMUtil::get_traduccion('', '', 'Actualizar:Permitereservarservicios,hotel,domicilios', LANGSESSION); ?>
  <br>
  <table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
      <td>
        <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
            <td colspan="2"> <?= SIMUtil::get_traduccion('', '', 'EstructuradelArchivo', LANGSESSION); ?> </td>
          </tr>
          <tr>
            <td>1</td>
            <td> <?= SIMUtil::get_traduccion('', '', 'Documento', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>2</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?></td>
          </tr>
          <tr>
            <td>3</td>
            <td><?= SIMUtil::get_traduccion('', '', 'PermiteReservarServicios', LANGSESSION); ?> (S/N)</td>
          </tr>
          <tr>
            <td>4</td>
            <td><?= SIMUtil::get_traduccion('', '', 'PermiteReservarHotel', LANGSESSION); ?> (S/N)</td>
          </tr>
          <tr>
            <td>5</td>
            <td><?= SIMUtil::get_traduccion('', '', 'Permitehacerdomicilios', LANGSESSION); ?> (S/N)</td>
          </tr>
          <tr>
        </table>
      </td>
      <td valign="top">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
            <td><?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?></td>
            <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                    else echo $frm["IDClub"];  ?>" />
              <input type="hidden" name="action" id="action" value="cargarpermitesocio" />
              <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Cargar', LANGSESSION); ?>">

            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>


</form>




<?php if (SIMUser::get("IDPerfil") <= 1) { ?>
  <form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPermitirNoReserva" name="frmSocioPermitirNoReserva" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          <?= SIMUtil::get_traduccion('', '', 'Actualizaratodoslossocioscon:Permitirreservaren:', LANGSESSION); ?>
          <input type="radio" name="PermiteReservar" value="S"><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?> <input type="radio" name="PermiteReservar" value="N"><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="inactivarsocio" />
          <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?>">

        </td>
      </tr>
    </table>
  </form>

  <form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPermitirNoReservaH" name="frmSocioPermitirNoReservaH" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          <?= SIMUtil::get_traduccion('', '', 'Actualizaratodoslossocioscon:Permitirreservarhotelen:', LANGSESSION); ?>
          <input type="radio" name="PermiteReservarHotel" value="S"><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?> <input type="radio" name="PermiteReservarHotel" value="N"><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="inactivarhotel" />
          <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?>">
        </td>
      </tr>
    </table>
  </form>

  <form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPermitirNoReservaD" name="frmSocioPermitirNoReservaD" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          <?= SIMUtil::get_traduccion('', '', 'Actualizaratodoslossocioscon:Permitirhacerdomiciliosen?:', LANGSESSION); ?>
          <input type="radio" name="PermiteDomicilios" value="S"><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?> <input type="radio" name="PermiteDomicilios" value="N"><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="inactivardomicilios" />
          <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?>">
        </td>
      </tr>
    </table>
  </form>
  <!--Barras-->
  <form class="form-horizontal formvalida" role="form" method="post" id="frmCodigoBarras" name="frmCodigoBarras" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          <?= SIMUtil::get_traduccion('', '', 'Crearatodoslossocioselcodigodebarras?:', LANGSESSION); ?>
          <input type="radio" name="CodigoBarras" value="S"><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?> <input type="radio" name="CodigoBarras" value="N"><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="crearbarras" />
          <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?>">
        </td>
      </tr>
    </table>
  </form>

  <!--QR-->
  <form class="form-horizontal formvalida" role="form" method="post" id="frmCodigoQr" name="frmCodigoQr" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          <?= SIMUtil::get_traduccion('', '', 'CrearatodoslossocioselcodigodeQR?:', LANGSESSION); ?>
          <input type="radio" name="CodigoQr" value="S"><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?> <input type="radio" name="CodigoQr" value="N"><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="crearqr" />
          <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?>">
        </td>
      </tr>
    </table>
  </form>
  <!-- Actualizar QR-->
  <form class="form-horizontal formvalida" role="form" method="post" id="frmCodigoQr" name="frmCodigoQr" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          <?= SIMUtil::get_traduccion('', '', 'ActualizaratodoslossocioselcodigodeQR?:', LANGSESSION); ?>
          <input type="radio" name="CodigoQr" value="S"><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?> <input type="radio" name="CodigoQr" value="N"><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="actualizarqr" />
          <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?>">
        </td>
      </tr>
    </table>
  </form>

  <!--Solicitar cambio de clave-->
  <form class="form-horizontal formvalida" role="form" method="post" id="frmCambioClave" name="frmCambioClave" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          <?= SIMUtil::get_traduccion('', '', 'Actualizaratodoslossociosparaobligaracambiarclave?:', LANGSESSION); ?>
          <input type="radio" name="CambiarClave" value="S"><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?> <input type="radio" name="CambiarClave" value="N"><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="actualizarclave" />
          <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?>">
        </td>
      </tr>
    </table>
  </form>

  <!--Solicitareditar perfil-->
  <form class="form-horizontal formvalida" role="form" method="post" id="frmEditaPerfil" name="frmEditaPerfil" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          <?= SIMUtil::get_traduccion('', '', 'Actualizaratodoslossociosparaobligaraeditarperfil?:', LANGSESSION); ?>
          <input type="radio" name="EditaPerfil" value="S"><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?> <input type="radio" name="EditaPerfil" value="N"><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="actualizarperfil" />
          <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?>">
        </td>
      </tr>
    </table>
  </form>

  <form class="form-horizontal formvalida" role="form" method="post" id="frmEditaPerfil" name="frmEditaPerfil" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          <?= SIMUtil::get_traduccion('', '', 'Actualizaratodoslossociosparaobligaraeditarfoto?:', LANGSESSION); ?>
          <input type="radio" name="EditaPerfilFoto" value="S"><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?> <input type="radio" name="EditaPerfilFoto" value="N"><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="actualizarperfilfoto" />
          <input type="submit" class="submit" value="<?= SIMUtil::get_traduccion('', '', 'Actualizar', LANGSESSION); ?>">
        </td>
      </tr>
    </table>
  </form>


<?php } ?>