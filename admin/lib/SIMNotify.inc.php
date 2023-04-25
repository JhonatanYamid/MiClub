<?php
class SIMNotify
{
	
	static $stack = array();
	
	function capture( $msg , $type , $id = 0 )
	{
		if( $msg )
		{
			if( $id )
				self::$stack[ $id ] = array( "type" => $type , "msg" => $msg );
			else
				self::$stack[] = array( "type" => $type , "msg" => $msg );
				
			return true;
		}
		else
			return false;
	}
	
	function thrower( $id = 0 , $return = false , $clean = true )
	{
		if( $id )
			$var = self::$stack[$id];
		else
			$var = array_shift( self::$stack );
		
		if( $clean ) unset( self::$stack[ $id ] );
		
		if( $return )			
			return $var;
		else
		{
			echo SIMHTML::message( $var["msg"] , $var["type"] );
			return true;
		}
	}
	
	function each( $clean = true )
	{
		foreach( self::$stack as $id => $data )
			self::thrower( $id );
		return true;
	}
	
	function clean()
	{
		self::$stack = array();
		return true;
	}
}
?>
