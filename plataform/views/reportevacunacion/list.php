<?php
  $query = $dbo->query("SELECT * FROM VacunaMarca");
  $marcaVacunas = $dbo->fetch($query);
?>

<div class="widget-box transparent" id="recent-box">

  <div class="widget-body">
    <div class="widget-main padding-4">
          <div class="tabbable" id="myTABS" role="tablist">
        <ul class="nav nav-tabs" id="myTab" >
          <li>
                <a data-toggle="tab" href="#tabVacunados" role="tab">
                <i class="ace-icon fa fa-check-square-o green bigger-120" ></i>
                  Vacunados
                </a>
          </li>
          <li>
                <a data-toggle="tab" href="#tabCitaVacunacion" role="tab">
                <i class="ace-icon fa fa-calendar green bigger-120" ></i>
                  Citas vacunación
                </a>
          </li>
          <li>
                <a data-toggle="tab" href="#tabMarcaVacuna" role="tab">
                <i class="green ace-icon fa fa-flask green bigger-120"></i>
                  Marcas vacunas
              </a>
          </li>
          <li>
                <a data-toggle="tab" href="#tabEntidadVacuna" role="tab">
                <i class="green ace-icon fa fa-hospital-o bigger-120"></i>
                  Entidades vacunación
              </a>
          </li>
         </ul>

          <div class="tab-content">
          <!-- INICIO TAB VACUNADOS -->
          <div id="tabVacunados" class="tab-pane fade">
                <div class="row">
                <div class="col-md-8">
                <form class="form-horizontal" id="frmBuscarVacunado">

                 <table id="simple-table" class="table table-striped table-bordered table-hover">
                  <tr>
                    <td>
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>

                                        <select name = "IDTipoInvitado" id="IDTipoInvitado" title="Tipo Invitado" class="form-control mandatory">
                                        	<option value="">Tipo</option>
                                        <?php
										                    $sql_tipoinv_club = "Select * From TipoInvitado Where IDClub = '".SIMUser::get("club")."' and Publicar = 'S'";
										                    $qry_tipoinv_club = $dbo->query($sql_tipoinv_club);
										                    while ($r_tipoinv = $dbo->fetchArray($qry_tipoinv_club)): ?>
											                    <option value="<?php echo $r_tipoinv["IDTipoInvitado"]; ?>" <?php if($r_tipoinv["IDTipoInvitado"]==$frm["IDTipoInvitado"]) echo "selected";  ?>><?php echo $r_tipoinv["Nombre"]; ?></option>
                                        <?php
										 	                    endwhile;    ?>
                                        </select>
								
                    </td>

                   
                  
                   <td>
                   <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clasificacion </label>


                    <select name = "IDClasificacionInvitado" id="IDClasificacionInvitado" class="form-control">
                      <option value="">Clasificacion</option>
                        <?php
                        if(!empty($frm["IDTipoInvitado"])):
                          $sql_clasifinv_club = "Select * From ClasificacionInvitado Where IDTipoInvitado = '".$frm["IDTipoInvitado"]."'";
                          $qry_clasifinv_club = $dbo->query($sql_clasifinv_club);
                         while ($r_clasifinv = $dbo->fetchArray($qry_clasifinv_club)): ?>
                            <option value="<?php echo $r_clasifinv["IDClasificacionInvitado"]; ?>" <?php if($r_clasifinv["IDClasificacionInvitado"]==$frm["IDClasificacionInvitado"]) echo "selected";  ?>><?php echo $r_clasifinv["Nombre"]; ?></option>
                          <?php
                          endwhile;
                        endif;
                        ?>
                        </select>

                    </td>
                
                    <td>
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Vacunación </label>
                      <select name="VacunadosEstado" id="VacunadosEstado" class="form-control">
                          <option value="">[Seleccione estado]</option>
                          <option value="S">Vacunado</option>
                          <option value="N">No Vacunado</option>
                     </select>
                    </td>
                    <td>
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cedula </label>
                      <input name="NumeroDocumento" id="NumeroDocumento" class="form-control" placeholder="Cédula">                    
                    </td>
                    <td style="width:140px;">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-purple btn-sm" id="btBuscarVacunados" >
                          <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                            Buscar
                        </button>
                      </span>
                      <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-sm" id="btExportaVacunados">
                            <span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
                            Exportar
                            </button>
                      </span>
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-primary btn-sm" id="btVerTodosVacunados">
                            Ver Todos
                        </button>
                      </span-->
                    </td>
                  </tr>
                </table>

                </form>
                </div>
           </div>
          </div>
          <!-- FIN TAB VACUNADOS -->
          <!-- INICIO TAB CITAS VACUNACIÓN -->
              <div id="tabCitaVacunacion" class="tab-pane fade" role="tabpanel">
                <div class="row">
                <div class="col-md-8">
                <form class="form-horizontal" id="frmBuscarSINDiagnostico">
                 <table id="simple-table" class="table table-striped table-bordered table-hover">
                  <tr>
                    <td>
                       <input type="text" id="fecha_inicio" name="fecha_inicio" placeholder="Fecha Incio" class="col-xs-12 calendar" title="col-xs-12 calendar" value="" autocomplete="off">
                    </td>
                    <td>
                      <input type="text" id="fecha_fin" name="fecha_fin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="Fecha" value="" autocomplete="off">
                    </td>
                    <td>
                      <select name="CitaVacuna" id="CitaVacuna" class="form-control">
                          <option value="">[Seleccione cita vacuna]</option>
                          <option value="ninguna">Sin citas</option>
                          <option value="primera">Solo con primera cita</option>
                          <option value="segunda">Con segunda cita</option>
                          <option value="ambas">Con primera o segunda cita</option>
                     </select>
                    </td>
                    <td  align="center" style="width:220px;">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-purple btn-sm" id="btBuscarCitaVacunacion" >
                          <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                          Buscar
                        </button>
                      </span>
                      <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-sm" id="btExportaCitaVacunacion">
                            <span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
                            Exportar
                            </button>
                      </span>
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-primary btn-sm btVerTodos" id="btVerTodosCitaVacunacion">
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
          <!-- FIN TAB CITAS VACUNACIÓN -->
          <!-- INICIO TAB MARCAS VACUNAS -->
            <div id="tabMarcaVacuna" class="tab-pane fade">
              <div class="row">
              <div class="col-md-8">
              <form class="form-horizontal" id="frmBuscarVacunado">

               <table id="simple-table" class="table table-striped table-bordered table-hover" style="width:840px;">
                <tr>
                  <td>
                    <select name="IDVacunaMarca" id="IDVacunaMarca" class="form-control">
                      <option value="">[Seleccione Marca vacuna]</option>
                      <?php foreach($marcaVacunas as $value){ ?>
                          <option value="<?php echo $value["IDVacunaMarca"]?>"><?php echo $value["Nombre"]?></option>
                      <?php } ?>
                      </select>
                  </td>
                  <td  align="center" style="width:220px;">
                    <span class="input-group-btn">
                          <button type="button" class="btn btn-purple btn-sm btnEnviar" id="btBuscarMarcaVacuna">
                            <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                            Buscar
                          </button>
                        </span>
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-sm" id="btExportaMarcaVacuna">
                              <span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
                              Exportar
                        </button>
                        </span>
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-primary btn-sm btVerTodos" id="btVerTodosMarcaVacuna">
                            Ver Todos
                          </button>
                        </span>
                    </td>
                </tr>
              </table>

              </form>
              </div>
         </div>

        </div>

    <!-- FIN TABMARCASVACUNAS -->



