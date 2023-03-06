<?php
//Encapsulando datos globales
SIMReg::setFromStructure( array(
					"title" => "Administraci&oacute;n del Sistema",
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
						Parametros
					</th>
				</tr>
                <tr>
					<td>
						<table class="tableadmin"  cellspacing="10">
                            <tr>
                            	<td>
                                	<img src="images/user.png" border="0" />
                                    <p><a href="?mod=Usuario">Usuarios</a></p>
                                </td>
								<td>
									<img src="images/addusers.png" border="0" />
                                    <p><a href="?mod=Usuario&amp;action=add">Agregar Usuarios</a></p>
                                </td>
                                 <td>
                                    <img src="images/user.png" border="0" />
                                    <p><a href="?mod=Perfil">Perfiles</a></p> 
                                </td>
                                <td>
									<img src="images/config.png" border="0" />
                                    <p><a href="?mod=Parametros">Par&aacute;metros</a></p> 
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                	<img src="images/browser.png" border="0" />
                                    <p><a href="?mod=Ciudad">Ciudad</a></p>
                                </td>
								<td><p><img src="images/browser.png" alt="" border="0" /> </p>
								  <p><a href="?mod=Pais">Pais</a></p>
                                </td>
                                 <td> 
                                    <img src="images/browser.png" border="0" />
                                    <p><a href="?mod=TipoDocumento">Tipo Documento</a><a href="?mod=CategoriaVideo"></a></p>
                                    
                                </td>
                                <td><p>&nbsp;</p>
									 
                                </td>
                            </tr>  
                        </table>
					</td>
				</tr>
			</table>            
            <br>    
		</td>
	</tr>
</table>