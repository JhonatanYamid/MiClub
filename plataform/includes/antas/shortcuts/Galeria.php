<?php
include("menuclub.php");
$Acceso = "Galeria";
?>
<div class="shortcuts">
	<br />
    <strong>Accesos Directos</strong>
    <hr />
    <ul>
        <li><a href="?mod=Galeria">Listar <?=$Acceso?></a></li>
        <li><a href="?mod=Galeria&action=add">Nuevo <?=$Acceso?></a></li>
    </ul>
</div>