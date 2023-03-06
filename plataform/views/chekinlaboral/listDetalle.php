<?

$url_search = "";

$table = (SIMNet::req("type") == 'Socio') ? 'Socio' : 'Usuario';
$id = SIMNet::req("id");
$User = $dbo->fetchAll($table, "ID" . $table . "=" . $id, "array");
$nombre = $User['Nombre'] . ' ' . $User['Apellido'];

$url_search = "?type=" . $table . "&id=" . $id;

if (SIMNet::req("action") == "search") {
    $url_search .= "&oper=search_url&qryString=" . SIMNet::get("qryString");
} //end if
if (SIMNet::req("search") == "searchDate") {
    $url_search = "?oper=searchDate&id=" . $id . "&type=" . $table . "&inicio=" . SIMNet::get("inicio") . "&fin=" . SIMNet::get("fin");
} //end if
?>


<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'listadode', LANGSESSION); ?> <?php echo strtoupper($nombre) ?>
        </h4>


    </div>

    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">




                    <div id="jqGrid_container">
                        <form name="frmexportapqr" id="frmexportapqr" method="post" enctype="multipart/form-data" action="procedures/excel-chekinlaboral.php">
                            <table>
                                <tr>
                                    <td><input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="FechaInicio" value=""></td>
                                    <td><input type="text" id="FechaFinal" name="FechaFinal" placeholder="Fecha Final" class="col-xs-12 calendar" title="FechaFinal" value=""></td>
                                    <td>

                                        <select name="Estado" id="Estado">
                                            <option value="">Estado</option>
                                            <?php $Estados = SIMResources::$estado_checkin_laboral;
                                            foreach ($Estados as $key => $estado) {


                                            ?>
                                                <option value="<?php echo $key; ?>"><?php echo $estado; ?></option>

                                            <?php } ?>
                                        </select>

                                    </td>

                                    <td>
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                                        <input type="hidden" name="Id" id="Id" value="<?php echo $_GET["id"]; ?>">
                                        <!--<input type="hidden" name="IDPerfil" id="IDPerfil" value="<?php echo SIMUser::get("IDPerfil"); ?>">-->
                                        <input class="btn btn-info" type="submit" name="expbnr" id="expbnr" value="<?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?>">
                                        <input class="btn btn-purple" type="button" name="searchCheckin" id="searchCheckin" rel="<?= $script; ?>.php?action=detalle&search=searchDate&id=<?= $id ?>&type=<?= $table ?>" value="<?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?>">
                                        <input class="btn btn-success" type="button" name="aprobarCheckin" id="aprobarCheckin" club="<?= SIMUser::get('club') ?>" table="<?= $table ?>" data-id="<?= $id ?>" value="<?= SIMUtil::get_traduccion('', '', 'AprobarTodo', LANGSESSION); ?>">


                                    </td>
                                <tr>
                            </table>
                        </form>
                        <br>

                        <!--  solo para perfiles administradores, ver los registros que se eliminaron -->
                        <?php if (SIMUser::get("IDPerfil") <= 1 || SIMUser::get("IDPerfil") == 62) { ?>
                            <form name="frmexportapqr" id="frmexportapqr" method="post" enctype="multipart/form-data" action="procedures/excel-chekinlaboral-eliminados.php">
                                <table>
                                    <tr>
                                        <td><input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="FechaInicio" value=""></td>
                                        <td><input type="text" id="FechaFinal" name="FechaFinal" placeholder=" Fecha Final" class="col-xs-12 calendar" title="FechaFinal" value=""></td>
                                        <td>

                                            <select name="Estado" id="Estado">
                                                <option value="">Estado</option>
                                                <?php $Estados = SIMResources::$estado_checkin_laboral;
                                                foreach ($Estados as $key => $estado) {


                                                ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $estado; ?></option>

                                                <?php } ?>
                                            </select>

                                        </td>


                                        <td>
                                            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                                            <input type="hidden" name="Id" id="Id" value="<?php echo $_GET["id"]; ?>">
                                            <input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo SIMUser::get("IDSocio"); ?>">
                                            <input type="hidden" name="IDUsuario" id="IDUsuario" value="<?php echo SIMUser::get("IDUsuario"); ?>">
                                            <input type="hidden" name="IDPerfil" id="IDPerfil" value="<?php echo SIMUser::get("IDPerfil"); ?>">
                                            <input class="btn btn-info" type="submit" name="expbnr" id="expbnr" value="<?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?> Registros Eliminados">


                                        </td>
                                    <tr>
                                </table>
                            </form>

                        <?php } ?>

                        <table id="grid-table"></table>
                    </div>


                    <br><br>

                    <div class="page-header">
                        <h1>
                            <a href="#" id="verregistrosmesactual">Ver todos los registros del mes actual </a>

                        </h1>
                    </div><!-- /.page-header -->
                    <div id="divicheckinlistado" style="display:none">
                        <div class="widget-body">
                            <div class="widget-main padding-4">
                                <div class="row">
                                    <div class="col-xs-12">

                                        <table id="grid-tableingresado"></table>

                                        <div id="grid-pageringresado"></div>

                                        <script type="text/javascript">
                                            var $path_base = "."; //in Ace demo this will be used for editurl parameter
                                        </script>

                                        <!-- PAGE CONTENT ENDS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="grid-pager"></div>

                    <script type="text/javascript">
                        var $path_base = "."; //in Ace demo this will be used for editurl parameter
                    </script>

                    <!-- PAGE CONTENT ENDS -->
                </div>
            </div>
        </div>
    </div>
