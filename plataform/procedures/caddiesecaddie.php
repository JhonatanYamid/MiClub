<?

SIMReg::setFromStructure(array(
    "title" => "Caddies Ecaddie",
    "table" => "CaddiesEcaddie",
    "key" => "IDCaddiesEcaddie",
    "mod" => "Socio"
));


$script = "caddiesecaddie";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            $IDClub= SIMUser::get("club");
  
			
		 
				
	    $file = $_FILES['file']['tmp_name']; 
	    
	    if(!empty($file)):
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
                $Categoria = $sheet->getCell("B" . $row)->getValue();
                $Servicio = $sheet->getCell("C" . $row)->getValue();
                $Nombre = $sheet->getCell("D" . $row)->getValue();
                $Whatsapp = $sheet->getCell("E" . $row)->getValue();
                $Descripcion = $sheet->getCell("F" . $row)->getValue();
                $Valor = $sheet->getCell("G" . $row)->getValue();
                
                
                 $servicio_caddie = $dbo->query("SELECT * FROM ServiciosCaddie WHERE Nombre='$Servicio'  AND IDClub='$IDClub' LIMIT 1");
                    	        
		 $info_caddie = $dbo->fetchArray($servicio_caddie);
                 $Servicio= $info_caddie["IDServiciosCaddie"];
                 if(empty($Servicio)):
                 $Servicio=0;
                 endif;
                 
                 $categoria_caddie = $dbo->query("SELECT * FROM CategoriasEcaddie WHERE Nombre='$Categoria' AND IDClub='$IDClub'  AND Activa='1' LIMIT 1");
                    	        
		 $info_caddie_categoria = $dbo->fetchArray($categoria_caddie);
                 $Categoria= $info_caddie_categoria["IDCategoriasEcaddie"];
                  
                 if(empty($Categoria)):
                 $Categoria=0;
                 endif;
                 $Nombrecategoria= $info_caddie_categoria["Nombre"];
                 
                $query = $dbo->query("SELECT IDUsuario FROM Usuario WHERE NumeroDocumento='$NumeroDocumento' AND IDClub='$IDClub' LIMIT 1");
                    	        
		 $info = $dbo->fetchArray($query);
                 $ID= $info["IDUsuario"];
      

                if (!empty($ID)) { 
                
                
                $query_total = $dbo->query("SELECT COUNT(*) AS total FROM CaddiesEcaddie WHERE IDUsuario='$ID' AND IDServiciosCaddie='$Servicio'  AND IDCategoriasEcaddie='$Categoria' AND Nombre='$Nombre'");
                    	        
		 $info_total = $dbo->fetchArray($query_total);
                 $cantidad= $info_total["total"]; 
                 
                 if($cantidad==0): 
              //INSERTAMOS EL CADDIES
                 $sql_insertar = $dbo->query("Insert Into CaddiesEcaddie (IDClub,IDServiciosCaddie,IDUsuario,IDCategoriasEcaddie,Categoria,Nombre,Descripcion,Valor,NumeroCelular) Values ('$IDClub','$Servicio',' $ID','$Categoria','$Nombrecategoria','$Nombre','$Descripcion','$Valor','$Whatsapp' )");
  
                  endif;
                  $ID=0;
                 //  endif;
                  
                } else { 
                
                //INSERTAMOS EL USUARIO
                 $sql_insertar1 = $dbo->query("Insert Into Usuario (IDClub,NumeroDocumento,Nombre,Telefono,User,Password,Email,Autorizado,Activo) Values ('$IDClub','$NumeroDocumento',' $Nombre','$Whatsapp','$NumeroDocumento',sha1('" . $NumeroDocumento . "'),'$NumeroDocumento','S','S' )");
                 
                 $ID = $dbo->lastID();
                   
                $query_total = $dbo->query("SELECT COUNT(*) AS total FROM CaddiesEcaddie WHERE IDUsuario='$ID' AND IDServiciosCaddie='$Servicio'  AND IDCategoriasEcaddie='$Categoria' AND Nombre='$Nombre'");
                    	        
		 $info_total = $dbo->fetchArray($query_total);
                 $cantidad= $info_total["total"]; 
                 
                 if($cantidad==0): 
                //INSERTAMOS EL CADDIES
                $sql_insertar = $dbo->query("Insert Into CaddiesEcaddie (IDClub,IDServiciosCaddie,IDUsuario,IDCategoriasEcaddie,Categoria,Nombre,Descripcion,Valor,NumeroCelular) Values ('$IDClub','$Servicio',' $ID','$Categoria','$Nombrecategoria','$Nombre','$Descripcion','$Valor','$Whatsapp'  )");
                  $ID="";
                  
                  
                  endif;
                }

                $cont++;
 
              
            } // END for
            
           else:
           
            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);


            endif;
            
            
 
            SIMHTML::jsAlert("Registro Guardado Correctamente");
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

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
