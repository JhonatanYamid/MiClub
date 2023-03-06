<?php

require('pdf_js.php');

class PDF_AutoPrint extends PDF_JavaScript
{
	function AutoPrint($printer='')
	{
		// Open the print dialog
		if($printer)
		{
			$printer = str_replace('\\', '\\\\', $printer);
			$script = "var pp = getPrintParams();";
			$script .= "pp.interactive = pp.constants.interactionLevel.full;";
			$script .= "pp.printerName = '$printer'";
			$script .= "print(pp);";
		}
		else
			$script = 'print(true);';
		$this->IncludeJS($script);
	}
}

$pdf = new PDF_AutoPrint('P','mm',array(65,80));
$pdf->AddPage();


$pdf->SetFont('Arial', '', 20);
$pdf->Text(5, 10, 'Mesa de Yeguas ');
$pdf->SetFont('Arial', '', 16);
$pdf->Text(5, 16, date("Y-m-d H:i:s"));
$pdf->SetFont('Arial', '', 14);
$pdf->Text(5, 22, 'Pedro Alexander Rodriguez');
$pdf->SetFont('Arial', '', 14);
$pdf->Text(5, 26, 'Predio w-82');
$pdf->SetFont('Arial', '', 14);
$pdf->Text(5, 30, 'Hora Entrada: ' . date("H:i:s"));
$pdf->SetFont('Arial', '', 14);
$pdf->Text(5, 34, 'Hora Salida: 18:00:00' );
$pdf->SetFont('Arial', '', 12);
$pdf->Text(5, 38, 'Por favor guarde este sticker en' );
$pdf->SetFont('Arial', '', 12);
$pdf->Text(5, 42, 'un lugar seguro.' );
$pdf->SetFont('Arial', '', 12);
$pdf->Text(5, 46, 'Sera solicitado en cualquier' );
$pdf->SetFont('Arial', '', 12);
$pdf->Text(5, 50, 'momento, de no tenerlo' );
$pdf->SetFont('Arial', '', 12);
$pdf->Text(5, 54, 'sera retirado de las instalaciones' );
$pdf->Image("https://www.miclubapp.com/file/socio/Barras_socio_9_4980.png", 5, 58, 60, 25);


/*
$pdf->SetFont('Arial', '', 18);
$pdf->Text(5, 42, 'Titulo Fuente 1 ');
$pdf->SetFont('Arial', '', 14);
$pdf->Text(5, 46, 'Fuente 1');

$pdf->SetFont('Arial', '', 20);
$pdf->Text(5, 50, 'Titulo Fuente 2 ');
$pdf->SetFont('Arial', '', 16);
$pdf->Text(5, 54, 'Fuente 2');


$pdf->SetFont('Arial', '', 24);
$pdf->Text(5, 60, 'Titulo Fuente 3 ');
$pdf->SetFont('Arial', '', 18);
$pdf->Text(5, 64, 'Fuente 3');
*/

$pdf->AutoPrint();
$pdf->Output();
?>
