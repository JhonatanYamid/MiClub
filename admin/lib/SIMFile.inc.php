<?php
class SIMFile
{
	static $SEPARADOR = "_";

	function write($name, $content)
	{
		if (!is_writable($name)) return false;

		if (file_exists($name))
			$filep = @fopen($name, 'a');
		else
			$filep = @fopen($name, 'w');

		if (@fwrite($filep, $content))
			return true;
		else
			return false;

		fclose($filep);
	}

	function delete($name)
	{
		return unlink($name);
	}

	function getFileData($archivo)
	{
		return pathinfo($archivo);
	}

	function getExtension($archivo)
	{
		$pathinfo = pathinfo($archivo);

		return $pathinfo["extension"];
	}

	function getName($archivo)
	{
		$pathinfo = pathinfo($archivo);

		return $pathinfo["basename"];
	}

	function getPathName($archivo)
	{
		$pathinfo = pathinfo($archivo);

		return $pathinfo["dirname"];
	}



	function getSize($file)
	{

		$size = filesize($file);

		$sizes = array(' Bytes', ' Kbs', ' Mbs', 'Gbs', 'Tbs', 'Pbs', 'Ebs');

		$ext = $sizes[0];

		for ($i = 1; ($i < count($sizes) && $size >= 1024); $i++) {
			$size = $size / 1024;
			$ext  = $sizes[$i];
		}

		clearstatcache();

		return round($size, 2) . $ext;
	}



	function isMIMEValid($mimetype)
	{
		return in_array($mimetype, SIMResources::$mimeValidos);
	}



	function isMIMEImageValid($mimetype)
	{
		return in_array($mimetype, SIMResources::$mimeImagenValidos);
	}



	function isMIMEDocValid($mimetype)
	{
		return in_array($mimetype, SIMResources::$mimeValidos);
	}



	function isMIMEVideoValid($mimetype)
	{
		return in_array($mimetype, SIMResources::$mimeVideoValidos);
	}

	function isMIMEAudioValid($mimetype)
	{
		return in_array($mimetype, SIMResources::$mimeAudioValidos);
	}



	function isMIMEGraphValid($mimetype)
	{
		return in_array($mimetype, SIMResources::$mimeGraficoValidos);
	}

	function makeSure($filename)
	{
		return preg_replace("/([^a-z0-9\.])/i", "_", $filename);
	}

	function makeInner($filename)
	{
		$startwith = (string)rand(1001, 999999);
		return $startwith . SIMFile::$SEPARADOR . $filename;
	}

	function upload($files_req, $destination, $validation = "ALL", $thumb = array(300, 300))
	{

		//datos del archivo a devolver
		$file_data = false;

		//flag de validacion
		$ismimevalid = false;

		//url temporal de destino para usar en el bucle
		$tmp_dest = "";

		if (isset($files_req["name"]))
			$files[0] =  $files_req;
		else
			$files = $files_req;

		foreach ($files as $nombre => $archivo) {

			switch ($validation) {
				case "IMAGE":
					$ismimevalid = self::isMIMEImageValid($archivo['type']);
					break;
				case "DOC":
					$ismimevalid = self::isMIMEDocValid($archivo['type']);
					break;
				case "VIDEO":
					$ismimevalid = self::isMIMEVideoValid($archivo['type']);
					break;
				case "AUDIO":
					$ismimevalid = self::isMIMEAudioValid($archivo['type']);
					break;
				case "GRAPH":
					$ismimevalid = self::isMIMEGraphValid($archivo['type']);
					break;
				default:
					$ismimevalid = self::isMIMEValid($archivo['type']);
					break;
			}


			if (!$archivo['error'] && $ismimevalid) {
				$safename = self::makeSure($archivo['name']);
				$innername = self::makeInner($safename);
				$nuevo_nombre = "image" . (string)rand(1, 999999);
				$innername = str_replace("image", $nuevo_nombre, $innername);
				if (move_uploaded_file($archivo['tmp_name'], $destination . "/" . $innername)) {
					if (!is_array($file_data)) $file_data = array();

					/*
					Probar luego de subir el sitio
					if( !empty( $thumb ) )
					{

						self::generarThumb( $destination . "/tn_" . $innername , $destination . "/" . $innername, $thumb[0],$thumb[1] );

					}//end if
					*/

					$file_data[] = array("name" => $safename, "innername" => $innername, "origname" => $archivo["name"], "size" => $archivo["size"], "type" => $archivo["type"]);
				}
			}
		}


		return $file_data;
	}

