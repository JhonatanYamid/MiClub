<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



  <div class="form-group first ">



    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?> </label>

      <div class="col-sm-8"><?php echo SIMHTML::formPopUp("SeccionObjetosPerdidos", "Nombre", "Nombre", "IDSeccionObjetosPerdidos", $frm["IDSeccionObjetosPerdidos"], "[Seleccione categoria]", "popup mandatory", "title = \"Categoria\"", " and IDClub = '" . SIMUser::get("club") . "'") ?></div>
    </div>

  </div>







  <div class="form-group first ">
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
      <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
    </div>
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>
      <div class="col-sm-8">
        <?php
        $oCuerpo = new FCKeditor("Descripcion");
        $oCuerpo->BasePath = "js/fckeditor/";
        $oCuerpo->Height = 400;
        //$oCuerpo->EnterMode = "p";
        $oCuerpo->Value =  $frm["Descripcion"];
        $oCuerpo->Create();
        ?>
      </div>
    </div>

  </div>


  <div class="form-group first ">

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?> </label>

      <div class="col-sm-8">
        <input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="<?php echo $frm["FechaInicio"] ?>">
      </div>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?> </label>

      <div class="col-sm-8">
        <input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" value="<?php echo $frm["FechaFin"] ?>">
      </div>
    </div>

  </div>

  <div class="form-group first ">
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 1 </label>
      <input name="Foto1" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 1" type="file" size="25" style="font-size: 10px">
      <div class="col-sm-8">
        <? if (!empty($frm["Foto1"])) {
          echo "<img src='" . OBJETOSPERDIDOS_ROOT . $frm["Foto1"] . "' >";
        ?>
          <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
        <?
        } // END if
        ?>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 2 </label>
      <input name="Foto2" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 2" type="file" size="25" style="font-size: 10px">
      <div class="col-sm-8">
        <? if (!empty($frm["Foto2"])) {
          echo "<img src='" . OBJETOSPERDIDOS_ROOT . $frm["Foto2"] . "' >";
        ?>
          <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto2]&campo=Foto2&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
        <?
        } // END if
        ?>
      </div>
    </div>

  </div>

  <div class="form-group first ">
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 3</label>
      <input name="Foto3" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 3" type="file" size="25" style="font-size: 10px">
      <div class="col-sm-8">
        <? if (!empty($frm["Foto3"])) {
          echo "<img src='" . OBJETOSPERDIDOS_ROOT . $frm["Foto3"] . "' >";
        ?>
          <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto3]&campo=Foto3&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
        <?
        } // END if
        ?>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 4</label>
      <input name="Foto4" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 4" type="file" size="25" style="font-size: 10px">
      <div class="col-sm-8">
        <? if (!empty($frm["Foto4"])) {
          echo "<img src='" . OBJETOSPERDIDOS_ROOT . $frm["Foto4"] . "' >";
        ?>
          <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto4]&campo=Foto4&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
        <?
        } // END if
        ?>
      </div>
    </div>

  </div>

  <div class="form-group first ">
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 5</label>
      <input name="Foto5" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 5" type="file" size="25" style="font-size: 10px">
      <div class="col-sm-8">
        <? if (!empty($frm["Foto5"])) {
          echo "<img src='" . OBJETOSPERDIDOS_ROOT . $frm["Foto5"] . "'  >";
        ?>
          <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto5]&campo=Foto5&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
        <?
        } // END if
        ?>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 6</label>
      <input name="Foto6" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> 6" type="file" size="25" style="font-size: 10px">
      <div class="col-sm-8">
        <? if (!empty($frm["Foto6"])) {
          echo "<img src='" . OBJETOSPERDIDOS_ROOT . $frm["Foto6"] . "' >";
        ?>
          <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto6]&campo=Foto6&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
        <?
        } // END if
        ?>
      </div>
    </div>

  </div>

  <div class="form-group first ">
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'EnviarNotificación', LANGSESSION); ?> ? </label>
      <div class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?></div>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PublicarFotos', LANGSESSION); ?> ? </label>
      <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PublicarFotos"], 'PublicarFotos', "class='input mandatory'") ?>
    </div>

  </div>



  <div class="widget-header widget-header-large">
    <h3 class="widget-title grey lighter">
      <i class="ace-icon fa fa-credit-card green"></i>
      <?= SIMUtil::get_traduccion('', '', 'Entregadeobjeto', LANGSESSION); ?>

    </h3>
  </div>



  <div class="form-group first ">

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>
      <?php echo SIMHTML::formPopUp("EstadoObjetosPerdidos", "Nombre", "Nombre", "IDEstadoObjetosPerdidos", $frm["IDEstadoObjetosPerdidos"], "[Seleccione estado]", "popup mandatory", "title = \"Estado\"") ?>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>

      <div class="col-sm-8">
        <!--
                                  <select name = "IDSocio" id="IDSocio" <?php if ($_GET["action"] != "add") echo "disabled"; ?>>
                <option value=""></option>
                <?php
                $sql_socio_club = "Select * From Socio Where IDClub = '" . SIMUser::get("club") . "' Order by Apellido Asc";
                $qry_socio_club = $dbo->query($sql_socio_club);
                while ($r_socio = $dbo->fetchArray($qry_socio_club)) : ?>
                <option value="<?php echo $r_socio["IDSocio"]; ?>" <?php if ($r_socio["IDSocio"] == $frm["IDSocio"]) echo "selected";  ?>><?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]); ?></option>
                <?php
                endwhile;    ?>
                </select>
                                  -->
        <?php
        $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
        $qry_socio_club = $dbo->query($sql_socio_club);
        $r_socio = $dbo->fetchArray($qry_socio_club); ?>

        <input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
        <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">
      </div>
    </div>
  </div>


  <div class="form-group first ">
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'TipoReclamante', LANGSESSION); ?> </label>

      <div class="col-sm-8">
        <select name="TipoReclamante" id="TipoReclamante" class="form-control">
          <option value="">[Seleccione]</option>
          <option value="Particular" <?php if ($frm["TipoReclamante"] == "Particular") echo "selected"; ?>><?= SIMUtil::get_traduccion('', '', 'Particular', LANGSESSION); ?></option>
          <option value="Socio" <?php if ($frm["TipoReclamante"] == "Socio") echo "selected"; ?>><?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?></option>
        </select>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'NombreParticular', LANGSESSION); ?></label>
      <div class="col-sm-8"><input type="text" id="NombreParticular" name="NombreParticular" placeholder="<?= SIMUtil::get_traduccion('', '', 'NombreParticular', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'NombreParticular', LANGSESSION); ?>" value="<?php echo $frm["NombreParticular"]; ?>"></div>
    </div>
  </div>

  <div class="form-group first ">

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DocumentoParticular', LANGSESSION); ?></label>
      <div class="col-sm-8"><input type="text" id="DocumentoParticular" name="DocumentoParticular" placeholder="<?= SIMUtil::get_traduccion('', '', 'DocumentoParticular', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'DocumentoParticular', LANGSESSION); ?>" value="<?php echo $frm["DocumentoParticular"]; ?>"></div>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipoDocumentoParticular', LANGSESSION); ?> </label>

      <div class="col-sm-8">
        <?php echo SIMHTML::formPopUp("TipoDocumento", "Nombre", "Nombre", "IDTipoDocumento", $frm["IDTipoDocumento"], "[Seleccione categoria]", "popup ", "title = \"Tipo Doc\"", "") ?>
      </div>
    </div>
  </div>

  <div class="form-group first ">
    <?php $fechaEntrega = explode(" ", $frm["FechaEntrega"]);

    ?>
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?> </label>

      <div class="col-sm-8">
        <input type="text" id="FechaEntrega1" name="FechaEntrega1" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaEntrega', LANGSESSION); ?>" value="<?php echo $fechaEntrega[0]; ?>">
      </div>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'HoraEntrega', LANGSESSION); ?> </label>

      <div class="col-sm-8">
        <input type="time" id="HoraEntrega" name="HoraEntrega" placeholder="<?= SIMUtil::get_traduccion('', '', 'HoraEntrega', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'HoraEntrega', LANGSESSION); ?>" value="<?php echo $fechaEntrega[1]; ?>">
      </div>
    </div>

  </div>




  <div class="form-group first ">
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FotoEntrega', LANGSESSION); ?> 1 </label>
      <input name="FotoEntrega1" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'FotoEntrega', LANGSESSION); ?> 1" type="file" size="25" style="font-size: 10px">
      <div class="col-sm-8">
        <? if (!empty($frm["FotoEntrega1"])) {
          echo "<img src='" . OBJETOSPERDIDOS_ROOT . $frm["FotoEntrega1"] . "' >";
        ?>
          <a href="<? echo $script . ".php?action=delfoto&foto=$frm[FotoEntrega1]&campo=FotoEntrega1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
        <?
        } // END if
        ?>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FotoEntrega', LANGSESSION); ?> 2 </label>
      <input name="FotoEntrega2" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'FotoEntrega', LANGSESSION); ?> 2" type="file" size="25" style="font-size: 10px">
      <div class="col-sm-8">
        <? if (!empty($frm["FotoEntrega2"])) {
          echo "<img src='" . OBJETOSPERDIDOS_ROOT . $frm["FotoEntrega2"] . "' >";
        ?>
          <a href="<? echo $script . ".php?action=delfoto&foto=$frm[FotoEntrega2]&campo=FotoEntrega2&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
        <?
        } // END if
        ?>
      </div>
    </div>

  </div>












  <div class="form-group first ">
    <div class="col-xs-12 col-sm-6">
      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ObservacionEspecial', LANGSESSION); ?> </label>

      <div class="col-sm-8">
        <textarea id="Observaciones" name="Observaciones" cols="10" rows="5" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'ObservacionEspecial', LANGSESSION); ?>"><?php echo $frm["Observaciones"]; ?></textarea>
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
      <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
      <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
    </div>
  </div>
</form>