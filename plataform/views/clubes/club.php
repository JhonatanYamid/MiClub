<?php
include_once("js/fckeditor/fckeditor.php"); // FCKEditor 

$idClub = $frm[IDClub];
$clubHijos = SIMUtil::ObtenerHijosClubPadre($idClub);

?>

<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Datos Basicos
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

            <div class="col-sm-8">
                <input id=Nombre type=text size=25 name=Nombre class="col-xs-12 mandatory" title="Nombre" value="<?= $frm[Nombre] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Direccion </label>

            <div class="col-sm-8">
                <input id=Direccion type=text size=25 name=Direccion class="col-xs-12 mandatory" title="Direccion" value="<?= $frm[Direccion] ?>">
            </div>
        </div>

    </div>




    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>

            <div class="col-sm-8">
                <input id=Email type=email size=25 name=Email class="col-xs-12 mandatory" title="Email" value="<?= $frm["Email"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono </label>

            <div class="col-sm-8">
                <input id=Telefono type=text size=25 name=Telefono class="col-xs-12 mandatory" title="Telefono" value="<?= $frm["Telefono"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dise&ntilde;o </label>

            <div class="col-sm-8"><?php echo SIMHTML::formPopUp("Diseno", "Nombre", "Nombre", "IDDiseno", $frm["IDDiseno"], "[Seleccione el Dise&ntilde;o]", "popup mandatory", "title = \"Dise&ntilde;o\"") ?></div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clase adicional </label>

            <div class="col-sm-8">
                <input id=ClaseAdicional type=text size=25 name=ClaseAdicional class="col-xs-12" title="ClaseAdicional" value="<?= $frm[ClaseAdicional] ?>">
            </div>
        </div>

    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color 1 </label>

            <div class="col-sm-8">
                <input name="Color1" type="color" value="<?php if (empty($frm["Color1"])) {
                                                                echo "#FFFFFF";
                                                            } else {
                                                                echo $frm["Color1"];
                                                            }    ?>" />
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color 2 </label>

            <div class="col-sm-8">
                <input name="Color2" type="color" value="<?php if (empty($frm["Color2"])) {
                                                                echo "#FFFFFF";
                                                            } else {
                                                                echo $frm["Color2"];
                                                            }    ?>" />
            </div>
        </div>

    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Schema </label>

            <div class="col-sm-8">
                <input id="UrlSchema" type=text size=25 name="UrlSchema" class="col-xs-12" title="UrlSchema" value="<?= $frm["UrlSchema"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Club Padre </label>
            <div class="col-sm-8">
                <select name="IDClubPadre" id="IDClubPadre">
                    <option value=""></option>
                    <?php
                    $sql_club_padre = string;
                    $sql_club_padre = "Select * From Club Where IDClubPadre = 0 order by Nombre";
                    $qry_club_padre = $dbo->query($sql_club_padre);
                    while ($r_club_padre = $dbo->fetchArray($qry_club_padre)) : ?>
                        <option value="<?php echo $r_club_padre["IDClub"]; ?>" <?php if ($r_club_padre["IDClub"] == $frm["IDClubPadre"]) echo "selected";  ?>><?php echo $r_club_padre["Nombre"]; ?></option>
                    <?php
                    endwhile;
                    ?>
                </select>
            </div>
        </div>



    </div>





    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto </label>

            <div class="col-sm-8">
                <? if (!empty($frm[Foto])) {
                    echo "<img src='" . CLUB_ROOT . "$frm[Foto]' width=55 >";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[Foto]&campo=Foto&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="Foto" id=file class="col-xs-12" title="Foto" type="file" size="25" style="font-size: 10px">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto Dise&ntilde;o </label>

            <div class="col-sm-8">

                <? if (!empty($frm[FotoDiseno1])) {
                    echo "<img src='" . CLUB_ROOT . "$frm[FotoDiseno1]' width=55 >";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[FotoDiseno1]&campo=FotoDiseno1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

                <?
                } // END if
                ?>
                <input name="FotoDiseno1" id=file class="col-xs-12" title="Foto" type="file" size="25" style="font-size: 10px">


            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Logo Club App </label>

            <div class="col-sm-8">
                <? if (!empty($frm[FotoLogoApp])) {
                    echo "<img src='" . CLUB_ROOT . "$frm[FotoLogoApp]' width=55 >";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[FotoLogoApp]&campo=FotoLogoApp&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="FotoLogoApp" id=FotoLogoApp class="col-xs-12" title="FotoLogoApp" type="file" size="25" style="font-size: 10px">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Notificaciones Clasificados </label>

            <div class="col-sm-8">
                <input id=EmailNotificaciones type=text size=25 name=EmailNotificaciones class="col-xs-12" title="EmailNotificaciones" value="<?= $frm[EmailNotificaciones] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Notificaciones Objetos Perdidos </label>

            <div class="col-sm-8">
                <input id=EmailObjetosPerdidos type=text size=25 name=EmailObjetosPerdidos class="col-xs-12" title="EmailObjetosPerdidos" value="<?= $frm["EmailObjetosPerdidos"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Cartera </label>

            <div class="col-sm-8">
                <input id=EmailCartera type=text size=25 name=EmailCartera class="col-xs-12" title="EmailCartera" value="<?= $frm["EmailCartera"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Boton de panico</label>

            <div class="col-sm-8">
                <input id=CorreoPanico type=text size=25 name=CorreoPanico class="col-xs-12" title="CorreoPanico" value="<?= $frm["CorreoPanico"] ?>">
            </div>
        </div>



    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre de Usuario Administrador General: </label>

            <div class="col-sm-8">
                <input id=NombreAdministrador type=text size=25 name=NombreAdministrador class="col-xs-12" title="Nombre Administrador" value="<?= $frm[NombreAdministrador] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cargo Dentro del Club: </label>

            <div class="col-sm-8">
                <input id=CargoAdministrador type=text size=25 name=CargoAdministrador class="col-xs-12" title="Cargo Administrador" value="<?= $frm[CargoAdministrador] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>

            <div class="col-sm-8">
                <input id=EmailAdministrador type=text size=25 name=EmailAdministrador class="col-xs-12" title="Email Administrador" value="<?= $frm[EmailAdministrador] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Teléfono </label>

            <div class="col-sm-8">
                <input id=TelefonoAdministrador type=text size=25 name=TelefonoAdministrador class="col-xs-12" title="TelefonoAdministrador" value="<?= $frm[TelefonoAdministrador] ?>">
            </div>
        </div>

    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Contrato y Envio Aceptación
        </h3>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Contrato de licenciamiento </label>

            <div class="col-sm-8">
                <? if (!empty($frm[Contrato])) {
                    echo "<a href='" . CLUB_ROOT . "$frm[Contrato]' target='_blank'>" . $frm[Contrato] . "</a>";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[Contrato]&campo=Contrato&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="Contrato" id=file class="col-xs-12" title="Contrato" type="file" size="25" style="font-size: 10px">
                <input type="hidden" name="ContratoArchivo" id="ContratoArchivo" value="<?php echo $frm[Contrato] ?>" />
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Oferta Mercantil </label>

            <div class="col-sm-8">
                <? if (!empty($frm[Oferta])) {
                    echo "<a href='" . CLUB_ROOT . "$frm[Oferta]' target='_blank'>" . $frm[Oferta] . "</a>";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[Oferta]&campo=Oferta&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="Oferta" id=file class="col-xs-12" title="Oferta" type="file" size="25" style="font-size: 10px">
                <input type="hidden" name="ContratoOferta" id="ContratoOferta" value="<?php echo $frm[Oferta] ?>" />
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email envio contratos </label>

            <div class="col-sm-8">
                <input id=EmailContrato type=text size=25 name=EmailContrato class="col-xs-12" title="EmailContrato" value="<?= $frm["EmailContrato"] ?>">
            </div>
        </div>



        <?php if ($_GET[action] == "edit") { ?>

            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> </label>
                <div class="col-sm-8">
                    <a href="<? echo $script . ".php?action=enviarcontratos&id=" . $frm[$key]; ?>">
                        <button class="btn btn-info btnEnviarContrato" type="button">
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            Enviar contrato para aceptación.
                        </button>
                    </a>
                </div>
            </div>
        <?php
        } ?>

    </div>






    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-cloud-download green"></i>
            Control de Versiones
        </h3>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version App IOS </label>

            <div class="col-sm-8">
                <input id=Version type=text size=25 name=Version class="col-xs-12" title="Version" value="<?= $frm[Version] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Es Esencial IOS? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Esencial"], 'Esencial', "class='input'") ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version Message IOS </label>

            <div class="col-sm-8">
                <input id="VersionMessage" type=text size=25 name=VersionMessage class="col-xs-12" title="Version Message" value="<?= $frm[VersionMessage] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version URL IOS </label>

            <div class="col-sm-8">
                <input id="VersionURLIOS" type=text size=25 name="VersionURLIOS" class="col-xs-12" title="Version URL IOS" value="<?= $frm[VersionURLIOS] ?>">
            </div>
        </div>

    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version App Android </label>

            <div class="col-sm-8">
                <input id=VersionAndroid type=text size=25 name=VersionAndroid class="col-xs-12" title="Version Android" value="<?= $frm[VersionAndroid] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Es Esencial Android? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["EsencialAndroid"], 'EsencialAndroid', "class='input'") ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version Message Android </label>

            <div class="col-sm-8">
                <input id="VersionMessageAndroid" type=text size=25 name=VersionMessageAndroid class="col-xs-12" title="Version Message Android" value="<?= $frm[VersionMessageAndroid] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version URL Andoird </label>

            <div class="col-sm-8">
                <input id="VersionURLAndroid" type=text size=25 name=VersionURLAndroid class="col-xs-12" title="Version URL Android" value="<?= $frm[VersionURLAndroid] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ruta base de datos Firebase </label>

            <div class="col-sm-8">
                <input id="BaseFirebase" type=text size=25 name=BaseFirebase class="col-xs-12" title="BaseFirebase" value="<?= $frm[BaseFirebase] ?>" placeholder="https://mi-club-40515.firebaseio.com/deliveries/">
            </div>
        </div>

    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-paper-plane green"></i>
            Configuraci&oacute;n Push
        </h3>
    </div>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> API Google </label>

            <div class="col-sm-8">
                <input id=GCM_API_KEY type=text size=25 name=GCM_API_KEY class="col-xs-12" title="API Google" value="<?= $frm["GCM_API_KEY"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ambiente Push </label>

            <div class="col-sm-8">
                <select name="PUSH_ENVIRONMENT" id="PUSH_ENVIRONMENT" class="popup mandatory" title="PUSH_ENVIRONMENT">
                    <option value="">[Seleccione el Ambiente Actual]</option>
                    <option value="prod" <? if ($frm["PUSH_ENVIRONMENT"] == "prod") echo " selected='selected' " ?>>Produccion</option>
                    <option value="dev" <? if ($frm["PUSH_ENVIRONMENT"] == "dev") echo " selected='selected' " ?>>Desarrollo</option>
                </select>
            </div>
        </div>

    </div>

    <!--
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Direccion del Certificado </label>
            <div class="col-sm-8">
                <input id=CERTIFICATES_DIR type=text size=25 name=CERTIFICATES_DIR class="col-xs-12" title="Certificado" value="<?= $frm["CERTIFICATES_DIR"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Clave del Certificado </label>
            <div class="col-sm-8">
                <input id=APNS_PASSPHRASE type=text size=25 name=APNS_PASSPHRASE class="col-xs-12" title="Certificado" value="<?= $frm["APNS_PASSPHRASE"] ?>">
            </div>
        </div>
    </div>
  -->

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">App Bundle Id </label>

            <div class="col-sm-8">
                <input id=AppBundleId type=text size=25 name=AppBundleId class="col-xs-12" title="AppBundleId" value="<?= $frm["AppBundleId"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Key IOS </label>

            <div class="col-sm-8">
                <input id=KeyIdiOS type=text size=25 name=KeyIdiOS class="col-xs-12" title="KeyIdiOS" value="88Y5K6B9B7">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">TeamIdiOS </label>

            <div class="col-sm-8">
                <input id=TeamIdiOS type=text size=25 name=TeamIdiOS class="col-xs-12" title="TeamIdiOS" value="G9C2JU32N6">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">PrivateKeyPathiOS </label>

            <div class="col-sm-8">
                <input id=PrivateKeyPathiOS type=text size=25 name=PrivateKeyPathiOS class="col-xs-12" title="PrivateKeyPathiOS" value="AuthKey_88Y5K6B9B7.p8">
            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa  fa-barcode green"></i>
            Configuraci&oacute;n Carne
        </h3>
    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo para carn&eacute;: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$tipocodigocarne), $frm["TipoCodigoCarne"], 'TipoCodigoCarne', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color Fondo Carne </label>

            <div class="col-sm-8">
                <input name="ColorFondoCarne" type="color" value="<?php if (empty($frm["ColorFondoCarne"])) {
                                                                        echo "#FFFFFF";
                                                                    } else {
                                                                        echo $frm["ColorFondoCarne"];
                                                                    }    ?>" />
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Porcentaje Codigo Qr Android? </label>

            <div class="col-sm-8">
                <input id="PorcentajeQrAndroid" type=text size=25 name="PorcentajeQrAndroid" class="col-xs-12" title="Porcentaje Qr Android" value="<?= $frm["PorcentajeQrAndroid"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Porcentaje Codigo Qr IOS: </label>

            <div class="col-sm-8">
                <input id="PorcentajeQrIOS" type=text size=25 name="PorcentajeQrIOS" class="col-xs-12" title="Porcentaje Qr IOS" value="<?= $frm["PorcentajeQrIOS"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label Identificador Usuario </label>

            <div class="col-sm-8">
                <input id=LabelIdentificadorUsuario type=text size=25 name=LabelIdentificadorUsuario class="col-xs-12" title="Label Identificador Usuario" value="<?= $frm["LabelIdentificadorUsuario"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Datos carne: </label>

            <div class="col-sm-8">
                <input id=LabelEstadoUsuario type=text size=25 name=LabelEstadoUsuario class="col-xs-12" title="Label Estado Usuario" value="<?= $frm["LabelEstadoUsuario"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir siempre el cambio de foto? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteCambioFotoPerfil"], 'PermiteCambioFotoPerfil', "class='input '") ?>
            </div>
        </div>
    </div>








    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Otros datos
        </h3>
    </div>





    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo: </label>

            <div class="col-sm-8">
                <?php echo SIMHTML::formPopUp("TipoClub", "Nombre", "Nombre", "IDTipoClub", $frm["IDTipoClub"], "[Seleccione el Tipo]", "popup mandatory", "title = \"Tipo\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar en las reservas: </label>

            <div class="col-sm-8">
                <input type="radio" name="MostrarReserva" class="" value="NombreSocio" <?php if ($frm["MostrarReserva"] == "NombreSocio") echo "checked" ?>> Nombre de Socio
                <input type="radio" name="MostrarReserva" class="" value="Pesonalizado" <?php if ($frm["MostrarReserva"] == "Pesonalizado") echo "checked" ?>>Pesonalizado
                <input type="LabelPersonalizado" name="LabelPersonalizado" class="form-control" placeholder="Titulo a mostrar" value="<?php echo $frm["LabelPersonalizado"]; ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar cambio de clave al primer ingreso al app? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaCambioClave"], 'SolicitaCambioClave', "class='input '") ?>
            </div>
        </div>




    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label Auxiliar (boleador) </label>

            <div class="col-sm-8">
                <input id=LabelAuxiliar type=text size=25 name=LabelAuxiliar class="col-xs-12" title="Label Auxiliar" value="<?= $frm["LabelAuxiliar"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar correo cuando se pida cambio de clave? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaCambioCorreo"], 'SolicitaCambioCorreo', "class='input '") ?>
            </div>
        </div>

    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar check seguridad social en invitacion: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CheckSeguridadSocial"], 'CheckSeguridadSocial', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Seguridad Social?: </label>

            <div class="col-sm-8">
                <input type="text" name="LabelSeguridadSocial" class="form-control" placeholder="Label Seguridad Social" value="<?php echo $frm["LabelSeguridadSocial"]; ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url formulario de registro de auto-diagnostico para invitados: </label>

            <div class="col-sm-8">
                <input type="text" name="UrlFormularioAutodiagnosticoInvitado" class="form-control" placeholder="Url Formulario Autodiagnostico" value="<?php echo $frm["UrlFormularioAutodiagnosticoInvitado"]; ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitar Pre-Salida en invitacion: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Presalida"], 'Presalida', "class='input '") ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo duración Splash: </label>

            <div class="col-sm-8">
                <input type="TiempoSplash" name="TiempoSplash" class="form-control" placeholder="TiempoSplash" value="<?php echo $frm["TiempoSplash"]; ?>"> segundos
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo visualizacion Splash: </label>

            <div class="col-sm-8">
                <input type="radio" name="TipoImagenBanner" id="TipoImagenBanner" value="Expandida" <?php if ($frm["TipoImagenBanner"] == "Expandida") echo "checked"; ?>> <b>Expandida</b> (La imagen ocupará todo el espacio de la pantalla (100% ancho - 100% alto), manteniendo el aspect_ratio de la imagen)
                <br>
                <input type="radio" name="TipoImagenBanner" id="TipoImagenBanner" value="Ajustada" <?php if ($frm["TipoImagenBanner"] == "Ajustada") echo "checked"; ?>> <b>Ajustada</b> (La imagen ocupará el espacio posible sin salirse de la pantalla, esto puede resultar en ocupar el 100% del ancho o 100% del alto, pero no los dos)
                <br>
                <input type="radio" name="TipoImagenBanner" id="TipoImagenBanner" value="Estirada" <?php if ($frm["TipoImagenBanner"] == "Estirada") echo "checked"; ?>> <b>Estirada</b> (La imagen ocupará todo el espacio de la pantalla (100% ancho - 100% alto), sin mantener el aspect_ratio de la imagen)
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Telefono directorio </label>

            <div class="col-sm-8">
                <? if (!empty($frm[IconoTelefono])) {
                    echo "<img src='" . CLUB_ROOT . "$frm[IconoTelefono]' width=55 >";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[IconoTelefono]&campo=IconoTelefono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="IconoTelefono" id=IconoTelefono class="col-xs-12" title="Icono Telefono" type="file" size="25" style="font-size: 10px">

            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Mail directorio </label>
            <div class="col-sm-8">
                <? if (!empty($frm[IconoEmail])) {
                    echo "<img src='" . CLUB_ROOT . "$frm[IconoEmail]' width=55 >";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[IconoEmail]&campo=IconoEmail&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="IconoEmail" id=IconoEmail class="col-xs-12" title="Icono Mail" type="file" size="25" style="font-size: 10px">
            </div>
        </div>

    </div>





    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solo mostrar Icono en reserva de servicios? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SoloIcono"], 'SoloIcono', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Imagen en noticia? </label>
            <div class="col-sm-8">
                <select name="TipoImagenNoticias" id="TipoImagenNoticias" class="form-control">
                    <option value=""></option>
                    <option value="Expandida" <?php if ($frm["TipoImagenNoticias"] == "Expandida") echo "selected"; ?>>Expandida</option>
                    <option value="Ajustada" <?php if ($frm["TipoImagenNoticias"] == "Ajustada") echo "selected"; ?>>Ajustada</option>
                </select>
            </div>
        </div>


    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Notificaciones nuevo contenido (Noticias, eventos, etc) </label>

            <div class="col-sm-8">
                <input id=EmailNotificacionContenido type=text size=25 name=EmailNotificacionContenido class="col-xs-12" title="EmailNotificacionContenido" value="<?= $frm["EmailNotificacionContenido"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Observaciones en Contratistas? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CampoObservacionContratista"], 'CampoObservacionContratista', "class='input '") ?>
            </div>
        </div>

    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Crear canjes de otros clubes automaticamente (solo para clubes con app de 109 Apps) </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CanjesAutomaticos"], 'CanjesAutomaticos', "class='input '") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Guardar accesos de funcionarios? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["AccesoFuncionarios"], 'AccesoFuncionarios', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="form-group first">
        <? if (!empty($clubHijos)) { ?>
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cargar socios de otras sedes? </label>

                <div class="col-sm-8">
                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CargarSociosHijos"], 'CargarSociosHijos', "class='input '") ?>
                </div>
            </div>
        <? } ?>
    </div>

    <div class="form-group first">
        Mensaje para invitados
        <div class="col-sm-12">
            <?php
            $oCuerpoInvi = new FCKeditor("MensajeInvitados");
            $oCuerpoInvi->BasePath = "js/fckeditor/";
            $oCuerpoInvi->Height = 200;
            //$oCuerpo->EnterMode = "p";
            $oCuerpoInvi->Value =  $frm["MensajeInvitados"];
            $oCuerpoInvi->Create();
            ?>
        </div>
    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Configuracion Modulo 1 Eventos
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permitir Mis Eventos (modulo 1)? (reservas de eventos) </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMisEventos"], 'PermiteMisEventos', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Tipo Filtro Evento (modulo 1)? </label>

            <div class="col-sm-8">
                <select name="TipoFiltroEvento" id="TipoFiltroEvento" class="form-control">
                    <option value=""></option>
                    <option value="Buscador" <?php if ($frm["TipoFiltroEvento"] == "Buscador") echo "selected"; ?>>Buscador</option>
                    <option value="Calendario" <?php if ($frm["TipoFiltroEvento"] == "Calendario") echo "selected"; ?>>Calendario</option>
                </select>

            </div>
        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mostrar en lista Eventos (modulo 1) </label>

            <div class="col-sm-8">
                <input type="radio" name="EventoLista" value="Imagen" <?php if ($frm["EventoLista"] == "Imagen") echo "checked"; ?>>Imagen
                <input type="radio" name="EventoLista" value="Calendario" <?php if ($frm["EventoLista"] == "Calendario") echo "checked"; ?>>Calendario
            </div>
        </div>


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color encabezado eventos (modulo 1) </label>

            <div class="col-sm-8">
                <input name="ColorFondoEvento" type="color" value="<?php if (empty($frm["ColorFondoEvento"])) {
                                                                        echo "#FFFFFF";
                                                                    } else {
                                                                        echo $frm["ColorFondoEvento"];
                                                                    }    ?>" />
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Buscador de fecha en eventos? (modulo 1) </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["BuscadorFechaEvento"], 'BuscadorFechaEvento', "class='input '") ?>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Tipo Celda Evento? (modulo 1) </label>

            <div class="col-sm-8">
                <input type="radio" name="TipoCeldaEvento" value="Compacto" <?php if ($frm["TipoCeldaEvento"] == "Compacto") echo "checked"; ?>>Compacto
                <input type="radio" name="TipoCeldaEvento" value="Ancho" <?php if ($frm["TipoCeldaEvento"] == "Ancho") echo "checked"; ?>>Ancho

            </div>
        </div>
    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Configuracion Modulo 2 Eventos
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permitir Mis Eventos (modulo2) ? (reservas de eventos) </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteMisEventos2"], 'PermiteMisEventos2', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Tipo Filtro Evento (modulo2)? </label>

            <div class="col-sm-8">
                <select name="TipoFiltroEvento2" id="TipoFiltroEvento2" class="form-control">
                    <option value=""></option>
                    <option value="Buscador" <?php if ($frm["TipoFiltroEvento2"] == "Buscador") echo "selected"; ?>>Buscador</option>
                    <option value="Calendario" <?php if ($frm["TipoFiltroEvento2"] == "Calendario") echo "selected"; ?>>Calendario</option>
                </select>

            </div>
        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mostrar en lista Eventos (modulo2) </label>

            <div class="col-sm-8">
                <input type="radio" name="EventoLista2" value="Imagen" <?php if ($frm["EventoLista2"] == "Imagen") echo "checked"; ?>>Imagen
                <input type="radio" name="EventoLista2" value="Calendario" <?php if ($frm["EventoLista2"] == "Calendario") echo "checked"; ?>>Calendario
            </div>
        </div>


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color encabezado eventos (modulo2)</label>

            <div class="col-sm-8">
                <input name="ColorFondoEvento2" type="color" value="<?php if (empty($frm["ColorFondoEvento2"])) {
                                                                        echo "#FFFFFF";
                                                                    } else {
                                                                        echo $frm["ColorFondoEvento2"];
                                                                    }    ?>" />
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Buscador de fecha en eventos? (modulo2) </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["BuscadorFechaEvento2"], 'BuscadorFechaEvento2', "class='input '") ?>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Tipo Celda Evento? (modulo2) </label>

            <div class="col-sm-8">
                <input type="radio" name="TipoCeldaEvento2" value="Compacto" <?php if ($frm["TipoCeldaEvento2"] == "Compacto") echo "checked"; ?>>Compacto
                <input type="radio" name="TipoCeldaEvento2" value="Ancho" <?php if ($frm["TipoCeldaEvento2"] == "Ancho") echo "checked"; ?>>Ancho

            </div>
        </div>


    </div>





    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-gavel green"></i>
            Clasificados
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir a socio crear clasificado?: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CrearClasificado"], 'CrearClasificado', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Crear Clasificado en: </label>

            <div class="col-sm-8">
                <select name="TipoCrearClasificado" id="TipoCrearClasificado" class="popup" title="Tipo Crear Clasificado">
                    <option value="">[Seleccione el tipo]</option>
                    <option value="app" <? if ($frm["TipoCrearClasificado"] == "app") echo " selected='selected' " ?>>App</option>
                    <option value="url" <? if ($frm["TipoCrearClasificado"] == "url") echo " selected='selected' " ?>>Url</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url para crear clasificado: </label>

            <div class="col-sm-8">
                <input type="text" id="URLCLasificado" name="URLCLasificado" placeholder="URL CLasificado" class="col-xs-12" title="URL CLasificado" value="<?php echo $frm["URLCLasificado"]; ?>">
            </div>
        </div>

    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-gavel green"></i>
            PASA LA PAGINA
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url: </label>

            <div class="col-sm-8">
                <input type="text" name="UrlPasaPagina" class="form-control" placeholder="Url Pasa Pagina" value="<?php echo $frm["UrlPasaPagina"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Referer: </label>

            <div class="col-sm-8">
                <input type="text" name="RefererPasaPagina" class="form-control" placeholder="RefererPasaPagina" value="<?php echo $frm["RefererPasaPagina"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Source: </label>

            <div class="col-sm-8">
                <input type="text" name="SourcePasaPagina" class="form-control" placeholder="SourcePasaPagina" value="<?php echo $frm["SourcePasaPagina"]; ?>">
            </div>
        </div>


    </div>





    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Labels Personalizados
        </h3>
    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Usuario (login app) </label>

            <div class="col-sm-8">
                <input id=LabelUsuario type=text size=25 name=LabelUsuario class="col-xs-12" title="Label Usuario" value="<?= $frm["LabelUsuario"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label clave (login app) </label>

            <div class="col-sm-8">
                <input id=LabelClave type=text size=25 name=LabelClave class="col-xs-12" title="Label Clave" value="<?= $frm["LabelClave"] ?>">
            </div>
        </div>


    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label login app (encima caja) </label>

            <div class="col-sm-8">
                <input id=LabelDigiteUsuario type=text size=25 name=LabelDigiteUsuario class="col-xs-12" title="Label Digite Usuario" value="<?= $frm["LabelDigiteUsuario"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label clave app (encima caja) </label>

            <div class="col-sm-8">
                <input id=LabelDigiteClave type=text size=25 name=LabelDigiteClave class="col-xs-12" title="Label Digite Clave" value="<?= $frm["LabelDigiteClave"] ?>">
            </div>
        </div>


    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label olvido usuario (login app) </label>

            <div class="col-sm-8">
                <input id=LabelOlvidoUsuario type=text size=25 name=LabelOlvidoUsuario class="col-xs-12" title="Label Olvido Usuario" value="<?= $frm["LabelOlvidoUsuario"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Error Foto (cuando ya se haya cambiado la foto) </label>

            <div class="col-sm-8">
                <input id=LabelFotoError type=text size=25 name=LabelFotoError class="col-xs-12" title="Label Foto Error" value="<?= $frm["LabelFotoError"] ?>">
            </div>
        </div>




    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label busqueda handicap codigo </label>

            <div class="col-sm-8">
                <input id=LabelCodigoHandicap type=text size=25 name=LabelCodigoHandicap class="col-xs-12" title="Label Codigo Handicap" value="<?= $frm["LabelCodigoHandicap"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> label busqueda handicap nombre </label>

            <div class="col-sm-8">
                <input id=LabelNombreHandicap type=text size=25 name=LabelNombreHandicap class="col-xs-12" title="Label Nombre Handicap" value="<?= $frm["LabelNombreHandicap"] ?>">
            </div>
        </div>




    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label ayuda handicap </label>

            <div class="col-sm-8">
                <input id=AyudaHandicap type=text size=25 name=AyudaHandicap class="col-xs-12" title="Label Ayuda Handicap" value="<?= $frm["AyudaHandicap"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label caja texto busqueda handicap </label>

            <div class="col-sm-8">
                <input id=LabelCodigoHandicapTexto type=text size=25 name=LabelCodigoHandicapTexto class="col-xs-12" title="Label Codigo Handicap Texto" value="<?= $frm["LabelCodigoHandicapTexto"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label caja texto busqueda nombre </label>

            <div class="col-sm-8">
                <input id=LabelNombreHandicapTexto type=text size=25 name=LabelNombreHandicapTexto class="col-xs-12" title="Label Nombre Handicap Texto" value="<?= $frm["LabelNombreHandicapTexto"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Terminos y condiciones (login app) </label>

            <div class="col-sm-8">
                <input id=LabelTerminos type=text size=25 name=LabelTerminos class="col-xs-12" title="Label Terminos" value="<?= $frm["LabelTerminos"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Tipo Terminos y Condiciones </label>

            <div class="col-sm-8">
                <select name="TipoTerminos" id="TipoTerminos" class="form-control">
                    <option value=""></option>
                    <option value="Archivo" <?php if ($frm["TipoTerminos"] == "Archivo") echo "selected"; ?>>Descargar Archivo</option>
                    <option value="Texto" <?php if ($frm["TipoTerminos"] == "Texto") echo "selected"; ?>>Abrir texto</option>
                </select>
            </div>
        </div>


    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pdf terminos y condiciones </label>

            <div class="col-sm-8">
                <? if (!empty($frm[ArchivoTerminos])) {
                    echo "<a href='" . CLUB_ROOT . "$frm[ArchivoTerminos]' target='_blank'>" . $frm[ArchivoTerminos] . "</a>";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[ArchivoTerminos]&campo=ArchivoTerminos&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="ArchivoTerminos" id=file class="col-xs-12" title="ArchivoTerminos" type="file" size="25" style="font-size: 10px">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label al invitar </label>

            <div class="col-sm-8">
                <input id=LabelInvitacion type=text size=25 name=LabelInvitacion class="col-xs-12" title="Label Invitacion" value="<?= $frm["LabelInvitacion"]; ?>">
            </div>
        </div>




    </div>



    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Encuesta </label>

            <div class="col-sm-8">
                <input id=LabelEncuesta type=text size=25 name=LabelEncuesta class="col-xs-12" title="Label Encuesta" value="<?= $frm["LabelEncuesta"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Votacion </label>

            <div class="col-sm-8">
                <input id=LabelVotacion type=text size=25 name=LabelVotacion class="col-xs-12" title="Label Votacion" value="<?= $frm["LabelVotacion"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Encuesta Calificada </label>

            <div class="col-sm-8">
                <input id=LabelEncuesta2 type=text size=25 name=LabelEncuesta2 class="col-xs-12" title="Label Encuesta calificada" value="<?= $frm["LabelEncuesta2"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Autodiagnostico </label>

            <div class="col-sm-8">
                <input id=LabelDiagnostico type=text size=25 name=LabelDiagnostico class="col-xs-12" title="Label Diagnostico" value="<?= $frm["LabelDiagnostico"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Placa en invitados </label>

            <div class="col-sm-8">
                <input id=LabelPlaca type=text size=25 name=LabelPlaca class="col-xs-12" title="Label Placa" value="<?= $frm["LabelPlaca"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label buscador Pre-salida </label>

            <div class="col-sm-8">
                <input id=LabelPresalida type=text size=25 name=LabelPresalida class="col-xs-12" title="Label Presalida" value="<?= $frm["LabelPresalida"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label pqr Felicitacion (condado) </label>

            <div class="col-sm-8">
                <input id=LabelFelicitacion type=text size=25 name=LabelFelicitacion class="col-xs-12" title="Label Felicitacion" value="<?= $frm["LabelFelicitacion"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label calificacion directorio </label>

            <div class="col-sm-8">
                <input id=LabelCalificacion type=text size=25 name=LabelCalificacion class="col-xs-12" title="Label Calificacion" value="<?= $frm["LabelCalificacion"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label comentario calificacion directorio </label>

            <div class="col-sm-8">
                <input id=LabelComentarioFelicitacion type=text size=25 name=LabelComentarioFelicitacion class="col-xs-12" title="Label Comentario Felicitacion" value="<?= $frm["LabelComentarioFelicitacion"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio comentario al calificar? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioComentarioCalificar"], 'ObligatorioComentarioCalificar', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label boton Eventos </label>

            <div class="col-sm-8">
                <input id=LabelBotonEventos type=text size=25 name=LabelBotonEventos class="col-xs-12" title="Label Boton Eventos" value="<?= $frm["LabelBotonEventos"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Ver Mis Reservas </label>

            <div class="col-sm-8">
                <input id=LabelVerMisReservas type=text size=25 name=LabelVerMisReservas class="col-xs-12" title="Label Ve rMis Reservas" value="<?= $frm["LabelVerMisReservas"] ?>">
            </div>
        </div>


    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Acerca de: </label>

            <div class="col-sm-8">
                <input id=LabelAcercaDe type=text size=25 name=LabelAcercaDe class="col-xs-12" title="Label Acerca De" value="<?= $frm["LabelAcercaDe"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ocultar Olvido usuario? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["OcultarOlvideMiUsuario"], 'OcultarOlvideMiUsuario', "class='input '") ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen Cumpleaños </label>

            <div class="col-sm-8">
                <? if (!empty($frm[ImagenCumpleanos])) {
                    echo "<a href='" . CLUB_ROOT . "$frm[ImagenCumpleanos]' target='_blank'>" . $frm[ImagenCumpleanos] . "</a>";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[ImagenCumpleanos]&campo=ImagenCumpleanos&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="ImagenCumpleanos" id=file class="col-xs-12" title="ImagenCumpleanos" type="file" size="25" style="font-size: 10px">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label al invitar </label>

            <div class="col-sm-8">
                <input id=LabelBotonCumpleanos type=text size=25 name=LabelBotonCumpleanos class="col-xs-12" title="LabelBotonCumpleanos" value="<?= $frm["LabelBotonCumpleanos"]; ?>">
            </div>
        </div>




    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            Configuracion Fedegolf
        </h3>
    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Boton Ver Scores</label>
            <div class="col-sm-8">
                <input type="text" id="BotonVerScores" name="BotonVerScores" placeholder="Boton Ver Scores" class="col-xs-12" title="BotonVerScores" value="<?php echo $frm["BotonVerScores"]; ?>" required>

            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Boton Buscar Otros Handicap </label>
            <div class="col-sm-8">
                <input type="text" id="BotonBuscarOtrosHandicap" name="BotonBuscarOtrosHandicap" placeholder="Boton Buscar Otros Handicap" class="col-xs-12" title="BotonBuscarOtrosHandicap" value="<?php echo $frm["BotonBuscarOtrosHandicap"]; ?>" required>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label Seleccione Club</label>
            <div class="col-sm-8">
                <input type="text" id="LabelSeleccioneClub" name="LabelSeleccioneClub" placeholder="Label Seleccione Club" class="col-xs-12" title="LabelSeleccioneClub" value="<?php echo $frm["LabelSeleccioneClub"]; ?>" required>

            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Seleccione Campo </label>
            <div class="col-sm-8">
                <input type="text" id="LabelSeleccioneCampo" name="LabelSeleccioneCampo" placeholder="Label Seleccione Campo" class="col-xs-12" title="LabelSeleccioneCampo" value="<?php echo $frm["LabelSeleccioneCampo"]; ?>" required>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label Seleccione Marca</label>
            <div class="col-sm-8">
                <input type="text" id="LabelSeleccioneMarca" name="LabelSeleccioneMarca" placeholder="Label Seleccione Marca" class="col-xs-12" title="LabelSeleccioneMarca" value="<?php echo $frm["LabelSeleccioneMarca"]; ?>" required>

            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Boton Calcular Handicap </label>
            <div class="col-sm-8">
                <input type="text" id="BotonCalcularHandicap" name="BotonCalcularHandicap" placeholder="Boton Calcular Handicap" class="col-xs-12" title="BotonCalcularHandicap" value="<?php echo $frm["BotonCalcularHandicap"]; ?>" required>
            </div>
        </div>
    </div>





    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Inicio de App
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Socios deben tener segunda clave? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ManejoSegundaClave"], 'ManejoSegundaClave', "class='input '") ?>
            </div>
        </div>


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Cambio segunda clave </label>

            <div class="col-sm-8">
                <input id=LabelCambioSegundaClave type=text size=25 name=LabelCambioSegundaClave class="col-xs-12" title="Label Cambio Segunda Clave" value="<?= $frm["LabelCambioSegundaClave"] ?>">
            </div>
        </div>

    </div>
    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Solicitar editar perfil al ingresar al app? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaEditarPerfil"], 'SolicitaEditarPerfil', "class='input '") ?>
            </div>
        </div>


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio Editar Perfil </label>

            <div class="col-sm-8">

                <input type="text" id="FechaInicioEditarPerfil" name="FechaInicioEditarPerfil" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php if ($frm["FechaInicioEditarPerfil"] == "" || $frm["FechaInicioEditarPerfil"] == "0000-00-00") echo "";
                                                                                                                                                                            else echo $frm["FechaInicioEditarPerfil"] ?>">
            </div>
        </div>

    </div>
    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Editar perfil </label>

            <div class="col-sm-8">
                <input type="text" id="SolicitaEditarPefilLabel" name="SolicitaEditarPefilLabel" placeholder="Solicita Editar PefilLabel" class="col-xs-12" title="Solicita Editar PefilLabel" value="<?php echo $frm["SolicitaEditarPefilLabel"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite Registro Usuario</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteRegistroUsuario"], 'PermiteRegistroUsuario', "class='input '") ?>

            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Registro Usuario </label>
            <div class="col-sm-8">
                <input type="text" id="UrlRegistroUsuario" name="UrlRegistroUsuario" placeholder="Url Registro Usuario" class="col-xs-12" title="UrlRegistroUsuario" value="<?php echo $frm["UrlRegistroUsuario"]; ?>" required>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mostrar icono mis reservas home superior</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBotomMisReservasHome"], 'MostrarBotomMisReservasHome', "class='input '") ?>

            </div>
        </div>
    </div>




    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-bell-o green"></i>
            Configuraci&oacute;n Lista de espera
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje Lista espera Hotel </label>

            <div class="col-sm-8">
                <input id="MensajeListaEsperaHotel" type=text size=25 name="MensajeListaEsperaHotel" class="col-xs-12" title="Mensaje Lista Espera Hotel" value="<?= $frm["MensajeListaEsperaHotel"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje Lista espera Reservas </label>

            <div class="col-sm-8">
                <input id="MensajeListaEsperaReservas" type=text size=25 name="MensajeListaEsperaReservas" class="col-xs-12" title="Mensaje Lista Espera Reservas" value="<?= $frm["MensajeListaEsperaReservas"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje Aceptacion Lista espera Hotel </label>

            <div class="col-sm-8">
                <input id="MensajeAceptacionListaEsperaHotel" type=text size=25 name="MensajeAceptacionListaEsperaHotel" class="col-xs-12" title="Mensaje Aceptacion Lista Espera Hotel" value="<?= $frm["MensajeAceptacionListaEsperaHotel"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje Aceptacion Lista espera Reservas </label>

            <div class="col-sm-8">
                <input id="MensajeAceptacionListaEsperaReservas" type=text size=25 name="MensajeAceptacionListaEsperaReservas" class="col-xs-12" title="Mensaje Aceptacion Lista Espera Reservas" value="<?= $frm["MensajeAceptacionListaEsperaReservas"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permitir Lista Espera hotel: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteListaEsperaHotel"], 'PermiteListaEsperaHotel', "class='input '") ?>
            </div>
        </div>



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permitir SMS: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteSMS"], 'PermiteSMS', "class='input '") ?>
            </div>
        </div>



    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permitir Otro Valor al cancelar?: (Si marca "NO" obliga a pagar el 100% del valor) </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteOtroValorHotel"], 'PermiteOtroValorHotel', "class='input '") ?>
            </div>
        </div>


    </div>




    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-coffee green"></i>
            Configuraci&oacute;n Domicilios
        </h3>
    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pedir Fecha en Domicilios: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaFechaDomicilio"], 'SolicitaFechaDomicilio', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pedir Hora Domicilio: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SolicitaHoraDomicilio"], 'SolicitaHoraDomicilio', "class='input '") ?>
            </div>
        </div>

    </div>






    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Publicidad
        </h3>
    </div>

    <!--  <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habilitar Publicidad? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicidad"], 'Publicidad', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo rotar Publicidad? </label>

            <div class="col-sm-8">
                <input id="TiempoPublicidad" type=text size=25 name="TiempoPublicidad" class="col-xs-12" title="Tiempo Publicidad" value="<?= $frm["TiempoPublicidad"] ?>">segundos
            </div>
        </div>

    </div> -->

    <!-- <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de Header App? </label>

            <div class="col-sm-8">
                <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Publicidad" <?php if ($frm["TipoHeaderApp"] == "Publicidad") echo "checked"; ?>> Publicidad (rota im&aacute;genes)
                <br>
                <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Clasico" <?php if ($frm["TipoHeaderApp"] == "Clasico") echo "checked"; ?>> Clasico (imagen fija logo club)
                <br>
                <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="PublicidadFoto" <?php if ($frm["TipoHeaderApp"] == "PublicidadFoto") echo "checked"; ?>> PublicidadFoto (Publicidad mas foto)
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo Rotar Publicidad Header? </label>

            <div class="col-sm-8">
                <input id="TiempoPublicidadHeader" type=text size=25 name="TiempoPublicidadHeader" class="col-xs-12" title="Tiempo Publicidad Header" value="<?= $frm["TiempoPublicidadHeader"] ?>">segundos
            </div>
        </div>

    </div> -->


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-credit-card green"></i>
            Parametros Plataforma de pago
        </h3>
    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Signo inicial texto pago</label>
            <div class="col-sm-8">
                <input id=SignoPago type=text size=25 name=SignoPago class="input" title="Signo Pago" value="<?= $frm["SignoPago"] ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto final pago</label>
            <div class="col-sm-8">
                <input id=TextoPago type=text size=25 name=TextoPago class="input" title="Texto Pago" value="<?= $frm["TextoPago"] ?>">

            </div>
        </div>
    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Porcentaje Iva</label>
            <div class="col-sm-8">
                <input id=PorcentajeIva type=number size=25 name=PorcentajeIva class="input" title="Signo Pago" value="<?= $frm["PorcentajeIva"] ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Moneda</label>
            <div class="col-sm-8">
                <input id=Moneda type=text size=25 name=Moneda class="input" title="Texto Pago" value="<?= $frm["Moneda"] ?>">

            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            PAYU
        </h3>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Habilitar PAYU? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PasarelaPAYU"], 'PasarelaPAYU', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Api Key Payu</label>

            <div class="col-sm-8">
                <input type="password" id="ApiKey" name="ApiKey" placeholder="Api Key" class="col-xs-12" title="ApiKey" value="<?php echo $frm["ApiKey"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Api Login Payu</label>
            <div class="col-sm-8">
                <input type="password" id="ApiLogin" name="ApiLogin" placeholder="ApiLogin" class="col-xs-12" title="ApiLogin" value="<?php echo $frm["ApiLogin"]; ?>">

            </div>
        </div>

    </div>

    <div class="form-group first ">

        3
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Merchant Id Payu</label>

            <div class="col-sm-8">
                <input type="password" id="MerchantId" name="MerchantId" placeholder="MerchantId" class="col-xs-12" title="Merchant Id" value="<?php echo $frm["MerchantId"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> AccountId Payu</label>
            <div class="col-sm-8">
                <input type="password" id="AccountId" name="AccountId" placeholder="Account Id" class="col-xs-12" title="Account Id" value="<?php echo $frm["AccountId"]; ?>">

            </div>
        </div>

    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modo de Pruebas Payu </label>

            <div class="col-sm-8">
                <select name="IsTest" id="IsTest" class="form-control">
                    <option value="1" <?php if ($frm["IsTest"] == 1) echo "selected"; ?>>Si son pruebas</option>
                    <option value="0" <?php if ($frm["IsTest"] == 0) echo "selected"; ?>>No son pruebas</option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL PAYU </label>
            <div class="col-sm-8">
                <input type="text" id="URL_PAYU" name="URL_PAYU" placeholder="URL_PAYU" class="col-xs-12" title="URL PAYU" value="<?php echo $frm["URL_PAYU"]; ?>">

            </div>
        </div>

    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            PSE
        </h3>
    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Habilitar PSE? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PasarelaPSE"], 'PasarelaPSE', "class='input '") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL PAGO </label>
            <div class="col-sm-8">
                <input type="text" id="UrlPago" name="UrlPago" placeholder="Url Pago" class="col-xs-12" title="Url Pago" value="<?php echo $frm["UrlPago"]; ?>">
            </div>
        </div>
    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            PAY ZEN
        </h3>
    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Habilitar PSE? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PasarelaPayZen"], 'PasarelaPayZen', "class='input '") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL PAGO </label>
            <div class="col-sm-8">
                <input type="text" id="UrlPagoPayZen" name="UrlPagoPayZen" placeholder="Url Pago" class="col-xs-12" title="Url Pago" value="<?php echo $frm["UrlPagoPayZen"]; ?>">
            </div>
        </div>
    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            Pay Phone
        </h3>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Habilitar PayPhone? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PasarelaPayPhone"], 'PasarelaPayPhone', "class='input '") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL PAGO PayPhone </label>
            <div class="col-sm-8">
                <input type="text" id="UrlPagoPayPhone" name="UrlPagoPayPhone" placeholder="Url Pago" class="col-xs-12" title="Url Pago PayPhone" value="<?php echo $frm["UrlPagoPayPhone"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Identificador </label>
            <div class="col-sm-8">
                <input type="text" id="Identificador" name="Identificador" placeholder="Identificador" class="col-xs-12" title="Identificador" value="<?php echo $frm["Identificador"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Id Cliente </label>
            <div class="col-sm-8">
                <input type="text" id="Id Cliente" name="IdCliente" placeholder="IdCliente" class="col-xs-12" title="IdCliente" value="<?php echo $frm["IdCliente"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clave secreta </label>
            <div class="col-sm-8">
                <input type="password" id="Clavesecreta" name="Clavesecreta" placeholder="Clavesecreta" class="col-xs-12" title="Clavesecreta" value="<?php echo $frm["Clavesecreta"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clave Codificacion </label>
            <div class="col-sm-8">
                <input type="password" id="ClaveCodificacion" name="ClaveCodificacion" placeholder="ClaveCodificacion" class="col-xs-12" title="ClaveCodificacion" value="<?php echo $frm["ClaveCodificacion"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Token </label>
            <div class="col-sm-8">
                <input type="text" id="TokenPayPhone" name="TokenPayPhone" placeholder="TokenPayPhone" class="col-xs-12" title="TokenPayPhone" value="<?php echo $frm["TokenPayPhone"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Store ID </label>
            <div class="col-sm-8">
                <input type="text" id="StoreIDPayPhone" name="StoreIDPayPhone" placeholder="StoreIDPayPhone" class="col-xs-12" title="StoreIDPayPhone" value="<?php echo $frm["StoreIDPayPhone"]; ?>">
            </div>
        </div>

    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            PLACE TO PAY
        </h3>
    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Habilitar PLACE TO PAY ? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PasarelaPLACETOPAY"], 'PasarelaPLACETOPAY', "class='input '") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Login </label>
            <div class="col-sm-8">
                <input type="text" id="LoginPlaceToPay" name="LoginPlaceToPay" placeholder="Login Place To Pay" class="col-xs-12" title="Login Place ToPay" value="<?php echo $frm["LoginPlaceToPay"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Secret Key </label>
            <div class="col-sm-8">
                <input type="password" id="SecretKeyPlaceToPay" name="SecretKeyPlaceToPay" placeholder="" class="col-xs-12" title="SecretKeyPlaceToPay" value="<?php echo $frm["SecretKeyPlaceToPay"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modo de Pruebas? </label>

            <div class="col-sm-8">
                <select name="IsTestPlaceToPay" id="IsTestPlaceToPay" class="form-control">
                    <option value="1" <?php if ($frm["IsTestPlaceToPay"] == 1) echo "selected"; ?>>Si son pruebas</option>
                    <option value="0" <?php if ($frm["IsTestPlaceToPay"] == 0) echo "selected"; ?>>No son pruebas</option>
                </select>
            </div>
        </div>

    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Place To Pay </label>
            <div class="col-sm-8">
                <input type="text" id="UrlPlaceToPay" name="UrlPlaceToPay" placeholder="Url Pago" class="col-xs-12" title="Url Pago Placetopay" value="<?php echo $frm["UrlPlaceToPay"]; ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Endpoint(Pruebas) </label>
            <div class="col-sm-8">
                <input type="text" id="EndpointPlaceToPayPruebas" name="EndpointPlaceToPayPruebas" placeholder="Endpoint Place To Pay Pruebas" class="col-xs-12" title="Endpoint Place To Pay Pruebas" value="<?php echo $frm["EndpointPlaceToPayPruebas"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Endpoint(Producción) </label>
            <div class="col-sm-8">
                <input type="text" id="EndpointPlaceToPayProduccion" name="EndpointPlaceToPayProduccion" placeholder="Endpoint Place To Pay Produccion" class="col-xs-12" title="Endpoint Place To Pay Produccion" value="<?php echo $frm["EndpointPlaceToPayProduccion"]; ?>">
            </div>
        </div>
    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            ZONA VIRTUAL
        </h3>
    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Habilitar ZONA VIRTUAL ? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PasarelaZonaVirtual"], 'PasarelaZonaVirtual', "class='input '") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> ID Tienda </label>
            <div class="col-sm-8">
                <input type="text" id="IDTiendaZona" name="IDTiendaZona" placeholder="ID Tienda Zona" class="col-xs-12" title="IDTiendaZona" value="<?php echo $frm["IDTiendaZona"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clave WS</label>
            <div class="col-sm-8">
                <input type="password" id="ClaveZona" name="ClaveZona" placeholder="Clave" class="col-xs-12" title="ClaveZona" value="<?php echo $frm["ClaveZona"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo Ruta WS</label>
            <div class="col-sm-8">
                <input type="text" id="CodigoRutaZona" name="CodigoRutaZona" placeholder="Codigo Ruta" class="col-xs-12" title="Codigo Ruta" value="<?php echo $frm["CodigoRutaZona"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo de servicio principal</label>
            <div class="col-sm-8">
                <input type="text" id="CodigoServicioZona" name="CodigoServicioZona" placeholder="Codigo Servicio" class="col-xs-12" title="Codigo Servicio" value="<?php echo $frm["CodigoServicioZona"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario para verificacion de transaccion</label>
            <div class="col-sm-8">
                <input type="text" id="UsuarioTransaccionZona" name="UsuarioTransaccionZona" placeholder="Usuario Transaccion Zona" class="col-xs-12" title="Usuario Transaccion Zona" value="<?php echo $frm["UsuarioTransaccionZona"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clave para verificacion de transaccion</label>
            <div class="col-sm-8">
                <input type="password" id="ClaveTransaccionZona" name="ClaveTransaccionZona" placeholder="Clave Transaccion Zona" class="col-xs-12" title="Clave Transaccion Zona" value="<?php echo $frm["ClaveTransaccionZona"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url proceso pago </label>
            <div class="col-sm-8">
                <input type="text" id="UrlZona" name="UrlZona" placeholder="UrlZona" class="col-xs-12" title="Url Zona" value="<?php echo $frm["UrlZona"]; ?>">
            </div>
        </div>

    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            CREDIBANCO REST
        </h3>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Habilitar Credibanco Ultima Version ? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PasarelaCredibancoApi"], 'PasarelaCredibancoApi', "class='input '") ?>
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Retorno </label>
            <div class="col-sm-8">
                <input type="text" id="UrlRetornoApiCredibanco" name="UrlRetornoApiCredibanco" placeholder="URL Retorno" class="col-xs-12" title="UrlRetornoApiCredibanco" value="<?php echo $frm["UrlRetornoApiCredibanco"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Pasarela Pago </label>
            <div class="col-sm-8">
                <input type="text" id="UrlPagoApiCredibanco" name="UrlPagoApiCredibanco" placeholder="URL Pago" class="col-xs-12" title="UrlPagoApiCredibanco" value="<?php echo $frm["UrlPagoApiCredibanco"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario API </label>
            <div class="col-sm-8">
                <input type="text" id="UsuarioApiCredibanco" name="UsuarioApiCredibanco" placeholder="Usuario Api" class="col-xs-12" title="UsuarioApiCredibanco" value="<?php echo $frm["UsuarioApiCredibanco"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Contraseña API </label>
            <div class="col-sm-8">
                <input type="password" id="PassApiCredibanco" name="PassApiCredibanco" placeholder="Contaseña Api" class="col-xs-12" title="PassApiCredibanco" value="<?php echo $frm["PassApiCredibanco"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modo de Pruebas? </label>
            <div class="col-sm-8">
                <select name="IsTestCredibancoApi" id="IsTestCredibancoApi" class="form-control">
                    <option value="1" <?php if ($frm["IsTestCredibancoApi"] == 1) echo "selected"; ?>>Si son pruebas</option>
                    <option value="0" <?php if ($frm["IsTestCredibancoApi"] == 0) echo "selected"; ?>>No son pruebas</option>
                </select>
            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            CREDIBANCO WEB
        </h3>
    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Habilitar Credibanco ? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PasarelaCredibanco"], 'PasarelaCredibanco', "class='input '") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> ID Tienda </label>
            <div class="col-sm-8">
                <input type="text" id="IDTiendaCredibanco" name="IDTiendaCredibanco" placeholder="ID Tienda" class="col-xs-12" title="IDTiendaCredibanco" value="<?php echo $frm["IDTiendaCredibanco"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> purchase Plan Id</label>
            <div class="col-sm-8">
                <input type="text" id="purchasePlanId" name="purchasePlanId" placeholder="purchasePlanId" class="col-xs-12" title="purchasePlanId" value="<?php echo $frm["purchasePlanId"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> purchaseQuotaId</label>
            <div class="col-sm-8">
                <input type="text" id="purchaseQuotaId" name="purchaseQuotaId" placeholder="purchaseQuotaId" class="col-xs-12" title="purchaseQuotaId" value="<?php echo $frm["purchaseQuotaId"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> purchaseTerminalCode</label>
            <div class="col-sm-8">
                <input type="text" id="purchaseTerminalCode" name="purchaseTerminalCode" placeholder="purchaseTerminalCode" class="col-xs-12" title="purchaseTerminalCode" value="<?php echo $frm["purchaseTerminalCode"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> VI</label>
            <div class="col-sm-8">
                <input type="text" id="VI" name="VI" placeholder="VI" class="col-xs-12" title="VI" value="<?php echo $frm["VI"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> LLAVE.VPOS.CRB.CRYPTO.1024.X509</label>
            <div class="col-sm-8">
                <textarea id="LlavePublicaCredibanco" name="LlavePublicaCredibanco" cols="10" rows="5" class="col-xs-12" title="LlavePublicaCredibanco"><?php echo $frm["LlavePublicaCredibanco"]; ?></textarea>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> c club .firma.privada </label>
            <div class="col-sm-8">
                <textarea id="LlavePrivadaCredibanco" name="LlavePrivadaCredibanco" cols="10" rows="5" class="col-xs-12" title="LlavePrivadaCredibanco"><?php echo $frm["LlavePrivadaCredibanco"]; ?></textarea>
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> LLAVE.VPOS.CRB.SIGN.1024.X509</label>
            <div class="col-sm-8">
                <textarea id="LlaveCriptoCredibanco" name="LlaveCriptoCredibanco" cols="10" rows="5" class="col-xs-12" title="LlaveCriptoCredibanco"><?php echo $frm["LlaveCriptoCredibanco"]; ?></textarea>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> c club.cifrado.privada </label>
            <div class="col-sm-8">
                <textarea id="LlaveFirmaPrivadaCredibanco" name="LlaveFirmaPrivadaCredibanco" cols="10" rows="5" class="col-xs-12" title="LlaveFirmaPrivadaCredibanco"><?php echo $frm["LlaveFirmaPrivadaCredibanco"]; ?></textarea>
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url credibanco</label>
            <div class="col-sm-8">
                <input type="text" id="UrlCredibanco" name="UrlCredibanco" placeholder="UrlCredibanco" class="col-xs-12" title="UrlCredibanco" value="<?php echo $frm["UrlCredibanco"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Modo pruebas ? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CredibancoPruebas"], 'CredibancoPruebas', "class='input '") ?>
            </div>
        </div>

    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo Unico credibanco</label>
            <div class="col-sm-8">
                <input type="text" id="CodigoUnico" name="CodigoUnico" placeholder="CodigoUnico" class="col-xs-12" title="CodigoUnico" value="<?php echo $frm["CodigoUnico"]; ?>">
            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            CREDIBANCO PAGO
        </h3>
    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Comercio PAGO ? </label>
            <div class="col-sm-8">
                <input type="text" id="ComercioPago" name="ComercioPago" placeholder="Comercio Pago" class="col-xs-12" title="ComercioPago" value="<?php echo $frm["ComercioPago"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo Unico PAGO </label>
            <div class="col-sm-8">
                <input type="text" id="CodigoUnicoPago" name="CodigoUnicoPago" placeholder="Codigo Unico Pago" class="col-xs-12" title="Codigo Unico Pago" value="<?php echo $frm["CodigoUnicoPago"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Terminal PAGO</label>
            <div class="col-sm-8">
                <input type="text" id="TerminalPago" name="TerminalPago" placeholder="TerminalPago" class="col-xs-12" title="TerminalPago" value="<?php echo $frm["TerminalPago"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario Plataforma PAGO</label>
            <div class="col-sm-8">
                <input type="text" id="UsuarioPlataformaPago" name="UsuarioPlataformaPago" placeholder="UsuarioPlataformaPago" class="col-xs-12" title="UsuarioPlataformaPago" value="<?php echo $frm["UsuarioPlataformaPago"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Contraseña PAGO</label>
            <div class="col-sm-8">
                <input type="text" id="ClavePago" name="ClavePago" placeholder="ClavePago" class="col-xs-12" title="ClavePago" value="<?php echo $frm["ClavePago"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> IDComercio PAGO</label>
            <div class="col-sm-8">
                <input type="text" id="IDComercioPago" name="IDComercioPago" placeholder="IDComercioPago" class="col-xs-12" title="IDComercioPago" value="<?php echo $frm["IDComercioPago"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> IDACQUIRER PAGO</label>
            <div class="col-sm-8">
                <input type="text" id="IDAcquirerPago" name="IDAcquirerPago" placeholder="IDAcquirerPago" class="col-xs-12" title="IDAcquirerPago" value="<?php echo $frm["IDAcquirerPago"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pdf terminos y condiciones </label>
            <div class="col-sm-8">
                <? if (!empty($frm["ArchivoTerminosPago"])) {
                    echo "<a href='" . CLUB_ROOT . "$frm[ArchivoTerminosPago]' target='_blank'>" . $frm[ArchivoTerminosPago] . "</a>";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[ArchivoTerminosPago]&campo=ArchivoTerminosPago&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="ArchivoTerminosPago" id=file class="col-xs-12" title="ArchivoTerminosPago" type="file" size="25" style="font-size: 10px">

            </div>
        </div>

    </div>




    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            E-COLLECT
        </h3>
    </div>


    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> EtyCode</label>

            <div class="col-sm-8">
                <input type="text" id="EtyCode" name="EtyCode" placeholder="EtyCode" class="col-xs-12" title="EtyCode" value="<?php echo $frm["EtyCode"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Srv Code</label>
            <div class="col-sm-8">
                <input type="text" id="SrvCode" name="SrvCode" placeholder="SrvCode" class="col-xs-12" title="SrvCode" value="<?php echo $frm["SrvCode"]; ?>">

            </div>
        </div>

    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Api Key</label>

            <div class="col-sm-8">
                <input type="text" id="ApiKeyEcollect" name="ApiKeyEcollect" placeholder="ApiKeyEcollect" class="col-xs-12" title="Api Key" value="<?php echo $frm["ApiKeyEcollect"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> End Point</label>
            <div class="col-sm-8">
                <input type="text" id="EndPoint" name="EndPoint" placeholder="End Point" class="col-xs-12" title="End Point" value="<?php echo $frm["EndPoint"]; ?>">

            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Pasarela</label>

            <div class="col-sm-8">
                <input type="text" id="UrlEcollect" name="UrlEcollect" placeholder="UrlEcollect" class="col-xs-12" title="Api Key" value="<?php echo $frm["UrlEcollect"]; ?>">
            </div>
        </div>

    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            Pasarela Wompi
        </h3>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Wompi PSE </label>
            <div class="col-sm-8">
                <input type="text" id="URLWompi" name="URLWompi" placeholder="URL Wompi" class="col-xs-12" title="URL Wompi" value="<?php echo $frm["URLWompi"]; ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Wompi Widget</label>
            <div class="col-sm-8">
                <input type="text" id="WompiWidget" name="WompiWidget" placeholder="Wompi Widget" class="col-xs-12" title="Wompi Widget" value="<?php echo $frm["WompiWidget"]; ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> URL Wompi Chekout</label>
            <div class="col-sm-8">
                <input type="text" id="WompiChekout" name="WompiChekout" placeholder="Wompi Chekout" class="col-xs-12" title="WompiChekout" value="<?php echo $frm["WompiChekout"]; ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Api Key Wompi </label>
            <div class="col-sm-8">
                <input type="password" id="ApiKeyWompi" name="ApiKeyWompi" placeholder="ApiKey Wompi" class="col-xs-12" title="ApiKey Wompi" value="<?php echo $frm["ApiKeyWompi"]; ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Integrity Key Wompi </label>
            <div class="col-sm-8">
                <input type="password" id="IntegridadWompi" name="IntegridadWompi" placeholder="IntegrityKey Wompi" class="col-xs-12" title="IntegridadWompi" value="<?php echo $frm["IntegridadWompi"]; ?>">
            </div>
        </div>
    </div>
    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modo </label>
            <div class="col-sm-8">
                <select name="IsTestWompi" id="IsTestWompi" class="form-control">
                    <option value="1" <?php if ($frm["IsTestWompi"] == 1) echo "selected"; ?>>Pruebas</option>
                    <option value="0" <?php if ($frm["IsTestWompi"] == 0) echo "selected"; ?>>Producción</option>
                </select>
            </div>
        </div>
    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            PayPal
        </h3>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Url PayPal </label>
            <div class="col-sm-8">
                <input type="text" id="UrlPayPal" name="UrlPayPal" placeholder="Llave PayPal" class="col-xs-12" title="UrlPayPal" value="<?php echo $frm["UrlPayPal"]; ?>">

            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Key PayPal </label>
            <div class="col-sm-8">
                <input type="text" id="LlavePayPal" name="LlavePayPal" placeholder="Llave PayPal" class="col-xs-12" title="LlavePayPal" value="<?php echo $frm["LlavePayPal"]; ?>">
            </div>
        </div>
    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon"></i>
            Orden de Club
        </h3>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Url Generar Orden </label>
            <div class="col-sm-8">
                <input type="text" id="PagoOrdenClub" name="PagoOrdenClub" placeholder="URL Orden" class="col-xs-12" title="PagoOrdenClub" value="<?php echo $frm["PagoOrdenClub"]; ?>">

            </div>
        </div>
    </div>




    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-envelope-o green"></i>
            Parametros Envio Correos
        </h3>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Remitente: </label>

            <div class="col-sm-8">
                <input id="RemitenteCorreo" type=text size=25 name="RemitenteCorreo" class="col-xs-12" title="Remitente Correo" value="<?= $frm["RemitenteCorreo"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Correo Remitente </label>

            <div class="col-sm-8">
                <input id="CorreoRemitente" type=text size=25 name="CorreoRemitente" class="col-xs-12" title="CorreoRemitente" value="<?= $frm["CorreoRemitente"] ?>">
            </div>
        </div>

    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-credit-card green"></i>
            Parametros WebView
        </h3>
    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje WebView 1</label>

            <div class="col-sm-8">

                <?php
                $oCuerpo = new FCKeditor("MensajeWebView1");
                $oCuerpo->BasePath = "js/fckeditor/";
                $oCuerpo->Height = 300;
                $oCuerpo->Width = 300;
                //$oCuerpo->EnterMode = "p";
                $oCuerpo->Value =  $frm["MensajeWebView1"];
                $oCuerpo->Create();
                ?>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje WebView 2</label>

            <div class="col-sm-8">

                <?php
                $oCuerpo = new FCKeditor("MensajeWebView2");
                $oCuerpo->BasePath = "js/fckeditor/";
                $oCuerpo->Height = 300;
                $oCuerpo->Width = 300;
                //$oCuerpo->EnterMode = "p";
                $oCuerpo->Value =  $frm["MensajeWebView2"];
                $oCuerpo->Create();
                ?>

            </div>
        </div>



    </div>

    <div class="form-group first ">


        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 1</label>
            <div class="col-sm-8">
                <input id="UrlWebView1" type=text size=25 name="UrlWebView1" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView1"] ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 2</label>
            <div class="col-sm-8">
                <input id="UrlWebView2" type=text size=25 name="UrlWebView2" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView2"] ?>">

            </div>
        </div>

    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encabezado Web View 3</label>
            <div class="col-sm-8">
                <input id="MensajeWebView3" type=text size=25 name="MensajeWebView3" class="col-xs-12" title="Url Web View" value="<?= $frm["MensajeWebView3"] ?>">

            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 3</label>
            <div class="col-sm-8">
                <input id="UrlWebView3" type=text size=25 name="UrlWebView3" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView3"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encabezado Web View 4</label>
            <div class="col-sm-8">
                <input id="MensajeWebView4" type=text size=25 name="MensajeWebView4" class="col-xs-12" title="Url Web View" value="<?= $frm["MensajeWebView4"] ?>">

            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 4</label>
            <div class="col-sm-8">
                <input id="UrlWebView4" type=text size=25 name="UrlWebView4" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView4"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encabezado Web View 5</label>
            <div class="col-sm-8">
                <input id="MensajeWebView5" type=text size=25 name="MensajeWebView5" class="col-xs-12" title="Url Web View" value="<?= $frm["MensajeWebView5"] ?>">

            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 5</label>
            <div class="col-sm-8">
                <input id="UrlWebView5" type=text size=25 name="UrlWebView5" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView5"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encabezado Web View 6</label>
            <div class="col-sm-8">
                <input id="MensajeWebView6" type=text size=25 name="MensajeWebView6" class="col-xs-12" title="Url Web View" value="<?= $frm["MensajeWebView6"] ?>">

            </div>
        </div>
        <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Url Web View 6</label>
            <div class="col-sm-8">
                <input id="UrlWebView6" type=text size=25 name="UrlWebView6" class="col-xs-12" title="Url Web View" value="<?= $frm["UrlWebView6"] ?>">
            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-envelope-o green"></i>
            Parametros PQR
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Respuesta automatica al crear pqr?: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["RespuestaAutomaticaPqr"], 'RespuestaAutomaticaPqr', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mensaje push al crear pqr: </label>

            <div class="col-sm-8">
                <input id=MensajePushCrearPqr type=text size=25 name=MensajePushCrearPqr class="col-xs-12 " title="Mensaje Push Crear Pqr" value="<?= $frm["MensajePushCrearPqr"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first">
        Respuesta automatica al crear PQR
        <div class="col-sm-12">
            <?php
            $oCuerpoQr = new FCKeditor("TextoRespuestaAutomaticaPqr");
            $oCuerpoQr->BasePath = "js/fckeditor/";
            $oCuerpoQr->Height = 200;
            //$oCuerpo->EnterMode = "p";
            $oCuerpoQr->Value =  $frm["TextoRespuestaAutomaticaPqr"];
            $oCuerpoQr->Create();
            ?>
        </div>
    </div>

    <div class="form-group first">
        Respuesta pesonalizada al responder PQR
        <div class="col-sm-12">
            <?php
            $oCuerpoQr = new FCKeditor("RespuestaPqr");
            $oCuerpoQr->BasePath = "js/fckeditor/";
            $oCuerpoQr->Height = 200;
            //$oCuerpo->EnterMode = "p";
            $oCuerpoQr->Value =  $frm["RespuestaPqr"];
            $oCuerpoQr->Create();
            ?>
        </div>
    </div>

    <div class="form-group first">
        Instrucciones QR
        <div class="col-sm-12">
            <?php
            $oCuerpoQr = new FCKeditor("InstruccionQr");
            $oCuerpoQr->BasePath = "js/fckeditor/";
            $oCuerpoQr->Height = 200;
            //$oCuerpo->EnterMode = "p";
            $oCuerpoQr->Value =  $frm["InstruccionQr"];
            $oCuerpoQr->Create();
            ?>
        </div>
    </div>

    <div class="form-group first">


        T&eacute;rminos y Condiciones

        <div class="col-sm-12">
            <?php
            $oCuerpo = new FCKeditor("Terminos");
            $oCuerpo->BasePath = "js/fckeditor/";
            $oCuerpo->Height = 400;
            //$oCuerpo->EnterMode = "p";
            $oCuerpo->Value =  $frm["Terminos"];
            $oCuerpo->Create();
            ?>
        </div>


    </div>



    <!--
                              <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> N&uacute;mero de invitados por socio por mes: </label>

										<div class="col-sm-8">
										  <input id=MaximoInvitadoSocio type=text size=25  name=MaximoInvitadoSocio class="col-xs-12" title="Maximo Invitado Socio" value="<?= $frm[MaximoInvitadoSocio] ?>">
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> N&uacute;mero de ingresos de invitado por mes:  </label>

										<div class="col-sm-8">
										  <input id=MaximoRepeticionInvitado type=text size=25  name=MaximoRepeticionInvitado class="col-xs-12" title="Maximo Repeticion Invitado" value="<?= $frm[MaximoRepeticionInvitado] ?>">
										</div>
								</div>

							</div>

                             <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cumplimiento obligatorio de invitados: </label>

										<div class="col-sm-8">
										  <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["CumplimientoInvitados"], "CumplimientoInvitados", "title=\"CumplimientoInvitados\"") ?>
										</div>
								</div>

							</div>
                            -->

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-glass green"></i>
            Configuracion de Servicios
        </h3>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-12">


            <?php

            // Consulto los servicios disponibles al usuario

            $sql_servicio_club = $dbo->query("select * from ServicioClub where IDClub = '" . $frm[IDClub] . "' and Activo = 'S'");
            while ($r_servicio_club = $dbo->object($sql_servicio_club)) {
                $servicio_club[] = $r_servicio_club->IDServicioMaestro;
            }

            $arrayop = array();
            // consulto las subsecciones
            //$query_servicios=$dbo->query("Select * from ServicioMaestro Where Publicar = 'S' Order by Nombre");
            //while($r=$dbo->object($query_servicios)){
            //$arrayservicio[$r->Nombre]=$r->IDServicioMaestro;
            //}


            //echo SIMHTML::formCheckGroup( $arrayservicio , $servicio_club , "ServicioClub[]") 
            ?>


            <table id="simple-table" class="table table-striped table-bordered table-hover">
                <tr>
                    <th>Activo</th>
                    <th>Servicio</th>
                    <th>Titulo</th>
                    <th>Orden</th>
                </tr>
                <tbody id="listacontactosanunciante">
                    <?php
                    $sql_servicio_club = "SELECT * FROM ServicioClub WHERE IDClub = '" . $frm[IDClub] . "'";
                    $r_servicio_club = $dbo->query($sql_servicio_club);
                    while ($row_servicio_club = $dbo->fetchArray($r_servicio_club)) {
                        $array_servicio_club[$row_servicio_club["IDServicioMaestro"]] = $row_servicio_club;
                    }

                    $r_servicioclub = &$dbo->all("ServicioMaestro", "Publicar = 'S' Order by Nombre");

                    while ($r = $dbo->object($r_servicioclub)) {
                    ?>

                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                            <td aling="center">
                                <input type="checkbox" name="IDServicioMaestro<?php echo $r->IDServicioMaestro; ?>" id="IDServicioMaestro<?php echo $r->IDServicioMaestro; ?>" <?php if (in_array($r->IDServicioMaestro, $servicio_club)) echo "checked"; ?>>
                            </td>
                            <td>
                                <?php
                                echo $r->Nombre;
                                if (!empty($r->Descripcion))
                                    echo  " (" . $r->Descripcion . ")";
                                ?>
                            </td>
                            <td><input id=TituloServicio<?php echo $r->IDServicioMaestro; ?> type=text size=25 name=TituloServicio<?php echo $r->IDServicioMaestro; ?> class="col-xs-12" title="Titulo Servicio" value="<?php echo $array_servicio_club[$r->IDServicioMaestro]["TituloServicio"]; ?>"></td>
                            <td>
                                <input id=OrdenServicio<?php echo $r->IDServicioMaestro; ?> type=text size=25 name=OrdenServicio<?php echo $r->IDServicioMaestro; ?> class="col-xs-12" title="Orden" value="<?php echo $array_servicio_club[$r->IDServicioMaestro]["Orden"]; ?>">
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <tr>
                    <th class="texto" colspan="14"></th>
                </tr>
            </table>









        </div>




    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-sitemap green"></i>
            Configuracion de Modulos
        </h3>
    </div>
    <div class="form-group first ">

        <div class="col-xs-12 col-sm-12">
            <?php
            // Consulto los modulos disponibles del club
            $sql_modulo_club = $dbo->query("select * from ClubModulo where IDClub = '" . $frm[IDClub] . "' and Activo = 'S'");
            while ($r_modulo_club = $dbo->object($sql_modulo_club)) {
                $modulo_club[] = $r_modulo_club->IDModulo;
            }

            // Consulto los modulos disponibles del club
            $sql_modulo = $dbo->query("select * from Modulo where 1");
            while ($r_modulo = $dbo->object($sql_modulo)) {
                $modulo_datos[$r_modulo->IDModulo] = $r_modulo->Nombre;
            }
            ?>
            <table id="simple-table" class="table table-striped table-bordered table-hover">
                <tr>
                    <th>Activo</th>
                    <th>Modulo</th>
                    <th>Titulo Club</th>
                    <th>Icono</th>
                    <th>Orden</th>
                    <th>Ubicacion</th>
                </tr>
                <tbody id="listacontactosanunciante">
                    <?php

                    $r_modulo = &$dbo->all("ClubModulo", "IDClub = '" . $frm[IDClub] . "'");

                    while ($r = $dbo->object($r_modulo)) {
                    ?>

                        <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                            <td aling="center">
                                <input type="checkbox" name="IDModulo<?php echo $r->IDModulo; ?>" id="IDModulo<?php echo $r->IDModulo; ?>" <?php if (in_array($r->IDModulo, $modulo_club)) echo "checked"; ?>>
                            </td>
                            <td><?php echo $modulo_datos[$r->IDModulo]; ?></td>
                            <td>
                                <input id=Titulo<?php echo $r->IDModulo; ?> type=text size=25 name=Titulo<?php echo $r->IDModulo; ?> class="col-xs-12" title="Titulo" value="<?= $r->Titulo ?>" placeholder="Titulo Menu Central">
                                <input id=TituloLateral<?php echo $r->IDModulo; ?> type=text size=25 name=TituloLateral<?php echo $r->IDModulo; ?> class="col-xs-12" title="Titulo Lateral" value="<?= $r->TituloLateral ?>" placeholder="Titulo menu lateral">
                            </td>
                            <td>
                                <? if (!empty($r->Icono)) {
                                    echo "<img src='" . MODULO_ROOT . "$r->Icono' width=55 >";
                                ?>
                                    <a href="<? echo $script . " .php?action=delfotomodulo&foto=$r->Icono&campo=Icono&idclubmodulo=" . $r->IDClubModulo; ?>&id=<?php echo $frm[IDClub]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                <?
                                } // END if
                                ?>
                                <input name="Icono<?php echo $r->IDModulo; ?>" id=Icono<?php echo $r->IDModulo; ?> class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                <input type="hidden" name="ImagenOriginal<?php echo $r->IDModulo; ?>" id="ImagenOriginal<?php echo $r->IDModulo; ?>" value="<?php echo $r->Icono; ?>">

                                <?php
                                if ($frm[IDClub] == "51") {
                                    if (!empty($r->IconoLateral)) {
                                        echo "<img src='" . MODULO_ROOT . "$r->IconoLateral' width=55 >";
                                ?>
                                        <a href="<? echo $script . " .php?action=delfotomodulo&foto=$r->IconoLateral&campo=IconoLateral&idclubmodulo=" . $r->IDClubModulo; ?>&id=<?php echo $frm[IDClub]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                    <?
                                    } // END if
                                    ?>
                                    <br>Icono lateral<input name="IconoLateral<?php echo $r->IDModulo; ?>" id=IconoLateral<?php echo $r->IDModulo; ?> class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                                    <input type="hidden" name="ImagenOriginalLateral<?php echo $r->IDModulo; ?>" id="ImagenOriginalLateral<?php echo $r->IDModulo; ?>" value="<?php echo $r->IconoLateral; ?>">
                                <?php } ?>
                            </td>



                            <td><input id=Orden<?php echo $r->IDModulo; ?> type=text size=25 name=Orden<?php echo $r->IDModulo; ?> class="col-xs-12" title="Orden" value="<?= $r->Orden ?>"></td>
                            <td>
                                <?php
                                unset($ubicacion_modulo);
                                if (!empty($r->Ubicacion)) :
                                    $ubicacion_modulo = explode("|", $r->Ubicacion);
                                endif;
                                ?>

                                <input type="checkbox" name="UbicacionModulo<?php echo $r->IDModulo; ?>[]" <?php if (in_array("Lateral", $ubicacion_modulo)) echo "checked"; ?> value="Lateral"> Menu Lateral app
                                <br><input type="checkbox" name="UbicacionModulo<?php echo $r->IDModulo; ?>[]" <?php if (in_array("Central", $ubicacion_modulo)) echo "checked"; ?> value="Central">Menu central app
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <tr>
                    <th class="texto" colspan="16"></th>
                </tr>
            </table>


        </div>



    </div>






    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
            </button>


        </div>
    </div>

</form>