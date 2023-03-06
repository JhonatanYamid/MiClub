<?php

$_SESSION[IDClub] = SIMNet::reqInt("id");

//Encapsulando datos globales
SIMReg::setFromStructure( array(
					"title" => "CLUB: ".$dbo->getFields( "Club" , "Nombre" , "IDClub = '".SIMNet::reqInt("id")."'")." ", 
					"mod" => "Admin"
) );




?>
<table class="adminheading">
	<tr>
		<th><?php echo SIMReg::get( "title" )?></th>
	</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td>
			<table class=adminlist width=100% >
				<tr>
					<th class=title>
						ACCESOS
					</th>
				</tr>
                <tr>
					<td>
						<table class="tableadmin"  cellspacing="10">
                            <tr>
								<td>
									<p><img src="images/addusers.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Socio</span>
                                    <br><a href="?mod=Socio">Ver Socio</a>
                                    <br><a href="?mod=Socio&action=add">Nuevo Socio</a>
                                    </p>
                                </td>
                                 <td>
                                    <p><img src="images/user.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Banner</span>
                                    <br><a href="?mod=BannerApp">Ver Banner</a>
                                    <br><a href="?mod=BannerApp&action=add">Nuevo Banner</a>
                                    </p> 
                                </td>
                                <td>
									<p><img src="images/config.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Noticias</span>
                                    <br><a href="?mod=Noticia">Ver Noticias</a>
                                    <br><a href="?mod=Noticia&action=add">Nueva Noticia</a>
                                    </p> 
                                </td>
                            
                           
                            	<td>
                                	<p><img src="images/browser.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Eventos</span>
                                    <br><a href="?mod=Evento">Ver Evento</a>
                                    <br><a href="?mod=Evento&action=add">Nuevo Evento</a>
                                    </p>
                                </td>
                                
                                <tr>
								<td><p><img src="images/browser.png" alt="" border="0" /> </p>
								  <p><span style="font-weight:bold">Directorio</span>
                                  <br><a href="?mod=Directorio">Ver Registro</a>
                                    <br><a href="?mod=Directorio&action=add">Nuevo Registro</a>
                                  </p>
                                </td>
                                 <td> 
                                    <p><img src="images/browser.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Galeria</span>
                                    <br><a href="?mod=Galeria">Ver Galerias</a>
                                    <br><a href="?mod=Galeria&action=add">Nueva Galeria</a>
                                    </p>
                                    
                                </td>
                                
                                <td> 
                                    <p><img src="images/browser.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Documentos</span>
                                    <br><a href="?mod=Documento">Ver Documentos</a>
                                    <br><a href="?mod=Documento&action=add">Nuevo Documento</a>
                                    </p>
                                    
                                </td>
                                <td> 
                                    <p><img src="images/browser.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Contacto</span>
                                    <br><a href="?mod=Contacto">Ver Contacto</a>
                                    <br><a href="?mod=Contacto&action=add">Nuevo Contacto</a>
                                    </p>
                                    
                                </td>
                                </tr>
                                <tr>
                                  <td><p><img src="images/user.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Invitados</span> <br>
                                      <a href="?mod=SocioInvitado">Ver Invitados</a> <br>
                                      <a href="?mod=SocioInvitado&action=add">Nuevo Invitado</a> </p></td>
                                  <td><p><img src="images/user.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Restaurantes</span> <br>
                                      <a href="?mod=Restaurante">Ver Restaurantes</a> <br>
                                      <a href="?mod=Restaurante&action=add">Nuevo Restaurante</a> </p></td>
                                  <td><p><img src="images/browser.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Club</span> <br>
                                      <a href="?mod=Club&action=edit&id=<?=$_SESSION[IDClub]?>">Informacion</a> </p></td>
                                  <td><p><img src="images/user.png" border="0" /></p>
                                    <p><span style="font-weight:bold">Fechas Cierre Club</span> <br>
                                      <a href="?mod=ClubFechaCierre">Ver Fechas</a> <br>
                                  <a href="?mod=ClubFechaCierre&action=add">Nuevo Fecha</a></p></td>
                                </tr>
                                <tr>
                                  <td colspan="4"><p><span style="font-weight:bold">SERVICIOS</span></p></td>
                                </tr>
                                <tr>
                                
                                <?php
                            $qry = $dbo->all( "ServicioMaestro", "Publicar = 'S'  ORDER BY Nombre ASC " );	
                            while ( $r = $dbo->fetchArray( $qry ) ): 
								$contador_columna++;
							?>
                            	<td>
                                	<? if (!empty($r[Icono])):
											$icono = SERVICIO_ROOT.$r[Icono];
										else:
											$icono = "images/config.png";	
										endif;
										
									?>
                                	<p><img src="<?php echo $icono; ?>" border="0" /></p>
                                    <p><span style="font-weight:bold"><?php echo $r[Nombre]; ?></span>
                                    <?php 
										$idservicioclub = $dbo->getFields( "ServicioClub" , "IDServicioMaestro" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."' and Activo = 'S' and IDClub = '".SIMNet::reqInt("id")."' "); 
										if (empty($idservicioclub)):?>
                                    		<br><span style="background-color: #818181"><a class="activar_servicio" title="<?php echo SIMNet::reqInt("id"); ?>" rel="<?php echo $r["IDServicioMaestro"]; ?>" href="#">Activar</a></span>
                                        <?php else: ?>        
                                        	
                                        	<br><a href="?mod=Servicio&action=edit&id=<?php echo $r["IDServicioMaestro"]; ?>">Reservas</a>
                                            <br><a href="?mod=Servicio&action=edit&IDServicioMaestro=<?php echo $r["IDServicioMaestro"]; ?>">Configuraci&oacute;n</a>
                                            <br><span style="background-color: #818181"><a class="activar_servicio" title="<?php echo SIMNet::reqInt("id"); ?>" rel="<?php echo $r["IDServicioMaestro"]; ?>" href="#">Inactivar</a></span>
                                            
                                        <?php endif;?>    
                                    </p>
                                </td>
                                
                             <?php 
							 if($contador_columna==4):
							 	echo "</tr><tr>";
								$contador_columna=0;
							 endif;
							 
							 
							 endwhile; ?>   
                                
                                
                                
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>  
                        </table>
                        
					</td>
				</tr>
			</table>            
            <br>    
		</td>
	</tr>
</table>