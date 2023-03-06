<?php
	include( "procedures/general.php" );
	include( "procedures/reserva.php" );
	include( "cmp/seo.php" );

	$datos_servicio = $dbo->fetchAll( "Servicio", " IDServicio= '" . $detalle_reserva["IDServicio"] . "' ", "array" );
	$id_servicio_maestro = $datos_servicio["IDServicioMaestro"];
?>
</head>

<body class="no-skin">
    <div class="main-container" id="main-container">
        <script type="text/javascript">
        try {
            ace.settings.check('main-container', 'fixed')
        } catch (e) {}
        </script>
        <div class="main-content">
            <div class="main-content-inner">
                <div class="page-content">
                    <?php
						SIMNotify::each();
						?>
                    <div class="page-header">
                        <h1> Home <small>
                                <i class="ace-icon fa fa-angle-double-right"></i> <?php echo $array_clubes[ SIMUser::get("club") ]["Nombre"] ?> <i class="ace-icon fa fa-angle-double-right"></i> DETALLE RESERVA </small>
                        </h1>
                    </div><!-- /.page-header -->
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="col-sm-12">                                       
                                    <form id="frmUpdateInvitado" name="frmUpdateInvitado" action="" method="post" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td>Fecha Creacion Reserva</td>
                                                <td><?php echo $detalle_reserva["FechaTrCr"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Creada por</td>
                                                <td><?php echo $detalle_reserva["UsuarioTrCr"] . " - " . $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$detalle_reserva["IDUsuarioReserva"]."'" ); ?> </td>
                                            </tr>                                             
                                                <td>Fecha</td>
                                                <td><?php echo $detalle_reserva["Fecha"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Club</td>
                                                <td><?php echo $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$detalle_reserva["IDClub"]."'" ); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Socio</td>
                                                <td><?php									   
										            echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" );
									  			    ?>
                                                </td>
                                            </tr> 
                                             <tr>
                                                <td>Servicio</td>
                                                <td><?php
											        $id_maestro=$datos_servicio["IDServicioMaestro"];

                                                    $nombre_servicio_personalizado = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDClub = '".SIMUser::get("club")."' and IDServicioMaestro = '" . $id_maestro . "'" );
                                                    if(empty($nombre_servicio_personalizado))
                                                        $nombre_servicio_personalizado =$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$id_maestro."'" );

                                                    echo $nombre_servicio_personalizado;
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Elementos</td>
                                                <td>
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <td>Elemento</td>                                                           
                                                            <td>Hora</td>
                                                            <td>Posicion</td>
                                                        </tr>
                                                        <?php

                                                        foreach($array_elementos as $id => $Elemento):
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '".$Elemento["IDElemento"]."'" ); ?></td>                                                           
                                                                <td><?php echo $Elemento["Hora"]; ?></td>
                                                                <td><?php echo $Elemento["PosicionElemento"]; ?></td>
                                                            </tr>
                                                            <?php
                                                        endforeach;
                                                        ?>
                                                    </table>
                                                </td>
                                            </tr>								
                                          
                                             
									        <?php
                                            $invitados="S";
                                            ?> 
                                            <tr>
                                                <td>Invitados</td>
                                                <td> <!-- <?php

                                                $permiso_escritura = $dbo->getFields( "Usuario" , "Permiso" , "IDUsuario = '" . SIMUser::get( "IDUsuario" ) . "'");
                                                if($id_servicio_maestro>0 && $permiso_escritura == 'E'):?>
                                                    <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-socios" title="número de derecho">
                                                    <br><a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
                                                    <br> 
                                                <?php endif; ?>  -->
                                                <select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8" multiple> <?php
                                                $item=1;
                                                foreach($array_invitados as $id_invitado => $datos_invitado):
                                                        $item--;
                                                        if($datos_invitado["IDSocio"]>0):
                                                            $nombre_socio = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ));
                                                            
                                                        ?> <option value="<?php echo "socio-".$datos_invitado["IDSocio"]; ?>"><?php echo $nombre_socio; ?> / SOCIO CLUB</option> <?php
                                                        else: ?> <option value="<?php echo "externo-".$datos_invitado["Nombre"]; ?>"><?php echo $datos_invitado["Nombre"];  ?> / INVITADO EXTERNO</option> <?php
                                                        endif;
                                                endforeach;
                                                ?> </select>
                                                        <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">
                                                    </td>
                                            </tr>
                                            <?php									       
                                            if($invitados=="S" || $id_servicio_maestro>0 || (SIMUser::get("PermiteCambiarReserva")=="S" || SIMUser::get("IDPerfil")==0)): //15 = Golf	 ?> 
                                                <tr>
                                                    <td align="center" colspan="2">
                                                        <input type="hidden" name="action" id="action" value="updateinvitado">
                                                        <input type="hidden" name="IDReservaGeneral" id="IDReservaGeneral" value="<?php echo $detalle_reserva["IDReservaGeneral"]; ?>">
                                                        <input type="hidden" name="IDSocioOrig" id="IDSocioOrig" value="<?php echo $detalle_reserva["IDSocio"]; ?>">
                                                        <!-- <input type="submit" name="actualiza_participante" id="actualiza_participante" value="Actualizar Datos"> -->
                                                    </td>
                                                </tr> 
                                            <?php endif; ?>
                                        </table>
                                    </form>                                    
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                            <!-- PAGE CONTENT ENDS -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->
        <?php
			include( "cmp/footer_scripts.php" );		
            include("cmp/footer.php");
        ?>
    </div><!-- /.main-container -->
</body>

</html>