<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
    $texto = str_replace("ñ", "&ntilde;", $texto);
    $texto = str_replace("á", "&aacute;", $texto);
    $texto = str_replace("é", "&eacute;", $texto);
    $texto = str_replace("í", "&iacute;", $texto);
    $texto = str_replace("ó", "&oacute;", $texto);
    $texto = str_replace("ú", "&uacute;", $texto);
    return $texto;
}

if (!empty($_POST["FechaTrEd"]) && !empty($_POST["FechaTrEd"])) {
    $condicion_fecha = " and FechaTrEd  >= '" . $_POST["FechaTrEd"] . "'  or FechaTrEd <= '" . $_POST["FechaTrEd"] . "'";
}


$sql_reporte = "Select * From PrimaderaEmpleados pe inner join PrimaderaBeneficiario pb on pe.IDPrimaderaEmpleados=pb.IDPrimaderaEmpleados
                                                  inner join PrimaderaEstudio pes on    pe.IDPrimaderaEmpleados=pes.IDPrimaderaEmpleados    
					Where pe.IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . " Order By pe.IDPrimaderaEmpleados DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "PrimaderaEmpleados_Reporte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    /* DATOS EMPLEADO*/
    $html .= "<th>ID SOCIO</th>";
    $html .= "<th>NOMBRE SOCIO</th>";
    $html .= "<th>CEDULA EMPLEADO</th>";
    $html .= "<th>NOMBRE EMPLEADO</th>";
    $html .= "<th>APELLIDO1 EMPLEADO</th>";
    $html .= "<th>APELLIDO2 EMPLEADO</th>";
    $html .= "<th>TIPO DE SANGRE EMPLEADO</th>";
    $html .= "<th>RH EMPLEADO</th>";
    $html .= "<th>ESTADO CIVIL EMPLEADO</th>";
    $html .= "<th>PROFESION EMPLEADO</th>";
    $html .= "<th>TARJETA PROFESIONAL EMPLEADO</th>";
    $html .= "<th>DIRECCION EMPLEADO</th>";
    $html .= "<th>TELEFONO EMPLEADO</th>";
    $html .= "<th>BARRIO EMPLEADO</th>";
    $html .= "<th>TIENE VIVIENDA PROPIA? EMPLEADO</th>";
    $html .= "<th>ADQUIRIDA POR MEDIO DE PRIMADERA? EMPLEADO</th>";
    $html .= "<th>NOMBRE ACUDIENTE</th>";
    $html .= "<th>TELEFONO ACUDIENTE</th>";
    $html .= "<th>DIRECCION ACUDIENTE</th>";
    $html .= "<th>NIVEL ACADEMICO</th>";
    $html .= "<th>ROL EN LA FAMILIA</th>";
    $html .= "<th>ZONA VIVIENDA</th>";
    $html .= "<th>ESTRATO SOCIO-ECONOMICO</th>";
    $html .= "<th>ES CABEZA DE HOGAR</th>";
    $html .= "<th>LE HAN DIAGNOSTICADO ALGUNA ENFERMEDAD</th>";
    $html .= "<th>TIENE ALGUN TIPO DE DISCAPACIDAD</th>";
    $html .= "<th>CUAL DISCAPACIDAD?</th>";
    $html .= "<th>TIENE INTERES EN ADQUIRIR VIVIENDA?</th>";
    $html .= "<th>COMPARTE DOMICILIO CON CONYUGUE</th>";
    $html .= "<th>CORREO ELECTRONICO</th>";
    $html .= "<th>CLASE DE VIVIENDA QUE VIVE ACTUALMENTE</th>";
    $html .= "<th>TIPO DE VIVIENDA QUE POSEE</th>";
    $html .= "<th>CUAL ENFERMEDAD</th>";
    $html .= "<th>CON CUANTAS PERSONAS VIVE</th>";
    $html .= "<th>CUANTAS PERSONAS DEPENDEN ECONOMICAMENTE DE USTED</th>";
    $html .= "<th>CUANTOS HIJOS TIENE</th>";
    $html .= "<th>FOTO</th>";

    /*DATOS BENEFICIARIOS */
    $html .= "<th>DATOS BENEFICIARIOS</th>";
    $html .= "<th>NOMBRE</th>";
    $html .= "<th>APELLIDO1</th>";
    $html .= "<th>APELLIDO2</th>";
    $html .= "<th>PARENTESCO</th>";
    $html .= "<th>SEXO</th>";
    $html .= "<th>TIPO DE IDENTIDAD</th>";
    $html .= "<th>NUMERO DE DOCUMENTO</th>";
    $html .= "<th>PAIS DE EXPEDICION</th>";
    $html .= "<th>GRUPO SANGUINEO</th>";
    $html .= "<th>RH</th>";
    $html .= "<th>FECHA DE NACIMIENTO</th>";
    $html .= "<th>PAIS DE NACIMIENTO</th>";
    $html .= "<th>ESTADO CIVIL</th>";
    $html .= "<th>BENEFICIARIO SALUD</th>";
    $html .= "<th>PROFESION</th>";
    $html .= "<th>DIRECCION</th>";
    $html .= "<th>TELEFONO</th>";
    $html .= "<th>LE HAN DIAGNOSTICADO ALGUNA ENFERMEDAD?</th>";
    $html .= "<th>CUAL ENFERMEDAD?</th>";
    $html .= "<th>TIENE ALGUNA DISCAPACIDAD?</th>";
    $html .= "<th>CORREO ELECTRONICO FAMILIAR 2</th>";
    $html .= "<th>CUAL DISCAPACIDAD?</th>";
    $html .= "<th>COMPARTE DOMICILIO?</th>";
    $html .= "<th>ACTIVO</th>";
    $html .= "<th>MOTIVO DE INACTIVACION</th>";
    $html .= "<th>DEPENDE ECONOMICAMENTE</th>";
    $html .= "<th>ARCHIVO</th>";

    /*DATOS ESTUDIOS */
    $html .= "<th>DATOS ESTUDIOS</th>";
    $html .= "<th> NIVEL DE ESTUDIO</th>";
    $html .= "<th> PAIS DONDE ESTUDIO</th>";
    $html .= "<th> INSTITUCION</th>";
    $html .= "<th> TITULO</th>";
    $html .= "<th> FECHA DE TERMINACION</th>";
    $html .= "<th> OBSERVACIONES</th>";
    $html .= "<th> ADJUNTO</th>";


    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $bitacora = "";
        unset($array_datos_seguimiento);
        $html .= "<tr>";
        /*DATOS EMPLEADOS*/
        $html .= "<td>" . $Datos["IDSocio"] . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_CEDULA"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_NOMBRE"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_APELLIDO1"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_APELLIDO2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_TIPO_SANGRE"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_SANGRE_RH"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_ESTADO_CIVIL"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_PROFESION"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_MATRICULA_PROFESIONAL"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_DIRECCION"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_TELEFONO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_BARRIO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_VIVIENDA"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_ADQ_EMPRESA"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_PERSONA_ACUDIENTE"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_TELEFONO_ACUDIENTE"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EMP_DIRECCION_ACUDIENTE"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TIEM_CAMPO_ALF1"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TIEM_CAMPO_ALF2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TIEM_CAMPO_ALF4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_NUM3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_IND1"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_IND2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_IND3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_NUM1"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_IND5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_IND6"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_ALF1"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_ALF2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_ALF3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_ALF5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_NUM2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_NUM4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_NUM5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["FOTO"]) . "</td>";

        /*DATOS BENEFICIARIOS */
        $html .= "<td>" . remplaza_tildes($Datos[""]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NOMBRE"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["APELLIDO1"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["APELLIDO2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["RELAC_FAM"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["SEXO"])   . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TIPO_IDENT"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["IDENT_NUM"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["UGN1_CODIGO_IDENT"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENE_TIPO_SANGRE"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENE_SANGRE_RH"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["FEC_NACIO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["UGN1_CODIGO_NACI"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EST_CIVIL"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_IND4"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["PROFESION"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["DIRECCION"])   . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TELEFONO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_IND2"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_ALF5"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_IND3"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENEF_CAMPO_NUM1"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["RESIDE_EMPLEADO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["BENE_ESTADO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["MOTIVO_INACTIVACION"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["DEPENDIENTE"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["ARCHIVO"]) . "</td>";

        /*DATOS ESTUDIOS */
        $html .= "<td>" . remplaza_tildes($Datos[""]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos[""]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NEST_CODIGO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["UGN1_CODIGO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TERC_DOCUMENTO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["ESXB_TITULO"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["ESXB_FECHA_RET"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["ESXB_IDIOMAS"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Archivo"]) . "</td>";
    }
    $html .= "</table>";

    //construimos el excel
    header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
    header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>

    <body>
        <?php
        echo $html;
        ?>
    </body>

    </html>
<?php
    exit();
}
?>