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


                    <div class="col-sm-12">
                        <div class="tabbable">
                            <ul class="nav nav-tabs" id="myTab">


                                <li class="<?php if (empty($_GET[tabtiporeservahotel])) echo "active"; ?>">
                                    <a data-toggle="tab" href="#home">
                                        <i class="green ace-icon fa fa-home bigger-120"></i>
                                        Tipo Reserva Hotel
                                    </a>
                                </li>


                                <?php
                                //validar si es pasadia
                                $TipoReservaHotel = $dbo->getFields("TipoReservaHotel", "Tipo", "IDClub = '" . SIMUser::get("club") . "' AND Tipo='Pasadia' AND  IDTipoReservaHotel='" . $_GET[id] . "'");
                                if (SIMNet::req("action") == "edit" && !empty($TipoReservaHotel)) : ?>


                                    <li class="<?php if ($_GET[tabtiporeservahotel] == "campohotelpasadia") echo "active"; ?>">
                                        <a data-toggle="tab" href="#messages">
                                            <i class="green ace-icon fa fa-check-circle bigger-120"></i>
                                            Campo Hotel Pasadia
                                        </a>
                                    </li>



                                <?php endif; ?>


                            </ul>

                            <div class="tab-content">

                                <div id="home" class="tab-pane fade <?php if (empty($_GET[tabtiporeservahotel])) echo "in active"; ?> ">
                                    <?php include("datos.php"); ?>
                                </div>


                                <?php if (SIMNet::req("action") == "edit") : ?>


                                    <div id="messages" class="tab-pane fade <?php if ($_GET[tabtiporeservahotel] == "messages") echo "in active"; ?>">
                                        <?php include("campohotelpasadia.php"); ?>
                                    </div>


                                <?php endif; ?>

                            </div>
                        </div>
                    </div>



                </div>
            </div>




        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>