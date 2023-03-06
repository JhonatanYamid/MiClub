<br><br>
OPCION PAGOS PENDIENTES
<br><br>

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
                        <td>Cliente</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Carn&eacute;</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Nombre Socio</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Saldo anterior</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Total pagos</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Total compras</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Cuota sostenimiento</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Cobro predial</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Notas credito</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Total a pagar</td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>P&aacute;guese antes de</td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td>Tipo archivo</td>
                        <td>
                            '.xlsx'
                            <input type="hidden" name="FIELD_TEMINATED" id="FIELD_TEMINATED" class="form-control" size="1" value=",">
                        </td>
                    </tr>
                    <tr>
                        <td>Encabezados en la primera Fila?</td>
                        <td>
                            Si
                            <input type="radio" name="IGNORELINE" value="1" border="0" />
                            No
                            <input type="radio" name="IGNORELINE" value="0" checked="" border="0" />

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo empty($frm["IDClub"]) ?  SIMUser::get("club") : $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarpagospendientes" />
                            <input type="submit" class="submit" value="Cargar">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>

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
                        <td>ID Movimiento</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Punto Venta</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Producto</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Cantidad</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Valor Producto</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Fecha(yyyy-mm-dd)</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Numero Factura</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Propina</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Total Factura</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Pagador</td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>Accion</td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
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
                            <input type="radio" name="IGNORELINE" value="1" border="0" />
                            No
                            <input type="radio" name="IGNORELINE" value="0" checked="" border="0" />

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarmovimiento" />
                            <input type="submit" class="submit" value="Cargar">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>

<br><br>
OPCION 2
<br><br>


<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlanoExtracto" name="frmSocioPlanoExtracto" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> Estructura del Archivo </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Accion</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Valor</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Fecha(yyyy-mm-dd)</td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo Excel</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>


                    <tr>
                        <td>Solo dejar la información de este archivo? (se borrar&aacute; los datos antiguos)</td>
                        <td>Si
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            No
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarextracto" />
                            <input type="submit" class="submit" value="Cargar">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>


<br><br>
OPCION CYB
<br><br>


<form class="form-horizontal formvalida" role="form" method="post" id="frmMovimiento3" name="frmMovimiento3" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> Estructura del Archivo </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Apellido</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Nombre</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Tipo Documento</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Documento</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Cuota</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Saldo 1</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Saldo 2</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Descuento 1</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Descuento 2</td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo Excel</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>


                    <tr>
                        <td>Solo dejar la información de este archivo? (se borrar&aacute; los datos antiguos)</td>
                        <td>Si
                            <input type="radio" name="BorrarInfoAntigua" checked="" value="S" border="0" />
                            No
                            <input type="radio" name="BorrarInfoAntigua" value="N" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarcuotasaldo" />
                            <input type="submit" class="submit" value="Cargar">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>


<br><br>
CARGA DTR
<br><br>


<form class="form-horizontal formvalida" role="form" method="post" id="frmMovimientoD" name="frmMovimientoD" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> Estructura del Archivo </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>CODIGO APP</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>FECHA</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>HORA</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>CAMPO</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>AFILIADO</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>TIPO TURNO</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>CADDIE</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>PROFESOR</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>VALOR CLASE</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>LUZ</td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>NOMBRE INVITADO</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>VALOR INVITADO</td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td>RETOS </td>
                    </tr>
                    <tr>
                        <td>15</td>
                        <td>TORNEOS</td>
                    </tr>
                    <tr>
                        <td>16</td>
                        <td>TOTAL</td>
                    </tr>
                    <tr>
                        <td>17</td>
                        <td>TOTAL MES ACTUAL</td>
                    </tr>

                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo Excel</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>


                    <tr>
                        <td>Solo dejar la información de este archivo? (se borrar&aacute; los datos antiguos)</td>
                        <td>Si
                            <input type="radio" name="BorrarInfoAntigua" checked="" value="S" border="0" />
                            No
                            <input type="radio" name="BorrarInfoAntigua" value="N" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarmovimientodtr" />
                            <input type="submit" class="submit" value="Cargar">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</form>

