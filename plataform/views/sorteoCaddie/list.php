		

	<div class="widget-box transparent" id="recent-box">		

              <div class="widget-header">
            <h4 class="widget-title lighter smaller">
                <i class="ace-icon fa fa-users orange"></i>CADDIES DISPONIBLES PARA EL SORTEO
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
							

			

		<?php
			include( "cmp/footer_grid.php" );
		?>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			
			jQuery(function($) {
				var grid_selector = "#grid-table";
				var pager_selector = "#grid-pager";
				
				//resize to fit page size
				$(window).on('resize.jqGrid', function () {
					$(grid_selector).jqGrid( 'setGridWidth', $(".page-content").width() );
			    })
				//resize on sidebar collapse/expand
				var parent_column = $(grid_selector).closest('[class*="col-"]');
				$(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
					if( event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed' ) {
						//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
						setTimeout(function() {
							$(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
						}, 0);
					}
			    })	
				
	
			
				jQuery(grid_selector).jqGrid({
					
					url:'includes/async/sorteoCaddie.async.php<?=$url_search ?>',
					datatype: "json",
					colNames:['Numero documento', 'Nombre', 'Apellido' ],
					colModel:[						
						{name:'numeroDocumento',index:'numeroDocumento', align:"left"},
						{name:'nombre',index:'nombre', align:"left"},
						{name:'apellido',index:'apellido', align:"left"},
					],
					rowNum:100,
					rowList:[20,40,100],
					sortname: 'nombre',
					viewrecords: true,
					sortorder: "ASC",
					caption:"",
					height: "100%",
					width:855,
					multiselect: true,
					editurl: "includes/async/sorteoCaddie.async.php",
					pager : pager_selector,
					altRows: true,
					//toppager: true,					
					multiselect: false,
					//multikey: "ctrlKey",
			                multiboxonly: true,
			
					loadComplete : function() {
						var table = this;
						setTimeout(function(){							
							updatePagerIcons(table);
						}, 0);

						preparaform();
					},
					
				});
				

				$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size
				
				
			
				//enable search/filter toolbar
				jQuery(grid_selector).jqGrid('filterToolbar',{defaultSearch:true,stringResult:true})
				jQuery(grid_selector).filterToolbar({});
			
			
				
			
				//navButtons
				jQuery(grid_selector).jqGrid('navGrid',pager_selector,
					{ 	//navbar options
						
						edit: false,
						editicon : 'ace-icon fa fa-pencil blue',
						add: false,
						addicon : 'ace-icon fa fa-plus-circle purple',
						del: false,
						delicon : 'ace-icon fa fa-trash-o red',
						search: false,
						searchicon : 'ace-icon fa fa-search orange',
						refresh: true,
						refreshicon : 'ace-icon fa fa-refresh green',
						view: true,
						viewicon : 'ace-icon fa fa-search-plus grey',
					},
					{
						//edit record form
						//closeAfterEdit: true,
						//width: 700,
						recreateForm: true,
						beforeShowForm : function(e) {
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
						beforeShowForm : function(e) {
							var form = $(e[0]);
							form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
							.wrapInner('<div class="widget-header" />')
							style_edit_form(form);
						}
					},
					{
						//delete record form
						recreateForm: true,
						beforeShowForm : function(e) {
							var form = $(e[0]);
							if(form.data('styled')) return false;
							
							form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
							style_delete_form(form);
							
							form.data('styled', true);
						},
						onClick : function(e) {
							//alert(1);
						}
					},
					{
						//search form
						recreateForm: true,
						afterShowSearch: function(e){
							var form = $(e[0]);
							form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
							style_search_form(form);

						},
						afterRedraw: function(){
							style_search_filters($(this));
						}
						,
						multipleSearch: true,
						/**
						multipleGroup:true,
						showQuery: true
						*/
					},
					{
						//view record form
						recreateForm: true,
						beforeShowForm: function(e){
							var form = $(e[0]);
							form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
						}
					}
				)
			
			
				//replace icons with FontAwesome icons like above iconos de paginado
				function updatePagerIcons(table) {
					var replacement = 
					{
						'ui-icon-seek-first' : 'ace-icon fa fa-angle-double-left bigger-140',
						'ui-icon-seek-prev' : 'ace-icon fa fa-angle-left bigger-140',
						'ui-icon-seek-next' : 'ace-icon fa fa-angle-right bigger-140',
						'ui-icon-seek-end' : 'ace-icon fa fa-angle-double-right bigger-140'
					};
					$('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function(){
						var icon = $(this);
						var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
						
						if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
					})
				}
			
				
			
			});
		</script>
	
