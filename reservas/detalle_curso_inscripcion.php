<?php

include("procedures/general.php");
include("procedures/login.php");
include("cmp/seoliga.php");

$frm = SIMUtil::varsLOG( $_POST );
if($frm["action"]=="BuscarCurso"){
	$resultado=SIMWebServiceApp::curso_buscar($frm["IDClub"],$frm["IDSocio"],$frm["IDCursoSede"],$frm["IDCursoTipo"],$frm["IDCursoEntrenador"]);
}

$IDSocio=$frm["IDSocio"];
$extra2=$frm["IDClub"];
$datos_club = $dbo->fetchAll("Club","IDClub = $extra2");
$datos_club_otros = $dbo->fetchAll("ConfiguracionClub","IDClub = $extra2");
$datos_socio = $dbo->fetchAll("Socio","IDSocio = " . $frm["IDSocio"]);
$data_public_key = $datos_club[ApiKeyWompi];
$Integrity_key = $datos_club[IntegridadWompi];
$moneda = "COP";
$CorreoSocio = $datos_socio[CorreoElectronico];
$NombreSocio = $datos_socio[Nombre] . " " .$datos_socio[Apellido];
$Celular = $datos_socio[Celular];
$Documento = $datos_socio[NumeroDocumento];



?>



<script type="text/javascript">

            $(document).ready(function () {

							$(".btnInscribirCurso").click(function(){
								var IDCursoHorario = $(this).attr("rel");
								var IDCursoCalendario = $(this).attr("calendario");
								var HoraDesde = $(this).attr("horadesde");
								var Consecutivo = $(this).attr("consecutivo");
								var Cupos = $(this).attr("cupos");
								var Valor = $(this).attr("valor");
								var detalle;
								$("#IDCursoHorario").val( IDCursoHorario );
								$("#IDCursoCalendario").val( IDCursoCalendario );
								$("#HoraDesde").val( HoraDesde );
								$("#Cupos").val( Cupos );
								$("#Valor").val( Valor );
								$("#txtmsjreserva"+Consecutivo).html("Procesando, por favor espere...");
								$("#frmInscribirCurso").submit();
								return false;
							});

            });
        </script>


    </head>
    <body>

       <div id="cont_general">
            <?php include("cmp/menuliga.php"); ?>


				<div id="cuerpo">

					<?php
					if($frm["action"]=="insert"){
						?>
						<div id="titulos_internas">Procesando, por favor espere...</div>
						<?php
						if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
						{
							//los campos al final de las tablas
							$frm = SIMUtil::varsLOG( $_POST );
							$array_cursos[]=$frm["IDCursoCalendario"];
							$referencia="CursoMensual".time();
							$Modulo="Curso";
							$ValorPagar=base64_decode($frm["Valor"]);


							if($frm["TipoInscripcion"]=="Trimestre"){
								$referencia="CursoTrimestral".time();
								$Modulo="CursoTrimestral";
								$ValorPagar=base64_decode($frm["ValorTrimestre"]);
								$frm["IDCursoCalendarioTrimestre"];
				        $array_otros=explode(",",$frm["IDCursoCalendarioTrimestre"]);
				        foreach ($array_otros as $key => $value) {
				          $array_cursos[]=$value;
				        }
				        if(count($array_cursos)!="3"){
				            echo "Ocurrio un problema al calcular los proximos cursos";
				            exit;
				        }
				      }

							$contador=0;
				      foreach($array_cursos as $id_curso => $curso){
								if($contador==0)
									$ValorInsertar=$ValorPagar;
								else
									$ValorInsertar="1";


									$ValorPagar.="00";
									$sqlWompi = "   INSERT INTO PagosWompi (IDReserva,IDClub, IDSocio, Tipo, Estado, Valor, FechaTrCr, UsuarioTrCr)
													VALUES ('$extra1', $extra2, $IDSocio, '$Modulo', 'INICIADO' ,$ValorPagar, NOW(), '$datos_socio[Nombre]')";
									$qryWompi = $dbo->query($sqlWompi);
									$data_reference = $dbo->lastID();
									$referencia=$data_reference;
									$sql_reserva="UPDATE  PagosWompi  SET IDReserva = '".$referencia."' WHERE IDPagosWompi = '".$referencia."' ";
									$dbo->query($sql_reserva);
									$redirect_url = "https://www.miclubapp.com/reservas/respuesta_transaccion_wompi.php?IDClub=$extra2&IDPagosWompi=$data_reference";

				        $respuesta = SIMWebServiceApp::set_curso_inscribir($frm["IDClub"],$frm["IDSocio"], $frm["IDCursoHorario"],$curso,'',$frm["HoraDesde"],$frm["Cupos"],$ValorInsertar,$referencia);
				        $mensaje_respuesta.="<br>".$respuesta["message"];
								$contador++;
				      }


							if($respuesta["success"]){
								//header("Location: https://www.payulatam.com/co/");
								$firma= $datos_club["ApiKey"]."~".$datos_club["MerchantId"]."~".$referencia."~".$ValorPagar."~"."COP";
								$firma_codificada = md5($firma);
								?>

								<!--
								<form name="frm_POL" method="post" action="<?php echo $datos_club["URL_PAYU"]; ?>">
								  <input name="moneda"    type="hidden"  value="COP"   >
								  <input name="ref"     type="hidden"  value="<?php echo $referencia; ?>" >
								  <input name="llave"   type="hidden"  value="<?php echo $datos_club["ApiKey"]; ?>"  >
								  <input name="userid" type="hidden"  value="<?php echo $datos_club["MerchantId"]; ?>" >
								  <input name="usuarioId"        type="hidden"  value="<?php echo $datos_club["MerchantId"]; ?>"   >
								  <input name="accountId"           type="hidden"  value="<?php echo $datos_club["AccountId"]; ?>"  >
								  <input name="descripcion" type="hidden"  value="Pago Curso" >
								  <input name="extra1"      type="hidden"  value="<?php echo $frm["IDClub"]; ?>" >
								  <input name="extra2"     type="hidden"  value="<?php echo $frm["IDSocio"]; ?>"  >
								  <input name="refVenta"          type="hidden"  value="<?php echo $referencia; ?>" >
								  <input name="valor"    type="hidden"  value="<?php echo $ValorPagar; ?>" >
									<input name="iva"    type="hidden"  value="0" >
									<input name="baseDevolucionIva"    type="hidden"  value="0" >
								  <input name="firma"    type="hidden"  value="<?php echo $firma_codificada; ?>" >
									<input name="prueba"    type="hidden"  value="<?php echo $datos_club["IsTest"]; ?>" >
								  <input name="emailComprador"    type="hidden"  value="<?php echo $datos_socio["CorreoElectronico"]; ?>" >
									<input name="url_respuesta"    type="hidden"  value="https://www.miclubapp.com/reservas/respuesta_transaccion.php" >
									<input name="url_confirmacion"    type="hidden"  value="https://www.miclubapp.com/reservas/confirmacion_pagos.php" >
								  
								</form>
									-->
								<?php //print "<script>document.frm_POL.submit();</script>"; ?>


								<?php
								
								?>
								<!-- WOMPI -->
								<form name="WOMPICHECKOUT" action="https://checkout.wompi.co/p/" method="GET">
										<!-- OBLIGATORIOS -->
										<input type="hidden" name="public-key" value="<?php echo $data_public_key ?>" />
										<input type="hidden" name="currency" value="<?php echo $moneda ?>" />
										<input type="hidden" name="amount-in-cents" value="<?php echo $ValorPagar ?>" />
										<input type="hidden" name="reference" value="<?php echo $data_reference ?>" />
										<!-- OPCIONALES -->
										<input type="hidden" name="redirect-url" value="<?php echo $redirect_url ?>" />									
										<input type="hidden" name="customer-data:email" value="<?php echo $CorreoSocio ?>" />
										<input type="hidden" name="customer-data:full-name" value="<?php echo $NombreSocio ?>" />
										<input type="text" name="shipping-address:region" value="<?php echo $referencia; ?>" />
										<input type="hidden" name="shipping-address:address-line-1" value="Pago curso" />
										<input type="hidden" name="shipping-address:country" value="CO" />
										<input type="hidden" name="shipping-address:city" value="Bogota" />
										<input type="hidden" name="shipping-address:phone-number" value="3118808080" />									
									</form>
									<script>document.WOMPICHECKOUT.submit();</script>



							<?php
							exit;
							}
							else{
								SIMHTML::jsAlert($respuesta["message"]);
								SIMHTML::jsRedirect( "cursoinscripcion.php" );
							}

						}
					}
					?>


            <div class="cont_central">

            <div id="titulos_internas">INSCRIPCION CURSOS</div>


            <div id="txt_internas">

              	<form name="frmGeneral" id="frmGeneral"  method="post" action="cursoinscripcion.php" class="formvalida" >

                  <input type="hidden" name="form" value="formContacto">
                  <input type="text" style="display:none;" name="xvar">
                      <div class="cont_1_form_pie">
                          <label class="etiqueta_form_vive_interna">Sede</label>
                          <?php echo SIMHTML::formPopUp( "CursoSede" , "Nombre" , "Nombre" , "IDCursoSede" , $frm["IDCursoSede"] , "[Seleccione]" , "campo_form_pie" , "title = \"Sede\"", " and IDClub = '".SIMUser::get("club")."'" ); ?>
                      </div>
                      <div class="cont_1_form_pie">
                          <label class="etiqueta_form_vive_interna">Tipo</label>
                          <?php echo SIMHTML::formPopUp( "CursoTipo" , "Nombre" , "Nombre" , "IDCursoTipo" , $frm["IDCursoTipo"] , "[Seleccione]" , "campo_form_pie" , "title = \"Horario\"", " and IDClub = '".SIMUser::get("club")."'" ); ?>
                      </div>

											<div class="cont_1_form_pie">
													<label class="etiqueta_form_vive_interna">Entrenador</label>
													<?php echo SIMHTML::formPopUp( "CursoEntrenador" , "Nombre" , "Nombre" , "IDCursoEntrenador" , $frm["IDCursoEntrenador"] , "[Seleccione]" , "campo_form_pie" , "title = \"Entrenador\"", " and IDClub = '".SIMUser::get("club")."'" ); ?>
											</div>


											<input type="hidden" id="IDSocio" name="IDSocio" value="<?php echo $datos->IDSocio;  ?>" />
                      <input type="submit" class="enviar_contacto" id="enviar_contacto"/>
											<input type="hidden" name="action" value="BuscarCurso">
										 <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                  </form>
            </div>


						<div id="txt_internas">

							<?php
								$datosdecode = base64_decode($_GET["datosseleccion"]);
								$datos=json_decode($datosdecode);
							?>


												<form id="frmCursoIncripcion" name="frmCursoIncripcion" action="" method="post" enctype="multipart/form-data">
														<table  class="blueTable">
														 <tr>
															 <td>Curso</td>
															 <td><?php echo $datos->Nombre . " " . $datos->Nivel; ?></td>
														 </tr>
														 <tr>
															<td>Edad</td>
																<td><?php echo $datos->Edad; ?></td>
														 </tr>
														 <tr>
															<td>Sede</td>
																<td><?php echo $datos->Sede; ?></td>
														 </tr>
														 <tr>
															 <td>Dia</td>
															 <td><?php echo $datos->Dia; ?>
														 </td>
														 </tr>
														 <tr>
															 <td>Fecha  Inicio</td>
															 <td><?php echo $datos->FechaInicio . " al " .$datos->FechaFin ?> <b>Hora:</b> <?php echo $datos->HoraDesde; ?> </td>
														 </tr>
														 <tr>
															 <td>Entrenador</td>
															 <td><?php echo $datos->Entrenador; ?></td>
														 </tr>
														 <tr>
															 <td>TIPO DE INSCRIPCION</td>
															 <td>
																 <table width="100%">
																	 <tr>
																		 <td valign="top">
																			 <input type="radio" name="TipoInscripcion" class="form-control" value="Mes" checked="checked" > 1 MES: <b><?php echo "$".number_format($datos->ValorMes,0,'','.');  ?></b>
																		 </td>
																		 <!--
																		 <td valign="top">
																			 <input type="radio" name="TipoInscripcion" class="form-control" value="Trimestre"> TRIMESTRE: <b><?php echo "$".number_format($datos->ValorTrimestre,0,'','.');  ?></b>
																			 <br><strong>Ser&aacute; inscrito también en las siguientes fechas:</strong>
																			 <?php
																				$sql_calendario="SELECT * FROM CursoCalendario WHERE IDCursoTipo = '".$datos->IDCursoTipo."' and FechaInicio > '".$datos->FechaInicio."' And IDClub =  '".$_GET["IDClub"]."' ORDER BY FechaInicio ASC LIMIT 2 " ;
																				$r_calendario=$dbo->query($sql_calendario);
																				if($dbo->rows($r_calendario)<2){
																					echo "<strong>No es posible registrar el trimestre ya que no no hay creados mas cursos despues de esta fecha</strong>";
																				}
																				else{
																					while($row_calendario = $dbo->fetchArray($r_calendario)){
																						$sql_siguientes = "SELECT IDCursoHorario
																																		FROM CursoHorario
																																		WHERE IDClub = '".$_GET["IDClub"]."' and IDCursoEntrenador = '".$datos->IDCursoEntrenador."'
																																		and IDCursoNivel = '".$datos->IDCursoNivel."' and IDCursoSede = '".$datos->IDCursoSede."'
																																		and IDCursoTipo = '".$datos->IDCursoTipo."' and HoraDesde = '".$datos->HoraDesde."'";
																						$r_siguientes = $dbo->query($sql_siguientes);
																						while($row_siguiente = $dbo->fetchArray($r_siguientes)){
																							//Verifico si quedan cupos
																							$inscritos = SIMWebServiceApp::get_curso_inscritos($_GET["IDClub"],$row_siguiente["IDCursoHorario"],$row_calendario["IDCursoCalendario"],$datos->HoraDesde);
																							if($inscritos<=$row_siguiente["Cupo"]){
																								echo "<strong>No es posible registrar el trimestre en la fecha ".$row_calendario["FechaInicio"]."  ya tiene el cupo completo.</strong>";
																							}
																							else{
																								$array_id_calendario[]=$row_calendario["IDCursoCalendario"];
																								echo "<br>" . $row_calendario["FechaInicio"] . " al " . $row_calendario["FechaFin"];
																								$array_id_trimestre[]=$row_siguiente["IDCursoHorario"];
																							}

																						}
																						if(count($array_id_calendario)>0){
																							$id_calendario=implode(",",$array_id_calendario);
																						}
																					}
																				}
																				?>
																		 </td>
																	 -->
																	 </tr>
																 </table>


															 </td>
														 </tr>
														 <tr>
															 <td colspan="2" align="center">
																 <input type="submit" class="boton_personalizado_pagar" id="" value="PAGAR"/>
															 </td>
														 </tr>


						<tr>
							<td align="center" colspan="2">
								<input type="hidden" name="IDCursoHorario"  id="IDCursoHorario" value="<?php echo $_GET["IDCursoHorario"];  ?>" />
								<input type="hidden" name="IDCursoCalendario"  id="IDCursoCalendario" value="<?php echo $_GET["calendario"];  ?>" />
								<input type="hidden" name="Cupos"  id="Cupos" value="<?php echo $_GET["cupos"];  ?>" />
								<input type="hidden" name="Valor"  id="Valor" value="<?php echo $_GET["vm"];  ?>" />
								<input type="hidden" name="ValorTrimestre"  id="ValorTrimestre" value="<?php echo $_GET["vt"];  ?>" />
								<input type="hidden" name="HoraDesde"  id="HoraDesde" value="<?php echo $_GET["horadesde"];  ?>" />
								<input type="hidden" name="IDSocio"  id="IDSocio" value="<?php echo $_GET["IDSocio"];  ?>" />
								<input type="hidden" name="action" id="action" value="insert" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $_GET["IDClub"];  ?>" />
								<input type="hidden" name="IDCursoCalendarioTrimestre" id="IDCursoCalendarioTrimestre" value="<?php echo $id_calendario;  ?>" />
						 </tr>
					</table>
				</form>


						</div>


        </div>




                  <?php include ("cmp/footerliga.php"); ?>
        </div>
