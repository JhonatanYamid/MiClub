<?php
require( "../../../admin/config.inc.php" );
$id_foto=date('YmdHis');//extraemos la fecha del servidor
$filename = "fotos/".$id_foto.'.jpg';//nombre del archivo
$result = file_put_contents( $filename, file_get_contents('php://input') );//renombramos la fotografia y la subimos
if (!$result) {
	print "No se pudo subir al servidor\n";
	exit();
}
if($_GET["Modulo"]=="Socio"):
	$consulta="Update Socio set Foto = '".$id_foto.'.jpg'."' Where IDSocio = '".$_GET["IDRegistro"]."' Limit 1";
	$inserta_foto= $dbo->query($consulta);
	if(copy($filename, SOCIO_DIR.$id_foto.'.jpg')):
		echo "copiada";
	endif;
else:
	$consulta="Update Invitado set FotoFile = '".$id_foto.'.jpg'."' Where IDInvitado = '".$_GET["IDRegistro"]."' Limit 1";
	$inserta_foto= $dbo->query($consulta);
	if(copy($filename, IMGINVITADO_DIR.$id_foto.'.jpg')):
		//echo "copiada";
	endif;

endif;
$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $filename;//generamos la respuesta como la ruta completa
print "$url\n";//20120214060943.jpg







?>
