<?php

	require( "config.inc.php" );
	SIMUtil::cache();

	
	//datos a la medida del proyecto
	define( "DOCUMENT_ROOT" , URLROOT . "file/VerCuestinario/" );
	define( "DOCUMENT_DIR" , DIRROOT . "file/VerCuestinario/" );
	
	define( "DOCVER_ROOT" , URLROOT . "file/VersionCues/" );
	define( "DOCVER_DIR" , DIRROOT . "file/VersionCues/" );
	
	define( "DOCGUI_ROOT" , URLROOT . "file/GuiaCues/" );
	define( "DOCGUI_DIR" , DIRROOT . "file/GuiaCues/" );
	
	define( "DOCINF_ROOT" , URLROOT . "file/VersionInfor/" );
	define( "DOCINF_DIR" , DIRROOT . "file/VersionInfor/" );
	
	define( "DOCENCAM_ROOT" , URLROOT . "file/VersionEntCam/" );
	define( "DOCENCAM_DIR" , DIRROOT . "file/VersionEntCam/" );
	
	define( "DOCENPRO_ROOT" , URLROOT . "file/VersionEntPro/" );
	define( "DOCENPRO_DIR" , DIRROOT . "file/VersionEntPro/" );
	
	define( "DOCPRECLITY_ROOT" , URLROOT . "file/VersionPrecuanty/" );
	define( "DOCPRECLITY_DIR" , DIRROOT . "file/VersionPrecuanty/" );
	
	define( "DOCPRECULITY_ROOT" , URLROOT . "file/VersionPrecuality/" );
	define( "DOCPRECULITY_DIR" , DIRROOT . "file/VersionPrecuality/" );
	
	define( "DOCPROPRO_ROOT" , URLROOT . "file/VersionProveedores/" );
	define( "DOCPROPRO_DIR" , DIRROOT . "file/VersionProveedores/" );
	
	define( "FRONT_DIR" , DIRROOT . "../" ); 
	define( "FRONT_ROOT" , URLROOT . "../" );
	
	define( "BANNER_DIR" , FRONT_DIR . "file/banner" ); 
	define( "BANNER_ROOT" , FRONT_ROOT . "file/banner/" );
	
	define( "CONTENIDO_DIR" , FRONT_DIR . "file/contenido" ); 
	define( "CONTENIDO_ROOT" , FRONT_ROOT . "file/contenido/" );

	define( "GALERIA_DIR" , FRONT_DIR . "file/galeria" ); 
	define( "GALERIA_ROOT" , FRONT_ROOT . "file/galeria/" );
	
	//handler de sesion
	$simsession = new SIMSession( SESSION_LIMIT );
	
	if( !empty( $idleng ) )
	{
		$datos = $simsession->update( "Lenguaje" , $idleng );
	}else{
		//traemos lo datos de la session
		$datos = $simsession->verificar();
	}
	
	if( !is_object( $datos ) )
	{
		header( "location:login.php?msg=" . $datos );
		exit;
	}
	
		
	//encapsulamos los parammetros
	SIMUser::setFromStructure( $datos );
	
	
	
		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<title><?php echo APP_TITLE?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<link rel="stylesheet" href="css/estilos.css" type="text/css" />
	<link rel="stylesheet" href="css/menu.css" type="text/css" />
	<link rel="stylesheet" href="css/calendario.css" type="text/css" />
	
	<!--jQuery-->
	<!--<script type="text/javascript" src="jscript/jquery-1.2.6.js"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js" type="text/javascript"></script>
    <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	
	<!--Menu-->
	<script type="text/javascript" src="jscript/jquerycssmenu.js"></script>
	
	<!--Calendario-->
	<script type="text/javascript" src="jscript/calendar/date.js"></script>
	<script type="text/javascript" src="jscript/calendar/jquery.datePicker.js"></script>
    
    
	
	<!--Tree-->
	<script language="JavaScript" src="jscript/tree/tree.js?1p"></script>
	<script language="JavaScript" src="jscript/tree/tree_tpl.php?a=1"></script>
	
	<!--general-->
	<script type="text/javascript" src="jscript/sim.js"></script>
	<script type="text/javascript" src="jscript/common.js"></script>
	<script type="text/javascript" src="jscript/jquery.selectboxes.js"></script>
    
    <!--Cgoosen select multiple-->
    <link rel="stylesheet" href="jscript/choosen/chosen.css">
	
    
