<?php
include("menuclub.php");
$Acceso = "Directorio";
?>
<div class="shortcuts">
	<br />
    <strong>Accesos Directos</strong>
    <hr />
    <ul>
        <li><a href="?mod=Directorio">Listar <?=$Acceso?></a></li>
        <li><a href="?mod=Directorio&action=add">Nuevo <?=$Acceso?></a></li>
    </ul>
</div>