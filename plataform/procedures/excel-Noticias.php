<?php
require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

		$version = "1";
		$nombre = "Noticias" . date( "Y_m_d H:i:s" );

		$sql = "SELECT * FROM Noticia WHERE IDClub = " .$_GET["IDClub"];

		$qry = $dbo->query( $sql );
		$Num = $dbo->rows( $qry );
		
		$html  = "";
		$html .= "<table width='100%' border='1'>";
			$html .= "<tr>";
				$html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
			$html .= "</tr>";
			$html .= "<tr>";
				$html .= "<th>SECCIÓN</th>";
				$html .= "<th>TITULAR</th>";
				/* $html .= "<th>INTRODUCCIÓN</th>"; */					
				/* $html .= "<th>CUERPO</th>";	 */				
				$html .= "<th>FECHA INICIO</th>";					
				$html .= "<th>FECHA FIN</th>";									
				$html .= "<th>NUMERO DE COMENTARIOS</th>";									
				$html .= "<th>COMENTARIOS</th>";									
				$html .= "<th>SOCIOS QUE COMENTARON</th>";									
				$html .= "<th>NUMERO DE LIKES</th>";							
				$html .= "<th>SOCIOS QUE DIERON LIKE</th>";							
			$html .= "</tr>";

			while ($row = $dbo->fetchArray($qry))
			{
				$html .= "<tr>";

					$html .= "<td>".$dbo->getFields("Seccion", "Nombre", "IDSeccion = ".$row["IDSeccion"])."</td>";
					$html .= "<td>".$row["Titular"]."</td>";
					/* $html .= "<td>".$row["Introduccion"]."</td>"; */					
					/* $html .= "<td>".$row["Cuerpo"]."</td>";	 */				
					$html .= "<td>".$row["FechaIncio"]."</td>";					
					$html .= "<td>".$row["FechaFin"]."</td>";

					$sqlComentario = "SELECT Comentario FROM NoticiaComentario WHERE IDNoticia = ".$row["IDNoticia"]." AND `Version` = ".$version;
					$qryComentario = $dbo->query($sqlComentario);
					$numeroComentarios = $dbo->rows($qryComentario);

					$html .="<td>".$numeroComentarios."</td>";
					$com = 1;
					$html .="<td>";
					while($comentario = $dbo->fetchArray($qryComentario))
					{
						$html .=$com." - ".$comentario["Comentario"]."<br>";
						$com++;
					}	
					$html .="</td>";

					$sqlSocio = "SELECT IDSocio FROM NoticiaComentario WHERE IDNoticia = ".$row["IDNoticia"]." AND `Version` = ".$version;
					$qrySocio = $dbo->query($sqlSocio);

					$html .="<td>";
					while($socio1 = $dbo->fetchArray($qrySocio))
					{
						$html .=$dbo->getFields("Socio", "Nombre", "IDSocio = ".$socio1["IDSocio"])." ".$dbo->getFields("Socio", "Apellido", "IDSocio = ".$socio1["IDSocio"])."<br>";
						$com++;
					}
					$html .="</td>";

					$sqlLikes = "SELECT IDSocio FROM NoticiaLike WHERE IDNoticia = ".$row["IDNoticia"]." AND `Version` = ".$version;
					$qryLikes = $dbo->query($sqlLikes);
					$numeroLikes = $dbo->rows($qryLikes);

					$html .="<td>".$numeroLikes."</td>";
					$html .="<td>";
					while($socio= $dbo->fetchArray($qryLikes))
					{
						$html .=$dbo->getFields("Socio", "Nombre", "IDSocio = ".$socio["IDSocio"])." ".$dbo->getFields("Socio", "Apellido", "IDSocio = ".$socio["IDSocio"])."<br>";
						$com++;
					}
					$html .="</td>";					

				$html .= "</tr>";
			}
		
		$html .= "</table>";	
		

		//construimos el excel
		header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
		header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		?>
		<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<?php
	echo $html;
	exit();
	?>
</body>
</html>

		exit();

?>
