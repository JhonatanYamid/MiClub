<?
    $url_search = "";

    $id = SIMNet::req("id");
    $User = $dbo->fetchAll("Usuario", "IDUsuario = $id", "array");
    $nombre = $User['Nombre'] . ' ' . $User['Apellido'];

    $url_search = "?id=".$id;

    if (SIMNet::req("action") == "search") {
        $url_search .= "&oper=search_url&qryString=" . SIMNet::get("qryString");
    } //end if
    if (SIMNet::req("search") == "searchDate") {
        $url_search = "?oper=searchDate&id=" . $id . "&inicio=" . SIMNet::get("inicio") . "&fin=" . SIMNet::get("fin");
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
                        <form name="frmexportapqr" id="frmexportapqr" method="post" enctype="multipart/form-data" action="procedures/excel-checkinfuncionarios.php">
                            <table>
                                <tr>
                                    <td><input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="FechaInicio" value="<?php echo date("Y-m-d") ?>"></td>
                                    <td><input type="text" id="FechaFinal" name="FechaFinal" placeholder="Fecha Final" class="col-xs-12 calendar" title="FechaFinal" value="<?php echo date("Y-m-d") ?>"></td>
                                    <td>
                                        <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                                        <input type="hidden" name="IDUsuario" id="IDUsuario" value="<?= $id ?>">
                                        <input class="btn btn-info" type="submit" name="expbnr" id="expbnr" value="<?= SIMUtil::get_traduccion('', '', 'Exportar', LANGSESSION); ?>">
                                        <input class="btn btn-purple" type="button" name="searchCheckin" id="searchCheckin" rel="<?= $script; ?>.php?action=detalle&search=searchDate&id=<?= $id ?>&type=<?= $table ?>" value="<?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?>">
                                        <input class="btn btn-success" type="button" name="aprobarCheckin" id="aprobarCheckin" club="<?= SIMUser::get('club') ?>"  idUsuario="<?= $id ?>" value="<?= SIMUtil::get_traduccion('', '', 'AprobarTodo', LANGSESSION); ?>">
                                    </td>
                                <tr>
                            </table>
                        </form>

                        <table id="grid-table"></table>
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

        $('#aprobarCheckin').click(function() {
            var club = $(this).attr('club');
            var id = $(this).attr('idUsuario');

            $.post('includes/async/checkinfuncionariosDetalle.async.php', {
                'club': club,
                'id': id,
                'oper': 'aprobarTodo'
            }, function(response) {
                if (response == 1) {
                    alert('Horas extras aprobadas')
                    window.location.reload();
                } else {
                    alert('Error al ejecutar acción');
                }
            });
        });

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
            colNames: [
                'Fecha',
                'Planificacion<BR>Del Dia',
                'Entrada<BR>(Estimada)', 
                'Entrada',
                'Salida<BR>(Estimada)', 
                'Salida',
                'Tiempo<BR>Almuerzo',
                'Novedades',
                'Tiempo<BR>Novedades',
                'Tiempo<BR>Laborado',
                'Tiempo<BR>extra',
                'Tiempo<BR>No Laborado',
                'Estado', 
                'Comentario',
                'Accion'
            ],
            colModel: [
                {name: 'Fecha', index: 'Fecha', align: "left", width: "60"},
                {name: 'Plan', index: 'Plan', align: "left", width: "80"},
                {name: 'EntradaPlan', index: 'EntradaPlan', align: "left", width: "60", search: false},
                {name: 'Entrada', index: 'Entrada', align: "left", width: "60", search: false},
                {name: 'SalidaPlan', index: 'SalidaPlan', align: "left", width: "60", search: false},
                {name: 'Salida', index: 'Salida', align: "left", width: "60", search: false},
                {name: 'TiempoAlmuerzo', index: 'TiempoAlmuerzo', align: "left", width: "60", search: false},
                {name: 'Novedades', index: 'Novedades', align: "left", width: "60", search: false},
                {name: 'TiempoNovedades', index: 'TiempoNovedades', align: "left", width: "60", search: false},
                {name: 'TiempoLaborado', index: 'TiempoLaborado', align: "left", width: "60", search: false},
                {name: 'Extra', index: 'Extra', align: "left", width: "60", search: false},
                {name: 'NoLaboral', index: 'NoLaboral', align: "left", width: "60", search: false},
                {name: 'Estado', index: 'Estado', align: "left", width: "90"},
                {name: 'ComentarioRevision', index: 'ComentarioRevision', align: "center", width: "110"},
                {name: 'Accion', index: '', align: "center", width: "50", search: false}
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