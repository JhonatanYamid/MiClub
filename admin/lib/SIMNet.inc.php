<?php

class SIMNet
{
	function IP()
	{
		$ip = "";

		if( getenv( "HTTP_CLIENT_IP" ) )
			$ip = getenv( "HTTP_CLIENT_IP" );
		else
			if( getenv( "HTTP_X_FORWARDED_FOR" ) )
				$ip = getenv( "HTTP_X_FORWARDED_FOR" );
			else
				$ip = getenv( "REMOTE_ADDR" );
		return $ip;
	}

	function req( $variable , $int="" )
	{
		if( isset( $_POST[ $variable ] ) )
				return self::post( $variable );
		else
			if( isset( $_GET[ $variable ] ) )
				return self::get( $variable );
			else
				return false;
	}

	function reqInt( $variable )
	{
		return intVal( self::req( $variable ) );
	}

	function get( $variable )
	{
		if( isset( $_GET[ $variable ] ) )
			return self::clear( $_GET[ $variable ] );
		else
			return false;
	}

	function post( $variable )
	{
		if( isset( $_POST[ $variable ] ) )
			return self::clear( $_POST[ $variable ] );
		else
			return false;
	}

	function clear( $vardata )
	{

		$quickexpr = "&[A-Za-z]+\;";

		if( is_array( $vardata ) )
		{
			foreach( $vardata as $key => $value )
				$vardata[ $key ] = self::clear(  $value );
		}
		else
		{
			/*if( !ereg( $quickexpr , $vardata ) )
				$vardata = htmlentities( $vardata );*/

			if( !get_magic_quotes_gpc() )
				$vardata = addslashes( $vardata );
		}

		return $vardata;
	}

}
?>
