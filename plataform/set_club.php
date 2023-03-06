<?
include("procedures/general.php");
if( !empty( $_GET[ "id" ] ) )
{
	$_SESSION["club"] = SIMNet::get( "id" );
}//end if

$location = "socios.php";
	
header("Location: " . $location );
?>