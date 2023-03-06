<script src="jscript/tabs/jquery.tabs.pack.js" type="text/javascript"></script>
<link rel="stylesheet" href="jscript/tabs/jquery.tabs.css" type="text/css" media="print, projection, screen">
<script type="text/javascript">
	/* FUNCION PARA MOSTRAR CARGAR MAS FOTOS */
	 $(document).ready(function(){
		$("#masfotos").click(function () {
    	  $("#CargarImg").toggle("slow");
    	});
		$("#masfotosareas").click(function () {
    	  $("#CargarImgAreas").toggle("slow");
    	});
		$("#masfotosseguimiento").click(function () {
    	  $("#CargarImgSeguimiento").toggle("slow");
    	});

	});
	/* FUNCION PARA MOSTRAR CARAGAR DOCUMENTOS */
	 $(document).ready(function(){
	 $("#masdoc").click(function () {
    	  $("#CargarDoc").toggle("slow");
    	});
	});
</script>
<body><?php

include_once("jscript/fckeditor/fckeditor.php"); // FCKEditor

SIMReg::setFromStructure( array(
					"title" => "Proyecto",
					"table" => "Proyecto",
					"key" => "IDProyecto",
					"mod" => "Proyecto"
) );


//para validar los campos del formulario
$array_valida = array(
	 "IDTipoProyecto" => "Tipo Proyecto" ,"Nombre" => "Nombre" , "Introduccion" => "Introduccion" , "Cuerpo" => "Cuerpo" , "Publicar" => "Publicar" , "Home" => "Home" , "FechaInicio" => "FechaInicio" , "FechaFin" => "FechaFin" , "FechaInicioPub" => "FechaInicioPub" , "FechaFinPub" => "FechaFinPub" 	
);



//extraemos las variables
$table = SIMReg::get( "table" );
$key = SIMReg::get( "key" );
$mod = SIMReg::get( "mod" );
$dbo =& SIMDB::get();

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );


if($action=="add")
{
?>
<script type="text/javascript">
	/* FUNCION PARA LOS TABS */
	$(function() {
         $('#ContenedorGaleria').tabs(1,{ disabled: [2,3,4,5] });
    });
</script>
<?	
}//end if
else
{
?>
<script type="text/javascript">
	/* FUNCION PARA LOS TABS */
	$(function() {
         $('#ContenedorGaleria').tabs(<?=$tab?>);
    });
</script>
<?
}//end else


