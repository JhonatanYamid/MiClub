 					 <?
include( "../../procedures/general_async.php" );
include("../../js/dompdf/autoload.inc.php");

// reference the Dompdf namespace
use Dompdf\Dompdf;

$dbo =& SIMDB::get();
 
$idClub = $_GET['IDClub'];
$idCorrespondencia = $_GET['IDCorrespondencia']; 
$hoy = date('Y-m-d');   

$sql = "SELECT * FROM Correspondencia WHERE IDClub= $idClub AND IDCorrespondencia = $idCorrespondencia";

// var_dump($sql);
$result = $dbo->query($sql);
$count = $dbo->rows($result);

$table = "<table width='105cm' >";

while($row = $dbo->fetchArray($result)) {
	 $row['FechaEntrega']= substr($row['FechaEntrega'], 0, -3); 
	$usuario = $dbo->getFields("Usuario", "Nombre", "IDUsuario=$row[IDUsuarioEntrega] "); 
	$usuario = substr("$usuario",0,13);
	$pdf= $row['CodigoBarraCorrespondencia'];
		$table .= "<tr>";
		
		$nombreImagen = "data:image/png;base64," . base64_encode(file_get_contents(CORRESPONDENCIA_ROOT."".$row['CodigoBarraCorrespondencia']));

		$table .= "  
		<style>
	@page {
		margin-left: 0.7cm;
		margin-right: 0.5cm;;
	}
</style>
 
		<td width='3.2cm' >
		<style>
		#foto{
    
display: inline-block;
margin: auto;
}
#foto2{
  
display: inline-block;
margin: auto;
}
#foto3{
   
display: inline-block;
margin: auto;
}
		</style>
					 
					 
					<h6>	EDIFICIO W.T.C.B.  <br>
						<b> ".$row['FechaEntrega']."  <br>
						 ID # ".$row['IDCorrespondencia']." <br> </b>  
						 
					 <img  id='foto3' src=$nombreImagen width='95' height='37' />  <h/6>
				 
						 
					 
 
				  </td>";
				  $table .= "
			                            
				  
		<td width='3.2cm' >
		<style>
		#foto{
    
display: inline-block;
margin: auto;
}
#foto2{
  
display: inline-block;
margin: auto;
}
#foto3{
   
display: inline-block;
margin: auto;
}
		</style>
					 
					<h6>	EDIFICIO W.T.C.B.  <br>
						<b> ".$row['FechaEntrega']."  <br>
						 ID # ".$row['IDCorrespondencia']." <br> </b>  
						 
					 <img  id='foto3' src=$nombreImagen width='95' height='37' />  </h6>
				 
					 
				  </td>";
				  
				  $table .= " 
				                      
				                     
		<td width='3.2cm' >
		<style>
		#foto{
    
display: inline-block;
margin: auto;
}
#foto2{
  
display: inline-block;
margin: auto;
}
#foto3{
   
display: inline-block;
margin: auto;
}
		</style>
					 
					<h6>	EDIFICIO W.T.C.B.  <br>
						<b> ".$row['FechaEntrega']."  <br>
						 ID # ".$row['IDCorrespondencia']." <br> </b>  
						 
					 <img  id='foto3' src=$nombreImagen width='95' height='37' />  </h6>
				 
					 
					 
 
				  </td> 
				  <td></td> 
				  
				  ";
 
	 
		$table .= "</tr>";  
	 
}

$table .= "</table>";

$html = "<html>
			<head>
				<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
				<title>".strtoupper($pdf)."/".strtoupper($pdf)."</title>
			</head>
			<body>
				$table
			</body>
		</html>";

$dompdf = new Dompdf();

$dompdf->loadHtml($html);
//tamaño del papel
 
// ajustamos al tamaño de las dimenciones de la hoja A6 105mm de ancho
 
$dompdf->set_paper(array(0, 0,297.64,419.53), 'portrait');

$dompdf->render();
//Nombre del archivo
$dompdf->stream($pdf);
