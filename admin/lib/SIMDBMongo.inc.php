<?php

class SIMDBMongo
{
	static $instancemongo = null;
	static $conexionmongo = null;

	public static function &get()
	{
		if( !self::$instancemongo )
		{
			self::$instancemongo = new SIMDBMongo();
			return self::$instancemongo;
		}
		else
			return self::$instancemongo;
	}

	function getConection()
	{
		return self::$conexionmongo;
	}

	function connect( $dbhost , $dbname , $dbuser , $dbpass )
	{

		if( $this == self::$instancemongo )
		{
			$uri = 'mongodb://'.$dbuser.':'.$dbpass.'@'.$dbhost.'/'.$dbname;
			$conexionmongo = new MongoDB\Driver\Manager($uri); // conectar
			if(!$conexionmongo->getServers()){
				//echo "Error: Fallo al conectarse a MongoDB \n";
				//exit;
			}
			self::$conexionmongo=$conexionmongo;
			return self::$conexionmongo;
		}
		else
			return false;
	}

	function insert( $datos, $Coleccion )	{
		$conexionmongo=self::$conexionmongo;
		if( $this == self::$instancemongo ){
			$bulk = new MongoDB\Driver\BulkWrite;
			$bulk->insert($datos);
			$conexionmongo->executeBulkWrite(DBNAMEMongo.'.'.$Coleccion, $bulk);
		}
		else
			return false;
	}	

}
?>
