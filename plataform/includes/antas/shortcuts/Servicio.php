<?
include("menuclub.php");
$Acceso = "Servicio";
?>
<div class="shortcuts">
	<br />
    <strong>Accesos Directos</strong>
    <hr />
    <ul>
        <li><a href="?mod=Servicio">Listar <?=$Acceso?></a></li>
        <li><a href="?mod=Servicio&action=add">Nuevo <?=$Acceso?></a></li>
    </ul>
</div>