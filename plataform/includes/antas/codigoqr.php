<?php

require( "config.inc.php" ); 

echo "codigo";
SIMUtil::enviar_codigo_qr(54,$parametros_codigo_qr);
echo "ok";
exit;


//set it to writable location, a place for temp generated PNG files
$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

//html PNG location prefix
$PNG_WEB_DIR = 'temp/';

include "lib/phpqrcode/qrlib.php";    

//ofcourse we need rights to create temp dir
if (!file_exists($PNG_TEMP_DIR))
    mkdir($PNG_TEMP_DIR);

$filename = $PNG_TEMP_DIR.'test.png';

$matrixPointSize = 10;
$errorCorrectionLevel = 'L';

$filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
QRcode::png('http://www.facebook.com', $filename, $errorCorrectionLevel, $matrixPointSize, 2); 

echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" /><hr/>';  
?>