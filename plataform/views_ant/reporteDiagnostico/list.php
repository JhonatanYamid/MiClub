	<?php

///	if (!empty($_GET["FechaDesde"]) && !empty($_GET["FechaHasta"])):
//		$condiciones .= " and Fecha >= '".$_GET["FechaDesde"]." 00:00:00' and Fecha <= '".$_GET["FechaHasta"]." 23:59:59' ";
//	endif;

//	if (!empty($_GET[IDDiagnostico])):
//		$condiciones .= " and IDDiagnostico = '".$_GET["IDDiagnostico"]."'";
//	endif;

	?>
    <div class="widget-box transparent" id="recent-box">

		<div class="widget-body">
			<div class="widget-main padding-4">
				    <div class="tabbable" id="myTABS" role="tablist">
					<ul class="nav nav-tabs" id="myTab" >
						<li class="active">
							<a data-toggle="tab" href="#home">
								<i class="green ace-icon fa fa-bar-chart-o bigger-120"></i>
									Estad&iacute;sticas
							</a>
						</li>
						<li>
							    <a data-toggle="tab" href="#tabDiagnostico" role="tab">
							    <i class="ace-icon fa fa-flask green bigger-120" ></i>
								    Auto-diagn&oacute;stico
							    </a>
						</li>
						<li>
							    <a data-toggle="tab" href="#tabSindiagnostico" role="tab">
							    <i class="ace-icon fa fa-flask green bigger-120" ></i>
								    SIN Auto-diagn&oacute;stico
							    </a>
						</li>
						<li>
							    <a data-toggle="tab" href="#tabEstado" role="tab">
									<i class="green ace-icon fa fa-stethoscope bigger-120"></i>
										Estado de Salud
								</a>
						</li>
							    <li>
								<a data-toggle="tab" href="#tabPerfil" role="tab">
									<i class="green ace-icon fa fa-user-md bigger-120"></i>
										Perfil
								</a>
							    </li>

							    <li>
								<a data-toggle="tab" href="#tabCierre" role="tab">
									<i class="green ace-icon fa  fa-archive bigger-120"></i>
										Cierre Epidemiol&oacute;gico
								</a>
							    </li>
				        </ul>
						<div class="tab-content">
							<div id="home" class="tab-pane fade  in active">
							    <!-- INICIO TAB DASHBOARD -->
							    <div class="row">
							        <div class="col-md-12">
													<?php if($tipoReporte != "Funcionario"){
														?>
								       <div class="col-md-4">
										   <table class="table">
											       <thead>
										      <tr>
												   <th scope="col">BD Empleados</th>
												   <th scope="col">#</th>
												 </tr>
											       </thead>
											       <tbody>

											       <?php
											       foreach($array_dataEstado AS  $estadoEmpleado){

													   list($estado,$total) = explode(",",$estadoEmpleado);

													   echo '<tr> <td scope="row">'.$estado.'</td>
													    <td>'.$total.'</td></tr>';
												}
											       ?>

											       </tbody>
										   </table>
										   <table class="table">
										   <thead>
										     <tr>
										       <th scope="col">Tipo Empleado</th>
										       <th scope="col">#</th>
										     </tr>
										   </thead>
										   <tbody>

										   <?php
										   foreach($array_dataTipo AS  $tipoEmpleado){

											       list($tipo,$total) = explode(",",$tipoEmpleado);

											       echo '<tr> <td scope="row">'.$tipo.'</td>
												<td>'.$total.'</td></tr>';
										    }
										   ?>

										   </tbody>
								       </table>
											 <table class="table">
										   <thead>
										     <tr>
										       <th scope="col">Estado de Salud</th>
										       <th scope="col">#</th>

										     </tr>
										   </thead>
										   <tbody>
										   <?php
										   foreach($array_dataEstadoSalud AS  $estadoSalud){

											       list($estado,$total) = explode(",",$estadoSalud);

											       echo ' <tr><td scope="row">'.$estado.'</td>
												<td>'.$total.'</td></tr>';
										    }
										   ?>
										    </tbody>
								       </table>
								       </div>
												<?php } //if($tipoReporte != "Funcionario"){
													?>
								       <div class="col-md-8">
										   <div id="container" style="width: 95%;">
										       <canvas id="canvas"></canvas>
										   </div>
									</div>
							       </div>
							    </div>
							    <!-- FIN TAB DASHBOARD-->
						</div>

						<!-- INICIO TAB DIAGNOSTICO -->
							    <div id="tabDiagnostico" class="tab-pane fade">
							    <div class="row">
									<div class="col-md-8">
							    <form class="form-horizontal" id="frmBuscarDiagnostico">

									 <table id="simple-table" class="table table-striped table-bordered table-hover" style="width:840px;">
										<tr>
										      <td style="width:360px;">
										    	<input type="text" name="qryString" id="qryString" class="form-control" style="height: 33px;" placeholder="Nombre">
											</td>
											<td style="width:120px;">
												<input type="text" id="DIA" name="DIA" placeholder="Fecha" class="col-xs-12 calendar" title="Fecha" value="" style="width:100px;" autocomplete="off">
											</td>
											<td style="width:120px;">
													    <?php

													    echo SIMHTML::formPopup( "EstadoSalud" , "Nombre" , "Nombre" , "IDEstadoSalud" , "","[ Seleccione el Estado ]"  , "mandatory" ,"",  " AND IDEstadoSalud <= 2 OR IDClub = '".SIMUser::get("club")."'" )
													    ?>											</td>

											<td  align="center" style="width:220px;">
												<span class="input-group-btn">
													    <button type="button" class="btn btn-purple btn-sm btnEnviar" id="btBuscarDiagnostico">
														    <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
														    Buscar
													    </button>
												    </span>
												    <span class="input-group-btn">
														<button type="button" class="btn btn-info btn-sm" id="btExportaDiagnostico">
															    <span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
															    Exportar
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
							    </div>
						 </div>
							     <div class=�row�>
							       <div class="col-md-4">
									<table id="grid-table"></table>
									<div id="grid-pager"></div>
							       </div>
							    </div>
						</div>
						<!-- FIN TAB DIAGNOSTICO
						<!-- INICIO TAB SIN DIAGNOSTICO -->
								<div id="tabSindiagnostico" class="tab-pane fade" role="tabpanel">
									<div class="row">
									<div class="col-md-8">
									<form class="form-horizontal" id="frmBuscarSINDiagnostico">
									 <table id="simple-table" class="table table-striped table-bordered table-hover">

										<tr>
										      <td>
												<input type="text" name="qryStringSIN" id="qryStringSIN" class="form-control" style="height: 33px;" placeholder="Nombre" autocomplete="off">
											</td>
											<td>
												<input type="text" id="DIA_SIN" name="DIA_SIN" placeholder="Fecha" class="col-xs-12 calendar" title="Fecha" value="" autocomplete="off">
											</td>

											<td  align="center" style="width:220px;">
												<span class="input-group-btn">
													<button type="button" class="btn btn-purple btn-sm" id="btSINDiagnostico" >
														<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
														Buscar
													</button>
												</span>
												<span class="input-group-btn">
												<button type="button" class="btn btn-info btn-sm" id="btExportaSINAUTO">
															<span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
															Exportar
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

									</div>
									</div>
									<div class="row">
									<table id="grid-table_sindagnostico"></table>
									<div id="grid-pager_sindagnostico"></div>
									</div>
								</div>
						<!-- FIN TAB SIN DIAGNOSTICO -->
						<!-- INICIO TAB Estado -->
							    <div id="tabEstado" class="tab-pane fade">
								      <div class="row">
				    					       <div class="col-md-4">
										    <table class="table">
										<!--    <thead class="thead-light">
										     <tr>
										       <th scope="col">Estado de Salud</th>
										       <th scope="col">#</th>

										     </tr>
										   </thead>-->
										   <head class="thead-light">
										   <?php
										   foreach($array_dataEstadoSalud AS  $estadoSalud){

											       list($estado,$total) = explode(",",$estadoSalud);

											       echo ' <tr><th scope="row">Estado '.$estado.'</th>
												<th>'.$total.'</th></tr>';
										    }
										   ?>
										    </thead>
								       </table>
									       </div>

								      </div>
							    <div class="row">
							    <form class="form-horizontal" id="frmBuscarDiagnosticoEdo">


									 <table id="simple-table" class="table table-striped table-bordered table-hover" style="width:800px;">
										<tr>
										      <td style="width:240px;">
										   		<input type="text" name="qryString" id="qryStringEdo" class="col-xs-12" style="height: 33px;" placeholder="Nombre">
											</td>
											<td style="width:120px;">
												<?php
												echo SIMHTML::formPopupV2( "EstadoSalud" , "Nombre" , "Nombre" , "IDEstadoSalud", "IDEstadoSaludEdo" , "","[ Seleccione el Estado ]"  , "mandatory" ,"",  " AND IDEstadoSalud <= 2 OR IDClub = '".SIMUser::get("club")."'" )
												?>
											</td>
											<td  align="center" style="width:220px;">
												<span class="input-group-btn">
										<button type="button" class="btn btn-purple btn-sm btnEnviar" id="btBuscarDiagnosticoEdo">
											<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
											Buscar
										</button>
									</span>
									<span class="input-group-btn">
										    <button type="button" class="btn btn-info btn-sm" id="btExportaEstado">
												<span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
												Exportar
										    </button>
									</span>
									<span class="input-group-btn">
										<button type="button" class="btn btn-primary btn-sm" id="btTodosEdo">
											Ver Todos
										</button>
									</span>
												</td>
										</tr>
									</table>

							    </form>
							    </div>
							     <div class="row">
									 <div class="col-md-4">
									<table id="grid-tableEdo"></table>
									<div id="grid-pagerEdo"></div>
									</div>
							    </div>


  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="padding:20px 50px 10px 50px;color:white !important;text-align: center;background-color: #6fb3e0;font-size: 30px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 style="font-size: 24px;"><span class="glyphicon glyphicon-alert" style="padding: 5px 5px;">

	  </span>Seguimiento : Nuevo Estado</h4>
        </div>
        <div class="modal-body" style="padding:30px 50px;">
	         <p>Seleccione el estado e ingrese las observaciones para actualizar el estado de salud. </p>
          <form role="form">
            <div class="form-group">
			 <input type="text" id="nombreUsuarioEdo" class="form-control" readonly>
            </div>
	    <div class="form-group">
			<?php
	    			echo SIMHTML::formPopupV2( "EstadoSalud" , "Nombre" , "Nombre" , "IDEstadoSalud", "IDEstadoSaludNuevo" , "","[ Seleccione el Estado ]"  , "form-control mandatory" ,"",  " AND IDEstadoSalud <= 2 OR IDClub = '".SIMUser::get("club")."'" )
			?>
	    </div>
            <div class="form-group">
                        <input type="textarea" id="Observacion" class="form-control validate" placeholder="Observaciones">
	    <input type="hidden" id="IDSocioEdo" value="">
	    <input type="hidden" id="rowIdEDO" value="">
            </div>

              <button type="submit" class="btn btn-default btn-block" id="submitObservacion"><span class="glyphicon glyphicon-save"></span> Guardar Datos !</button>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal">
	    <span class="glyphicon glyphicon-remove"></span> Cancelar</button>

        </div>
      </div>

    </div>
  </div>

							    </div>
						<!-- FIN TAB Estado -->
							    <div id="tabPerfil" class="tab-pane fade">
								      <div class="row"><div class="col-md-12">
									    <div id="canvas-holder" style="width:95%">
											<?php for($i=1;$i <= count($array_data);$i++){
											?>
												<canvas id="chart-area_<?php echo $i; ?>" /></canvas>
											<?php } ?>
									    </div>

								    </div></div>
							    </div>

						 <div id="tabCierre" class="tab-pane fade">
								      <div class="row"><div class="col-md-12">

							    <table class="table table-bordered">
							        <tr>
							           <td style="width: 310px;">

									  <div class="input-group input-group-sm">
				    							      <input type="text" name="FindInput" placeholder="Nombre Apellido" class="typeahead" id="FindInput" style="min-width: 280px;" autocomplete="off" value="" >
									 </div>
								   </td> <td>
												<button type="button" id="btresetBusqueda" class="btn btn-purple btn-sm" disabled>
													    <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
														Nueva Busqueda
													</button>
												<button type="button" class="btn btn-primary btn-sm" id="btExportaCierre" disabled>
													    <span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
													    Exportar
												</button>



										    <form action="/plataform/procedures/excel-cierreEpidemiologico.php" id="frmExportCierre" method="post">
												<input type="hidden" id="IDSocio" name="IDSocio" value="" >
										    </form>

							           </td>
							        </tr>
							    </table>
									    <!-- INICIO TABS CIERRE EPIDEMIOLOGICO -->

									    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
										    <li class="nav-item">
										      <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">
				    				    				<i class="orange ace-icon fa fa-user bigger-120"></i>

												Perfil</a>
										    </li>
										    <li class="nav-item">
										      <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">
							    				<i class="orange ace-icon fa fa-users bigger-120"></i>

												Contacto Estrecho</a>
										    </li>
										    <li class="nav-item">
										      <a class="nav-link " id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="false">
				    			    				<i class="orange ace-icon fa fa-check-square-o bigger-120"></i>

												Seguimiento</a>
										    </li>
										      <li class="nav-item">
										      <a class="nav-link " id="pills-diagnosticos-tab" data-toggle="pill" href="#pills-diagnosticos" role="tab" aria-controls="pills-diagnosticos" aria-selected="false">
				    			    				<i class="orange ace-icon fa fa-check-square-o bigger-120"></i>

												Diagn&oacute;stico</a>
										    </li>
										  </ul>
										  <div class="tab-content" id="pills-tabContent">
										      <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
												<div class="row"><div class="col-md-12"><table id="grid_perfilCierre"></table></div></div>
										    </div>
										    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
												<table id="grid_contactoCierre"></table>
										    </div>
										    <div class="tab-pane fade" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
												<table id="grid_edoSaludCierre"></table>

										    </div>
										     <div class="tab-pane fade" id="pills-diagnosticos" role="tabpanel" aria-labelledby="pills-diagnosticos-tab">
												<table id="grid_diagnosticosCierre"></table>
										    </div>

										  </div>


									    <!-- FINNTABS CIERRE EPIDEMIOLOGICO -->

								    </div></div>
							    </div>
						 </div>
				    </div>
			</div>


	         </div>
    </div>


