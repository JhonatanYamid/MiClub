 <?
	$dbo = &SIMDB::get();

	SIMReg::setFromStructure(array(
		"title" => "planificaciondiaria",
		"titleB" => "planificacionesdiarias",
		"table" => "PlanificacionDiaria",
		"key" => "IDPlanificacionDiaria",
		"mod" => "PlanificacionDiaria"
	));

	$script = "planificaciondiaria";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");
	$action = SIMNet::req("action");

	$IDClub = SIMUser::get("club");
	$hoy = date("Y-m-d");
	
	function copiar_archivo(&$frm, $file, $hoy){
        
		$filedir = PLANIFICACIONDIARIA_DIR;
        $nuevo_nombre = rand(0, 1000000) . "_" . $hoy . "_" . $file['file']['name'];

        if (copy($file['file']['tmp_name'], "$filedir/$nuevo_nombre")) {
            
			echo "File : " . $file['file']['name'] . "... ";
            echo "Size :" . $file['file']['size'] . " Bytes ... ";
            echo "Status : Transfer Ok ...<br>";

            return $nuevo_nombre;
        } else {
            echo "error";
        }
    }

	function get_data($nombre_archivo, $archivo, $inicio, $fin){
		$dbo = &SIMDB::get();

		$hoy = date("Y-m-j h:i:s");
		$IDClub = SIMUser::get("club");
		$UsuarioTrCr = SIMUser::get("Nombre");

		$numregok = 0;
		$cont = 0;
		$arrRes = array();

		$fechaInicio = strtotime($inicio);
		$fechaFin = strtotime($fin);

		require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
		
        $inputFileType = PHPExcel_IOFactory::identify($archivo);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($archivo);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

		for ($row = 2; $row <= $highestRow; $row++) {
			
			$numeroDocumento = $sheet->getCell("A" . $row)->getValue();

			$idUsuario = $dbo->getFields("Usuario", "IDUsuario", "NumeroDocumento = '$numeroDocumento' AND IDClub = $IDClub");

			$col = 1;
			$colok = true;
			for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){

				if($idUsuario){
					$fecha = date("Y-m-j", $i);
					$idDianl = 0;
					$idTurno = 0;

					$cell = $sheet->getCellByColumnAndRow($col,$row);
					$valCell = $cell->getValue();
					$resCell = explode("-", $valCell);

					if($resCell[0] == 'N'){
						$diaLaboral = 0;
						$idDianl = $dbo->getFields("DiaNoLaboral", "IDDiaNoLaboral", "IDClub = $IDClub AND Activo = 'S' AND Codigo = '".$resCell[1]."'");

						if(!$idDianl){
							echo "<BR>Error!, El dia no laboral '".$resCell[1]."' no existe.<BR>";
							$colok = false;
						}
					}
					else if($resCell[0] == 'S'){
						$diaLaboral = 1;
						$idTurno = $dbo->getFields("Turnos", "IDTurnos", "IDClub = $IDClub AND Activo = 'S' AND Codigo = '".$resCell[1]."'");

						if(!$idTurno){
							echo "<BR>Error!, El turno '".$resCell[1]."' no existe.<BR>";
							$colok = false;
						}
					}
					else{
						echo "<BR>Error!, Los parametros del dia '$fecha' para el usuario con numero de documento'$numeroDocumento' no se han ingresado correctamente.<BR>";
						$colok = false;
					}

					if($colok){
						$arrDiario = [
							'IDClub' => $IDClub,
							'IDUsuario' => $idUsuario,
							'IDTurnos' => $idTurno,
							'IDDiaNoLaboral' => $idDianl,
							'Fecha' => $fecha,
							'DiaLaboral' => $diaLaboral,
							'Activo' => 'S',
							'UsuarioTrCr' => $UsuarioTrCr,
							'FechaTrCr' => $hoy
						];

						$idPlanAnt = $dbo->getFields("PlanificacionDiaria", "IDPlanificacionDiaria", "IDClub = $IDClub AND Activo = 'S' AND Fecha = '$fecha' AND IDUsuario = $idUsuario");
						if($idPlanAnt){
							$idPlan = $dbo->update($arrDiario, 'PlanificacionDiaria', 'IDPlanificacionDiaria', $idPlanAnt);
						}else{
							$idPlan = $dbo->insert($arrDiario, 'PlanificacionDiaria', 'IDPlanificacionDiaria');
						}

						$idCheck = $dbo->getFields("CheckinFuncionarios", "IDCheckinFuncionarios", "IDClub = $IDClub AND DATE(FechaEntrada) = '$fecha' AND IDUsuario = $idUsuario");

						if($idCheck){
							$idCheck = $dbo->update(array('IDPlanificacionDiaria'=>$idPlan), 'CheckinFuncionarios', 'IDCheckinFuncionarios', $idCheck);
						}

						$numregok++;
					}
									
					$col++;
				}
				else{
					echo "<BR>Error!, El usuario con numero de documento: '$numeroDocumento' no existe.<BR>";
				}
				
				$cont++;
			}
		}

		fclose($fp);
        return array("Numregs" => $cont, "RegsOK" => $numregok);
	}

	//Verificar permisos
	//SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

	switch ($action) {

		case "add":
			$view = "views/" . $script . "/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
		break;

		case "insert":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);		
			
				if($frm['Activo'] == '')
					$frm['Activo'] = 'S';

				//insertamos los datos
				$id = $dbo->insert($frm, $table, $key);

				$idCheck = $dbo->getFields("CheckinFuncionarios", "IDCheckinFuncionarios", "IDClub = ".$frm['IDClub']." AND DATE(FechaEntrada) = '".$frm['Fecha']."' AND IDUsuario = ".$frm['IDUsuario']);

				if($idCheck){
					$idCheck = $dbo->update(array('IDPlanificacionDiaria'=>$id), 'CheckinFuncionarios', 'IDCheckinFuncionarios', $idCheck);
				}


				SIMHTML::jsAlert("RegistroGuardadoCorrectamente");
				SIMHTML::jsRedirect($script . ".php");
			} else
				exit;

		break;

		case "edit":
			$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";

		break;

		case "update":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);						

				$id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

				$sqlUpd = "UPDATE CheckinFuncionarios SET IDPlanificacionDiaria = 0 WHERE IDPlanificacionDiaria = $id";
				$resUpd = $dbo->query($sqlUpd);

				$idCheck = $dbo->getFields("CheckinFuncionarios", "IDCheckinFuncionarios", "IDClub = ".$frm['IDClub']." AND DATE(FechaEntrada) = '".$frm['Fecha']."' AND IDUsuario = ".$frm['IDUsuario']);

				if($idCheck){
					$idCheck = $dbo->update(array('IDPlanificacionDiaria'=>$id), 'CheckinFuncionarios', 'IDCheckinFuncionarios', $idCheck);
				}

				$frm = $dbo->fetchById($table, $key, $id, "array");

				SIMHTML::jsAlert("RegistroGuardadoCorrectamente");
				SIMHTML::jsRedirect($script . ".php");
			} else
				exit;

		break;

		case "search":
			$view = "views/" . $script . "/list.php";
		break;

		case "cargarlote":
			$time_start = SIMUtil::getmicrotime();
            $nombre_archivo = copiar_archivo($_POST, $_FILES, $hoy);
           
			if ($nombre_archivo == "error") :
                echo "Error Transfiriendo Archivo";
                exit;
            endif;

            $result = get_data($nombre_archivo, PLANIFICACIONDIARIA_DIR.$nombre_archivo, $_POST['fechaInicio'], $_POST['fechaFin']);
            if ($result["Numregs"] > 0) {
                echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";
            } 

            $time_end = SIMUtil::getmicrotime();
            $time = $time_end - $time_start;
            $time = number_format($time, 3);

            SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
            exit;
		break;
		
		default:
			$view = "views/" . $script . "/list.php";
	} // End switch

	if (empty($view))
		$view = "views/" . $script . "/form.php";

?>