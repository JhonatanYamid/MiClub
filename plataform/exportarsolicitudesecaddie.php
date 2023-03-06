<?
	include( "procedures/general.php" );
    include("procedures/solicitudesecaddie.php");
	include("cmp/seo.php");

?>
</head>

<body class="no-skin">
    <?
			include( "cmp/header.php" );
		?>
    <div class="main-container" id="main-container">
        <script type="text/javascript">
        try {
            ace.settings.check('main-container', 'fixed')
        } catch (e) {}
        </script>
        <?
				$menu_reservas[$ids] = " class=\"active\" ";
				include( "cmp/menu.php" );
			?>
        <div class="main-content">
            <div class="main-content-inner">
                <div class="breadcrumbs" id="breadcrumbs">
                    <script type="text/javascript">
                    try {
                        ace.settings.check('breadcrumbs', 'fixed')
                    } catch (e) {}
                    </script>
                    <ul class="breadcrumb">
                        <li>
                            <i class="ace-icon fa fa-home home-icon"></i>
                            <a href="reservas.php?ids=<?=$_GET["ids"]?>">Home</a>
                        </li>
                        <li>
                            <a href=""><?=$datos_club["Nombre"] ?></a>
                        </li>
                    </ul><!-- /.breadcrumb -->
                </div>
                <div class="page-content">
                    <div class="page-header">
                        <h1>
                            <i class="ace-icon fa fa-angle-double-right"></i> Exportar Solicitudes
                        </h1>
                    </div><!-- /.page-header -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="tabbable">
                                <ul class="nav nav-tabs" id="myTab">
                                    <li>
                                        <a data-toggle="tab" class="noTabLink" href="solicitudesecaddie.php">
                                            <i class="green ace-icon fa fa-download bigger-120"></i> Lista Solicitudes </a>
                                    </li> <?php
                                    
                                    $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoExportar");
									if ($Permiso == 1 && $datos_servicio[$_GET["ids"]][TipoSorteo] == 0) :
									?> <li class="active">
                                        <a data-toggle="tab" class="noTabLink" href="exportarsolicitudesecaddie.php">
                                            <i class="green ace-icon fa fa-download bigger-120"></i> Exportar Solicitudes </a>
                                    </li> <?php endif; ?>
                                </ul>
                                <div class="tab-content">
                                    <div id="home" class="tab-pane fade in active">
                                        <div class="widget-box transparent" id="recent-box">
                                            <div class="widget-body">
                                                <div class="widget-main padding-4">
                                                    <div class="row">
                                                        <div class="col-sm-12 widget-container-col ui-sortable">
                                                            <form class="form-horizontal formvalida" role="form" method="get" name="frm" id="frm" action="procedures/exportarsolicitudesecaddie.php" enctype="multipart/form-data">
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo date("Y-m-d") ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Caddie </label>
                                                                        <div class="col-sm-8">
                                                                            <select name="IDCaddiesEcaddie" id="IDCaddiesEcaddie">
                                                                                <option value="">[Seleccione una opción]</option> <?php
                                                                                    $sql_area_club = "Select * From CaddiesEcaddie Where IDClub = '".SIMUser::get("club")."'";
                                                                                    $qry_area_club = $dbo->query($sql_area_club);
                                                                                    while ($r_area = $dbo->fetchArray($qry_area_club)): ?> 
                                                                                        <option value="<?php echo $r_area["IDCaddiesEcaddie"]; ?>"><?php echo $r_area["Nombre"]; ?></option> <?php
                                                                                    endwhile;?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado Solicitud </label>
                                                                        <div class="col-sm-8">
                                                                            <select name="EstadoSolicitud" id="EstadoSolicitud"">
                                                                                <option value="">[Seleccione una opción]</option>
                                                                                <option value="Confirmada">Confirmada</option>
                                                                                <option value="PendientePago">Pendiente de Pago</option>
                                                                                <option value="Cancelada">Cancelada</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Servicio Caddie </label>
                                                                        <div class="col-sm-8">
                                                                            <select name="IDServiciosCaddie" id="IDServiciosCaddie">
                                                                                <option value="">[Seleccione una opción]</option> <?php
                                                                                    $sql_area_club = "Select * From ServiciosCaddie Where IDClub = '".SIMUser::get("club")."'";
                                                                                    $qry_area_club = $dbo->query($sql_area_club);
                                                                                    while ($r_area = $dbo->fetchArray($qry_area_club)): ?> 
                                                                                        <option value="<?php echo $r_area["IDServiciosCaddie"]; ?>"><?php echo $r_area["Nombre"]; ?></option> <?php
                                                                                    endwhile;?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Club donde se ha realizado algun servicio </label>
                                                                        <div class="col-sm-8">
                                                                            <select name="IDClubSeleccion" id="IDClubSeleccion">
                                                                                <option value="">[Seleccione una opción]</option> <?php
                                                                                    $sql_area_club = "SELECT CC.IDListaClubes, LC.Nombre  FROM ListaClubes LC, ClubesCaddies CC, ConfiguracionCaddies COC WHERE LC.IDListaClubes = CC.IDListaClubes AND COC.IDClub = " . SIMUser::get("club") ." AND COC.IDConfiguracionCaddies = CC.IDConfiguracionCaddies ";
                                                                                    $qry_area_club = $dbo->query($sql_area_club);
                                                                                    while ($r_area = $dbo->fetchArray($qry_area_club)): ?> 
                                                                                        <option value="<?php echo $r_area["IDListaClubes"]; ?>"><?php echo $r_area["Nombre"]; ?></option> <?php
                                                                                    endwhile;?>
                                                                            </select>
                                                                        </div>
                                                                    </div>                                                                    
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de Caddie </label>
                                                                        <div class="col-sm-8">
                                                                            <select name="TipoCaddie" id="TipoCaddie">
                                                                                <option value="">[Seleccione una opción]</option>
                                                                                <option value="Aleatorio">Aleatorio</option>
                                                                                <option value="Turbo">Turbo</option>
                                                                                <option value="Especial">Especial</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descargar por Socio </label>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="Accion" name="Accion" placeholder=" <?= SIMUtil::get_traduccion('', '', 'AccionNombreApellidoNumeroDocumento', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax" title="Accion" value="">
                                                                            <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" title="Socio">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                       
                                                                <
                                                                <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                                                                <input type="hidden" name="IDServicio" id="IDServicio" value="<?=$ids ?>">
                                                                <button class="btn btn-info btnEnviar" type="button" rel="frm">
                                                                    <i class="ace-icon fa fa-cloud-download bigger-110"></i> Exportar </button> 																	
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div><!-- /.widget-main -->
                                            </div><!-- /.widget-body -->
                                        </div><!-- /.widget-box -->
                                        <script type="text/javascript">
                                        var $path_base = "."; //in Ace demo this will be used for editurl parameter
                                        </script>
                                        <!-- PAGE CONTENT ENDS -->
                                    </div> <!-- end tab -->
                                </div>
                            </div>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->
        <?
				include("cmp/footer.php");
			?>
    </div><!-- /.main-container -->
    <?
			include( "cmp/footer_grid.php" );
		?>
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
    jQuery(function($) {
        var grid_selector = "";
        var pager_selector = ""; < ? foreach($elementos[$ids] as $key_elemento => $datos_elemento) {
                $grillas[] = $key_elemento; ? > grid_selector = "#grid-table<?=$key_elemento?>";
                pager_selector = "#grid-pager<?=$key_elemento?>";
                jQuery(grid_selector).jqGrid({
                    url: 'includes/async/reservas.async.php?idservicio=<?=$ids ?>&idelemento=<?=$datos_elemento["IDElemento"] ?><?=$url_search ?>',
                    datatype: "json",
                    colNames: ['Fecha', 'Hora', 'Socio', 'Cancelar Reserva.'],
                    colModel: [{
                        name: 'Fecha',
                        index: 'Fecha',
                        align: "center"
                    }, {
                        name: 'Hora',
                        index: 'Hora',
                        align: "left",
                        search: false
                    }, {
                        name: 'Socio',
                        index: 'Socio',
                        align: "left",
                        searchoptions: {
                            attr: {
                                placeholder: "Número de derecho o número de documento"
                            }
                        }
                    }, {
                        name: 'Cancelar',
                        index: 'Cancelar',
                        align: "center",
                        search: false
                    }, ],
                    rowNum: 100,
                    rowList: [100, 200, 300],
                    sortname: 'Hora',
                    viewrecords: true,
                    sortorder: "ASC",
                    caption: "Reservas",
                    height: "100%",
                    width: 855,
                    multiselect: false,
                    editurl: "includes/reservas.async.php",
                    pager: pager_selector,
                    altRows: true,
                    //toppager: true,
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
                    onSelectRow: function(id) {
                        //var IDSocio = $(this).attr("rel");
                        //var IDReserva = $(this).attr("id");
                        //var IDClub = $(this).attr("lang");
                        if (confirm("Esta seguro que desea cancelar la reserva?")) {
                            jQuery.ajax({
                                "type": "POST",
                                "data": {
                                    "IDReservaGeneral": id
                                },
                                "dataType": "json",
                                "url": "includes/async/cancela_reserva.async.php",
                                "success": function(data) {
                                    alert("Reserva Cancelada con exito");
                                    $("#grid-table<?=$key_elemento?>").trigger("reloadGrid");
                                    return false;
                                }
                            });
                        }
                        return false;
                    },
                });
                $(grid_selector).jqGrid('setGridWidth', $("#grillasReserva .tab-content").width());
                $(grid_selector).jqGrid('sortGrid', 'Fecha', true, 'asc');
                $(grid_selector).jqGrid('sortGrid', 'Hora', true, 'asc');
                //resize to fit page size
                $(window).on('resize.jqGrid', function() {
                    $(grid_selector).jqGrid('setGridWidth', $("#grillasReserva .tab-content").width());
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
                }); < ?
            } //end for
            ? >
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
                $(cell).find('input[type=checkbox]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');
            }, 0);
        }
        //enable datepicker
        function pickDate(cellvalue, options, cell) {
            setTimeout(function() {
                $(cell).find('input[type=text]').datepicker({
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
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
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
    </script>
    <input type="hidden" name="grillas" id="grillas" value="<?=implode( ",", $grillas )?>">
</body>

</html>