<?
			include( "cmp/footer_grid_chart.php" );
?>

 <script type="text/javascript">

	    	$( ".calendar" ).datepicker( {
		format: "yyyy-mm-dd",
			} );

	    jQuery(function($) {
			//var '#grid-table' = "#grid-table";
			var pager_selector = "#grid-pager";
			//var '#grid-table_sindagnostico' = "#grid-table_sindagnostico";
			var pager_selector2 = "#grid-pager_sindagnostico";



			$('#btBuscarDiagnostico').on('click', function (e) {

				var qryString = $("#qryString").val();
				var DIA = $("#DIA").val();
				var IDES = $("#IDEstadoSalud").val();

				$('#grid-table').jqGrid('setGridParam', {
					url:'includes/async/registroDiagnostico.async.php?qryString='+ qryString +'&DIA='+DIA+'&IDES='+IDES+'&week=2',
					page:1,
					datatype: "json"
				}).trigger("reloadGrid");

				return false;
			});

			$('#btBuscarDiagnosticoEdo').on('click', function (e) {

				var qryString = $("#qryStringEdo").val();
				//var DIA = $("#DIA_EDO").val();
				var IDES = $("#IDEstadoSaludEdo").val();

				$('#grid-tableEdo').jqGrid('setGridParam', {
				//	url:'includes/async/registroDiagnostico.async.php?qryString='+ qryString +'&IDES='+IDES,
				    url:'includes/async/getUsuario.async.php?qryString='+ qryString +'&IDES='+IDES,
					page:1,
					datatype: "json"
				}).trigger("reloadGrid");

				return false;
			});

			$("#btExportaDiagnostico").click(function(){
				    var qryString = $("#qryString").val();
				    var DIA = $("#DIA").val();
				    var IDES = $("#IDEstadoSalud").val();

				    window.location.href="./procedures/excel-Autodiagnostico.php?qryString="+ qryString +'&DIA='+DIA+'&IDES='+IDES;

			    });

			$("#btExportaSINAUTO").click(function(){
				    var qryString = $("#qryStringSIN").val();
				    var DIA = $("#DIA_SIN").val();

				    window.location.href="./procedures/excel-SinAutodiagnosticoPerfil.php?qryString="+ qryString +'&DIA='+DIA+'&SINAUTO=true';

			    });

			$("#btExportaEstado").click(function(){
				    var qryString = $("#qryStringEdo").val();
				 //   var DIA = $("#DIA").val();
				    var IDES = $("#IDEstadoSaludEdo").val();

				    window.location.href="./procedures/excel-regDiagnostico.php?qryString="+ qryString + '&IDES='+IDES+'&week=2';

			    });

			$("#btExportaCierre").click(function(){
				    $( "#frmExportCierre").submit();
			});


			$('#btSINDiagnostico').on('click', function (e) {

				    var qryString = $("#qryStringSIN").val();
				    var DIA = $("#DIA_SIN").val();

				    $('#grid-table_sindagnostico').jqGrid('setGridParam', {
				        url:'includes/async/registroDiagnosticoSIN.async.php?oper=search&qryString='+ qryString +'&DIA='+DIA,
				    	    datatype: "json"
				    }).trigger("reloadGrid");

				    return false;
			});

			$('#btresetBusqueda').on('click', function (e) {

				    $("#FindInput").val('');
				    $("#btresetBusqueda").attr("disabled", true);
				    $("#btExportaCierre").attr("disabled", true);
				    $("#FindInput").attr("disabled", false);

				  //  $("#IDSocio").val('');
				    $("#grid_edoSaludCierre").jqGrid("GridUnload");
				    $("#grid_perfilCierre").jqGrid("GridUnload");
				    $("#grid_contactoCierre").jqGrid("GridUnload");
				    $("#grid_diagnosticosCierre").jqGrid("GridUnload");


			 });


			$('#btTodos').on('click', function (e) {

				$('#grid-table').jqGrid('setGridParam', {
					url:'includes/async/registroDiagnostico.async.php?week=2',

					datatype: "json"
				}).trigger("reloadGrid");

				$('#qryString').val('');
				$('#DIA').val('');
				$('#IDEstadoSalud').val('');


			});
			$('#btTodosSIN').on('click', function (e) {

				$('#grid-table_sindagnostico').jqGrid('setGridParam', {
					url:'includes/async/registroDiagnosticoSIN.async.php',

					datatype: "json"
				}).trigger("reloadGrid");

				$('#qryStringSIN').val('');
				$('#DIA_SIN').val('');
			});
			$('#btTodosEdo').on('click', function (e) {

				$('#grid-tableEdo').jqGrid('setGridParam', {
					url:'includes/async/registroDiagnostico.async.php?week=2',

					datatype: "json"
				}).trigger("reloadGrid");

				$('#qryStringEdo').val('');
				$('#IDEstadoSaludEdo').val('');

			});

			$('#myTABS a[href="#tabSindiagnostico"]').on('click', function (e) {
				jQuery('#grid-table_sindagnostico').jqGrid({
					url:'includes/async/registroDiagnosticoSIN.async.php',
					datatype: "json",
					colNames:['Usuario','Estado','Celular' ],
					colModel:[
						{name:'Usuario',index:'Nombre', align:"left",width:'440',sortable:false},
						{name:'Estado',index:'Estado', align:"left",width:'200',sortable:false},
						{name:'Celular',index:'Celular', align:"left",width:'120',sortable:false},

					],
					rowNum:40,
					rowList:[20,40,100],
					sortname: 'Nombre',
					viewrecords: true,
					sortorder: "ASC",
					caption:"",
					height: "100%",
					width:800,
					pager : pager_selector2,
					altRows: true,
					//toppager: true,
					multiselect: false,
					//multikey: "ctrlKey",
					multiboxonly: true,
					loadComplete : function() {
						var table = this;
						setTimeout(function(){
						//	styleCheckbox(table);

						//	updateActionIcons(table);
						//	updatePagerIcons(table);
						//	enableTooltips(table);
						}, 0);

					//	preparaform();
					},

					    subGrid: true,
					     subGridOptions: {
						plusicon: "green  fa fa-plus-square-o",
						minusicon: "green fa fa-minus-square-o",
						openicon : "green fa fa-folder-open-o"
					    },
					    subGridRowExpanded: function(subgrid_id, row_id) {
					       var subgrid_table_id;
					       subgrid_table_id = subgrid_id+"_t";
					       jQuery("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table>");
					       jQuery("#"+subgrid_table_id).jqGrid({
						  url:"includes/async/get_perfilUsuario.async.php?id="+row_id,
						  datatype: "json",
						  colNames: ['Perfil','Respuesta'],
						  colModel: [
						    {name:"Pregunta",index:"Pregunta",width:420},
						    {name:"Respuesta",index:"Respuesta",width:200},
						  ],
						  height: '100%',
						  rowNum:20,
						  sortname: 'Orden',
						  sortorder: "asc"
					       });
					   }
				});
			});


	    	$('#myTABS a[href="#tabDiagnostico"]').on('click', function (e) {
			      jQuery('#grid-table').jqGrid({
					url:'includes/async/registroDiagnostico.async.php?week=2',
					datatype: "json",
					colNames:['Usuario','Nombre Diagnostico', 'Fecha','Celular' ],
					colModel:[
						{name:'Usuario',index:'Nombre', align:"left",width:'360',sortable:false},
						{name:'NomDiag',index:'NomDiag', align:"left",width:'180',sortable:false},
						{name:'DIA',index:'DIA', align:"left",width:'120',sortable:false},
						{name:'Celular',index:'Celular', align:"left",width:'120',sortable:false}
					],
					rowNum:40,
					rowList:[20,40,100],
					sortname: 'DIA',
					viewrecords: true,
					sortorder: "ASC",
					caption:"",
					height: "100%",
					pager : "#grid-pager",
					altRows: true,
					//toppager: true,
					multiselect: false,
					loadComplete : function() {
						var table = this;
						setTimeout(function(){
							updatePagerIcons(table);
						}, 0);
					},
					    subGrid: true,
					     subGridOptions: {
						plusicon: "green  fa fa-plus-square-o",
						minusicon: "green fa fa-minus-square-o",
						openicon : "green fa fa-folder-open-o"
					    },
					    subGridRowExpanded: function(subgrid_id, row_id) {

					       var subgrid_table_id;
					       subgrid_table_id = subgrid_id+"_t";
					       RowData = $(this).jqGrid("getRowData", row_id);

					       jQuery("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table>");
					       jQuery("#"+subgrid_table_id).jqGrid({
						  url:"includes/async/get_diagnosticoUsuario.async.php?id="+row_id,
						  datatype: "json",
						  colNames: ['Pregunta','Respuesta'],
						  colModel: [
						    {name:"Pregunta",index:"Pregunta",width:420},
						    {name:"Respuesta",index:"Respuesta",width:200}
						  ],
						  height: '100%',
						  rowNum:20,
						  sortname: 'Orden',
						  sortorder: "asc"
					       });
					   }

				});
			});

		$('#myTABS a[href="#tabEstado"]').on('click', function (e) {
			      jQuery('#grid-tableEdo').jqGrid({
					url:'includes/async/getUsuario.async.php',
					datatype: "json",
					colNames:['Usuario','Estado Salud','Celular','' ],
					colModel:[
						{name:'Nombre',index:'Nombre', align:"left",width:'360',sortable:false},
						{name:'EstadoSalud',index:'EstadoSalud', align:"left",width:'120',sortable:false},
						{name:'Celular',index:'Celular', align:"left",width:'120',sortable:false},
						{name:'',index:'', align:"left",width:'90',formatter:buttonFunction,sortable:false}
					],
					rowNum:40,
					rowList:[20,40,100],
					sortname: 'Nombre',
					viewrecords: true,
					sortorder: "ASC",
					caption:"",
					height: "100%",
					//width:800,
					pager : "grid-pagerEdo",
					altRows: true,
					//toppager: true,
					multiselect: false,
					loadComplete : function() {
						var table = this;
						setTimeout(function(){
							updatePagerIcons(table);
						}, 0);
					},
					    subGrid: true,
					     subGridOptions: {
						plusicon: "green  fa fa-plus-square-o",
						minusicon: "green fa fa-minus-square-o",
						openicon : "green fa fa-folder-open-o"
					    },
					    subGridRowExpanded: function(subgrid_id, row_id) {

					       var subgrid_table_id;
					       subgrid_table_id = subgrid_id+"_t";
					       RowData = $(this).jqGrid("getRowData", row_id);
					//	 pager_id = "p_"+subgrid_table_id;

					       jQuery("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table>");
					       // <div id='"+ pager_id +"' class='scroll'></div>
					       jQuery("#"+subgrid_table_id).jqGrid({
						  url:"includes/async/seguimientoUsuarioCRUD.async.php?IDSocio="+row_id,
						  datatype: "json",
						  colNames:['Estado','Fecha','Observaci&oacute;n' ],
						  colModel: [
							    {name:'Estado',index:'Estado', align:"left",width:'140',sortable:false},
							    {name:'Fecha',index:'Fecha', align:"left",width:'100',sortable:false, editable: true},
							    {name:'Observacion',index:'Observacion', align:"left",width:'240',sortable:false}
						  ],
						  height: '100%',
						  rowNum:20,
						  sortname: 'Fecha',
						  sortorder: "asc",
						//  pager:pager_id
					       });
					//	jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:true,del:false,search:false})


					   }

				});



			});

				$('#grid-tableEdo').on('click', "a.addEstado", function() {
					var idSocio = $(this).attr("rel");
					var Nombre = $(this).attr("nomUsuario");
					var rowId = $(this).attr("rowId");
				//	console.log(idSubGrid);
					$('#IDSocioEdo').val(idSocio);
				        $('#rowIdEDO').val(rowId);
					$('#nombreUsuarioEdo').val(Nombre);

				     $("#grid-tableEdo").jqGrid ('collapseSubGridRow', rowId);

					//   var gr = jQuery('#'+idSubGrid).jqGrid('editGridRow',"new",{height:280,reloadAfterSubmit:false});
					$('#myModal').modal('toggle');
					//    $('#myModal').modal('show');

				});


				$('#submitObservacion').on('click',function(){

	       if($("#IDEstadoSaludNuevo").val() == '' || $("#Observacion").val() == ''){
		//SIMHTML::jsAlert("Error debe seleccionar el Estado e ingresar una observaci�n !");
		n = noty({
			text: "<br><br>Error debe seleccionar el Estado e ingresar una observaci&oacute;n !<br><br>",
			type: 'warning',
			dismissQueue: true,
			layout: "topCenter",
			theme: 'defaultTheme',
			modal: true,
			timeout: 1500,
			closeWith: ['button'],
			buttons: false,
			animation: {
			open: {height: 'toggle'},
			close: {height: 'toggle'},
			easing: 'swing',
			speed: 500 // opening & closing animation speed
			}

			});
		 return false;
	       }


		  $.ajax({
		       url:'includes/async/seguimientoUsuarioCRUD.async.php',
		       method:"POST",
		       data: 'oper=insert&IDSS='+ $('#IDEstadoSaludNuevo').val()+'&Observacion='+ $('#Observacion').val()+'&IDSocio='+ $('#IDSocioEdo').val(),
		       type:'json',
		       async: true,
		       success:function(data)
		       {
			   //var result = eval('('+data+')');
			 //  var result = eval("var json='"+data+"';");
			   //result.sucess
			   if(data.sucess){

			 //   i=1;


			        $('#myModal').modal('hide');

			       n = noty({
					text: "<br><br>"+data.msg+" !<br><br>",
					type: 'sucess',
					dismissQueue: true,
					layout: "topCenter",
					theme: 'defaultTheme',
					modal: true,
					timeout: 1500,
					closeWith: ['button'],
					buttons: false,
					animation: {
					open: {height: 'toggle'},
					close: {height: 'toggle'},
					easing: 'swing',
					speed: 500
					}

				});


			   }else{
			   }
		       }
		  });
		return false;
	     });


			    $('#myModal').on("hide.bs.modal", function() {

				       idSubGridEdo = 'grid-tableEdo_'+$('#IDSocioEdo').val()+'_t';

				    $('#nombreUsuarioEdo').val('');
				    $('#IDEstadoSaludNuevo').val('');
				    $('#Observacion').val('');
				    $('#IDSocioEdo').val('');



				//    console.log('close noty');
				    $("#grid-tableEdo").jqGrid ('expandSubGridRow', $('#rowIdEDO').val());
				     $('#rowIdEDO').val('');

			      })

			     function buttonFunction(cellvalue, options, rowObject)
				    {
				       return "<a class='addEstado' rel='"+rowObject.IDSocio+"' nomUsuario='"+rowObject.Nombre+"' rowId='"+options.rowId+"' href='#'><i class='ace-icon fa fa-plus-square bigger-130'/></a>";
				    }


				//resize to fit page size
				$(window).on('resize.jqGrid', function () {
					$('#grid-table').jqGrid( 'setGridWidth', $(".page-content").width() );
				});
				$(window).on('resize.jqGrid', function () {
					$('#grid-table_sindagnostico').jqGrid( 'setGridWidth', $(".page-content").width() );
				});

				//resize on sidebar collapse/expand
				var parent_column = $('#grid-table').closest('[class*="col-"]');

				var parent_column = $('#grid-table_sindagnostico').closest('[class*="col-"]');

				$(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
					if( event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed' ) {
						//setTimeout is for webkit only to give time for DOM changes and then redraw!!!
						setTimeout(function() {
							$('#grid-table').jqGrid( 'setGridWidth', parent_column.width() );
						}, 0);
					}
				})


				var datePick = function(elem)
				{
				   jQuery(elem).datepicker();
				}

				$(window).triggerHandler('resize.jqGrid');//trigger window resize to make the grid get the correct size

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


				$(document).one('ajaxloadstart.page', function(e) {
					$('#grid-table').jqGrid('GridUnload');
					$('.ui-jqdialog').remove();
				});


	    var suggestionEngine = new Bloodhound({
						 datumTokenizer: function(d) {
							     console.log("token");
								  console.log(d);
						// return [d.value];
						  return Bloodhound.tokenizers.whitespace(d.value);
						},
						queryTokenizer: Bloodhound.tokenizers.whitespace,
						remote: {
						  //url: '//api.swiftype.com/api/v1/public/engines/search?q=',
							  url:'includes/async/getUsuario.async.php?qryString=',

						  replace: function(url, query) {
						    return url + query;// + "&engine_key=VdyvTho_MpwQbhzSC6kD";
						  },
						identify: function(obj) { return obj.id; },
						filter: function(data) {
							var results;
								    if(data.records > 0)
											 results = $.map(data.rows, function(dataItem) {
												    return { value: dataItem.cell.Nombre,
														id: dataItem.cell.IDSocio };
												});
						      results = suggestionEngine.sorter(results);
						      console.log(results);
						  return results;
						},

						//ajax: {
						//  type: "GET",
						//  data: {
						//    q: function() { return $('.typeahead').val() }
						//  }
						//}
				    },
				    sorter: function(a, b) {
			var input_string = $('#FindInput').val();
			return Levenshtein.get(a.value, input_string) - Levenshtein.get(b.value, input_string);
		    }

	    });

	suggestionEngine.initialize();

	    $('.typeahead').typeahead({minLength: 3,
			highlight: true,
		       // autoSelect : false,
		        dropdownFilter: "all",
			showHintOnFocus : "all"
			}, {
			// name: 'rows',
	    //	    display: 'value',
			source: suggestionEngine,
			display: function(item){
				  //  	console.log('display');
					//    console.log(item);
					    return item.value;
				    },
			limit: 15,
			templates: {
			    suggestion: function(item) {
				return '<div>'+ item.value +'</div>';
			    }
			},
		     //   afterSekect : function(item){ alert(iten.value);},
	});

	    $('.typeahead').on('typeahead:selected', function (e, item) {


		$("#btresetBusqueda").attr("disabled", false);
		$("#btExportaCierre").attr("disabled", false);

	      $("#FindInput").attr("disabled", true);


		$('#pills-tab a[href="#pills-profile"]').tab('show');


		//	 $('#pills-contact-tab').tab('show');
		//console.log(item.id);
		//console.log(' ids ' + $("#IDSocio").val());

		if(item.id != ''){
			$("#IDSocio").val(item.id);

			jQuery("#grid_edoSaludCierre").jqGrid({
					url:'includes/async/seguimientoUsuarioCRUD.async.php?IDSocio='+ item.id,
					datatype: "json",
					colNames:['Estado','Fecha','Observaci&oacute;n' ],
					colModel:[
						{name:'Estado',index:'Estado', align:"left",width:'80'},
						{name:'Fecha',index:'Fecha', align:"left",width:'60'},
						{name:'Observacion',index:'Observacion', align:"left",width:'240'},
					],
					rowNum:100,
					rowList:[20,40,100],
					sortname: 'IDSocioSeguimiento',
					viewrecords: true,
					sortorder: "ASC",
					caption:"",
					height: "100%",
					width:855,
					altRows: true,
					multiselect: false,
					//multikey: "ctrlKey",
					multiboxonly: true,
					loadComplete : function() {
						var table = this;

					}
				});

		  	jQuery($("#grid_perfilCierre")).jqGrid({
					  url:"includes/async/get_perfilUsuario.async.php?id="+item.id,
						  datatype: "json",
						  colNames: ['Pregunta','Respuesta'],
						  colModel: [
						    {name:"Pregunta",index:"Pregunta",width:420},
						    {name:"Respuesta",index:"Respuesta",width:200},
						  ],
						  height: '100%',
						  rowNum:40,
						  sortname: 'Orden',
						  sortorder: "asc",
					caption:"",
					//width:800,
					altRows: true,
					multiselect: false,
					//multikey: "ctrlKey",
					multiboxonly: true,
					loadComplete : function() {
						var table = this;
					}
				});
				    jQuery("#grid_contactoCierre").jqGrid({
					url:'includes/async/get_registroContacto.async.php?id='+ item.id,
					datatype: "json",
					colNames:['Lugar','Fecha','NombreExterno','Variable','Tipo','Campo','Valor' ],
					colModel:[
						{name:'Lugar',index:'Lugar', align:"left",width:'160'},
						{name:'Fecha',index:'Fecha', align:"left",width:'140'},
						{name:'NombreExterno',index:'NombreExterno', align:"left",width:'200'},
						{name:'Variable',index:'Variable', align:"left",width:'110'},
						{name:'Valor_Variable',index:'Valor_Variable', align:"left",width:'120'},
						{name:'Campo',index:'Campo', align:"left",width:'120'},
						{name:'Valor',index:'Observacion', align:"left",width:'120'},
					],
					rowNum:100,
					rowList:[20,40,100],
					sortname: 'Fecha',
					viewrecords: true,
					sortorder: "ASC",
					caption:"",
					height: "100%",
					//width:855,
					altRows: true,
					multiselect: false,
					//multikey: "ctrlKey",
					multiboxonly: true,
					loadComplete : function() {
						var table = this;

					}
				});

			jQuery('#grid_diagnosticosCierre').jqGrid({
					url:'includes/async/registroDiagnostico.async.php?id='+ item.id+'&week=2',
					datatype: "json",
					colNames:['Usuario','Nombre Diagnostico', 'Fecha' ],
					colModel:[
						{name:'Usuario',index:'Nombre', align:"left",width:'360'},
						{name:'NomDiag',index:'NomDiag', align:"left",width:'180'},
						{name:'DIA',index:'DIA', align:"left",width:'120'},
					],
					rowNum:40,
					rowList:[20,40,100],
					sortname: 'DIA',
					viewrecords: true,
					sortorder: "ASC",
					caption:"",
					height: "100%",
					//pager : "#grid-pager",
					altRows: true,
					//toppager: true,
					multiselect: false,
				//	loadComplete : function() {
				//		var table = this;
				//		setTimeout(function(){
				//			updatePagerIcons(table);
				//		}, 0);
				//	},
					    subGrid: true,
					     subGridOptions: {
						plusicon: "green  fa fa-plus-square-o",
						minusicon: "green fa fa-minus-square-o",
						openicon : "green fa fa-folder-open-o"
					    },
					    subGridRowExpanded: function(subgrid_id, row_id) {

					       var subgrid_table_id;
					       subgrid_table_id = subgrid_id+"_t";
					       RowData = $(this).jqGrid("getRowData", row_id);

					       jQuery("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table>");
					       jQuery("#"+subgrid_table_id).jqGrid({
						  url:"includes/async/get_diagnosticoUsuario.async.php?id="+row_id,
						  datatype: "json",
						  colNames: ['Pregunta','Respuesta'],
						  colModel: [
						    {name:"Pregunta",index:"Pregunta",width:420},
						    {name:"Respuesta",index:"Respuesta",width:200}
						  ],
						  height: '100%',
						  rowNum:20,
						  sortname: 'Orden',
						  sortorder: "asc"
					       });
					   }

				});


	     }



	    });


}); // End jquery


 	//function labelFormatter(label, series) {
