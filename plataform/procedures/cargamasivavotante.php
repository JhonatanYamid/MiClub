 <?php

	 $titulo = "Invitado";


	SIMReg::setFromStructure( array(
						"title" => "VotacionVotante",
						"table" => "VotacionVotante",
						"key" => "IDVotacionVotante",
						"mod" => "Votacion"
	) );


	$script = "votacionesvotante";

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

function get_data($nombrearchivo,$file,$IGNORE_FIRTS_ROW,$FIELD_TEMINATED='',$field='',$IDClub,$IDVotacionEvento){

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
        $Nombre =  trim($sheet->getCell("A".$row)->getValue());
        $NumeroCasa =  trim($sheet->getCell("B".$row)->getFormattedValue());
        $Cedula = $sheet->getCell("C".$row)->getValue();
        $Coeficiente =  $sheet->getCell("D".$row)->getValue();
        $Consejero =  $sheet->getCell("E".$row)->getValue();
        $Moroso =  $sheet->getCell("F".$row)->getValue();


        $IDSocio = $dbo->getFields( "Socio", "IDSocio", "NumeroDocumento = '" . $Cedula . "' and IDClub = '".$IDClub."'" );


        if((int)$IDSocio<=0){
          echo "<br>Este documento no existe ene la base de usuarios".$Cedula;
          //$resultado=SIMWebServiceApp::set_socio($IDClub,$Cedula,$Cedula,$Parentesco,$Genero,$Nombre,$Apellido,$FechaNacimiento,$Cedula,$CorreoElectronico,$Telefono,$Celular,$Direccion,$TipoSocio,"A",100,$Cedula,$NumeroCasa,$Categoria,"S","","");
          //$IDSocio = $dbo->getFields( "Socio", "IDSocio", "NumeroDocumento = '" . $Cedula . "' and IDClub = '".$IDClub."'" );
        }

         $UsuarioCrea=SIMUser::get("IDUsuario");

         if((int)$IDSocio>0){
           $numregok+=SIMUtil::ingreso_votante($IDClub,$IDVotacionEvento,$IDSocio,$Nombre,$NumeroCasa,$Cedula,$Coeficiente,$Consejero,$Moroso,$UsuarioCrea);
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

					$result = get_data($nombre_archivo,SOCIOPLANO_DIR.$nombre_archivo,$_POST['IGNORELINE'],$_POST['FIELD_TEMINATED'],$_POST['field'],$_POST['IDClub'],$_POST['IDVotacionEvento']);
					if($result["Numregs"] > 0){
						echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";

				} // if($result["Numregs"] > 0){

				$time_end = SIMUtil::getmicrotime();
				$time = $time_end - $time_start;
				$time = number_format($time,3);
				SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
				exit;
		break;




	} // End switch



?>
