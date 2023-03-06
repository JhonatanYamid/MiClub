<?php

switch ($_POST["action"]) {

	case "insert":


		// respuesta vacía
		$response = null;

		
		


		$_POST["FechaTrCr"] = date("Y-m-d H:i:s");
		$_POST["FechaRegistro"] = date("Y-m-d H:i:s");

		if (!empty($_POST["Tipo"]) && !empty($_POST["NumeroDocumentoTitular"])) :
			//seguridad para cada campo del formulario
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = SIMUtil::antiinjection($valor);
			} //end for

			//$id = $dbo->insert($_POST, "ActualizacionArrayanes", "IDActualizacionArrayanes");
                         
			$cant= $_POST["TotalBenef"];
			
			//recibo los datos del titular
			$tipoid= $_POST["Tipo"];
			$numero= $_POST["NumeroDocumentoTitular"];
			 
			 
			 $validar_invitados= "SELECT COUNT(*) AS Total  from ParticipantesEvento where NumeroDocumento='$numero' and IDTipoDocumento='$tipoid'";
			 $result = $dbo->query($validar_invitados);
			
                         while ($consulta = mysqli_fetch_array($result)){
	  
	  
                         $total= $consulta['Total'];
                          
 
			  
	                 if($total==1) {
	                 
			SIMHTML::jsAlert("Ya se ha registrado antes, continue el proceso.");

                        SIMHTML::jsRedirect("participantes_continua.php?dato=$numero");
	                 
	                 }else{
	                 
	                 
			SIMHTML::jsAlert("Perfecto, participante nuevo.");

                        SIMHTML::jsRedirect("participantes_nuevo.php?dato=$numero");
	                 
	                 }
	                 

	                 
 						 
		}
		
		 
					
 
		
                        


			// Ahora creamos el cuerpo del mensaje
			 
			  
		else :
			SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Todoslosdatossonobligatorios,porfavorverifica', LANGSESSION));
			SIMHTML::jsRedirect("participantes.php");
			exit;
		endif;
		break;
}//end switch
