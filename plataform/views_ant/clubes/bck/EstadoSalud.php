<div id="estadosalud">
								
								<table style="margin-bottom: 10px;"><tr><td>
								<div class="form-group" >
									<form name="frmestadosalud" id="frmestadosalud" class="form-horizontal formvalida" role="form">
									<input type="hidden" name="IDEstadoSalud" id="IDEstadoSalud" value="">
									<input type="hidden" name="action" id="action" value="insert">

									<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
										<input type="text" name="NombreEstadoSalud" id="NombreEstadoSalud" placeholder="Estado"  value="" required='true'>

									</div>
				
									<div class="col-lg-6 col-md-4 col-sm-6 col-xs-12">
										<input type="text" name="DescripcionEstadoSalud" id="DescripcionEstadoSalud" placeholder="Descripci&oacute;n" style="width:220px" value="">
									</div>
									<div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">

										  <input type="button" name="submitEstadoSalud" id="submitEstadoSalud" class="btn btn-info" value="Crear Estado" />

									</div>
									</div>
									</form>
								</div>
							</td></tr></table>
								<div id="jqGrid_container">
									<table id="grid-table"></table>
									<div id="grid-pager"></div>
								</div>
							</div>
							