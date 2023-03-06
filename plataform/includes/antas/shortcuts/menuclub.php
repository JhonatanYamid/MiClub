<?

$Acceso = "CLUB";
if( ($mod == "Socio"))
	$Despliegue = "Socio";
if( ($mod == "BannerApp"))
	$Despliegue = "BannerApp";
if( ($mod == "Seccion") || $mod == "Noticia" )
	$Despliegue = "SeccionNoticia";
if( ($mod == "SeccionEvento") || $mod == "NoticiaEvento" )
	$Despliegue = "SeccionEvento";	
if( ($mod == "Directorio"))
	$Despliegue = "Directorio";
if( ($mod == "Restaurante"))
	$Despliegue = "Restaurante";	
if( $mod == "SeccionGaleria" || $mod == "Galeria")
	$Despliegue = "Galeria";
if( ($mod == "Documento"))
	$Despliegue = "Documento";
if( ($mod == "Servicio"))
	$Despliegue = "Servicio";	
if( ($mod == "Contacto"))
	$Despliegue = "Contacto";		
	

	
?>
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
    if(isset($_GET[id]) || !empty($_SESSION[IDClub]) && $_GET[mod]!="Club")
    {
    ?>
	<br />
    	<strong style="color:#E8501D; font-size: 15px; font-weight: bold;">Menu Del Club </strong>
    <hr />
    <br>
	    <ul id="menupropuesta" class="treeview-red">
			<li><a href="?mod=Club&action=edit&id=<?=$_SESSION[IDClub]?>">Configuracion Club</a></li>
			<?php //<li><a href="?mod=ProveedoresPropuesta&id=<?=$_GET[id]">Ver Proveedores</a></li>?>
			<li><span>Socio</span>
				<ul id="Socio">
					<li><a href="?mod=Socio">Listar Socios</a></li>
					<li><a href="?mod=Socio&action=add">Crear Socio</a></li>
				</ul>
			</li>
			<li><span>Banner</span>
				<ul id="BannerApp">
					<li><a href="?mod=BannerApp">Listar Banner</a></li>
					<li><a href="?mod=BannerApp&action=add">Crear Banner</a></li>
				</ul>
			</li>
			<li><span>Noticias</span>
				<ul id="SeccionNoticia">
					<li><a href="?mod=Seccion">Listar Secciones</a></li>
					<li><a href="?mod=Seccion&action=add">Crear Seccion</a></li>
					<li><a href="?mod=Noticia">Listar Noticia</a></li>
					<li><a href="?mod=Noticia&action=add">Crear Noticia</a></li>
                    
				</ul>
			</li>
            
			<li><span>Eventos</span>
				<ul id="SeccionEvento">
					<li><a href="?mod=SeccionEvento">Listar Secciones</a></li>
					<li><a href="?mod=SeccionEvento&action=add">Crear Seccion</a></li>
					<li><a href="?mod=Evento">Listar Evento</a></li>
					<li><a href="?mod=Evento&action=add">Crear Evento</a></li>
				</ul>
			</li>          
			<li><span>Directorio</span>
				<ul id="Directorio">
					<li><a href="?mod=Directorio">Ver Directorio</a></li>
					<li><a href="?mod=Directorio&action=add">Agregar Registro</a></li>
				</ul>
			</li>      
            <li><span>Restaurante</span>
				<ul id="Restaurante">
					<li><a href="?mod=Restaurante">Ver Restaurantes</a></li>
					<li><a href="?mod=Restaurante&action=add">Agregar Registro</a></li>
				</ul>
			</li>                        
            
			<li><span>Galeria</span>
				<ul id="Galeria">
                	<li><a href="?mod=SeccionGaleria">Listar Secciones</a></li>
					<li><a href="?mod=SeccionGaleria&action=add">Crear Seccion</a></li>
					<li><a href="?mod=Galeria">Listar Galeria</a></li>
					<li><a href="?mod=Galeria&action=add">Crear Galeria</a></li>
				</ul>
			</li>                                    
            
			<li><span>Documentos</span>
				<ul id="Documento">
					<li><a href="?mod=Documento">Listar Documentos</a></li>
					<li><a href="?mod=Documento&action=add">Crear Documento</a></li>
				</ul>
			</li>      
            <li><span>Servicios</span>
				<ul id="Servicio">
					<li><a href="?mod=Servicio">Listar Servicio</a></li>
					<li><a href="?mod=Servicio&action=add">Crear Servicio</a></li>
				</ul>
			</li>       
               
            
            <li><span>Cont&aacute;ctenos</span>
				<ul id="Contacto">
					<li><a href="?mod=Contacto">Listar Contactenos</a></li>
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