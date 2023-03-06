<?php
header('Content-Type: application/json; charset=utf-8');
	/* header('Content-Type: text/txt; charset=UTF-8'); */
	include( "../../procedures/general_async.php" );
	SIMUtil::cache( "text/json" );
	$dbo =& SIMDB::get();
	$frm = SIMUtil::makeSafe( $_POST );
	$frm_get =  SIMUtil::makeSafe( $_GET );
	$IDObjetos =  (array) json_decode($frm['Objetos']);

	if($frm["Tipo"]!="Salida"){
								
		foreach ($IDObjetos as $id_obj) {

		
			$sql_ingreso = "SELECT * FROM LogAccesoObjeto  WHERE IDAccesoObjeto ='$id_obj' ORDER BY IDLogAcceso  DESC LIMIT 1";
		
			$qry_ingreso = $dbo->query($sql_ingreso);
			$r_datosm = $dbo->fetch($qry_ingreso);


			if($r_datosm ['Salida'] =='S' ){
			
			$sql_ingreso_obj="INSERT INTO LogAccesoObjeto (IDClub,IDAccesoObjeto,Entrada,FechaIngreso,IDUsuario)
			VALUES('".SIMUser::get("club")."','".$id_obj."','S',NOW(),'".SIMUser::get("IDUsuario")."')";
			$dbo->query($sql_ingreso_obj);
	

			
			}

			if($r_datosm ['IDAccesoObjeto']==''){
				$sql_ingreso_obj="INSERT INTO LogAccesoObjeto (IDClub,IDAccesoObjeto,Entrada,FechaIngreso,IDUsuario)
				VALUES('".SIMUser::get("club")."','".$id_obj."','S',NOW(),'".SIMUser::get("IDUsuario")."')";
				$dbo->query($sql_ingreso_obj);
		  
			}

	}
	}
	else{
	
		foreach ($IDObjetos as $id_obj) {


			$sql_ingreso = "SELECT * FROM LogAccesoObjeto  WHERE IDAccesoObjeto ='$id_obj' ORDER BY IDLogAcceso  DESC LIMIT 1";

			$qry_ingreso = $dbo->query($sql_ingreso);
			$r_datosm = $dbo->fetch($qry_ingreso);
			
		if($r_datosm ['Salida'] !=='S' ){
	
			$sql_ingreso_obj="INSERT INTO LogAccesoObjeto (IDClub,IDAccesoObjeto,Salida,FechaSalida,IDUsuario)
			VALUES('".SIMUser::get("club")."','".$id_obj."','S',NOW(),'".SIMUser::get("IDUsuario")."')";
			$dbo->query($sql_ingreso_obj);

		}

		 
		}
	
		}


	

?>


