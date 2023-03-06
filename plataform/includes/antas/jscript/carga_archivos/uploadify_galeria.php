<?php

require( "../../config.inc.php" );
$dbo =& SIMDB::get();
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = '/file/carrera/galeria'; // Relative to the root

$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	$nombre_imagen=$_POST['IDCarrera']."-".$_POST['fechahora'].$_FILES['Filedata']['name'];
	$targetFile = rtrim($targetPath,'/') . '/' . $nombre_imagen;
	
	// Validate the file type
	$fileTypes = array('jpg','png','gif','JPG','PNG','GIF','jpeg','JPEG','jpeg','JPEG'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		//creo el registro en la base de datos
		$sql_foto="Insert into GaleriaCarrera (IDCarrera, Nombre, File, FechaTrCr, UsuarioTrCr)
					Values ('".$_POST['IDCarrera']."','".$_FILES['Filedata']['name']."','".$nombre_imagen."',NOW(),'Admin')";
		$dbo->query($sql_foto);
		
		echo '1';
	} else {
		echo 'Invalid file type.';
	}
}
?>