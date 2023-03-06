<?php
	require( "config.inc.php" );
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	require_once(LIBDIR.'nusoap/lib/nusoap.php');

//Evento
//$tabla="Evento";
//$IndiceTabla="IDEvento";
//$array_campos=array("Valor","Titular","Introduccion");

//$tabla="ClubModulo";
//$IndiceTabla="IDClubModulo";
//$array_campos=array("Titulo","TituloLateral");

//$tabla="Noticia";
//$IndiceTabla="IDNoticia";
//$array_campos=array("Titular","Subtitular","Introduccion","");

//$tabla="Votacion";
//$IndiceTabla="IDVotacion";
//$array_campos=array("Nombre","Descripcion");

//$tabla="Votacion";
//$IndiceTabla="IDVotacion";
//$array_campos=array("Nombre","Descripcion");

//$tabla="PreguntaVotacion";
//$IndiceTabla="IDPregunta";
//$array_campos=array("EtiquetaCampo");

$tabla="Pregunta";
$IndiceTabla="IDPregunta";
$array_campos=array("EtiquetaCampo");

$tabla="Documento";
$IndiceTabla="IDDocumento";
$array_campos=array("Nombre","Descripcion");

$tabla="ServicioElemento";
$IndiceTabla="IDServicioElemento";
$array_campos=array("Nombre","Descripcion");

$tabla="Producto";
$IndiceTabla="IDProducto";
$array_campos=array("Nombre","Descripcion");

$tabla="Area";
$IndiceTabla="IDArea";
$array_campos=array("Nombre","Responsable");

$tabla="TipoPqr";
$IndiceTabla="IDTipoPqr";
$array_campos=array("Nombre","Descripcion");

$tabla="DirectorioSocio";
$IndiceTabla="IDDirectorioSocio";
$array_campos=array("Nombre","Descripcion");

$tabla="ServicioClub";
$IndiceTabla="IDServicioMaestro";
$array_campos=array("Nombre","Descripcion");

$tabla="Directorio";
$IndiceTabla="IDDirectorio";
$array_campos=array("Nombre","Descripcion");

$tabla="Encuesta";
$IndiceTabla="IDEncuesta";
$array_campos=array("Nombre","Descripcion");

$tabla="Clasificado";
$IndiceTabla="IDClasificado";
$array_campos=array("Nombre","Descripcion");

$tabla="ObjetoPerdido";
$IndiceTabla="IDObjetoPerdido";
$array_campos=array("Nombre","Descripcion");

$tabla="Oferta";
$IndiceTabla="IDOferta";
$array_campos=array("NombreEmpresa","Cargo","Ciudad");

$tabla="Beneficio";
$IndiceTabla="IDBeneficio";
$array_campos=array("Nombre","Descripcion","Introduccion");

$tabla="Socio";
$IndiceTabla="IDSocio";
$array_campos=array("Predio");

$tabla="ServicioTipoReserva";
$IndiceTabla="IDServicioTipoReserva";
$array_campos=array("Nombre");

$tabla="Usuario";
$IndiceTabla="IDUsuario";
$array_campos=array("Nombre");

$tabla="ServicioMaestro";
$IndiceTabla="IDServicioMaestro";
$array_campos=array("Nombre","Descripcion","LabelElemento","LabelTipoReserva");

$tabla="Pqr";
$IndiceTabla="IDPqr";
$array_campos=array("Asunto","Descripcion","NombreColaborador","ApellidoColaborador");

$tabla="Restaurante";
$IndiceTabla="IDRestaurante";
$array_campos=array("Nombre","Menu","Horario","Localizacion");


//$sql_tabla="SELECT * FROm ".$tabla." where IDEvento in (4364,4348,4346,4279,4275,4269,4153) ";
echo $sql_tabla="SELECT * FROm ".$tabla." where 1 ";
$r_tabla=$dbo->query($sql_tabla);
while($row_tabla=$dbo->fetchArray($r_tabla)){

	foreach($array_campos as $CampoTabla){
		$convertido=$row_tabla[$CampoTabla];
		$convertido=str_replace("Ã¡","á",$convertido);
		$convertido=str_replace("í¡","á",$convertido);
		$convertido=str_replace("Ã","Á",$convertido);

		$convertido=str_replace("Ã©","é",$convertido);
		$convertido=str_replace("Ã­","í",$convertido);
		$convertido=str_replace("Ã","Í",$convertido);

		$convertido=str_replace("í³","ó",$convertido);
		$convertido=str_replace("Ã³","ó",$convertido);
		$convertido=str_replace("í“","Ó",$convertido);
		$convertido=str_replace("Ã“","Ó",$convertido);


		$convertido=str_replace("íº","ú",$convertido);
		$convertido=str_replace("íš","ú",$convertido);
		$convertido=str_replace("Ãº","ú",$convertido);

		$convertido=str_replace("í±","ñ",$convertido);
		$convertido=str_replace("Ã±","ñ",$convertido);
		$convertido=str_replace("Ìƒ","ñ",$convertido);
		$convertido=str_replace("Ã‘","Ñ",$convertido);


		$convertido=str_replace("Â¡","¡",$convertido);
		$convertido=str_replace("Â¿","¿",$convertido);
		$convertido=str_replace("Âº","o",$convertido);




		echo "<br>".$actualiza="UPDATE ".$tabla." SET ".$CampoTabla." = '".$convertido."' WHERE ".$IndiceTabla." = '".$row_tabla[$IndiceTabla]."'";
		$dbo->query($actualiza);
	}




}

echo "<br><br>FIN";
exit;
?>
