<?php
	require( "config.inc.php" );
	SIMUtil::cache();
	$_POST = SIMUtil::makeSafe( $_POST );

	//handler de sesion
	$simsession = new SIMSession( SESSION_LIMIT );
	
	if(isset($_POST[ "action"]))
		$action = $_POST[ "action"];
	else
		$action = $_GET[ "action"];
	
	switch( $action )
	{

		case 'Iniciar':

			$login = SIMNet::post( "login" );//$_POST["login"];
			$clave = SIMNet::post( "clave" );//$_POST["clave"];
			
			$dbo =& SIMDB::get();
		$user_data = $dbo->fetchAll( "Usuario" , "User = '" . $login . "' AND Password = '" . sha1( $clave ) . "' AND Autorizado = 'S'" , "object" );	
						
			$simsession->clean();	
			
			if( $user_data )
			{	
				$usuariosave = addslashes( serialize( $user_data ) );
				
				if( $simsession->crear( $user_data->IDUsuario , $usuariosave ) )
				{
					header( "location:./?mod=Club" );
					exit;
				}			
			}
			else
			{
				header( "location:login.php?msg=LI" );//login incorrecto
				exit;
			}

		break;

		case 'Salir':
			$simsession->eliminar();
			header( "location:login.php?msg=EX" );//cierre correcto
			exit;
		break;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<title><?php echo APP_TITLE;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	
	<link rel="stylesheet" href="css/estilos.css" type="text/css" />	
	
	<script language="JavaScript" src="jscript/validaForm.js"></script>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic' rel='stylesheet' type='text/css'>
</head>
<body class="mainhomeinvit">
		
		<table width="100%" border="0" cellspacing="0" cellpadding="1" height="750">
			<tr height="750">
				<td valign="top" height="750">
					<table width="100%" border="0"  align="center"  cellpadding="0" cellspacing="0">
				  		<tr height="18">
							<td align="center" height="18">
                            	<div class="menubackgr"></div>
                            </td>
						</tr>
						<tr>
							<td align="center">
								<table class="login"  cellspacing=0 cellpadding=0 border=0 width="100%" align="center">
									
                                         <tr>
											<td >
                                                <img src="images/logoinvt.png" alt="consulta invitados" border="0" />
                                            </td>
											
										</tr>
                                        
                                        
                                        
								</table>
                                <br />
                                <form action="<?php echo $PHP_SELF?>" method="post" name="loginfrm" >  
                                <table class="loginfrmintv"  cellspacing=0 cellpadding=0 border=0 width="100%" align="center">
                                      
                                        <tr >
                                            <td class="menubackgrlow" align="left" height="18">
                                            <?php
	                                            $msg = $_GET["msg"];
	                                            
	                                            if( empty( $msg ) )
	                                                $msg = "Por favor, Ingrese su email y clave ";
	                                            else
	                                            	$msg = SIMResources::$session[ $msg ];
	                                                
	                                            echo $msg;
                                            ?>
                                            </td>
                                        </tr>
                                        <tr>
											<td align=right>Email</td>
                                        </tr>
                                        <tr>
											<td><input type=text size=25  id=Usuario name=login class=input /></td>
										</tr>
										<tr>
											<td align=right>Clave</td>
                                        </tr>
                                        <tr>
											<td><input type=password size=25  id=Clave name=clave class=input /></td>
										</tr>
										<tr>
											<td colspan="2" align=center><input type="hidden" value="<? echo $redirect?>" name="redirect" />
											<input class="submit" type="submit" name="action" value="Iniciar" /></td>
										</tr>
									
								</table>
								</form>
							</td>
						</tr>
					</table>
			  </td>
			</tr>
			<tr>
				<td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" height="37">
						<tr height="37">
						  <td class="bgBottom" height="37" align="center">
                          	<span class="siteBotLinks"></span>
                          	<span class="gen">&nbsp;</span>
                          	<span class="copyright">
                            	<?php echo date("Y"); ?> &copy; Todos los derechos reservados <a href="#" target="_blank" class="copyright">22cero2</a>
                                  
                                Desarrollado por:  <a href="http://www.solucionesdeinternetymercadeo.com" target="_blank" class="copyright">22cero2</a>
                                
                            </span>
                          </td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<p></p>
	</body>
</html>
<?php
	$dbo->close();
?>
