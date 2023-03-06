<?

$url_search = "";
if (SIMNet::req("action") == "search") {
    $url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
} //end if

?>

<div class="widget-box transparent" id="recent-box">		

<div class="widget-header">
    <h4 class="widget-title lighter smaller">
        <i class="ace-icon fa fa-users orange"></i>Listado de Talegas
    </h4>
</div>

<div class="widget-body">
    <div class="widget-main padding-4">
        <div class="row">
            <div class="col-xs-12">		


                <table id="grid-table"></table>

                <div id="grid-pager"></div>

                <script type="text/javascript">
                    var $path_base = ".";//in Ace demo this will be used for editurl parameter
                </script>

                <!-- PAGE CONTENT ENDS -->
            </div>
        </div>
    </div>
</div>
</div>


<div class="modal fade" id="modalHistorico" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">               
            <h5 class="modal-title" id="exampleModalLabel">Histórico</h5>
            <button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <table id="tablaHistorico"></table>
                <div id="tablaHistorico-pager"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

        </div>
    </div>
</div>
</div>

<div class="modal fade" id="modalHistoricoInventario" tabindex="-1" role="dialog" aria-labelledby="LabelHistoricoInventario" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">               
            <h5 class="modal-title" id="LabelHistoricoInventario">Histórico de Inventario</h5>
            <button type="button" class="close" style="line-height:0px !important;" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" style="overflow:auto;">
            <table id="tablaHistoricoInventario" ></table>
            <div id="tablaHistoricoInventario-pager"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
</div>
</div>


<?php
include( "cmp/footer_grid.php" );
?>

<!-- inline scripts related to this page -->
<script type="text/javascript">

