<?php


/* Change to the correct path if you copy this example! */
require __DIR__ . '/../../autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

/* Most printers are open on port 9100, so you just need to know the IP 
 * address of your receipt printer, and then fsockopen() it on that port.
 */
 
 
try {
    
	$nombre_socio = "Pedro Alejandro Castillo";
	$accion_socio = "001";
	$hora_entrega= "15:30";
	$hora_solicitud= "14:30";
	$pedido=" Empanada:4 \n California Dinamita:5 \n Gaseosa lata:2";
	$comentarios = "Por favor entregar recibo";
	
	$connector = new NetworkPrintConnector("181.48.188.75", 9100);	
    /* Print a "Hello world" receipt" */
    $printer = new Printer($connector);
	$printer -> initialize();
	$printer -> setJustification(Printer::JUSTIFY_CENTER);
	$printer -> setTextSize(2, 2);
    $printer -> text("CLUB LOS LAGARTOS \n");
	$printer -> setTextSize(2, 2);
	$printer -> text("App para todos\n");
	$printer -> text("Para llevar \n\n");
	$printer -> setJustification(Printer::JUSTIFY_LEFT);
	$printer -> setTextSize(1, 1);
	$printer -> text("Nombre Socio: ".$nombre_socio."\n");
	$printer -> text("Numero Accion: ".$accion_socio."\n");
	$printer -> text("Hora Solicitud:".$hora_solicitud." \n");
	$printer -> text("Hora Entrega:".$hora_entrega." \n");	
	$printer -> text("Descripcion Pedido: \n".$pedido."\n");
	$printer -> text("Comentarios: ".$comentarios." \n");	
    $printer -> cut();
    
    /* Close printer */
    $printer -> close();
	echo "Impresion Terminada";
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}
