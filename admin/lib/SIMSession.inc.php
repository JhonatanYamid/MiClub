<?php
class SIMSession
{
	//tiempo limite de la sesion
	var $_limit;

	//constructor
	function __construct( $msgerror , $session_limit = 180 )
	{
		$this->_limit = $session_limit;
	}

	function crear( $idusuario , $datos )
	{
		$fecha = date( "Y-m-d H-i-s" , time() );

		$id = md5( uniqid( $fecha ) );

		$campos = array( "IDSesion" => $id , "IDUsuario" => $idusuario , "Inicio" => $fecha , "Datos" => $datos );

		$dbo =& SIMDB::get();
		$guardarqry = $dbo->insert( $campos , "Sesion" , "IDSession" );

		if( setcookie( "SIM_SESION" , $id ) )
			return true;
		return false;
	}

	function verificar()
	{
		$defaultdata = array( "flag" => false );


		$cookie_session = $_COOKIE[ "SIM_SESION" ];

		//Primero verificar que el cookie este activo
		if ( !$cookie_session )
			return "NSA";//sesion no activa
		else
		{
			$this->clean();

			$dbo =& SIMDB::get();
			$sessiondata = $dbo->getFields( "Sesion" , "Datos" , "IDSesion = '" . $cookie_session . "'" );

			if( !$sessiondata )
				return "XS";//expiro la sesion
			else
			{
				$defaultdata = unserialize( stripslashes( $sessiondata ) );

				//Actualizo la sesio a la hora de la transaccion
				$dbo->query( "UPDATE Sesion SET Inicio = NOW() WHERE IDSesion='" . $cookie_session . "'" );

				return $defaultdata;
			}
		}

	}

	function update($name, $value){
		$defaultdata = array( "flag" => false );

		$cookie_session = $_COOKIE[ "SIM_SESION" ];


		//Primero verificar que el cookie este activo
		if ( !$cookie_session )
			return "NSA";//sesion no activa
		else
		{
			$this->clean();

			$dbo =& SIMDB::get();
			$sessiondata = $dbo->getFields( "Sesion" , "Datos" , "IDSesion = '" . $cookie_session . "'" );

			if( !$sessiondata )
				return "XS";//expiro la sesion
			else
			{
				$defaultdata = unserialize( stripslashes( $sessiondata ) );

				$defaultdata->$name = $value;

				$data = addslashes( serialize( $defaultdata ) );
				//Actualizo la sesio a la hora de la transaccion
				$dbo->query( "UPDATE Sesion SET Inicio = NOW() , Datos = '$data' WHERE IDSesion='" . $cookie_session . "'" );

				return $defaultdata;
			}
		}
	}

	function clean()
	{
		$dbo =& SIMDB::get();
		return $dbo->query( "DELETE FROM Sesion WHERE DATE_ADD( Inicio, INTERVAL " . $this->_limit . " MINUTE ) < NOW()" );
	}

	function eliminar()
	{
		$cookie_session = $_COOKIE[ "SIM_SESION" ];
		unset( $_COOKIE[ "SIM_SESION" ] );

		$dbo =& SIMDB::get();

		return $dbo->deleteById( "Sesion" , "IDSesion" , $cookie_session );
	}

	/********** SESSION CLIENTE *************/
	function crear_cliente( $idusuario , $datos )
	{
		$fecha = date( "Y-m-d H-i-s" , time() );

		$id = md5( uniqid( $fecha ) );

		$campos = array( "IDSesion" => $id , "IDUsuario" => $idusuario , "Inicio" => $fecha , "Datos" => $datos );

		$dbo =& SIMDB::get();

		$guardarqry = $dbo->insert( $campos , "Sesion_Cliente" , "IDSession" );

		if( setcookie( "SIM_SESION_CLIENTE" , $id ) )
			return true;
		return false;
	}

