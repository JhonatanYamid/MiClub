 
<div>
	<div id="jqGrid_containerPagos">
	<table id="grid-table2"></table>
	</div>
	<div id="grid-pager2"></div>
</div>
 
<script src="assets/js/jquery.jqGrid.min.js"></script>
<script src="assets/js/grid.locale-en.js"></script>

 
<script type="text/javascript">
	jQuery(function($) {

	 

		var grid_selector = "#grid-table2";
		var pager_selector = "#grid-pager2";

		<?php if (count($array_NoPregunta1) > 0) {

		?>
			 
				jQuery(grid_selector).jqGrid({
					url: 'includes/async/get_encuestasresp_empleados.async.php?id=<?php echo $_GET["id"]; ?>&encuesta=729',
					datatype: "json",
					//colNames:['Usuario','Valor','Fecha' ],
					<?php

					echo "colNames: ['Eliminar','Usuario','Fecha','" . str_replace(",", "','", implode(",", $array_preguntas1)) . '\'],'; ?>
					colModel: [{
							name: 'Eliminar',
							index: '',
							width: '1200',
							align: 'center',
							sortable: false
						},
						{
							name: 'Nombre',
							index: 'Nombre',
							width: '1200',
							sortable: false
						},
						{
							name: 'Fecha',
							index: 'Fecha',
							width: '1200',
							sortable: false
						},
						 
						<?php $numcols = 1;
						foreach ($array_NoPregunta1 as $colData1) {
							echo "{name:'" . $colData1 . "',index:'" . $colData1 . "', align:'center',width:'1200',sortable:false},";
							$numcols++;
						}
						?>
					],
					rowNum: 100,
					rowList: [20, 40, 100],
					//sortname: 'Nombre',
					viewrecords: true,
					sortorder: "ASC",
					caption: "",
					height: "150%",
					width:  "150%",
					//pager : pager_selector,
					altRows: true,
					//toppager: true,
					multiselect: false,
					//multikey: "ctrlKey",
					//multiboxonly: true,
					loadComplete: function() {
						var table = this;
						setTimeout(function() {
							//	styleCheckbox(table);

							//	updateActionIcons(table);
							updatePagerIcons(table);
							//	enableTooltips(table);
						}, 0);

						//	preparaform();
					}
				});
		 
		<?php
		} // END IF count preguntas
		?>
		$(window).on('resize.jqGrid', function() {
			$(grid_selector).jqGrid('setGridWidth', $(".page-content").width());
		});

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


		$(window).triggerHandler('resize.jqGrid'); //trigger window resize to make the grid get the correct size

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