<!-- INICIO TAB Entidad VACUNAS -->
  <div id="tabEntidadVacuna" class="tab-pane fade">
      <div class="row">
      <div class="col-md-8">
      <form class="form-horizontal" id="frmBuscarVacunado">

       <table id="simple-table" class="table table-striped table-bordered table-hover" style="width:840px;">
        <tr>
          <td>
             <input type="text" id="entidad" name="entidad" placeholder="Entidad que vacuna" class="col-xs-12" title="Entidad" value="">
          </td>
          <td  align="center" style="width:220px;">
            <span class="input-group-btn">
                  <button type="button" class="btn btn-purple btn-sm btnEnviar" id="btBuscarEntidadVacuna">
                    <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                    Buscar
                  </button>
                </span>
                <span class="input-group-btn">
                <button type="button" class="btn btn-info btn-sm" id="btExportaEntidadVacuna">
                      <span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
                      Exportar
                </button>
                </span>
                <span class="input-group-btn">
                  <button type="button" class="btn btn-primary btn-sm btVerTodos" id="btVerTodosEntidadVacuna">
                    Ver Todos
                  </button>
                </span>
            </td>
        </tr>
      </table>

      </form>
  </div>
  
  </div>
</div>
<!-- 
<ul class="nav nav-tabs" id="pills-tab" role="tablist">
	<li class="nav-item">
	  <a class="nav-link" id="pills-socio-tab" data-toggle="pill" href="#pills-socio" role="tab" aria-controls="pills-socio" aria-selected="false">
      <i class="orange ace-icon fa fa-users bigger-120"></i>
			Socios
    </a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="pills-usuario-tab" data-toggle="pill" href="#pills-usuario" role="tab" aria-controls="pills-usuario" aria-selected="false">
		  <i class="orange ace-icon fa fa-users bigger-120"></i>
      Usuarios
    </a>									
	</li>
	<li class="nav-item">
		<a class="nav-link" id="pills-empleado-tab" data-toggle="pill" href="#pills-empleado" role="tab" aria-controls="pills-empleado" aria-selected="false">
		  <i class="orange ace-icon fa fa-users bigger-120"></i>
      Empleado
    </a>									
	</li>
	<li class="nav-item">
		<a class="nav-link" id="pills-estudiante-tab" data-toggle="pill" href="#pills-estudiante" role="tab" aria-controls="pills-estudiante" aria-selected="false">
		  <i class="orange ace-icon fa fa-users bigger-120"></i>
      Estudiante
    </a>									
	</li>
