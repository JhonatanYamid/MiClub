<?php
require("../../../admin/config.inc.php");
if ($_GET['idservicio'] != '') {

    $sql_servicio = "Select * From ServicioElemento Where IDServicio = '" . $_GET['idservicio'] . "' and Publicar = 'S' Order by IDServicioElemento";
    $result_servicio = $dbo->query($sql_servicio);
    while ($row_servicio = $dbo->fetchArray($result_servicio)) :



        echo '<option value="' . $row_servicio["IDServicioElemento"] . '">' . $row_servicio["Nombre"] . '</option>';
    endwhile;
}
