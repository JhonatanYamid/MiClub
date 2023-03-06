<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">



                           <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

										<div class="col-sm-8">
										  <!--
                                          <select name = "IDSocio" id="IDSocio" <?php if($_GET["action"]!= "add") echo "disabled"; ?>>
										    <option value=""></option>
										    <?php
										$sql_socio_club = "Select * From Socio Where IDClub = '".SIMUser::get("club")."' Order by Apellido Asc";
										$qry_socio_club = $dbo->query($sql_socio_club);
										while ($r_socio = $dbo->fetchArray($qry_socio_club)): ?>
										    <option value="<?php echo $r_socio["IDSocio"]; ?>" <?php if($r_socio["IDSocio"]==$frm["IDSocio"]) echo "selected";  ?>><?php echo utf8_decode($r_socio["Apellido"] . " " .$r_socio["Nombre"]); ?></option>
										    <?php
										 	endwhile;    ?>
									      </select>
                                          -->
                                          	<?php
										$sql_socio_club = "Select * From Socio Where IDSocio = '".$frm["IDSocio"]."'";
										$qry_socio_club = $dbo->query($sql_socio_club);
										$r_socio = $dbo->fetchArray($qry_socio_club); ?>

                                          	<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if($_GET["action"]!= "add") echo "readonly"; ?> value="<?php echo utf8_encode($r_socio["Apellido"] . " " .$r_socio["Nombre"]) ?>" >
											<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
										</div>
								</div>

                                <div  class="col-xs-12 col-sm-6">
                                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Industria </label>

                                            <div class="col-sm-8"><?php echo SIMHTML::formPopUp( "Industria" , "Nombre" , "Nombre" , "IDIndustria" , $frm["IDIndustria"] , "[Seleccione Industria]" , "popup mandatory" , "title = \"Industria\""," " )?></div>
                                        </div>

							</div>







                            <div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre de la empresa </label>
										<div class="col-sm-8"><input type="text" id="NombreEmpresa" name="NombreEmpresa" placeholder="Nombre Empresa" class="col-xs-12 mandatory" title="Nombre Empresa" value="<?php echo $frm["NombreEmpresa"];?>"></div>
								</div>
                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar Empresa </label>
                    <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["PublicarEmpresa"] , "PublicarEmpresa" , "title=\"Publicar Empresa\"" )?></div>
								</div>
							</div>

              <div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cargo a publicar </label>
										<div class="col-sm-8"><input type="text" id="Cargo" name="Cargo" placeholder="Cargo" class="col-xs-12 mandatory" title="Cargo" value="<?php echo $frm["Cargo"];?>"></div>
								</div>

                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ciudad </label>
										<div class="col-sm-8"><input type="text" id="Ciudad" name="Ciudad" placeholder="Ciudad" class="col-xs-12 mandatory" title="Ciudad" value="<?php echo $frm["Ciudad"];?>"></div>
								</div>
							</div>

              <div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Encargado </label>
										<div class="col-sm-8"><input type="text" id="NombreEncargado" name="NombreEncargado" placeholder="NombreEncargado" class="col-xs-12 mandatory" title="NombreEncargado" value="<?php echo $frm["NombreEncargado"];?>"></div>
								</div>

                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Correo Contacto </label>
										<div class="col-sm-8"><input type="email" id="CorreoContacto" name="CorreoContacto" placeholder="CorreoContacto" class="col-xs-12 mandatory" title="CorreoContacto" value="<?php echo $frm["CorreoContacto"];?>"></div>
								</div>
							</div>



              <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio Publicacion</label>

										<div class="col-sm-8">
											<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin Publicacion</label>

										<div class="col-sm-8">
											<input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo $frm["FechaFin"] ?>" >
										</div>
								</div>

							</div>


              <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Contrato</label>

										<div class="col-sm-8">
											<?php echo SIMHTML::formPopupArray( SIMResources::$tipo_contrato  ,  $frm["TipoContrato"] , "TipoContrato" ,  "Seleccione tipo" , "form-control"  ); ?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado</label>

										<div class="col-sm-8">
											<?php echo SIMHTML::formPopUp( "EstadoOferta" , "Nombre" , "Nombre" , "IDEstadoOferta" , $frm["IDEstadoOferta"] , "[Seleccione estado]" , "popup mandatory" , "title = \"Estado\"" )?>
										</div>
								</div>
							</div>

              <div  class="form-group first ">

                  <div  class="col-xs-12 col-sm-6">
                      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion del cargo </label>

                      <div class="col-sm-8">
                                          <textarea id="DescripcionCargo" name="DescripcionCargo" cols="10" rows="5" class="col-xs-12 " title="Descripcion Cargo"><?php echo $frm["DescripcionCargo"]; ?></textarea>
                                          </div>
                  </div>

                  <div  class="col-xs-12 col-sm-6">
                      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Comentarios o condiciones adicionales </label>

                      <div class="col-sm-8">
                                          <textarea id="ComentarioAdicional" name="ComentarioAdicional" cols="10" rows="5" class="col-xs-12 " title="Comentario Adicional"><?php echo $frm["ComentarioAdicional"]; ?></textarea>
                                          </div>
                  </div>

            </div>



							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[ $key ] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[ $key ] ?>" />
								</div>
							</div>
                           </form>
