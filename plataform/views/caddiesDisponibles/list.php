

<div class="widget-box transparent" id="recent-box">

    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CADDIES DISPONIBLES
        </h4>
    </div>

    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">

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
																		<li class="<?php if ($indice == 0) echo "active"; ?>">
		                                    <a  href="screen/pantallasorteo.php" target="_blank">
		                                        <i class="green ace-icon fa fa-pencil-square-o bigger-120"></i>
		                                        Ver Pantalla TV
		                                                                          </a>
		                                </li>
                                </ul>

                                <div class="tab-content">


                                    <?php
                                    $indice = 0;
                                    foreach ($frm["sorteo"] AS $index => $categoria) {
                                        ?>
                                        <div id="categoria<?php echo $index; ?>" class="tab-pane fade <?php if ($indice == 0) echo "in active"; ?> ">
                                            <div style="max-height: 800px;overflow: auto;">
                                                <table style="width: 100%;border: #000 1px solid;text-align: center;" class="tablaReporte">
                                                    <thead>
                                                        <tr style="background: #3fb0ac;height: 30px;color: #FFF;text-align: center;">
                                                            <th style="width: 20%;text-align: center;">Numero documento</th>
                                                            <th style="width: 20%;text-align: center;">Codigo</th>
                                                            <th style="text-align: center;">Nombre</th>
                                                            <th style="text-align: center;">Apellido</th>
                                                            <th style="text-align: center;">Estado</th>
                                                            <th style="text-align: center;">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        foreach ($categoria AS $index2 => $caddie) {
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $caddie["numeroDocumento"]; ?></td>
                                                                <td><?php echo $caddie["Codigo"]; ?></td>
                                                                <td><?php echo $caddie["nombre"]; ?></td>
                                                                <td>
                                                                    <?php echo $caddie["apellido"]; ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if ($caddie["estado"] == 1)
                                                                        echo 'Disponible';
                                                                    else if ($caddie["estado"] == 2)
                                                                        echo 'En campo';
                                                                    else if ($caddie["estado"] == 3)
                                                                        echo 'Inactivo';
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    if ($caddie["estado"] == 1) {
                                                                        echo '<a style="color:#000" href="javaScript:void(0)" sorteoCaddie="' . $caddie['IDSorteoCaddie'] . '" sorteoCaddieDetalle = "' . $caddie['IDSorteoCaddieDetalle'] . '" caddie="' . $caddie["IDCaddie"] . '" class="asignarSocio" title="Asignar socio"><i class="ace-icon fa fa-user bigger-250" ></i></a>';
                                                                    } else if ($caddie["estado"] == 2) {
                                                                        echo '<a style="color:#000" href="javaScript:void(0)" sorteoCaddie="' . $caddie['IDSorteoCaddie'] . '" sorteoCaddieDetalle = "' . $caddie['IDSorteoCaddieDetalle'] . '" caddie="' . $caddie["IDCaddie"] . '" codigoTalega="' . $caddie['codigoTalega'] . '" class="detallesAsignacion" title="Ver detalles asignación"><i class="ace-icon fa fa-eye bigger-130" ></i></a>&nbsp;&nbsp;';
                                                                        echo '<a style="color:#000" href="javaScript:void(0)" sorteoCaddie="' . $caddie['IDSorteoCaddie'] . '" sorteoCaddieDetalle = "' . $caddie['IDSorteoCaddieDetalle'] . '" caddie="' . $caddie["IDCaddie"] . '" codigoTalega="' . $caddie['codigoTalega'] . '" class="liberarCaddie" title="Liberar"><i class="ace-icon fa fa-exchange bigger-130" ></i></a>';
                                                                    }
                                                                    ?>

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
                                        $idSorteo = $caddie['IDSorteoCaddie'];
                                        $indice++;
                                    }
                                    ?>
                                    <input type="hidden" id="idSorteo" name="idSorteo" value="<?php echo $idSorteo; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PAGE CONTENT ENDS -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAsignarSocio" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">

                </div>
                <h5 class="modal-title" id="tituloAsignacion"></h5>
                <button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="frmAsignarSocio" name="frmAsignarSocio" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" method="POST">

                    <div class="row divAsignar">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="label-control">Codigo Talega</label>
                                <input type="text" id="codigoTalega" name="codigoTalega" class="form-control" />
                            </div>
                        </div>
                    </div>


                    <div class="row divAsignar">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-control">
                                 <input checked type="radio" id="tipo" name="tipo"  value="1" />
                            Socio
                        </label>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-control">
                                 <input type="radio" id="tipo" name="tipo" value="2"/>
                           Invitado
                        </label>
                            </div>
                        </div>
                    </div>

                    <div class="row divAsignar">
                        <div class="col-md-6 divSocio">
                            <div class="form-group">
                                <label class="label-control">Socio</label>
                                <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax" title="número de derecho"  value="" >
                                 <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio"  title="Socio">
                            </div>
                        </div>


                        <div class="col-md-6 divInvitado" style="display:none">
                            <div class="form-group">
                                <label class="label-control">Invitado</label>
                                <input type="text" id="Accion2" name="Accion2" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-invitadosIntegrados" title="Nombre invitado" value="" >
                                  <input type="hidden" name="IDInvitado" value="<?php echo $idInvitado; ?>" id="IDInvitado">
                                <input type="hidden" name="tipoInvitado" value="<?php echo $tipo; ?>" id="tipoInvitado">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-control">Talega</label>
                                <select id="talega" name="talega" class="form-control" >
                                    <option value="">-- Seleccione Opción --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="label-control">Observaciones</label>
                                <textarea id="observaciones" name="observaciones" placeholder="Observaciones" class="col-xs-12" ></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="propiedadesTalega" style="display: none;">
                        <hr>
                        <h4>Propiedades talega</h4>
                        <hr>
                        <?php
                        $indexFinal = count($frm["propiedadesTalega"]) - 1;
                        foreach ($frm["propiedadesTalega"] AS $index => $propiedad) {
                            ?>

                            <?php
                            if ($index == 0) {
                                ?>
                                <div  class="row">
                                    <?php
                                }
                                ?>

                                <div  class="col-md-6">
                                    <div  class="form-group">
                                        <label class="label-control"> <?= $propiedad["nombre"] ?></label>
                                        <input type="hidden" id="idPropiedad[]" name="idPropiedad[]" value="<?php echo $propiedad["IDPropiedadesTalega"]; ?>" >
                                        <input type="hidden" id="nombrePropiedad[]" name="nombrePropiedad[]" value="<?php echo $propiedad["nombre"]; ?>" >
                                        <input type="text" id="propiedad[]" name="propiedad[]" placeholder="Digite un valor para <?= $propiedad["nombre"] ?>" class="form-control" value="" >
                                    </div>
                                </div>

                                <?php
                                if ($index == $indexFinal || ($index + 1) % 2 == 0) {
                                    ?>
                                </div>
                                <?php
                            }
                            if ($index != $indexFinal && ($index + 1) % 2 == 0) {
                                echo '<div  class="row">';
                            }
                            ?>

                            <?php
                        }
                        ?>

                    </div>

                    <div class="row divLiberar">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="label-control">
                                    <!--<input checked type="checkbox" id="liberarCaddie" name="liberarCaddie" />-->
                                    Desea liberar el caddie para que este disponible y se pueda asociar a un nuevo socio?
                                </label>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="idCaddie" id="idCaddie" value="" />
                    <input type="hidden" name="idSorteoCaddieDetalle" id="idSorteoCaddieDetalle" value="" />
                    <input type="hidden" name="idSorteoCaddie" id="idSorteoCaddie" value="" />
                    <input type="hidden" name="regresa" id="regresa" value="" />
                    <input type="hidden" name="idTalega" id="idTalega" value="" />
                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                    <input type="hidden" id="FlagLiberarCaddie" name="FlagLiberarCaddie" value="N" />
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarAsociacion">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerAsignarSocio" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">

                </div>
                <h5 class="modal-title" id="exampleModalLabel">Asignar Socio</h5>
                <button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label-control"><b>Socio: </b></label>
                            <span id="socioVer"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label-control"><b>Talega: </b></label>
                            <span id="talegaVer"></span>
                        </div>
                    </div>
                </div>

                 <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label-control"><b>Fecha: </b></label>
                            <span id="fechaVer"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="label-control"><b>Observaciones: </b></label>
                            <span id="observacionesVer"></span>
                        </div>
                    </div>
                </div>

                <div id="propiedadesTalegaVer" style="display: none;">
                    <hr>
                    <h4>Propiedades talega</h4>
                    <hr>
                    <?php
                    $indexFinal = count($frm["propiedadesTalega"]) - 1;
                    foreach ($frm["propiedadesTalega"] AS $index => $propiedad) {
                        ?>

                        <?php
                        if ($index == 0) {
                            ?>
                            <div  class="row">
                                <?php
                            }
                            ?>

                            <div  class="col-md-6">
                                <div  class="form-group">
                                    <label class="label-control"><b> <?= $propiedad["nombre"] ?>: </b></label>
                                    <input type="hidden" id="idPropiedadVer[]" name="idPropiedadVer[]" value="<?php echo $propiedad["IDPropiedadesTalega"]; ?>" >
                                    <span id="propiedadVer[]" name="propiedadVer[]" ></span>
                                </div>
                            </div>

                            <?php
                            if ($index == $indexFinal || ($index + 1) % 2 == 0) {
                                ?>
                            </div>
                            <?php
                        }
                        if ($index != $indexFinal && ($index + 1) % 2 == 0) {
                            echo '<div  class="row">';
                        }
                        ?>

                        <?php
                    }
                    ?>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalAgregarCaddie" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">

                </div>
                <h5 class="modal-title" id="exampleModalLabel">Agregar caddie</h5>
                <button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="frmAgregarCaddie" name="frmAgregarCaddie" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" method="POST">


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-control"><b>Caddie: </b></label>
                                <select class="form-control" id="caddieAsignar" name="caddieAsignar">
                                    <option value="">-- Seleccione opción --</option>
                                    <?php
                                    foreach ($frm["caddies"] AS $caddie) {
                                        echo '<option idCategoria="' . $caddie["IDCategoriaCaddie"] . '" value="' . $caddie["IDCaddie"] . '">' . $caddie["caddie"] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="idSorteoCaddieAgregar" name="idSorteoCaddieAgregar" value="" />
                    <input type="hidden" id="idCategoriaCaddieAgregar" name="idCategoriaCaddieAgregar" value="" />
                    <input type="hidden" id="action" name="action" value="agregarCaddie" />

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarCaddieNuevo">Guardar</button>

            </div>
        </div>
    </div>
</div>


<?php
//include( "cmp/footer_grid.php" );
include( "cmp/footer_scripts.php" );
?>

<!-- inline scripts related to this page -->
<script type="text/javascript">

    $(".asignarSocio").on("click", function ()
    {
        var objeto = $(this);
        $("#frmAsignarSocio")[0].reset();
        var html = "<option value=''>-- Seleccione Opción --</option>"
        $("#talega").html(html);
        inicializarTalega();
        $("#idCaddie").val(objeto.attr("caddie"));
        $("#idSorteoCaddieDetalle").val(objeto.attr("sorteoCaddieDetalle"));
        $("#idSorteoCaddie").val(objeto.attr("sorteoCaddie"));
        $(".divAsignar").show();
        $(".divLiberar").hide();
        $("#FlagLiberarCaddie").val("N");
        $("#tituloAsignacion").html("Asignar socio");
        $("#modalAsignarSocio").modal("show");
    });


    $(".liberarCaddie").on("click", function ()
    {
        var objeto = $(this);
        $("#frmAsignarSocio")[0].reset();
        var html = "<option value=''>-- Seleccione Opción --</option>"
        $("#talega").html(html);
        inicializarTalega();
        $("#idCaddie").val(objeto.attr("caddie"));
        $("#idSorteoCaddieDetalle").val(objeto.attr("sorteoCaddieDetalle"));
        $("#idSorteoCaddie").val(objeto.attr("sorteoCaddie"));
        $(".divAsignar").hide();
        $(".divLiberar").show();
        $("#regresa").val(1);
        $("#FlagLiberarCaddie").val("S");
        if (objeto.attr("codigoTalega") != "")
        {
            getTalegaPropiedades(objeto.attr("codigoTalega"), 2);
        }
        $("#tituloAsignacion").html("Liberar caddie");
        $("#modalAsignarSocio").modal("show");
    });

    $(".detallesAsignacion").on("click", function ()
    {
        var objeto = $(this);
        if (objeto.attr("codigoTalega") != "")
        {
            getTalegaPropiedadesVer(objeto.attr("codigoTalega"), 2);
        }
        getInfoAsignacion(objeto.attr("caddie"));
        $("#modalVerAsignarSocio").modal("show");
    });


    $("#talega").on("change", function ()
    {
        getTalegaPropiedades($(this).val(), 1);
    });

    $("#codigoTalega").on("blur", function ()
    {
        $("#IDSocio").val("");
        var html = "<option value=''>-- Seleccione Opción --</option>";
        $("#talega").html(html);
        getTalegaPropiedades($(this).val(), 1);
    });

    function inicializarTalega()
    {
        $("#idTalega").val("");
        $("input[name='propiedad[]']").val("0");
        $("#propiedadesTalega").hide("slow");
    }

    function getTalega(idSocio, idInvitado, tipo)
    {

        inicializarTalega();
        if (idSocio > 0 || idInvitado > 0)
        {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                'async': true,
                url: "includes/async/acciones.async.php",
                data: {action: "getTalegasSocio", idSocio: idSocio, idInvitado : idInvitado, tipo : tipo },
                success: function (data) {
                    var html = "<option value=''>-- Seleccione Opción --</option>";
                    if (data.length > 0)
                    {
                        $.each(data, function (index, objeto)
                        {
                            html += "<option value='" + objeto.codigo + "'>" + objeto.nombre + "</option>"
                        });
                    }
                    $("#talega").html(html);
                }
            });

        } else
        {
            var html = "<option value=''>-- Seleccione Opción --</option>"
            $("#talega").html(html);
        }

    }

    function getTalegaPropiedades(codigoTalega, estadoTalega)
    {
        inicializarTalega();
        if (codigoTalega != "")
        {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                'async': true,
                url: "includes/async/acciones.async.php",
                data: {action: "getTalegaPropiedades", codigoTalega: codigoTalega, estadoTalega: estadoTalega},
                success: function (data) {
                    if (data.length > 0)
                    {
                        $("#propiedadesTalega").show("slow");
                        $("#idTalega").val(data[0]["IDTalega"]);
                        $.each(data, function (index, objeto)
                        {
                            var objetoIdPropiedad = $("input[name='idPropiedad[]'][value='" + objeto.idPropiedadesTalega + "']");
                            var index = $("input[name='idPropiedad[]']").index(objetoIdPropiedad);
                            var valor = 0;
                            if (objeto.valor > 0)
                                valor = objeto.valor;
                            $("input[name='propiedad[]']").eq(index).val(valor);
                        });

                    } else
                    {
                        alert("No se encontro una talega con este codigo");
                    }
                }
            });

        }

    }

    function getTalegaPropiedadesVer(codigoTalega, estadoTalega)
    {
        if (codigoTalega != "")
        {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                'async': true,
                url: "includes/async/acciones.async.php",
                data: {action: "getTalegaPropiedades", codigoTalega: codigoTalega, estadoTalega: estadoTalega},
                success: function (data) {
                    if (data.length > 0)
                    {
                        $("#propiedadesTalegaVer").show("slow");
                        $.each(data, function (index, objeto)
                        {
                            var objetoIdPropiedad = $("input[name='idPropiedadVer[]'][value='" + objeto.idPropiedadesTalega + "']");
                            var index = $("input[name='idPropiedadVer[]']").index(objetoIdPropiedad);
                            $("[name='propiedadVer[]']").eq(index).text(objeto.valor);
                        });

                    } else
                    {
                        alert("No se encontro una talega con este codigo");
                    }
                }
            });

        }

    }

    function getInfoAsignacion(idCaddie)
    {

        jQuery.ajax({
            type: "POST",
            dataType: "json",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            'async': true,
            url: "includes/async/acciones.async.php",
            data: {action: "CaddieHistoricoAsignacion", idCaddie: idCaddie},
            success: function (data) {
                if (data != null)
                {
                    $("#socioVer").html(data.socio);
                    $("#talegaVer").html(data.talega);
                    $("#fechaVer").html(data.fechaRegistro);
                    $("#observacionesVer").html(data.observaciones);
                }
            }
        });


    }


    $("#guardarAsociacion").on("click", function ()
    {
        var continua = 0;
        if ($("#propiedadesTalega").is(":visible"))
        {
            if ($("input[name='propiedad[]']").length > 0)
            {
                $.each($("input[name='propiedad[]']"), function (index, objeto)
                {
                    if ($(objeto).val() == "")
                        continua++;
                });
            }
            else continua++;

            if (continua > 0)
            {
                alert("para continuar las propiedades de la talega no pueden estar vacias");
            }
        } else
        {
            var tipopersona= $('input:radio[name=tipo]:checked').val();
            var FlagLiberarCaddie = $("#FlagLiberarCaddie").val();
            if ($("#IDSocio").val() == "" && tipopersona==1 && FlagLiberarCaddie!="S")
            {
                continua++;
                alert("Debe seleccionar un socio para continuar");
            }

            if ($("#IDInvitado").val() == "" && tipopersona==2)
            {
                continua++;
                alert("Debe seleccionar un invitado para continuar");
            }

            //Obligatorio talega
            /*
            if($("#IDSocio").val() != "" && $("#talega").length > 0 &&  $("#talega").val() == "")
            {
                continua++;
                alert("Debe seleccionar una talega para continuar");
            }
            */
        }

        if (continua == 0)
        {
            document.getElementById('frmAsignarSocio').submit();
        }


    });

    $(".btnAgregarCaddie").on("click", function ()
    {
        $("#idSorteoCaddieAgregar").val($("#idSorteo").val());
        $("#modalAgregarCaddie").modal("show");
    });

    $("#guardarCaddieNuevo").on("click", function ()
    {
        if ($("#caddieAsignar").val() != "")
        {
            $("#idCategoriaCaddieAgregar").val($("#caddieAsignar option:selected").attr("idCategoria"));
            setTimeout(function ()
            {
                document.getElementById('frmAgregarCaddie').submit();
            }, 300);
        } else
        {
            alert("Debe seleccionar un caddie para continuar");
        }

    });



    $("#Accion").on("keyup", function ()
    {
        $("#IDSocio").val("");
    });

    $("#Accion").on("blur", function ()
    {
        if ($("#IDSocio").val() == "")
            $("#Accion").val("");

            setTimeout(function()
            {
            if($("#IDSocio").val() > 0)
                        {
                             getTalega($("#IDSocio").val());
                        }

            }, 500);


    });


 $("#Accion2").on("keyup", function ()
    {
        $("#IDInvitado").val("");
        $("#tipoInvitado").val("");
    });

    $("#Accion2").on("blur", function ()
    {
        if ($("#IDInvitado").val() == "")
            $("#Accion2").val("");

            setTimeout(function()
            {
            if($("#IDInvitado").val() > 0)
                        {
                            getTalega("",$("#IDInvitado").val() ,$("#tipoInvitado").val() );
                        }

            }, 500);


    });

    $("[name='tipo']").on("click", function()
    {
        if($("#tipo:checked").val() == 1)
        {
            $(".divSocio").show("slow");
            $(".divInvitado").hide("slow");
        }
        else if($("#tipo:checked").val() == 2)
        {
            $(".divSocio").hide("slow");
            $(".divInvitado").show("slow");
        }


    });



</script>
