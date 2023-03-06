<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>

            <div class="col-sm-8">
                <?php
                $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                $qry_socio_club = $dbo->query($sql_socio_club);
                $r_socio = $dbo->fetchArray($qry_socio_club); ?>

                <input type="text" id="Accion" name="Accion" placeholder="Nombre" class="col-xs-12 autocomplete-ajax" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>" value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
                <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio"  title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">
                 
            </div>
        </div> 

        <!-- prueba --> 

         <div class="col-xs-12 col-sm-6">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Apartamento </label>

                        <div class="col-sm-8">
                            <input type="text" id="Accion" name="Accion" placeholder="Apartamento" class="col-xs-12 autocomplete-ajax_predio" title="apartamento">
                            <input type="hidden" name="IDSocios" value="" id="IDSocios"  title="Socio">
 
                        </div> 
                           </div> 
                           

<!-- -->

        <div class="col-xs-12 col-sm-6">
        <br>
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <select name="IDTipoCorrespondencia" id="IDTipoCorrespondencia" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?>">
                    <option value=""></option>
                    <?php
                    $sql_cat_corresp = string;
                    $sql_cat_corresp = "Select * From TipoCorrespondencia Where IDClub = '" . SIMUser::get("club") . "' order by Nombre";
                    $qry_cat_corresp = $dbo->query($sql_cat_corresp);
                    while ($r_cat_corresp = $dbo->fetchArray($qry_cat_corresp)) : ?>
                        <option value="<?php echo $r_cat_corresp["IDTipoCorrespondencia"]; ?>" <?php if ($r_cat_corresp["IDTipoCorrespondencia"] == $frm["IDTipoCorrespondencia"]) echo "selected";  ?>><?php echo $r_cat_corresp["Nombre"]; ?></option>
                    <?php
                    endwhile;    ?>
                </select>

            </div>
        </div>


    </div>







    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'UsuarioCrea', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <select name="IDUsuarioCrea" id="IDUsuarioCrea" class="form-control mandatory" title="<?= SIMUtil::get_traduccion('', '', 'UsuarioCrea', LANGSESSION); ?>">
                    <option value=""></option>
                    <?php
                    $sql_usu = "Select * From Usuario Where IDClub = '" . SIMUser::get("club") . "' order by Nombre";
                    $qry_cat_corresp = $dbo->query($sql_usu);
                    while ($r_usu = $dbo->fetchArray($qry_cat_corresp)) : ?>
                        <option value="<?php echo $r_usu["IDUsuario"]; ?>" <?php if ($r_usu["IDUsuario"] == $frm["IDUsuarioCrea"]) echo "selected";  ?>><?php echo $r_usu["Nombre"]; ?></option>
                    <?php
                    endwhile;    ?>
                </select>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'UsuarioEntrega', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <select name="IDUsuarioEntrega" id="IDUsuarioEntrega" class="form-control " title="<?= SIMUtil::get_traduccion('', '', 'UsuarioEntrega', LANGSESSION); ?>">
                    <option value=""></option>
                    <?php
                    $sql_usu = "Select * From Usuario Where IDClub = '" . SIMUser::get("club") . "' order by Nombre";
                    $qry_cat_corresp = $dbo->query($sql_usu);
                    while ($r_usu = $dbo->fetchArray($qry_cat_corresp)) : ?>
                        <option value="<?php echo $r_usu["IDUsuario"]; ?>" <?php if ($r_usu["IDUsuario"] == $frm["IDUsuarioEntrega"]) echo "selected";  ?>><?php echo $r_usu["Nombre"]; ?></option>
                    <?php
                    endwhile;    ?>
                </select>

            </div>
        </div>

    </div>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Vivienda', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <input type="text" id="Vivienda" name="Vivienda" placeholder="<?= SIMUtil::get_traduccion('', '', 'Vivienda', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Vivienda', LANGSESSION); ?>" value="<?php echo utf8_encode($frm["Vivienda"]); ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Destinatario', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <input type="text" id="Destinatario" name="Destinatario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Destinatario', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Destinatario', LANGSESSION); ?>" value="<?php echo utf8_encode($frm["Destinatario"]); ?>">

            </div>
        </div>

    </div>
 
    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaRecepcion', LANGSESSION); ?> </label>
            <?php $fechaRecepcion = explode(" ", $frm["FechaRecepcion"]) ?>
            <div class="col-sm-4">
 
 

                <input type="date" id="FechaRecepcion" name="FechaRecepcion" min="<?php $hoy= date("Y-m-d"); echo $hoy;?>" readonly placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaRecepcion', LANGSESSION); ?>" title="<?= SIMUtil::get_traduccion('', '', 'FechaRecepcion', LANGSESSION); ?>" value="<?php
                if(empty($fechaRecepcion[0])){
                echo $hoy;
                }else{ echo $fechaRecepcion[0]; 
                
                }?>">

            </div>
            <div class="col-sm-4">
                <input type="time" name="HoraFechaRecepcion" id="HoraFechaRecepcion" value="<?php echo $fechaRecepcion[1] ?>" required>
            </div>
        </div>
 
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?> </label>
            <?php $fechaEntrega = explode(" ", $frm["FechaEntrega"]) ?>
            <div class="col-sm-4">
                <input type="date" id="FechaEntrega" name="FechaEntrega" min="<?php $hoy= date("Y-m-d"); echo $hoy;?>" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?>"   title="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?>" value="<?php echo $fechaEntrega[0] ?>">
            </div>
 
            <div class="col-sm-4">
              <input type="time" name="HoraFechaEntrega" min="18:00" max="21:00"   value="<?php echo $fechaEntrega[1] ?>" required />
 
            </div>
        </div>

    </div> 

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'EntregadoA', LANGSESSION); ?></label>

            <div class="col-sm-8">
                <input type="text" id="EntregadoA" name="EntregadoA" placeholder="<?= SIMUtil::get_traduccion('', '', 'EntregadoA', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'EntregadoA', LANGSESSION); ?>" value="<?php echo utf8_encode($frm["EntregadoA"]); ?>">
            </div>
        </div>


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?> </label>
            <div class="col-sm-8">

                <? if (!empty($frm[Archivo])) { ?>
                    <a target="_blank" href="<?php echo CORRESPONDENCIA_ROOT . $frm[Archivo] ?>"><?php echo $frm[Archivo]; ?></a>
                    <a href="<? echo $script . " .php?action=delfoto&doc=$frm[Archivo]&campo=Archivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="Archivo" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">
            </div>
        </div>




    </div>


    <div class="form-group first ">






        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <?php echo SIMHTML::formPopUp("CorrespondenciaEstado", "Nombre", "Nombre", "IDCorrespondenciaEstado", $frm["IDCorrespondenciaEstado"], "[Seleccione el estado]", "form-control", "title = \"Estado\"") ?>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Observaciones', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <textarea name="Observaciones" id="Observaciones" cols="30" rows="10"><?php echo $frm["Observaciones"] ?></textarea>

            </div>
        </div>


    </div>
    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">

            <i class="ace-icon fa fa-info-circle green"></i>
          Codigo de barra correspondencia recibida
        </h3>
    </div>
    
    <div class="form-group first ">
					<div class="col-xs-12 col-sm-6">
						<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'CodigoBarras', LANGSESSION); ?> </label>
						<div class="col-sm-8">
							<? $idclub=SIMUser::get("club"); if (!empty($frm[CodigoBarraCorrespondencia]))
								echo "<img src='" . CORRESPONDENCIA_ROOT . "$frm[CodigoBarraCorrespondencia]'>"; ?>
								
					 		<a href="./views/correspondencia/codigosbarraspdf.php?IDClub=<? echo $idclub ?>&IDCorrespondencia=<?php echo $frm[$key] ?>"  target="_blank" class="btn btn-sm btn-primary" style="background-color:#5D9732">Exportar</a>
						</div>
					</div>
				</div>
				
				
    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            <?= SIMUtil::get_traduccion('', '', 'SoloparalosserviciosPublicos', LANGSESSION); ?>
        </h3>
    </div>


    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'NumerodeCliente', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <input type="text" id="NumeroCliente" name="NumeroCliente" placeholder="<?= SIMUtil::get_traduccion('', '', 'NumerodeCliente', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'NumerodeCliente', LANGSESSION); ?>" value="<?php echo utf8_encode($frm["NumeroCliente"]); ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Medidor', LANGSESSION); ?> </label>

            <div class="col-sm-8">

                <input type="text" id="Medidor" name="Medidor" placeholder="<?= SIMUtil::get_traduccion('', '', 'Medidor', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Medidor', LANGSESSION); ?>" value="<?php echo utf8_encode($frm["Medidor"]); ?>">

            </div>
        </div>

    </div>




    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                    else echo $frm["IDClub"];  ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
            </button>


        </div>
    </div>

</form>

	 
			
