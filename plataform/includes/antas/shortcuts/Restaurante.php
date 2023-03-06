<?php
include("menuclub.php");
$Acceso = "Restaurante";
?>
<div class="shortcuts">
	<br />
    <strong>Accesos Directos</strong>
    <hr />
    <ul>
        <li><a href="?mod=Restaurante">Listar <?=$Acceso?></a></li>
        <li><a href="?mod=Restaurante&action=add">Nuevo <?=$Acceso?></a></li>
    </ul>
</div>