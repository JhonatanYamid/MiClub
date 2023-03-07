 <?


	switch ( SIMNet::req( "action" ) ) {
	
		case "insert" :
		
			if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );
					
					echo $IDClub = $_POST["IDClub"];
					$IDSocio = $_POST["IDSocio"];
					$HoraEntrega = $_POST["Hora"];
					$ComentariosSocio =$_POST["Comentario"];					
					$Celular = $_POST["Celular"];
					$Direccion = $_POST["Direccion"];	
					
					$contador=0;
					foreach($frm["IDProductos"] as $id_producto):
						$array_productos[$contador]["IDProducto"]=$id_producto;
						$array_productos[$contador]["Cantidad"]=$frm["Cantidad".$id_producto];
						$array_productos[$contador]["ValorUnitario"]=$dbo->getFields( "Producto" , "Precio" , "IDProducto = '" . $id_producto . "'" );			
						$contador++;
					endforeach;
					$array_productos = json_encode($array_productos);
					$DetallePedido = $array_productos;
					
					$respuesta = SIMWebServiceApp::set_domicilio($IDClub,$IDSocio,$HoraEntrega,$ComentariosSocio,$DetallePedido,$Celular,$Direccion);					
					
					SIMHTML::jsAlert($respuesta["message"]);
					SIMHTML::jsRedirect( "domicilios.php" );
				}
				else
					exit;
	break;
	
	} // End switch




?>