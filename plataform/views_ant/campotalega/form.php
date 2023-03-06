
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Etiquetadelcampo', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="EtiquetaCampo" name="EtiquetaCampo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Etiquetadelcampo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Etiquetadelcampo', LANGSESSION); ?>" value="<?php echo $frm["EtiquetaCampo"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Tipo"> <?= SIMUtil::get_traduccion('', '', 'TipodeCampo', LANGSESSION); ?>: </label>
                                <div class="col-sm-8">
                                    <select name = "TipoCampo" id="TipoCampo" class="mandatory">
                                        <?
                                            $options = array(
                                                "Texto en una línea" => "texto",
                                                "Texto en párrafo" => "textarea", 
                                                "Múltiples opciones" => "radio",
                                                "Casillas de verificación" => "checkbox",
                                                "Menú desplegable" => "select",
                                                "Número" => "number",
                                                "Titulo" => "titulo",
                                                "Fecha" => "date",
                                                "Hora" => "time",
                                                "Correo electrónico" => "email",
                                                "Imagen" => "imagen",
                                                "Imagen/Archivo" => "imagenarchivo"
                                            );
                                            foreach($options as $key => $val){              
                                                if($frm["TipoCampo"] == $val){
                                                    echo '<option value="' .$val. '" selected="selected">' .$key. '</option>';
                                                }else{
                                                    echo '<option value="' .$val. '">' .$key. '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
						</div>							
						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Valores', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12"><?php echo $frm["Valores"]; ?></textarea>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Obligatorio', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["Obligatorio"], 'Obligatorio', "class='input'") ?>
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="number" id="Orden" name="Orden" placeholder="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Orden', LANGSESSION); ?>" value="<?php echo $frm["Orden"]; ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Parametrodeenviopost', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="ParametroEnvioPost" name="ParametroEnvioPost" placeholder="<?= SIMUtil::get_traduccion('', '', 'Parametrodeenviopost', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Parametrodeenviopost', LANGSESSION); ?>" value="<?php echo $frm["ParametroEnvioPost"]; ?>"></div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["Activo"], 'Activo', "class='input mandatory'") ?>
								</div>
							</div>
						</div>
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>
								<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>