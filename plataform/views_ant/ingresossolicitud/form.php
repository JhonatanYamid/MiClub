<?php
$frm_get =  SIMUtil::makeSafe($_GET);
$IngresosSolicitud = $dbo->fetchAll('IngresosSolicitud', 'IDIngresosSolicitud = ' . $frm_get['id'], 'array');
$Ingresos = $dbo->fetchAll('Ingresos', 'IDIngresos = ' . $IngresosSolicitud['IDIngresos'], 'array');

// Validar si es un Usuario o un Socio
if ($IngresosSolicitud["IDUsuario"] > 0) {
    $sqlUsuario = "SELECT * FROM Usuario WHERE IDUsuario = " . $IngresosSolicitud['IDUsuario'];
    $queryUsuario = $dbo->query($sqlUsuario);
    $rowUser = $dbo->assoc($queryUsuario);
    $user = "Empleado";
} else {
    $sqlSocio = "SELECT * FROM Socio WHERE IDSocio = " . $IngresosSolicitud['IDSocio'];
    $querySocio = $dbo->query($sqlSocio);
    $rowUser = $dbo->assoc($querySocio);
    $user = "Socio";
}


?>
<style>
    a {
        white-space: nowrap;
        width: 100%;
        overflow: hidden;
        /* text-overflow: ellipsis !important; */
    }
</style>

<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
        </h4>
    </div>
    <br>
    <div id="jqGrid_container">
        <form name="frmexportaingresossolicitud" id="frmexportaingresossolicitud" method="post" enctype="multipart/form-data" action="procedures/zip-ingresosSolicitud.php">
            <table>
                <tr>
                    <td>
                        <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                        <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $IngresosSolicitud['IDSocio']; ?>">
                        <!-- <input class="btn btn-info" type="submit" name="expbnr" id="expbnr" value=""> -->
                        <button class="btn btn-info ExportarZip" type="submit">Exportar ZIP <i class="fa fa-file-archive-o" aria-hidden="true"></i></button>
                    </td>
                <tr>
            </table>
        </form>

    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-sm-12">
                    <div class="tabbable" id="myTABS" role="tablist">
                        <ul class="nav nav-tabs" id="myTab">
                            <li class="<?php if (empty($_GET['tabencuesta'])) echo "active"; ?>">
                                <a data-toggle="tab" href="#home">
                                    <i class="green ace-icon fa fa-home bigger-120"></i>
                                    Datos personales
                                </a>
                            </li>

                            <li class="<?php if ($_GET['tabencuesta'] == "experiencia") echo "active"; ?>">
                                <a data-toggle="tab" href="#experiencia">
                                    <i class="green ace-icon fa fa-check-circle bigger-120"></i>
                                    Experiencia
                                </a>
                            </li>

                            <li class="<?php if ($_GET['tabencuesta'] == "beneficiarios") echo "active"; ?>">
                                <a data-toggle="tab" href="#beneficiarios">
                                    <i class="green ace-icon fa fa-users bigger-120"></i>
                                    Beneficiarios
                                </a>
                            </li>

                            <li class="<?php if ($_GET['tabencuesta'] == "estudios") echo "active"; ?>">
                                <a data-toggle="tab" href="#estudios">
                                    <i class="green ace-icon fa fa-book bigger-120"></i>
                                    Estudios
                                </a>
                            </li>

                            <li class="<?php if ($_GET['tabencuesta'] == "deportes") echo "active"; ?>">
                                <a data-toggle="tab" href="#deportes">
                                    <i class="green ace-icon fa fa-futbol-o bigger-120"></i>
                                    Deportes
                                </a>
                            </li>
                            <li class="<?php if ($_GET['tabencuesta'] == "idiomas") echo "active"; ?>">
                                <a data-toggle="tab" href="#idiomas">
                                    <i class="green ace-icon fa fa-globe bigger-120"></i>
                                    Idiomas
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div id="home" class="tab-pane fade <?php if (empty($_GET['tabencuesta'])) echo "in active"; ?> ">
                                <?php require_once("datosPersonales.php");
                                ?>
                            </div>
                            <div id="experiencia" class="tab-pane fade <?php if ($_GET['tabencuesta'] == "experiencia") echo "in active"; ?>">
                                <?php include_once("datosExperiencias.php");
                                ?>
                            </div>
                            <div id="beneficiarios" class="tab-pane fade <?php if ($_GET['tabencuesta'] == "beneficiarios") echo "in active"; ?>">
                                <?php include("beneficiarios.php");
                                ?>
                            </div>
                            <div id="estudios" class="tab-pane fade <?php if ($_GET['tabencuesta'] == "estudios") echo "in active"; ?>">
                                <?php //include("estudios.php"); 
                                ?>
                            </div>
                            <div id="deportes" class="tab-pane fade <?php if ($_GET['tabencuesta'] == "deportes") echo "in active"; ?>">
                                <?php //include("deportes.php"); 
                                ?>
                            </div>
                            <div id="idiomas" class="tab-pane fade <?php if ($_GET['tabencuesta'] == "idiomas") echo "in active"; ?>">
                                <?php //include("idiomas.php"); 
                                ?>
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