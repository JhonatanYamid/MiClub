<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlano" name="frmSocioPlano" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

  <table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
      <td>
        <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
            <td colspan="2"> Estructura del Archivo </td>
          </tr>
          <tr>
            <td>1</td>
            <td>*Acci&oacute;n</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Accion Padre</td>
          </tr>
          <tr>
            <td>3</td>
            <td>*Numero Documento</td>
          </tr>
          <tr>
            <td>4</td>
            <td>*Nombre</td>
          </tr>
          <tr>
            <td>5</td>
            <td>*Apellido</td>
          </tr>
          <tr>
            <td>6</td>
            <td>*Email</td>
          </tr>
          <tr>
            <td>7</td>
            <td>Telefono</td>
          </tr>
          <tr>
            <td>8</td>
            <td>Direccion</td>
          </tr>
          <tr>
            <td>9</td>
            <td>Parentesco</td>
          </tr>
          <tr>
            <td>10</td>
            <td>Fecha Nacimiento (yyyy-mm-dd)</td>
          </tr>
          <tr>
            <td>11</td>
            <td>Lote</td>
          </tr>
          <tr>
            <td>12</td>
            <td>Numero Invitaciones</td>
          </tr>
          <tr>
            <td>13</td>
            <td>Numero Accesos</td>
          </tr>
          <tr>
            <td>14</td>
            <td>Pemite Reservar</td>
          </tr>
          <tr>
            <td>15</td>
            <td>*Estado (A - Activo | I - Inactivo | MCA - Moroso con acceso al App | MSA - Moroso sin acceso al App | ASC - Activo sin Cargo a Socio | SPJ - Suspendido Por Junta)</td>
          </tr>
          <tr>
            <td>16</td>
            <td>Usuario App</td>
          </tr>
          <tr>
            <td>17</td>
            <td>Clave App</td>
          </tr>
          <tr>
            <td>18</td>
            <td>Categoria</td>
          </tr>
          <tr>
            <td>19</td>
            <td>Celular</td>
          </tr>
          <tr>
            <td>20</td>
            <td>Tipo(Titular,Bebef,etc)</td>
          </tr>
          <tr>
            <td>21</td>
            <td>Ausente (S/N) </td>
          </tr>
          <tr>
            <td>22</td>
            <td>Cantidad de ingresos </td>
          </tr>
          <tr>
            <td>23</td>
            <td>Fecha Inicio Ausente (yyyy-mm-dd)</td>
          </tr>
          <tr>
            <td>24</td>
            <td>Codigo Carne</td>
          </tr>
          <tr>
            <td>25</td>
            <td>Fecha Inicio Canje (Solo para Tipo Canje) (yyyy-mm-dd)</td>
          </tr>
          <tr>
            <td>26</td>
            <td>Fecha Fin Canje (Solo para Tipo Canje) (yyyy-mm-dd)</td>
          </tr>
          <tr>
            <td>27</td>
            <td>Observaciones General</td>
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
              <input type="submit" class="submit" value="Cargar">

            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

</form>


<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPermite" name="frmSocioPermite" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

  <br>
  Actualizar: Permite reservar servicios, hotel, domicilios
  <br>
  <table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
      <td>
        <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
            <td colspan="2"> Estructura del Archivo </td>
          </tr>
          <tr>
            <td>1</td>
            <td> Documento</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Accion</td>
          </tr>
          <tr>
            <td>3</td>
            <td>Permite Reservar Servicios (S/N)</td>
          </tr>
          <tr>
            <td>4</td>
            <td>Permite Reservar Hotel (S/N)</td>
          </tr>
          <tr>
            <td>5</td>
            <td>Permite hacer domicilios (S/N)</td>
          </tr>
          <tr>
        </table>
      </td>
      <td valign="top">
        <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
            <td>Archivo</td>
            <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                    else echo $frm["IDClub"];  ?>" />
              <input type="hidden" name="action" id="action" value="cargarpermitesocio" />
              <input type="submit" class="submit" value="Cargar">

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
          Actualizar a todos los socios con: "Permitir reservar en":
          <input type="radio" name="PermiteReservar" value="S">SI <input type="radio" name="PermiteReservar" value="N">No
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="inactivarsocio" />
          <input type="submit" class="submit" value="Actualizar">

        </td>
      </tr>
    </table>
  </form>

  <form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPermitirNoReservaH" name="frmSocioPermitirNoReservaH" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          Actualizar a todos los socios con: "Permitir reservar hotel en":
          <input type="radio" name="PermiteReservarHotel" value="S">SI <input type="radio" name="PermiteReservarHotel" value="N">No
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="inactivarhotel" />
          <input type="submit" class="submit" value="Actualizar">
        </td>
      </tr>
    </table>
  </form>

  <form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPermitirNoReservaD" name="frmSocioPermitirNoReservaD" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          Actualizar a todos los socios con: "Permitir hacer domicilios en ?":
          <input type="radio" name="PermiteDomicilios" value="S">SI <input type="radio" name="PermiteDomicilios" value="N">No
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="inactivardomicilios" />
          <input type="submit" class="submit" value="Actualizar">
        </td>
      </tr>
    </table>
  </form>
  <!--Barras-->
  <form class="form-horizontal formvalida" role="form" method="post" id="frmCodigoBarras" name="frmCodigoBarras" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          Crear a todos los socios el codigo de barras ?":
          <input type="radio" name="CodigoBarras" value="S">SI <input type="radio" name="CodigoBarras" value="N">No
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
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
        <td valign="top">
          Crear a todos los socios el codigo de QR ?":
          <input type="radio" name="CodigoQr" value="S">SI <input type="radio" name="CodigoQr" value="N">No
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="crearqr" />
          <input type="submit" class="submit" value="Actualizar">
        </td>
      </tr>
    </table>
  </form>
  <!-- Actualizar QR-->
  <form class="form-horizontal formvalida" role="form" method="post" id="frmCodigoQr" name="frmCodigoQr" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          Actualizar a todos los socios el codigo de QR ?":
          <input type="radio" name="CodigoQr" value="S">SI <input type="radio" name="CodigoQr" value="N">No
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="actualizarqr" />
          <input type="submit" class="submit" value="Actualizar">
        </td>
      </tr>
    </table>
  </form>

  <!--Solicitar cambio de clave-->
  <form class="form-horizontal formvalida" role="form" method="post" id="frmCambioClave" name="frmCambioClave" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          Actualizar a todos los socios para obligar a cambiar clave ?":
          <input type="radio" name="CambiarClave" value="S">SI <input type="radio" name="CambiarClave" value="N">No
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="actualizarclave" />
          <input type="submit" class="submit" value="Actualizar">
        </td>
      </tr>
    </table>
  </form>

  <!--Solicitareditar perfil-->
  <form class="form-horizontal formvalida" role="form" method="post" id="frmEditaPerfil" name="frmEditaPerfil" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <td valign="top">
          Actualizar a todos los socios para obligar a editar perfil ?":
          <input type="radio" name="EditaPerfil" value="S">SI <input type="radio" name="EditaPerfil" value="N">No
          <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                else echo $frm["IDClub"];  ?>" />
          <input type="hidden" name="action" id="action" value="actualizarperfil" />
          <input type="submit" class="submit" value="Actualizar">
        </td>
      </tr>
    </table>
  </form>


<?php } ?>