	function verificar_cliente()
	{
		$defaultdata = array( "flag" => false );

		$cookie_session = $_COOKIE[ "SIM_SESION_CLIENTE" ];


		//Primero verificar que el cookie este activo
		if ( !$cookie_session  )
		{
			return "NHC";//No hay cookie
		}//end if
		else
		{
			$this->clean_cliente();

			$dbo =& SIMDB::get();
			$sessiondata = $dbo->getFields( "Sesion_Cliente" , "Datos" , "IDSesion = '" . $cookie_session . "'" );


			if( !$sessiondata )
			{
				return "XS";//return "XS";//expiro la sesion
			}//end if
			else
			{
				$defaultdata = unserialize( stripslashes( $sessiondata ) );
				//Actualizo la sesio a la hora de la transaccion
				$dbo->query( "UPDATE Sesion_Cliente SET Inicio = NOW() WHERE IDSesion='" . $cookie_session . "'" );

				//print_r( $sessiondata );

				return $defaultdata;
			}
		}

	}

	function clean_cliente()
	{
		$dbo =& SIMDB::get();
		return $dbo->query( "DELETE FROM Sesion_Cliente WHERE DATE_ADD( Inicio, INTERVAL " . $this->_limit . " MINUTE ) < NOW()" );
	}

	function eliminar_cliente()
	{
		$cookie_session = $_COOKIE[ "SIM_SESION_CLIENTE" ];

		setcookie( "SIM_SESION_CLIENTE" );
		unset( $_COOKIE[ "SIM_SESION_CLIENTE" ] );

		$dbo =& SIMDB::get();

		return $dbo->deleteById( "Sesion_Cliente" , "IDSesion" , $cookie_session );
	}

	/********** SESSION WEB *************/
	public static function crear_web( $idusuario , $datos,$domain )
	{
		$fecha = date( "Y-m-d H-i-s" , time() );

		$id = md5( uniqid( $fecha ) );

		$campos = array( "IDSesion" => $id ,
				"IDUsuario" => $idusuario ,
				"Inicio" => $fecha ,
				"Datos" => $datos,
				"UsuarioTrCr" =>"IDSocio".$idusuario );

		$dbo =& SIMDB::get();

		$guardarqry = $dbo->insert( $campos , "Sesion_Web" , "IDSession" );

		if( setcookie( "SIM_SESION_WEB",$id,time()+3600,"/",$domain ) ){
			$_COOKIE["SIM_SESION_WEB"] = $id;
			return true;
		}
		
		return false;
	}

	function verificar_web()
	{
		$defaultdata = array( "flag" => false );

		//Primero verificar que el cookie este activo
		if ( empty($_COOKIE["SIM_SESION_WEB"] ) )
		{
			return "NHC";//No hay cookie
		}//end if
		else
		{
			$this->clean_web();

			$dbo =& SIMDB::get();
			$sessiondata = $dbo->getFields( "Sesion_Web" , "Datos" , "IDSesion = '" . $_COOKIE["SIM_SESION_WEB"] . "'" );


			if( !$sessiondata )
			{
				return "XS";//return "XS";//expiro la sesion
			}//end if
			else
			{
				$defaultdata = unserialize( stripslashes( $sessiondata ) );
				//Actualizo la sesio a la hora de la transaccion
				$dbo->query( "UPDATE Sesion_Web SET Inicio = NOW() WHERE IDSesion='" . $_COOKIE["SIM_SESION_WEB"] . "'" );

				//print_r( $sessiondata );

				return $defaultdata;
			}
		}

	}

	function clean_web()
	{
		$dbo =& SIMDB::get();
		return $dbo->query( "DELETE FROM Sesion_Web WHERE DATE_ADD( Inicio, INTERVAL " . $this->_limit . " MINUTE ) < NOW()" );
	}

	function eliminar_web()
	{
		$cookie_session = $_COOKIE[ "SIM_SESION_WEB" ];

		setcookie( "SIM_SESION_WEB" );
		unset( $_COOKIE[ "SIM_SESION_WEB" ] );

		$dbo =& SIMDB::get();

		return $dbo->deleteById( "Sesion_Web" , "IDSesion" , $cookie_session );
	}
}
