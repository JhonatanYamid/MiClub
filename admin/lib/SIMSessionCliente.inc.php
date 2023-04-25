<?php
class SIMSessionCliente
{
	//tiempo limite de la sesion
	var $_limit;
	
	//constructor
	function SIMSessionCliente( $msgerror , $session_limit = 40 )
	{
		$this->_limit = $session_limit;	
	}
	
	function crear( $idusuario , $datos )
	{
		session_start(); 
		
		$fecha = date( "m-d-Y H:i:s" , time() );

		$id = md5( uniqid( $fecha ) );
		
		$campos = array( "IDSesion" => $id , "IDRegistro" => $idusuario , "Inicio" => $fecha , "Datos" => $datos );
		
		$dbo =& SIMDB::get();
		
		$guardarqry = $dbo->insert( $campos , "Sesion_Cliente" , "IDSessionCliente" );
		
		$_SESSION["SIM_SESION_CLIENTE"] = $id; 
		
		if( $_SESSION["SIM_SESION_CLIENTE"] )
			return true;
		return false;
	}

	function verificar()
	{
		session_start(); 
		
		$defaultdata = array( "flag" => false );
		
		$variable_session = $_SESSION["SIM_SESION_CLIENTE"];
	
		//Primero verificar que el cookie este activo
		if ( !$variable_session )
			return "NSA";//sesion no activa
		else
		{			
			$this->clean();
			

			
			$dbo =& SIMDB::get();
			$sessiondata = $dbo->getFields( "Sesion_Cliente" , "Datos" , "IDSesion = '" . $variable_session . "'" );
			
			if( !$sessiondata )
				return "XS";//expiro la sesion
			else
			{				
				$defaultdata = unserialize( stripslashes( $sessiondata ) );
				//Actualizo la sesio a la hora de la transaccion
				$dbo->query( "UPDATE Sesion_Cliente SET Inicio = CURRENT_TIMESTAMP WHERE IDSesion='" . $variable_session . "'" );
				return $defaultdata;
			}
		}
		
	}
	
	function clean()
	{
		$dbo =& SIMDB::get();		
		return $dbo->query( "DELETE FROM Sesion_Cliente WHERE DATEADD( minute," . $this->_limit . ", Inicio ) < CURRENT_TIMESTAMP" );		
	}
	
	function eliminar()
	{
		session_start(); 
		$variable_session = $_SESSION["SIM_SESION_CLIENTE"];
		session_destroy(); 
		$dbo =& SIMDB::get();
		$dbo->deleteById( "Sesion_Cliente" , "IDSesion" , $variable_session );		
	}
}

?>