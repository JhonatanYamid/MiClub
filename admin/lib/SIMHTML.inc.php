<?php
class SIMHTML
{

	function texturl($text)
	{
		$text = self::limpiar_acentos($text);
		$tildes = array('А', 'И', 'М', 'С', 'З', '<88>', '<8f>', '<93>', '<98>', '<9d>', ' ', '<96>');
		$sin_tildes = array('a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', '-', 'n');

		//reemplazar tildes y espacios
		$text = str_replace($tildes, $sin_tildes, strtolower(trim($text)));

		//otros caracteres
		$text = preg_replace("/([^a-z0-9-_])/i", "", $text);

		return $text;
	}
	function limpiar_acentos($string)
	{
		//$string = trim($string);
		// $string = eregi_replace('&quot;', '', $string);

		$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
		);

		$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
		);

		$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
		);

		$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
		);

		$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
		);

		$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
		);

		//Esta parte se encarga de eliminar cualquier caracter extraño


		return $string;
	}


	function limpiar_caracteres_especiales($s)
	{
		$s = ereg_replace("[áàâãª]", "%", $s);
		$s = ereg_replace("[ÁÀÂÃ]", "%", $s);
		$s = ereg_replace("[éèê]", "%", $s);
		$s = ereg_replace("[ÉÈÊ]", "%", $s);
		$s = ereg_replace("[íìî]", "%", $s);
		$s = ereg_replace("[ÍÌÎ]", "%", $s);
		$s = ereg_replace("[óòôõº]", "%", $s);
		$s = ereg_replace("[ÓÒÔÕ]", "%", $s);
		$s = ereg_replace("[úùû]", "%", $s);
		$s = ereg_replace("[ÚÙÛ]", "%", $s);
		$s = str_replace(" ", "-", $s);
		$s = str_replace("ñ", "%", $s);
		$s = str_replace("Ñ", "%", $s);
		//para ampliar los caracteres a reemplazar agregar lineas de este tipo:
		//$s = str_replace("caracter-que-queremos-cambiar","caracter-por-el-cual-lo-vamos-a-cambiar",$s);
		return $s;
	}

	function convertir_hexad($hex)
	{

		$hex = str_replace("u00e1", 'á', $hex);
		$hex = str_replace("u00e9", 'é', $hex);
		$hex = str_replace("u00ed", 'í', $hex);
		$hex = str_replace("u00f3", 'ó', $hex);
		$hex = str_replace("u00fa", 'ú', $hex);
		$hex = str_replace("u00c1", 'Á', $hex);
		$hex = str_replace("u00c9", 'É', $hex);
		$hex = str_replace("u00cd", 'Í', $hex);
		$hex = str_replace("u00d3", 'Ó', $hex);
		$hex = str_replace("u00da", 'Ú', $hex);
		$hex = str_replace("u00f1", 'ñ', $hex);
		$hex = str_replace("u00d1", 'Ñ', $hex);

		return $hex;
	}
	function formRadioGroup($options, $value, $name, $attrs = "", $class = "")
	{
		$radiogroup = "";

		foreach ($options as $key => $val) {
			$radiogroup .= " <label class=\"radiogroup $class\"><input type=\"radio\" name=\"" . $name . "\" id=\"" . $name . "\" value=\"" . $val . "\" " . $attrs;

			if (!empty($value) || $value == "0")
				$radiogroup .= ($val == $value) ? " checked" : "";

			$radiogroup .= "> " . $key . "</label>";
		}

		return $radiogroup;
	}

	function formCheckGroup($options, $selection, $name, $sep = "", $attrs = "", $class = "")
	{
		$checkgroup = "";

		$checkgroup = "<table id='simple-table' class='table table-striped table-bordered table-hover'><tr><td>";
		$columnas = 0;
		foreach ($options as $key => $val) {
			$columnas++;
			$checkgroup .= "<label class=\"checkgroup $class\"><input type=\"checkbox\" name=\"" . $name . "\" id=\"" . $name . "\" value=\"" . $val . "\" " . $attrs;

			if (!empty($selection))
				$checkgroup .= (in_array($val, $selection)) ? " checked" : "";

			$checkgroup .= "> " . $key;
			$checkgroup .= "</label>" . $sep;

			$checkgroup .= "</td>";

			if ($columnas == 4) :
				$checkgroup .= "</tr><tr><td>";
				$columnas = 0;
			else :
				$checkgroup .= "<td>";
			endif;
		}
		$checkgroup .= "</tr></table>";

		return $checkgroup;
	}

	function formCheckGroup2($options, $selection, $name, $attrs = "", $class = "")
	{
		//return implode(",", $selection);
		$cantidad = count($options);

		$checkgroup = "<table id=\"simple-table\" class=\"table table-striped table-bordered table-hover\">";
		$columnas = 0;
		$j = 0;
		foreach ($options as $key => $val) {
			if ($columnas == 0) {
				$checkgroup .= "<tr>";
			}

			$check = '';
			if (!empty($selection))
				$check = (in_array($val, $selection)) ? " checked" : "";

			$checkgroup .= "<td><label class=\"checkgroup\"><input type=\"checkbox\" name=\"" . $name . "\" id=\"" . $name . "\" class =\"" . $class . "\" value=\"" . $val . "\" " . $attrs . " $check> " . $key . "</label></td>";

			if ($columnas == 1 || $j == $cantidad) {
				$checkgroup .= "</tr>";
				$columnas = 0;
			} else {
				$columnas++;
			}

			$j++;
		}
		$checkgroup .= "</table>";

		return $checkgroup;
	}

	function formCheckInput($options, $selection, $name, $attrs = "", $class = "", $classInput = "")
	{
		$cantidad = count($options);

		$checkgroup = "<table id=\"simple-table\" class=\"table table-striped table-bordered table-hover\">";
		$columnas = 0;
		$j = 0;

		foreach ($options as $key => $val) {
			if ($columnas == 0) {
				$checkgroup .= "<tr>";
			}

			$check = '';
			$value = '';
			if (!empty($selection)) {
				$check = (array_key_exists($val, $selection)) ? " checked" : "";
				$value = (array_key_exists($val, $selection)) ? $selection[$val] : "";
			}

			$checkgroup .= "<td>
								<label class=\"checkgroup\"><input type=\"checkbox\" name=\"" . $name . "\" id=\"" . $name . "\" class =\"" . $class . "\" value=\"" . $val . "\" " . $attrs . "$check> " . $key . " </label>
							</td>
							<td>
								<input type=\"text\" id=\"" . $name . $val . "\" name=\"" . $name . $val . "\" class =\"" . $classInput . "\" value=\"" . $value . "\">
							</td>";

			if ($columnas == 1 || $j == $cantidad) {
				$checkgroup .= "</tr>";
				$columnas = 0;
			} else {
				$columnas++;
			}

			$j++;
		}
		$checkgroup .= "</table>";

		return $checkgroup;
	}

	function formPopupArray($options, $selection, $name, $initialtext = "", $class = "", $attrs = "")
	{
		$checkgroup = "<select name=\"" . $name . "\" id=\"" . $name . "\" class=\"" . $class . "\" " . $attrs . "><option value=\"\">" . $initialtext . "</option>";

		foreach ($options as $key => $val) {
			$checkgroup .= "<option value=\"" . $key . "\"";

			if (!empty($selection) && $selection == $key)
				$checkgroup .= " selected";

			$checkgroup .= "> " . $val . "</option>";
		}

		$checkgroup .= "</select>";

		return $checkgroup;
	}



	function formPopup($table, $field, $order, $name, $value = "", $text = "", $style = "", $attrs = "", $condicion = "")
	{
		$popup .= "<select name=\"" . $name . "\" id=\"" . $name . "\" class=\"" . $style . "\" " . $attrs . ">";
		$popup .= "<option value=\"\">" . $text . "</option>";

		$dbo = &SIMDB::get();
		$qry = &$dbo->all($table, " 1 " . $condicion . " ORDER BY " . $order);

		while ($r = $dbo->object($qry)) {
			$popup .= "<option value=" . $r->$name;
			$popup .= ($r->$name == $value) ? " selected" : "";
			//$popup .=  " >" . htmlentities( $r->$field ) . "</option>";
			$popup .=  " >" . $r->$field  . "</option>";
		}

		$popup .= "</select>";

		return $popup;
	}

	function formPopupV2($table, $field, $order, $name, $idField, $value = "", $text = "", $style = "", $attrs = "", $condicion = "")
	{
		$popup .= "<select name=\"" . $name . "\" id=\"" . $idField . "\" class=\"" . $style . "\" " . $attrs . ">";
		$popup .= "<option value=\"\">" . $text . "</option>";

		$dbo = &SIMDB::get();
		$qry = &$dbo->all($table, " 1 " . $condicion . " ORDER BY " . $order);

		while ($r = $dbo->object($qry)) {
			$popup .= "<option value=" . $r->$name;
			$popup .= ($r->$name == $value) ? " selected" : "";
			//$popup .=  " >" . htmlentities( $r->$field ) . "</option>";
			$popup .=  " >" . $r->$field  . "</option>";
		}

		$popup .= "</select>";

		return $popup;
	}

	function formPopupSeccion($table, $field, $order, $namepadre, $name, $value = "", $text = "", $style = "", $attrs = "")
	{
		$popup .= "<select name=\"" . $name . "\" id=\"" . $name . "\" class=\"" . $style . "\" " . $attrs . ">";
		$popup .= "<option value=\"\">" . $text . "</option>";

		$dbo = &SIMDB::get();
		$qry = &$dbo->all($table, " 1 ORDER BY " . $order);

		while ($r = $dbo->object($qry)) {
			$popup .= "<option value=" . $r->$namepadre;
			$popup .= ($r->$namepadre == $value) ? " selected" : "";
			$popup .=  " >" . htmlentities($r->$field) . "</option>";
		}

		$popup .= "</select>";

		return $popup;
	}


	function message($message, $class)
	{
		return "<div class=\"alert alert-block  mensaje " . $class . "\">" . $message . "</div>";
	}

	function tableCheckList($descfield, $Key, $key_value, $table_option, $key_option, $table_reference, $check_name)
	{
		$dbo = &SIMDB::get();
		$option_checked = array();
		$array_option = array();

		$qry_option_checked = $dbo->query("SELECT " . $key_option . " FROM " . $table_reference . " WHERE " . $Key . " = '" . $key_value . "' ");

		while ($r_option = $dbo->assoc($qry_option_checked))
			$option_checked[] = $r_option[$key_option];

		$qry = $dbo->query("SELECT * FROM " . $table_option);

		while ($item_option = $dbo->object($qry))
			$array_option[$item_option->$descfield] = $item_option->$key_option;

		return self::formCheckGroup($array_option, $option_checked, $check_name);
	}


	function formPopUpHora($fecha, $name, $style = "", $text = "[Seleccione]", $attrs = "")
	{
		$horasvalue = array(
			"00:00:00", "00:30:00", "01:00:00", "01:30:00", "02:00:00", "02:30:00", "03:00:00", "03:30:00", "04:00:00",
			"04:30:00", "05:00:00", "05:30:00", "06:00:00", "06:30:00", "07:00:00", "07:30:00", "08:00:00", "08:30:00", "09:00:00",
			"09:30:00", "10:00:00", "10:30:00", "11:00:00", "11:30:00", "12:00:00", "12:30:00", "13:00:00", "13:30:00", "14:00:00",
			"14:30:00", "15:00:00", "15:30:00", "16:00:00", "16:30:00", "17:00:00", "17:30:00", "18:00:00", "18:30:00", "19:00:00",
			"19:30:00", "20:00:00", "20:30:00", "21:00:00", "21:30:00", "22:00:00", "22:30:00", "23:00:00", "23:30:00"
		);

		$horamostrar = array(
			"12:00 am", "12:30 am", "01:00 am", "01:30 am", "02:00 am", "02:30 am", "03:00 am", "03:30 am", "04:00 am",
			"04:30 am", "05:00 am", "05:30 am", "06:00 am", "06:30 am", "07:00 am", "07:30 am", "08:00 am", "08:30 am", "09:00 am",
			"09:30 am", "10:00 am", "10:30 am", "11:00 am", "11:30 am", "12:00 m", "12:30 pm", "01:00 pm", "01:30 pm", "02:00 pm",
			"02:30 pm", "03:00 pm", "03:30 pm", "04:00 pm", "04:30 pm", "05:00 pm", "05:30 pm", "06:00 pm", "06:30 pm", "07:00 pm",
			"07:30 pm", "08:00 pm", "08:30 pm", "09:00 pm", "09:30 pm", "10:00 pm", "10:30 pm", "11:00 pm", "11:30 pm"
		);

		$popup .= "<select name=\"" . $name . "\" id=\"" . $name . "\" class=\"" . $style . "\" " . $attrs . ">";
		$popup .= "<option value=\"\">" . $text . "</option>";

		foreach ($horasvalue as $key => $horavalue) {
			$popup .= "<option value=" . $horavalue;

			$popup .= (($horavalue == $fecha) ? " selected" : "");

			$popup .=  " >" . $horamostrar[$key] . "</option>";
		}

		$popup .= "</select>";

		return $popup;
	}




	//inicio alert//
	function jsAlert($msg)
	{
		//echo "<script type=\"text/javascript\">alert(\"" .   SIMUtil::get_traduccion('', '', $msg, LANGSESSION) . "\");</script> ";
		echo "<script type=\"text/javascript\">alert(\"" .  $msg . "\");</script> ";
		return true;
	}
	//fin alert//

	//inicio redireccionamiento//
	function jsRedirect($msg)
	{
		echo "<script type=\"text/javascript\">location.href=\"" .  $msg . "\";</script> ";
		return true;
	}
	//fin redireccionamiento//

	//inicio redireccionamiento TOP//
	function jsTopRedirect($msg)
	{
		echo "<script type=\"text/javascript\">top.location.href=\"" .  $msg . "\";</script> ";
		return true;
	}
	//fin redireccionamiento TOP//

	//inicio comando script//
	function jsCommand($msg)
	{
		echo "<script >" . $msg . "</script> ";
		return true;
	}


	function ajustar_logo($img, $width, $height)
	{

		$array_tam = getimagesize($img);
		if ($array_tam[1] > $height) {
			$array_tam[0] = ($height * $array_tam[0]) / $array_tam[1];
			$array_tam[1] = $height;
		} //end if

		if ($array_tam[0] > $width) {
			$array_tam[1] = ($width * $array_tam[1]) / $array_tam[0];
			$array_tam[0] = $width;
		} //end if

		return $array_tam;
	} //end funtion

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
			$thumb = imagecreatetruecolor($anchoFoto, $altoFoto); // Lo haremos de un tama�o 150x150
			imagecopyresampled($thumb, $original, 0, 0, 0, 0, $anchoFoto, $altoFoto, $ancho, $alto);
		} else {
			$thumb = imagecreatetruecolor($ancho, $alto); // Lo haremos de un tama�o 150x150
			imagecopyresampled($thumb, $original, 0, 0, 0, 0, $ancho, $alto, $ancho, $alto);
		}

		return imagejpeg($thumb, $pathNombre, 100);
	}

	function mostrar_arbol($menu_seccion, $idpadre, $class = "", $tipo, $sin_ciudad)
	{
		if (!empty($menu_seccion[$idpadre])) {
?>
			<ul class="<?= $class ?> link-<?= $idpadre ?>">
				<?
				foreach ($menu_seccion[$idpadre] as $idseccion => $datos_seccion) {
					$link = "interna.php?ids=" . $idseccion;
					if (!empty($datos_seccion["URL"]))
						$link = $datos_seccion["URL"];
					if (!empty($menu_seccion[$idseccion]))
						$link = "javascript:void(0);";


					$onclic = 'rel="link-"' . $idseccion;

					/*if($sin_ciudad == 'S')
					{
						if($idseccion == 14)
						{
							$onclic = " rel='ciudades_div'";
							$link = "ciudades_layer.php";
						}
					}*/
				?>
					<li>
						<a href="<?= $link ?>" <?= $onclic ?>><span><?= $datos_seccion["Nombre"] ?></span></a>
						<?

						//para las secciones especiales
						switch ($idseccion) {
							case "3":
								$array_categorias = SIMReg::get("categorias");
								echo "<ul class='subnav2 link-" . $idseccion . "'>";
								foreach ($array_categorias[0] as $idpadre => $datos_cat) {
									//echo "<li><a href=\"javascript:void(0);\"><span>" . $datos_cat["Nombre"] . "</span></a>";

									#if($sin_ciudad != 'S')

									if ($datos_cat["IDCategoria"] == 39) {
										echo "<li><a href=\"catalogo.php?id=40\"><span>" . $datos_cat["Nombre"] . "</span></a>";
									} else if ($datos_cat["IDCategoria"] == 41) {
										echo "<li><a href=\"catalogo.php?id=saldos\"\"><span>" . $datos_cat["Nombre"] . "</span></a>";
									} else {
										echo "<li><a href=\"catalogo_linea.php?id=" . $datos_cat["IDCategoria"] . "\"><span>" . $datos_cat["Nombre"] . "</span></a>";
									}


									/*else
										echo "<li><a href='ciudades_layer.php'   rel='ciudades_div'><span>" . $datos_cat["Nombre"] . "</span></a>";
									*/

									echo "<ul>";

									foreach ($array_categorias[$datos_cat["IDCategoria"]] as $idcat => $datos_categoria) {
										#if($sin_ciudad != 'S')
										echo "<li><a href=\"catalogo.php?id=" . $datos_categoria["IDCategoria"] . "\">" . $datos_categoria["Nombre"] . "</li></a>";
										/*else
											echo "<li><a href='ciudades_layer.php' rel='ciudades_div'>" . $datos_categoria["Nombre"] . "</li></a>";
									*/
									}
									echo "</ul>";
									echo "</li>";
								} //end for


								echo "</ul>";
								break;
							default:
								SIMHTML::mostrar_arbol($menu_seccion, $idseccion, "subnav2");
								break;
						} //end sw


						?>
					</li>


				<?
				} //end for
				?>
				<?
				if ($menu_seccion[$idpadre] == 0) {
				?>

					<!--                <li><a rel="catalogo" href="catalogo_fin_ano.php" style="display: block;"><span>Cat&aacute;logo de Madres</span></a></li>-->
				<?
				}
				?>
				<?
				if ($tipo == "Proveedor") {
				?>
					<li><a rel="link-15" href="catalogo_proveedor.php" style="display: block;"><span>Pedidos Proveedores</span></a></li>
				<?
				}
				?>
			</ul>
<?
		} //end if
		return false;
	} //end function


	function diasperiodotarifa($fechainicio, $fechafin)
	{
		$dbo = &SIMDB::get();

		//  $diasperiodo = $dbo->query("SELECT DATEDIFF('".$fechafin."','".$fechainicio."') + 1 AS DIAS;");
		$diasperiodo = $dbo->query("SELECT DATEDIFF('" . $fechafin . "','" . $fechainicio . "') AS DIAS;");

		$arraydiasperiodo = $dbo->fetchArray($diasperiodo);

		$diasperiodo = $arraydiasperiodo["DIAS"];

		return $diasperiodo;
	}

	function calculaterifareserva($IDClub, $fechainicio, $fechafin, $IDTipoHabitacion, $TipoTarifa, $NumeroAsistentes, $Adicional, $TipoPromocion, $ValorDescuento, $ValorDescuentoInvitado, $NumeroInvSocio, $NumeroInvExterno, $ValidarSanAndresSofacama)
	{
		$dbo = &SIMDB::get();

		$year_inicio = substr($fechainicio, 0, 4);

		$ArrayDiasEntreFechas = SIMHTML::arrayperiodofechas($fechainicio, $fechafin);

		//echo $ArrayDiasEntreFechas[0];
		//echo $ArrayDiasEntreFechas[1];
		//recorro dia a dia para verificar si no hacen tranpa para la temporada alta
		foreach ($ArrayDiasEntreFechas as $i) {
			$ArrayTemporadaAlta = array();
			$TemporadaAlta = $dbo->query("SELECT * FROM TemporadaAlta WHERE IDClub = '" . $IDClub . "' and  FechaInicio <= '$i' AND FechaFin >= '$i'");
			$ArrayTemporadaAlta = $dbo->fetchArray($TemporadaAlta);

			if (count($ArrayTemporadaAlta) > 1) {
				//tarifa para la reserva
				//$TarifaReserva = $dbo->query( "SELECT * FROM Tarifa WHERE IDClub = '".$IDClub."' and IDTipoHabitacion = '".$IDTipoHabitacion."' AND TipoTarifa = '".$TipoTarifa."' AND Temporada = 'Alta' AND NumeroPersonas = '".$NumeroAsistentes."' AND Adicional = 'N' " );
				$TarifaReserva = $dbo->query("SELECT * FROM Tarifa WHERE IDClub = '" . $IDClub . "' and IDTipoHabitacion = '" . $IDTipoHabitacion . "' AND TipoTarifa = '" . $TipoTarifa . "' AND Temporada = 'Alta' and Year <= '" . $year_inicio . "' ORDER BY Year DESC Limit 1 ");
				$ArrayTarifaReserva = $dbo->fetchArray($TarifaReserva);

				//verificamos si tiene adicional y se le suma al valor al total de la tarifa
				if ($Adicional == "S") {
					//tarifa para la reserva
					//$TarifaReservaAdiconal = $dbo->query( "SELECT * FROM Tarifa WHERE IDClub = '".$IDClub."' and IDTipoHabitacion = '".$IDTipoHabitacion."' AND TipoTarifa = '".$TipoTarifa."' AND Temporada = 'Alta' AND NumeroPersonas = '0' AND Adicional = '".$Adicional."' " );
					$TarifaReservaAdiconal = $dbo->query("SELECT * FROM Tarifa WHERE IDClub = '" . $IDClub . "' and IDTipoHabitacion = '" . $IDTipoHabitacion . "' AND TipoTarifa = '" . $TipoTarifa . "' AND Temporada = 'Alta' and Year <= '" . $year_inicio . "' ORDER BY Year DESC Limit 1 ");
					$ArrayTarifaReservaAdicional = $dbo->fetchArray($TarifaReservaAdiconal);
					$ValorAcompananteAdicional = $ArrayTarifaReservaAdicional["Valor"];
				}
			} else {
				//tarifa para la reserva

				//$TarifaReserva = $dbo->query( "SELECT * FROM Tarifa WHERE IDClub = '".$IDClub."' and IDTipoHabitacion = '".$IDTipoHabitacion."' AND TipoTarifa = '".$TipoTarifa."' AND Temporada = 'Baja' AND NumeroPersonas = '".$NumeroAsistentes."' AND Adicional = 'N' " );
				$TarifaReserva = $dbo->query("SELECT * FROM Tarifa WHERE IDClub = '" . $IDClub . "' and IDTipoHabitacion = '" . $IDTipoHabitacion . "' AND TipoTarifa = '" . $TipoTarifa . "' AND Temporada = 'Baja' and Year <= '" . $year_inicio . "' ORDER BY Year DESC Limit 1 ");
				$ArrayTarifaReserva = $dbo->fetchArray($TarifaReserva);




				//verificamos si tiene adicional y se le suma al valor al total de la tarifa
				if ($Adicional == "S") {
					//tarifa para la reserva
					//$TarifaReservaAdiconal = $dbo->query( "SELECT * FROM Tarifa WHERE IDClub = '".$IDClub."' and IDTipoHabitacion = '".$IDTipoHabitacion."' AND TipoTarifa = '".$TipoTarifa."' AND Temporada = 'Baja' AND NumeroPersonas = '0' AND Adicional = '".$Adicional."' " );
					$TarifaReservaAdiconal = $dbo->query("SELECT * FROM Tarifa WHERE IDClub = '" . $IDClub . "' and IDTipoHabitacion = '" . $IDTipoHabitacion . "' AND TipoTarifa = '" . $TipoTarifa . "' AND Temporada = 'Baja' and Year <= '" . $year_inicio . "' ORDER BY Year DESC Limit 1 ");
					$ArrayTarifaReservaAdicional = $dbo->fetchArray($TarifaReservaAdiconal);
					$ValorAcompananteAdicional = $ArrayTarifaReservaAdicional["Valor"];
				}
			}


			//proceso para las tarifas con promociones de viernes
			if ($TipoPromocion == "ViernesGratis") {

				$dialetra = $dbo->query("SELECT DAYNAME('$i') AS DIALETRA");
				$arraydialetra = $dbo->fetchArray($dialetra);
				$dialetra = $arraydialetra["DIALETRA"];

				if ($dialetra != "Friday")
					$ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReserva["Valor"];

				if ($dialetra == "Friday") {
					if ($TipoTarifa == "Invitado") {
						$ValorAcompananteAdicional = ($ValorAcompananteAdicional * $ValorDescuentoInvitado) / 100;

						$ArrayTarifaReservaPromo = ($ArrayTarifaReserva["Valor"] * $ValorDescuentoInvitado) / 100;

						$ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReservaPromo;
					}
				}
			} else {
				//proceso para las tarifas con promociones de martes y miercoles
				if ($TipoPromocion == "NocheMartesMiercoles") {
					$dialetra = $dbo->query("SELECT DAYNAME('$i') AS DIALETRA");
					$arraydialetra = $dbo->fetchArray($dialetra);
					$dialetra = $arraydialetra["DIALETRA"];

					if (($dialetra == "Tuesday") || ($dialetra == "Wednesday")) {
						if ($TipoTarifa == "Invitado") {
							$ValorAcompananteAdicional = ($ValorAcompananteAdicional * $ValorDescuentoInvitado) / 100;
							$ArrayTarifaReservaPromo = ($ArrayTarifaReserva["Valor"] * $ValorDescuentoInvitado) / 100;
							$ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReservaPromo;
						}

						if ($TipoTarifa == "Socio") {
							$ValorAcompananteAdicional = ($ValorAcompananteAdicional * $ValorDescuento) / 100;
							$ArrayTarifaReservaPromo = ($ArrayTarifaReserva["Valor"] * $ValorDescuento) / 100;
							$ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReservaPromo;
						}
					} else
						$ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReserva["Valor"];
				} else {

					if ($TipoTarifa == "Socio") {
						if ($IDClub == 70 && $ValidarSanAndresSofacama) :
							$ValorAdicional = $ArrayTarifaReserva["ValorSocioAdicionalSofacama"];
						else :
							$ValorAdicional = $ArrayTarifaReserva["ValorSocioAdicional"];
						endif;
					} else {

						if ($IDClub == 70 && $ValidarSanAndresSofacama) :
							$ValorAdicional = $ArrayTarifaReserva["ValorExternoAdicionalSofacama"];
						else :
							$ValorAdicional = $ArrayTarifaReserva["ValorExternoAdicional"];
						endif;
					}


					//$ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReserva["Valor"];

					//Solo cobro adicional despues del segundo invitado
					$CargoxAdicionalesSocio = 0;
					$CargoxAdicionalesExterno = 0;
					if ((int)$NumeroInvSocio > 1) {
						$CargoxAdicionalesSocio = $ValorAdicional * (int)((int)$NumeroInvSocio - 2);
					}


					if ((int)$NumeroInvExterno >= 2) {
						//Para san andres adicional es apartir del tercer invitado
						$CargoxAdicionalesExterno = $ValorAdicional * ((int)$NumeroInvExterno - 1);
					}

					//$ValorReserva += $ValorAcompananteAdicional + $ArrayTarifaReserva["Valor"];
					$ValorReserva +=  $ArrayTarifaReserva["Valor"] + $CargoxAdicionalesSocio + $CargoxAdicionalesExterno;
				}
			}
			/*echo $ValorReserva;
								echo "<br/>";*/
			//echo  $ValorReserva;

			//para anapoima se cobra un valor fijo
			if ($IDClub == 46) {
				$ValorReserva = $ArrayTarifaReserva["Valor"];
			}
		}


		return $ValorReserva;
	}

	function plantillamailreserva($CabezaReserva, $NombreCabezaReserva, $Estado, $TipoHabitacion, $Habitacion, $FechaInicio, $FechaFin, $Adicional, $ArrayAcompanantesReserva)
	{
		$html = '
<body>
<table width="602" border="0" align="center" cellpadding="0" cellspacing="0" class="bgverde">
<tr>
<td height="89" colspan="3"><span><img src="' . MAILRESERVA . 'bg_r1_c1.jpg" alt="01" width="800" height="123" border="0" /></span></td>
</tr>
<tr>
<td width="26" align="left" valign="top"><img src="' . MAILRESERVA . 'bg_r2_c1.jpg" width="26" height="100%" align="left"></td>
<td width="563" height="34">
	<table width="544" border="0" align="center" cellpadding="0" cellspacing="0" class="fontStyle">
			<tr>
				<td height="44" colspan="2" align="center" valign="bottom" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:18px; font-weight:bold">RESERVACION</td>
			</tr>
			<tr>
				<td height="53" colspan="2" align="left" valign="middle" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><div align="center"><span style="color: #333333">Sus datos son los siguientes:</span></div></td>
			</tr>
			<tr>
				<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Cabeza Reserva:</span><span class="Estilo10"></span></td>
				<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $CabezaReserva . '</td>
			</tr>
			<tr>
				<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Nombre De Cabeza Reserva:</span><span class="Estilo10"></span></td>
				<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $NombreCabezaReserva . '</td>
			</tr>
			<tr>
				<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Estado:</span><span class="Estilo10"></span></td>
				<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $Estado . '</td>
			</tr>
			<tr>
				<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Tipo Habitacion:</span><span class="Estilo10"></span></td>
				<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $TipoHabitacion . '</td>
			</tr>
			<tr>
				<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Habitacion:</span><span class="Estilo10"></span></td>
				<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $Habitacion . '</td>
			</tr>
			<tr>
				<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Fecha Inicio:</span><span class="Estilo10"></span></td>
				<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $FechaInicio . '</td>
			</tr>
			<tr>
				<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Fecha Fin:</span><span class="Estilo10"></span></td>
				<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $FechaFin . '</td>
			</tr>
			<tr>
				<td height="25" align="left" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Adicional:</span><span class="Estilo10"></span></td>
				<td width="312" align="left" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $Adicional . '</td>
			</tr>
	</table>
<table width="544" border="0" align="center" cellpadding="0" cellspacing="0" class="fontStyle">
	<tr>
		<td height="44" align="left" valign="middle" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:18px; font-weight:bold"><div align="center">ACOMPA&Ntilde;ANTES</div></td>
	</tr>';
		foreach ($ArrayAcompanantesReserva as $claveAcompanante => $ValorAcompanante) {
			$html .= '
	<tr>
		<td width="312" height="25" align="left" valign="top" style="color:#333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal">' . $ValorAcompanante["Nombre"] . ' ' . $ValorAcompanante["Apellido"] . '</td>
	</tr>';
		}



		$html .= '<tr>
		<td height="55" colspan="2" align="center" valign="top" class="Estilo4"><div align="left"></div></td>
	</tr>
	<tr>
		<td height="55" align="center" valign="top" style="color:#60A03C; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold"><span>Le agradecemos por hacer su reserva en <a style="color: #333333; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold" href="' . URLROOT . 'admin">' . URLROOT . 'admin</a><font face="Verdana, Arial, Helvetica, sans-serif">.</font></span></td>
	</tr>
</table></td>
<td width="26" align="left" valign="top"><img src="' . MAILRESERVA . 'bg_r2_c3.jpg" width="26" height="100%" align="right"></td>
</tr>

<tr>
<td height="35" colspan="5" align="center" valign="middle" bgcolor="#999999"><div align="center" style="color: #000000; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:normal">      Oficinas en Bogot�: Cra. 15 No. 76 - 60. Of. 501. PBX: 6167088. Fax: 6352693. payande@cable.net.co</div></td>
</tr>
</table>

</body>';

		return $html;
	}

	function arrayperiodofechas($FechaInicio, $FechaFin)
	{
		$dbo = &SIMDB::get();
		//$diasperiodo = $dbo->query("SELECT DATEDIFF('".$FechaFin."','".$FechaInicio."') + 1 AS DIAS;");
		$diasperiodo = $dbo->query("SELECT DATEDIFF('" . $FechaFin . "','" . $FechaInicio . "')  AS DIAS;");
		//$diasperiodo = $dbo->query("SELECT DATEDIFF('".$FechaFin."','".$FechaInicio."') AS DIAS;");
		//echo "SELECT DATEDIFF('".$FechaFin."','".$FechaInicio."') + 1 AS DIAS;";
		$arraydiasperiodo = $dbo->fetchArray($diasperiodo);
		$diasperiodo = $arraydiasperiodo["DIAS"];

		$ArrayDiasEntreFechas = array();
		$ArrayDiasEntreFechas[$FechaInicio] = $FechaInicio;

		for ($i = 1; $i < $diasperiodo; $i++) {
			$DiaActual = $dbo->query("SELECT DATE_ADD('" . $FechaInicio . "', INTERVAL $i DAY) AS DIAACTUAL");
			$arrayDiaActual = $dbo->fetchArray($DiaActual);
			$ArrayDiasEntreFechas[$arrayDiaActual["DIAACTUAL"]] = $arrayDiaActual["DIAACTUAL"];
		}

		return $ArrayDiasEntreFechas;
	}
}
?>