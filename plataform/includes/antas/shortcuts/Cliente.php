<?
$Acceso = "CLUB";
if( ($mod == "Cuestionario") || ( $mod == "Informe"))
	$Despliegue = "Consultoria";
if( ($mod == "EntregaCampo") || ( $mod == "Precuanty") || ( $mod == "Precuality"))
	$Despliegue = "Operaciones";
if( ($mod == "Facturas") || ( $mod == "EntregaProveedor"))
	$Despliegue = "Finanzas";
	
?>
<script src="jscript/treeview/jquery.cookie.js" type="text/javascript"></script>
<script src="jscript/treeview/jquery.treeview.js" type="text/javascript"></script>
<link rel="stylesheet" href="jscript/treeview/jquery.treeview.css" />
<script type="text/javascript">
$(document).ready(function(){
	
	// third example
	$("#menupropuesta").treeview({
		animated: "fast",
		collapsed: true,
		unique: true,
		persist: "location"
	});

	$("#<?php echo $Despliegue?>").css("display","block");
		

});
</script>
<div style="border-right:1px solid #DDDDDD;">
<?php 
    if(isset($_GET[id]))
    {
    ?>
	<br />
    	<strong style="color:#E8501D; font-size: 15px; font-weight: bold;">Menu Del Club </strong>
    <hr />
    <br>
	    <ul id="menupropuesta" class="treeview-red">
			<li><a href="?mod=Propuesta&action=edit&id=<?=$id?>">Ver Datos Club</a></li>
			<?php //<li><a href="?mod=ProveedoresPropuesta&id=<?=$id">Ver Proveedores</a></li>?>
			<li><span>Socios</span>
				<ul id="Consultoria">
					<li><a href="?mod=Cuestionario&id=<?=$id?>">Crear Socios</a></li>
					<?php //<li><a href="?mod=Cuestionario&action=add&id=<?=$id>">Nuevo Cuestionarios</a></li>?>
					<li><a href="?mod=Informe&id=<?=$id?>">Informes</a></li>
					<? //<li><a href="?mod=Informe&action=add&id=<?=$id>">Nuevo Informe</a></li>?>
					<li><a href="?mod=CargarBrief&id=<?=$id?>">Cargar Brief</a></li>
					<li><a href="?mod=CargarPresupuesto&id=<?=$id?>">Cargar Presupuesto</a></li>
                    <li><a href="?mod=Otros&id=<?=$id?>">Otros Documentos</a></li>
					
				</ul>
			</li>
			<li><span>Operaciones</span>
				<ul id="Operaciones">
					<li><a href="?mod=EntregaCampo&id=<?=$id?>"> Entrega Campo</a></li>
					<? //<li><a href="?mod=EntregaCampo&action=add&id=<?=$id>">Nuevo Entrega Campo</a></li>?>
					<li><a href="?mod=Precuanty&id=<?=$id?>">Pre-cuanti</a></li>
					<? //<li><a href="?mod=Precuanty&action=add&id=<?=$id>">Nuevo Pre-cuanti</a></li>?>
					<li><a href="?mod=Precuality&id=<?=$id?>">Pre-cuali</a></li>
					<? //<li><a href="?mod=Precuality&action=add&id=<?=$id>">Nuevo Pre-cuali</a></li>?>
				</ul>
			</li>
			<li><span>Finanzas</span>
				<ul id="Finanzas">
					<li><a href="?mod=Facturas&id=<?=$id?>">Ver Facturas</a></li>
					<li><a href="?mod=Facturas&action=add&id=<?=$id?>">Nueva Factura</a></li>
					<li><a href="?mod=EntregaProveedor&id=<?=$id?>">Ver Entrega Proveedor</a></li>
					<li><a href="?mod=EntregaProveedor&action=add&id=<?=$id?>">Nuevo Entrega Proveedor</a></li>
				</ul>
			</li>
		</ul>
	<br />
	<br />
	<br />
	<br />
	<?php 	
    }
    ?>
</div>
<div class="shortcuts">
	<br />
    <strong>Accesos Directos</strong>
    <hr />
    <ul>
        <li><a href="?mod=Cliente">Listar <?=$Acceso?></a></li>
        <li><a href="?mod=Cliente&action=add">Nuevo <?=$Acceso?></a></li>
    </ul>
</div>