//		return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
//	}

//	    var randomScalingFactor = function() {
//		return Math.round(Math.random() * 100);
//	    };

	    var randomColorFactor = function() {
		return Math.round(Math.random() * 255);
	    };
        var randomColor = function() {
            return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',.7)';
        };

        var barChartData = {

            labels: [<?php echo "'" . str_replace(",", "','", implode(",",$array_dataTotalDiagnosticos["DIA"])) ."'"; ?>],

            datasets: [
				<?php
					$array_datos[] = "{
						label: 'Total x Dia',
						backgroundColor: randomColor(),
						data: ['" . str_replace(",", "','", implode(",",$array_dataTotalDiagnosticos["Total"])) ."']
					}";
				?>

				<?php echo implode(",",$array_datos); ?>

			]

        };

<?php $i=1; foreach($array_data AS $data_pregunta){


	    echo 'var config_'.($i++).' = {
        type: "pie",
        data: {
            datasets: [{
			data: [\'' . str_replace(",", "','", implode(",",$data_pregunta["TotalxPregunta"])) .'\'],
				backgroundColor: [
					randomColor(),
					randomColor(),
					randomColor(),
					randomColor(),
					randomColor(),randomColor(),
					randomColor()
				],
				hoverBackgroundColor: [
					randomColor(),
					randomColor(),
					randomColor(),
					randomColor(),
					randomColor()
				]
        }],
            labels: [\'' . str_replace(",", "','", implode(",",$data_pregunta["Opcion"])) .'\']
        },
        options: {
            responsive: true,
			title: {
                        display: true,
                        text: "'.$data_pregunta["Pregunta"].'",
			fontSize: 17
                    }
        }
    };
';

	    } // foreach
?>

        window.onload = function() {

            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    elements: {
                        rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Total Diagnosticos Ultimas Dos Semanas',
			fontSize: 20
                    },
	    animation: {
				duration: 0,
	    		easing: "easeOutQuart",
			onComplete: function () {
						var ctx = this.chart.ctx;
	        				ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
	    					ctx.textAlign = 'center';
	    					ctx.textBaseline = 'bottom';
	    					this.data.datasets.forEach(function (dataset) {
				    		for (var i = 0; i < dataset.data.length; i++) {
							var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model, scale_max = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._yScale.maxHeight;
							ctx.fillStyle = '#444';
							var y_pos = model.y - 5;
				    			if ((scale_max - model.y) / scale_max >= 0.93) y_pos = model.y + 20; ctx.fillText(dataset.data[i], model.x, y_pos);
						}
						});
						}
			}

                }
            });

	 <?php

//	 if($tipoReporte != "Funcionario")
		for($i=1;$i <= count($array_data);$i++){

		   echo 	"var ctx_categoria_$i = document.getElementById(\"chart-area_$i\").getContext(\"2d\");
			   window.myPie = new Chart(ctx_categoria_$i, config_$i);";

		} // end for
	 ?>
        };


    </script>
