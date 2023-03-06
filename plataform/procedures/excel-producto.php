<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

$sql_cat = "SELECT IDCategoriaProducto, Nombre FROM CategoriaProducto" . $_GET["Version"] . "  WHERE IDClub = '" . $_GET["IDClub"] . "'";
$r_cat = $dbo->query($sql_cat);



while ($row_cat = $dbo->fetchArray($r_cat)) {
	$array_nombre_cat[$row_cat["IDCategoriaProducto"]] = $row_cat["Nombre"];
}


$sql_reporte = "SELECT *
									From Producto" . $_GET["Version"] . "
									Where IDClub = '" . $_GET["IDClub"] . "' ";


$result_reporte = $dbo->query($sql_reporte);

$nombre = "Producto_" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);


if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>Nombre</th>";
	$html .= "<th>Codigo</th>";
	$html .= "<th>Descripcion</th>";
	$html .= "<th>Foto</th>";
	$html .= "<th>Precio</th>";
	$html .= "<th>Permite Comentarios</th>";
	$html .= "<th>Existencias</th>";
	$html .= "<th>Proveedor</th>";
	$html .= "<th>Orden</th>";
	$html .= "<th>Publicar</th>";
	$html .= "<th>Categoria</th>";

	foreach ($array_preguntas as $key_pregunta => $value_pregunta) {
		$html .= "<th>" . $value_pregunta . "</th>";
	}

	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {

		$html .= "<tr>";
		$html .= "<td>" . $Datos["Nombre"] . "</td>";
		$html .= "<td>" . $Datos["IDProductoExterno"] . "</td>";
		$html .= "<td>" . $Datos["Descripcion"] . "</td>";
		$html .= "<td>" . $Datos["Foto"] . "</td>";
		$html .= "<td>" . $Datos["Precio"]   . "</td>";
		$html .= "<td>" . $Datos["PermiteComentarios"]   . "</td>";
		$html .= "<td>" . $Datos["Existencias"]   . "</td>";
		$html .= "<td>" . $Datos["Proveedor"]   . "</td>";
		$html .= "<td>" . $Datos["Orden"]   . "</td>";
		$html .= "<td>" . $Datos["Publicar"]   . "</td>";

		$sql_cate = "SELECT IDCategoriaProducto
											From ProductoCategoria" . $_GET["Version"] . "
											Where IDProducto = '" . $Datos["IDProducto"] . "' ";
		$result_cate = $dbo->query($sql_cate);
		unset($array_cat);
		$cate = "";
		while ($row_cat = $dbo->fetchArray($result_cate)) {
			$array_cat[] = $array_nombre_cat[$row_cat["IDCategoriaProducto"]];
		}
		if (count($array_cat) > 0) {
			$cate = implode(",", $array_cat);
		}

		$html .= "<td>" . $cate   . "</td>";
		$html .= "</tr>";
	}
	$html .= "</table>";


	//construimos el excel
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $html;
	exit();
}
