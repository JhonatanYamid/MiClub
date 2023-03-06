<?

	$url_search = "";
	if( SIMNet::req("action") == "search" )
	{
		$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
		foreach($_GET as $id_campo => $valor_campo):
			$url_search .= "&".$id_campo ."=". $valor_campo;
		endforeach;
	}//end if
?>

		<div class="tabbable" id="myTABS" role="tablist">
			<ul class="nav nav-tabs" id="myTab">
				<li class="<?php if(empty($_GET[tabsocio])) echo "active"; ?>" >
					<a data-toggle="tab" href="#diagnosticos" role="tab">
					<i class="ace-icon fa fa-flask orange bigger-120" ></i>
					Diagn&oacute;sticos
					</a>
				</li>
				<li class="<?php if($_GET[tabsocio]=="sindiagnostico") echo "active"; ?>">
					<a data-toggle="tab" href="#sindiagnostico" role="tab">
						<i class="green ace-icon fa fa-users orange bigger-120" ></i>
						Sin Diagn&oacute;stico
					</a>
				</li>
				<li class="<?php if($_GET[tabsocio]=="sindiagnostico") echo "active"; ?>">
					<a data-toggle="tab" href="#indicadores">
						<i class="green ace-icon fa fa-signal bigger-120"></i>
							Indicadores
					</a>
				</li>
			</ul>
		</div>

<div class="tab-content">
	<div id="diagnosticos" class="tab-pane fade <?php if(empty($_GET[tabsocio])) echo "in active"; ?> ">
		
		<form class="form-horizontal" id="frmBuscarDiagnostico">	
			 <table id="simple-table" class="table table-striped table-bordered table-hover">
				<tr>
                                      <td>
						<input type="text" name="qryString" id="qryString" class="form-control"  placeholder="Nombre">
					</td>
					<td>
						<input type="text" id="DIA" name="DIA" placeholder="Fecha" class="col-xs-12 calendar" title="Fecha" value="<?php echo date("Y-m-d") ?>" >
					</td>

					<td  align="center">
						<span class="input-group-btn">
							<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmBuscarDiagnostico">
								<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
								Buscar
							</button>
						</span>
						<span class="input-group-btn">
							<button type="button" class="btn btn-primary btn-sm" id="btTodos">
								Ver Todos
							</button>
						</span>
						</td>
				</tr>
			</table>
		</form>
		
	<div class="widget-box transparent" id="recent-box">
		
		<div class="widget-body">
			<div class="widget-main padding-4">
				<div class="row">
					<div class="col-xs-12">
						<div id="jqGrid_container">
							<form name="frmexportaregdiagnostico" id="frmexportaregdiagnostico" method="post" enctype="multipart/form-data" action="procedures/excel-regDiagnostico.php">
							<table>
								<tr>
									<td><input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo date("Y-m-d") ?>" ></td>
									<td><input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo date("Y-m-d") ?>" ></td>
									<td>
										<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
									<input type="hidden" name="IDUsuario" id="IDUsuario" value="<?php echo SIMUser::get("IDUsuario"); ?>">
										<input type="hidden" name="IDPerfil" id="IDPerfil" value="<?php echo SIMUser::get("IDPerfil"); ?>">
										<input class="btn btn-info" type="submit" name="exppqr" id="exppqr" value="Exportar" >
										<!-- <a href="procedures/excel-pqr.php?IDClub=<?php echo SIMUser::get("club"); ?>&IDUsuario=<?php echo SIMUser::get("IDUsuario"); ?>&IDPerfil=<?php echo SIMUser::get("IDPerfil"); ?>"><img src="assets/img/xls.gif" >Exportar</a>-->
									</td>
								<tr>
							</table>
							</form>
						<table id="grid-table"></table>
						<div id="grid-pager"></div>
						</div>

						

						<script type="text/javascript">
							var $path_base = ".";//in Ace demo this will be used for editurl parameter
						</script>

						<!-- PAGE CONTENT ENDS -->
					</div>
				</div>
			</div>
		</div>
	</div>

		<?
			include( "cmp/footer_grid.php" );
		?>
	</div><!-- END TAB CONTEC tab 1 -->
                                                
		<div id="sindiagnostico" class="tab-pane fade <?php if($_GET[tabsocio]=="sindiagnostico") echo "in active"; ?> " role="tabpanel">
			<form class="form-horizontal" id="frmBuscarSINDiagnostico">	
			 <table id="simple-table" class="table table-striped table-bordered table-hover">
				<tr>
                                      <td>
						<input type="text" name="qryStringSIN" id="qryStringSIN" class="form-control"  placeholder="Nombre">
					</td>
					<td>
						<input type="text" id="DIA_SIN" name="DIA_SIN" placeholder="Fecha" class="col-xs-12 calendar" title="Fecha" value="<?php echo date("Y-m-d") ?>" >
					</td>

					<td  align="center">
						<span class="input-group-btn">
							<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmBuscarSINDiagnostico">
								<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
								Buscar
							</button>
						</span>
						<span class="input-group-btn">
							<button type="button" class="btn btn-primary btn-sm" id="btTodosSIN">
								Ver Todos
							</button>
						</span>
						</td>
				</tr>
			</table>
			</form>		
			<table id="grid-table_sindagnostico"></table>
			<div id="grid-pager_sindagnostico"></div>
		</div>
