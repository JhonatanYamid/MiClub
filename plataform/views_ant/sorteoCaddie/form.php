<?php
if ($frm != "") {
    ?>
    <div class="widget-box transparent" id="recent-box">
        <div class="widget-header">
            <h4 class="widget-title lighter smaller">
                <i class="ace-icon fa fa-users orange"></i>SORTEO
                <?php echo strtoupper(SIMReg::get("title")); ?>
            </h4>
        </div>

        <div class="widget-body">
            <div class="widget-main padding-4">
                <br><br>
                <form class="form-horizontal formvalida" role="form" method="post" id="frmSorteocaddie" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                <div class="row">

                    <div class="col-sm-12">
                        <div class="tabbable">
                            <ul class="nav nav-tabs" id="myTab">
                                <?php
                                $indice = 0;
                                foreach ($frm["sorteo"] AS $index => $categoria) {
                                    ?>
                                    <li class="<?php if ($indice == 0) echo "active"; ?>">
                                        <a data-toggle="tab" href="#categoria<?php echo $index; ?>">
                                            <i class="green ace-icon fa fa-pencil-square-o bigger-120"></i>
                                            <?php echo $categoria[0]["categoria"]; ?>
                                        </a>
                                    </li>
                                    <?php
                                    $indice++;
                                }
                                ?>
                                
                            </ul>

                            <div class="tab-content">


                                <?php
                                $indice = 0;
                                foreach ($frm["sorteo"] AS $index => $categoria) {
                                    ?>
                                    <div id="categoria<?php echo $index; ?>" class="tab-pane fade <?php if ($indice == 0) echo "in active"; ?> ">
                                        <div style="max-height: 200px;overflow: auto;">
                                            <table style="width: 100%;border: #000 1px solid;text-align: center;" class="tablaReporte">
                                                <thead>
                                                    <tr style="background: #3fb0ac;height: 30px;color: #FFF;text-align: center;">
                                                        <th style="width: 20%;text-align: center;">Numero documento</th>
                                                        <th style="text-align: center;">Nombre</th>
                                                        <th style="text-align: center;">Apellido</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($categoria AS $index2 => $caddie) {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $caddie["numeroDocumento"]; ?></td>
                                                            <td><?php echo $caddie["nombre"]; ?></td>
                                                            <td>
                                                                <?php echo $caddie["apellido"]; ?>
                                                                 </td>
                                                        </tr>

                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php
                                    $indice++;
                                }
                                ?>
                            </div>
                        </div>


                    </div>
                </div>
            </form>

            </div>
        </div>
    </div>

    <?php
}
?>


<?php
include( "cmp/footer_scripts.php" );
?>

<script type="text/javascript">

   $(".btnRealizarSorteo").on("click", function(e)
   {
       var fechaInicio = $("#fechaInicio").val();
       var fechaFin = $("#fechaFin").val();

       if(fechaInicio != "" && fechaFin != "")
       {
        $("#fechaInicioSorteo").val(fechaInicio);
        $("#fechaFinSorteo").val(fechaFin);
        var form = $(this).attr("rel");
	$( "#" + form ).submit();
       }
       else
       {
         alert("debe seleccionar la fecha de inicio y fecha de fin para continuar");
       }

   });




</script>
