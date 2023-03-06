 <?

	SIMReg::setFromStructure( array(
						"title" => "Socio",
						"table" => "Socio",
						"key" => "IDSocio",
						"mod" => "Socio"
	) );


	$script = "ajustesregistros";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

	//Verificar permisos
	SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );

	switch ( SIMNet::req( "action" ) ) {

		case "update" :

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );

          if((int)$frm["DocumentoFicticio1"]>0)
            $array_ficticio[]=$frm["DocumentoFicticio1"];
          if((int)$frm["DocumentoFicticio2"]>0)
            $array_ficticio[]=$frm["DocumentoFicticio2"];
          if((int)$frm["DocumentoFicticio3"]>0)
            $array_ficticio[]=$frm["DocumentoFicticio3"];
          if((int)$frm["DocumentoFicticio4"]>0)
            $array_ficticio[]=$frm["DocumentoFicticio4"];

          if(count($array_ficticio)>0) {
            $condicion_ficticio=implode(",",$array_ficticio);
          }


          if($frm["Tipo"]=="Socio"){
            $sql_socio = "SELECT IDSocio FROM Socio WHERE IDClub = '".$frm["IDClub"]."' and NumeroDocumento = '".$frm["DocumentoReal"]."'";
            $result_socio = $dbo->query($sql_socio);
            while($row_socio = $dbo->fetchArray($result_socio)):
              $id_principal = $row_socio["IDSocio"];

              $sql_datos_socio = "Select * From Socio Where NumeroDocumento in ($condicion_ficticio) order by IDSocio";
              $result_datos_socio = $dbo->query($sql_datos_socio);
              while($row_datos_socio = $dbo->fetchArray($result_datos_socio)):
                  $sql_autorizacion = "Update SocioAutorizacion Set IDSocio = '".$id_principal."' Where IDClub = '".$frm["IDClub"]."' and IDSocio ='".$row_datos_socio["IDSocio"]."' Limit 1";
                  $dbo->query($sql_autorizacion );
                  $sql_autorizacion = "Update SocioInvitadoEspecial Set IDSocio = '".$id_principal."' Where IDClub = '".$frm["IDClub"]."' and IDSocioInvitadoEspecial ='".$row_datos_socio["IDSocio"]."' Limit 1";
                  $dbo->query($sql_autorizacion );
                  $sql_autorizacion = "Update Vehiculo Set IDSocio = '".$id_principal."' Where IDSocio ='".$row_datos_socio["IDSocio"]."' Limit 1";
                  $dbo->query($sql_autorizacion );
                  $sql_autorizacion = "Update LicenciaSocio Set IDSocio = '".$id_principal."' Where IDSocio ='".$row_datos_socio["IDSocio"]."' Limit 1";
                  $dbo->query($sql_autorizacion );
                  //Borro el duplicado
                  $delete_duplicado = "Delete From Socio Where IDClub = '".$frm["IDClub"]."' and IDSocio = '".$row_datos_socio["IDSocio"]."' Limit 1";
                  $dbo->query($delete_duplicado );
              endwhile;
            endwhile;

          }
          elseif($frm["Tipo"]=="Invitado"){
            $sql_invitado = "SELECT IDInvitado FROM Invitado WHERE IDClub = '".$frm["IDClub"]."' and NumeroDocumento = '".$frm["DocumentoReal"]."'";
            $result_invitado = $dbo->query($sql_invitado);
            while($row_invitado = $dbo->fetchArray($result_invitado)):
              $id_principal = $row_invitado["IDInvitado"];

              $sql_datos_invitado = "Select * From Invitado Where NumeroDocumento in ($condicion_ficticio) order by IDInvitado";
              $result_datos_invitado = $dbo->query($sql_datos_invitado);
              while($row_datos_invitado = $dbo->fetchArray($result_datos_invitado)):

                  $sql_autorizacion = "Update SocioAutorizacion Set IDInvitado = '".$id_principal."' Where IDClub = '".$frm["IDClub"]."' and IDSocioAutorizacion ='".$row_datos_invitado["IDInvitado"]."' Limit 1";
                  $dbo->query($sql_autorizacion );
                  $sql_autorizacion = "Update SocioInvitadoEspecial Set IDInvitado = '".$id_principal."' Where IDClub = '".$frm["IDClub"]."' and IDSocioInvitadoEspecial ='".$row_datos_invitado["IDInvitado"]."' Limit 1";
                  $dbo->query($sql_autorizacion );
                  $sql_autorizacion = "Update Vehiculo Set IDInvitado = '".$id_principal."' Where IDInvitado ='".$row_datos_invitado["IDInvitado"]."' Limit 1";
                  $dbo->query($sql_autorizacion );
                  $sql_autorizacion = "Update LicenciaInvitado Set IDInvitado = '".$id_principal."' Where IDInvitado ='".$row_datos_invitado["IDInvitado"]."' Limit 1";
                  $dbo->query($sql_autorizacion );
                  $sql_autorizacion = "Update ObservacionInvitado Set IDInvitado = '".$id_principal."' Where IDInvitado ='".$row_datos_invitado["IDInvitado"]."' Limit 1";
                  $dbo->query($sql_autorizacion );
                  //Borro el duplicado
                  $delete_duplicado = "Delete From Invitado Where IDClub = '".$frm["IDClub"]."' and IDInvitado = '".$row_datos_invitado["IDInvitado"]."' Limit 1";
                  $dbo->query($delete_duplicado );
              endwhile;
            endwhile;
          }

    			SIMHTML::jsAlert("Registro Guardado Correctamente");
					SIMHTML::jsRedirect( $script.".php");
				}
				else
					exit;

		break;
	} // End switch


?>
