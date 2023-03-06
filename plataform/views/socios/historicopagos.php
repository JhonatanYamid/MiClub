<?
	$url_search = "";
	if (SIMNet::req("action") == "search") {
		$url_search = "&oper=search_url&qryString=" . SIMNet::get("qryString");
	} 
?>


<div>
	<div id="jqGrid_containerPagos">
		<table id="pagosTable"></table>
	</div>
	<div id="pagospager"></div>
</div>

<script src="assets/js/jquery.jqGrid.min.js"></script>
<script src="assets/js/grid.locale-en.js"></script>

<script type="text/javascript">

	// GRILLA PAGOS PRODUCTOS
	let grid_selector = "#pagosTable";
	let pager_selector = "#pagospager";

	//resize to fit page size
	$(window).on('resize.jqGrid', function() {
		$(grid_selector).jqGrid('setGridWidth', $(".tab-content").width()-10);
	})
	//resize on sidebar collapse/expand
	var parent_column = $(grid_selector).closest('[class*="col-"]');
	$(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
		if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
			//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
			setTimeout(function() {
				$(grid_selector).jqGrid('setGridWidth', parent_column.width()-10);
			}, 0);
		}
	})
	
	jQuery(grid_selector).jqGrid({
		url: 'includes/async/historicopagos.async.php?idsocio=<?= SIMNet::req("id"); ?><?= $url_search ?>',
		datatype: "json",
		colNames: [
			'<?= SIMUtil::get_traduccion('', '', 'verfacturas', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'sede', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'factura', LANGSESSION); ?> No.', 
			'<?= SIMUtil::get_traduccion('', '', 'fechadecompra', LANGSESSION); ?>', 
			'<?= SIMUtil::get_traduccion('', '', 'producto', LANGSESSION); ?>',
			'<?= SIMUtil::get_traduccion('', '', 'cantidad', LANGSESSION); ?>', 
			'<?= SIMUtil::get_traduccion('', '', 'Valortotal', LANGSESSION); ?>'
		],
		colModel: [
			{name: 'Ver',index: 'Ver',search: false,align: "center"},
			{name: 'Sede',index: 'c.Nombre',align: 'left'},
			{name: 'Consecutivo',index: 'Consecutivo',align: "left"},
			{name: 'FechaCreacion',index: 'FechaCreacion',align: "left"},
			{name: 'Producto',index: 'p.Nombre',align: "left"},
			{name: 'Cantidad',index: 'Cantidad',align: "left"},
			{name: 'Total',index: 'fp.Total',align: "left", formatter:'number',formatoptions :{thousandsSeparator: ".", decimalPlaces: 0, defaultValue: '0.00'}}
		],
		rowNum: 100,
		rowList: [20, 40, 100],
		sortname: 'FechaCreacion',
		viewrecords: true,
		sortorder: "ASC",
		caption: "<?= SIMUtil::get_traduccion('', '', 'historialpagos', LANGSESSION); ?>",
		height: "100%",
		width: $(".tab-content").width(),
		pager: pager_selector,
		altRows: true,
		subGrid : true,
		subGridOptions: {
			"plusicon"  : "ace-icon fa fa-plus",
			"minusicon" : "ace-icon fa fa-minus",
			"openicon"  : "ace-icon fa fa-caret-right",
			"reloadOnExpand" : true,
			"selectOnExpand" : true

		},
		subGridRowExpanded: function(subgrid_id, row_id) {

			let subgrid_table_id = subgrid_id+"_t";
			$("#"+subgrid_id).html("<table id='"+subgrid_table_id+"'></table></div>");
			
			jQuery("#"+subgrid_table_id).jqGrid({
				url:"includes/async/historicopagos.async.php?oper=subgrid&id="+row_id,
				datatype: "json",
				colNames: [
					'<?= ucwords(SIMUtil::get_traduccion('', '', 'Documento', LANGSESSION));?>',
					'<?= ucwords(SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION));?>',
					'<?= ucwords(SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION));?>',
					'<?= ucwords(SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION));?>'
				],
				colModel: [
					{name:"NumeroDocumento",index:"NumeroDocumento",key:true},
					{name:"Nombre",index:"Nombre"},
					{name:"FechaInicio",index:"FechaInicio",align:"right"},
					{name:"FechaFin",index:"FechaFin",align:"right"}
				],
				sortname: 's.Nombre',
				sortorder: "asc",
				height: '100%',
				autowidth: true,
    			shrinkToFit: false,
			});
		},

		loadComplete: function() {
			var table = this;
			setTimeout(function() {
				updatePagerIcons(table);
				enableTooltips(table);
			}, 0);
		},
	});

	//enable search/filter toolbar
	jQuery(grid_selector).jqGrid('filterToolbar', {
		defaultSearch: true,
		stringResult: true
	})
	jQuery(grid_selector).filterToolbar({});

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
		recreateForm: true,
		beforeShowForm: function(e) {
			var form = $(e[0]);
			form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
			style_edit_form(form);
		}
	}, {

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

	}, {
		//view record form
		recreateForm: true,
		beforeShowForm: function(e) {
			var form = $(e[0]);
			form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
		}
	})

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
</script>