	function uploadName($files_req, $destination, $name, $validation = "ALL", $thumb = array(300, 300))
	{

		//datos del archivo a devolver
		$file_data = false;

		//flag de validacion
		$ismimevalid = false;

		//url temporal de destino para usar en el bucle
		$tmp_dest = "";

		if (isset($files_req["name"]))
			$files[0] =  $files_req;
		else
			$files = $files_req;


		foreach ($files as $nombre => $archivo) {


			switch ($validation) {
				case "IMAGE":
					$ismimevalid = self::isMIMEImageValid($archivo['type']);
					break;
				case "DOC":
					$ismimevalid = self::isMIMEDocValid($archivo['type']);
					break;
				case "VIDEO":
					$ismimevalid = self::isMIMEVideoValid($archivo['type']);
					break;
				case "AUDIO":
					$ismimevalid = self::isMIMEAudioValid($archivo['type']);
					break;
				case "GRAPH":
					$ismimevalid = self::isMIMEGraphValid($archivo['type']);
					break;
					/*default:
					$ismimevalid = self::isMIMEValid( $archivo['type'] );
				break;*/
				default:
					$ismimevalid = true;
					break;
			}


			if (!$archivo['error'] && $ismimevalid) {
				$extension = explode(".", $archivo['name'])[1];

				$safename = self::makeSure($archivo['name']);
				$innername = self::makeInner($safename);
				$nuevo_nombre = "image" . (string)rand(1, 999999);
				$innername = str_replace("image", $nuevo_nombre, $innername);

				if (move_uploaded_file($archivo['tmp_name'], $destination . "/" . $name . "." . $extension)) {
					if (!is_array($file_data)) $file_data = array();

					/*
					Probar luego de subir el sitio
					if( !empty( $thumb ) )
					{

						self::generarThumb( $destination . "/tn_" . $innername , $destination . "/" . $innername, $thumb[0],$thumb[1] );

					}//end if
					*/

					$file_data[] = array("name" => $safename, "innername" => $innername, "origname" => $archivo["name"], "size" => $archivo["size"], "type" => $archivo["type"]);
				}
			}
		}


		return $file_data;
	}


	function download($file, $filename)
	{
		// BEGIN extra headers to resolve IE caching bug (JRP 9 Feb 2003)
		// [http://bugs.php.net/bug.php?id=16173]
		header("Pragma: ");
		header("Cache-Control: ");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

		//	header("Cache-Control: no-store, no-cache, must-revalidate");
		//HTTP/1.1
		//	header("Cache-Control: post-check=0, pre-check=0", false);
		// END extra headers to resolve IE caching bug

		header("Content-Length: " . filesize($filename));
		header("Content-Type: $file->FileType");
		header("Content-Disposition: attachment; filename={$file->File}");

		readfile($filename);

		return true;
	}

	function makeDir($dir_name)
	{
		if (!mkdir($dir_name, 0755))
			return false;
		else
			chmod($dir_name, 0757);

		return true;
	}


	function listDir($dirname)
	{
		if ($dirname[strlen($dirname) - 1] != "/")
			$dirname .= "/";

		$result_array = array();

		$mode = fileperms($dirname);

		if (($mode & 0x4000) == 0x4000 && ($mode & 0x00004) == 0x00004) {
			chdir($dirname);
			$handle = @opendir($dirname);
		}

		if (isset($handle)) {
			while ($file = readdir($handle)) {
				if ($file == '.' || $file == '..')
					continue;

				if (is_file($dirname . $file))
					$result_array[] = $file;
			}

			closedir($handle);
		}
		return $result_array;
	}