</ul> -->




<div class="tab-content" id="pills-tabContent">
	<div class="tab-pane fade" id="pills-socio" role="tabpanel" aria-labelledby="pills-profile-tab"> </div>
	<div class="tab-pane fade" id="pills-usuario" role="tabpanel" aria-labelledby="pills-contact-tab"> </div>
  <div class="tab-pane fade" id="pills-empleado" role="tabpanel" aria-labelledby="pills-contact-tab"> </div>
  <div class="tab-pane fade" id="pills-estudiante" role="tabpanel" aria-labelledby="pills-contact-tab"> </div>					
</div>

<? include("views/reportevacunacion/vacunacion.php")?>


<!-- Fin Tab marca vacuna-->


<script type="text/javascript">
  function getVacunacionParmas(){
    let tipoInvitado = $("#IDTipoInvitado").val();
    let tipoClasificacionInvitado = $("#IDClasificacionInvitado").val();
    let estadoVacunados = $('#VacunadosEstado').val();
    let fechaInicio = $("#fecha_inicio").val();
    let fechaFin = $("#fecha_fin").val();
    let citaVacuna = $("#CitaVacuna").val();
    let idVacunaMarca = $("#IDVacunaMarca").val();
    let entidadVacuna = $("#entidad").val();
    let numeroDocumento = $("#NumeroDocumento").val();

    let params = (tipoInvitado==undefined || tipoInvitado=="")? "":"tipoInvitado="+tipoInvitado+"&"
    params += (tipoClasificacionInvitado==undefined || tipoClasificacionInvitado=="")? "":"tipoClasificacionInvitado="+tipoClasificacionInvitado+"&"
    params += (estadoVacunados==undefined || estadoVacunados=="")? "":"estadoVacunados="+estadoVacunados+"&";
    params += (fechaInicio==undefined || fechaInicio=="")? "":"fechaInicio="+fechaInicio+"&fechaFin="+fechaFin;
    params += (idVacunaMarca==undefined || idVacunaMarca=="")? "":"idVacunaMarca="+idVacunaMarca+"&";
    params += (citaVacuna==undefined || citaVacuna=="")? "":"citaVacuna="+citaVacuna+"&";
    params += (entidadVacuna==undefined || entidadVacuna=="")? "":"entidadVacuna="+entidadVacuna+"&";
    params += (numeroDocumento==undefined || numeroDocumento=="")? "":"numeroDocumento="+numeroDocumento;

    return params;
  }

  function emptyInputs(){
   
    $("#IDTipoInvitado").val("");
    $("#IDClasificacionInvitado").val("");
    $("#VacunadosTipo").val("");
    $('#VacunadosEstado').val("");
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");
    $("#CitaVacuna").val("");
    $("#IDVacunaMarca").val("");
    $("#entidad").val("");
    $("#NumeroDocumento").val("");
  }
  
  paramBusqueda = "&tipoVacunados=socio";
  moduloVacunado = "socios";

  //Botón exportar
  $("#btExportaVacunados").click(function(){

    let params = getVacunacionParmas();
    window.location.href="./procedures/excel-vacuna.php?"+params;

  });

  $("#btExportaCitaVacunacion").click(function(){

    let params = getVacunacionParmas();
    window.location.href="./procedures/excel-vacuna.php?"+params;

  });

  $("#btExportaMarcaVacuna").click(function(){

    let params = getVacunacionParmas();
    window.location.href="./procedures/excel-vacuna.php?"+params;

  });

  $("#btExportaEntidadVacuna").click(function(){

    let params = getVacunacionParmas();
    window.location.href="./procedures/excel-vacuna.php?"+params;
  
  });

  //Botón Buscar
  $("#btBuscarVacunados").click(function(){

    let params = getVacunacionParmas();
    params += paramBusqueda;
    jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php?<?=$url_search ?>'+params ,datatype:"json"}).trigger("reloadGrid");

  });

  $("#btBuscarCitaVacunacion").click(function(){

    let params = getVacunacionParmas();
    params += paramBusqueda;
    jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php?<?=$url_search ?>'+params ,datatype:"json"}).trigger("reloadGrid");

  });

  $("#btBuscarMarcaVacuna").click(function(){

    let params = getVacunacionParmas();
    params += paramBusqueda;
    jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php?<?=$url_search ?>'+params ,datatype:"json"}).trigger("reloadGrid");

  });

  $("#btBuscarEntidadVacuna").click(function(){

    let params = getVacunacionParmas();
    params += paramBusqueda;
    jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php?<?=$url_search ?>'+params ,datatype:"json"}).trigger("reloadGrid");

  });

  //Boton  ver todos
  $("#btVerTodosVacunados").click(function(){
    emptyInputs();
    params = paramBusqueda;
    jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php?<?=$url_search ?>'+params ,datatype:"json"}).trigger("reloadGrid");
  });

  $("#btVerTodosCitaVacunacion").click(function(){
    emptyInputs();
    params = paramBusqueda;
    jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php?<?=$url_search ?>'+params ,datatype:"json"}).trigger("reloadGrid");
  });

  $("#btVerTodosMarcaVacuna").click(function(){
    emptyInputs();
    params = paramBusqueda;
    jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php?<?=$url_search ?>'+params ,datatype:"json"}).trigger("reloadGrid");
  });

  $("#btVerTodosEntidadVacuna").click(function(){
    emptyInputs();
    params = paramBusqueda;
    jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php?<?=$url_search ?>'+params ,datatype:"json"}).trigger("reloadGrid");
  });

