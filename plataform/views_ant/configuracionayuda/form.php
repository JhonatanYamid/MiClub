<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get( "title" ))?>
        </h4>


    </div>

    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->


                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Configuración </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>

                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuarios: </label>
                                <div class="col-sm-8">
                                    <input type="text" id="AccionPush" name="AccionPush" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-push" title="número de derecho">
                                    <br>
                                    <a id="agregar_UsuariosPush" href="#">Agregar</a> | <a id="borrar_UsuariosPush" href="#">Borrar</a>
                                    <br>
                                    <select name="SocioInvitado[]" id="UsuariosNotifica" class="col-xs-8" multiple>
                                        <?php
										$item=1;
										$array_invitados=explode("|||",$frm["UsuariosPush"]);
										foreach($array_invitados as $id_invitado => $datos_invitado):
											if(!empty($datos_invitado)){
												$array_datos_invitados=explode("-",$datos_invitado);
												$item--;
												$IDSocioInvitacion=$array_datos_invitados[1];
												if($IDSocioInvitacion > 0):
													$nombre_socio = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocioInvitacion."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocioInvitacion."'" ));
													?>
											<option value="<?php echo "socio-".$IDSocioInvitacion; ?>"><?php echo $nombre_socio; ?></option>
                                        <?php
												endif;
											}
										endforeach;?>
                                    </select>
                                    <input type="hidden" name="UsuariosPush" id="UsuariosPush" value="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Correos para Notificar (Separados por coma ',') </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Correos" name="Correos" placeholder="Correos" class="col-xs-12 mandatory" title="Correos" value="<?php echo $frm["Correos"]; ?>">
                                </div>
                            </div>

                        </div>
                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje</label>

                                <div class="col-sm-8">
                                    <textarea rows="5" cols="50" id="Mensaje" name="Mensaje" class="input"><?php echo $frm["Mensaje"] ?></textarea>
                                </div>
                            </div>

                        </div>


                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
                                </button>


                            </div>
                        </div>

                    </form>
                </div>
            </div>




        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>