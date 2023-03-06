<?
	include( "procedures/general.php" );
	include( "procedures/reserva.php" );
	include( "cmp/seo.php" );

  $datos_servicio = $dbo->fetchAll( "Servicio", " IDServicio= '" . $DetalleSorteo["IDServicio"] . "' ", "array" );
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
                    <?
						SIMNotify::each();
						?>
                    <div class="page-header">
                        <h1> Home <small>
                                <i class="ace-icon fa fa-angle-double-right"></i> <?=$array_clubes[ SIMUser::get("club") ]["Nombre"] ?> <i class="ace-icon fa fa-angle-double-right"></i> DETALLE RESERVA </small>
                        </h1>
                    </div><!-- /.page-header -->
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <form id="frmDeleteReservaSorteo" name="frmDeleteReservaSorteo" action="" method="post" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td>Fecha Creacion Reserva</td>
                                                <td><?php echo $detalle_reserva["FechaTrCr"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Creada por</td>
                                                <td><?php echo $DetalleSorteo["UsuarioTrCr"] . " - " . $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$DetalleSorteo["IDUsuarioReserva"]."'" ); ?> </td>
                                            </tr>                                             
                                                <td>Fecha / Hora</td>
                                                <td><?php echo $DetalleSorteo["Fecha"] . " " . $detalle_reserva["Hora"] ; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Club</td>
                                                <td><?php echo $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$DetalleSorteo["IDClub"]."'" ); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Socio</td>
                                                  <td><?php									   
                                                    echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$DetalleSorteo["IDSocio"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" );
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
                                                        <tr>
                                                            <td><?php echo $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '".$detalle_reserva["IDElemento"]."'" ); ?></td>                                                           
                                                            <td><?php echo $detalle_reserva["Hora"]; ?></td>
                                                            <td><?php echo $detalle_reserva["PosicionElemento"]; ?></td>
                                                        </tr>
                                                           
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Invitados</td>
                                                <td>
                                               
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
                                            <tr>
                                                <td colspan="2">Razon de la cancelacion de la reserva</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <textarea name="RazonCancelacion" id="RazonCancelacion" class="form-control" required></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" colspan="2">
                                                    <input type="hidden" name="action" id="action" value="delete_reserva">
                                                    <input type="hidden" name="UsuarioElimina" id="UsuarioElimina" value="<?php echo $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".SIMUser::get("IDUsuario")."'" ); ?>">
                                                    <input type="hidden" name="IDReservaSorteoElemento" id="IDReservaSorteoElemento" value="<?php echo $detalle_reserva["IDReservaSorteoElemento"]; ?>">
                                                    <input type="submit" name="elimina_reserva_sorteo" id="elimina_reserva_sorteo" value="Elimina Reserva">
                                                </td>
                                            </tr>
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
        <?
			include( "cmp/footer_scripts.php" );
			?>
        <?
				include("cmp/footer.php");
			?>
    </div><!-- /.main-container -->
</body>

</html>