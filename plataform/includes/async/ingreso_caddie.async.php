<?php
	header('Content-Type: text/txt; charset=UTF-8');
	include( "../../procedures/general_async.php" );
	SIMUtil::cache( "text/json" );
	$dbo =& SIMDB::get();
	$frm = SIMUtil::makeSafe( $_POST );
	$frm_get =  SIMUtil::makeSafe( $_GET );
	if($frm["Cedula"]!=""){
		$sql_caddie="SELECT * FROM Caddie Where numeroDocumento = '".$frm["Cedula"]."' and IDClub = '".SIMUser::get("club")."' Limit 1";
		$result=$dbo->query($sql_caddie);
		if($dbo->rows($result)>0){
				$row_caddie=$dbo->fetchArray($result);
				$sql_registro="SELECT * FROM RegistroCaddie WHERE IDCaddie = '".$row_caddie["IDCaddie"]."' and fechaRegistro >= '".date("Y-m-d")."'";
				$result_reg=$dbo->query($sql_registro);
				if($dbo->rows($result_reg)<=0){
						$inserta_registro="INSERT INTO RegistroCaddie (IDCaddie, fechaRegistro, idUsuarioRegistra)
						VALUES ('".$row_caddie["IDCaddie"]."',NOW(),'".SIMUser::get("IDUsuario")."')";
						$dbo->query($inserta_registro);
						$nombre_caddie=$row_caddie["nombre"]." " . $row_caddie["apellido"];
						echo '["'.$nombre_caddie.'"]';
						//Si ya se hizo sorteo en el dia de hoy lo agrego de ultimas
						$sql_sorteo="SELECT * FROM SorteoCaddie WHERE fechaInicio <= CURDATE() AND fechaFin >= CURDATE() and IDClub = '".$_POST["IDClub"]."' Order by IDSorteoCaddie DESC Limit 1";
						$result_sorteo=$dbo->query($sql_sorteo);
						if($dbo->rows($result_sorteo)>0){
							$row_sorteo=$dbo->fetchArray($result_sorteo);
							$sql = "select MAX(orden) AS orden  "
			                . "from SorteoCaddieDetalle "
			                . "where IDSorteoCaddie = '" . $row_sorteo['IDSorteoCaddie'] . "' "
			                . "AND IDCategoriaCaddie = " . $row_caddie['IDCategoriaCaddie']." ";

			        $result = $dbo->query($sql);
			        $aDataMax = mysql_fetch_array($result, MYSQL_ASSOC);
			        if ($aDataMax != null)
			            $siguienteOrden = $aDataMax["orden"] + 1;
			        else
			            $siguienteOrden = 0;

			        $aSorteoDetalle = array(
			            "IDSorteoCaddie" => $row_sorteo['IDSorteoCaddie'],
			            "IDCaddie" => $row_caddie['IDCaddie'],
			            "IDCategoriaCaddie" => $row_caddie['IDCategoriaCaddie'],
			            "orden" => $siguienteOrden,
			            "estado" => 1,
			            "fechaRegistro" => date("Y-m-d H:i:s"),
			            "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
			        );

			        $dbo->insert($aSorteoDetalle, "SorteoCaddieDetalle", "IDSorteoCaddieDetalle");
						}

				}
				else{
						echo '["yaregistrado"]';
				}

		}
		else{
			echo '["noencontrado"]';
		}
	}
	else{
		echo '["vacio"]';
	}
?>
