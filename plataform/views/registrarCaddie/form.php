<?php
if (count($frm) > 0) {
    ?>
    <div class="widget-box transparent" id="recent-box">
        <div class="widget-header">
            <h4 class="widget-title lighter smaller">
                <i class="ace-icon fa fa-users orange"></i>REGISTRO DE CADDIE
                <?php //echo strtoupper(SIMReg::get("title"));  ?>
            </h4>


        </div>

        <div class="widget-body">
            <div class="widget-main padding-4">
                <br>
                <div class="row">               
                    <div class="col-md-12 col-xs-12">

                        <table style="width: 60%">
                            <tr>
                                <td style="width: 20%"><b>Nombres:</b></td>
                                <td style="width: 35%"><?= $frm['nombre']; ?></td>
                                <td style="width: 20%"><b>Apellidos:</b></td>
                                <td><?= $frm['apellido']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Numero documento:</b></td>
                                <td><?= $frm['numeroDocumento']; ?></td>
                                <td><b>Categoria:</b></td>
                                <td><?= $frm['categoria']; ?></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <?php
                                    if (!empty($frm[foto])) {
                                        $foto = $frm[foto];
                                        $foto = str_replace("_", "jsalcm", $foto);
                                        echo "<img src='" . CADDIE_ROOT . "$frm[foto]' width=55 >";
                                    }// END if
                                    ?>
                                </td>                               
                            </tr>
                        </table>                        

                    </div>

                </div>   
                <form id="frmregistrarCaddie" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="POST">
                <div class="clearfix form-actions">
                    <div class="col-xs-12 text-center">
                        <input type="hidden" name="ID" id="ID" value="<?php echo $frm["IDCaddie"] ?>" />
                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                        <input type="hidden" name="IDClub" id="IDClub" value="<?php
                        if (empty($frm["IDClub"]))
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

            </div><!-- /.widget-main -->
        </div><!-- /.widget-body -->
    </div><!-- /.widget-box -->

    <?php
}
include( "cmp/footer_scripts.php" );
?>