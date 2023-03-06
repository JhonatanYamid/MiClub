<?php 
include("menuclub.php");
$Acceso = "Club";
?>
<div class="shortcuts">
	<br />
    <strong>Accesos Directos</strong>
    <hr />
    <ul>
        <li><a href="?mod=Club">Listar <?=$Acceso?></a></li>
        <li><a href="?mod=Club&action=add">Nuevo <?=$Acceso?></a></li>
    </ul>
</div>