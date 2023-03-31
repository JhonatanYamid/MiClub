<?
var_dump(1);
	SIMReg::setFromStructure(array(
		"title" => "Servicio",
		"table" => "Servicio",
		"key" => "IDServicio",
		"mod" => "Servicio"
	));


	$script = "serviciosclub";

	//extraemos las variables
	$table = SIMReg::get("table");
	$key = SIMReg::get("key");
	$mod = SIMReg::get("mod");

	//verificar si el servicio ya tiene alguna configuracion
	$idservicio_club = $dbo->getFields("Servicio", "IDServicio", "IDServicio = '" . $_GET[ids] . "' and IDClub = '" . SIMUser::get("club") . "' ");
	if (empty($idservicio_club)) :
		$_GET["action"] = "add";
	endif;


	$nombre_servicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $_GET[IDServicioMaestro] . "'");

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);



	switch (SIMNet::req("action")) {

		case "add":
			$view = "views/" . $script . "/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
			break;
			 
			 
			case "cargaindividual": 
			$frm = SIMUtil::varsLOG($_POST);
			$IDClub= $frm["IDClub"];
			$IDTipoReserva= $frm["IDTipoReserva"];
			$Handicap= $frm["Handicap"];
			$NumeroDocumento= $frm["NumeroDoc"];
			$ID= $frm["IDSocio"];
			
			//VALIDO EL PERMISO DE RESERVAR PARA IDENTIFICAR LOS SOCIOS Y SU PERMISO
                                $permite = $dbo->getFields("Servicio", "PermiteReservar", "IDServicio = '" . $_GET["ids"] . "'");
       
                                if($permite=='S'):
                                $permiso="S";
                                elseif($permite=='N'):
                                $permiso="N";
                                else:
                                $permiso="";
                                endif;
                                
                                //FIN VALIDACION
                                $sql_total = $dbo->query("SELECT COUNT(*) as total FROM SocioPermisoReserva  WHERE IDSocio='$ID' AND PermiteReservar='$permiso' AND IDServicio='".$_GET["ids"]."' AND IDTipoReserva='$IDTipoReserva' AND IDClub='$IDClub'");
                                $row = $dbo->fetchArray($sql_total);
                                $count = $row['total'];
                                if($count>0): 
			 SIMHTML::jsAlert("El socio ya esta agregado!");
			 SIMHTML::jsRedirect($script . ".php?action=edit&ids=".$_GET["ids"]);
			 
			       else:
			       
		$sql_insertar = $dbo->query("Insert Into SocioPermisoReserva (IDClub, IDServicio, IDSocio, IDTipoReserva, NumeroDocumento,PermiteReservar ,Handicap) Values ('$IDClub','".$_GET["ids"]."','$ID','$IDTipoReserva','$NumeroDocumento','$permiso','$Handicap' )");
			
			 SIMHTML::jsAlert("Registro Exitoso");
			 SIMHTML::jsRedirect($script . ".php?action=edit&ids=".$_GET["ids"]);
			 endif;
			break;
		                 
				 
			 
			case "cargarplano": 
			$frm = SIMUtil::varsLOG($_POST);
			$file= $_FILES['file']['tmp_name'];
			$IDClub= SIMUser::get("club");
  
			
		 
				
	    $file = $_FILES['file']['tmp_name']; 
            require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";
            $archivo = $file; 
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

 
            for ($row = 2; $row <= $highestRow; $row++) {
                $NumeroDocumento = $sheet->getCell("A" . $row)->getValue();
                      
                    	       $query = $dbo->query("SELECT IDSocio FROM Socio WHERE NumeroDocumento='$NumeroDocumento' AND IDClub='$IDClub'");
                    	       
                    	        while ($row_socios = mysqli_fetch_array($query)) {
                                    $ID= $row_socios["IDSocio"];
                                }
                                
      

                if (!empty($ID)) {
                
			//VALIDO EL PERMISO DE RESERVAR PARA IDENTIFICAR LOS SOCIOS Y SU PERMISO
                                $permite = $dbo->getFields("Servicio", "PermiteReservar", "IDServicio = '" . $_GET["ids"] . "'");
       
                                if($permite=='S'):
                                $permiso="S";
                                elseif($permite=='N'):
                                $permiso="N";
                                else:
                                $permiso="";
                                endif;
                                
                                //FIN VALIDACION
                                
                /*                
                   $sql_total = $dbo->query("SELECT COUNT(*) as total FROM SocioPermisoReserva  WHERE IDSocio='$ID' AND PermiteReservar='$permiso'  AND IDServicio='".$_GET["ids"]."'");
                                $row = $dbo->fetchArray($sql_total);
                                $count = $row['total'];
                                if($count==0):  */
                            
                 $sql_insertar = $dbo->query("Insert Into SocioPermisoReserva (IDClub,IDServicio,IDSocio,NumeroDocumento,PermiteReservar) Values ('$IDClub','".$_GET["ids"]."',' $ID','$NumeroDocumento','$permiso' )");
                  $ID=0;
                 //  endif;
                  
                } else {
                    echo "<br>" . "El numero de documento esta equivocado: " . $Accion;
                }

                $cont++;
            } // END for
   
			        SIMHTML::jsAlert("Registro Exitoso");
				SIMHTML::jsRedirect($script . ".php?action=edit&ids=".$_GET["ids"]);
			break;
		               

		case "insert":

			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);

				$CategoriasServicio = explode("|||", $frm[CategoriasServicios]);

				foreach ($CategoriasServicio as $id => $IDCategoriaServicio) :

					if (!empty($IDCategoriaServicio)) :
					
					
				 
						$Insert = "INSERT INTO CategoriasServiciosServicios (IDCategoriasServicios,IDServicio) VALUES ($IDCategoriaServicio,$frm[ID])";
						$dbo->query($Insert);
					endif;

				endforeach;


				if (empty($_FILES["Icono"]["name"])) {
					$id = $dbo->insert($frm, $table, $key);
				} else {
					$files =  SIMFile::upload($_FILES["Icono"], SERVICIO_DIR, "IMAGE");
					if (empty($files)) {
						SIMNotify::capture("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.", "error");
						//print_form( $frm , "insert" , "Agregar Registro" );
						exit;
					}

					$frm["Icono"] = $files[0]["innername"];
					$frm["IconoName"] = $files[0]["innername"];
					$id = $dbo->insert($frm, $table, $key);
				}

				SIMHTML::jsAlert("Registro Guardado Correctamente");
				SIMHTML::jsRedirect($script . ".php");
			} else
				exit;



			break;


		case "edit":
			$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("ids"), "array");
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";


			break;
			
	       case "editarprecio":
	       $table="PreciosReservas";
	       $key="IDPreciosReservas";
			$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
			$view = "views/" . $script . "/form.php";
			$newmode = "actualizarprecio";
			$titulo_accion = "Actualizar";


			break;
			
	       case "actualizarprecio":
	       $table="PreciosReservas";
	       $key="IDPreciosReservas";
	       $frm = SIMUtil::varsLOG($_POST);
	       				
		        $actualizar = "UPDATE $table SET Nombre ='$frm[Nombre]' , Valor='$frm[Valor]' WHERE $key = $frm[ID] ";
			$dbo->query($actualizar);
			SIMHTML::jsAlert("Actualizado Correctamente"); 
			SIMHTML::jsRedirect($script . ".php?ids=" . $frm["IDServicio"] ."&action=editarprecio&tab=precios");

			break;
			
	       case "insertarprecio":
	       $table="PreciosReservas";
	       $key="IDPreciosReservas";
	      $frm = SIMUtil::varsLOG($_POST);
             $club=SIMUser::get("club");
             $nombre=$frm["Nombre"];
             $valor=$frm["Valor"];
             $servicio=$_GET["ids"];
 
		   $actualizar = "INSERT INTO PreciosReservas ( IDClub, IDServicio, Nombre, Valor) VALUES ('".$club."','".$servicio."','".$nombre."','".$valor."')";
 
			$dbo->query($actualizar);
			SIMHTML::jsAlert("Guardaro Correctamente"); 
			SIMHTML::jsRedirect($script . ".php?ids=" . $servicio ."&action=editarprecio&tab=precios");

			break;
			
			
	     case "eliminarprecio":
	                  $servicio=$_GET["ids"];
 
			$sql_precio="DELETE   FROM PreciosReservas Where IDPreciosReservas = '".$_GET[id]."' ";
			$r_precio = $dbo->query($sql_precio);			
			if($r_precio){
				SIMHTML::jsAlert("Eliminado correctamente!");
			SIMHTML::jsRedirect($script . ".php?ids=" . $servicio ."&action=editarprecio&tab=precios");
				
			}else{
			        SIMHTML::jsAlert("Lo sentimos, no se puedo eliminar!");
			SIMHTML::jsRedirect($script . ".php?ids=" . $servicio ."&action=editarprecio&tab=precios");
			}
			 
			exit;
			break;
			
			 

		case "update":



			if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
				//los campos al final de las tablas
				$frm = SIMUtil::varsLOG($_POST);


				
				$CategoriasServicio = explode("|||", $frm[CategoriasServicios]);
                                 $delete = "DELETE FROM `CategoriasServiciosServicios` WHERE IDServicio='".$frm[ID]."'";
						$dbo->query($delete);
				foreach ($CategoriasServicio as $id => $IDCategoriaServicio) :

					if (!empty($IDCategoriaServicio)) :
						$Insert = "INSERT INTO CategoriasServiciosServicios (IDCategoriasServicios,IDServicio) VALUES ($IDCategoriaServicio,$frm[ID])";
						$dbo->query($Insert);
					endif;

				endforeach;


				$frm["LabelElementoSocio"] = utf8_decode($frm["LabelElementoSocio"]);
				$frm["LabelElementoExterno"] = utf8_decode($frm["LabelElementoExterno"]);

				if (count($frm["ServicioAsociado"]) > 0) {
					$frm["IDServicioAsociado"] = "|||" . implode("|||", $frm["ServicioAsociado"]);
				} else {
					$frm["IDServicioAsociado"] = " ";
				}

				foreach ($frm["IDDia"] as $Dia_seleccion) :
					$array_dia[] = $Dia_seleccion;
				endforeach;
				if (count($array_dia) > 0) :
					$id_dia = implode("|", $array_dia) . "|";
				endif;
				$frm["DiasListaEsperaReserva"] = $id_dia;
				

				if (empty($_FILES["Icono"]["name"])) {

					$id = $dbo->update($frm, $table, $key, $frm["ID"],  array("Icono"));
					$ids = $frm["ID"];
				} //end if
				else {
					$files =  SIMFile::upload($_FILES["Icono"], SERVICIO_DIR, "IMAGE");
					if (empty($files)) {
						SIMNotify::capture("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.", "error");
						//print_form( $frm , "insert" , "Agregar Registro" );
						exit;
					}

					$frm["Icono"] = $files[0]["innername"];
					$frm["IconoName"] = $files[0]["innername"];


					$id = $dbo->update($frm, $table, $key, $frm["ID"]);
					$ids = $frm["ID"];
				}

				if (empty($_FILES["ImagenEncabezado"]["name"])) {

					$id = $dbo->update($frm, $table, $key, $frm["ID"],  array("Icono"));
					$ids = $frm["ID"];
				} //end if
				else {
					$files =  SIMFile::upload($_FILES["ImagenEncabezado"], SERVICIO_DIR, "IMAGE");
					if (empty($files)) {
						SIMNotify::capture("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.", "error");
						//print_form( $frm , "insert" , "Agregar Registro" );
						exit;
					}

					$frm["ImagenEncabezado"] = $files[0]["innername"];
					$frm["ImagenEncabezadoName"] = $files[0]["innername"];



					$id = $dbo->update($frm, $table, $key, $frm["ID"]);
					$ids = $frm["ID"];
				}

				$delete_tipo_pago = $dbo->query("Delete From ServicioTipoPago Where IDServicio = '" . $ids . "'");
				foreach ($frm["IDTipoPago"] as $Pago_seleccionado) :
					$sql_servicio_forma_pago = $dbo->query("Insert into ServicioTipoPago (IDServicio, IDTipoPago) Values ('" . $ids . "', '" . $Pago_seleccionado . "')");
				endforeach;






				$frm = $dbo->fetchById($table, $key, $id, "array");





				SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
				SIMHTML::jsAlert("Registro Guardado Correctamente");
				SIMHTML::jsRedirect($script . ".php?action=edit&tab=configuracion&ids=" . $ids);
			} else
				exit;

			break;


		case "delfoto":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$id = $_GET['ids'];
			$filedelete = SERVICIO_DIR . $foto;
			unlink($filedelete);
			$borrar = "UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ";
			$dbo->query($borrar);
			SIMHTML::jsAlert("Imagen Eliminada Correctamente");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=configuracion&ids=" . $_GET["ids"]);

			break;




		case "InsertarServicioDisponibilidad":
			$frm = SIMUtil::varsLOG($_POST);

			foreach ($frm["IDDia"] as $Dia_seleccion) :
				$array_dia[] = $Dia_seleccion;
			endforeach;

			if (count($array_dia) > 0) :
				$id_dia = implode("|", $array_dia) . "|";;
			endif;
			$frm["IDDia"] = $id_dia;

			//Elementos
			foreach ($frm["IDServicioElemento"] as $IDServicioElemnto) :
				$array_servicio_elemento[] = $IDServicioElemnto;
			endforeach;
			if (count($array_servicio_elemento) > 0) :
				$ID_Servicio_Elemento = implode("|", $array_servicio_elemento) . "|";
			endif;
			$frm["IDServicioElemento"] = $ID_Servicio_Elemento;

			$id = $dbo->insert($frm, "ServicioDisponibilidad", "IDServicioDisponibilidad");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=disponibilidad&id=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaServicioDisponibilidad":
			$frm = SIMUtil::varsLOG($_POST);


			foreach ($frm["IDDia"] as $Dia_seleccion) :
				$array_dia[] = $Dia_seleccion;
			endforeach;

			if (count($array_dia) > 0) :
				$id_dia = implode("|", $array_dia) . "|";
			endif;
			$frm["IDDia"] = $id_dia;

			//Elementos
			foreach ($frm["IDServicioElemento"] as $IDServicioElemnto) :
				$array_servicio_elemento[] = $IDServicioElemnto;
			endforeach;
			if (count($array_servicio_elemento) > 0) :
				$ID_Servicio_Elemento = implode("|", $array_servicio_elemento) . "|";
			endif;
			$frm["IDServicioElemento"] = $ID_Servicio_Elemento;


			$dbo->update($frm, "ServicioDisponibilidad", "IDServicioDisponibilidad", $frm[IDServicioDisponibilidad]);

			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=disponibilidad&id=" . $frm[IDServicio]);
			exit;
			break;

		case "CopiarServicioDisponibilidad":

			$DatosDisponibilidad = $dbo->fetchAll("Disponibilidad", "IDDisponibilidad =" . $_GET[IDDisponibilidad] . " AND IDServicio = " . $_GET["ids"], "array");

			$SQLSD = "SELECT * FROM ServicioDisponibilidad WHERE IDDisponibilidad = $_GET[IDDisponibilidad] AND IDServicio = $_GET[ids]";
			$QRYSD = $dbo->query($SQLSD);

			$now = date("Y-m-d H:m:s");
			unset($DatosDisponibilidad["IDDisponibilidad"]);
			$DatosDisponibilidad["Nombre"] .= "-COPIA";
			$DatosDisponibilidad["FechaTrCr"] = $now;
			$DatosDisponibilidad["UsuarioTrCr"] = "COPIA";

			$disponibilidad = $dbo->insert($DatosDisponibilidad, "Disponibilidad", "IDDisponibilidad");

			while ($row = $dbo->fetchArray($QRYSD)) :

				unset($row["IDServicioDisponibilidad"]);
				$row["IDDisponibilidad"] = $disponibilidad;
				$row["Nombre"] .= "-COPIA";
				$row["FechaTrCr"] = $now;
				$row["UsuarioTrCr"] = "COPIA";

				$serviciodisponibilidad = $dbo->insert($row, "ServicioDisponibilidad", "IDServicioDisponibilidad");

			endwhile;

			SIMHTML::jsAlert("Copia Exitosa");
			SIMHTML::jsRedirect("serviciosclub.php?action=edit&tab=disponibilidad&ids=" . $_GET["ids"]);
			exit;
			break;

		case "EliminaServicioDisponibilidad":
			$frm = SIMUtil::varsLOG($_POST);
			$frm["UsuarioTrCr"] = SIMUser::get("Nombre");
			$sql_l="INSERT INTO Log ( IDUsuario , Fecha , Modulo , Transaccion , Operacion , DireccionIP,FechaTrCr )
			VALUES( '" . $frm["UsuarioTrCr"] . "' , NOW() , 'Disponibilidad','Delete','" . "DELETE FROM Disponibilidad WHERE IDDisponibilidad   = " . $_GET[IDDisponibilidad] . " LIMIT 1" . "','" . $IP . "',NOW())";
			$dbo->query( $sql_l );
			$id = $dbo->query("DELETE FROM Disponibilidad WHERE IDDisponibilidad   = '" . $_GET[IDDisponibilidad] . "' LIMIT 1");
			$id = $dbo->query("DELETE FROM ServicioDisponibilidad WHERE IDDisponibilidad   = '" . $_GET[IDDisponibilidad] . "'");
			SIMHTML::jsAlert("Eliminacion Exitosa");
			SIMHTML::jsRedirect("serviciosclub.php?action=edit&tab=disponibilidad&ids=" . $_GET["ids"]);
			exit;
			break;

		case "InsertarDisponibilidadElemento":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "ElementoDisponibilidad", "IDElementoDisponibilidad");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=elementos&id=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaDisponibilidadElemento":
			$frm = SIMUtil::varsLOG($_POST);

			$dbo->update($frm, "ElementoDisponibilidad", "IDElementoDisponibilidad", $frm[IDElementoDisponibilidad]);

			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=elementos&id=" . $frm[IDServicio]);
			exit;
			break;

		case "EliminaDisponibilidadElemento":
			$id = $dbo->query("DELETE FROM ElementoDisponibilidad WHERE IDElementoDisponibilidad   = '" . $_GET[IDElementoDisponibilidad] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=elementos&id=" . $_GET["id"]);
			exit;
			break;




		case "InsertarAuxiliar":

			$frm = SIMUtil::varsLOG($_POST);

			$files =  SIMFile::upload($_FILES["Foto"], ELEMENTOS_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["Foto"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
			$frm["Foto"] = $files[0]["innername"];


			$id = $dbo->insert($frm, "Auxiliar", "IDAuxiliar");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=auxiliares&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaAuxiliar":

			$frm = SIMUtil::varsLOG($_POST);

			$files =  SIMFile::upload($_FILES["Foto"], ELEMENTOS_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["Foto"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
			$frm["Foto"] = $files[0]["innername"];

			$dbo->update($frm, "Auxiliar", "IDAuxiliar", $frm[IDAuxiliar]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=auxiliares&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "EliminaAuxiliar":
			$id = $dbo->query("DELETE FROM Auxiliar WHERE IDAuxiliar   = '" . $_GET[IDAuxiliar] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=auxiliares&ids=" . $_GET["ids"]);
			exit;
			break;

		case "InsertarServicioTipoReserva":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "ServicioTipoReserva", "IDServicioTipoReserva");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=tiporeservas&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaServicioTipoReserva":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "ServicioTipoReserva", "IDServicioTipoReserva", $frm[IDServicioTipoReserva]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=tiporeservas&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "EliminaServicioTipoReserva":
			$id = $dbo->query("DELETE FROM ServicioTipoReserva WHERE IDServicioTipoReserva   = '" . $_GET[IDServicioTipoReserva] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=tiporeservas&ids=" . $_GET["ids"]);
			exit;
			break;

			//preguntas
		case "InsertarServicioCampo":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "ServicioCampo", "IDServicioCampo");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=preguntas&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaServicioCampo":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "ServicioCampo", "IDServicioCampo", $frm[IDServicioCampo]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=preguntas&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "EliminaServicioCampo":
			$id = $dbo->query("DELETE FROM ServicioCampo WHERE IDServicioCampo   = '" . $_GET[IDServicioCampo] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=preguntas&ids=" . $_GET["ids"]);
			exit;
			break;
			//Fin preguntas



		case "InsertarServicioCampo":
			$frm = SIMUtil::varsLOG($_POST);

			$id = $dbo->insert($frm, "ServicioCampo", "IDServicioCampo");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=campos&id=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaServicioCampo":
			$frm = SIMUtil::varsLOG($_POST);

			$dbo->update($frm, "ServicioCampo", "IDServicioCampo", $frm[IDServicioCampo]);

			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=campos&id=" . $frm[IDServicio]);
			exit;
			break;

		case "EliminaServicioCampo":
			$id = $dbo->query("DELETE FROM ServicioCampo WHERE IDServicioCampo   = '" . $_GET[IDServicioCampo] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=campos&id=" . $_GET["id"]);
			exit;
			break;


		case "InsertarServicioCierre":
			$frm = SIMUtil::varsLOG($_POST);

			//quito los dos punto del texto ya que lo utilizo para traer la razon de cierre(:)
			$frm["Descripcion"] = str_replace(":", " ", $frm["Descripcion"]);

			foreach ($frm["IDDia"] as $Dia_seleccion) :
				$array_dia[] = $Dia_seleccion;
			endforeach;

			if (count($array_dia) > 0) :
				$id_dia = implode("|", $array_dia) . "|";
			endif;
			$frm["Dias"] = $id_dia;

			//Elementos
			foreach ($frm["IDServicioElemento"] as $IDServicioElemnto) :
				$array_servicio_elemento[] = $IDServicioElemnto;					
			endforeach;
			if (count($array_servicio_elemento) > 0) :
				$ID_Servicio_Elemento = "|" . implode("|", $array_servicio_elemento) . "|";
			endif;
			$frm["IDServicioElemento"] = $ID_Servicio_Elemento;


			//verificarq ue no exista un cierre igual
			$IDCierre = $dbo->getFields("ServicioCierre", "IDServicioCierre", "FechaInicio = '" . $frm["FechaInicio"] . "' and  FechaFin ='" . $frm["FechaFin"] . "'
                                  and IDServicio = '" . $frm["IDServicio"] . "' and  HoraInicio = '" . $frm["HoraInicio"] . "' and Tee1='" . $frm["Tee1"] . "'
                                  and Tee10 = '" . $frm["Tee10"] . "' and Dias= '" . $frm["Dias"] . "' and IDServicioElemento = '" . $frm["IDServicioElemento"] . "' ");

			/*
    	$IDCierre=$dbo->getFields( "ServicioCierre" , "IDServicioCierre" , "FechaInicio = '".$frm["FechaInicio"]."' and  FechaFin ='".$frm["FechaFin"]."'
                                  and IDServicio = '".$frm["IDServicio"]."' and  HoraInicio = '".$frm["HoraInicio"]."' and HoraFin = '".$frm["HoraFin"]."' and Tee1='".$frm["Tee1"]."'
                                  and Tee10 = '".$frm["Tee10"]."' and Dias= '".$frm["Dias"]."' and IDServicioElemento = '".$frm["IDServicioElemento"]."' ");
    	*/
			if (!empty($IDCierre)) {
				SIMHTML::jsAlert("ATENCION: El cierre ya existe por favor verifique");
				SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechas&ids=" . $frm[IDServicio]);
			} else {
				$id = $dbo->insert($frm, "ServicioCierre", "IDServicioCierre");
				foreach ($array_servicio_elemento as $IDServicioElemnto) :					
					$inserta_cierre="INSERT INTO ServicioCierreElemento (IDServicioCierre, IDServicioElemento) VALUES ('".$id."', '".$IDServicioElemnto."') ";
					$dbo->query($inserta_cierre);		
				endforeach;
				SIMHTML::jsAlert("Registro Exitoso");
				SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechas&ids=" . $frm[IDServicio]);
			}
			exit;
			break;

		case "ModificaServicioCierre":
			$frm = SIMUtil::varsLOG($_POST);

			
			//quito los dos punto del texto ya que lo utilizo para traer la razon de cierre(:)
			$frm["Descripcion"] = str_replace(":", " ", $frm["Descripcion"]);

			foreach ($frm["IDDia"] as $Dia_seleccion) :
				$array_dia[] = $Dia_seleccion;
			endforeach;

			if (count($array_dia) > 0) :
				$id_dia = implode("|", $array_dia) . "|";
			else :
				$id_dia = "";
			endif;

			$frm["Dias"] = $id_dia;

			//Elementos
			$borra_cierre="DELETE FROM ServicioCierreElemento WHERE IDServicioCierre = '".$frm[IDServicioCierre]."' ";
			$dbo->query($borra_cierre);
			foreach ($frm["IDServicioElemento"] as $IDServicioElemnto) :
				$array_servicio_elemento[] = $IDServicioElemnto;
				$inserta_cierre="INSERT INTO ServicioCierreElemento (IDServicioCierre, IDServicioElemento) VALUES ('".$frm[IDServicioCierre]."', '".$IDServicioElemnto."') ";
				$dbo->query($inserta_cierre);			
			endforeach;
			if (count($array_servicio_elemento) > 0) :
				$ID_Servicio_Elemento = "|" . implode("|", $array_servicio_elemento) . "|";
			endif;
			$frm["IDServicioElemento"] = $ID_Servicio_Elemento;

			if ($frm["Tee1"] != "S")
				$frm["Tee1"] = "N";

			if ($frm["Tee10"] != "S")
				$frm["Tee10"] = "N";



			$dbo->update($frm, "ServicioCierre", "IDServicioCierre", $frm[IDServicioCierre]);

			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechas&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "BuscadorFecha":
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechas&ids=" . $_GET["ids"] . "&FechaBuscar=" . $_POST["FechaBusqueda"]);
			break;

		case "EliminaServicioCierre":
			$sql_borra_servicio_ele = "DELETE FROM ServicioCierreElemento WHERE IDServicioCierre   = '" . $_GET[IDServicioCierre] . "' LIMIT 1";
			$dbo->query($sql_borra_servicio_ele);
			$sql_borra = "DELETE FROM ServicioCierre WHERE IDServicioCierre   = '" . $_GET[IDServicioCierre] . "' LIMIT 1";
			SIMLog::insert(SIMUser::get("Nombre"), "ServicioCierre", "ServicioCierre", "delete",  $sql_borra);
			$id = $dbo->query($sql_borra);
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechas&ids=" . $_GET["ids"]);
			exit;
			break;


		case "EliminaSeleccionFecha":
			if (count($_POST["SeleccFechaCierre"]) > 0) :
				foreach ($_POST["SeleccFechaCierre"] as $id_cierre) :
					$id = $dbo->query("DELETE FROM ServicioCierre WHERE IDServicioCierre   = '" . $id_cierre . "' LIMIT 1");
				endforeach;
			endif;
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechas&ids=" . $_POST["ids"]);
			exit;
			break;

			// FECHAS DE CIERRE AUXILIARES

		case "InsertarAuxiliarCierre":
			$frm = SIMUtil::varsLOG($_POST);

			//quito los dos punto del texto ya que lo utilizo para traer la razon de cierre(:)
			$frm["Descripcion"] = str_replace(":", " ", $frm["Descripcion"]);

			foreach ($frm["IDDia"] as $Dia_seleccion) :
				$array_dia[] = $Dia_seleccion;
			endforeach;

			if (count($array_dia) > 0) :
				$id_dia = implode("|", $array_dia) . "|";
			endif;
			$frm["Dias"] = $id_dia;

			foreach ($frm["IDAuxiliar"] as $IDAuxiliar) :
				$array_servicio_elemento[] = $IDAuxiliar;
			endforeach;
			if (count($array_servicio_elemento) > 0) :
				$ID_Servicio_Elemento = "|" . implode("|", $array_servicio_elemento) . "|";
			endif;
			$frm["IDAuxiliar"] = $ID_Servicio_Elemento;


			//verificarq ue no exista un cierre igual
			$IDCierre = $dbo->getFields("AuxiliarCierre", "IDAuxiliarCierre", "FechaInicio = '" . $frm["FechaInicio"] . "' and  FechaFin ='" . $frm["FechaFin"] . "'
                                  and IDServicio = '" . $frm["IDServicio"] . "' and  HoraInicio = '" . $frm["HoraInicio"] . "' and Dias= '" . $frm["Dias"] . "'  and IDAuxiliar = '$frm[IDAuxiliar]'");

			if (!empty($IDCierre)) {
				SIMHTML::jsAlert("ATENCION: El cierre ya existe por favor verifique");
				SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechascierreauxiliares&ids=" . $frm[IDServicio]);
			} else {
				$id = $dbo->insert($frm, "AuxiliarCierre", "IDAuxiliarCierre");
				SIMHTML::jsAlert("Registro Exitoso");
				SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechascierreauxiliares&ids=" . $frm[IDServicio]);
			}
			exit;
			break;

		case "ModificaAuxiliarCierre":
			$frm = SIMUtil::varsLOG($_POST);

			//quito los dos punto del texto ya que lo utilizo para traer la razon de cierre(:)
			$frm["Descripcion"] = str_replace(":", " ", $frm["Descripcion"]);

			foreach ($frm["IDDia"] as $Dia_seleccion) :
				$array_dia[] = $Dia_seleccion;
			endforeach;

			if (count($array_dia) > 0) :
				$id_dia = implode("|", $array_dia) . "|";
			else :
				$id_dia = "";
			endif;

			$frm["Dias"] = $id_dia;

			foreach ($frm["IDAuxiliar"] as $IDAuxiliar) :
				$array_servicio_elemento[] = $IDAuxiliar;
			endforeach;
			if (count($array_servicio_elemento) > 0) :
				$ID_Servicio_Elemento = "|" . implode("|", $array_servicio_elemento) . "|";
			endif;
			$frm["IDAuxiliar"] = $ID_Servicio_Elemento;


			$dbo->update($frm, "AuxiliarCierre", "IDAuxiliarCierre", $frm[IDAuxiliarCierre]);

			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechascierreauxiliares&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "BuscadorFechaCierreAuxiliar":
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechascierreauxiliares&ids=" . $_GET["ids"] . "&FechaBuscar=" . $_POST["FechaBusqueda"]);
			break;

		case "EliminaAuxiliarCierre":
			$sql_borra = "DELETE FROM AuxiliarCierre WHERE IDAuxiliarCierre   = '" . $_GET[IDAuxiliarCierre] . "' LIMIT 1";
			SIMLog::insert(SIMUser::get("Nombre"), "AuxiliarCierre", "AuxiliarCierre", "delete",  $sql_borra);
			$id = $dbo->query($sql_borra);
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechascierreauxiliares&ids=" . $_GET["ids"]);
			exit;
			break;

		case "EliminaSeleccionFechaCierreAuxiliar":
			if (count($_POST["SeleccFechaCierre"]) > 0) :
				foreach ($_POST["SeleccFechaCierre"] as $id_cierre) :
					$id = $dbo->query("DELETE FROM AuxiliarCierre WHERE IDAuxiliarCierre   = '" . $id_cierre . "' LIMIT 1");
				endforeach;
			endif;
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=fechascierreauxiliares&ids=" . $_POST["ids"]);
			exit;
			break;


		case "InsertarServicioElemento":
			$frm = SIMUtil::varsLOG($_POST);

			$files =  SIMFile::upload($_FILES["Foto"], ELEMENTOS_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["Foto"]["name"]))
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
			$frm["Foto"] = $files[0]["innername"];


			$id = $dbo->insert($frm, "ServicioElemento", "IDServicioElemento");
			$ultimo_id = $dbo->lastID();

			//Actualizao las canchas asociadas
			if (count($frm["IDServicioElementoAsociado"]) > 0) :
				foreach ($frm["IDServicioElementoAsociado"] as $IDServicioElemento) :
					$sql_insert_asociado = "Insert Into ServicioElementoAsociado (IDServicioElementoPrincipal, IDServicioElementoSecundario) Values ('" . $ultimo_id . "','" . $IDServicioElemento . "')";
					$dbo->query($sql_insert_asociado);
				endforeach;
			endif;

			if (count($frm["ElementoTipoReserva"]) > 0) :
				foreach ($frm["ElementoTipoReserva"] as $id_tiporeserva => $tiporeserva) :
					// echo "Insert Into ServicioElementoTipoReserva (IDServicioElemento, IDServicioTipoReserva) Values('".$frm[IDServicioElemento]."','".$tiporeserva."')";
					$sql_inserta_mod = $dbo->query("Insert Into ServicioElementoTipoReserva (IDServicioElemento, IDServicioTipoReserva) Values('$ultimo_id','" . $tiporeserva . "')");
				endforeach;
			endif;


			$frm["IDServicioElemento"]	= $ultimo_id;
			if (!empty($frm["IDDia"]) && !empty($frm["HoraDesde"]) && !empty($frm["HoraHasta"])) :
				$id = $dbo->insert($frm, "ElementoDisponibilidad", "IDElementoDisponibilidad");
			endif;



			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=elementos&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaServicioElemento":
			$frm = SIMUtil::varsLOG($_POST);

			$files =  SIMFile::upload($_FILES["Foto"], ELEMENTOS_DIR, "IMAGE");
			if (empty($files) && !empty($_FILES["Foto"]["name"])) {
				SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
			}
			$frm["Foto"] = $files[0]["innername"];

			$dbo->update($frm, "ServicioElemento", "IDServicioElemento", $frm[IDServicioElemento]);

			if (!empty($frm["IDDia"]) && !empty($frm["HoraDesde"]) && !empty($frm["HoraHasta"])) :
				$id = $dbo->insert($frm, "ElementoDisponibilidad", "IDElementoDisponibilidad");
			endif;

			//Actualizo las modalidades
			$sql_borra_modalidad = $dbo->query("Delete From ServicioElementoModalidad Where IDServicioElemento = '" . $frm[IDServicioElemento] . "'");
			if (count($frm["ElementoModalidad"]) > 0) :
				foreach ($frm["ElementoModalidad"] as $id_modalidad => $modalidad) :
					$sql_inserta_mod = $dbo->query("Insert Into ServicioElementoModalidad (IDServicioElemento, IDTipoModalidadEsqui) Values('" . $frm[IDServicioElemento] . "','" . $modalidad . "')");
				endforeach;
			endif;

			//Actualizo los tipos de reserva asociados
			$sql_borra_modalidad = $dbo->query("Delete From ServicioElementoTipoReserva Where IDServicioElemento = '" . $frm[IDServicioElemento] . "'");
			if (count($frm["ElementoTipoReserva"]) > 0) :
				foreach ($frm["ElementoTipoReserva"] as $id_tiporeserva => $tiporeserva) :
					$sql_inserta_mod = $dbo->query("Insert Into ServicioElementoTipoReserva (IDServicioElemento, IDServicioTipoReserva) Values('" . $frm[IDServicioElemento] . "','" . $tiporeserva . "')");
				endforeach;
			endif;

			//Actualizao las canchas asociadas
			//Borro anterior
			// print_r($frm);
			// exit;
			$dbo->query("Delete From ServicioElementoAsociado Where IDServicioElementoPrincipal  = '" . $frm[IDServicioElemento] . "'");
			if (count($frm["IDServicioElementoAsociado"]) > 0) :
				foreach ($frm["IDServicioElementoAsociado"] as $IDServicioElemento) :
					$sql_insert_asociado = "INSERT INTO ServicioElementoAsociado (IDServicioElementoPrincipal, IDServicioElementoSecundario, HoraInicio, HoraFinal) Values ('$frm[IDServicioElemento]','$IDServicioElemento','" . $frm[HoraInicio][$IDServicioElemento] . "','" . $frm[HoraFinal][$IDServicioElemento] . "')";
					$dbo->query($sql_insert_asociado);
				endforeach;
			endif;
			

			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=elementos&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "EliminaServicioElemento":
			///Si tiene reservas no se deja eliminar
			$sql_reserva="SELECT IDReservaGeneral FROM ReservaGeneral Where IDServicioElemento = '".$_GET[IDServicioElemento]."' ";
			$r_reserva = $dbo->query($sql_reserva);			
			if($dbo->rows($r_reserva)>0){
				SIMHTML::jsAlert("No se puede eliminar este elemento ya tiene reservas asociadas");
				SIMHTML::jsRedirect($script . ".php?action=edit&tab=elementos&ids=" . $_GET["ids"]);
				
			}
			else{
				$sql_eli="DELETE FROM ServicioElemento WHERE IDServicioElemento   = '" . $_GET[IDServicioElemento] . "' LIMIT 1";
				$id = $dbo->query($sql_eli);
				SIMLog::insert(SIMUser::get("Nombre"), "ServicioElemento", "ServicioElemento", "delete",  $sql_eli);
				SIMHTML::jsAlert("Eliminacion Exitosa");
				SIMHTML::jsRedirect($script . ".php?action=edit&tab=elementos&ids=" . $_GET["ids"]);
			}
			
			exit;
			break;

		case "delfotoelemento":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$idelemento = $_GET['IDServicioElemento'];
			$filedelete = ELEMENTOS_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE ServicioElemento SET $campo = '' WHERE IDServicioElemento = $idelemento   LIMIT 1 ;");
			SIMHTML::jsAlert("Imagen Eliminada Correctamente");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=elementos&ids=" . $_GET["ids"]);
			break;

		case "delfotoauxiliar":
			$foto = $_GET['foto'];
			$campo = $_GET['campo'];
			$idauxiliar = $_GET['IDAuxiliar'];
			$filedelete = ELEMENTOS_DIR . $foto;
			unlink($filedelete);
			$dbo->query("UPDATE Auxiliar SET $campo = '' WHERE IDAuxiliar = $idauxiliar   LIMIT 1 ;");
			SIMHTML::jsAlert("Imagen Eliminada Correctamente");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=auxiliares&ids=" . $_GET["ids"]);
			break;

		case "InsertarServicioReserva":
			$frm = SIMUtil::varsLOG($_POST);

			$id = $dbo->insert($frm, "ReservaGeneral", "IDReservaGeneral");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&id=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaServicioReserva":
			$frm = SIMUtil::varsLOG($_POST);

			$dbo->update($frm, "ReservaGeneral", "IDReservaGeneral", $frm[IDReservaGeneral]);

			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&id=" . $frm[IDServicio]);
			exit;
			break;

		case "EliminaServicioReserva":
			$id = $dbo->query("DELETE FROM ReservaGeneral WHERE IDReservaGeneral   = '" . $_GET[IDReservaGeneral] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&id=" . $_GET["id"]);
			exit;
			break;

		case "EliminaInvitadoReserva":
			$id = $dbo->query("DELETE FROM ReservaGeneralInvitado WHERE IDReservaGeneralInvitado   = '" . $_GET[IDReservaGeneralInvitado] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&id=" . $_GET["id"]);
			exit;
			break;

		case "EliminaAuxiliarDisponibilidad":
			$idaux = $dbo->query("DELETE FROM AuxiliarDisponibilidadDetalle WHERE IDAuxiliarDisponibilidad   = '" . $_GET["IDAuxiliarDisponibilidad"] . "'");
			$id = $dbo->query("DELETE FROM AuxiliarDisponibilidad WHERE IDAuxiliarDisponibilidad   = '" . $_GET["IDAuxiliarDisponibilidad"] . "' LIMIT 1");

			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=" . $_GET["tab"] . "&ids=" . $_GET["ids"]);
			exit;
			break;

		case "EliminaCaddieDisponibilidad":
			$idaux = $dbo->query("DELETE FROM CaddieDisponibilidadDetalle WHERE IDCaddieDisponibilidad   = '" . $_GET["IDCaddieDisponibilidad"] . "'");
			$id = $dbo->query("DELETE FROM CaddieDisponibilidad WHERE IDCaddieDisponibilidad   = '" . $_GET["IDCaddieDisponibilidad"] . "' LIMIT 1");

			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=" . $_GET["tab"] . "&ids=" . $_GET["ids"]);
			exit;
			break;

		case "InsertarCategoriaCaddie2":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "CategoriaCaddie2", "IDCategoriaCaddie");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=categoriacaddie2&ids=" . $frm["IDServicio"]);
			exit;
			break;
		
		case "ModificaCategoriaCaddie2":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "CategoriaCaddie2", "IDCategoriaCaddie", $frm["IDCategoriaCaddie"]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=categoriacaddie2&ids=" . $frm["IDServicio"]);
			exit;
			break;
			
		case "EliminaCategoriaCaddie2":
			$id = $dbo->query("DELETE FROM CategoriaCaddie2 WHERE IDCategoriaCaddie   = '" . $_GET["IDCategoriaCaddie"] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=categoriacaddie2&ids=" . $_GET["ids"]);
			exit;
			break;
		case "InsertarCaddie2":
			/* print_r($_POST);
			exit; */
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "Caddie2", "IDCaddie");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=caddie&ids=" . $frm[IDServicio]);
			exit;
			break;


		case "ModificaCaddie2":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "Caddie2", "IDCaddie", $frm["IDCaddie"]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=caddie&ids=" . $frm["IDServicio"]);
			exit;
			break;
		case "EliminaCaddie2":
			$id = $dbo->query("DELETE FROM Caddie2 WHERE IDCaddie   = '" . $_GET["IDCaddie"] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=caddie&ids=" . $_GET["ids"]);
			exit;
			break;

			//porcentaje abono
		case "InsertarPorcentajeAbono":
			/* print_r($_POST);
			exit; */
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "PorcentajeAbono", "IDPorcentajeAbono");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=porcentajeabono&ids=" . $frm[IDServicio]);
			exit;
			break;


		case "ModificaPorcentajeAbono":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "PorcentajeAbono", "IDPorcentajeAbono", $frm["IDPorcentajeAbono"]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=porcentajeabono&ids=" . $frm["IDServicio"]);
			exit;
			break;
		case "EliminaPorcentajeAbono":
			$id = $dbo->query("DELETE FROM PorcentajeAbono WHERE IDPorcentajeAbono   = '" . $_GET["IDPorcentajeAbono"] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=porcentajeabono&ids=" . $_GET["ids"]);
			exit;
			break;

		case "InsertarObservacionesParaReservas":
			/* print_r($_POST);
			exit; */
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "ObservacionesParaReservas", "IDObservacionesParaReservas");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=ObservacionesParaReservas&ids=" . $frm[IDServicio]);
			exit;
			break;


		case "ModificaObservacionesParaReservas":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "ObservacionesParaReservas", "IDObservacionesParaReservas", $frm["IDObservacionesParaReservas"]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=ObservacionesParaReservas&ids=" . $frm["IDServicio"]);
			exit;
			break;

		case "EliminaObservacionesParaReservas":
			$id = $dbo->query("DELETE FROM ObservacionesParaReservas WHERE IDObservacionesParaReservas   = '" . $_GET["IDObservacionesParaReservas"] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=ObservacionesParaReservas&ids=" . $_GET["ids"]);
			exit;
			break;


			//categorias
		case "InsertarServicioPropiedad":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "ServicioPropiedad", "IDServicioPropiedad");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=categoriaserv&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaServicioPropiedad":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "ServicioPropiedad", "IDServicioPropiedad", $frm["IDServicioPropiedad"]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=categoriaserv&ids=" . $frm["IDServicio"]);
			exit;
			break;

		case "EliminaServicioPropiedad":
			$sqlAdicional = "SELECT COUNT(IDReservaGeneralAdicional) as num
							FROM ReservaGeneralAdicional as rga
								LEFT JOIN ReservaGeneral as rg ON rg.IDReservaGeneral = rga.IDReservaGeneral
							WHERE 
								rg.Fecha >= CURDATE() AND rg.IDEstadoReserva = 1 AND rga.IDServicioPropiedad = ".$_GET["IDServicioPropiedad"];
			
			$qryAdicional = $dbo->query($sqlAdicional);
			$rAdicional = $dbo->fetchArray($qryAdicional);

			$sqlAdicionalInvitado = "SELECT COUNT(IDReservaGeneralAdicionalInvitado) as num
							FROM ReservaGeneralAdicionalInvitado as rga
								LEFT JOIN ReservaGeneral as rg ON rg.IDReservaGeneral = rga.IDReservaGeneral
							WHERE 
								rg.Fecha >= CURDATE() AND rg.IDEstadoReserva = 1 AND rga.IDServicioPropiedad = ".$_GET["IDServicioPropiedad"];
			
			$qryAdicionalInvitado = $dbo->query($sqlAdicionalInvitado);
			$rAdicionalInvitado = $dbo->fetchArray($qryAdicionalInvitado);

			if($rAdicional['num'] > 0 || $rAdicionalInvitado['num'] > 0){
				SIMHTML::jsAlert("El elemento no se puede eliminar, tiene reservas asociadas");
			}
			else{
				$dbo->query("UPDATE ServicioPropiedad SET Publicar = 'N' WHERE IDServicioPropiedad = ".$_GET["IDServicioPropiedad"]);
				SIMHTML::jsAlert("Eliminacion Exitoso");
			}
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=categoriaserv&ids=" . $_GET["ids"]);
			exit;
		break;
			//Fin preguntas

			//Caracteristica
		case "InsertarServicioAdicional":
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "ServicioAdicional", "IDServicioAdicional");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=adicionales&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaServicioAdicional":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "ServicioAdicional", "IDServicioAdicional", $frm["IDServicioAdicional"]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=adicionales&ids=" . $frm["IDServicio"]);
			exit;
			break;

		case "EliminaServicioAdicional":
			$sqlAdicional = "SELECT COUNT(IDReservaGeneralAdicional) as num
						FROM ReservaGeneralAdicional as rga
							LEFT JOIN ReservaGeneral as rg ON rg.IDReservaGeneral = rga.IDReservaGeneral
						WHERE 
							rg.Fecha >= CURDATE() AND rg.IDEstadoReserva = 1 AND rga.IDServicioAdicional = ".$_GET["IDServicioAdicional"];
		
			$qryAdicional = $dbo->query($sqlAdicional);
			$rAdicional = $dbo->fetchArray($qryAdicional);

			$sqlAdicionalInvitado = "SELECT COUNT(IDReservaGeneralAdicionalInvitado) as num
							FROM ReservaGeneralAdicionalInvitado as rga
								LEFT JOIN ReservaGeneral as rg ON rg.IDReservaGeneral = rga.IDReservaGeneral
							WHERE 
								rg.Fecha >= CURDATE() AND rg.IDEstadoReserva = 1 AND rga.IDServicioAdicional = ".$_GET["IDServicioAdicional"];
			
			$qryAdicionalInvitado = $dbo->query($sqlAdicionalInvitado);
			$rAdicionalInvitado = $dbo->fetchArray($qryAdicionalInvitado);

			if($rAdicional['num'] > 0 || $rAdicionalInvitado['num'] > 0){
				SIMHTML::jsAlert("El elemento no se puede eliminar, tiene reservas asociadas");
			}
			else{
				$dbo->query("UPDATE ServicioAdicional SET Publicar = 'N' WHERE IDServicioAdicional = ".$_GET["IDServicioAdicional"]);
				SIMHTML::jsAlert("Eliminacion Exitoso");
			}
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=adicionales&ids=" . $_GET["ids"]);
			exit;
		break;
			//Fin caracteristica

			// VALORES POR ELEMENTO
		case "InsertarValorReservaElemento":

			$frm = SIMUtil::varsLOG($_POST);

			foreach ($frm["IDDia"] as $Dia_seleccion) :
				$array_dia[] = $Dia_seleccion;
			endforeach;

			if (count($array_dia) > 0) :
				$id_dia = implode("|", $array_dia) . "|";
			endif;
			$frm["Dias"] = $id_dia;

			//Elementos
			foreach ($frm["IDServicioElemento"] as $IDServicioElemnto) :
				$array_servicio_elemento[] = $IDServicioElemnto;
			endforeach;
			if (count($array_servicio_elemento) > 0) :
				$ID_Servicio_Elemento = "|" . implode("|", $array_servicio_elemento) . "|";
			endif;
			$frm["IDServicioElemento"] = $ID_Servicio_Elemento;

			$frm[UsuarioTrCr] = SIMUser::get("Nombre");

			$id = $dbo->insert($frm, "ValorReservaElemento", "IDValorReservaElemento");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=valorelemento&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "ModificaValorReservaElemento":

			$frm = SIMUtil::varsLOG($_POST);

			foreach ($frm["IDDia"] as $Dia_seleccion) :
				$array_dia[] = $Dia_seleccion;
			endforeach;

			if (count($array_dia) > 0) :
				$id_dia = implode("|", $array_dia) . "|";
			else :
				$id_dia = "";
			endif;

			$frm["Dias"] = $id_dia;

			//Elementos
			foreach ($frm["IDServicioElemento"] as $IDServicioElemnto) :
				$array_servicio_elemento[] = $IDServicioElemnto;
			endforeach;
			if (count($array_servicio_elemento) > 0) :
				$ID_Servicio_Elemento = "|" . implode("|", $array_servicio_elemento) . "|";
			endif;
			$frm["IDServicioElemento"] = $ID_Servicio_Elemento;

			$frm[UsuarioTrEd] = SIMUser::get("Nombre");

			$dbo->update($frm, "ValorReservaElemento", "IDValorReservaElemento", $frm["IDValorReservaElemento"]);
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=valorelemento&ids=" . $frm[IDServicio]);
			exit;
			break;

		case "EliminaValorReservaElemento":
			$id = $dbo->query("DELETE FROM ValorReservaElemento WHERE IDValorReservaElemento   = '" . $_GET["IDValorReservaElemento"] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=valorelemento&ids=" . $_GET["ids"]);
			exit;
			break;

		case "credibanconuevaversion":

			$frm = SIMUtil::varsLOG($_POST);				
			$dbo->query("DELETE FROM CredibancoNuevaVersionServicio WHERE IDServicio   = '$frm[IDServicio]'");
			$id = $dbo->insert($frm, "CredibancoNuevaVersionServicio", "IDCredibancoNuevaVersionServicio");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=credibandonuevaversion&ids=$frm[IDServicio]");
			exit;
		break;

		case "InsertarPreguntaInvitadosExternos":
			/* print_r($_POST);
			exit; */
			$frm = SIMUtil::varsLOG($_POST);
			$id = $dbo->insert($frm, "CampoInvitadoExterno", "IDCampoInvitadoExterno");
			SIMHTML::jsAlert("Registro Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=preguntasinvitados&ids=" . $frm[IDServicio]);
			exit;
			break;
		
		
		case "ModificaPreguntaInvitadosExternos":
			$frm = SIMUtil::varsLOG($_POST);
			$dbo->update($frm, "CampoInvitadoExterno", "IDCampoInvitadoExterno", $frm["IDCampoInvitadoExterno"]);
			SIMHTML::jsAlert("Modificacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=preguntasinvitados&ids=" . $frm["IDServicio"]);
			exit;
			break;
		
		case "EliminaPreguntaInvitadosExternos":
			$id = $dbo->query("DELETE FROM CampoInvitadoExterno WHERE IDCampoInvitadoExterno   = '" . $_GET["IDCampoInvitadoExterno"] . "' LIMIT 1");
			SIMHTML::jsAlert("Eliminacion Exitoso");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=preguntasinvitados&ids=" . $_GET["ids"]);
			exit;
			break;

		case "configuracionrepetirreserva":

			$frm = SIMUtil::varsLOG($_POST);
			$numSemanas = $dbo->getFields("ConfiguracionReservaHorario", "NumeroSemanas", "IDClub = ".SIMUser::get("club"));

			if($frm['SemanasSeguidasARepetir'] <= $numSemanas):

				if(!empty($frm["IDConfiguracionRepetirReserva"]) && $frm["IDConfiguracionRepetirReserva"] != "")
					$dbo->update($frm, "ConfiguracionRepetirReserva", "IDConfiguracionRepetirReserva", $frm["IDConfiguracionRepetirReserva"]);
				else
					$idConf = $dbo->insert($frm, "ConfiguracionRepetirReserva", "IDConfiguracionRepetirReserva");
			else:
				SIMHTML::jsAlert("El numero de semanas a repetir no puede ser mayor al numero de semanas que se muestran, por favor verifiquelo");
				SIMHTML::jsRedirect($script . ".php?action=edit&tab=configuracionrepetirreserva&ids=" . $frm["IDServicio"]);
			endif;

			SIMHTML::jsAlert("Guardado Exitosamente");
			SIMHTML::jsRedirect($script . ".php?action=edit&tab=configuracionrepetirreserva&ids=" . $frm["IDServicio"]);

			exit;
			break;
		

		case "search":
			$view = "views/" . $script . "/list.php";
			break;


		default:
			$frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
			$view = "views/" . $script . "/form.php";
			$newmode = "update";
			$titulo_accion = "Actualizar";
			break;
	} // End switch



	if (empty($view))
		$view = "views/" . $script . "/form.php";


	?>
