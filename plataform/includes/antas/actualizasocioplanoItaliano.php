<?php


  ini_set('max_execution_time', 0);
  include( "config.inc.php" );
  exit;

  $FIELD_TEMINATED = "TAB";
  $file = "basesocio/ItalianoJun1_2019.txt";

  $IDClub = 54;


	if($fp = fopen($file,"r")):


		ini_set('auto_detect_line_endings', true);
		while(!feof($fp)):

		$row = fgets($fp,4096);
		if(!empty($FIELD_TEMINATED))
			if($FIELD_TEMINATED == "TAB")
				$row_data = explode("\t",$row);
			else
				$row_data = explode($FIELD_TEMINATED,$row);



			$AccionPrincipal=trim($row_data[3]);
      $Documento=trim($row_data[0] );
      $Nombre=trim($row_data[1] );
      $Apellido="";
      $Celular=trim($row_data[2] );

      $Accion=$Documento;
      if($AccionPrincipal==""){
        $AccionPadre="";
        $TipoDerecho="Socio";
      }
      else{
        $AccionPadre=$AccionPrincipal;
        $TipoDerecho="Beneficiario";
      }

      $Usuario=trim($Documento);
			$Clave = trim($Documento);
			$NumeroInvitados=100;

			//quito ñ
			$Nombre = str_replace("Ñ","N",utf8_encode($Nombre));
      $Nombre = str_replace("'"," ",utf8_encode($Nombre));
      $Apellido = str_replace("Ñ","N",utf8_encode($Apellido));
      $Apellido = str_replace("'"," ",utf8_encode($Apellido));


			if(!empty($Documento) && $Documento!=0 &&  !empty($Nombre) &&  !empty($Accion) ):

				if($contador>=1): //Para no tomar el encabezado

						//Consulto si existe
						$sql_socio = "Select *
									  From Socio
									  Where IDClub = '".$IDClub."'
									  and NumeroDocumento = '$Documento' ";
						$result_socio = $dbo->query($sql_socio);
						$total_socio = $dbo->rows($result_socio);
						if((int)$total_socio>0):
							$datos_socio = $dbo->fetchArray($result_socio);

							$sql_update = "Update Socio
										  Set Accion = '".$Accion."', AccionPadre = '".$AccionPadre."',
										  Nombre = '".$Nombre."', Apellido = '".$Apellido."',
										  NumeroDocumento = '".$Documento."', CorreoElectronico = '".$CorreoElectronico."',
										  TipoSocio = '".$TipoDerecho."', PermiteReservar = 'S', Telefono = '".$Telefono."', Celular = '".$Celular."',
										  IDEstadoSocio = 1, NumeroInvitados = '".$NumeroInvitados."', NumeroAccesos = '".$NumeroInvitados."', Predio = '".$Predio."' ".$actualiza_clave."
										  Where IDSocio = '".$datos_socio["IDSocio"]."' and IDClub = '".$IDClub."'";

							echo "<br>".$sql_update;
							//exit;
							$dbo->query($sql_update);
						else:

							$sql_inserta = "Insert into Socio (IDClub,Accion,AccionPadre,Nombre,Apellido,NumeroDocumento,Email,Clave,CorreoElectronico,TipoSocio,PermiteReservar,CambioClave,IDEstadoSocio,FechaNacimiento,NumeroInvitados,NumeroAccesos, Predio)
											Values ('".$IDClub."','".$Accion."','".$AccionPadre."','".$Nombre."','".$Apellido."','".$Documento."','".$Usuario."',sha1('".$Clave."'),'".$CorreoElectronico."',
											'".$TipoDerecho."','S','S','1','".$FechaNacimiento."','".$NumeroInvitados."','".$NumeroInvitados."','".$Predio."')";

							echo "<br>" . $sql_inserta;
							$dbo->query($sql_inserta);
						endif;
				endif;

			else:
				echo "<br>Faltan Datos " . 	$Accion;
			endif;
			$contador++;


		endwhile;
	else:
		echo "No se pudo abrir";
	endif;
  echo "Terminado";
?>