jQuery(function ($) {
    var grid_selector = "#grid-table";
    var pager_selector = "#grid-pager";

    //resize to fit page size
    $(window).on('resize.jqGrid', function () {
        $(grid_selector).jqGrid('setGridWidth', $(".page-content").width());
    })
    //resize on sidebar collapse/expand
    var parent_column = $(grid_selector).closest('[class*="col-"]');
    $(document).on('settings.ace.jqGrid', function (ev, event_name, collapsed) {
        if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
            //setTimeout is for webkit only to give time for DOM changes and then redraw!!!
            setTimeout(function () {
                $(grid_selector).jqGrid('setGridWidth', parent_column.width());
            }, 0);
        }
    })

    //Grilla principal
    jQuery(grid_selector).jqGrid({

        url: 'includes/async/administrarTalega.async.php<?= $url_search ?>',
        datatype: "json",
        colNames: ['Codigo', 'Nombre', 'Socio/Invitado', 'Estado','Lugar solicitud','Fecha solicitud','Lugar entrega','Fecha entrega','Localización', 'Fecha registro', 'Acciones'],
        colModel: [
            {name: 'codigo', index: 'codigo', align: "left"},
            {name: 'nombre', index: 'nombre', align: "left"},
            {name: 'socio', index: 'socio', align: "left"},
            {name: 'estado', index: 'estado', align: "left"},
            {name: 'lugarSolicitud', index: 'lugarSolicitud', align: "left"},
            {name: 'fechaSolicitud', index: 'fechaSolicitud', align: "left"},
            {name: 'lugarEntrega', index: 'lugarEntrega', align: "left"},
            {name: 'fechaEntrega', index: 'fechaEntrega', align: "left"},
            {name: 'localizacion', index: 'localizacion', align: "left"},
            {name: 'fechaRegistro', index: 'fechaRegistro', align: "left"},
            {name: 'Acciones', index: 'Acciones', align: "left", search: false, width:'200'},
        ],
        rowNum: 100,
        rowList: [20, 40, 100],
        sortname: 't.fechaRegistro',
        viewrecords: true,
        sortorder: "DESC",
        caption: "",
        height: "100%",
        width: 855,
        multiselect: true,
        editurl: "includes/async/administrarTalega.async.php",
        pager: pager_selector,
        altRows: true,
        //toppager: true,					
        multiselect: false,
        //multikey: "ctrlKey",
        multiboxonly: true,

        loadComplete: function () {
            var table = this;
            setTimeout(function () {
                updatePagerIcons(table);
            }, 0);

            preparaform();
        },
    });

    $(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size

    //enable search/filter toolbar
    jQuery(grid_selector).jqGrid('filterToolbar', {defaultSearch: true, stringResult: true})
    jQuery(grid_selector).filterToolbar({});

    //navButtons
    jQuery(grid_selector).jqGrid('navGrid', pager_selector,
        {

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
        },
        {
            beforeShowForm: function (e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        },
        {
            //new record form
            //width: 700,
            closeAfterAdd: true,
            recreateForm: true,
            viewPagerButtons: false,
            beforeShowForm: function (e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
                        .wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        },
        {
            //delete record form
            recreateForm: true,
            beforeShowForm: function (e) {
                var form = $(e[0]);
                if (form.data('styled'))
                    return false;

                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_delete_form(form);

                form.data('styled', true);
            },
            onClick: function (e) {
                //alert(1);
            }
        },
        {
            //search form
            recreateForm: true,
            afterShowSearch: function (e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
                style_search_form(form);

            },
            afterRedraw: function () {
                style_search_filters($(this));
            }
            ,
            multipleSearch: true,
        },
        {
            //view record form
            recreateForm: true,
            beforeShowForm: function (e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
            }
        }
    )

    //replace icons with FontAwesome icons like above iconos de paginado
    function updatePagerIcons(table) {
        var replacement = {
            'ui-icon-seek-first': 'ace-icon fa fa-angle-double-left bigger-140',
            'ui-icon-seek-prev': 'ace-icon fa fa-angle-left bigger-140',
            'ui-icon-seek-next': 'ace-icon fa fa-angle-right bigger-140',
            'ui-icon-seek-end': 'ace-icon fa fa-angle-double-right bigger-140'
        };
        $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function () {
            var icon = $(this);
            var $class = $.trim(icon.attr('class').replace('ui-icon', ''));

            if ($class in replacement)
                icon.attr('class', 'ui-icon ' + replacement[$class]);
        })
    }

    $(document).on("click", ".imprimirCodigoBarras", function (){
        var ruta = $(this).attr("ruta");

        var ficha = '';
        ficha += '<div style="margin-left:20px;text-align:center;">';
        ficha += '<table width="440px">';
        ficha += '<tr><td><img src="' + ruta + '"></td></tr>';
        ficha += '</table>';
        ficha += '</div>';

        var ventana = window.open('', 'Codigo de barras');
        ventana.document.write(ficha);
        setTimeout(function ()
        {
            ventana.print();
            ventana.close();
        }, 1000);
    });
    
    //Grilla Historico
    jQuery("#tablaHistorico").jqGrid({
        datatype: "json",
        colNames: ['Estado', 'Fecha', 'Lugar', 'Procesa', 'Gestiona', 'Observaciones'],  
        colModel: [
            {name: 'estado', index: 'estado', align: "left"},
            {name: 'fechaRegistro', index: 'fechaRegistro', align: "left"},             
            {name: 'lugar', index: 'lugar', align: "left"},                     
            {name: 'socio', index: 'socio', align: "left"},                   
            {name: 'usuario', index: 'usuario', align: "left"},           
            {name: 'observaciones', index: 'observaciones', align: "left"},           
        ],
        rowNum: 10,
        rowList: [10, 40, 100],
        sortname: 'fechaRegistro',
        viewrecords: true,
        sortorder: "DESC",
        caption: "",
        height: "100%",
        pager: "#tablaHistorico-pager",
        altRows: true,
        multiboxonly: true,

        loadComplete: function () {
            $("#tablaHistorico").jqGrid('setGridWidth', 850, true);
        },

    });
    
    jQuery("#tablaHistorico").jqGrid('filterToolbar', {defaultSearch: true, stringResult: true})
    jQuery("#tablaHistorico").filterToolbar({});

    $(document).on("click", ".btnHistorico", function ()
    {
        var idTalega = $(this).attr("talega");
        $("#modalHistorico").modal("show");
        
        jQuery("#tablaHistorico").jqGrid('setGridParam',{url:'includes/async/historicoTalega.async.php?idTalega='+idTalega}).trigger("reloadGrid");

    });

    //Grillas Historico Inventario
    //funcion para eliminar y volver a crear el contenido del modal
    jQuery('#modalHistoricoInventario').on('hidden.bs.modal', function (e) {
        var contenido = "<table id='tablaHistoricoInventario'></table> <div id='tablaHistoricoInventario-pager'></div>";
        jQuery(this).find('.modal-body').empty();
        jQuery(this).find('.modal-body').html(contenido);
    });

    //creacion de la grilla y su contenido 
    $(document).on("click", ".btnHistoricoInventario", function (){ 

        var idTalega = $(this).attr("talega");
        var nombres = $(this).attr("nombresProp");
        var nombresArr = nombres.split(",");

        var Names = ['Estado', 'Fecha'];
        var Model = [
            {name: 'estado', index: 'estado', align: "left"},
            {name: 'fechaRegistro', index: 'fechaRegistro', align: "left"} 
        ]
        
        $.each(nombresArr,function(id,elem){
            Names[Names.length]= elem;
            Model[Model.length]= {name: elem, index: elem, align: "left"};
        });

        jQuery("#tablaHistoricoInventario").jqGrid({
            datatype: "json",
            colNames: Names,
            colModel: Model,
            url:'includes/async/historicoTalegaInventario.async.php?idTalega='+idTalega,
            //url:'includes/async/historicoTalegaInventario.async.php<?= $url_search ?>?idTalega='+idTalega,
            rowNum: 10,
            rowList: [10, 40, 100],
            sortname: 'fechaRegistro',
            viewrecords: true,
            sortorder: "DESC",
            caption: "",
            height: "100%",
            shrinkToFit:false,
            forceFit:true,
            width: 860,
            pager: "#tablaHistoricoInventario-pager",
            altRows: true,
            multiboxonly: true

        });
        jQuery("#tablaHistoricoInventario").jqGrid('filterToolbar', {defaultSearch: true, stringResult: true})
        jQuery("#tablaHistoricoInventario").filterToolbar({});
        
        $("#modalHistoricoInventario").modal("show");

    });

});

setInterval(function () {
    jQuery('#grid-table').trigger("reloadGrid");  
}, 3000); 
</script>

