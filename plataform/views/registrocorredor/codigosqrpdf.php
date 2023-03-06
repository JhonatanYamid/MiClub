
<?
include( "../../procedures/general_async.php" );
include("../../js/dompdf/autoload.inc.php");

// reference the Dompdf namespace
use Dompdf\Dompdf;

$dbo =& SIMDB::get();


$idClub = $_POST['IDClub'];
$idCategoria = $_POST['IDCategoriaTriatlon'];
$idCarrera = $_POST['IDCarrera'];
$hoy = date('Y-m-d');

$categoria = $dbo->getFields("CategoriaTriatlon", "Nombre", "IDCategoriaTriatlon = $idCategoria");
$carrera = $dbo->getFields("Carrera", "Nombre", "IDCarrera = $idCarrera");

$datos_club = $dbo->getFields("Club", array("Nombre","FotoLogoApp"), "IDClub = $IDClub");

$sql = "SELECT CONCAT(Nombre,' ',Apellido) as Nombre, NumeroDocumento, CodigoQr, NumCamiseta 
		FROM RegistroCorredor
		WHERE IDClub = $idClub AND IDCategoriaTriatlon = $idCategoria";

// var_dump($sql);
$result = $dbo->query($sql);
$count = $dbo->rows($result);

$i=1;
$j=1;
$table = "<table width='100%' cellspacing='3'>";

while($row = $dbo->fetchArray($result)) {
	if($i == 1)
		$table .= "<tr>";
		
		$nombreImagen = "data:image/png;base64," . base64_encode(file_get_contents(TRIATLON_ROOT."qr/".$row['CodigoQr']));

		$table .= "<td valign='top'>
					<p>
						".strtoupper($row['Nombre'])." <br>
						".$row['NumeroDocumento']." <br>
						<b>CAMISETA # ".$row['NumCamiseta']." </b>
					</p>
					<img src=$nombreImagen width='200' height='200'/>
				  </td>";

	if($i == 3){
		$table .= "</tr>";
		$i = 1;
	}else if($j == $count){
		
		for($a = 0 ; $a < ($i-3); $a++){
			$table .= "<td></td>";
		}
		$table .= "</tr>";
		
	}else{
		$i++;
	}

	$j++;
}

$table .= "</table>";

$html = "<html>
			<head>
				<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
				<title>".strtoupper($carrera)."/".strtoupper($categoria)."</title>
			</head>
			<body>
				$table
			</body>
		</html>";

$dompdf = new Dompdf();

$dompdf->loadHtml($html);
//tamaño del papel
$dompdf->setPaper('letter');
$dompdf->render();
//Nombre del archivo
$dompdf->stream($carrera."_".$categoria."_".$hoy.".pdf");