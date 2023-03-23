<?php
$numerodocumento = $_POST['NumeroDocumento'];
$query = "SELECT CONCAT(RC.nombre,' ',RC.apellido) as nombre FROM RegistroCorredor as RC join  Carrera as C ON C.IDCarrera = RC.IDCarrera WHERE RC.numerodocumento = $numerodocumento AND C.Exoneracion = 'S'";
$nombre = '';
$numerodocumento = $_POST['NumeroDocumento'];
try {
	if ($_POST['NumeroDocumento']) {
		$result = $dbo->query($query);
		$row = $dbo->fetch($result);

		if (isset($row['nombre']) && $row['nombre']) {
			$nombre = $row['nombre'];
			$encontrado = true;
			$texto = 'El usuario ' . $row['nombre'] . ' se encuentra exento';
		} else {
			$encontrado = false;
			$texto = 'El usuario no ha sido encontrado';
		}
	} else {
		$texto = 'Ingrese un número de cedula para consultar';
	}
} catch (PDOException $e) {
	$encontrado = false;
	$texto = 'Ha ocurrido un error con la busqueda, intente de nuevo por favor';
}