</div>




<?
include("cmp/footer_grid.php");
?>

<!-- inline scripts related to this page -->
<script type="text/javascript">
    jQuery(function($) {

        $("#verregistrosmesactual").click(function() {
            $("#divicheckinlistado").toggle();
            return false
        });

        $('#aprobarCheckin').click(function() {
            var club = $(this).attr('club');
            var table = $(this).attr('table');
            var id = $(this).attr('data-id');
            $.post('includes/async/aprobarHorasExtra.async.php', {
                'club': club,
                'table': table,
                'id': id
            }, function(response) {
                if (response == 1) {
                    alert('Horas extras aprobadas')
                    window.location.reload();
                } else {
                    alert('Error al ejecutar acción');
                }
            });
        });

        /*  $('#borrarCheckin').click(function() {
             var club = $(this).attr('club');
             var table = $(this).attr('table');
             var id = $(this).attr('data-id');
             var fechainicio = $(FechaInicio).val();
             var fechafinal = $(FechaFinal).val();
             if (confirm("Esta seguro que desea borrar todos los registros en las fechas seleccionadas?")) {

                 $.post('includes/async/eliminarHorasExtra.async.php', {
                     'club': club,
                     'table': table,
                     'id': id,
                     'fechainicio': fechainicio,
                     'fechafinal': fechafinal
                 }, function(response) {
                     if (response == 1) {
                         alert('Registro eliminado con exito.')
                         window.location.reload();
                     } else if (response == 0) {
                         alert('Debe de ingresar fecha inicio y fecha final');
                     } else if (response == 2) {
                         alert('Error al ejecutar');
                     }
                 });
             }
         }); */


        var grid_selector = "#grid-table";
        var pager_selector = "#grid-pager";

        //resize to fit page size
        $(window).on('resize.jqGrid', function() {
            $(grid_selector).jqGrid('setGridWidth', $(".page-content").width());
        })
        //resize on sidebar collapse/expand
        var parent_column = $(grid_selector).closest('[class*="col-"]');
        $(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
            if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
                //setTimeout is for webkit only to give time for DOM changes and then redraw!!!
                setTimeout(function() {
                    $(grid_selector).jqGrid('setGridWidth', parent_column.width());
                }, 0);
            }
        })





        jQuery(grid_selector).jqGrid({





            url: 'includes/async/<?php echo $script; ?>Detalle.async.php<?= $url_search ?>',
            datatype: "json",
            colNames: ['<?= SIMUtil::get_traduccion('', '', 'Horainiciolaboral', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Horafinallaboral', LANGSESSION); ?>',
                '<?= SIMUtil::get_traduccion('', '', 'Fechaentrada', LANGSESSION); ?>', 'Fecha Salida',
                '<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Tiempoextraentrada', LANGSESSION); ?>',
                '<?= SIMUtil::get_traduccion('', '', 'Tipohoraentrada', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Tiempoextrasalida', LANGSESSION); ?>',
                '<?= SIMUtil::get_traduccion('', '', 'Tipohorasalida', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?>', 'Observacion Entrada', 'Observacion Salida', '<?= SIMUtil::get_traduccion('', '', 'Comentario', LANGSESSION); ?>', 'Registro',
                '<?= SIMUtil::get_traduccion('', '', 'Guardar', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?>'
            ],
            colModel: [{
                    name: 'HoraInicioLaboral',
                    index: 'HoraInicioLaboral',
                    align: "left",
                    width: "60"
                },
                {
                    name: 'HoraFinalLaboral',
                    index: 'HoraFinalLaboral',
                    align: "left",
                    width: "60"
                },
                {
                    name: 'FechaMovimientoEntrada',
                    index: 'FechaMovimientoEntrada',
                    align: "left",
                    width: "130"

                },
                {
                    name: 'FechaMovimientoSalida',
                    index: 'FechaMovimientoSalida',
                    align: "left",
                    width: "130"

                },
                {
                    name: 'Dia',
                    index: 'Dia',
                    align: "left",
                    width: "60"

                },
                {
                    name: 'TiempoExtraEntrada',
                    index: 'TiempoExtraEntrada',
                    align: "left",
                    width: "60"

                },
                {
                    name: 'TipoTiempoEntrada',
                    index: 'TipoTiempoEntrada',
                    align: "left",
                    width: "100"

                },
                {
                    name: 'TiempoExtraSalida',
                    index: 'TiempoExtraSalida',
                    align: "left",
                    width: "60"

                },
                {
                    name: 'TipoTiempoSalida',
                    index: 'TipoTiempoSalida',
                    align: "left",
                    width: "100"

                },
                {
                    name: 'Estado',
                    index: 'Estado',
                    align: "left",
                    width: "90"

                },
                {
                    name: 'ObservacionEntrada',
                    index: 'ObservacionEntrada',
                    align: "left",
                    width: "90"

                },
                {
                    name: 'ObservacionSalida',
                    index: 'ObservacionSalida',
                    align: "left",
                    width: "90"

                },
                {
                    name: 'ComentarioRevision',
                    index: 'ComentarioRevision',
                    align: "center",
                    width: "110"

                },
                {
                    name: 'Registro',
                    index: 'Registro',
                    align: "center",
                    width: "110"

                },

                {
                    name: 'Guardar',
                    index: '',
                    align: "center",
                    width: "50"

                },
                {
                    name: 'Editar',
                    index: '',
                    align: "center",
                    width: "50"
                },
                {
                    name: 'Eliminar',
                    index: '',
                    align: "center",
                    width: "50"

                },



            ],
            rowNum: 100,
            rowList: [20, 40, 100],
            sortname: 'FechaTrCr',
            viewrecords: true,
            sortorder: "DESC",
            caption: "<?php echo SIMReg::get("title"); ?>",
            height: "100%",
            width: 855,
            multiselect: true,
            editurl: "includes/async/<?php echo $script; ?>Detalle.async.php",
            pager: pager_selector,
            altRows: true,
            //toppager: true,

            multiselect: false,
            //multikey: "ctrlKey",
            multiboxonly: true,

            loadComplete: function() {
                var table = this;
                setTimeout(function() {
                    styleCheckbox(table);

                    updateActionIcons(table);
                    updatePagerIcons(table);
                    enableTooltips(table);
                }, 0);

                preparaform();
            },

            // onSelectRow: function(id) {
            //     location.href = "<?php echo $script ?>.php?action=edit&id=" + id;
            //     return false;
            // },

        });

        var datePick = function(elem) {
            jQuery(elem).datepicker();
        }


        $(window).triggerHandler('resize.jqGrid'); //trigger window resize to make the grid get the correct size



        //enable search/filter toolbar
        jQuery(grid_selector).jqGrid('filterToolbar', {
            defaultSearch: true,
            stringResult: true
        })
        jQuery(grid_selector).filterToolbar({});


        //switch element when editing inline
        function aceSwitch(cellvalue, options, cell) {
            setTimeout(function() {
                $(cell).find('input[type=checkbox]')
                    .addClass('ace ace-switch ace-switch-5')
                    .after('<span class="lbl"></span>');
            }, 0);
        }
        //enable datepicker
        function pickDate(cellvalue, options, cell) {
            setTimeout(function() {
                $(cell).find('input[type=text]')
                    .datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true
                    });
            }, 0);
        }


        //navButtons
        jQuery(grid_selector).jqGrid('navGrid', pager_selector, { //navbar options
            edit: false,
            editicon: 'ace-icon fa fa-pencil blue',
            add: false,
            addicon: 'ace-icon fa fa-plus-circle purple',
            del: false,
            delicon: 'ace-icon fa fa-trash-o red',
            search: false,
            searchicon: 'ace-icon fa fa-search orange',
            refresh: true,
            refreshicon: 'ace-icon fa fa-refresh green',
            view: true,
            viewicon: 'ace-icon fa fa-search-plus grey',
        }, {
            //edit record form
            //closeAfterEdit: true,
            //width: 700,
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        }, {
            //new record form
            //width: 700,
            closeAfterAdd: true,
            recreateForm: true,
            viewPagerButtons: false,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
                    .wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        }, {
            //delete record form
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                if (form.data('styled')) return false;

                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_delete_form(form);

                form.data('styled', true);
            },
            onClick: function(e) {
                //alert(1);
            }
        }, {
            //search form
            recreateForm: true,
            afterShowSearch: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
                style_search_form(form);

            },
            afterRedraw: function() {
                style_search_filters($(this));
            },
            multipleSearch: true,
            /**
            multipleGroup:true,
            showQuery: true
            */
        }, {
            //view record form
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
            }
        }).navButtonAdd(pager_selector, {
            caption: "Ver Extras Despues Del Turno",
            buttonicon: 'ace-icon fa fa-eye',
            onClickButton: function() {

                location.href = "<?php echo $script; ?>.php?action=extras&id=<?php echo $_GET["id"]; ?>&type=<?php echo $_GET["type"]; ?>";
                //alert("Seras redirigido a: <?php echo $script; ?>.php?action=historial&id");

            },
            position: "last"
        });


        //REGISTROS DEL MES ACTUAL


        var grid_selectoringresado = "#grid-tableingresado";
        var pager_selector = "#grid-pageringresado";

        //resize to fit page size
        $(window).on('resize.jqGrid', function() {
            $(grid_selectoringresado).jqGrid('setGridWidth', $(".page-content").width());
        })
        //resize on sidebar collapse/expand
        var parent_column = $(grid_selectoringresado).closest('[class*="col-"]');
        $(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
            if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
                //setTimeout is for webkit only to give time for DOM changes and then redraw!!!
                setTimeout(function() {
                    $(grid_selectoringresado).jqGrid('setGridWidth', parent_column.width());
                }, 0);
            }
        })



        jQuery(grid_selectoringresado).jqGrid({



            <?php
            $Usuario = $dbo->fetchAll('Usuario', 'IDUsuario = ' . SIMUser::get('IDUsuario'), 'array'); ?>

            url: 'includes/async/chekinlaboralListadoMesActual.async.php<?= $url_search ?>',
            datatype: "json",
            colNames: ['<?= SIMUtil::get_traduccion('', '', 'Horainiciolaboral', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Horafinallaboral', LANGSESSION); ?>',
                '<?= SIMUtil::get_traduccion('', '', 'Fechaentrada', LANGSESSION); ?>', 'Fecha Salida',
                '<?= SIMUtil::get_traduccion('', '', 'día', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Tiempoextraentrada', LANGSESSION); ?>',
                '<?= SIMUtil::get_traduccion('', '', 'Tipohoraentrada', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Tiempoextrasalida', LANGSESSION); ?>',
                '<?= SIMUtil::get_traduccion('', '', 'Tipohorasalida', LANGSESSION); ?>', '<?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?>', 'Observacion Entrada', 'Observacion Salida', 'Registro',
                <?php if ($Usuario['IDPerfil'] == 1 || $Usuario['IDPerfil'] == 0) echo "'" .  SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION)  . "'"; ?>

            ],
            colModel: [{
                    name: 'HoraInicioLaboral',
                    index: 'HoraInicioLaboral',
                    align: "left",
                    width: "60"
                },
                {
                    name: 'HoraFinalLaboral',
                    index: 'HoraFinalLaboral',
                    align: "left",
                    width: "60"
                },
                {
                    name: 'FechaMovimientoEntrada',
                    index: 'FechaMovimientoEntrada',
                    align: "left",
                    width: "130"

                },
                {
                    name: 'FechaMovimientoSalida',
                    index: 'FechaMovimientoSalida',
                    align: "left",
                    width: "130"

                },
                {
                    name: 'Dia',
                    index: 'Dia',
                    align: "left",
                    width: "60"

                },
                {
                    name: 'TiempoExtraEntrada',
                    index: 'TiempoExtraEntrada',
                    align: "left",
                    width: "60"

                },
                {
                    name: 'TipoTiempoEntrada',
                    index: 'TipoTiempoEntrada',
                    align: "left",
                    width: "100"

                },
                {
                    name: 'TiempoExtraSalida',
                    index: 'TiempoExtraSalida',
                    align: "left",
                    width: "60"

                },
                {
                    name: 'TipoTiempoSalida',
                    index: 'TipoTiempoSalida',
                    align: "left",
                    width: "100"

                },
                {
                    name: 'Estado',
                    index: 'Estado',
                    align: "left",
                    width: "90"

                },
                {
                    name: 'ObservacionEntrada',
                    index: 'ObservacionEntrada',
                    align: "left",
                    width: "90"

                },
                {
                    name: 'ObservacionSalida',
                    index: 'ObservacionSalida',
                    align: "left",
                    width: "90"

                },

                {
                    name: 'Registro',
                    index: 'Registro',
                    align: "center",
                    width: "110"

                },

                <?php


                if ($Usuario['IDPerfil'] == 1 || $Usuario['IDPerfil'] == 0) { ?> {
                        name: 'Eliminar',
                        index: '',
                        align: "center",
                        search: false
                    },
                <? } ?>





            ],
            rowNum: 100,
            rowList: [20, 40, 100],
            sortname: 'FechaTrCr',
            viewrecords: true,
            sortorder: "ASC",
            caption: "Registros",
            height: "100%",
            width: 855,
            multiselect: true,
            editurl: "includes/chekinlaboralListadoMesActual.async.php",

            pager: pager_selector,
            altRows: true,
            //toppager: true,

            multiselect: false,
            //multikey: "ctrlKey",
            multiboxonly: true,

            loadComplete: function() {
                var table = this;
                setTimeout(function() {
                    styleCheckbox(table);

                    updateActionIcons(table);
                    updatePagerIcons(table);
                    enableTooltips(table);
                }, 0);

                preparaform();
            },
            /*     onCellSelect: function(rowid, icol, cellcontent, e) {


                    $("#detalle" + rowid).click();
                    //location.href="invitados.php?action=edit&id="+rowid;
                    //$('.fancybox').click();
                    //$.fancybox.open
                    $.fancybox.open([{
                        type: 'iframe',
                        href: 'invitados.php?action=editobservacion&id=' + rowid,
                        afterClose: function() { // USE THIS IT IS YOUR ANSWER THE KEY WORD IS "afterClose"
                            $("#grid-table0").trigger("reloadGrid");
                            $("#grid-table1").trigger("reloadGrid");
                            $("#grid-table2").trigger("reloadGrid");
                            $("#grid-table3").trigger("reloadGrid");
                            $("#grid-table").trigger("reloadGrid");
                        }

                    }], {
                        padding: 0
                    });
                    return false;



                }, */







        });

        var datePick = function(elem) {
            jQuery(elem).datepicker();
        }


        $(window).triggerHandler('resize.jqGrid'); //trigger window resize to make the grid get the correct size



        //enable search/filter toolbar
        jQuery(grid_selectoringresado).jqGrid('filterToolbar', {
            defaultSearch: true,
            stringResult: true
        })
        jQuery(grid_selectoringresado).filterToolbar({});


        //switch element when editing inline
        function aceSwitch(cellvalue, options, cell) {
            setTimeout(function() {
                $(cell).find('input[type=checkbox]')
                    .addClass('ace ace-switch ace-switch-5')
                    .after('<span class="lbl"></span>');
            }, 0);
        }
        //enable datepicker
        function pickDate(cellvalue, options, cell) {
            setTimeout(function() {
                $(cell).find('input[type=text]')
                    .datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true
                    });
            }, 0);
        }


        //navButtons
        jQuery(grid_selectoringresado).jqGrid('navGrid', pager_selector, { //navbar options

            edit: false,
            editicon: 'ace-icon fa fa-pencil blue',
            add: false,
            addicon: 'ace-icon fa fa-plus-circle purple',
            del: false,
            delicon: 'ace-icon fa fa-trash-o red',
            search: true,
            searchicon: 'ace-icon fa fa-search orange',
            refresh: true,
            refreshicon: 'ace-icon fa fa-refresh green',
            view: true,
            viewicon: 'ace-icon fa fa-search-plus grey',
        }, {
            //edit record form
            //closeAfterEdit: true,
            //width: 700,
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        }, {
            //new record form
            //width: 700,
            closeAfterAdd: true,
            recreateForm: true,
            viewPagerButtons: false,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
                    .wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        }, {
            //delete record form
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                if (form.data('styled')) return false;

                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_delete_form(form);

                form.data('styled', true);
            },
            onClick: function(e) {
                //alert(1);
            }
        }, {
            //search form
            recreateForm: true,
            afterShowSearch: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
                style_search_form(form);

            },
            afterRedraw: function() {
                style_search_filters($(this));
            },
            multipleSearch: true,
            /**
            multipleGroup:true,
            showQuery: true
            */
        }, {
            //view record form
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
            }
        })

        //FIN REGISTROS DEL MES ACTUAL

        function style_edit_form(form) {
            //enable datepicker on "sdate" field and switches for "stock" field
            form.find('input[name=sdate]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            })

            form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');
            //don't wrap inside a label element, the checkbox value won't be submitted (POST'ed)
            //.addClass('ace ace-switch ace-switch-5').wrap('<label class="inline" />').after('<span class="lbl"></span>');


            //update buttons classes
            var buttons = form.next().find('.EditButton .fm-button');
            buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide(); //ui-icon, s-icon
            buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
            buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')

            buttons = form.next().find('.navButton a');
            buttons.find('.ui-icon').hide();
            buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
            buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');
        }

        function style_delete_form(form) {
            var buttons = form.next().find('.EditButton .fm-button');
            buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide(); //ui-icon, s-icon
            buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
            buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
        }

        function style_search_filters(form) {
            form.find('.delete-rule').val('X');
            form.find('.add-rule').addClass('btn btn-xs btn-primary');
            form.find('.add-group').addClass('btn btn-xs btn-success');
            form.find('.delete-group').addClass('btn btn-xs btn-danger');

        }

        function style_search_form(form) {
            var dialog = form.closest('.ui-jqdialog');
            var buttons = dialog.find('.EditTable')
            buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
            buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
            buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
        }

        function beforeDeleteCallback(e) {
            var form = $(e[0]);
            if (form.data('styled')) return false;

            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
            style_delete_form(form);

            form.data('styled', true);
        }

        function beforeEditCallback(e) {
            var form = $(e[0]);
            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
            style_edit_form(form);
        }



        //it causes some flicker when reloading or navigating grid
        //it may be possible to have some custom formatter to do this as the grid is being created to prevent this
        //or go back to default browser checkbox styles for the grid
        function styleCheckbox(table) {
            /**
					$(table).find('input:checkbox').addClass('ace')
					.wrap('<label />')
					.after('<span class="lbl align-top" />')
			
			
					$('.ui-jqgrid-labels th[id*="_cb"]:first-child')
					.find('input.cbox[type=checkbox]').addClass('ace')
					.wrap('<label />').after('<span class="lbl align-top" />');
				*/
        }


        //unlike navButtons icons, action icons in rows seem to be hard-coded
        //you can change them like this in here if you want
        function updateActionIcons(table) {
            /**
            var replacement = 
            {
            	'ui-ace-icon fa fa-pencil' : 'ace-icon fa fa-pencil blue',
            	'ui-ace-icon fa fa-trash-o' : 'ace-icon fa fa-trash-o red',
            	'ui-icon-disk' : 'ace-icon fa fa-check green',
            	'ui-icon-cancel' : 'ace-icon fa fa-times red'
            };
            $(table).find('.ui-pg-div span.ui-icon').each(function(){
            	var icon = $(this);
            	var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
            	if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
            })
            */
        }

        //replace icons with FontAwesome icons like above
        function updatePagerIcons(table) {
            var replacement = {
                'ui-icon-seek-first': 'ace-icon fa fa-angle-double-left bigger-140',
                'ui-icon-seek-prev': 'ace-icon fa fa-angle-left bigger-140',
                'ui-icon-seek-next': 'ace-icon fa fa-angle-right bigger-140',
                'ui-icon-seek-end': 'ace-icon fa fa-angle-double-right bigger-140'
            };
            $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function() {
                var icon = $(this);
                var $class = $.trim(icon.attr('class').replace('ui-icon', ''));

                if ($class in replacement) icon.attr('class', 'ui-icon ' + replacement[$class]);
            })
        }

        function enableTooltips(table) {
            $('.navtable .ui-pg-button').tooltip({
                container: 'body'
            });
            $(table).find('.ui-pg-div').tooltip({
                container: 'body'
            });
        }

        //var selr = jQuery(grid_selector).jqGrid('getGridParam','selrow');

        $(document).one('ajaxloadstart.page', function(e) {
            $(grid_selector).jqGrid('GridUnload');
            $('.ui-jqdialog').remove();
        });


        //INGRESADOS


        var grid_selectoringresado = "#grid-tableingresado";
        var pager_selector = "#grid-pageringresado";

        //resize to fit page size
        $(window).on('resize.jqGrid', function() {
            $(grid_selectoringresado).jqGrid('setGridWidth', $(".page-content").width());
        })
        //resize on sidebar collapse/expand
        var parent_column = $(grid_selectoringresado).closest('[class*="col-"]');
        $(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
            if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
                //setTimeout is for webkit only to give time for DOM changes and then redraw!!!
                setTimeout(function() {
                    $(grid_selectoringresado).jqGrid('setGridWidth', parent_column.width());
                }, 0);
            }
        })



        jQuery(grid_selectoringresado).jqGrid({





            url: 'includes/async/invitadosingreso.async.php<?= $url_search ?>',
            datatype: "json",
            colNames: ['Estado', 'Socio', 'Numero de derecho', 'Documento Invitado', 'Nombre Invitado', 'Fecha de Ingreso', 'Obs'],
            colModel: [{
                    name: 'Estado',
                    index: 'Estado',
                    align: "center"
                },
                {
                    name: 'Socio',
                    index: 'Socio',
                    align: "left"
                },
                {
                    name: 'Accion',
                    index: 'Socio',
                    align: "left"
                },
                {
                    name: 'NumeroDocumento',
                    index: 'NumeroDocumento',
                    align: "left"
                },
                {
                    name: 'Nombre',
                    index: 'Nombre',
                    align: "center"
                },

                {
                    name: 'FechaIngreso',
                    index: 'FechaIngreso',
                    align: "center",
                    autosearch: true,
                    searchoptions: {
                        // dataInit is the client-side event that fires upon initializing the toolbar search field for a column
                        // use it to place a third party control to customize the toolbar
                        dataInit: function(element) {
                            $(element).datepicker({
                                    format: 'yyyy-mm-dd',
                                    //minDate: new Date(2010, 0, 1),


                                })
                                .on("changeDate", function(e) {
                                    $(grid_selectoringresado).trigger('triggerToolbar');
                                });
                        },
                    }

                },
                {
                    name: 'Obs',
                    index: 'Obs',
                    align: "left"
                },


            ],
            rowNum: 100,
            rowList: [20, 40, 100],
            sortname: 'FechaIngreso',
            viewrecords: true,
            sortorder: "ASC",
            caption: "Invitados",
            height: "100%",
            width: 855,
            multiselect: true,
            editurl: "includes/invitados.async.php",





            pager: pager_selector,
            altRows: true,
            //toppager: true,

            multiselect: true,
            //multikey: "ctrlKey",
            multiboxonly: true,

            loadComplete: function() {
                var table = this;
                setTimeout(function() {
                    styleCheckbox(table);

                    updateActionIcons(table);
                    updatePagerIcons(table);
                    enableTooltips(table);
                }, 0);

                preparaform();
            },
            onCellSelect: function(rowid, icol, cellcontent, e) {


                $("#detalle" + rowid).click();
                //location.href="invitados.php?action=edit&id="+rowid;
                //$('.fancybox').click();
                //$.fancybox.open
                $.fancybox.open([{
                    type: 'iframe',
                    href: 'invitados.php?action=editobservacion&id=' + rowid,
                    afterClose: function() { // USE THIS IT IS YOUR ANSWER THE KEY WORD IS "afterClose"
                        $("#grid-table0").trigger("reloadGrid");
                        $("#grid-table1").trigger("reloadGrid");
                        $("#grid-table2").trigger("reloadGrid");
                        $("#grid-table3").trigger("reloadGrid");
                        $("#grid-table").trigger("reloadGrid");
                    }

                }], {
                    padding: 0
                });
                return false;



            },







        });

        var datePick = function(elem) {
            jQuery(elem).datepicker();
        }


        $(window).triggerHandler('resize.jqGrid'); //trigger window resize to make the grid get the correct size



        //enable search/filter toolbar
        jQuery(grid_selectoringresado).jqGrid('filterToolbar', {
            defaultSearch: true,
            stringResult: true
        })
        jQuery(grid_selectoringresado).filterToolbar({});


        //switch element when editing inline
        function aceSwitch(cellvalue, options, cell) {
            setTimeout(function() {
                $(cell).find('input[type=checkbox]')
                    .addClass('ace ace-switch ace-switch-5')
                    .after('<span class="lbl"></span>');
            }, 0);
        }
        //enable datepicker
        function pickDate(cellvalue, options, cell) {
            setTimeout(function() {
                $(cell).find('input[type=text]')
                    .datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true
                    });
            }, 0);
        }


        //navButtons
        jQuery(grid_selectoringresado).jqGrid('navGrid', pager_selector, { //navbar options

            edit: false,
            editicon: 'ace-icon fa fa-pencil blue',
            add: false,
            addicon: 'ace-icon fa fa-plus-circle purple',
            del: false,
            delicon: 'ace-icon fa fa-trash-o red',
            search: true,
            searchicon: 'ace-icon fa fa-search orange',
            refresh: true,
            refreshicon: 'ace-icon fa fa-refresh green',
            view: true,
            viewicon: 'ace-icon fa fa-search-plus grey',
        }, {
            //edit record form
            //closeAfterEdit: true,
            //width: 700,
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        }, {
            //new record form
            //width: 700,
            closeAfterAdd: true,
            recreateForm: true,
            viewPagerButtons: false,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
                    .wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        }, {
            //delete record form
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                if (form.data('styled')) return false;

                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_delete_form(form);

                form.data('styled', true);
            },
            onClick: function(e) {
                //alert(1);
            }
        }, {
            //search form
            recreateForm: true,
            afterShowSearch: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
                style_search_form(form);

            },
            afterRedraw: function() {
                style_search_filters($(this));
            },
            multipleSearch: true,
            /**
            multipleGroup:true,
            showQuery: true
            */
        }, {
            //view record form
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
            }
        })



        function style_edit_form(form) {
            //enable datepicker on "sdate" field and switches for "stock" field
            form.find('input[name=sdate]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            })

            form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');
            //don't wrap inside a label element, the checkbox value won't be submitted (POST'ed)
            //.addClass('ace ace-switch ace-switch-5').wrap('<label class="inline" />').after('<span class="lbl"></span>');


            //update buttons classes
            var buttons = form.next().find('.EditButton .fm-button');
            buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide(); //ui-icon, s-icon
            buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
            buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')

            buttons = form.next().find('.navButton a');
            buttons.find('.ui-icon').hide();
            buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
            buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');
        }

        function style_delete_form(form) {
            var buttons = form.next().find('.EditButton .fm-button');
            buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide(); //ui-icon, s-icon
            buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
            buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
        }

        function style_search_filters(form) {
            form.find('.delete-rule').val('X');
            form.find('.add-rule').addClass('btn btn-xs btn-primary');
            form.find('.add-group').addClass('btn btn-xs btn-success');
            form.find('.delete-group').addClass('btn btn-xs btn-danger');

        }

        function style_search_form(form) {
            var dialog = form.closest('.ui-jqdialog');
            var buttons = dialog.find('.EditTable')
            buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
            buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
            buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
        }

        function beforeDeleteCallback(e) {
            var form = $(e[0]);
            if (form.data('styled')) return false;

            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
            style_delete_form(form);

            form.data('styled', true);
        }

        function beforeEditCallback(e) {
            var form = $(e[0]);
            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
            style_edit_form(form);
        }



        //it causes some flicker when reloading or navigating grid
        //it may be possible to have some custom formatter to do this as the grid is being created to prevent this
        //or go back to default browser checkbox styles for the grid
        function styleCheckbox(table) {
            /**
            	$(table).find('input:checkbox').addClass('ace')
            	.wrap('<label />')
            	.after('<span class="lbl align-top" />')


            	$('.ui-jqgrid-labels th[id*="_cb"]:first-child')
            	.find('input.cbox[type=checkbox]').addClass('ace')
            	.wrap('<label />').after('<span class="lbl align-top" />');
            */
        }


        //unlike navButtons icons, action icons in rows seem to be hard-coded
        //you can change them like this in here if you want
        function updateActionIcons(table) {
            /**
            var replacement =
            {
            	'ui-ace-icon fa fa-pencil' : 'ace-icon fa fa-pencil blue',
            	'ui-ace-icon fa fa-trash-o' : 'ace-icon fa fa-trash-o red',
            	'ui-icon-disk' : 'ace-icon fa fa-check green',
            	'ui-icon-cancel' : 'ace-icon fa fa-times red'
            };
            $(table).find('.ui-pg-div span.ui-icon').each(function(){
            	var icon = $(this);
            	var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
            	if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
            })
            */
        }

        //replace icons with FontAwesome icons like above
        function updatePagerIcons(table) {
            var replacement = {
                'ui-icon-seek-first': 'ace-icon fa fa-angle-double-left bigger-140',
                'ui-icon-seek-prev': 'ace-icon fa fa-angle-left bigger-140',
                'ui-icon-seek-next': 'ace-icon fa fa-angle-right bigger-140',
                'ui-icon-seek-end': 'ace-icon fa fa-angle-double-right bigger-140'
            };
            $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function() {
                var icon = $(this);
                var $class = $.trim(icon.attr('class').replace('ui-icon', ''));

                if ($class in replacement) icon.attr('class', 'ui-icon ' + replacement[$class]);
            })
        }

        function enableTooltips(table) {
            $('.navtable .ui-pg-button').tooltip({
                container: 'body'
            });
            $(table).find('.ui-pg-div').tooltip({
                container: 'body'
            });
        }

        //var selr = jQuery(grid_selector).jqGrid('getGridParam','selrow');

        $(document).one('ajaxloadstart.page', function(e) {
            $(grid_selector).jqGrid('GridUnload');
            $('.ui-jqdialog').remove();
        });
    });

    $(window).bind('resize', function() {
        var width = $('#jqGrid_container').width();
        $('#jqList').setGridWidth(width);
    });
</script>