</div>
					
	<!-- inline scripts related to this page -->
	<script type="text/javascript">

		jQuery(function($) {
		
			var grid_selector = "#grid-table";
			var pager_selector = "#grid-pager";
				
			var grid_selector2 = "#grid-table_sindagnostico";
			var pager_selector2 = "#grid-pager_sindagnostico";
				
			$('#frmBuscarDiagnostico').submit(function(){
			
				var qryString = $("#qryString").val(); 
				var DIA = $("#DIA").val(); 

				$(grid_selector).jqGrid('setGridParam', {
					url:'includes/async/<?php echo $script; ?>.async.php?oper=search&qryString='+qryString+'&DIA='+DIA,

				//	url: '/getdata?id=1234&name=val.text',
					datatype: "json"
				}).trigger("reloadGrid");
				
				return false;
			});
			$('#frmBuscarSINDiagnostico').submit(function(){
			
				var qryString = $("#qryStringSIN").val(); 
				var DIA = $("#DIA_SIN").val(); 

				$(grid_selector2).jqGrid('setGridParam', {
					url:'includes/async/<?php echo $script; ?>SIN.async.php?oper=search&qryString='+qryString+'&DIA='+DIA,

				//	url: '/getdata?id=1234&name=val.text',
					datatype: "json"
				}).trigger("reloadGrid");
				
				return false;
			});

			
			$('#btTodos').on('click', function (e) {
			
				$(grid_selector).jqGrid('setGridParam', {
					url:'includes/async/<?php echo $script; ?>.async.php',

					datatype: "json"
				}).trigger("reloadGrid");
				
			});
			
			$('#btTodosSIN').on('click', function (e) {
			
				$(grid_selector2).jqGrid('setGridParam', {
					url:'includes/async/<?php echo $script; ?>SIN.async.php',

					datatype: "json"
				}).trigger("reloadGrid");
				
			});
			
			$('#myTABS a[href="#sindiagnostico"]').on('click', function (e) {
				jQuery(grid_selector2).jqGrid({
					url:'includes/async/<?php echo $script; ?>SIN.async.php<?=$url_search ?>',
					datatype: "json",
					colNames:['Ver','Usuario' ],
					colModel:[
						{name:'Editar',index:'', align:"center",width:'30'},
						{name:'Usuario',index:'Nombre', align:"left",width:'220'},
					],
					rowNum:100,
					rowList:[20,40,100],
					sortname: 'Nombre',
					viewrecords: true,
					sortorder: "ASC",
					caption:"",
					height: "100%",
					width:855,
					pager : pager_selector2,
					altRows: true,
					toppager: true,
					multiselect: false,
					//multikey: "ctrlKey",
					multiboxonly: true,
					loadComplete : function() {
						var table = this;
						setTimeout(function(){
						//	styleCheckbox(table);

						//	updateActionIcons(table);
							updatePagerIcons(table);
							enableTooltips(table);
						}, 0);

					//	preparaform();
					}
				});		
			})	
				
				//resize to fit page size
				//resize to fit page size
				$(window).on('resize.jqGrid', function () {
					$(grid_selector).jqGrid( 'setGridWidth', $(".page-content").width() );
				});
				
				$(window).on('resize.jqGrid', function () {
					$(grid_selector2).jqGrid( 'setGridWidth', $(".page-content").width() );
				});
				
				//resize on sidebar collapse/expand
				var parent_column = $(grid_selector).closest('[class*="col-"]');
				var parent_column = $(grid_selector2).closest('[class*="col-"]');
				
				$(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
					if( event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed' ) {
						//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
						setTimeout(function() {
							$(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
						}, 0);
					}
				})

				jQuery(grid_selector).jqGrid({
					url:'includes/async/<?php echo $script; ?>.async.php',
					datatype: "json",
					colNames:['Ver','Usuario','Nombre Diagnostico', 'Fecha' ],
					colModel:[
						{name:'Editar',index:'', align:"center",width:'30'},
						{name:'Usuario',index:'Nombre', align:"left",width:'220'},
						{name:'NomDiag',index:'NomDiag', align:"left",width:'220'},
						{name:'DIA',index:'DIA', align:"left",width:'100'}
					],
					rowNum:100,
					rowList:[20,40,100],
					sortname: 'DIA',
					viewrecords: true,
					sortorder: "DESC",
					caption:"",
					height: "100%",
					width:855,
					pager : pager_selector,
					altRows: true,
					toppager: true,
					multiselect: false,
					//multikey: "ctrlKey",
					//multiboxonly: true,

					loadComplete : function() {
						var table = this;
						setTimeout(function(){
						//	styleCheckbox(table);

						//	updateActionIcons(table);
							updatePagerIcons(table);
							enableTooltips(table);
						}, 0);

					//	preparaform();
					}

				});
	
				var datePick = function(elem)
				{
				   jQuery(elem).datepicker();
				}

				$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size

				//enable search/filter toolbar
			//	jQuery(grid_selector).jqGrid('filterToolbar',{defaultSearch:true,stringResult:true})
			//	jQuery(grid_selector).filterToolbar({});


				//enable datepicker
				function pickDate( cellvalue, options, cell ) {
					setTimeout(function(){
						$(cell) .find('input[type=text]')
								.datepicker({format:'yyyy-mm-dd' , autoclose:true});
					}, 0);
				}

				//navButtons
			
	
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

				function enableTooltips(table) {
					$('.navtable .ui-pg-button').tooltip({container:'body'});
					$(table).find('.ui-pg-div').tooltip({container:'body'});
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
