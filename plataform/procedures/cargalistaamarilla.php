<?php

$titulo = "Lista amarilla";


SIMReg::setFromStructure( array(
                   "title" => "Listaamarilla",
                   "table" => "ListaAmarillaApp",
                   "key" => "IDListaAmarillaApp",
                   "mod" => "SocioInvitado"
) );


$script = "invitadosespeciales";

//extraemos las variables
$table = SIMReg::get( "table" );
$key = SIMReg::get( "key" );
$mod = SIMReg::get( "mod" );



function copiar_archivo(&$frm,$file) {
   $filedir=SOCIOPLANO_DIR;
   $nuevo_nombre = rand(0,1000000). "_".date("Y-m-d")."_".$file['file']['name'];
   if (copy($file['file']['tmp_name'], "$filedir/".$nuevo_nombre) ) {
       echo "File : ".$file['file']['name']."... ";
       echo "Size :".$file['file']['size']." Bytes ... ";
       echo "Status : Transfer Ok ...<br>";
       return $nuevo_nombre;

   }
   else{
       echo "error";
   }
}

function get_data($nombrearchivo,$file,$IGNORE_FIRTS_ROW,$FIELD_TEMINATED='',$field='',$IDClub){

$dbo =& SIMDB::get();

$numregok = 0;

require_once LIBDIR."excel/PHPExcel-1.8/Classes/PHPExcel.php";

$archivo = $file;
$inputFileType = PHPExcel_IOFactory::identify($archivo);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($archivo);
$sheet = $objPHPExcel->getSheet(0);
$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();
for ($row = 2; $row <= $highestRow; $row++){

           //Relacion de Campos
           $numerodocumento = $sheet->getCell("A".$row)->getValue();
           $nombre = $sheet->getCell("B".$row)->getValue();
           $razon = $sheet->getCell("C".$row)->getValue();
           
           if(is_numeric($numerodocumento)){

                if(isset($nombre) && isset($razon)){

                    $array_lista["IDClub"] = SIMUser::get("club");   
                    $array_lista["NumeroDocumento"] = $numerodocumento;
                    $array_lista["Nombre"] = $nombre;
                    $array_lista["Razon"]=$razon;
                           

                    $id = $dbo->insert( $frm , $table , $key);
                    /*print_r($respuesta["message"]);
                    echo "<br><br>";
                    $numregok++;*/
                     
                }
                else{
                    echo "<br>" . "El nombre y la razón no pueden estar vacíos";
                }

           }
           else{
               echo "<br>" . "El numero de documento debe ser numerico: " . $documento ;
           }




           $cont++;
   } // END While

     fclose($fp);
       return array("Numregs"=>$cont,"RegsOK"=>$numregok);


return false;
}




//extraemos las variables
$table = SIMReg::get( "table" );
$key = SIMReg::get( "key" );
$mod = SIMReg::get( "mod" );

//Verificar permisos
SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );


switch ( SIMNet::req( "action" ) ) {


   case "cargarplano" :
               $time_start = SIMUtil::getmicrotime();
               $nombre_archivo = copiar_archivo($_POST,$_FILES);
               if($nombre_archivo=="error"):
                   echo "Error Transfiriendo Archivo";
                   exit;
               endif;

               $result = get_data($nombre_archivo,SOCIOPLANO_DIR.$nombre_archivo,$_POST['IGNORELINE'],$_POST['FIELD_TEMINATED'],$_POST['field'],$_POST['IDClub']);
               if($result["Numregs"] > 0){
                   echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";

           } // if($result["Numregs"] > 0){

           $time_end = getmicrotime();
           $time = $time_end - $time_start;
           $time = number_format($time,3);
           display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
           exit;
   break;




} // End switch
