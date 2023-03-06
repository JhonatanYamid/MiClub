<?
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
                                <div class="col-sm-12"> <?php if($_GET["tipo"]!="horario"){ ?> <form id="frmUpdateInvitado" name="frmUpdateInvitado" action="" method="post" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td>Fecha Creacion Reserva</td>
                                                <td><?php echo $detalle_reserva["FechaTrCr"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Creada por</td>
                                                <td><?php echo $detalle_reserva["UsuarioTrCr"] . " - " . $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$detalle_reserva["IDUsuarioReserva"]."'" ); ?> </td>
                                            </tr>
                                            <tr>
                                                <td>Numero Reserva</td>
                                                <td><?php echo $detalle_reserva["IDReservaGeneral"]; ?></td>
                                            </tr> <?php
                                            if(SIMUser::get("club") == 106)
                                            {
                                                if($detalle_reserva["IDTipoPago"] == 1)
                                                {    $valor = $dbo->getFields("ZonaPagosPagos", "ValorPagado", "IDReserva= '".$detalle_reserva["IDReservaGeneral"]."'");
                                                    $id = $dbo->getFields("ZonaPagosPagos", "IDZonaPagosPagos", "IDReserva= '".$detalle_reserva["IDReservaGeneral"]."'");
                                                    $estado = $dbo->getFields("ZonaPagosPagos", "EstadoPago", "IDReserva= '".$detalle_reserva["IDReservaGeneral"]."'");
                                                    ?> <tr>
                                                <td>Valor Transaccion</td>
                                                <td><?php echo $valor;?></td>
                                            </tr>
                                            <tr>
                                                <td>Identificador Transacción</td>
                                                <td><?php echo $id;?></td>
                                            </tr>
                                            <tr>
                                                <td>Estado Transaccion</td>
                                                <td><?php
                                                    if($estado == "1")
                                                        echo "APROBADA";
                                                    elseif($estado == "1000" || $estado == "1001" || $estado == "1002" || $estado == "4003" || $estado == "4000")
                                                        echo "RECHAZADA";
                                                    elseif($estado == "4001" || $estado == "999" || $estado == "888" )
                                                        echo "PENDIENTE";
                                                    else
                                                        echo "OTRO";
                                                    ?> </td>
                                            </tr> <?php
                                                }
                                            }
                                            // INFO DE PAGO PARA RESERVAS DE SOCCER CLUB
                                            if(SIMUser::get("club") == 190):
                                                $datosPago = $dbo->fetchAll("PagosEpayco","IDReserva = $detalle_reserva[IDReservaGeneral]");
                                                if($detalle_reserva[IDPorcentajeAbono] > 0):
                                                    $datosAbono = $dbo->fetchAll("PorcentajeAbono", "IDPorcentajeAbono = $detalle_reserva[IDPorcentajeAbono]");
                                                    $Abono = $detalle_reserva[ValorPagado] * $datosAbono[Porcentaje] / 100;
                                                    $Saldo = $detalle_reserva[ValorPagado] - $Abono;
                                                else:
                                                    $Abono = "NO SE PAGO CON ABONO";
                                                    $Saldo = "NO HAY SALDO PENDIENTE";
                                                endif;                                                
                                               ?> <tr>
                                                <td>Valor completo de la cancha</td>
                                                <td><?php echo $detalle_reserva[ValorPagado] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Valor del abono pagado</td>
                                                <td><?php echo $Abono; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Comprobante de la transacción</td>
                                                <td><?php echo $datosPago[transactionID]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Valor del saldo a pagar</td>
                                                <td><?php echo $Saldo; ?></td>
                                            </tr> <?php
                                            endif;
                                     ?> <?php if(!empty($datos_servicio["IdentificadorServicio"])){ ?> <tr>
                                                <td>Consecutivo Reserva</td>
                                                <td><?php
																				$otros_datos_reserva = " " . $detalle_reserva[ "IdentificadorServicio" ]."-".$detalle_reserva[ "ConsecutivoServicio" ];
																				echo $otros_datos_reserva; ?></td>
                                            </tr> <?php } ?> <tr>
                                                <td>Fecha / Hora Reserva</td>
                                                <td><?php echo $detalle_reserva["Fecha"] . " " .  $detalle_reserva["Hora"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Club</td>
                                                <td><?php echo $dbo->getFields( "Club" , "Nombre" , "IDClub = '".$detalle_reserva["IDClub"]."'" ); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Socio</td>
                                                <td><?php
                                                    if(SIMUser::get("PermiteCambiarReserva")=="S" || SIMUser::get("IDPerfil")==0):
                                                        $sql_socio_club = "Select * From Socio Where IDSocio = '".$detalle_reserva["IDSocio"]."'";
                                                        $qry_socio_club = $dbo->query($sql_socio_club);
                                                        $r_socio = $dbo->fetchArray($qry_socio_club); ?> 
                                                        <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho, nombre, apellido" value="<?php echo utf8_encode($r_socio["Apellido"] . " " .$r_socio["Nombre"]) ?>">
                                                        <input type="hidden" name="IDSocio" value="<?php echo $detalle_reserva["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio"> <?php
                                                    else:
                                                        echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" );
                                                    endif;
									                    ?>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>Telefono Socio</td>
                                                <td><?php
                                                    if(SIMUser::get("PermiteCambiarReserva")=="S" || SIMUser::get("IDPerfil")==0):
                                                        $sql_socio_club = "Select * From Socio Where IDSocio = '".$detalle_reserva["IDSocio"]."'";
                                                        $qry_socio_club = $dbo->query($sql_socio_club);
                                                        $r_socio = $dbo->fetchArray($qry_socio_club); ?> 
                                                        <input type="text" id="Celular" name="Celular" placeholder="Telefono Socio" class="col-xs-12 " title="" value="<?php echo $r_socio[Celular]; ?>">
                                                        
                                                        <?php
                                                    else:
                                                        echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" );
                                                    endif;
									                    ?>
                                                </td>
                                            </tr> 
                                            <?php
                                            if($detalle_reserva["IDClub"]==37): //Para el polo club muestro handicap?> <tr>
                                                <td>Handicap</td>
                                                <td><?php echo $dbo->getFields( "Socio" , "Handicap" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ); ?></td>
                                            </tr> <?php endif; ?> <?php
                                              $predio = $dbo->getFields( "Socio" , "Predio" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" );
                                              if(!empty($predio)): ?> <tr>
                                                <td>Predio</td>
                                                <td><?php echo $dbo->getFields( "Socio" , "Predio" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ); ?></td>
                                            </tr> <?php endif; 
                                            if($detalle_reserva["IDClub"]==70)
                                            {
                                            ?> <tr>
                                                <td>Estado Socio Zeus</td>
                                                <td> <?php
                                                  $estadoZeus = $dbo->getFields( "Socio" , "IDEstadoZeus" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" );
                                                  foreach(SIMResources::$EstadoZeus as $tipo => $estado)
                                                  {
                                                    if($tipo == $estadoZeus)
                                                      echo $estado;
                                                  }?> </td>
                                            </tr> <?php                                            
                                            }
                                            ?> <tr>
                                                <td>Servicio</td>
                                                <td><?php
											$id_maestro=	$datos_servicio["IDServicioMaestro"];

										$nombre_servicio_personalizado = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDClub = '".SIMUser::get("club")."' and IDServicioMaestro = '" . $id_maestro . "'" );
										if(empty($nombre_servicio_personalizado))
											$nombre_servicio_personalizado =$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$id_maestro."'" );

									   	echo $nombre_servicio_personalizado;
									   ?></td>
                                            </tr>
                                            <tr>
                                                <td>Elemento</td>
                                                <td><?php echo $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '".$detalle_reserva["IDServicioElemento"]."'" ); ?></td>
                                            </tr> <?php if($detalle_reserva["IDClub"]!=112){ ?> <tr>
                                                <td># Personas (salones)</td>
                                                <td><?php echo $detalle_reserva["CantidadInvitadoSalon"]; ?></td>
                                            </tr> <?php
																 } else{ ?> <tr>
                                                <td># Personas confirmadas</td>
                                                <td><?php
																		foreach($array_invitados as $id_invitado => $datos_invitado){
																			$Totalinvitados++;
																			if($datos_invitado["IDSocio"]>0){
																					if($datos_invitado["Confirmado"]=="S"){
																						$TotalConfirmados++;
																					}
																					else{
																						$TotalNoConfirmados++;
																					}
																				}
																			}
																			echo "Invitados: " . (int)$Totalinvitados . " Confirmados: " . (int)$TotalConfirmados;


																		 ?></td>
                                            </tr> <?php }
																  ?> <?php
									/*
									 if($detalle_reserva["IDClub"]==37): //Para el polo club muestro cancah y equipo?> <tr>
                                                <td>Asignar Cancha / Equipo: </td>
                                                <td>
                                                    <? echo  SIMHTML::formPopupArray( SIMResources::$canchas_polo  ,  $detalle_reserva["Cancha"] , "Cancha" ,  "Seleccione cancha" , "form-control"  ); ?>
                                                    <br>
                                                    <? echo  SIMHTML::formPopupArray( SIMResources::$equipos_polo  ,  $detalle_reserva["Equipo"] , "Equipo" ,  "Seleccione equipo" , "form-control"  ); ?>
                                                </td>
                                            </tr> <?php

									 endif;
									*/
										?> <?php
                                     //Si se solicita otros campos al momento de reservar muestro los valores
									 $sql_otro_dato = "Select * From ReservaGeneralCampo Where IDReservaGeneral = '".$detalle_reserva["IDReservaGeneral"]."'";
									 $result_otro_dato = $dbo->query($sql_otro_dato);
									 while($row_otro_dato = $dbo->fetchArray($result_otro_dato)): ?> <tr>
                                                <td><?php echo $dbo->getFields( "ServicioCampo" , "Nombre" , "IDServicioCampo = '".$row_otro_dato["IDServicioCampo"]."'" ); ?></td>
                                                <td><?php echo $row_otro_dato["Valor"]; ?></td>
                                            </tr> <?php endwhile; ?> <?php if ($detalle_reserva["IDSocioBeneficiario"]>0): ?> <tr>
                                                <td>Beneficiario</td>
                                                <td> <?php
																					   if(SIMUser::get("PermiteCambiarReserva")=="S" || SIMUser::get("IDPerfil")==0):
																						$sql_socio_club = "Select * From Socio Where IDSocio = '".$detalle_reserva["IDSocioBeneficiario"]."'";
																						$qry_socio_club = $dbo->query($sql_socio_club);
																						$r_socio = $dbo->fetchArray($qry_socio_club); ?> <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax-beneficiario" title="número de derecho, nombre, apellido" value="<?php echo utf8_encode($r_socio["Apellido"] . " " .$r_socio["Nombre"]) ?>">
                                                    <input type="hidden" name="IDSocioBeneficiario" value="<?php echo $detalle_reserva["IDSocioBeneficiario"]; ?>" id="IDSocioBeneficiario" class="mandatory" title="Socio Beneficiario"> <?php
																					   else:
																						  echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$detalle_reserva["IDSocioBeneficiario"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$detalle_reserva["IDSocioBeneficiario"]."'" );
																							//Calculo edad si tiene fecha de nacimiento
																							$FechaNac = $dbo->getFields( "Socio" , "FechaNacimiento" , "IDSocio = '".$detalle_reserva["IDSocioBeneficiario"]."'" );
																							if(!empty($FechaNac) && $FechaNac<>"0000-00-00"):
																								list($Y,$m,$d) = explode("-",$FechaNac);
																							    echo "( Edad: " . ( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y ) . " a&ntilde;os )";
																							endif;
																					   endif;
																					    ?>
                                                </td>
                                            </tr> <?php endif; ?> <?php if ($detalle_reserva["IDInvitadoBeneficiario"]>0): ?> <tr>
                                                <td>Invitado Socio (autorizacion ingreso)</td>
                                                <td><?php echo $dbo->getFields( "Invitado" , "Nombre" , "IDInvitado = '".$detalle_reserva["IDInvitadoBeneficiario"]."'" ) . " " . $dbo->getFields( "Invitado" , "Apellido" , "IDInvitado = '".$detalle_reserva["IDInvitadoBeneficiario"]."'" );	 ?></td>
                                            </tr> <?php endif; ?> <?php if ($detalle_reserva["IDAuxiliar"]>0): ?> <tr>
                                                <td>Auxiliar</td>
                                                <td><?php
                                                    $array_aux = explode(",",$detalle_reserva["IDAuxiliar"]);
                                                    if (count($array_aux)>0):
                                                        foreach($array_aux as $id_aux):
                                                            if(!empty($id_aux))
                                                                echo $dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '".$id_aux."'" ) . "<br>"; ?> <?php
										                endforeach;
										            endif; ?> </td>
                                            </tr> <?php endif; ?> <?php if ($detalle_reserva["IDCaddie"]>0): ?> <tr>
                                                <td>Caddie Reserva</td>
                                                <td><?php                       
                                                    echo $dbo->getFields("Caddie2" , "Nombre" , "IDCaddie = '$detalle_reserva[IDCaddie]'" ); 
                                                    ?> </td>
                                            </tr> <?php endif; ?> <?php if ($detalle_reserva["IDTipoModalidadEsqui"]>0): ?> <tr>
                                                <td>Modalidad</td>
                                                <td><?php echo $dbo->getFields( "TipoModalidadEsqui" , "Nombre" , "IDTipoModalidadEsqui = '".$detalle_reserva["IDTipoModalidadEsqui"]."'" ); ?></td>
                                            </tr> <?php endif; ?> <?php if (!empty($detalle_reserva["Tee"])): ?> <tr>
                                                <td>Tee</td>
                                                <td><?php echo $detalle_reserva["Tee"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Hora Par</td>
                                                <td><?php
									    $HoraPar  = $dbo->getFields( "ServicioDisponibilidad" , "HoraPar" , "HoraDesde = '".$detalle_reserva["Hora"]."'" );
									   echo $HoraPar;
									   ?></td>
                                            </tr> <?php endif; ?> <?php if (!empty($detalle_reserva["IDServicioTipoReserva"])): ?> <tr>
                                                <td>Tipo</td>
                                                <td><?php echo $dbo->getFields( "ServicioTipoReserva" , "Nombre" , "IDServicioTipoReserva = '".$detalle_reserva["IDServicioTipoReserva"]."'" ); ?></td>
                                            </tr> <?php endif; ?> <tr>
                                                <td>Observaciones</td>
                                                <td>  <textarea rows="3" cols="50"  name="Observaciones" class="form-control"> <?php echo $detalle_reserva["Observaciones"]; ?> </textarea>  </td>
                                            </tr> <?php
									  //verifico si tiene una reserva automatica para mostrarla
									  $id_reserva_automatica = $dbo->getFields( "ReservaGeneralAutomatica" , "IDReservaGeneralAsociada" , " IDReservaGeneral = '".$detalle_reserva["IDReservaGeneral"]."'" );
									  if (!empty( $id_reserva_automatica)):
										  $detalle_reserva_auto = $dbo->fetchAll( "ReservaGeneral", " IDReservaGeneral = '" . $id_reserva_automatica . "' ", "array" );
									  ?> <tr>
                                                <td>Reserva Automatica</td>
                                                <td>
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <td>Numero Reserva</td>
                                                            <td><?php echo $detalle_reserva_auto["IDReservaGeneral"]; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Fecha / Hora</td>
                                                            <td><?php echo $detalle_reserva_auto["Fecha"] . " " .  $detalle_reserva_auto["Hora"]; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Servicio</td>
                                                            <td><?php

									   $id_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '".$detalle_reserva_auto["IDServicio"]."'" );

										//echo $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$id_maestro."'" ); ;

										$nombre_servicio_personalizado = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDClub = '".SIMUser::get("club")."' and IDServicioMaestro = '" . $id_maestro . "'" );
										if(empty($nombre_servicio_personalizado))
											$nombre_servicio_personalizado =$dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$id_maestro."'" );

										echo $nombre_servicio_personalizado;

									   ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Elemento</td>
                                                            <td><?php echo $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '".$detalle_reserva_auto["IDServicioElemento"]."'" ); ?></td>
                                                        </tr> <?php if ($detalle_reserva_auto["IDAuxiliar"]>0): ?> <tr>
                                                            <td>Auxiliar</td>
                                                            <td><?php echo $dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '".$detalle_reserva_auto["IDAuxiliar"]."'" ); ?></td>
                                                        </tr> <?php endif; ?> <?php if ($detalle_reserva_auto["IDTipoModalidadEsqui"]>0): ?> <tr>
                                                            <td>Modalidad</td>
                                                            <td><?php echo $dbo->getFields( "TipoModalidadEsqui" , "Nombre" , "IDTipoModalidadEsqui = '".$detalle_reserva_auto["IDTipoModalidadEsqui"]."'" ); ?></td>
                                                        </tr> <?php endif; ?>
                                                    </table>
                                                </td>
                                            </tr> <?php endif; ?> <?php
																		 //if(count($array_invitados)>0 || $detalle_reserva["IDServicio"] == "24" || $detalle_reserva["IDServicio"] == "289" ):
									 	$invitados="S";
									  ?> <tr>
                                                <td>Invitados</td>
                                                <td> <?php

                                            $permiso_escritura = $dbo->getFields( "Usuario" , "Permiso" , "IDUsuario = '" . SIMUser::get( "IDUsuario" ) . "'");
										if($id_servicio_maestro>0 && $permiso_escritura == 'E'):?> <input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-socios" title="número de derecho">
                                                    <br><a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
                                                    <br> <?php endif; ?> <select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8" multiple> <?php
											$item=1;
                                        	foreach($array_invitados as $id_invitado => $datos_invitado):
													$item--;
													if($datos_invitado["IDSocio"]>0):
														$nombre_socio = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ));
														//Bellavista reconfiracion
														if($detalle_reserva["IDClub"]==112){
															if(empty($datos_invitado["Confirmado"]))
																	$confirmado="N";
															else
																$confirmado=$datos_invitado["Confirmado"];

																$nombre_socio.=" Confirmado:".$confirmado;
														}
													?> <option value="<?php echo "socio-".$datos_invitado["IDSocio"]; ?>"><?php echo $nombre_socio; ?> / SOCIO CLUB</option> <?php
													else: ?> <option value="<?php echo "externo-".$datos_invitado["Nombre"]."-$datos_invitado[IDReservaGeneralInvitado]"; ?>"><?php echo $datos_invitado["Nombre"];  ?> / INVITADO EXTERNO</option> <?php
													endif;
											endforeach;
											?> </select>
                                                    <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">
                                                </td>
                                            </tr>
                                            <!-- OBSERVACIONES DEL SOCIO PARA CLUB XPORTIVA --> <?php if($detalle_reserva["IDClub"]==136):?> <tr>
                                                <td> Observaciones Generales Socio </td>
                                                <textarea class="form-control" name="Observaciones" id="Observaciones">
                                                            <?php echo $Observaciones = $dbo->getFields( "Socio" , "ObservacionGeneral" , "IDSocio = '$detalle_reserva[IDSocio]'" ); ?>
                                                        </textarea>
                                                </td>
                                            </tr> <?php endif;?> <?php
									 //if($id_servicio_maestro==15): //15 = Golf
                                     if($invitados=="S" || $id_servicio_maestro>0 || (SIMUser::get("PermiteCambiarReserva")=="S" || SIMUser::get("IDPerfil")==0)): //15 = Golf	 ?> <tr>
                                                <td align="center" colspan="2">
                                                    <input type="hidden" name="action" id="action" value="updateinvitado">
                                                    <input type="hidden" name="IDReservaGeneral" id="IDReservaGeneral" value="<?php echo $detalle_reserva["IDReservaGeneral"]; ?>">
                                                    <input type="hidden" name="IDSocioOrig" id="IDSocioOrig" value="<?php echo $detalle_reserva["IDSocio"]; ?>">
                                                    <input type="submit" name="actualiza_participante" id="actualiza_participante" value="Actualizar Datos">
                                                </td>
                                            </tr> <?php endif; ?>
                                        </table>
                                    </form> <?php 
                                        $CamposDinamicos = $dbo->getFields("Servicio","CamposDinamicosInvitadoExternoHabilitado","IDServicio = $detalle_reserva[IDServicio]");
                                        if($CamposDinamicos == 'S'):
                                            ?>
                                    <!-- CAMPOS DINAMICOS DE INVITADOS -->
                                    <table id="simple-table" class="table table-striped table-bordered table-hover">
                                        <tr>
                                            <td align="center" colspan="3"> Respuestas campos dinamicos invitados externos </td>
                                        </tr>
                                        <tr>
                                            <th>Persona</th>
                                            <th>Pregunta</th>
                                            <th>Respuesta</th>
                                        </tr> <?php 
                                                        foreach($array_invitados as $idReservaInvitado => $invitado): 
                                                            if($invitado[IDSocio] == 0):
                                                                $SLQCamposInvitado = "SELECT IDCampoInvitadoExterno, EtiquetaCampo FROM CampoInvitadoExterno WHERE IDServicio = $detalle_reserva[IDServicio] AND Activo = 1";
                                                                $QRYCamposInvitado = $dbo->query($SLQCamposInvitado);    
                                                                while($Campos = $dbo->fetchArray($QRYCamposInvitado)):   
                                                                    $respuesta = $dbo->getFields("RespuestasCampoInvitadoExterno","Valor","IDReservaGeneralInvitado = $invitado[IDReservaGeneralInvitado] AND IDCampoInvitadoExterno = $Campos[IDCampoInvitadoExterno]");                                                     
                                                                    ?> <tr>
                                            <th><?=$invitado[Nombre]?></th>
                                            <td><?=$Campos[EtiquetaCampo]?></td>
                                            <td><?=$respuesta?></td>
                                        </tr> <?php
                                                                endwhile;
                                                            endif;
                                                        endforeach;
                                                    ?>
                                    </table> <?php endif; 
                                    ?> <form>
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td align="center" colspan="3"> Adicionales Reserva </td>
                                            </tr>
                                            <tr>
                                                <th>Persona</th>
                                                <th>Adicional</th>
                                                <th>Estado</th>
                                            </tr> <?php                                                                             
                                            
                                           
                                            $sql = "SELECT * FROM ReservaGeneralAdicional RGA WHERE IDReservaGeneral = $detalle_reserva[IDReservaGeneral]";
                                            $qry = $dbo->query($sql);

                                            while($Adicionales = $dbo->fetchArray($qry)):
                                                $retornado = $Adicionales[Retornado];

                                                if($retornado == null){
                                                    $retornado = "N";                                            
                                                }

                                                $NombreCategoria = $dbo->getFields("ServicioPropiedad","Nombre","IDServicioPropiedad = $Adicionales[IDServicioPropiedad]");
                                                $NombreServicio = $dbo->getFields("ServicioAdicional","Nombre","IDServicioAdicional = $Adicionales[IDServicioAdicional]");
                                                ?> <tr>
                                                <td><?php echo $r_socio["Apellido"] . " " .$r_socio["Nombre"]?></td>
                                                <td><?php echo "<strong>" . $NombreCategoria . "</strong> - " . $NombreServicio?></td>
                                                <td>
                                                    <label>retornado</label>
                                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $retornado ,  "RetornadoSocio-{$reservaItem['IDReservaGeneralAdicional']}", "class='input mandatory'" ) ?>
                                                </td>
                                            </tr> <?php
                                            endwhile;                                           
                                            ?> <?php                                           
                                            

                                        foreach($array_invitados as $idReservaInvitado => $invitado){
                                                                                        
                                           
                                            $sql = "SELECT * FROM ReservaGeneralAdicionalInvitado WHERE IDReservaGeneralInvitado = $invitado[IDReservaGeneralInvitado]";
                                            $qry = $dbo->query($sql);

                                            if($invitado[IDSocio] > 0):
                                                $NombreInvitado = $dbo->getFields("Socio","Nombre","IDSocio = $invitado[IDSocio]") . " " . $dbo->getFields("Socio","Apellido","IDSocio = $invitado[IDSocio]");
                                            else:
                                                $NombreInvitado = $invitado[Nombre];
                                            endif;

                                            while($Adicionales = $dbo->fetchArray($qry)):
                                                $retornado = $Adicionales[Retornado];

                                                if($retornado == null){
                                                    $retornado = "N";                                            
                                                }

                                                $NombreCategoria = $dbo->getFields("ServicioPropiedad","Nombre","IDServicioPropiedad = $Adicionales[IDServicioPropiedad]");
                                                $NombreServicio = $dbo->getFields("ServicioAdicional","Nombre","IDServicioAdicional = $Adicionales[IDServicioAdicional]");
                                                ?> <tr>
                                                <td><?php echo $NombreInvitado?></td>
                                                <td><?php echo "<strong>" . $NombreCategoria . "</strong> - " . $NombreServicio?></td>
                                                <td>
                                                    <label>retornado</label>
                                                    <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $retornado ,  "RetornadoSocio-{$reservaItem['IDReservaGeneralAdicional']}", "class='input mandatory'" ) ?>
                                                </td>
                                            </tr> <?php
                                            endwhile;                                                                          
                                        }
                                        ?> <?php if(!empty($reservaAdicionalInvitado) > 0 || !empty($reservaAdicionalSocio)):?> <tr>
                                                <td align="center" colspan="3">
                                                    <input type="hidden" name="idr" id="idr" value="<?php echo $detalle_reserva["IDReservaGeneral"]; ?>">
                                                    <input type="hidden" name="action" id="action" value="actualizarretornos">
                                                    <input type="submit" name="actualiza_retornos" id="actualiza_retornos" value="Actualizar Retornos">
                                                </td>
                                            </tr> <?php endif ?>
                                        </table>
                                    </form> <?php } ?> <?php if ( $id_servicio_maestro == 15 || $id_servicio_maestro == 27 || $id_servicio_maestro == 28 || $id_servicio_maestro == 30 ): //Golf ?> <form name="actualiza_horarios" id="actualiza_horarios" method="post" action="" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td>Hora de salida primero 9 hoyos:</td>
                                                <td>
                                                    <input type="time" name="HoraSalidaPrimero9hoyos" id="HoraSalidaPrimero9hoyos" value="<?php echo $detalle_reserva["HoraSalidaPrimero9hoyos"]; ?>">
                                                </td>
                                                <td>Hora de terminación primeros 9 hoyos:</td>
                                                <td>
                                                    <input type="time" name="HoraFinPrimero9Hoyos" id="HoraFinPrimero9Hoyos" value="<?php echo $detalle_reserva["HoraFinPrimero9Hoyos"]; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Hora de inicio segundos 9 hoyos:</td>
                                                <td>
                                                    <input type="time" name="HoraSalidaSegundo9Hoyos" id="HoraSalidaSegundo9Hoyos" value="<?php echo $detalle_reserva["HoraSalidaSegundo9Hoyos"]; ?>">
                                                </td>
                                                <td>Hora de finalización de segundo 9 hoyos:</td>
                                                <td>
                                                    <input type="time" name="HoraFinSegundo9Hoyos" id="HoraFinSegundo9Hoyos" value="<?php echo $detalle_reserva["HoraFinSegundo9Hoyos"]; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="hidden" name="action" id="action" value="confirmacionhorario">
                                                    <input type="hidden" name="IDReservaGeneral" id="IDReservaGeneral" value="<?php echo $detalle_reserva["IDReservaGeneral"]; ?>">
                                                    <input type="hidden" name="IDUsuario" id="IDUsuario" value="<?php echo SIMUser::get("IDUsuario"); ?>">
                                                    <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $detalle_reserva["IDSocio"]; ?>">
                                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $detalle_reserva["IDClub"]; ?>">
                                                    <input type="submit" name="confirmacion_horario" id="confirmacion_horario" value="Confirmar Horario">
                                                </td>
                                            </tr>
                                        </table>
                                    </form> <?php endif; ?> <?php if($_GET["tipo"]!="horario"){ ?> <?php if($detalle_reserva["IDClub"]=="36"): //Para aeroclub le deben confirmar la reserva al socio ?> <form name="confirma_reserva_pendiente" id="confirma_reserva_pendiente" method="post" action="" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td>Confirmar reserva?</td>
                                                <td> <?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $detalle_reserva["Confirmada"] , "Confirmada" , "title=\"Confirmada\"" )?> </td>
                                            </tr>
                                            <tr>
                                                <td>Observaciones:</td>
                                                <td><textarea class="form-control" name="ObservacionConfirmada" id="ObservacionConfirmada"><?php echo $detalle_reserva["ObservacionConfirmada"]; ?></textarea></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="hidden" name="action" id="action" value="updateconfirmarreserva">
                                                    <input type="hidden" name="IDReservaGeneral" id="IDReservaGeneral" value="<?php echo $detalle_reserva["IDReservaGeneral"]; ?>">
                                                    <input type="hidden" name="IDUsuario" id="IDUsuario" value="<?php echo SIMUser::get("IDUsuario"); ?>">
                                                    <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $detalle_reserva["IDSocio"]; ?>">
                                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $detalle_reserva["IDClub"]; ?>">
                                                    <input type="hidden" name="FechaReservaConfirma" id="FechaReservaConfirma" value="<?php echo $detalle_reserva["Fecha"]; ?>">
                                                    <input type="hidden" name="HoraReservaConfirma" id="HoraReservaConfirma" value="<?php echo $detalle_reserva["Hora"]; ?>">
                                                    <input type="submit" name="actualiza_confirmar_reserva" id="actualiza_confirmar_reserva" value="Confirmar reserva">
                                                </td>
                                            </tr>
                                        </table>
                                    </form> <?php endif; ?> <form name="cump" id="confirma_reserva_tomada" method="post" action="" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td>Reserva Cumplida?</td>
                                                <td> <?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$tipocumplimientoreserva ) , $detalle_reserva["Cumplida"] , "Cumplida" , "title=\"Cumplida\"" )?> </td>
                                            </tr>
                                            <tr>
                                                <td>Presente <?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$detalle_reserva["IDSocio"]."'" ); ?>?</td>
                                                <td><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $detalle_reserva["CumplidaCabeza"] , "CumplidaCabeza", "title=\"CumplidaCabeza"."\"" )?></td>
                                            </tr> <?php if ($detalle_reserva["IDSocioBeneficiario"]>0): ?> <tr>
                                                <td colspan="2" bgcolor="#A7CAA7">Beneficiarios Presentes?</td>
                                            </tr>
                                            <tr>
                                                <td> Presente <?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$detalle_reserva["IDSocioBeneficiario"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$detalle_reserva["IDSocioBeneficiario"]."'" ); ?></td>
                                                <td><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $detalle_reserva["CumplidaCabeza"] , "CumplidaCabeza", "title=\"CumplidaCabeza"."\"" )?></td>
                                            </tr> <?php endif; ?> <?php
                                          $sql_invitados_reserva ="Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$detalle_reserva["IDReservaGeneral"]."'";
                                          $qry_invitados_reserva = $dbo->query($sql_invitados_reserva);
                                          $total_invitados = $dbo->rows($qry_invitados_reserva);
                                          if($total_invitados>0):
                                          ?> <tr>
                                                <td colspan="2" bgcolor="#A7CAA7">Invitados Presentes?</td>
                                            </tr> <?php while($row_invitados_reserva = $dbo->fetchArray($qry_invitados_reserva)):?> <tr>
                                                <td><?php
                                                    if(empty($row_invitados_reserva["Nombre"]) && (int)$row_invitados_reserva["IDSocio"]>0){
                                                        $datos_invi=$dbo->fetchAll( "Socio", " IDSocio = '" . $row_invitados_reserva["IDSocio"] . "' ", "array" );
                                                        echo $datos_invi["Nombre"] . " " . $datos_invi["Apellido"];
                                                    }
                                                    else{
                                                        echo $row_invitados_reserva["Nombre"];
                                                    }

                                                    ?></td>
                                                <td><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $row_invitados_reserva["Cumplida"] , "InvitadoCumplio".$row_invitados_reserva["IDReservaGeneralInvitado"] , "title=\"InvitadoCumplio".$row_invitados_reserva["IDReservaGeneralInvitado"]."\"" )?></td>
                                            </tr> <?php endwhile; ?> <?php endif;?> <?php 
                                                 $permite_sistema_abono = $dbo->getFields("Servicio", "PermiteObservacionesPersonalizadas", "IDServicio = '$detalle_reserva[IDServicio]'");
                                                 if ($permite_sistema_abono == "1") {
                                            ?> <tr>
                                                <td>Observaciones Personlizadas:</td>
                                                <td>
                                                    <script>
                                                    function escribir() {
                                                        var Observacion = document.getElementById("ObservacionesPersonalizadas").value;
                                                        document.getElementById("ObservacionCumplida").value += Observacion + "\n";
                                                    }
                                                    </script> <?php
                                                    $SQLObservaciones = "SELECT * FROM ObservacionesParaReservas WHERE IDServicio = $detalle_reserva[IDServicio]";
                                                    $QRYObservaciones = $dbo->query($SQLObservaciones);
                                                    ?> <select id="ObservacionesPersonalizadas" onchange="escribir();">
                                                        <option value="">[Selecciona una opcion]</option> <?php
                                                        while($Datos = $dbo->fetchArray($QRYObservaciones)):
                                                            ?> <option value="<?=$Datos[Obersvacion]?>"><?=$Datos[Obersvacion]?></option> <?php
                                                        endwhile;
                                                        ?>
                                                    </select> <?php
                                                    ?>
                                                </td>
                                            </tr> <?php  } ?> <tr>
                                                <td>Observaciones:</td>
                                                <td><textarea class="form-control" name="ObservacionCumplida" id="ObservacionCumplida"> <?php echo $detalle_reserva["ObservacionCumplida"]; ?></textarea></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <input type="hidden" name="action" id="action" value="updatereservatomada">
                                                    <input type="hidden" name="IDReservaGeneral" id="IDReservaGeneral" value="<?php echo $detalle_reserva["IDReservaGeneral"]; ?>">
                                                    <input type="hidden" name="IDUsuario" id="IDUsuario" value="<?php echo SIMUser::get("IDUsuario"); ?>">
                                                    <input type="submit" name="actualiza_estado_reserva" id="actualiza_estado_reserva" value="Guardar Asistencia">
                                                </td>
                                            </tr>
                                        </table>
                                    </form> <?php } ?> <?php

                                       $PermiteAdicionales =$dbo->getFields("Servicio","PermiteAdicionarServicios","IDServicio = $detalle_reserva[IDServicio]");

                                    if($PermiteAdicionales=="S"): ?> <form name="cump" id="confirma_reserva_tomada" method="post" action="" enctype="multipart/form-data">
                                        <table id="simple-table" class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td bgcolor="#A7CAA7">Adicionales para socio: <?php echo $detalle_reserva["NombreSocio"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover"> <?php
                                                            $sql = "SELECT * FROM ServicioPropiedad WHERE IDClub = '".SIMUser::get("club")."' AND IDServicio =  $detalle_reserva[IDServicio] AND Publicar = 'S'";
                                                            $query = $dbo->query($sql);
                                                            while ($row = $dbo->fetchArray($query))
                                                            { 
                                                                $sqlAdicionales = "SELECT * FROM ServicioAdicional WHERE IDServicioPropiedad = $row[IDServicioPropiedad] AND Publicar = 'S'";
                                                                $queryAicionales = $dbo->query($sqlAdicionales);
                                                                $columnas = 0;                                                             
                                                                ?> <tr>
                                                            <td> Categoria : <?php echo $row["Nombre"]; ?> </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                                    <tr>
                                                                        <input type="hidden" name="IDCaracteristicaSocio[]" value="<?php echo $row[IDServicioPropiedad]; ?>"> <?php                                                                                
                                                                                while( $rowProductos = $dbo->fetchArray($queryAicionales))
                                                                                {     
                                                                                    $columnas++;      

                                                                                    if($row[Tipo] == "Radio"):
                                                                                        echo '<td>
                                                                                                    <label class="radiogroup">
                                                                                                        <input type="radio" name="ServicioAdicionalSocio_'.$row[IDServicioPropiedad].'[]"  value="'. $rowProductos[IDServicioAdicional] .'">                                                    
                                                                                                        '.$rowProductos[Nombre].'                                                                                                      
                                                                                                    </label>
                                                                                                </td>';
                                                                                    elseif($row[Tipo] == "Checkbox"):
                                                                                        echo '<td>
                                                                                                <label class="checkgroup">
                                                                                                    <input type="checkbox" name="ServicioAdicionalSocio_'.$row[IDServicioPropiedad].'[]"  value="'. $rowProductos[IDServicioAdicional] .'">                                                    
                                                                                                    '.$rowProductos[Nombre].'                                                                                                      
                                                                                                </label>
                                                                                            </td>';                                                                                   
                                                                                    endif;                                        

                                                                                    if($columnas== 4):
                                                                                        $checkgroup .= "</tr><tr>";
                                                                                        $columnas = 0;                                                                   
                                                                                    endif;                                              
                                                                                }                                                                                                       
                                                                                ?>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr> <?php
                                                            }
                                                            ?>
                                                    </table>
                                                </td>
                                            </tr> <?php 
                                                foreach($array_invitados as $id_invitado => $datos_invitado): 
                                                    if(!empty($datos_invitado[Nombre]))
                                                        $nombre_socio = $datos_invitado[Nombre];
                                                    else
                                                        $nombre_socio = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ));
                                                    ?> <tr>
                                                <td bgcolor="#A7CAA7">Adicionales para Invitado: <?php echo $nombre_socio; ?></td>
                                                <input type="hidden" name="IDReservaGeneralInvitado[]" value="<?php echo $datos_invitado["IDReservaGeneralInvitado"]; ?>">
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table id="simple-table" class="table table-striped table-bordered table-hover"> <?php   
                                                                $sql = "SELECT * FROM ServicioPropiedad WHERE IDClub = '".SIMUser::get("club")."' AND IDServicio =  $detalle_reserva[IDServicio] AND Publicar = 'S'";
                                                                $query = $dbo->query($sql);                                                           
                                                                while ($row = $dbo->fetchArray($query))
                                                                { 
                                                                    $sqlAdicionales = "SELECT * FROM ServicioAdicional WHERE IDServicioPropiedad = $row[IDServicioPropiedad] AND Publicar = 'S'";
                                                                    $queryAicionales = $dbo->query($sqlAdicionales);
                                                                    $columnas = 0;                                                             
                                                                    ?> <tr>
                                                            <td> Categoria : <?php echo $row["Nombre"]; ?> </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table id="simple-table" class="table table-striped table-bordered table-hover">
                                                                    <tr>
                                                                        <input type="hidden" name="IDCaracteristicaInvitado_<?php echo $datos_invitado["IDReservaGeneralInvitado"]; ?>[]" value="<?php echo $row[IDServicioPropiedad]; ?>"> <?php                                                                                
                                                                                    while( $rowProductos = $dbo->fetchArray($queryAicionales))
                                                                                    {     
                                                                                        $columnas++;      

                                                                                        if($row[Tipo] == "Radio"):
                                                                                            echo '<td>
                                                                                                        <label class="radiogroup">
                                                                                                            <input type="radio" name="ServicioAdicional_'.$row[IDServicioPropiedad].'_Invitado_'.$datos_invitado[IDReservaGeneralInvitado].'[]"  value="'. $rowProductos[IDServicioAdicional] .'">                                                    
                                                                                                            '.$rowProductos[Nombre].'                                                                                                      
                                                                                                        </label>
                                                                                                    </td>';
                                                                                        elseif($row[Tipo] == "Checkbox"):
                                                                                            echo '<td>
                                                                                                    <label class="checkgroup">
                                                                                                        <input type="checkbox" name="ServicioAdicional_'.$row[IDServicioPropiedad].'_Invitado_'.$datos_invitado[IDReservaGeneralInvitado].'[]"  value="'. $rowProductos[IDServicioAdicional] .'">                                                    
                                                                                                        '.$rowProductos[Nombre].'                                                                                                      
                                                                                                    </label>
                                                                                                </td>';                                                                                   
                                                                                        endif;                                        

                                                                                        if($columnas== 4):
                                                                                            $checkgroup .= "</tr><tr>";
                                                                                            $columnas = 0;                                                                   
                                                                                        endif;                                              
                                                                                    }                                                                                                       
                                                                                    ?>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr> <?php
                                                                }
                                                                ?>
                                                    </table>
                                                </td>
                                            </tr> <?php 
                                                endforeach; ?> <tr>
                                                <td colspan="2" align="center">
                                                    <input type="hidden" name="action" id="action" value="updateadicionales">
                                                    <input type="hidden" name="IDReservaGeneral" id="IDReservaGeneral" value="<?php echo $detalle_reserva["IDReservaGeneral"]; ?>">
                                                    <input type="hidden" name="IDUsuario" id="IDUsuario" value="<?php echo SIMUser::get("IDUsuario"); ?>">
                                                    <input type="submit" name="actualiza_estado_reserva" id="actualiza_estado_reserva" value="Guardar Adicionales">
                                                </td>
                                            </tr>
                                        </table>
                                    </form> <?php endif; ?>
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
