      <div id="ServicioAdicional">
	<form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
      <table id="simple-table" class="table table-striped table-bordered table-hover">
      	<tr>
      		<td>


		<table id="simple-table" class="table table-striped table-bordered table-hover">
        	<tr>
            	<td valign="top">


                  <?php
                  $action = "InsertarServicioAdicional";

                  if( $_GET[IDServicioAdicional] )
                  {
                    $EditServicioAdicional =$dbo->fetchAll("ServicioAdicional"," IDServicioAdicional = '".$_GET[IDServicioAdicional]."' ","array");
                    $action = "ModificaServicioAdicional";
                    ?>
                    <input type="hidden" name="IDServicioAdicional" id="IDServicioAdicional" value="<?php echo $EditServicioAdicional[IDServicioAdicional]?>" />
                    <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">

                    <tr>
                      <td width="26%">Categoria </td>
                      <td width="74%">
                        <select name="IDServicioPropiedad" id="IDServicioPropiedad" class="input mandatory" required>
                          <option value="">Seleccione</option>
                          <?php $sql="SELECT IDServicioPropiedad, Nombre
                                      FROM ServicioPropiedad
                                      WHERE IDServicio = '".$_GET["ids"]."'";
                                $r_prop=$dbo->query($sql);
                                while ($row_prop = $dbo->fetchArray($r_prop)) { ?>
                                  <option value="<?php echo $row_prop["IDServicioPropiedad"] ?>" <?php if($row_prop["IDServicioPropiedad"]==$EditServicioAdicional["IDServicioPropiedad"]) echo "selected"; ?> ><?php echo $row_prop["Nombre"] ?></option>
                                <?php }  ?>
                        </select>
                      </td>
                  </tr>

                  <tr>
                    <td width="26%">Nombre </td>
                    <td width="74%">
                      <input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditServicioAdicional["Nombre"] ?>" />
                    </td>
                </tr>

                <tr>
                  <td width="26%">Valor </td>
                  <td width="74%">
                    <input id="Valor" type="number" size="25" title="Valor" name="Valor" class="input" value="<?php echo $EditServicioAdicional["Valor"] ?>" />
                  </td>
              </tr>

              <tr>
                <td width="26%">Stock </td>
                <td width="74%">
                  <input id="Stock" type="number" size="25" title="Stock" name="Stock" class="input" value="<?php echo $EditServicioAdicional["Stock"] ?>" />
                </td>
              </tr>
              <tr>
                <td width="26%">Validar Edad socio Adicional </td>
                <td><? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $EditServicioAdicional["ValidarEdad"] , 'ValidarEdad' , "class='input'" ) ?></td>
              </tr>
              <tr>
                <td width="26%">Edad Minima </td>
                <td width="74%">
                  <input id="EdadMinima" type="number" size="25" title="EdadMinima" name="EdadMinima" class="input" value="<?php echo $EditServicioAdicional["EdadMinima"] ?>" />
                </td>
              </tr>
              <tr>
                <td width="26%">Edad Maxima </td>
                <td width="74%">
                  <input id="EdadMaxima" type="number" size="25" title="EdadMaxima" name="EdadMaxima" class="input" value="<?php echo $EditServicioAdicional["EdadMaxima"] ?>" />
                </td>
              </tr>

              <tr>
                <td>Publicar</td>
                <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditServicioAdicional["Publicar"] , 'Publicar' , "class='input'" ) ?></td>
              </tr>
                  </table>
                  <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
                  <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm["IDClub"]?>" />

                </td>
                <td valign="top">


              </td>
            </tr>
        </table>

        </td>
        </tr>
        <tr>
        	<td align="center"><input type="submit" class="submit" value="Agregar"></td>
        </tr>

        </table>
</form>


              <br />
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                          <input id="fecha-disponibilidad" type="date">
                        </td>                
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Nombre</th>
                              <th>Categoria</th>
                              <th>Valor</th>
                              <th>Stock</th>
                              <th>Disponibles</th>
                              <th>Publicar</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $sql_carac="SELECT SA.*,SP.Nombre as Categoria
                                          FROM ServicioAdicional SA, ServicioPropiedad SP
                                          WHERE SA.IDServicioPropiedad = SP.IDServicioPropiedad
                                          And SA.IDServicio = '" . $frm[$key]  ."'";
                              $r_carac=$dbo->query($sql_carac);
                              $servicosAdicional = [];
                              while( $r = $dbo->object( $r_carac ) )
                              {
                                $servicosAdicional[$r->IDServicioAdicional] = $r->Stock;

                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&ids=" . $frm[$key] ."&IDServicioAdicional=".$r->IDServicioAdicional?>&tab=adicionales" class="ace-icon glyphicon glyphicon-pencil"></a></td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php echo $r->Categoria; ?></td>
                              <td><?php echo $r->Valor; ?></td>
                              <td><?php echo $r->Stock; ?></td>
                              <td id="td-servicioadicional-<?php echo $r->IDServicioAdicional?>"></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioAdicional&ids=<?php echo $_GET[ids];?>&IDServicioAdicional=<? echo $r->IDServicioAdicional ?>&tab=adicionales" ></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="15"></th>
                      </tr>
              </table>

</div>
<script> 
  serviciosAdicional = [];
  <?php
    foreach($servicosAdicional as $key => $value){?>
      serviciosAdicional["<?php echo $key?>"] = <?php echo $value?>;
    <?php
    }
  ?>
  $(document).ready(function(){
  $("#fecha-disponibilidad").change(function() {
    
    serviciosAdicional.forEach((element, index ) => { 
      fechaDisponibilidad = $("#fecha-disponibilidad").val(); 
      if(fechaDisponibilidad != undefined)
      {
        console.log(fechaDisponibilidad)
        if(fechaDisponibilidad === ""){
          $("#td-servicioadicional-"+index).html("");
        }else {         
          $.ajax({
            url: "includes/async/serviciosclub.async.php?oper=calservicioadicionalocupacion&fecha="+fechaDisponibilidad+"&idservicioadicional="+index,
            success: function(result){				
              ocupacion = result["response"];
              $("#td-servicioadicional-"+index).html(element - ocupacion);				
            },
            error: function(error){
              console.log(error);
            }
          });
        }        
      }    
    });
  }); 
  });
</script>