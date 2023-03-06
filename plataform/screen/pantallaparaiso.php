<?
  require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
  //Copio los datos del dia a la tabla de VarnishStat
					$time_start = SIMUtil::getmicrotime();

if(empty($_GET["IDClub"]))
  $_GET["IDClub"]=9;

$fecha_hoy=date("Y-m-d")." 00:00:00";
$sql_vista="INSERT IGNORE INTO `LogAccesoVista` SELECT * FROM `LogAcceso` WHERE FechaTrCr >= '".$fecha_hoy."'";
$dbo->query($sql_vista);

  function consulta_ocupacion_esp($Valores,$IDClub, $TipoConsulta,$id_socio ){
      $dbo =& SIMDB::get();

    	$fecha_hoy=date("Y-m-d")." 00:00:00";

   // 	$sql_vista="SELECT * FROM `LogAccesoVista` SELECT * FROM `LogAcceso` WHERE >= '".$fecha_hoy."'";
    //	$dbo->query($sql_vista);

    	if(empty($Valores["FechaInicio"])):
    		$Valores["FechaInicio"]=date("Y-m-d");
    		$Valores["FechaFin"]=date("Y-m-d");;
    	endif;

    	if(!empty($Valores["IDTipoInvitado"])):
    		$condicion_busqueda .= " and IDTipoInvitado = '".$Valores["IDTipoInvitado"]."'";
    	endif;

    	if(!empty($Valores["FechaInicio"])):
    		$condicion_fecha_ingreso  .= " and FechaIngreso >= '".$Valores["FechaInicio"]." 00:00:00'";
    		$condicion_fecha_salida  .= " and FechaSalida >= '".$Valores["FechaInicio"]." 00:00:00'";
    		$condicion_fecha_ingreso_ocupacion  = " and FechaIngreso <= '".$Valores["FechaInicio"]." 23:59:59'";
        $condicion_fecha_salida_ocupacion  = " and L.FechaTrCr >= '".$Valores["FechaInicio"]." 00:00:00'";
    	endif;

    	if(!empty($Valores["FechaFin"])):
    		$condicion_fecha_ingreso  .= " and FechaIngreso <= '".$Valores["FechaFin"]." 23:59:59'";
    		$condicion_fecha_salida  .= " and FechaSalida <= '".$Valores["FechaFin"]." 23:59:59'";
        $condicion_fecha_salida_ocupacion  .= " and L.FechaTrCr <= '".$Valores["FechaFin"]." 23:59:59'";
    	endif;


      //Consulto por Tipo
     	$sql_tipo = "Select  Tipo  From LogAccesoVista Where Tipo <> '' and IDClub = '".$IDClub."' and Entrada = 'S' " .  $condicion_fecha_ingreso_ocupacion . " ". $condicion_busqueda . " GROUP BY Tipo";
  //     	$sql_tipo = "Select  Tipo  From LogAccesoVista Where Tipo <> '' and IDClub = '".$IDClub."' and Entrada = 'S' ". $condicion_busqueda . " GROUP BY Tipo";



      $r_tipo=$dbo->query($sql_tipo);
    	while($row_tipo=$dbo->fetchArray($r_tipo)){

    	//Ocupacion Actual
     	$sql_ocupacion_actual = "Select  IDLogAcceso,IDInvitacion  From LogAccesoVista Where Tipo = '".$row_tipo["Tipo"]."' and IDClub = '".$IDClub."' and Entrada = 'S' " .  $condicion_fecha_ingreso_ocupacion . " ". $condicion_busqueda . " and FechatrCr >= '2020-03-16' Order By IDLogAcceso Desc Limit 4000";

      //$sql_ocupacion_actual = "Select  IDLogAcceso,IDInvitacion  From LogAcceso Where Tipo <> '' and IDClub = '".$IDClub."' and Entrada = 'S' and IDInvitacion = 913" .  $condicion_fecha_ingreso_ocupacion . " ". $condicion_busqueda . " Order By IDLogAcceso Desc Limit 3000";
    	$result_ocupacion_actual = $dbo->query($sql_ocupacion_actual);
      $cont=0;
    	while( $r_ocupacion_actual = $dbo->fetchArray( $result_ocupacion_actual ) ):
    		//echo "<br>" .$r_ocupacion_actual["IDLogAcceso"] . " - " . $r_ocupacion_actual["IDInvitacion"];


     		//Verifico si el ultimo movimiento fue de salida para saber si ya salio del club
     		$sql_salida = "Select L.IDLogAcceso, L.Tipo, L.IDInvitacion, L.Entrada, L.Salida,S.NumeroDocumento
                       From LogAccesoVista L
                       LEFT JOIN Socio S ON S.IDSocio = L.IDInvitacion
                       Where  L.IDInvitacion = '".$r_ocupacion_actual["IDInvitacion"]."'"  . $condicion_fecha_ingreso_ocupacion . " " . $condicion_busqueda . " Order by IDLogAcceso Desc Limit 1 ";

       $sql_salida = "Select L.IDLogAcceso, L.Tipo, L.IDInvitacion, L.Entrada, L.Salida, S.NumeroDocumento
                      From LogAccesoVista L
                      LEFT JOIN Socio S ON S.IDSocio = L.IDInvitacion
                      Where  L.Tipo <> '' and L.IDClub = '".$IDClub."' and L.IDInvitacion = '".$r_ocupacion_actual["IDInvitacion"]."'"  . $condicion_fecha_ingreso_ocupacion . " " . $condicion_busqueda . " Order by IDLogAcceso Desc Limit 1 ";


 		$sql_salidaOLD = "Select IDLogAcceso, Tipo, IDInvitacion, Entrada, Salida
    						From LogAccesoVista
    					    Where Tipo <> '' and IDClub = '".$IDClub."' and IDInvitacion = '".$r_ocupacion_actual["IDInvitacion"]."'"  . $condicion_fecha_ingreso_ocupacion . " " . $condicion_busqueda . " Order by IDLogAcceso Desc Limit 1 ";



    		$result_salida = $dbo->query($sql_salida);
    		$row_salida = $dbo->fetchArray($result_salida);
    		$tipo_salida=$row_salida["Tipo"];

    		if($row_salida["Salida"]<>"S"):
//echo " SALIDA+ ".$row_salida["Tipo"];
    			$documento="";
    			// Guardo el Id del invitado o socio para no tenerlo en cuenta mas de una vez
    			switch($row_salida["Tipo"]):
    				case "Socio":
    				//	$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row_salida["IDInvitacion"] . "' ", "array" );
    					$documento=$row_salida["NumeroDocumento"];
            // 	$documento=$datos_socio["NumeroDocumento"];

    				break;
            case "InvitadoAcceso":
            case "InvitadoSocio":
           //   echo $cont++;
    				//	$IDSocio=$dbo->getFields( "SocioInvitadoEspecial" , "IDSocio" , "IDSocioInvitadoEspecial = '".$row_salida["IDInvitacion"]."'" );
            //  $datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDSocio . "' ", "array" );

              $r_invitado = $dbo->query("Select S.IDSocio,S.NumeroDocumento FROM Socio S JOIN  SocioInvitadoEspecial SA ON SA.IDSocio = S.IDSocio
                                     WHERE SA.IDSocioInvitadoEspecial =  '".$row_salida["IDInvitacion"]."'" );
              $datos_socio=$dbo->fetchArray($r_invitado);

    					$documento=$datos_socio["NumeroDocumento"];
              $row_salida["IDInvitacion"]=$datos_socio["IDSocio"];
              //$tipo_salida="Socio";
    				break;
    			endswitch;

    			if(!in_array($documento,$array_documento) && $tipo_salida=="Socio"):
    				if($TipoConsulta=="Totales"):
    					$array_adentro[]=$row_salida["IDInvitacion"];
    				endif;
    			endif;
    			$array_documento[]=$documento;
    		endif;


    	endwhile;


    	//FIN Ocupacion actual
    }
 //echo "$cont TOTAL";exit;

    	return $array_adentro;

    	} // End Function

      function consulta_ocupacion($Valores,$IDClub, $TipoConsulta, $TipoInvitado="" ){
	$dbo =& SIMDB::get();

	$fecha_hoy=date("Y-m-d")." 00:00:00";
//	$sql_vista="INSERT IGNORE INTO `LogAccesoVista` SELECT * FROM `LogAcceso` WHERE FechaTrCr >= '".$fecha_hoy."'";
	//$dbo->query($sql_vista);

	$tipo_invitado="SELECT IDTipoInvitado, Nombre FROM TipoInvitado WHERE IDClub = '".$IDClub."' ";
	$r_tipo_invitado=$dbo->query($tipo_invitado);
	while($row_tipo_invitado=$dbo->fetchArray($r_tipo_invitado)){
		$array_tipo_inv[$row_tipo_invitado["IDTipoInvitado"]]=$row_tipo_invitado["Nombre"];

		$clasif_invitado="SELECT IDClasificacionInvitado, IDTipoInvitado, Nombre FROM ClasificacionInvitado WHERE  IDTipoInvitado = '".$row_tipo_invitado["IDTipoInvitado"]."' ";
		$r_clasif_invitado=$dbo->query($clasif_invitado);
		while($row_clasif_invitado=$dbo->fetchArray($r_clasif_invitado)){
			$array_clasif_inv[$row_clasif_invitado["IDTipoInvitado"]]=$row_clasif_invitado["Nombre"];
		}
	}

	if(empty($Valores["FechaInicio"])):
		$Valores["FechaInicio"]=date("Y-m-d");
		$Valores["FechaFin"]=date("Y-m-d");;
	endif;

	if(!empty($Valores["IDTipoInvitado"])):
		$condicion_busqueda .= " and IDTipoInvitado = '".$Valores["IDTipoInvitado"]."'";
	endif;

	if(!empty($Valores["FechaInicio"])):
		$condicion_fecha_ingreso  .= " and FechaIngreso >= '".$Valores["FechaInicio"]." 00:00:00'";
		$condicion_fecha_salida  .= " and FechaSalida >= '".$Valores["FechaInicio"]." 00:00:00'";
		$condicion_fecha_ingreso_ocupacion  = " and FechaIngreso <= '".$Valores["FechaInicio"]." 23:59:59'";
	endif;

	if(!empty($Valores["FechaFin"])):
		$condicion_fecha_ingreso  .= " and FechaIngreso <= '".$Valores["FechaFin"]." 23:59:59'";
		$condicion_fecha_salida  .= " and FechaSalida <= '".$Valores["FechaFin"]." 23:59:59'";
	endif;

	//Consulto por Tipo
	$sql_tipo = "Select  Tipo  From LogAccesoVista Where Tipo <> '' and IDClub = '".$IDClub."' and Entrada = 'S' " .  $condicion_fecha_ingreso_ocupacion . " ". $condicion_busqueda . " GROUP BY Tipo";
  $r_tipo=$dbo->query($sql_tipo);
	while($row_tipo=$dbo->fetchArray($r_tipo)){

		//Ocupacion Actual
		$sql_ocupacion_actual = "Select  IDLogAcceso,IDInvitacion  From LogAccesoVista Where Tipo = '".$row_tipo["Tipo"]."' and IDClub = '".$IDClub."' and Entrada = 'S' " .  $condicion_fecha_ingreso_ocupacion . " ". $condicion_busqueda . " and FechatrCr >= '2020-03-16' Order By IDLogAcceso Desc Limit 4000";
		//$sql_ocupacion_actual = "Select  IDLogAcceso,IDInvitacion  From LogAcceso Where Tipo <> '' and IDClub = '".$IDClub."' and Entrada = 'S' and IDInvitacion = 913" .  $condicion_fecha_ingreso_ocupacion . " ". $condicion_busqueda . " Order By IDLogAcceso Desc Limit 3000";
		$result_ocupacion_actual = $dbo->query($sql_ocupacion_actual);
    $cont=0;
		while( $r_ocupacion_actual = $dbo->fetchArray( $result_ocupacion_actual ) ):
			//echo "<br>" .$r_ocupacion_actual["IDLogAcceso"] . " - " . $r_ocupacion_actual["IDInvitacion"];


			//Verifico si el ultimo movimiento fue de salida para saber si ya salio del club
			$sql_salidaOLD = "Select IDLogAcceso, Tipo, IDInvitacion, Entrada, Salida
							From LogAccesoVista
								Where Tipo <> '' and IDClub = '".$IDClub."' and IDInvitacion = '".$r_ocupacion_actual["IDInvitacion"]."'"  . $condicion_fecha_ingreso_ocupacion . " " . $condicion_busqueda . " Order by IDLogAcceso Desc Limit 1 ";

  		$sql_salida = "Select L.IDLogAcceso, L.Tipo, L.IDInvitacion, L.Entrada, L.Salida,S.NumeroDocumento
                      From LogAccesoVista L
                      LEFT JOIN Socio S ON S.IDSocio = L.IDInvitacion
                      Where  L.Tipo <> '' and L.IDClub = '".$IDClub."' and L.IDInvitacion = '".$r_ocupacion_actual["IDInvitacion"]."'"  . $condicion_fecha_ingreso_ocupacion . " " . $condicion_busqueda . " Order by IDLogAcceso Desc Limit 1 ";

			$result_salida = $dbo->query($sql_salida);
			$row_salida = $dbo->fetchArray($result_salida);
			$tipo_salida=$row_salida["Tipo"];
			if($tipo_salida=="Contratista"):
   //   echo $cont++;

    //		$IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$row_salida["IDInvitacion"]."'" );

			//	$sql_datos_inv="SELECT IDTipoInvitado, IDClasificacionInvitado FROM Invitado WHERE IDInvitado = '".$IDInvitado."' LIMIT 1";
		//		$r_datos_inv=$dbo->query($sql_datos_inv);

          $r_datos_inv = $dbo->query("Select I.IDTipoInvitado,I.IDClasificacionInvitado FROM Invitado I JOIN  SocioAutorizacion S ON S.IDInvitado = I.IDInvitado
                                     WHERE S.IDSocioAutorizacion =  '".$row_salida["IDInvitacion"]."'" );
        //      $datos_invitado=$dbo->fetchArray($r_invitado);

				$row_datos_inv=$dbo->FetchArray($r_datos_inv);

				switch($TipoInvitado){

						case "ClasificacionInvitado":
							//$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDClasificacionInvitado" , "IDInvitado = '".$IDInvitado."'" );
							$tipo_invitado=$dbo->getFields( "ClasificacionInvitado" , "Nombre" , "IDClasificacionInvitado = '".$row_datos_inv["IDClasificacionInvitado"]."'" );
							if($tipo_invitado==""){
								//selecciono el primer tipo que encuentre de esa clasificacion
								$tipo_invitado=$array_clasif_inv[$row_datos_inv["IDTipoInvitado"]];
							}

						break;
						default:
							//$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDTipoInvitado" , "IDInvitado = '".$IDInvitado."'" );
							$tipo_invitado=$dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$row_datos_inv["IDTipoInvitado"]."'" );

				}

				//$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDTipoInvitado" , "IDInvitado = '".$IDInvitado."'" );
				//$tipo_invitado=$dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$IDTipoInvitado."'" );
				if(!empty($tipo_invitado))
					$tipo_salida=$tipo_invitado;
			endif;

			if($row_salida["Salida"]<>"S"):


				$documento="";
				// Guardo el Id del invitado o socio para no tenerlo en cuenta mas de una vez
				switch($row_salida["Tipo"]):
					case "Contratista":
					case "InvitadoSocio":

            $r_invitado = $dbo->query("Select I.NumeroDocumento FROM Invitado I JOIN  SocioAutorizacion S ON S.IDInvitado = I.IDInvitado
                                     WHERE S.IDSocioAutorizacion =  '".$row_salida["IDInvitacion"]."'" );
              $datos_invitado=$dbo->fetchArray($r_invitado);
         //$total_socio_copropietario=$row_cop["Total"];
					//	$IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$row_salida["IDInvitacion"]."'" );
					//	$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $IDInvitado . "' ", "array" );
						$documento = $datos_invitado["NumeroDocumento"];
					break;
					case "InvitadoAcceso":
        //      echo $cont++;
                $r_invitado = $dbo->query("Select I.NumeroDocumento FROM Invitado I JOIN  SocioInvitadoEspecial S ON S.IDInvitado = I.IDInvitado
                                     WHERE S.IDSocioInvitadoEspecial =  '".$row_salida["IDInvitacion"]."'" );
              $datos_invitado=$dbo->fetchArray($r_invitado);

				//		$IDInvitado=$dbo->getFields( "SocioInvitadoEspecial" , "IDInvitado" , "IDSocioInvitadoEspecial = '".$row_salida["IDInvitacion"]."'" );
				//		$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $IDInvitado . "' ", "array" );
						$documento = $datos_invitado["NumeroDocumento"];
					break;
					case "Socio":
					//	$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row_salida["IDInvitacion"] . "' ", "array" );
					//	$documento=$datos_socio["NumeroDocumento"];
						$documento = $row_salida["NumeroDocumento"];
					break;
					case "SocioInvitado":
						$datos_socio_invitado = $dbo->fetchAll( "SocioInvitado", " IDSocioInvitado = '" . $row_salida["IDInvitacion"] . "' ", "array" );
						$documento=$datos_socio_invitado["NumeroDocumento"];
					break;
				endswitch;


				if(!in_array($documento,$array_documento)):
          $array_id_adentro[]=$row_salida["IDLogAcceso"];
					if($TipoConsulta=="Totales"):
						$array_adentro[$tipo_salida]++;
					elseif($TipoConsulta=="ID"):
						$array_adentro[]=$row_salida["IDLogAcceso"];
					endif;
				endif;

				$array_documento[]=$documento;

			endif;


		endwhile;
		//FIN Ocupacion actual



	}

  $id_log_adentro=implode(",",$array_id_adentro);
  $sql_ocp="INSERT INTO SocioOcupacion (IDClub,Fecha,Socios,Otros,IDLogAcceso,UsuarioTrCr,FechaTrCr)
            VALUES ('".$_GET["IDClub"]."',NOW(),'','','".$id_log_adentro."','Cron',NOW())";
  $dbo->query($sql_ocp);

	return $array_adentro;

	} // End function



  $datos_cub=$dbo->fetchAll( "Club", " IDClub = '" . $_GET["IDClub"] . "' ", "array" );
  $logo_club = CLUB_ROOT.$datos_cub["FotoDiseno1"];
  $ruta_logo_club = CLUB_DIR.$datos_cub["FotoDiseno1"];

  $YearMenorEdad=date("Y")-18;

  $array_adentro_socio=consulta_ocupacion_esp($Valores,$_GET["IDClub"],"Totales",$id_socio_casa);

 // echo count($array_adentro_socio);exit;
  //$time_end = SIMUtil::getmicrotime();
		//			$time = $time_end - $time_start;
			//		$time = number_format($time,3);
	//				SIMUtil::display_msg("Tiempo de Procesamiento $time Segundos");
   //       exit;
  $id_socio_adentro=implode(",",$array_adentro_socio);


  /************************************************************************************************
  Socios, copropietarios,etc
  *************************************************************************************************/
 $sql_cop="SELECT COUNT(IDSocio) Total FROM Socio WHERE IDClub = '".$_GET["IDClub"]."' and TipoSocio = 'Titular' and Predio <> '' and IDSocio in ($id_socio_adentro)";

  $r_cop=$dbo->query($sql_cop);
  $row_cop=$dbo->fetchArray($r_cop);
  $total_socio_copropietario=$row_cop["Total"];


  $sql_no_cop="SELECT COUNT(IDSocio) Total FROM Socio WHERE IDClub = '".$_GET["IDClub"]."' and TipoSocio = 'Titular' and Predio = '' and IDSocio in ($id_socio_adentro) ";
  $r_cop=$dbo->query($sql_no_cop);
  $row_no_cop=$dbo->fetchArray($r_cop);
  $total_socio_no_copropietario=$row_no_cop["Total"];

  $total_soc=$total_socio_copropietario+$total_socio_no_copropietario;

  $porcentaje_copropietario=number_format(($total_socio_copropietario*100/$total_soc),0,'.',',');
  $porcentaje_socio_no_copropietario=number_format(($total_socio_no_copropietario*100/$total_soc),0,'.',',');;



  /************************************************************************************************
  CASAS
  *************************************************************************************************/
  //Casas Todos por casa
  $sql_casa="SELECT count(IDSocio) AS TotalCasa FROM `Socio` WHERE IDClub = '".$_GET["IDClub"]."'  and Predio not like '%MTV%' and  Predio not like '%MVT%' and IDSocio in ($id_socio_adentro) Group by Predio ";

  $r_casa=$dbo->query($sql_casa);
  //  $row_casa=$dbo->fetchArray($r_casa);
    $array_casa = $dbo->rows($r_casa);//$row_casa["TotalCasa"];
 // while($row_casa=$dbo->fetchArray($r_casa))
 //   $array_casa = $row_casa["IDSocio"];


  //Casas Adultos
  $sql_casa="SELECT count(IDSocio) AS TotalCasaAdulto FROM `Socio` WHERE IDClub = '".$_GET["IDClub"]."'  and Predio not like '%MTV%' and  Predio not like '%MVT%' and (YEAR(FechaNacimiento)<$YearMenorEdad or YEAR(FechaNacimiento)=0) and IDSocio in ($id_socio_adentro) ";
  $r_casa=$dbo->query($sql_casa);
    $row_casa=$dbo->fetchArray($r_casa);
    $array_casa_adulto= $row_casa["TotalCasaAdulto"];
 // while($row_casa=$dbo->fetchArray($r_casa))
 //   $array_casa_adulto[]=$row_casa["IDSocio"];
//  $array_casa_adulto=$dbo->fetchArray($r_casa);

    $tipo_invitado="SELECT IDTipoInvitado, Nombre FROM TipoInvitado WHERE IDClub = '".$_GET["IDClub"]."' ";
    $r_tipo_invitado=$dbo->query($tipo_invitado);
    while($row_tipo_invitado=$dbo->fetchArray($r_tipo_invitado)){
      $array_tipo_inv[$row_tipo_invitado["IDTipoInvitado"]]=$row_tipo_invitado["Nombre"];

      $clasif_invitado="SELECT IDClasificacionInvitado, IDTipoInvitado, Nombre FROM ClasificacionInvitado WHERE  IDTipoInvitado = '".$row_tipo_invitado["IDTipoInvitado"]."' ";
      $r_clasif_invitado=$dbo->query($clasif_invitado);
      while($row_clasif_invitado=$dbo->fetchArray($r_clasif_invitado)){
        $array_clasif_inv[$row_clasif_invitado["Nombre"]]=$row_clasif_invitado["IDTipoInvitado"];
        $array_clasif_inv_tot[$row_clasif_invitado["IDTipoInvitado"]][]=$row_clasif_invitado["Nombre"];
      }
    }

  //Casas Menor Edad
  $sql_casa="SELECT count(IDSocio) AS TotalMenores FROM `Socio` WHERE IDClub = '".$_GET["IDClub"]."'  and Predio not like '%MTV%' and  Predio not like '%MVT%' and YEAR(FechaNacimiento)>=$YearMenorEdad and YEAR(FechaNacimiento)!=0 and IDSocio in ($id_socio_adentro)";

  $r_casa=$dbo->query($sql_casa);
  $row_casa=$dbo->fetchArray($r_casa);
  //while($row_casa=$dbo->fetchArray($r_casa))
    $array_casa_menor= $row_casa["TotalMenores"];

 // exit;

  //Acceso Casa
  $Valores["FechaInicio"]=date("Y-m-d");
  $Valores["FechaFin"]=date("Y-m-d");

  $array_adentro = SIMUtil::consulta_ocupacion($Valores,$_GET["IDClub"],"Totales","ClasificacionInvitado");

  $datos_adentro=json_encode($array_adentro);

  $sql_ocp="INSERT INTO SocioOcupacion (IDClub,Fecha,Socios,Otros,UsuarioTrCr,FechaTrCr)
            VALUES ('".$_GET["IDClub"]."',NOW(),'".$id_socio_adentro."','".$datos_adentro."','Cron',NOW())";
  $dbo->query($sql_ocp);



$array_consultado=array();
foreach ($array_adentro as $key => $value) {
  //echo "<br>KEY" . $key ."=>". $value;
  $id_tipo=$array_clasif_inv[$key];
  $array_adentro_tipo[$array_tipo_inv[$id_tipo]][]=$key."|".$value;
  //para este tipo se deebn mostras la clasificacion asi sea 0

    $array_consultado[$id_tipo][]=$key;
    $array_clasif_inv;
}


  foreach ($array_clasif_inv_tot as $key => $value) {
    if($key==30){ //Solo para Empleados MDY
          foreach ($value as $key_clasif => $NombreClasif) {
            if(!in_array($NombreClasif,$array_consultado[$key])){
                $array_adentro_tipo[$array_tipo_inv[$key]][]=$NombreClasif."|0";
            }
          }
    }
  }

  $row_dia_casa["TotalAcceso"]=$array_casa;

  //Sumo los socios que no tiene predio registrado pero igual estan en el club
  foreach( $array_adentro as $tipo => $total ){
      if($tipo=="Socio"){
          $Total_SociosOcp=$total;
      }
  }




  $row_dia_casa_adulto["TotalAcceso"] = $array_casa_adulto;


  $row_dia_casa_menor["TotalAcceso"]=$array_casa_menor;



  /************************************************************************************************
  FIN CASAS
  *************************************************************************************************/


  /************************************************************************************************
  MULTIVILLAS
  *************************************************************************************************/

  //Multivillas Todos
   $sql_multi="SELECT count(IDSocio) AS TotalTodos FROM `Socio` WHERE IDClub = '".$_GET["IDClub"]."'  and (Predio like 'MTV%' or Predio like 'MVT%' ) and IDSocio in ($id_socio_adentro) Group by Predio ";
  $r_multi=$dbo->query($sql_multi);
  $array_multi = $dbo->rows($r_multi);
 // while($row_muti=$dbo->fetchArray($r_multi))
   // echo $array_multi = $row_muti["TotalTodos"];



  //Multivillas Adultos
  $sql_multi_adulto="SELECT count(IDSocio) AS TotalAdulto FROM `Socio` WHERE IDClub = '".$_GET["IDClub"]."'  and (Predio like 'MTV%' or Predio like 'MVT%' ) and (YEAR(FechaNacimiento)<$YearMenorEdad or YEAR(FechaNacimiento)=0) and IDSocio in ($id_socio_adentro) ";
  $r_multi_adulto=$dbo->query($sql_multi_adulto);

  while($row_muti_adulto=$dbo->fetchArray($r_multi_adulto))
    $array_multi_adulto = $row_muti_adulto["TotalAdulto"];



  //Multivillas Menor Edad
  $sql_multi_menor="SELECT count(IDSocio) AS TotalMenor FROM `Socio` WHERE IDClub = '".$_GET["IDClub"]."'  and (Predio like 'MTV%' or Predio like 'MVT%' ) and YEAR(FechaNacimiento)>=$YearMenorEdad and IDSocio in ($id_socio_adentro) ";
  $r_multi_menor=$dbo->query($sql_multi_menor);
  while($row_muti_menor=$dbo->fetchArray($r_multi_menor))
    $array_multi_menor = $row_muti_menor["TotalMenor"];


  $row_dia_multi["TotalAcceso"] = $array_multi;

  $row_dia_multi_adulto["TotalAcceso"] = $array_multi_adulto;

  $row_dia_multi_menor["TotalAcceso"] = $array_multi_menor;


  /************************************************************************************************
  FIN MULTIVILLAS
  *************************************************************************************************/

  //Acceso Multi Invitado
  $sql_dia_accion="SELECT count(IDLogAcceso) TotalAcceso
                  FROM LogAccesoDiario
                  WHERE (Tipo='Socio' or Tipo='SocioClub') and IDClub = '".$_GET["IDClub"]."' ";


  //Tabla casas
  $total_casas="301";
  $porcentaje_casas="100%";
  $total_casa_construida="284";
  $porcentaje_casa_construida="94%";
  $total_casa_en_construccion="17";
  $porcentaje_casa_en_construccion="6";
  $total_casas_ocupadas=$row_dia_casa["TotalAcceso"];
  $porcentaje_casas_ocupadas=number_format($row_dia_casa["TotalAcceso"]*100/$total_casas,'2','.',',');

  //Tabla Multivillas
  $total_multi="33";
  $porcentaje_multi="100";
  $total_multi_construida="33";
  $porcentaje_multi_construida="100";
  $total_multi_en_construccion="0";
  $porcentaje_multi_en_construccion="0";
  $total_multi_ocupadas=$row_dia_multi["TotalAcceso"];
  $porcentaje_multi_ocupadas=number_format($row_dia_multi["TotalAcceso"]*100/$total_multi,'2','.',',');

  //Tabla Total viviendas
  $total_viviendas=$total_casas+$total_multi;
  $porcentaje_viviendas=100;
  $total_viviendas_construidas=$total_casa_construida+$total_multi_construida;
  $porcentaje_viviendas_construidas=number_format($total_viviendas_construidas*100/$total_viviendas,'2','.',',');
  $total_viviendas_en_construccion=$total_casa_en_construccion+$total_multi_en_construccion;
  $porcentaje_viviendas_en_construccion=number_format($total_viviendas_en_construccion*100/$total_viviendas,'2','.',',');
  $total_vivienda_ocupadas=$total_casas_ocupadas+$total_multi_ocupadas;
  $porcentaje_vivienda_ocupadas=number_format($total_vivienda_ocupadas*100/$total_viviendas,'2','.',',');



  //Tabla poblacion casas
  $total_casa_adultos_socios=$row_dia_casa_adulto["TotalAcceso"];
  $total_casa_ninos_socios=$row_dia_casa_menor["TotalAcceso"];
  $total_casa_adultos_invitado=$array_adentro["InvitadoAcceso"];

  $total_casa_nino_invitado="";
  $total_poblacion_casas=$total_casa_adultos_socios+$total_casa_ninos_socios+$total_casa_adultos_invitado+$total_casa_nino_invitado;


  //Tabla poblacion MULTIVILLAS
  $total_multi_adultos_socios=$row_dia_multi_adulto["TotalAcceso"];
  $total_multi_nino_socios=$row_dia_multi_menor["TotalAcceso"];
  $total_multi_adultos_invitado=$row_dia_multi_invitado["TotalAcceso"];;
  $total_multi_nino_invitado="";
  $total_poblacion_multi=$total_multi_adultos_socios+$total_multi_nino_socios+$total_multi_adultos_invitado+$total_multi_nino_invitado;







  //Tabla Total pobacion
  //$total_acciones=$row_dia_accion["TotalAcceso"];
  $total_adulto_socio=$total_casa_adultos_socios+$total_multi_adultos_socios;
  $total_nino_socio=$total_casa_ninos_socios+$total_multi_nino_socios;
  $total_adulto_invitado=$total_casa_adultos_invitado+$total_multi_adultos_invitado;
  $total_nino_invitado=$total_casa_nino_invitado+$total_multi_nino_invitado;

  $TotalSociosParcial=$total_adulto_socio+$total_nino_socio;
  if($Total_SociosOcp>=$TotalSociosParcial){
    $SocioRestante=$Total_SociosOcp  - $TotalSociosParcial;
    //echo "A:".$Total_SociosOcp."-".$TotalSociosParcial;
  }
  else{
    //$SocioRestante= $TotalSociosParcial - $Total_SociosOcp;
    //echo "B:".$TotalSociosParcial."-".$Total_SociosOcp;
  }

  //echo " TOT: " . $total_casa_adultos_socios . " : " . $SocioRestante;
  //exit;
  $total_casa_adultos_socios+=$SocioRestante;
  $total_adulto_socio+=$SocioRestante;
  $total_poblacion_casas+=$SocioRestante;


  $total_personas_casa_multi=$total_acciones+$total_adulto_socio+$total_nino_socio+$total_adulto_invitado+$total_nino_invitado;





  //Tabla Mayordomo
  $total_mayordomo_residente=$array_tipo_persona["11"];
  $total_mayordomo_no_residente=$array_tipo_persona["1"];
  $total_oficios_varios=$array_tipo_persona["9"];
  $total_nineras=$array_tipo_persona["8"];
  $total_conductores="";
  $total_mayordomo=$total_mayordomo_residente+$total_mayordomo_no_residente+$total_oficios_varios+$total_nineras+$total_conductores;
  //Tabla Obras
  $total_obras=$array_tipo_persona["2"];
  $total_proveedores=$array_tipo_persona["4"];
  $total_obra_proveedor=$total_obras+$total_proveedores;
  //Tabla Empleados
  $total_empleado_club=$array_tipo_persona["3"];
  $total_contratistas_conjunto=$array_tipo_persona["21"];
  $total_contratistas_club=$array_tipo_persona["19"];
  $total_seguridad=$array_tipo_persona["7"];
  $total_wg=$row_otro["TotalAcceso"];
  $total_empleados=$total_empleado_club+$total_contratistas_conjunto+$total_contratistas_club+$total_seguridad+$total_wg;
  //Tabla personas empleados
  $total_personas_empleados=$total_empleados+$total_mayordomo;
  //Tabla resumen final
  $total_viviendas_ocupadas=$total_vivienda_ocupadas;
  $porcentaje_viviendas_ocupadas=$porcentaje_vivienda_ocupadas;
  $total_personas_club=$total_poblacion_casas+$total_poblacion_multi+$total_empleados;


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Paraiso <?php echo $datos_cub["Nombre"]; ?></title>
    <meta http-equiv="refresh" content="800" />

	<link href='https://fonts.googleapis.com/css?family=Raleway:200,400,300,700,500,600' rel='stylesheet' type='text/css'>
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="dist/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<style>


.banner {
}
.header {
    height: 50px;
    padding: 5px;
    width: 100%;
}

/*
Generic Styling, for Desktops/Laptops
*/
table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;/**Forzamos a que las filas tenga el mismo ancho**/
    width: 100%; /*El ancho que necesitemos*/
}

th {
  background: #fff;
  color: #000;
  font-weight: bold;
  text-align:center;
}
td, th {
  padding: 3px;
  border: 1px solid #ccc;
  text-align: center;
  font-size:<?php if(SIMUser::get("club")==34) echo "16px"; else echo "12px"; ?>;
  margin: 0;
  word-wrap: break-word;/*Si el contenido supera el tamano, adiciona a una nueve linea**/
  font-weight: bold;
}
tr{
  height: 10px;
}

thead{
}

.rosado{
  background-color: #EFC1E6;
}

.azul{
  background-color: #2F64C8;
  color:#FFF;
}

.verde{
  background-color: #6DEC32;
}

.amarillo{
  background-color: #FEFBB5;
}
.blanco{
  background-color: #FFF;
}
.rojo{
  background-color: #F4535F;
}


.cheader {
    background-color: #428BCA;
	   color: #FFFFFF;
}

</style>

  </head>
  <body>

    <table class="fixed">
    	<thead>
        <tr>
                <th width="80%">
                <h3 class="fecha">Paraiso <?php echo $datos_cub["Nombre"] ." " . SIMUtil::tiempo( date( "Y-m-d H:i:s" ) ) ?></h3>
                </th>
                <th align="right" style="background-color:<?php if($color_personalizado=="S") echo $colortv; else echo "#FFFCFC"; ?>;" <?php if($columnas>=10): $columna_ultima = ((int)count($elementos[$ids])-$columnas_titulo)+1; echo "colspan = '".$columna_ultima."' "; endif;  ?>>
                <?php
				$tamano = getimagesize($ruta_logo_club);
					$ancho = $tamano[0];              //Ancho
					$alto = $tamano[1];
				if($ancho>115):
					$tamano_logo = 'width="90" height="40"';
				endif;
				?>
                	<img class="boxlogo" src="<?php echo $logo_club; ?>" <?php echo $tamano_logo; ?> />
                </th>
            </tr>
            </thead>
            <tr>
                <th colspan="2">

                <table>
                  <tr>
                      <td valign="top">

                        <!--
                        <table>
                            <!--
                            <tr class="blanco">
                              <td>Total socios copropietarios</td>
                              <td><?php echo $total_socio_copropietario ?></td>
                              <td><?php echo $porcentaje_socio_copropietario ?>%</td>
                            </tr>
                          -->
                          <!--
                            <tr class="verde">
                              <td >Copropietarios</td>
                              <td><?php echo $total_socio_copropietario ?></td>
                              <td><?php echo $porcentaje_copropietario ?>%</td>
                            </tr>
                            <tr class="rojo">
                              <td>Socios no copropietarios</td>
                              <td><?php echo $total_socio_no_copropietario ?></td>
                              <td><?php echo $porcentaje_socio_no_copropietario ?>%</td>
                            </tr>
                            <!--
                            <tr class="amarillo">
                              <td>Copropietarios no socios</td>
                              <td><?php echo $total_copropietario_no_socio ?></td>
                              <td><?php echo $porcentaje_copropietario_no_socio ?>%</td>
                            </tr>
                          -->
                          <!--
                        </table>
                        <br>
                        -->

                        <table>
                            <tr class="azul">
                              <td>CASAS</td>
                              <td><?php echo $total_casas; ?></td>
                              <td><?php echo $porcentaje_casas; ?></td>
                            </tr>
                            <tr class="rosado">
                              <td >Construidas</td>
                              <td><?php echo $total_casa_construida; ?></td>
                              <td><?php echo $porcentaje_casa_construida; ?></td>
                            </tr>
                            <tr class="amarillo">
                              <td>En construccion</td>
                              <td><?php echo $total_casa_en_construccion; ?></td>
                              <td><?php echo $porcentaje_casa_en_construccion; ?>%</td>
                            </tr>
                            <tr class="verde">
                              <td>Ocupadas</td>
                              <td><?php echo $total_casas_ocupadas ?></td>
                              <td><?php echo $porcentaje_casas_ocupadas ?>%</td>
                            </tr>
                        </table>
                        <br>
                        <table>
                            <tr class="azul">
                              <td>MULTIVILLAS</td>
                              <td><?php echo $total_multi; ?></td>
                              <td><?php echo $porcentaje_multi; ?>%</td>
                            </tr>
                            <tr class="rosado">
                              <td >Construidas</td>
                              <td><?php echo $total_multi_construida; ?></td>
                              <td><?php echo $porcentaje_multi_construida; ?>%</td>
                            </tr>
                            <tr class="amarillo">
                              <td>En construccion</td>
                              <td><?php echo $total_multi_en_construccion; ?></td>
                              <td><?php echo $porcentaje_multi_en_construccion; ?>%</td>
                            </tr>
                            <tr class="verde">
                              <td>Ocupadas</td>
                              <td><?php echo $total_multi_ocupadas ?></td>
                              <td><?php echo $porcentaje_multi_ocupadas ?>%</td>
                            </tr>
                        </table>
                        <br>
                        <table>
                            <tr class="azul">
                              <td>TOTAL VIVIENDAS</td>
                              <td><?php echo $total_viviendas; ?></td>
                              <td><?php echo $porcentaje_viviendas; ?>%</td>
                            </tr>
                            <tr class="rosado">
                              <td >Construidas</td>
                              <td><?php echo $total_viviendas_construidas; ?></td>
                              <td><?php echo $porcentaje_viviendas_construidas; ?>%</td>
                            </tr>
                            <tr class="amarillo">
                              <td>En construccion</td>
                              <td><?php echo $total_viviendas_en_construccion; ?></td>
                              <td><?php echo $porcentaje_viviendas_en_construccion; ?>%</td>
                            </tr>
                            <tr class="verde">
                              <td>Ocupadas</td>
                              <td><?php echo $total_vivienda_ocupadas ?></td>
                              <td><?php echo $porcentaje_vivienda_ocupadas ?>%</td>
                            </tr>
                        </table>

                        <br>

                        <table>
                            <tr >
                                <td class="azul">Total Viviendas ocupadas</td>
                                <td class="verde"><?php echo $total_viviendas_ocupadas; ?></td>
                            </tr>
                            <tr class="rosado">
                                <td>Porcentaje</td>
                                <td><?php echo $porcentaje_viviendas_ocupadas; ?>%</td>
                            </tr>
                        </table>

                          <br>


                      </td>
                      <td valign="top">
                        <table>
                            <tr class="azul">
                                <td colspan="2">POBLACION CASAS</td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos socios</td>
                                <td><?php echo $total_casa_adultos_socios; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños socios</td>
                                <td><?php echo $total_casa_ninos_socios; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos invitados</td>
                                <td><?php echo $total_casa_adultos_invitado; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños invitados</td>
                                <td><?php echo $total_casa_nino_invitado; ?></td>
                            </tr>
                            <tr class="verde">
                                <td>Total personas</td>
                                <td><?php echo $total_poblacion_casas; ?></td>
                            </tr>

                        </table>
                        <br>
                        <table>
                            <tr class="azul">
                                <td colspan="2">POBLACION MULTIVILLAS</td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos socios</td>
                                <td><?php echo $total_multi_adultos_socios; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños socios</td>
                                <td><?php echo $total_multi_nino_socios; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos invitados</td>
                                <td><?php echo $total_multi_adultos_invitado; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños invitados</td>
                                <td><?php echo $total_multi_nino_invitado; ?></td>
                            </tr>
                            <tr class="verde">
                                <td>Total personas</td>
                                <td><?php echo $total_poblacion_multi; ?></td>
                            </tr>

                        </table>

                        <br>
                        <!--
                        <table>
                            <tr class="azul">
                                <td colspan="2">POBLACION HOTEL</td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos socios</td>
                                <td>281</td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños socios</td>
                                <td>52</td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos copropietarios</td>
                                <td>496</td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños copropietarios</td>
                                <td>90</td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos invitados	</td>
                                <td>496</td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños invitados</td>
                                <td>90</td>
                            </tr>
                            <tr class="verde">
                                <td>Total personas</td>
                                <td>910</td>
                            </tr>

                        </table>
                        -->



                        <table>
                            <tr class="azul">
                                <td colspan="2">TOTAL POBLACION </td>
                            </tr>

                            <tr class="blanco">
                                <td>Adultos socios</td>
                                <td><?php echo $total_adulto_socio; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños socios</td>
                                <td><?php echo $total_nino_socio; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Adultos invitados</td>
                                <td><?php echo $total_adulto_invitado; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>Niños invitados</td>
                                <td><?php echo $total_nino_invitado; ?></td>
                            </tr>
                            <tr class="verde">
                                <td>Total personas</td>
                                <td><?php echo $total_personas_casa_multi; ?></td>
                            </tr>

                        </table>
                      </td>
                      <td valign="top">


                        <table>
                            <?php foreach ($array_adentro_tipo as $key => $value) {
                              if(!empty($key)){ ?>
                              <tr class="azul">
                                  <td colspan="2">
                                    <?php echo $key; ?><br>
                                    <table>
                                      <?php foreach ($value as $key_c => $value_c) {
                                          $array_total_clasif=explode("|",$value_c);
                                          if($array_total_clasif[0]!="InvitadoAcceso" && $array_total_clasif[0]!="Socio"){
                                        ?>
                                          <tr class="blanco">
                                            <td style="color:#000"><?php echo $array_total_clasif[0];  ?></td>
                                            <td style="color:#000"><?php echo $array_total_clasif[1]; ?></td>
                                          </tr>
                                          <?php
                                          }
                                        } ?>
                                    </table>


                                  </td>
                              </tr>
                            <?php
                            }
                            } ?>


                        </table>

                        <!--
                        <table>
                            <tr class="azul">
                                <td colspan="2">OTROS MDY</td>
                            </tr>
                            <? foreach( $array_adentro as $tipo => $total ){
                                if($tipo!="InvitadoAcceso" && $tipo!="Socio"){
                              ?>
                            <tr class="blanco">
                                <td><?php
                                switch($tipo){
    															case "InvitadoAcceso";
    																echo "Invitado Socio";
    															break;
    															default:
    																echo $tipo;
                                }
                                ?>
                              </td>
                                <td><?php
    														$total_adentro += (int)$total;
    														echo $total; ?></td>
                            </tr>
                          <?php }
                        }
                          ?>

                        </table>
                      -->


                        <!--
                        <table>
                            <tr class="azul">
                                <td colspan="2">MAYORDOMOS MDY</td>
                            </tr>
                            <tr class="blanco">
                                <td>MAYORDOMOS RESIDENTE</td>
                                <td><?php echo $total_mayordomo_residente; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>MAYORDOMOS NO RESIDENTES</td>
                                <td><?php echo $total_mayordomo_no_residente; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>OFICIOS VARIOS CASAS</td>
                                <td><?php echo $total_oficios_varios; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>NIÑERAS</td>
                                <td><?php echo $total_nineras; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>CONDUCTORES</td>
                                <td><?php echo $total_conductores; ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="verde"><?php echo $total_mayordomo; ?></td>
                            </tr>

                        </table>
                        <br>
                        <table>
                            <tr class="azul">
                                <td colspan="2">OBRAS Y PROVEEDORES MDY</td>
                            </tr>
                            <tr class="blanco">
                                <td>OBRAS</td>
                                <td><?php echo $total_obras; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>PROVEEDORES</td>
                                <td><?php echo $total_proveedores; ?></td>
                            </tr>
                            <tr >
                                <td></td>
                                <td class="verde"><?php echo $total_obra_proveedor; ?></td>
                            </tr>

                        </table>
                        <br>
                        <table>
                            <tr class="azul">
                                <td colspan="2">EMPLEADOS MDY</td>
                            </tr>
                            <tr class="blanco">
                                <td>EMPLEADOS CLUB</td>
                                <td><?php echo $total_empleado_club; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>CONTRATISTAS CONJUNTO</td>
                                <td><?php echo $total_contratistas_conjunto; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>CONTRATISTAS CLUB</td>
                                <td><?php echo $total_contratistas_club; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>SEGURIDAD</td>
                                <td><?php echo $total_seguridad; ?></td>
                            </tr>
                            <tr class="blanco">
                                <td>WG</td>
                                <td><?php echo $total_wg; ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="verde"><?php echo $total_empleados; ?></td>
                            </tr>

                        </table>
                      -->
                        <br>
                        <table>
                            <tr>
                                <td class="azul">Total personas empleados</td>
                                <td class="azul"><?php echo $total_adentro; ?></td>
                            </tr>

                            <tr>
                                <td class="azul">Total personas socios e invitados</td>
                                <td class="azul"><?php echo $total_personas_casa_multi; ?></td>
                            </tr>
                            <tr>
                                <td class="azul">Total personas</td>
                                <td class="azul"><?php echo $grantotal=$total_personas_club+$total_adentro; ?></td>
                            </tr>

                        </table>
                        <br>

                      </td>
                  </tr>


                </table>



              </th>



          </tr>
      </tbody>
    </table>
<?

  $time_end = SIMUtil::getmicrotime();
	$time = $time_end - $time_start;
	$time = number_format($time,3);
	SIMUtil::display_msg("Tiempo de Procesamiento $time Segundos");

  $Hora=date("H");
  if($Hora>=2){
    $DetallePersonas="";
    $Personas=json_encode($array_adentro_tipo);
    foreach ($array_adentro_tipo as $key => $value) {
      if(!empty($key)){
            $DetallePersonas.=$key;
            foreach ($value as $key_c => $value_c) {
                  $array_total_clasif=explode("|",$value_c);
                  if($array_total_clasif[0]!="InvitadoAcceso" && $array_total_clasif[0]!="Socio"){
                    $DetallePersonas .= $array_total_clasif[0];
                    $DetallePersonas .= $array_total_clasif[1];
                  }
              }
      }
    }
    $Fecha=date("Y-m-d");
    $Casas=$total_casas;
    $CasasPorcentaje=$porcentaje_casas;
    $CasasConstruidas=$total_casa_construida;
    $CasasConstruidasPorcentaje=$porcentaje_casa_construida;
    $CasasConstruccion=$total_casa_en_construccion;
    $CasasConstruccionPorcentaje=$porcentaje_casa_en_construccion;
    $CasasOcupadas=$total_casas_ocupadas;
    $CasasOcupadasPorcentaje=$porcentaje_casas_ocupadas;
    $Multivillas=$total_multi;
    $MultivillasPorcentajes=$porcentaje_multi;
    $MultiConstruidas=$total_multi_construida;
    $MultiConstruidasPOrcentajes=$porcentaje_multi_construida;
    $MultiConstruccion=$total_multi_en_construccion;
    $MultiConstruccionPorcentaje=$porcentaje_multi_en_construccion;
    $MultiOcupadas=$total_multi_ocupadas;
    $MultiOcupadasPorcentaje=$porcentaje_multi_ocupadas;
    $TotalViviendas=$total_viviendas;
    $TotalViviendasPorcentaje=$porcentaje_viviendas;
    $TotalViviendaConstruida=$total_viviendas_construidas;
    $TotalViviendaConstruidaPorcentaje=$porcentaje_viviendas_construidas;
    $TotalViviendaConstruccion=$total_viviendas_en_construccion;
    $TotalViviendaConstruccionPorcentaje=$porcentaje_viviendas_en_construccion;
    $TotalViviendaOcupada=$total_vivienda_ocupadas;
    $TotalViviendaOcupadaPorcentaje=$porcentaje_vivienda_ocupadas;
    $GranTotalViviendaOcupada=$$total_viviendas_ocupadas;
    $TotalViviendaPorcentaje=$porcentaje_viviendas_ocupadas;
    $CasaAdultoSocio=$total_casa_adultos_socios;
    $CasaNinoSocio=$total_casa_ninos_socios;
    $CasaAdultoInvitado=$total_casa_adultos_invitado;
    $CasaNinoInvitado="";
    $CasaTotalPersonas="";
    $MultiAdultoSocio=$total_multi_adultos_socios;
    $MultiNinoSocio=$total_multi_nino_socios;
    $MultiAdultoInvitado=$total_multi_adultos_invitado;
    $MultiNinoInvitado=$total_multi_nino_invitado;
    $MultiTotalPersonas=$total_poblacion_multi;
    $TotalAdultoSocio=$total_adulto_socio;
    $TotalNinoSocio=$total_nino_socio;
    $TotalAdultoInvitado=$total_adulto_invitado;
    $TotalNinoInvitado=$total_nino_invitado;
    $TotalPoblacion="";
    $ContratistasSocio="";
    $ContratistasMYD="";
    $EmpleadosMDY="";
    $TotalPersonasEmpleados=$total_adentro;
    $TotalPersonasSocioseInvitados=$total_personas_casa_multi;
    $GranTotalPersonas=$grantotal;
    $UsuarioTrCr="CRON";
    $FechaTrCr=date("Y-m-d H:i:s");

  $sql="INSERT INTO Ocupacion (Fecha,Casas,CasasPorcentaje,CasasConstruidas,CasasConstruidasPorcentaje,CasasConstruccion,
  CasasConstruccionPorcentaje,CasasOcupadas,CasasOcupadasPorcentaje,Multivillas,MultivillasPorcentajes,MultiConstruidas,MultiConstruidasPOrcentajes,MultiConstruccion,
  MultiConstruccionPorcentaje,MultiOcupadas,MultiOcupadasPorcentaje,TotalViviendas,TotalViviendasPorcentaje,TotalViviendaConstruida,
  TotalViviendaConstruidaPorcentaje,TotalViviendaConstruccion,TotalViviendaConstruccionPorcentaje,TotalViviendaOcupada,TotalViviendaOcupadaPorcentaje,
  GranTotalViviendaOcupada,TotalViviendaPorcentaje,CasaAdultoSocio,CasaNinoSocio,CasaAdultoInvitado,CasaNinoInvitado,CasaTotalPersonas,MultiAdultoSocio,
  MultiNinoSocio,MultiAdultoInvitado,MultiNinoInvitado,MultiTotalPersonas,TotalAdultoSocio,TotalNinoSocio,TotalAdultoInvitado,TotalNinoInvitado,TotalPoblacion,
  ContratistasSocio,ContratistasMYD,EmpleadosMDY,TotalPersonasEmpleados,TotalPersonasSocioseInvitados,GranTotalPersonas,DetallePersonas,Personas,UsuarioTrCr,FechaTrCr)
  VALUES ('".$Fecha."',
  '".$Casas."',
  '".$CasasPorcentaje."',
  '".$CasasConstruidas."',
  '".$CasasConstruidasPorcentaje."',
  '".$CasasConstruccion."',
  '".$CasasConstruccionPorcentaje."',
  '".$CasasOcupadas."',
  '".$CasasOcupadasPorcentaje."',
  '".$Multivillas."',
  '".$MultivillasPorcentajes."',
  '".$MultiConstruidas."',
  '".$MultiConstruidasPOrcentajes."',
  '".$MultiConstruccion."',
  '".$MultiConstruccionPorcentaje."',
  '".$MultiOcupadas."',
  '".$MultiOcupadasPorcentaje."',
  '".$TotalViviendas."',
  '".$TotalViviendasPorcentaje."',
  '".$TotalViviendaConstruida."',
  '".$TotalViviendaConstruidaPorcentaje."',
  '".$TotalViviendaConstruccion."',
  '".$TotalViviendaConstruccionPorcentaje."',
  '".$TotalViviendaOcupada."',
  '".$TotalViviendaOcupadaPorcentaje."',
  '".$GranTotalViviendaOcupada."',
  '".$TotalViviendaPorcentaje."',
  '".$CasaAdultoSocio."',
  '".$CasaNinoSocio."',
  '".$CasaAdultoInvitado."',
  '".$CasaNinoInvitado."',
  '".$CasaTotalPersonas."',
  '".$MultiAdultoSocio."',
  '".$MultiNinoSocio."',
  '".$MultiAdultoInvitado."',
  '".$MultiNinoInvitado."',
  '".$MultiTotalPersonas."',
  '".$TotalAdultoSocio."',
  '".$TotalNinoSocio."',
  '".$TotalAdultoInvitado."',
  '".$TotalNinoInvitado."',
  '".$TotalPoblacion."',
  '".$ContratistasSocio."',
  '".$ContratistasMYD."',
  '".$EmpleadosMDY."',
  '".$TotalPersonasEmpleados."',
  '".$TotalPersonasSocioseInvitados."',
  '".$GranTotalPersonas."',
  '".$DetallePersonas."',
  '".$Personas."',
  '".$UsuarioTrCr."',
  '".$FechaTrCr."') ";
  $dbo->query($sql);
  }





?>
  </body>
</html>
