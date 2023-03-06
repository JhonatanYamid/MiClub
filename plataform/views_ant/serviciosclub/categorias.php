      <div id="ServicioPropiedad">
	<form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
      <table id="simple-table" class="table table-striped table-bordered table-hover">
      	<tr>
      		<td>


		<table id="simple-table" class="table table-striped table-bordered table-hover">
        	<tr>
            	<td valign="top">


                  <?php
                  $action = "InsertarServicioPropiedad";

                  if( $_GET[IDServicioPropiedad] )
                  {
                          $EditServicioPropiedad =$dbo->fetchAll("ServicioPropiedad"," IDServicioPropiedad = '".$_GET[IDServicioPropiedad]."' ","array");
                          $action = "ModificaServicioPropiedad";
                          ?>
                          <input type="hidden" name="IDServicioPropiedad" id="IDServicioPropiedad" value="<?php echo $EditServicioPropiedad[IDServicioPropiedad]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">


                  <tr>
                    <td width="26%">Nombre </td>
                    <td width="74%">
                      <input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditServicioPropiedad["Nombre"] ?>" />
                    </td>
                </tr>
                <tr>
                  <td>Tipo</td>
                  <td>
                  <select name="Tipo" id="Tipo" class="form-control" required>
                    <option value=""></option>
                    <option value="Radio" <?php if($EditServicioPropiedad["Tipo"]=="Radio") echo "selected"; ?>>Unica opcion</option>
                    <option value="Checkbox" <?php if($EditServicioPropiedad["Tipo"]=="Checkbox") echo "selected"; ?>>Multiple opción</option>
                  </select>
                  </td>
                </tr>
                  <tr>
                    <td>Obligatorio?</td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditServicioPropiedad["Obligatorio"] , 'Obligatorio' , "class='input'" ) ?></td>
                  </tr>
                  <tr>
                    <td width="26%">Maximo de opciones permitidas para seleccionar cunado es multiple </td>
                    <td width="74%">
                      <input id="MaximoPermitido" type="text" size="25" title="Maximo Permitido" name="MaximoPermitido" class="input" value="<?php echo $EditServicioPropiedad["MaximoPermitido"] ?>" />
                    </td>
                </tr>
                  <tr>
                    <td>Publicar</td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditServicioPropiedad["Publicar"] , 'Publicar' , "class='input'" ) ?></td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
                  <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm["IDClub"]?>" />
                  <input type="hidden" name="Version" id="Version" value="1" />

                </td>
                <td valign="top">


              </td>
            </tr>
        </table>

        </td>
        </tr>
        <tr>
        	<td align="center"><input type="submit" class="submit" value="Agregar"/></td>
        </tr>

        </table>
</form>


              <br />
              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Nombre</th>
                              <th>Tipo</th>
                              <th>Publicar</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "ServicioPropiedad" , "IDServicio = '" . $frm[$key]  ."'");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&ids=" . $frm[$key] ."&IDServicioPropiedad=".$r->IDServicioPropiedad?>&tab=categoriaserv" class="ace-icon glyphicon glyphicon-pencil"></a></td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php echo $r->Tipo; ?></td>
                              <td><?php echo $r->Publicar; ?></td>
                              <td align="center" width="64">
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioPropiedad&ids=<?php echo $_GET["ids"];?>&IDServicioPropiedad=<? echo $r->IDServicioPropiedad ?>&tab=categoriaserv" ></a>                                </td>
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