<br><br>
Puntos Socio
<br><br>


<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlanoExtracto" name="frmSocioPlanoExtracto" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <td>
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td colspan="2"> Estructura del Archivo Excel </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Fecha Inicio (yyyy-mm-dd)</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Fecha Fin (yyyy-mm-dd)</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Membresia</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Apellido</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Nombre</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Inscripcion Campaña </td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Visitas </td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Consumos restaurantes y delicatessen </td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Escuelas y talleres deportivos </td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Inscripción en torneos deportivos </td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>Eventos sociales y corporativos </td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>Total Puntos </td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td>Ruta imagen </td>
                    </tr>

                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td>Solo dejar la información de este archivo? (se borrar&aacute; los datos antiguos)</td>
                        <td>Si
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            No
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarpuntos" />
                            <input type="submit" class="submit" value="Cargar">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>



<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioMovimientoCuenta" name="frmSocioMovimientoCuenta" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <br>
    MOvimiento de Cuenta (INVERMETROS)
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
                        <td>codigo</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>nombre</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>nit</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>fecha</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>detalle</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>cheque</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>vrcheque</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>consignado</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>ctacheq</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>tipoc</td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>numero</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>factura</td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td>debito</td>
                    </tr>
                    <tr>
                        <td>14</td>
                        <td>credito</td>
                    </tr>
                    <tr>
                        <td>15</td>
                        <td>saldo</td>
                    </tr>
                    <tr>
                        <td>16</td>
                        <td>basereten</td>
                    </tr>
                    <tr>
                        <td>17</td>
                        <td>porcret o Descuento</td>
                    </tr>
                    <tr>
                        <td>18</td>
                        <td>cencosto o Valor 1 al 15</td>
                    </tr>
                    <tr>
                        <td>19</td>
                        <td>ivacompras o Valor 16 al 30</td>
                    </tr>
                    <tr>
                        <td>20</td>
                        <td>niif</td>
                    </tr>
                    <tr>
                        <td>21</td>
                        <td>nom_niif</td>
                    </tr>
                    <tr>
                        <td>22</td>
                        <td>anulada</td>
                    </tr>
                    <tr>
                        <td>23</td>
                        <td>registro</td>
                    </tr>
                    <tr>
                        <td>24</td>
                        <td>regnom</td>
                    </tr>

                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td>Solo dejar la información de este archivo? (se borrar&aacute; los datos antiguos)</td>
                        <td>Si
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            No
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarmovimientocuenta" />
                            <input type="submit" class="submit" value="Cargar">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</form>


<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioSaldoCartera" name="frmSocioSaldoCartera" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <br>
    Saldo cartera (INVERMETROS)
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
                        <td>codigo</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>nombre</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>1-30</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>31-60</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>61-90</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>mas 90</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>total</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>consignado</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Juridico</td>
                    </tr>


                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td>Solo dejar la información de este archivo? (se borrar&aacute; los datos antiguos)</td>
                        <td>Si
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            No
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargarsaldocartera" />
                            <input type="submit" class="submit" value="Cargar">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</form>

<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioSaldoCartera" name="frmSocioSaldoCartera" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <br>
    Descuentos (INVERMETROS)
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
                        <td>Codigo</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Propietario</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Coeficiente</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Agua Serena</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Descuento Agua serena</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Club House</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Descuento Club House</td>
                    </tr>


                </table>
            </td>
            <td valign="top">
                <table id="simple-table" class="table table-striped table-bordered table-hover">
                    <tr>
                        <td>Archivo</td>
                        <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
                    </tr>
                    <tr>
                        <td>Solo dejar la información de este archivo? (se borrar&aacute; los datos antiguos)</td>
                        <td>Si
                            <input type="radio" name="BorrarInfoAntigua" value="S" border="0" />
                            No
                            <input type="radio" name="BorrarInfoAntigua" value="N" checked="" border="0" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                    else echo $frm["IDClub"];  ?>" />
                            <input type="hidden" name="action" id="action" value="cargardescuento" />
                            <input type="submit" class="submit" value="Cargar">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</form>