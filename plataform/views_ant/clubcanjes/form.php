<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>
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
                    <!-- PAGE CONTENT BEGINS -->


                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">



                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Activo', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "class='input mandatory'") ?>
                                </div>
                            </div>



                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DiasMinimoCanje', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="DiaMinimo" name="DiaMinimo" placeholder="<?= SIMUtil::get_traduccion('', '', 'DiasMinimo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'DiasMinimo', LANGSESSION); ?>" value="<?php echo $frm["DiaMinimo"]; ?>">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'DiasMaximocanje', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="DiaMaximo" name="DiaMaximo" placeholder="<?= SIMUtil::get_traduccion('', '', 'DiasMaximo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'DiasMaximo', LANGSESSION); ?>" value="<?php echo $frm["DiaMaximo"]; ?>">
                                </div>
                            </div>



                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'SecuenciaDias', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <input type="number" id="SecuenciaDia" name="SecuenciaDia" placeholder="<?= SIMUtil::get_traduccion('', '', 'SecuenciaDias', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'SecuenciaDias', LANGSESSION); ?>" value="<?php echo $frm["SecuenciaDia"]; ?>">
                                    <?= SIMUtil::get_traduccion('', '', '(Ejemplo:SiMindiases5ymax20ysecuenciaes5,mostraráenellistadodelapp:5,10,15y20)', LANGSESSION); ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CorreoNotificacionClub', LANGSESSION); ?>: </label>

                                <div class="col-sm-8">
                                    <input type="text" id="CorreoNotificacion" name="CorreoNotificacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'CorreoNotificacionClub', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'CorreoNotificacionClub', LANGSESSION); ?>n" value="<?php echo $frm["CorreoNotificacion"]; ?>">
                                </div>
                            </div>


                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermitealsocioeliminarlasolicituddecanjeenelApp', LANGSESSION); ?> </label>

                                <div class="col-sm-8">
                                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteEliminar"], 'PermiteEliminar', "class='input'") ?>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tiempoquesepermiteconanticipacioneliminarelcanje', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <input type="number" name="TiempoEliminarCanje" id="TiempoEliminarCanje" class="col-xs-4 " title="<?= SIMUtil::get_traduccion('', '', 'Tiempoquesepermiteconanticipacioneliminarelcanje', LANGSESSION); ?>" value="<?php echo $frm["TiempoEliminarCanje"] ?>">
                                    <select name="MedicionTiempoEliminarCanje" id="MedicionTiempoEliminarCanje" class="" title="Medicion Tiempo Eliminar Canje">
                                        <option value=""></option>
                                        <option value="Dias" <?php if ($frm["MedicionTiempoEliminarCanje"] == "Dias") echo "selected";  ?>>Dias</option>
                                    </select>
                                </div>
                            </div>


                        </div>



                        <div class="form-group first">


                            <?= SIMUtil::get_traduccion('', '', 'MensajeClubDestino', LANGSESSION); ?>

                            <div class="col-sm-12">
                                <?php
                                $oCuerpo = new FCKeditor("MensajeClubDestino");
                                $oCuerpo->BasePath = "js/fckeditor/";
                                $oCuerpo->Height = 400;
                                //$oCuerpo->EnterMode = "p";
                                $oCuerpo->Value =  $frm["MensajeClubDestino"];
                                $oCuerpo->Create();
                                ?>
                            </div>


                        </div>



                        <div class="widget-header widget-header-large">
                            <h3 class="widget-title grey lighter">
                                <i class="ace-icon fa fa-globe green"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Clubesconconvenioparacanjes', LANGSESSION); ?>
                            </h3>
                        </div>


                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-12">


                                <div class="col-sm-12">

                                    <?php
                                    // Consulto del listado de clubes del club
                                    $sql_clubes_canje = $dbo->query("select * from DetalleClubCanje where IDClub = '" . SIMUser::get("club") . "' and IDCLubCanje = '" . $frm[$key] . "'");
                                    while ($r_club_canje = $dbo->object($sql_clubes_canje)) {
                                        $club_canje[] = $r_club_canje->IDListaClubes;
                                        $club_canje_correo[$r_club_canje->IDListaClubes] = $r_club_canje->CorreoNotificacion;
                                        $club_canje_mensaje[$r_club_canje->IDListaClubes] = $r_club_canje->MensajeAlCrearCanje;
                                        $MaximoDias[$r_club_canje->IDListaClubes] = $r_club_canje->MaximoDias;
                                    }
                                    $arrayop = array();
                                    // consulto los clubes
                                    $query_listaclubes = $dbo->query("Select * from ListaClubes Where Publicar = 'S' Order by Nombre");
                                    while ($r = $dbo->object($query_listaclubes)) {
                                        $nombre_club = utf8_encode($r->Nombre) . "(" . $dbo->getFields("Pais", "Nombre", "IDPais = '" . $r->IDPais . "'") . ")";
                                        $arrayclubes[$nombre_club] = $r->IDListaClubes;
                                    }


                                    echo formCheckGroup_especial($arrayclubes, $club_canje, "ClubCanje[]", "&nbsp;", "", $club_canje_correo, $club_canje_mensaje, $MaximoDias); ?>

                                    <?php
                                    function formCheckGroup_especial($options, $selection, $name, $sep = "", $attrs = "", $club_canje_correo, $club_canje_mensaje, $MaximoDias)
                                    {



                                        $checkgroup = "";

                                        $checkgroup = "<table id='simple-table' class='table table-striped table-bordered table-hover'><tr><td>";
                                        $columnas = 0;
                                        foreach ($options as $key => $val) {
                                            $columnas++;
                                            $checkgroup .= "<label class=\"checkgroup\"><input type=\"checkbox\" name=\"ClubCanje[" . $val . "]" . "\" id=\"" . $name . "\" value=\"" . $val . "\" " . $attrs;

                                            if (!empty($selection))
                                                $checkgroup .= (in_array($val, $selection)) ? " checked" : "";

                                            $checkgroup .= "> " . utf8_decode($key);
                                            $checkgroup .= "</label>" . $sep;

                                            $checkgroup .= "<br><label class=\"checkgroup\"><input type=\"text\" name=\"CorreoNotificacionDestino[" . $val . "]" . "\" id=\"CorreoNotificacionDestino" . $name . "\" value=\"" . $club_canje_correo[$val] . "\" " . $attrs . " placeholder=\"Correo notificacion Destino\" > " . "</label>";
                                            $checkgroup .= "<br><label class=\"checkgroup\"><input type=\"text\" name=\"MensajeAlCrearCanje[" . $val . "]" . "\" id=\"MensajeAlCrearCanje" . $name . "\" value=\"" . $club_canje_mensaje[$val] . "\" " . $attrs . " placeholder=\"Mensaje Al Crear Canje\" > " . "</label>";
                                            $checkgroup .= "<br><label class=\"checkgroup\"><input type=\"text\" name=\"MaximoDias[" . $val . "]" . "\" id=\"MaximoDias" . $name . "\" value=\"" . $MaximoDias[$val] . "\" " . $attrs . " placeholder=\"Maximo Dias \" > " . "</label>";







                                            $checkgroup .= "</td>";

                                            if ($columnas == 4) :
                                                $checkgroup .= "</tr><tr><td>";
                                                $columnas = 0;
                                            else :
                                                $checkgroup .= "<td>";
                                            endif;
                                        }
                                        $checkgroup .= "</tr></table>";

                                        return $checkgroup;
                                    }
                                    ?>



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
                </div>
            </div>




        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>