//Pestañas socio - usuario

  $("#pills-socio-tab").click(function(){
    moduloVacunado = "socios";
    paramBusqueda = "&tipoVacunados=socio";
    let params = getVacunacionParmas();
		jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php<?=$url_search ?>?tipoVacunados=socio&'+params ,datatype:"json"}).trigger("reloadGrid");
	});

	$("#pills-usuario-tab").click(function(){
    moduloVacunado = "usuarios";
    paramBusqueda = "&tipoVacunados=usuario";
    let params = getVacunacionParmas();
		jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php<?=$url_search ?>?tipoVacunados=usuario&'+params ,datatype:"json"}).trigger("reloadGrid");
});
$("#pills-empleado-tab").click(function(){
    moduloVacunado = "socios";
    paramBusqueda = "&tipoVacunados=empleado";
    let params = getVacunacionParmas();
		jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php<?=$url_search ?>?tipoVacunados=empleado&'+params ,datatype:"json"}).trigger("reloadGrid");
});
$("#pills-estudiante-tab").click(function(){
    moduloVacunado = "socios";
    paramBusqueda = "&tipoVacunados=estudiante";
    let params = getVacunacionParmas();
		jQuery("#grid-table").jqGrid('setGridParam', { url: 'includes/async/<?php echo $script; ?>.async.php<?=$url_search ?>?tipoVacunados=estudiante&'+params ,datatype:"json"}).trigger("reloadGrid");
});
	
</script>
