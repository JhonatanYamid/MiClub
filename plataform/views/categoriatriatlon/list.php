<?

$url_search = "";
if(SIMNet::req("action") == "search") {
	$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
}//end if

$actionFrm = SIMNet::req("action") == 'edit' ? SIMUtil::lastURI() : "/plataform/$script.php?action=add";

?>

<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-floppy-o orange"></i><?= SIMUtil::get_traduccion('', '', 'crearmodificar', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?= $actionFrm ?>" enctype="multipart/form-data">
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Carrera', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formPopup('Carrera', 'Nombre', 'IDCarrera', 'IDCarrera', $frm["IDCarrera"], SIMUtil::get_traduccion('', '', 'seleccioneuno', LANGSESSION), '', '', "AND Activo = 'S' AND IDClub = $IDClub"); ?>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>"></div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Numerodecamisetainicial', LANGSESSION); ?>: </label>
								<div class="col-sm-8"><input type="text" id="NumInicial" name="NumInicial" placeholder="<?= SIMUtil::get_traduccion('', '', 'NumInicial', LANGSESSION); ?>" value="<?php echo $frm["NumInicial"]; ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'NumInicial', LANGSESSION); ?>"></div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Activo"], 'Activo', "title='".SIMUtil::get_traduccion('', '', 'activo', LANGSESSION)."' class='input mandatory'") ?>
								</div>
							</div>
						</div>
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $IDClub ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>
								<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-list orange"></i><?= SIMUtil::get_traduccion('', '', 'listadode', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("titleB"), LANGSESSION)); ?>
		</h4>
	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<div id="jqGrid_container">
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

			url: 'includes/async/<?php echo $script; ?>.async.php<?= $url_search ?>',
			datatype: "json",
			colNames: ['<?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'carrera', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Camisetainicial', LANGSESSION); ?>',
					   '<?= SIMUtil::get_traduccion('', '', 'activo', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Eliminar', LANGSESSION); ?>'],
			colModel: [
				{name: 'Editar',index: 'editar',search: false,align: "center"},
				{name: 'Carrera',index: 'Carrera',align: "left"},
				{name: 'Nombre',index: 'Nombre',align: "left"},
				{name: 'NumInicial',index: 'NumInicial',align: "left"},
				{name: 'Activo',index: 'Activo',align: "left"},
				{name: 'Eliminar',index: '',search: false,align: "center"}
			],
			rowNum: 100,
			rowList: [20, 40, 100],
			sortname: ' IDCarrera,Nombre',
			viewrecords: true,
			sortorder: "DESC",
			caption: "<?= SIMUtil::get_traduccion('', '', SIMReg::get("titleB"), LANGSESSION); ?>",
			height: "100%",
			width: 855,
			multiselect: true,
			pager: pager_selector,
			altRows: true,
			multiselect: false,
			multiboxonly: true,

			loadComplete: function() {
				var table = this;
				setTimeout(function() {
					updatePagerIcons(table);
					enableTooltips(table);
				}, 0);

				preparaform();
			},

			onSelectRow: function(id) {
				var cat = jQuery(grid_selector).jqGrid('getRowData', id);
				if (cat.Editar != ''){
					location.href = "<?php echo $script ?>.php?action=edit&id=" + id;
				}
				return false;
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

		jQuery(grid_selector).navGrid(pager_selector,{

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
			view: false,
			viewicon: 'ace-icon fa fa-search-plus grey'

		},).navButtonAdd(pager_selector,{
			caption:"<?= SIMUtil::get_traduccion('', '', 'Agregar', LANGSESSION);?>", 
			buttonicon: 'ace-icon fa fa-plus',
			onClickButton: function(){ 
				location.href = "<?php echo $script ?>.php?action=add";
			}, 
			position:"last"
		});

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

		$(document).one('ajaxloadstart.page', function(e) {
			$(grid_selector).jqGrid('GridUnload');
			$('.ui-jqdialog').remove();
		});
	});

</script>