	function generarThumb($pathNombre, $ImgOriginal, $anchoLimite, $altoLimite)
	{

		$original = imagecreatefromjpeg($ImgOriginal);

		//Defino variables
		$anchoFoto = "";
		$altoFoto = "";
		//Armo las dimesiones de la imagen
		$ancho = imagesx($original);
		$alto = imagesy($original);
		if ($ancho > $anchoLimite) {
			$anchoFoto = $anchoLimite;
			$altoFoto = ($alto * $anchoLimite) / $ancho;
		}
		if ($alto > $altoLimite) {
			$altoFoto = $altoLimite;
			$anchoFoto = ($ancho * $altoLimite) / $alto;
		}
		if ($anchoFoto > $anchoLimite) {
			$anchoFoto = $anchoLimite;
			$altoFoto = ($alto * $anchoLimite) / $ancho;
		}
		if ($altoFoto > $altoLimite) {
			$altoFoto = $altoLimite;
			$anchoFoto = ($ancho * $altoLimite) / $alto;
		}
		if ($anchoFoto != "" && $altoFoto != "") {
			$thumb = imagecreatetruecolor($anchoFoto, $altoFoto); // Lo haremos de un tamaÒo 150x150
			imagecopyresampled($thumb, $original, 0, 0, 0, 0, $anchoFoto, $altoFoto, $ancho, $alto);
		} else {
			$thumb = imagecreatetruecolor($ancho, $alto); // Lo haremos de un tamaÒo 150x150
			imagecopyresampled($thumb, $original, 0, 0, 0, 0, $ancho, $alto, $ancho, $alto);
		}

		//return imagejpeg($thumb,'thumb.jpg',90); // 90 es la calidad de compresiÛn
		return imagejpeg($thumb, $pathNombre, 100); // 90 es la calidad de compresiÛn

	} //end function

	function export2xls($DB_TBLName, $sql = "")
	{
		$dbo = &SIMDB::get();
		//define date for title: EDIT this to create the time-format you need
		$now_date = date('m-d-Y H:i');
		//define title for .doc or .xls file: EDIT this if you want

		$result = $dbo->query($sql);

		$title = "Datos Tabla $DB_TBLName Fecha $now_date";

		$file_type = "vnd.ms-excel";
		$file_ending = "xls";

		// Header("Content-Type: application/vnd.ms-excel");

		header("Content-Type: application/$file_type");
		header("Content-Disposition: attachment; filename=$DB_TBLName.$file_ending");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo ("$title\n");

		//define separator (defines columns in excel & tabs in word)
		$sep = "\t"; //tabbed character

		//start of printing column names as names of MySQL fields
		for ($i = 0; $i < $dbo->fields($result); $i++) {
			echo $dbo->fieldName($result, $i) . "\t";
		}

		print("\n");
		//end of printing column names

		//start while loop to get data
		while ($row = $dbo->fetchRow($result)) {
			//set_time_limit(60); // HaRa
			$schema_insert = "";

			for ($j = 0; $j < $dbo->fields($result); $j++) {
				if (!isset($row[$j]))
					$schema_insert .= "NULL" . $sep;
				elseif ($row[$j] != "")
					$schema_insert .= html_entity_decode($row[$j]) . $sep;
				else
					$schema_insert .= "" . $sep;
			}

			$schema_insert = str_replace($sep . "$", "", $schema_insert);
			//this corrects output in excel when table fields contain \n or \r
			//these two characters are now replaced with a space
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			print(trim($schema_insert));
			print "\n";
		}
	}


	function redimensionarIMAGEN($origen, $destino, $ancho_max, $alto_max, $fijar, $extension)
	{
		$info_imagen = getimagesize($origen);
		$ancho = $info_imagen[0];
		$alto = $info_imagen[1];
		if ($ancho >= $alto) {
			$nuevo_alto = round($alto * $ancho_max / $ancho, 0);
			$nuevo_ancho = $ancho_max;
		} else {
			$nuevo_ancho = round($ancho * $alto_max / $alto, 0);
			$nuevo_alto = $alto_max;
		}
		switch ($fijar) {
			case "ancho":
				$nuevo_alto = round($alto * $ancho_max / $ancho, 0);
				$nuevo_ancho = $ancho_max;
				break;
			case "alto":
				$nuevo_ancho = round($ancho * $alto_max / $alto, 0);
				$nuevo_alto = $alto_max;
				break;
			default:
				$nuevo_ancho = $nuevo_ancho;
				$nuevo_alto = $nuevo_alto;
				break;
		}

		switch ($extension):
			case "jpg":
			case "jpeg":
			case "JPG":
			case "JPEG":
				$imagen_nueva = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
				$imagen_vieja = imagecreatefromjpeg($origen);
				imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
				imagejpeg($imagen_nueva, $destino);
				break;
			case "png":
			case "PNG":

				$imagen_nueva = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
				$imagen_vieja = imagecreatefrompng($origen);
				imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
				imagepng($imagen_nueva, $destino);
				break;
			default:
				$imagen_nueva = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
				$imagen_vieja = imagecreatefromjpeg($origen);
				imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
				imagejpeg($imagen_nueva, $destino);
		endswitch;




		imagedestroy($imagen_nueva);
		imagedestroy($imagen_vieja);
	}
}
