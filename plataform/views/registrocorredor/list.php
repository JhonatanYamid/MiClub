<?

$url_search = "";
if(SIMNet::req("action") == "search") {
	$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
}//end if



?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-floppy-o orange"></i><?= SIMUtil::get_traduccion('', '', 'registrodecorredores', LANGSESSION); ?> 
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12 tabbable" id="tabsRegistro">
					<ul class="nav nav-tabs">
						<li class="<?php if (empty($_GET[tabregistrocorredor])) echo "active"; ?>">
							<a data-toggle="tab" href="#registroindividual">
								<i class="green ace-icon fa fa-user bigger-120"></i>
								<?= SIMUtil::get_traduccion('', '', 'registroindividual', LANGSESSION); ?>
							</a>
						</li>
						<li class="<?php if ($_GET[tabregistrocorredor] == "RegistroExcel") echo "active"; ?>">
							<a data-toggle="tab" href="#RegistroExcel">
								<i class="green ace-icon fa fa-file-excel-o bigger-120"></i>
								<?= SIMUtil::get_traduccion('', '', 'RegistroExcel', LANGSESSION); ?>
							</a>
						</li>
						<li class="<?php if ($_GET[tabregistrocorredor] == "codigosqr") echo "active"; ?>">
							<a data-toggle="tab" href="#codigosqr">
								<i class="green ace-icon glyphicon glyphicon-qrcode bigger-120"></i>
								<?= SIMUtil::get_traduccion('', '', 'codigosqr', LANGSESSION); ?>
							</a>
						</li>

					</ul>
					<div class="tab-content">
						<div id="registroindividual" class="tab-pane fade <?php if (empty($_GET[tabregistrocorredor])) echo "in active"; ?> ">
							<?php include("views/registrocorredor/registrocorredor.php");?>
						</div>
						<div id="RegistroExcel" class="tab-pane fade <?php if ($_GET[tabregistrocorredor] == "RegistroExcel") echo "in active"; ?> ">
							<?php include("ingresolote.php");?>
						</div>
						<div id="codigosqr" class="tab-pane fade <?php if ($_GET[tabregistrocorredor] == "codigosqr") echo "in active"; ?> ">
							<?php include("codigosqr.php");?>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div>

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
		var idcarrera = $("#IDCarrera").val();

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
			colNames: ['<?= SIMUtil::get_traduccion('', '', 'Camiseta', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'NumeroDocumento', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Email', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Carrera', LANGSESSION); ?>', 
					   '<?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?>',
					   '<?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION); ?>'],
			colModel: [
				{name: 'NumCamiseta',index: 'NumCamiseta',align: "left"},
				{name: 'NumeroDocumento',index: 'NumeroDocumento',align: "left"},
				{name: 'Nombre',index: 'Nombre',align: "left"},
				{name: 'Email',index: 'Email',align: "left"},
				{name: 'Carrera',index: 'Carrera',align: "left"},
				{name: 'Categoria',index: 'Categoria',align: "left"},
				{name: 'Accion',index: '',search: false,align: "center"}
			],
			rowNum: 100,
			rowList: [20, 40, 100],
			sortname: 'IDRegistroCorredor',
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
			title:"Agregar",
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

<script type="text/javascript" src="js/jquery.autocomplete.js"></script>

<script type="text/javascript">
	
	changeCategoria();
	changeCategoria('Lote');
	changeCategoria('Pdf');

	function changeCategoria(tipo=""){
		var idCarrera = $('#IDCarrera'+tipo).val();
		var valueCategoria = tipo != ''  ? '' : '<?= $frm["IDCategoriaTriatlon"]; ?>';

		jQuery.ajax({
			type: "GET",
			data: {
				oper: "select",
				val: valueCategoria,
				tipo: tipo,
				idCarrera: idCarrera
			},
			dataType: "text",
			url: "includes/async/registrocorredor.async.php",
			success: function (data) {
				$("#selectCategoria"+tipo).html(data);
			}
		}); 
	}

	function limpiar(){			
		let NumeroDocumento = $("#NumeroDocumento").val();
		let numDoc = $("#numDoc").val();

		if( NumeroDocumento == '' || NumeroDocumento != numDoc){
			$("#IDSocio,#numDoc, #Nombre, #nom, #Apellido, #apl, #Email, #mail").val('');
		}
	}

	function guardarCorredor(){
		if($("#IDSocio").val() != ''){
			if($("#Nombre").val() != $("#nom").val() || $("#Apellido").val() != $("#apl").val() || $("#Email").val() != $("#mail").val()){
				let msgConf = "Atencion! la información del socio ha cambiado. Si continua los datos del socio tambien seran modificados en nuestra base de datos ¿Desea continuar?";
					
				if(confirm(msgConf) === false) {
					return;
				}
			}
		}
		$("#submitForm").click();
	}

	$('.autocomplete-ajax-corredor').autocomplete({
		serviceUrl: 'includes/async/registrocorredor.async.php',
		dataType: "json",
		transformResult: function(response) {
			return {
				suggestions: $.map(response, function(dataItem) {
					return {
						value: dataItem.NumeroDocumento + "-" + dataItem.Nombre,
						data: dataItem.IDSocio,
						documento: dataItem.NumeroDocumento,
						nombre: dataItem.Nombre,
						apellido: dataItem.Apellido,
						email: dataItem.CorreoElectronico
					};
				})
			};
		},
		params: {
			"oper": "autocomplete",
		},

		paramName: "qryString",
		minChars: 2,

		lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
			var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
			return re.test(suggestion.value);
		},
		onSelect: function(suggestion) {
			$('#NumeroDocumento').val(suggestion.documento);
			$('#IDSocio').val(suggestion.data);
			$('#nmDoc').val(suggestion.documento);
			$('#Nombre').val(suggestion.nombre);
			$('#nom').val(suggestion.nombre);
			$('#Apellido').val(suggestion.apellido);
			$('#apl').val(suggestion.apellido);
			$('#Email').val(suggestion.email);
			$('#mail').val(suggestion.email);
		}

	});
</script>