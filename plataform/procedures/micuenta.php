<?php

SIMReg::setFromStructure( array( "title" => "Mi Cuenta", "table" => "Usuario", "key" => "IDUsuario" ) );

if( !empty( $action ) )
	$_POST["IDAgencia"] = SIMUser::get("IDAgencia");
	
$key = SIMReg::get("key");
$table = SIMReg::get("table");

SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );

switch ( SIMNet::req("action") ) {
	case "update" :	
	
		SIMReg::setFromStructure( array( "title" => SIMReg::get("title") . ": Editar" ) );
		//los campos al final de las tablas
		$frm = SIMUtil::varsLOG( $_POST );
		
		
		if($_FILES["archivo_oculto"]["tmp_name"] != "")
		{
			$hoy = date('Y-m-d h-i-s');
			$frm["Foto"] = ($hoy.$_FILES["archivo_oculto"]["name"]);
			$documento = $frm["Foto"];
			copy($_FILES["archivo_oculto"]["tmp_name"],"file/Usuarios/".$frm["Foto"]);
		}
		
		if ( !empty( $frm["Password"] ) ){
			$frm["Pass"] = $frm["Password"];
			$frm["Password"] = sha1( $frm["Password"] );
			$sql = "UPDATE Usuario SET IDPerfil = '$frm[Perfil]', Nombre = '$frm[Nombre]', Email = '$frm[Email]', Pass = '$frm[Pass]', Password = '$frm[Password]'  WHERE IDUsuario = '$frm[id]'";
		} else {
			$sql = "UPDATE Usuario SET IDPerfil = '$frm[Perfil]', Nombre = '$frm[Nombre]', Email = '$frm[Email]'  WHERE IDUsuario = '$frm[id]'";
		}
		
		if ( !empty( $documento ) ){
			$sql1 = "UPDATE Usuario SET Foto = '$documento'  WHERE IDUsuario = '$frm[id]'";
		}
		$result1 = $dbo->query( $sql1 );
		
		$result = $dbo->query( $sql );
		
		//$id = $dbo->update( $frm , SIMReg::get("table") , SIMReg::get("key") , SIMNet::reqInt("id"), $array_exceptions , " AND IDRegistro = '" . SIMReg::get("IDRegistro") . "' "  );
		
		SIMHTML::jsRedirect( "micuenta.php?m=guardarexito" );		
			
	break;

	
} // End switch


$view = "views/micuenta/form";

?>