switch ( $action ) {
	case "add" :
		print_form( "" , "insert" , "Agregar Registro" );
		break;
			
	case "insert" :
		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{			
			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST );
			
			
			//UPLOAD de imagenes
			if(isset($_FILES)){

				$files =  SIMFile::upload( $_FILES["NoticiaFile"] , FILES_PROYECTO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["NoticiaFile"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["NoticiaFile"] = $files[0]["name"];				
				
				
				$files =  SIMFile::upload( $_FILES["Foto1"] , FILES_PROYECTO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Foto1"] = $files[0]["name"];				
				
				$files =  SIMFile::upload( $_FILES["Foto2"] , FILES_PROYECTO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto2"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Foto2"] = $files[0]["name"];				
				
				$files =  SIMFile::upload( $_FILES["File"] , FILES_PROYECTO_DIR  );
				if( empty( $files ) && !empty( $_FILES["File"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["File"] = $files[0]["name"];				
				
				$files =  SIMFile::upload( $_FILES["FotoHomeDestacada"] , FILES_PROYECTO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["FotoHomeDestacada"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["FotoHomeDestacada"] = $files[0]["name"];
				
				
				
			}//end if

			//insertamos los datos
			$id = $dbo->insert( $frm , $table , $key );

			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $id . "&m=insertarexito" );
		}
		else
		print_form( $_POST , "insert" , "Agregar Registro" );
		break;
			
	case "edit":
			
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );
		print_form( $frm , "update" , "Realizar Cambios" );

		break ;

			
	case "editPlano":
			
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );
		$idplano = SIMNet::reqInt("idplano") ;
		print_form( $frm , "update" , "Realizar Cambios", $idplano );

		break ;

			
	case "update" :
		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
		{
				

			//los campos al final de las tablas
			$frm = SIMUtil::varsLOG( $_POST );


			//UPLOAD de imagenes
			if(isset($_FILES)){

				$files =  SIMFile::upload( $_FILES["NoticiaFile"] , FILES_PROYECTO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["NoticiaFile"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["NoticiaFile"] = $files[0]["name"];				
				
				
				$files =  SIMFile::upload( $_FILES["Foto1"] , FILES_PROYECTO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Foto1"] = $files[0]["name"];				
				
				$files =  SIMFile::upload( $_FILES["Foto2"] , FILES_PROYECTO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["Foto2"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["Foto2"] = $files[0]["name"];				
				
				$files =  SIMFile::upload( $_FILES["File"] , FILES_PROYECTO_DIR  );
				if( empty( $files ) && !empty( $_FILES["File"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["File"] = $files[0]["name"];		
				
				
				$files =  SIMFile::upload( $_FILES["FotoHomeDestacada"] , FILES_PROYECTO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $_FILES["FotoHomeDestacada"]["name"] ) )
					SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
				$frm["FotoHomeDestacada"] = $files[0]["name"];		
				
			}//end if

			$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );

			$frm = $dbo->fetchById( $table , $key , $id , "array" );

			SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );

			print_form( $frm , "update" ,  "Realizar Cambios" );
		}
		else
		print_form( $_POST , "update" ,  "Realizar Cambios" );
		break;
			
	case "del":
			$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id"), "array" );
			
			print_form( $frm , "delete" , "Remover Registro" );
		break ;
			
	case "delete" :
		$dbo =& SIMDB::get();
		$dbo->deleteById( $table , $key , SIMNet::reqInt("ID") );

		SIMHTML::jsRedirect( "?mod=" . $mod . "&amp;m=eliminarrexito" );
		break;
		
	case "delfoto":
		$foto = $_GET['foto'];
		$campo = $_GET['campo'];
		$id = $_GET['id'];
		$filedelete = FILES_PROYECTO_DIR.$foto;
		unlink($filedelete);
		$dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
		SIMHTML::jsAlert("Imagen Eliminada Correctamente");
		SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$id."" );
	break;
	
	
		case "PlanosTipo":
			
			
			
			$files =  SIMFile::upload( $_FILES["Foto1"] , PLANOS_DIR  );
			if( empty( $files ) && !empty( $_FILES["Foto1"]["name"] ) )
				SIMNotify::capture( "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido." , "error" );
			
			$frm = SIMUtil::varsLOG( $_POST );
			$frm["IDProyecto"] = $frm["ID"];
			//print_r( $frm );
			$frm["Foto1"] = $files[0]["name"];				
			
			if( !empty( $frm["IDProyectoPlano"] ) )//actualizar
			{
				if($frm["Foto1"] != "")
					$sql_update = " UPDATE ProyectoPlano SET Nombre = '" . $frm["Nombre"] . "', Titulo= '" . $frm["Titulo"] . "', Orden = '".$frm["Orden"]."',Publicar = '".$frm["Publicar"]."', Foto1 = '".$frm["Foto1"]."' WHERE IDProyectoPlano = '" . $frm["IDProyectoPlano"] . "' ";	
				else
					$sql_update = " UPDATE ProyectoPlano SET Nombre = '" . $frm["Nombre"] . "', Titulo= '" . $frm["Titulo"] . "', Orden = '".$frm["Orden"]."',Publicar = '".$frm["Publicar"]."' WHERE IDProyectoPlano = '" . $frm["IDProyectoPlano"] . "' ";
				
				$qry_update = $dbo->query( $sql_update );
				$idplano = 	$frm["IDProyectoPlano"];
			}
			else//insertar
			{
				$idplano = $dbo->insert( $frm , "ProyectoPlano" , "IDProyectoPlano" );
			}//end else
			
			
			//BORRAR ESPECIFICACIONES
			$sql_delete = " DELETE FROM PlanoEspecificacion WHERE IDProyectoPlano = '" . $idplano . "' ";
			$qry_delete = $dbo->query( $sql_delete );
			
			//CREAR ESPECIFICACIONES
			foreach( $frm["Especificacion"] as $key_esp => $value_esp )
				if( !empty( $value_esp ) )
				{
					 $sql_insert = " INSERT INTO PlanoEspecificacion ( IDProyectoPlano, Especificacion, Valor, Orden ) VALUES ( '" . $idplano . "','" . $value_esp . "','" . $frm["Valor"][ $key_esp ] . "','" . $frm["OrdenEsp"][ $key_esp ] . "' ) ";
					$qry_insert = $dbo->query( $sql_insert );
				}//end for
			
			//SUBIR GALERIA
			$files =  SIMFile::upload( $_FILES , PLANOS_DIR , "IMAGE" );
			foreach( $files as $llave => $archivo )
			{
				 $sql_insert = " INSERT INTO GaleriaPlanos (IDProyectoPlano, File ) VALUES ( '" . $idplano . "','" . $archivo["name"] . "' ) ";
				$qry_insert = $dbo->query( $sql_insert );
							
			}//end for
			
			
			echo( "<script type='text/javascript'>location.href='?mod=Proyecto&action=edit&id=" . $frm["IDProyecto"] . "&tab=5&m=insertarexito'</script>" );
	break;

	case "Caracteristicas":
		
		$frm = SIMUtil::varsLOG( $_POST );

		$sql_delete = "DELETE FROM ProyectoCaracteristica WHERE IDProyecto = '" . $frm["ID"] . "' ";	
		$dbo->query( $sql_delete );

		//CREAR ESPECIFICACIONES
		foreach( $frm["NombreCaracteristica"] as $key_esp => $value_esp )
			if( !empty( $value_esp ) )
			{
				$sql_insert = " INSERT INTO ProyectoCaracteristica ( IDProyecto, Nombre, Valor, Orden ) VALUES ( '" . $frm["ID"] . "','" . $value_esp . "','" . $frm["ValorCaracteristica"][ $key_esp ] . "','" . $frm["OrdenCaracteristica"][ $key_esp ] . "' ) ";
				$qry_insert = $dbo->query( $sql_insert );
			}//end for
						
		echo( "<script type='text/javascript'>location.href='?mod=Proyecto&action=edit&id=" . $frm["ID"] . "&tab=6&m=insertarexito'</script>" );
	
	break;
	
	case "delPlano" :
		$foto = $dbo->getFields( "ProyectoPlano" , array( "Nombre", "IDProyectoPlano", "Foto1") , "IDProyectoPlano = '$idplano'");


		$dbo->query("DELETE FROM ProyectoPlano WHERE IDProyectoPlano = '$idplano' ");
		echo( "<script type='text/javascript'>
		alert('Plano (" . $foto["Nombre"] . ") Eliminada Correctamente');
		location.href='?mod=Proyecto&action=edit&id=$id#PlanosTipo';
		</script>");
    break;
	
	
		case "Galeria":
			
			$files =  SIMFile::upload( $_FILES , GALERIA_DIR , "IMAGE" );

			foreach( $files as $llave => $archivo ){
				$sql_foto = "INSERT INTO FotoProyecto (IDFoto, IDProyecto, Nombre, Foto, FotoSize, FotoType, FechaTrCr, UsuarioTrCr ) 
							VALUES ('', '" . $_POST["ID"] . "','" . $archivo["origname"] . "','" . $archivo["name"] . "','" . $archivo["size"] . "','" . $archivo["type"] . "',NOW(), '" . SIMUser::get( "Nombre" ) . "' )";
				$dbo->query( $sql_foto );
			}//end for

			if( empty( $files ) )
			{
				
				$frm = $dbo->fetchById( $table , $key , $_POST["ID"] , "array" );
				SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
				print_form( $frm , "insert" , "Agregar Registro" );
				exit;
			}//end if
			
			
			echo( "<script type='text/javascript'>location.href='?mod=Proyecto&action=edit&id=$id&tab=2&m=insertarexito'</script>" );
	break;
	
	case "delFotoProyecto" :
		$foto = $dbo->getFields( "FotoProyecto" , array( "Nombre", "IDFoto", "Foto") , "IDFoto = '$IDFoto'");
		$archivo = GALERIA_DIR . "/" .$foto["Foto"];
		unlink( $archivo ); 
		$dbo->query("DELETE FROM FotoProyecto WHERE IDFoto = '$IDFoto' ");
		echo( "<script type='text/javascript'>
		alert('Foto (" . $foto["Foto"] . ") Eliminada Correctamente');
		location.href='?mod=Proyecto&action=edit&id=$id&tab=2';
		</script>");
    break;
	case "delFotoAreaComun" :
		$foto = $dbo->getFields( "FotoAreaComun" , array( "Nombre", "IDFoto", "Foto") , "IDFoto = '$IDFoto'");
		$archivo = GALERIA_DIR . "/" .$foto["Foto"];
		unlink( $archivo ); 
		$dbo->query("DELETE FROM FotoAreaComun WHERE IDFoto = '$IDFoto' ");
		echo( "<script type='text/javascript'>
		alert('Foto (" . $foto["Foto"] . ") Eliminada Correctamente');
		location.href='?mod=Proyecto&action=edit&id=$id&tab=3';
		</script>");
    break;
    
		case "GaleriaArea":
			
			$files =  SIMFile::upload( $_FILES , GALERIA_DIR , "IMAGE" );

			foreach( $files as $llave => $archivo ){
				$sql_foto = "INSERT INTO FotoAreaComun (IDFoto, IDProyecto, Nombre, Foto, FotoSize, FotoType, FechaTrCr, UsuarioTrCr ) 
							VALUES ('', '" . $_POST["ID"] . "','" . $archivo["origname"] . "','" . $archivo["name"] . "','" . $archivo["size"] . "','" . $archivo["type"] . "',NOW(), '" . SIMUser::get( "Nombre" ) . "' )";
				$dbo->query( $sql_foto );
			}//end for

			if( empty( $files ) )
			{
				
				$frm = $dbo->fetchById( $table , $key , $_POST["ID"] , "array" );
				SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
				print_form( $frm , "insert" , "Agregar Registro" );
				exit;
			}//end if
			
			
			echo( "<script type='text/javascript'>location.href='?mod=Proyecto&action=edit&id=$id&tab=3&m=insertarexito'</script>" );
	break;
	
	case "GaleriaSeguimiento":
			
			$files =  SIMFile::upload( $_FILES , GALERIA_DIR , "IMAGE" );

			foreach( $files as $llave => $archivo ){
				$sql_foto = "INSERT INTO FotoSeguimiento (IDFoto, IDProyecto, Nombre, Foto, FotoSize, FotoType, FechaTrCr, UsuarioTrCr ) 
							VALUES ('', '" . $_POST["ID"] . "','" . $archivo["origname"] . "','" . $archivo["name"] . "','" . $archivo["size"] . "','" . $archivo["type"] . "',NOW(), '" . SIMUser::get( "Nombre" ) . "' )";
				$dbo->query( $sql_foto );
			}//end for

			if( empty( $files ) )
			{
				
				$frm = $dbo->fetchById( $table , $key , $_POST["ID"] , "array" );
				SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
				print_form( $frm , "insert" , "Agregar Registro" );
				exit;
			}//end if
			
			
			echo( "<script type='text/javascript'>location.href='?mod=Proyecto&action=edit&id=$id&tab=4&m=insertarexito'</script>" );
	break;
	
	case "delFotoArea" :
		$foto = $dbo->getFields( "FotoAreaComun" , array( "Nombre", "IDFoto", "Foto") , "IDFoto = '$IDFoto'");
		$archivo = GALERIA_DIR . "/" .$foto["Foto"];
		unlink( $archivo ); 
		$dbo->query("DELETE FROM FotoAreaComun WHERE IDFoto = '$IDFoto' ");
		echo( "<script type='text/javascript'>
		alert('Foto (" . $foto["Foto"] . ") Eliminada Correctamente');
		location.href='?mod=Proyecto&action=edit&id=$id&tab=3';
		</script>");
    break;
		case "GaleriaSeguimiento":
			
			$files =  SIMFile::upload( $_FILES , GALERIA_DIR , "IMAGE" );

			foreach( $files as $llave => $archivo ){
				$sql_foto = "INSERT INTO FotoSeguimiento (IDFoto, IDProyecto, Nombre, Foto, FotoSize, FotoType, FechaTrCr, UsuarioTrCr ) 
							VALUES ('', '" . $_POST["ID"] . "','" . $archivo["origname"] . "','" . $archivo["name"] . "','" . $archivo["size"] . "','" . $archivo["type"] . "',NOW(), '" . SIMUser::get( "Nombre" ) . "' )";
				$dbo->query( $sql_foto );
			}//end for

			if( empty( $files ) )
			{
				
				$frm = $dbo->fetchById( $table , $key , $_POST["ID"] , "array" );
				SIMNotify::capture( "Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido." , "error" );
				print_form( $frm , "insert" , "Agregar Registro" );
				exit;
			}//end if
			
			
			echo( "<script type='text/javascript'>location.href='?mod=Proyecto&action=edit&id=$id&tab=5&m=insertarexito'</script>" );
	break;
	
	case "delFotoSeguimiento" :
		$foto = $dbo->getFields( "FotoSeguimiento" , array( "Nombre", "IDFoto", "Foto") , "IDFoto = '$IDFoto'");
		$archivo = GALERIA_DIR . "/" .$foto["Foto"];
		unlink( $archivo ); 
		$dbo->query("DELETE FROM FotoSeguimiento WHERE IDFoto = '$IDFoto' ");
		echo( "<script type='text/javascript'>
		alert('Foto (" . $foto["Foto"] . ") Eliminada Correctamente');
		location.href='?mod=Proyecto&action=edit&id=$id&tab=4';
		</script>");
    break;
	
	case "delGalPlano" :
		$foto = $dbo->getFields( "GaleriaPlanos" , array( "File") , "IDGaleria = '$Foto'");
		$archivo = GALERIA_DIR . "/" .$foto["File"];
		unlink( $archivo ); 
		$dbo->query("DELETE FROM GaleriaPlanos WHERE IDGaleria = '$Foto' ");
		echo( "<script type='text/javascript'>
		alert('Foto (" . $foto["File"] . ") Eliminada Correctamente');
		location.href='?mod=Proyecto&action=edit&id=$id&tab=5';
		</script>");
    break;
	
	
	case "InsertarTorre":
                $frm = SIMUtil::varsLOG( $_POST );
                $id = $dbo->insert( $frm , "Torre" , "IDTorre" );
                SIMHTML::jsAlert("Registro Exitoso");
                SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID] ."#Torre" );
                exit;
	break;
	
	case "ModificaTorre":

                $frm = SIMUtil::varsLOG( $_POST );
				$dbo->update( $frm , "Torre" , "IDTorre" , $frm[IDTorre] );
				 		
                SIMHTML::jsAlert("Modificacion Exitoso");
    			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$frm[ID] ."#Torre" );
                exit;
        break;	
		
		case "EliminaTorre":
			$id = $dbo->query( "DELETE FROM  Torre WHERE IDTorre   = '".$_GET[IDTorre ]."' LIMIT 1" );
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."#Torre" );
			exit;
		break;
		
	case "InsertarApartamento":
                $frm = SIMUtil::varsLOG( $_POST );
                $id = $dbo->insert( $frm , "Apartamento" , "IDApartamento" );
                SIMHTML::jsAlert("Registro Exitoso");
                SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID] ."#Apartamento" );
                exit;
	break;
	
	case "ModificaApartamento":

                $frm = SIMUtil::varsLOG( $_POST );
				$dbo->update( $frm , "Apartamento" , "IDApartamento" , $frm[IDApartamento] );
				 		
                SIMHTML::jsAlert("Modificacion Exitoso");
    			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$frm[ID] ."#Apartamento" );
                exit;
        break;	
		
		case "EliminaApartamento":
			$id = $dbo->query( "DELETE FROM  Apartamento WHERE IDApartamento   = '".$_GET[IDApartamento ]."' LIMIT 1" );
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."#Apartamento" );
			exit;
		break;
 
 	case "InsertarPropietarioInmueble":
                $frm = SIMUtil::varsLOG( $_POST );
                $id = $dbo->insert( $frm , "PropietarioInmueble" , "IDPropietarioInmueble" );
                SIMHTML::jsAlert("Registro Exitoso");
                SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=" . $frm[ID] ."#PropietarioInmueble" );
                exit;
	break;
	
	case "ModificaPropietarioInmueble":

                $frm = SIMUtil::varsLOG( $_POST );
				$dbo->update( $frm , "PropietarioInmueble" , "IDPropietarioInmueble" , $frm[IDPropietarioInmueble] );
				 		
                SIMHTML::jsAlert("Modificacion Exitoso");
    			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$frm[ID] ."#PropietarioInmueble" );
                exit;
        break;	
		
		case "EliminaPropietarioInmueble":
			$id = $dbo->query( "DELETE FROM  PropietarioInmueble WHERE IDPropietarioInmueble   = '".$_GET[IDPropietarioInmueble ]."' LIMIT 1" );
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect( "?mod=" . $mod . "&action=edit&id=".$_GET[id]."#PropietarioInmueble" );
			exit;
		break;
    
    		
	case "list" :
		$sql = SIMUtil::find('Proyecto',$_GET['Buscar_por'],$_GET['QryString'],$_GET['Ordenar_por'],$_GET['in_order']);


		list_r( $sql );
		break;
			
	default :
		list_r();
		break;

} // End switch



/*******************************************************************************************
 funtcion Print_form
 *******************************************************************************************/
function print_form( $frm = "" , $newmode , $submit_caption, $idplano = "" )
{	
	$dbo =& SIMDB::get();
	$key = SIMReg::get( "key" );
	$table = SIMReg::get( "table" );
	$mod = SIMReg::get( "mod" );
	
	if($newmode != "insert")
{
	/* TRAEMOS TODAS LAS FOTOS */
	$qry_fotos = $dbo->query( "SELECT * FROM FotoProyecto WHERE IDProyecto = $frm[$key] ;" );
	while( $r_fotos = $dbo->fetchArray( $qry_fotos ) )
		$array_fotos[ $r_fotos[IDFoto] ] = $r_fotos;
}

?>

<table class=adminheading>
	<tr>
		<th><?php echo SIMReg::get( "title" )?></th>


	</tr>
</table>
	<?
	//imprime el HTML de errores
	SIMNotify::each();
	include( "includes/tabs.html" );
	
	?>


<div id="ContenedorGaleria" style="z-index: 3;">
    <ul>
       <li><a href="#Galeria"><span>Proyecto</span></a></li>
       <li><a href="#GaleriaGaleria"><span>Galeria Fotos</span></a></li>
       <li><a href="#GaleriaArea"><span>Galeria Areas Comunes</span></a></li>
       <li><a href="#GaleriaSeguimiento"><span>Galeria Seguimiento</span></a></li>
       <li><a href="#PlanosTipo"><span>Planos Tipo</span></a></li>
       <li><a href="#Caracteristicas"><span>Caracteristicas</span></a></li>
       <li><a href="#Torre"><span>Torres</span></a></li>
       <li><a href="#Apartamento"><span>Aptos</span></a></li>
       <li><a href="#PropietarioInmueble"><span>Propietario</span></a></li>
    </ul>
<div id="Galeria">
<form name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>"
	method="post" enctype="multipart/form-data" class="formvalida">
<table class="adminform">
	<tr>
						<th>&nbsp;Datos B&aacute;sicos</th>
					</tr>
	<tr>
						<td>
		<table cellspacing="0" cellpadding="0" border="0" width="100%">


			<tr>
				<td class="columnafija">Tipo Poyecto</td>
				<td><?php echo SIMHTML::formPopUp( "TipoProyecto" , "Nombre" , "Nombre" , "IDTipoProyecto" , $frm["IDTipoProyecto"] , "[Seleccione el Tipo de Proyecto]" , "popup" , "title = \"Tipo\"" )?></td>
			</tr>
            <tr>
              <td class="columnafija">Ciudad</td>
              <td><?php echo SIMHTML::formPopUp( "Ciudad" , "Nombre" , "Nombre" , "IDCiudad" , $frm["IDCiudad"] , "[Seleccione la Ciudad]" , "popup" , "title = \"Tipo\"" )?></td>
            </tr>
            <tr>
              <td class="columnafija">Barrio</td>
              <td><input id=Barrio type=text size=25 name=Barrio
					class="input mandatory" title="Barrio" value="<?=$frm[Barrio] ?>"></td>
            </tr>
            <tr>
				<td class="columnafija">Asesor</td>
				<td><?php echo SIMHTML::formPopUp( "Asesor" , "Nombres" , "Nombres" , "IDAsesor" , $frm["IDAsesor"] , "[Seleccione el Asesor]" , "popup" , "title = \"Tipo\"" )?></td>
			</tr>
			<tr>
				<td class="columnafija">Nombre</td>
				<td><input id=Nombre type=text size=25 name=Nombre
					class="input mandatory" title="Nombre" value="<?=$frm[Nombre] ?>">				</td>
			</tr>
                        <tr>
				<td class="columnafija">Precio</td>
				<td><input id=Nombre type=text size=25 name=Precio
					class="input mandatory" title="Precio" value="<?=$frm[Precio] ?>">				</td>
			</tr>
                        <tr>
				<td class="columnafija">Nro Alcobas</td>
				<td><input id=Nombre type=text size=25 name=NAlcobas
					class="input mandatory" title="NAlcobas" value="<?=$frm[NAlcobas] ?>">				</td>
			</tr>
                        <tr>
				<td class="columnafija">Estrato</td>
				<td><input id=Nombre type=text size=25 name=Estrato
					class="input mandatory" title="Estrato" value="<?=$frm[Estrato] ?>">				</td>
			</tr>
            <tr>
				<td class="columnafija">Texto ToolTip</td>
				<td><input id=TextoToolTip type=text size=25 name=TextoToolTip
					class="input " title="TextoToolTip" value="<?=$frm[TextoToolTip] ?>">				</td>
			</tr>
						<tr>
							<td> Introduccion </td>
							<td>
							<?php
								$oCuerpo = new FCKeditor( "Introduccion" ) ;
								$oCuerpo->BasePath = "jscript/fckeditor/";
								$oCuerpo->Height = 400;
								//$oCuerpo->EnterMode = "p";
								$oCuerpo->Value =  $frm["Introduccion"];
								$oCuerpo->Create() ;
							?>							</td>
						</tr>
						<tr>
							<td> Cuerpo </td>
							<td>
							<?php
								$oCuerpo = new FCKeditor( "Cuerpo" ) ;
								$oCuerpo->BasePath = "jscript/fckeditor/";
								$oCuerpo->Height = 400;
								//$oCuerpo->EnterMode = "p";
								$oCuerpo->Value =  $frm["Cuerpo"];
								$oCuerpo->Create() ;
							?>							</td>
						</tr>
						<tr>
							<td> Texto Areas Comunes </td>
							<td>
							<?php
								$oCuerpo = new FCKeditor( "AreasComunes" ) ;
								$oCuerpo->BasePath = "jscript/fckeditor/";
								$oCuerpo->Height = 400;
								//$oCuerpo->EnterMode = "p";
								$oCuerpo->Value =  $frm["AreasComunes"];
								$oCuerpo->Create() ;
							?>							</td>
						</tr>
						<tr>
							<td> Seguimiento Obras </td>
							<td>
							<?php
								$oCuerpo = new FCKeditor( "SeguimientoObras" ) ;
								$oCuerpo->BasePath = "jscript/fckeditor/";
								$oCuerpo->Height = 400;
								//$oCuerpo->EnterMode = "p";
								$oCuerpo->Value =  $frm["SeguimientoObras"];
								$oCuerpo->Create() ;
							?>							</td>
						</tr>
			<tr>
				<td class="columnafija">Publiar</td>
				<td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?></td>
			</tr>
			<tr>
				<td class="columnafija">Nuevo</td>
				<td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Nuevo"] , 'Nuevo' , "class='input mandatory'" ) ?></td>
			</tr>
			<tr>
              <td class="columnafija">Publicar en el Home de Inmobiliaria</td>
			  <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["PublicarHome"] , 'PublicarHome' , "class='input mandatory'" ) ?></td>
			  </tr>
			<tr>
				<td class="columnafija">Home Constructora</td>
				<td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Home"] , 'Home' , "class='input mandatory'" ) ?></td>
			</tr>
			<tr>
				<td class="columnafija">Fecha Inicio</td>
				<td><input id=FechaInicio type=text size=10 readonly
					name=FechaInicio class="input mandatory calendar"
					title="FechaInicio" value="<?=$frm[FechaInicio] ?>"></td>
			</tr>
			<tr>
				<td class="columnafija">Fecha Fin</td>
				<td><input id=FechaFin type=text size=10 readonly name=FechaFin
					class="input mandatory calendar" title="FechaFin"
					value="<?=$frm[FechaFin] ?>"></td>
			</tr>
			<tr>
				<td class="columnafija">Fecha Inicio Publicacion</td>
				<td><input id=FechaInicioPub type=text size=10 readonly
					name=FechaInicioPub class="input mandatory calendar"
					title="FechaInicioPub" value="<?=$frm[FechaInicioPub] ?>"></td>
			</tr>
			<tr>
				<td class="columnafija">Fecha Fin Publicacion</td>
				<td><input id=FechaFinPub type=text size=10 readonly
					name=FechaFinPub class="input mandatory calendar"
					title="FechaFinPub" value="<?=$frm[FechaFinPub] ?>"></td>
			</tr>
			
			<tr>
				<td class="columnafija"><? if (!empty($frm[NoticiaFile])) {
					echo "<img src='".FILES_PROYECTO_ROOT."$frm[NoticiaFile]' width=55 >";
					?> <a
					href="<? echo "".$frm[$key]; ?>">
				<img src='images/trash.png' border='0'> </a> <?
				}// END if
				?> Foto Noticia 447 x 310</td>
				<td><input name="NoticiaFile" id=file class=""
					title="NoticiaFile" type="file" size="25" style="font-size: 10px"></td>
			</tr>

			<tr>
				<td class="columnafija"><? if (!empty($frm[Foto1])) {
					echo "<img src='".FILES_PROYECTO_ROOT."$frm[Foto1]' width=55 >";
					?> <a
					href="<? echo "".$frm[$key]; ?>">
				<img src='images/trash.png' border='0'> </a> <?
				}// END if
				?> Logo Grande (Imagen de 560 x 70 pixeles)</td>
				<td><input name="Foto1" id=file class=""
					title="Logo Grande" type="file" size="25" style="font-size: 10px"></td>
			</tr>
			<tr>
				<td class="columnafija"><? if (!empty($frm[Foto2])) {
					echo "<img src='".FILES_PROYECTO_ROOT."$frm[Foto2]' width=55 >";
					?> <a
					href="<? echo "".$frm[$key]; ?>">
				<img src='images/trash.png' border='0'> </a> <?
				}// END if
				?> Logo Peque&ntilde;o (Imagen de 118 x 118 pixeles)</td>
				<td><input name="Foto2" id=file class=""
					title="Logo Peque–o" type="file" size="25" style="font-size: 10px"></td>
			</tr>
			<tr>
				<td class="columnafija"><? if (!empty($frm[File])) {
					echo "<img src='".FILES_PROYECTO_ROOT."$frm[File]' width=55 >";
					?> <a
					href="<? echo "".$frm[$key]; ?>">
				<img src='images/trash.png' border='0'> </a> <?
				}// END if
				?> Foto Home constructora</td>
				<td><input name="File" id=file class=""
					title="Logo Peque–o" type="file" size="25" style="font-size: 10px"></td>
			</tr>
            
            <tr>
                <td> Tipo Archivo en Proyectos Nuevos</td>
                <td><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$imgSWF ) , $frm["TipoFile"] , "TipoFile" , "title=\"TipoFile\"" )?> </td>
            </tr>
            <tr>
				<td class="columnafija"><? if (!empty($frm[FotoHomeDestacada])) {
					echo "<img src='".FILES_PROYECTO_ROOT."$frm[FotoHomeDestacada]' width=55 >";
					?> <a
					href="<? echo "".$frm[$key]; ?>">
				<img src='images/trash.png' border='0'> </a> <?
				}// END if
				?> Foto Destacada Home Inmobiliaria (Imagen de 228 x 261 pixeles)</td>
				<td><input name="FotoHomeDestacada" id=file class=""
					title="Logo Peque–o" type="file" size="25" style="font-size: 10px"></td>
			</tr>
            
			<tr>
				<td class="columnafija">Codigo Video</td>
				<td><textarea rows="5" id=CodigoVideo cols=60 wrap=virtual
					class="input " title="Codigo Video" name=CodigoVideo><?=$frm[CodigoVideo]?></textarea></td>
			</tr>
			<tr>
				<td class="columnafija">Codigo Mapa</td>
				<td><textarea rows="5" id=CodigoMapa cols=60 wrap=virtual
					class="input " title="Codigo Mapa" name=CodigoMapa><?=$frm[CodigoMapa]?></textarea></td>
			</tr>

			<tr>
				<td class="columnafija">Latitud</td>
				<td><input id=Latitud type=text size=25 name=Latitud
					class="input " title="Latitud" value="<?=$frm[Latitud] ?>">				</td>
			</tr>
			<tr>
				<td class="columnafija">Longitud</td>
				<td><input id=Longitud type=text size=25 name=Longitud
					class="input " title="Longitud" value="<?=$frm[Longitud] ?>">				</td>
			</tr>

			<tr>
			  <td  class="columnafija" ><input type="button" class="submit" value="Ubicar Proyecto" onclick="window.open('mapaProyecto.php','','width=400, height=600, scrollbars=no');" /></td>
			  <td>&nbsp;</td>
			</tr>


			<tr>
				<td class="columnafija">Codigo Vias de Acceso</td>
				<td><textarea rows="5" id=CodigoVias cols=60 wrap=virtual class="input " title="Codigo Vias" name=CodigoVias><?=$frm[CodigoVias]?></textarea></td>
			</tr>
			<tr>
				<td class="columnafija">Codigo Sitios de Interes</td>
				<td><textarea rows="5" id=CodigoSitios cols=60 wrap=virtual class="input " title="Codigo Sitios" name=CodigoSitios><?=$frm[CodigoSitios]?></textarea></td>
			</tr>
            <tr>
                               <td>Posici&oacute;n</td>
                               <td>
                                    <input type="button" value="Ubicar Punto de Mapa" class="submit" onClick="window.open('popMapa.php','','width=920, height=579, scrollbars=no');"><br>
									<div id="strPosicion">
									X: <?=$frm[PosicionX]?><br>y: <?=$frm[PosicionY]?><br>                                    </div>
                                    
                               </td>
                         </tr>
            
			<tr>
				<td class="columnafija">SEO Title</td>
				<td><textarea rows="5" id=SEO_Title cols=60 wrap=virtual
					class="input " title="SEO Title" name=SEO_Title><?=$frm[SEO_Title]?></textarea></td>
			</tr>
			<tr>
				<td class="columnafija">SEO Description</td>
				<td><textarea rows="5" id=SEO_Description cols=60 wrap=virtual
					class="input " title="SEO Description"
					name=SEO_Description><?=$frm[SEO_Description]?></textarea></td>
			</tr>
			<tr>
				<td class="columnafija">SEO KeyWords</td>
				<td><textarea rows="5" id=SEO_KeyWords cols=60 wrap=virtual
					class="input " title="SEO_KeyWords" name="SEO KeyWords"><?=$frm[SEO_KeyWords]?></textarea></td>
			</tr>
			<tr>
				<td colspan=2 align=center><input type=submit name=submit
					value="<? echo $submit_caption ?>" class=submit> <input type=hidden name=ID value="<? echo $frm[$key] ?>"> <input type=hidden name=action value=<?=$newmode?>>
                    
                    
                    <input type="hidden" name="PosicionX" class="mandatory" title="Coordenadas" id="PosicionX" value="<?=$frm[PosicionX]?>">
                    <input type="hidden" name="PosicionY" class="mandatory" title="Coordenadas" id="PosicionY" value="<?=$frm[PosicionY]?>">
                    </td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
</div>

<!--Fin De Galeria--> 
	<div id="GaleriaGaleria">
       <table class="adminform">
            <tr>
                <th>&nbsp;Galeria De <?php echo $frm["Nombre"] ?></th>
            </tr>
            <tr>
                <td>
                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td width="283">
                    <input type="button" name="masfotos" value="Agregar Imagenes" id="masfotos" class="submit"/>
                    (width=&quot;421&quot; height=&quot;420&quot;)                    </td>
					<td >
                    	<table width="100%" style="display:none;" id="CargarImg" border="0">
                        	<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" class="formvalida">
                        	<tr>
                            	
                                    
                                    <?
									$numcols = 2;
									$contador = 1;
                                    for( $i = 1; $i <= 10; $i++ )
									{
                                    ?>	
                                    	<td>
                                        <input type="file" name="fichero_<?=$i?>" id="req" />
                                        </td>
                                    <?
										if( $contador % $numcols == 0 )
										{
											echo "</tr><tr>";
											$contador = 0;
										}//end if
										$contador++;
                                    }//end if
                                    ?>
                                    	
                                    
                             </tr>
                             <tr>
                             	<td colspan="<?=$numcols?>">
                                	<input type="hidden" name="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" value="Galeria" />
                                    <input type="submit" name="submit" value="Cargar Imagenes" class="submit" />
                                </td>
                             </tr>
                             </form>
                          </table>
                    </td>
                </tr>                
                </table>
             	</td>
            </tr>
            <tr>
                    <td>
                    <table>
					<?
						if($array_fotos!=Null)
						{
							?>
							<tr>
							<? 
							$cont=1;
							$modulo=0;
						    foreach($array_fotos as $clave_fotos => $valor_fotos)
							{
							?>
	                        <td align="center" valign="middle" nowrap class=row<?if(($cont%2)==0) echo "1"; else echo "2";?>>
	                            <? if (!empty($valor_fotos["Foto"]))
								{
	                                $ruta= GALERIA_ROOT . $valor_fotos["Foto"];
	                                $tam = @getimagesize($ruta);
	                                $w=$tam[0]+35;
	                                $h=$tam[1]+60;
	                            ?>
								<a href="javascript:;" onClick="PopupPic('<?=$ruta?>','$w','$w')">
									<img src='<?=$ruta ?>?<?=rand( 1,100 );?>' width="100" border=0>
								</a>
								<a href="<? echo ""?>">
								<img src="images/trash.png" border='0'>
								</a>
								<?
								}// END if
								?>
								<table border="0" cellspacing="4" cellpadding="0">
									<tr>
	                                    <td colspan="2" >
	                                    	<!--
                                            <form action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data">
		                                    	<table>
		                                    		<tr>
		                                    			<td colspan="2">Descripcion: </td>
		                                    		</tr> 
		                                    		<tr>
		                                    			<td colspan="2">
		                                    			<textarea rows="5" cols="30" name="DescripcionFotoImg" class="input"><? echo $valor_fotos["Descripcion"] ?></textarea>
		                                    			<input type="hidden" value="desGaleFoto" name="action" /> <input type="hidden" value="<?php echo $valor_fotos['IDFoto']?>" name="IDFotoProyecto" /> </td>
		                                    		</tr>
		                                    		<tr>
		                                    			<td colspan="2"><input type="submit" class="submit" value="Enviar"  /> </td>
		                                    		</tr> 
		                                    	</table>
	                                    	</form> 
	                                    		-->	
	                                    </td>	                                    
	                                    </tr>
	                                <tr>
	                                <tr>
	                                    <td width=70><b>Nombre:</b></td>
	                                    <td><? echo $valor_fotos["Nombre"] ?></td>
	                                    </tr>
	                                <tr>
	                                    <td width=70><b>Tama&ntilde;o:</b></td>
	                                    <td><? echo $valor_fotos["FotoSize"] ?></td>
	                                </tr>
	                                <tr>
	                                    <td width=70><b>Tipo:</b></td>
	                                    <td><? echo $valor_fotos["FotoType"] ?></td>
	                                </tr>
								</table>
								<?
								if(($cont%4)==0)
									echo "</td></tr><tr>";
								$cont++;
							}//end for
						}//end if
				
                    ?>
                    	</tr>
                    </table>
					</td>
                </tr>
         </table> 
    </div>
    <!--Fin De Galeria-->  
    
    <?
    include( "includes/proyecto/galeriaarea.php" );
    include( "includes/proyecto/galeriaseguimiento.php" );
    include( "includes/proyecto/planostipo.php" );
    include( "includes/proyecto/caracteristicas.php" );
	include( "includes/proyecto/torre.php" );
	include( "includes/proyecto/apartamento.php" );
	include( "includes/proyecto/propietarioinmueble.php" );
	?>
    
</div>


				<?
}// End function print_form()

/*******************************************************************************************
 funcion Listar
 *******************************************************************************************/
function list_r($sql=""){
	$key = SIMReg::get( "key" );
	$table = SIMReg::get( "table" );
	$mod =  SIMReg::get( "mod" );

if( empty( $sql ) )
	$sql =  "SELECT * FROM Proyecto ORDER BY " . $key;

	$result =& SIMUtil::createPag( $sql , 20 );

	?>



<table class="adminheading">
	<tr>
		<th><?php echo SIMReg::get( "title" )?></th>
	</tr>
</table>
	<?php
	filtrar();

	if( $result["rows"] > 0 )
	{
		//imprime el HTML de errores
		SIMNotify::each();
		?>


<table width=100% cellpadding=0 cellspacing=0 align=center>
	<tr>
		<td>
		<table class=adminlist width=100%>

			<tr>
				<th class=title colspan=9><?php echo strtoupper( SIMReg::get( "title" ) ) . ": Listado"?></th>
			</tr>


			<tr>
				<th class=texto colspan=9><?php echo $result["info"]?></th>
			</tr>
			<tr>
				<th align=center valign=middle width=64>Editar</th>
				<th>Tipo Poyecto&nbsp;</th>
				<th>Asosor&nbsp;</th>
				<th>Nombre&nbsp;</th>
						<th>Publicar&nbsp;</th>
						<th>Home&nbsp;</th>
				<th>FechaInicio&nbsp;</th>
				<th>FechaFin&nbsp;</th>
						<th align=center valign=middle width=64>Eliminar</th>
					</tr>

			<?
			$dbo =&SIMDB::get();
			while( $r = $dbo->object( $result["result"] ) )
			{
			
				?>

			<tr class=<%echo SIMUtil::repetition()?'row0':'row1';%>>
				<td align=center width=64><a
					href='<?php echo "" . $mod . "&amp;action=edit&amp;id=" . $r->$key?>'><img
					src='images/edit.png' border='0'></a></td>
				<td nowrap><?php echo $dbo->getFields( "TipoProyecto" , "Nombre" , "IDTipoProyecto = '" . $r->IDTipoProyecto . "'" )?>				</td>
                <td nowrap><?php 
				$nombre_comp = array("Nombres"=>"Nombres", "Apellidos"=> "Apellidos");
				$datos_nom = $dbo->getFields( "Asesor" , $nombre_comp , "IDAsesor = '" . $r->IDAsesor . "'" );
				
				echo $datos_nom[Nombres]." ".$datos_nom[Apellidos];
				?>				</td>
				<td nowrap><? echo $r->Nombre ?></td>
						<td nowrap><? echo $r->Publicar ?></td>
						<td nowrap><? echo $r->Home ?></td>
				<td nowrap><? echo $r->FechaInicio ?></td>
				<td nowrap><? echo $r->FechaFin ?></td>
						<td align=center width=64><a
					href='<? echo "" . $mod . "&amp;action=del&amp;id=" . $r->$key?>'><img
					src='images/trash.png' border='0'></a></td>
					</tr>
			<? } // END for
			?>
			<tr>
				<th class=texto colspan=9 nowrap><?php echo $result["pages"]?></th>
			</tr>
		</table>
	  </td>
	</tr>
</table>

			<?
	}// End if$rows


	else
	{
		SIMNotify::capture( "No se han encontrado registros" , "error" );
		//imprime el HTML de errores
		SIMNotify::each();
	}//end else



}// Enf function list()

/*******************************************************************************************
 funcion filtrar
 *******************************************************************************************/
function filtrar(){
	$mod =  SIMReg::get( "mod" );
	?>

<form name="frm" action='<?php echo SIMUtil::lastURI()?>' method="get">

<table width=100% align=center class="adminlist">
	<tr>

		<th align="center" class="title">BUSCAR</th>
	</tr>
	<tr>
		<td align="center"><legend>Filtrar</legend> <select name="Buscar_por"
			id="Buscar_por" class="popup">
			<option value=''>Buscar por...</option>
			<option value="TipoProyecto">Tipo Proyecto</option>			
			<option value="Nombre">Nombre</option>
			<option value="Introduccion">Introduccion</option>
			<option value="Cuerpo">Cuerpo</option>
			<option value="Publicar">Publicar</option>
			<option value="Home">Home</option>
			<option value="FechaInicio">FechaInicio</option>
			<option value="FechaFin">FechaFin</option>			

		</select> <input type="text" size="16" name="QryString" class=input> <select
			name="Ordenar_por" id=Ordenar_por class="popup">
			<option value=''>Ordenar por</option>
			<option value="TipoProyecto">Tipo Proyecto</option>			
			<option value="Nombre">Nombre</option>
			<option value="Introduccion">Introduccion</option>
			<option value="Cuerpo">Cuerpo</option>
			<option value="Publicar">Publicar</option>
			<option value="Home">Home</option>
			<option value="FechaInicio">FechaInicio</option>
			<option value="FechaFin">FechaFin</option>	

		</select> de forma <select name="in_order" class="popup" id=in_order>
			<option value="ASC">Ascendente</option>
			<option value="DESC">Descendente</option>
		</select> Listar <select name="listar" class="popup">
			<option value="10">10</option>
			<option value="15">15</option>
			<option value="20">20</option>
			<option value="25">25</option>
			<option value="30">30</option>
		</select> <input type="hidden" name="mod" value="<?=$mod?>"> <input
			type="hidden" name="action" value="list"> <input type="submit"
			name="submit" value="Buscar" class="submit"></td>
	</tr>
</table>
</form>
	<?
}//End function filtrar
?></body>

