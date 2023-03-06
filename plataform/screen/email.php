<?php

date_default_timezone_set('America/Bogota');

//Destinatario
//$to = 'alvaro.gonzalez.oviedo@mesadeyeguas.com.co,jorgechirivi@gmail.com,yobanny.nino@mesadeyeguas.com.co,eduardo.romero@mesadeyeguas.com.co,johanna.rosania@mesadeeguas.com.co,nohora.unibio@mesadeyeguas.com.co,calidad@mesadeyeguas.com.co,juan.garavito@mesadeyeguas.com.co,comunicaciones@mesadeyeguas.com.co,registro@mesadeyeguas.com.co,gerencia@mesadeyeguas.com.co
//';

$to = 'alvaro.gonzalez.oviedo@mesadeyeguas.com.co,yobanny.nino@mesadeyeguas.com.co,jorgechirivi@gmail.com';

//remitente del correo
$from = 'info@miclubapp.com';
$fromName = 'Mi Club';

//Asunto del email
$subject = 'Reporte Paraiso '.date('d/m/Y');

//Ruta del archivo adjunto
//$file = "Reporte.pdf";
$file = "/home/http/miclubapp/httpdocs/plataform/screen/Reporte.pdf";


//Contenido del Email
$htmlContent = "<h1>Reporte Paraiso</h1>
    <p>Buen día, a continuación adjuntamos reporte del día <b>Fecha: </b>".date('d/m/Y')." <b>Hora: </b>".date('h:i:s a')."</p>";

//Encabezado para información del remitente
$headers = "De: $fromName"." <".$from.">";

//Limite Email
$semi_rand = md5(time());
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

//Encabezados para archivo adjunto
$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

//límite multiparte
$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
"Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";

//preparación de archivo
if(!empty($file) > 0){
    if(is_file($file)){
        $message .= "--{$mime_boundary}\n";
        $fp =    @fopen($file,"rb");
        $data =  @fread($fp,filesize($file));

        @fclose($fp);
        $data = chunk_split(base64_encode($data));
        $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" .
        "Content-Description: ".basename($file)."\n" .
        "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" .
        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
    }
}
$message .= "--{$mime_boundary}--";
$returnpath = "-f" . $from;

//Enviar EMail
$mail = @mail($to, $subject, $message, $headers, $returnpath);

//Estado de envío de correo electrónico
$fecha_hora = "<b>Fecha: </b>".date('d/m/Y')." <br><b>Hora: </b>".date('h:i:s a');
echo $mail?"<h1>Correo enviado.</h1>".$fecha_hora:"<h1>El envío de correo falló.</h1>".$fecha_hora;