<!-- tree jquery -->
<script src="jscript/treeview/jquery.cookie.js" type="text/javascript"></script>
<script src="jscript/treeview/jquery.treeview.js" type="text/javascript"></script>
<link rel="stylesheet" href="jscript/treeview/jquery.treeview.css" />
    
        <script type="text/javascript" language="javascript" src="jscript/colorPicker.js"></script>
<link rel="stylesheet" href="css/colorPicker.css" type="text/css"></link>
    
<script src="http://jwpsrv.com/library/Ddbl2EJ2EeSwCSIACtqXBA.js"></script>
	
</head>
<body class="mainbody" >

	<table width="99%" border="0" cellspacing="0" cellpadding="1">
	  	<tr>
			<td valign="top" >
					
					<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
		  				<tr >
							<td class="menubackgr"  >
							
                            	<div class="superior">
                            		<strong><?php echo SIMUtil::tiempo( date( "Y-m-j" ) )?>.</strong> Bienvenido,  <strong><?php echo htmlentities( SIMUser::get( "Nombre" ) )?></strong> [ <a href="login.php?action=Salir">Salir</a> ]
									
                                    | <a href="?mod=Admin">Admin</a> | 
                            	</div>
                             	
								<?php include( "includes/menu.php" )?>
                            </td>
						</tr>
                        <tr >
							<td class="lastview">
                            	<strong>&Uacute;ltimo Visto:</strong> <?php echo $LAST?>
                            </td>
						</tr>
						
					</table>
                    
					<table width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
		  				<tr >
							<td valign="top" width="100%"  >
								<div class="content">
									<table width="100%" border="0">
										<tr>
											<?
											$mod = SIMUtil::makeSafe( $_GET["mod"] );
											$nivel = SIMUser::get( "Nivel" );
											
											if( $nivel <> "0" )
												$permiso = SIMUtil::verify( $mod, SIMUser::get( "IDUsuario" ) );
											else
												$permiso = 3;
		                                    	
											if( empty( $mod ) )
												$mod = "Admin";
												
												
                                            if( $mod <> "Admin" )
											{
											
                                            /*
                                            if( ($mod == "Club") || ($mod == "Socio") || ($mod == "BannerApp") || ($mod == "Seccion") || ($mod == "Noticia") || ($mod == "SeccionEvento") || ($mod == "Evento") || ($mod == "Directorio") || ($mod == "Galeria") || ($mod == "SeccionGaleria") || ($mod == "Documento") || ($mod == "Servicio") || ($mod == "Contacto") || ($mod == "Restaurante") )
		                                    	echo "<td style='width:170px;' valign='top'>";
		                                    else
		                                    	echo "<td class='shortcuts'>";
												
												if( $permiso > 1 )
													include( "shortcuts/".$mod.".php" );
													
											*/
											?>
		                                    </td>
                                            <?
                                            }//end if
											?>
		                                     <td class="contenido">
		                                     	<?php 
		                                     		if( $permiso > 1 ){
														include( $mod.".php" );
													}
													else{														
														include( "denegado.php" );
													}
												?>
		                                     </td>
	                                     </tr>
	                                </table>   
                                 </div>
							 </td>
						</tr>
					</table>
					
		</td>
			</tr>
			<tr >
				<td  bgcolor="#FFFFFF" >
					<table width="100%" border="0" cellspacing="0" cellpadding="0" height="37">
						<tr height="37">
						  <td class="bgBottom" height="37" align="center"><span class="siteBotLinks">
						  </span><span class="gen">&nbsp;</span>
                          	<span class="copyright">
                            	<?php echo date("Y"); ?> &copy; Todos los derechos reservados <a href="#" target="_blank" class="copyright">22cero2</a>
                               <br />
                                
                                Desarrollado por:  <a href="http://www.solucionesdeinternetymercadeo.com" target="_blank" class="copyright">22cero2</a>
                                <br />
                                <small>Powered By SIM Tools v.<?php echo VERSION?></small>
                            </span></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
<?php
	$dbo =& SIMDB::get();
	$dbo->close();
?>