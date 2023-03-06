<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor ?> <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>"
    action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Categoria </label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formPopUp( "SeccionBeneficio" , "Nombre" , "Nombre" , "IDSeccionBeneficio" , $frm["IDSeccionBeneficio"] , "[Seleccione categoria]" , "popup mandatory" , "title = \"Categoria\""," and IDClub = '".SIMUser::get("club")."'" )?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
            <div class="col-sm-8">
                <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"];?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Introduccion </label>
            <div class="col-sm-8">
                <textarea id="Introduccion" name="Introduccion" cols="10" rows="5" class="col-xs-12 mandatory" title="Introduccion"><?php echo $frm["Introduccion"]; ?></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion Corta </label>
            <div class="col-sm-8">
                <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
            </div>
        </div>
    </div>
    <div class="form-group first"> Descripcion <div class="col-sm-12"> <?php
                    $oCuerpo = new FCKeditor( "DescripcionHtml" ) ;
                    $oCuerpo->BasePath = "js/fckeditor/";
                    $oCuerpo->Height = 400;
                    //$oCuerpo->EnterMode = "p";
                    $oCuerpo->Value =  $frm["DescripcionHtml"];
                    $oCuerpo->Create() ;
                  ?> </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono </label>
            <div class="col-sm-8">
                <input type="number" id="Telefono" name="Telefono" placeholder="Telefono" class="col-xs-12 mandatory" title="Telefono" value="<?php echo $frm["Telefono"];?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pagina Web </label>
            <div class="col-sm-8">
                <input type="text" id="PaginaWeb" name="PaginaWeb" placeholder="Pagina Web" class="col-xs-12" title="Pagina Web" value="<?php echo $frm["PaginaWeb"];?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Icono Telefono </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["OcultarTelefono"] , 'OcultarTelefono' , "class='input mandatory'" ) ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Icono Web </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["OcultarPaginaWeb"] , 'OcultarPaginaWeb' , "class='input mandatory'" ) ?>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Latitud </label>
            <div class="col-sm-8">
                <input type="number" id="Latitud" name="Latitud" placeholder="Latitud" class="col-xs-12 mandatory" title="Latitud" value="<?php echo $frm["Latitud"];?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Longitud </label>
            <div class="col-sm-8">
                <input type="text" id="Longitud" name="Longitud" placeholder="Longitud" class="col-xs-12" title="Longitud" value="<?php echo $frm["Longitud"];?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Mapa </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["OcultarMapa"] , 'OcultarMapa' , "class='input mandatory'" ) ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Boton Ruta </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["OcultarBotonRuta"] , 'OcultarBotonRuta' , "class='input mandatory'" ) ?>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>
            <div class="col-sm-8">
                <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>
            <div class="col-sm-8">
                <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo $frm["FechaFin"] ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>
            <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar Notificación ? </label>
            <div class="col-sm-8"> <?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , "" , "NotificarPush" , "title=\"NotificarPush\"" )?> </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto 1 </label>
            <input name="Foto1" id=file class="" title="Foto1" type="file" size="25" style="font-size: 10px">
            <div class="col-sm-8">
                <? if (!empty($frm["Foto1"])) {
												echo "<img src='".CLASIFICADOS_ROOT.$frm["Foto1"]."' >";
												?>
                <a href="<? echo $script." .php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
											}// END if
											?>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pdf adjunto 1 </label>
            <div class="col-sm-8"> <?php
				$ruta_adjunto1file = string;
				if($frm["Adjunto1File"])
				{

					if(strstr(strtolower($frm["Adjunto1File"]),"http://"))
						$ruta_adjunto1file = $frm["Adjunto1File"];
					else
						$ruta_adjunto1file = CLASIFICADOS_ROOT.$frm["Adjunto1File"];
					?> <a target="_blank" href="<?php echo $ruta_adjunto1file; ?>"><?php echo $frm["Adjunto1File"] ?></a>
                <a href="<? echo $script." .php?action=DelDocNot&cam=Adjunto1File&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a> <?php
				}
				else
				{
				?> <input type="file" name="Adjunto1Documento" id="Adjunto1Documento" class="popup" title="Noticia Documento"> <?php
				}
				?>
            </div>
        </div>
    </div>
    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                <i class="ace-icon fa fa-check bigger-110"></i> <?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?> </button>
            <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[ $key ] ?>" />
            <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[ $key ] ?>" />
        </div>
    </div>
</form>