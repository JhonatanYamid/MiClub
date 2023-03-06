<?
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	session_start();
//handler de sesion
$get = SIMUtil::makeSafe( $_GET );

if( !empty( $get[ "tipoRep" ] ) )
{
	$_SESSION["TipoRepDiagnostico"] = $get[ "tipoRep" ];

	echo "true";
}//end if


?>
