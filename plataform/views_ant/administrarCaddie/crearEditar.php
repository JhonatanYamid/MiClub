<form class="form-horizontal formvalida" role="form" method="post" id="frmadministrarCaddie" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-users green"></i>
            Datos Basicos
        </h3>
    </div>


    <div  class="form-group first ">

        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombres </label>

            <div class="col-sm-8">
                <input type="text" id="Nombre" name="nombre" placeholder="nombre" class="col-xs-12 mandatory" title="nombre" value="<?php echo utf8_encode($frm["nombre"]); ?>" >
            </div>
        </div>

        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1" > Apellidos </label>

            <div class="col-sm-8">
                <input type="text" id="apellido" name="apellido" placeholder="apellido" class="col-xs-12 mandatory" title="apellido" value="<?php echo utf8_encode($frm["apellido"]); ?>" >
            </div>
        </div>

    </div>

    <div  class="form-group first ">

        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Número de Documento </label>

            <div class="col-sm-8">
                <input type="text" id="numeroDocumento" name="numeroDocumento" placeholder="Número de Documento" class="col-xs-12 mandatory" title="número de documento" value="<?php echo $frm["numeroDocumento"]; ?>" >
            </div>
        </div>

        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Codigo </label>

            <div class="col-sm-8">
                <input type="text" id="Codigo" name="Codigo" placeholder="Codigo" class="col-xs-12" title="Codigo" value="<?php echo $frm["Codigo"]; ?>" >
            </div>
        </div>



    </div>


    <div  class="form-group first ">

      <div  class="col-xs-12 col-sm-6">
          <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Categoria </label>

          <div class="col-sm-8">
              <?php
                  echo SIMHTML::formPopUp("CategoriaCaddie", "nombre", "nombre", "IDCategoriaCaddie", $frm["IDCategoriaCaddie"], "[Seleccione la categoria]", "form-control mandatory", "title = \"categoria caddie\"", " AND IDClub = " . SIMUser::get("club")) ?>
          </div>
      </div>

        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto </label>

            <div class="col-sm-8">
                <?php
                if (!empty($frm[foto])) {
                    $foto = $frm[foto];
                    $foto = str_replace("_", "jsalcm", $foto);
                    echo "<img src='" . CADDIE_ROOT . "$frm[foto]' width=55 >";
                    ?>
                <a href="<?php echo $script . ".php?action=delfoto&foto=".$foto."&campo=foto&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                    <?php
                }// END if
                ?>
                <input name="foto" id=file class="" title="Foto" type="file" size="25" style="font-size: 10px">
            </div>
        </div>

    </div>


    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <input type="hidden" name="fotoAnterior" id="fotoAnterior" value="<?php echo $foto ?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"]))
                    echo SIMUser::get("club");
                else
                    echo $frm["IDClub"];
                ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
                <i class="ace-icon fa fa-check bigger-110"></i>
<?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
            </button>
        </div>
    </div>
</form>







</form>
