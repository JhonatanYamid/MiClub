 <?php

	 $titulo = "Invitado";


	SIMReg::setFromStructure( array(
						"title" => "Cartasbase",
						"table" => "Cartasbase",
						"key" => "IDCartasbase",
						"mod" => "Cartasbase"
	) );


	$script = "cartasbase";

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

  $array_tipo_socio=array("VITALICIOS","NUMERO","AUSENTES","ACUERDO DE PAGO","SOCIOS ASISTENTES EDAD 18 a 25 AÑOS","SOCIOS ASISTENTES EDAD 26 a 30 AÑOS","SOCIOS ASISTENTES EDAD 31 a 35 AÑOS","SOCIOS EN TRANSICION","PRESENTARON RENUNCIA","CUENTAS ESPECIALES","CLUBES");
  $sql_borra=$dbo->query("DELETE FROM CartasBase WHERE IDClub = '".$IDClub."'");

  $archivo = $file;
  $inputFileType = PHPExcel_IOFactory::identify($archivo);
  $objReader = PHPExcel_IOFactory::createReader($inputFileType);
  $objPHPExcel = $objReader->load($archivo);
  $sheet = $objPHPExcel->getSheet(0);
  $highestRow = $sheet->getHighestRow();
  $highestColumn = $sheet->getHighestColumn();
  for ($row = 4; $row <= $highestRow; $row++){
        $Numero =  trim($sheet->getCell("A".$row)->getValue());
        $Nombre =  trim(utf8_decode($sheet->getCell("B".$row)->getValue()));
        $Carta =  trim(utf8_decode($sheet->getCell("C".$row)->getFormattedValue()));
        $PorVencera30 = $sheet->getCell("D".$row)->getValue();
        $Dia30 =  $sheet->getCell("E".$row)->getValue();
        $Dia60 =  $sheet->getCell("F".$row)->getValue();
        $Dia90 =  $sheet->getCell("G".$row)->getValue();
        $Dia120 =  $sheet->getCell("H".$row)->getValue();
        $Mas120 =  $sheet->getCell("I".$row)->getValue();
        $Saldovencido60Dias =  $sheet->getCell("J".$row)->getCalculatedValue();
        $General =  $sheet->getCell("K".$row)->getCalculatedValue();
        $FechaAbono=$sheet->getCell("L".$row)->getFormattedValue();
        $AbonoActual=$sheet->getCell("M".$row)->getValue();
        $NuevoSaldo=$sheet->getCell("N".$row)->getCalculatedValue();

        if(empty($Numero) && in_array($Nombre,$array_tipo_socio)){
          $CategoriaActual=$Nombre;
        }
        else{
          //traigo el id del socio
          $AccionSocio=substr($Numero,0,4);
          $IDSocio = $dbo->getFields( "Socio", "IDSocio", "Accion = '" . $AccionSocio . "' and IDClub = '".$IDClub."'" );
          if(!empty($IDSocio)){
            $sql_insert="INSERT INTO CartasBase (IDClub,IDSocio,Numero,Nombres,Carta,PorVencer,Dia30,Dia60,Dia90,Dia120,Mas120,SaldoVencido60,GeneralValor,FechaAbono,AbonoActual,NuevoSaldo,Tipo,FechaTrCr,UsuarioTrCr)
                         VALUES ('".$IDClub."','".$IDSocio."','".$Numero."','".$Nombre."','".$Carta."','".$PorVencera30."','".$Dia30."','".$Dia60."','".$Dia90."','".$Dia120."','".$Mas120."','".$Saldovencido60Dias."',
                         '".$General."','".$FechaAbono."','".$AbonoActual."','".$NuevoSaldo."','".$CategoriaActual."',NOW(),'".SIMUser::get("IDUsuario")."')";
            $dbo->query($sql_insert);
            $numregok++;
          }
          else{
            $array_reporte_carga[]="El Socio ".$Numero." no fue encontrado";
          }
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

				$time_end = SIMUtil::getmicrotime();
				$time = $time_end - $time_start;
				$time = number_format($time,3);
				SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
				exit;
		break;




	} // End switch



?>
