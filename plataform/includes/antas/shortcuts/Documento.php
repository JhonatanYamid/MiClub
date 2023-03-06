<?php
include("menuclub.php");
$Acceso = "Documento";
?>
<div class="shortcuts">
	<br />
    <strong>Accesos Directos</strong>
    <hr />
    <ul>
        <li><a href="?mod=Socio">Listar <?=$Acceso?></a></li>
        <li><a href="?mod=Socio&action=add">Nuevo <?=$Acceso?></a></li>
    </ul>
</div>