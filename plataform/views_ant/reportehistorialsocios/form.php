<div class="widget-box transparent" id="recent-box">

    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">

                    <!-- PAGE CONTENT BEGINS -->

                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header" style="padding:10px 50px 10px 50px;color:white !important;text-align: center;background-color: #6fb3e0;font-size: 30px;">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h3 style="font-size: 24px;"><span class="glyphicon glyphicon-alert" style="padding: 5px 5px;">

                                        </span>Que datos desea consultar ?</h3>
                                </div>
                                <div class="modal-body" style="padding:30px 50px;">
                                    <p> </p>
                                    <form role="form">

                                        <div class="form-group">
                                            <select name="TipoReporte" id="TipoReporte" class="form-control mandatory">
                                                <option value="">[ Seleccione el Tipo de Reporte ]</option>
                                                <option value="Socio">Socio</option>
                                                <option value="Funcionario">Funcionario</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-sucess btnEnviar" id="submitObservacion"><span class="ace-icon fa fa-check"></span> Confirmar !</button>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal">
                                        <span class="glyphicon glyphicon-remove"></span> Cancelar</button>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>




        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_grid_chart.php");

?>
<script type="text/javascript">
    jQuery(function($) {

        $('#myModal').modal('toggle');

        $('#submitObservacion').on('click', function() {

            if ($("#TipoReporte").val() == '') {
                //SIMHTML::jsAlert("Error debe seleccionar el Estado e ingresar una observacin !");
                n = noty({
                    text: "<br><br>Error debe seleccionar el Tipo de Datos a consultar !<br><br>",
                    type: 'warning',
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: 'defaultTheme',
                    modal: true,
                    timeout: 1500,
                    closeWith: ['button'],
                    buttons: false,
                    animation: {
                        open: {
                            height: 'toggle'
                        },
                        close: {
                            height: 'toggle'
                        },
                        easing: 'swing',
                        speed: 500 // opening & closing animation speed
                    }

                });
                return false;
            }

            $.ajax({
                url: 'procedures/set_tipoReporte.php',
                method: "GET",
                data: 'tipoRep=' + $('#TipoReporte').val(),
                type: 'json',
                async: true,
                success: function(data) {
                    //var result = eval('('+data+')');
                    //  var result = eval("var json='"+data+"';");
                    //result.sucess
                    if (data) {

                        $('#myModal').modal('hide');
                        window.location.href = "reporteDiagnostico.php";
                    }
                }
            });
            return false;
        });

    });
</script>