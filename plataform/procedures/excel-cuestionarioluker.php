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


$sql_reporte = "Select * From CuestionarioLuker 
                                                 
					Where IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . " Order By IDCuestionarioLuker DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "CuestionarioLuker_Reporte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    /* DATOS EMPLEADO*/
    //$html .= "<th>ID SOCIO</th>";
    $html .= "<th>Nivel de rol al cual pertenece dentro de la  organización</th>";
    $html .= "<th>Nombre del Proceso o área dónde se encuentra su rol</th>";
    $html .= "<th>Nivel de Estrato Socio economico</th>";
    $html .= "<th>Número de Personas que componen el núcleo familiar principal</th>";
    $html .= "<th>Número de personas de la familia  que tienen un trabajo temporal,  fijo o indefinido. Núcleo Familiar Principal</th>";
    $html .= "<th>¿Dentro de qué rango se encuentra los ingresos totales del grupo familiar mensualmente?</th>";
    $html .= "<th>Tipo de Vivienda</th>";
    $html .= "<th>Nivel De Estudio</th>";
    $html .= "<th>Departamento de Nacimiento</th>";
    $html .= "<th>Municipio De Nacimiento</th>";
    $html .= "<th>Rango de Edad</th>";
    $html .= "<th>Perteneces o te identificas con algún grupo étnico</th>";
    $html .= "<th>Perteneces o te identificas con algún grupo siguiente:</th>";
    $html .= "<th>Si tienes alguna discapacidad, selecciona la que más se acerca:</th>";
    $html .= "<th>Cual Otra Discapacidad</th>";
    $html .= "<th>con qué género te identificas más:</th>";
    $html .= "<th>Cual Genero</th>";
    $html .= "<th>Con cuál orientación sexual te identificas más:</th>";
    $html .= "<th>Eres madre o padre cabeza de familia</th>";
    $html .= "<th>Número de  hijos</th>";
    $html .= "<th>Rango de Edad de los hijos</th>";
    $html .= "<th>Durante las horas laborales  quién se hace cargo del cuidado de los hijos:</th>";
    $html .= "<th>Cuales otros</th>";
    $html .= "<th>Tienes emprendimientos, negocios propios o del núcleo familiar</th>";
    $html .= "<th>Cual Emprendimiento</th>";
    $html .= "<th>Posees servicios de salud adicionales </th>";
    $html .= "<th>Cual Servicios de salud</th>";
    $html .= "<th>Cuáles de los siguientes temas son de tu interés en temas de voluntariado</th>";
    $html .= "<th>Cual Tema De Interes</th>";
    $html .= "<th>Cuáles de los siguientes son temas de interés y que realizas dentro de tu tiempo libre </th>";
    $html .= "<th>Cual Tema De Interes en tiempo Libre</th>";
    $html .= "<th>Nombre</th>";
    $html .= "<th>Comentarios</th>";





    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $bitacora = "";
        unset($array_datos_seguimiento);
        $html .= "<tr>";
        /*DATOS EMPLEADOS*/
        // $html .= "<td>" . $Datos["IDSocio"] . "</td>";
        //$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
        //$html .= "<td>" . remplaza_tildes($Datos["NombrePropietario"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NivelDeRolAlCualPerteneceDentroDeLaOrganizacion"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NombreDelProcesoOAreaDondeSeEncuentraSuRol"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NivelDeEstratoSocieconomico"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NumeroDePersonasQueComponenElNucleoFamiliarPrincipal"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NumeroDePersonasDeLaFamiliaQueTienenUnTrabajo"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["RangoIngresosTotalesGrupoFamiliarMensualmente"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TipoDeVivienda"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NivelDeEstudio"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["DepartamentoDeNacimiento"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["MunicipioDeNacimiento"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["RangoDeEdad"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["GrupoEtnico"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["PertenecesAlgunGrupo"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Discapacidad"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CualDiscapacidad"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Genero"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CualGenero"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["OrientacionSexual"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["MadreOPadreCabezaDeFamilia"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["NumeroHijos"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["RangoEdadHijos"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CuidadoDeLosHijos"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CualCuidadoDeLosHijos"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Emprendimientos"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CualEmprendimientos"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["ServiciosDeSalud"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CualServiciosDeSalud"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TemasDeInteres"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CualTemaDeInteres"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["TemasDeInteresTiempoLibre"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["CualTemaDeInteresTiempoLibre"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Nombre"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Comentario"]) . "</td>";
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