<?php
require("../../../admin/config.inc.php");
if ($_GET['idpais'] != '') {
    $sql_servicio = "Select Nombre,IDCiudad From Ciudad Where IDPais = '" . $_GET['idpais'] . "' and Publicar = 'S' Order by Nombre";
    $result_servicio = $dbo->query($sql_servicio);
    while ($row_servicio = $dbo->fetchArray($result_servicio)) :




        echo '<option value="' . $row_servicio["IDCiudad"] . '"' . "class='" . $frm["IDCiudad"] . "'>"  . $row_servicio["Nombre"] . '</option>';
    endwhile;
}
