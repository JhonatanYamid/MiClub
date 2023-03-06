      <div id="CampoEditarSocio">
<form name="frmproCampoDirectorioSocio" id="frmproCampoDirectorioSocio" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
      <table id="simple-table" class="table table-striped table-bordered table-hover">
      	<tr>
      		<td>


		<table id="simple-table" class="table table-striped table-bordered table-hover">
        	<tr>
            	<td valign="top">


                  <?php
                  $action = "InsertarCampoEditarSocio";

                  if( $_GET[IDCampoEditarSocio] )
                  {
                          $EditCampoEditarSocio =$dbo->fetchAll("CampoEditarSocio"," IDCampoEditarSocio = '".$_GET[IDCampoEditarSocio]."' ","array");
                          $action = "ModificarCampoEditarSocio";
                          ?>
                          <input type="hidden" name="IDCampoEditarSocio" id="IDCampoEditarSocio" value="<?php echo $EditCampoEditarSocio[IDCampoEditarSocio]?>" />
                          <?php
                  }
                  ?>
                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                  <!--
                  <tr>
                    <td>Padre</td>
                    <td>
					<select name="IDPadre" id="IDPadre">
                    	<option value="">[Seleccione]</option>
					<?php
						$qry_padre = $dbo->all( "CampoEditarSocio", " IDClub = '".$frm[ $key ]."'" );
						while ( $r_pade = $dbo->object( $qry_padre ) ): ?>
							<option value="<?php echo $r_pade->IDCampoEditarSocio?>" <?php if($r_pade->IDCampoEditarSocio==$EditCampoEditarSocio[IDPadre]) echo "selected"; ?>> <?php echo $r_pade->Nombre?></option>
                        <?php
						endwhile;
					?>
                    </select>
                    </td>
                  </tr>
                  -->

                  <tr>
                    <td width="">Nombre </td>
                    <td width=""><input id="Nombre" type="text" size="25" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditCampoEditarSocio["Nombre"] ?>" /></td>
                    <td>Tipo Respuesta</td>
                    <td>
                      <select class="form-control" id="Tipo" name="Tipo">
                                            <optgroup label="Estándar">
                                            <option value="text" <?php if($EditCampoEditarSocio["Tipo"]=="text") echo "selected"; ?>>Texto en una línea</option>
                                            <option value="textarea" <?php if($EditCampoEditarSocio["Tipo"]=="textarea") echo "selected"; ?>>Texto en párrafo</option>
                                            <option value="radio" <?php if($EditCampoEditarSocio["Tipo"]=="radio") echo "selected"; ?>>Varias opciones (una sola respuesta)</option>
                                            <option value="checkbox" <?php if($EditCampoEditarSocio["Tipo"]=="checkbox") echo "selected"; ?>>Varias opciones (múltiples selección)</option>
                                            <option value="outerselect" <?php if($EditCampoEditarSocio["Tipo"]=="outerselect") echo "selected"; ?>>Varias opciones (unica opcion en otra pantalla)</option>
                                            <option value="select" <?php if($EditCampoEditarSocio["Tipo"]=="select") echo "selected"; ?>>Menú desplegable</option>
                                            <option value="number" <?php if($EditCampoEditarSocio["Tipo"]=="number") echo "selected"; ?>>Número</option>
                                            <!--<option value="page">Page Break</option>-->
                                            </optgroup>
                                            <optgroup label="Elegantes">
                                            <option value="date" <?php if($EditCampoEditarSocio["Tipo"]=="date") echo "selected"; ?>>Fecha</option>
                                            <option value="time" <?php if($EditCampoEditarSocio["Tipo"]=="time") echo "selected"; ?>>Hora</option>
                                            <option value="email" <?php if($EditCampoEditarSocio["Tipo"]=="email") echo "selected"; ?>>Correo electrónico</option>
                                            </optgroup>
                                            <optgroup label="Titulo">
                                            <option value="titulo" <?php if($EditCampoEditarSocio["Tipo"]=="titulo") echo "selected"; ?>>Titulo</option>

                                          </select>
                    </td>
                  </tr>
                  <tr>
                    <td >Opciones de respuesta (separados por coma) </td>
                    <td ><textarea id="Valores" name="Valores" cols="10" rows="5" class="col-xs-12" title="Descripcion"><?php echo $EditCampoEditarSocio["Valores"]; ?></textarea></td>
                    <td>Orden</td>
                    <td>
                    <input type="number" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $EditCampoEditarSocio["Orden"]; ?>" >
                    </td>
                  </tr>
                  <tr>
                    <td>Permite Editar</td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditCampoEditarSocio["PermiteEditar"] , 'PermiteEditar' , "class='input'" ) ?></td>
                    <td>Obligatorio</td>
                    <td><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditCampoEditarSocio["Obligatorio"] , 'Obligatorio' , "class='input'" ) ?></td>
                  </tr>
                  <tr>
                          <td align="center">&nbsp;</td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="<?php echo $action?>" />

                </td>
                <td valign="top">

                  <?php
                  //$action = "InsertarDisponibilidadElemento";
                  ?>

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
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th>Nombre</th>
                              <th>TipoCampo</th>
                              <th>Obligatorio</th>
                              <th>Orden</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_documento =& $dbo->all( "CampoEditarSocio" , "IDClub = '" . $frm[$key]  ."' Order by Orden");

                              while( $r = $dbo->object( $r_documento ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDCampoEditarSocio=".$r->IDCampoEditarSocio?>&tabclub=parametros&tabparametro=camposperfil" class="ace-icon glyphicon glyphicon-pencil"></a>                                </td>
                              <td><?php echo $r->Nombre; ?></td>
                              <td><?php echo $r->Tipo; ?></td>
                              <td><?php echo $r->Obligatorio; ?></td>
                              <td><?php echo $r->Orden; ?></td>
                              <td align="center" width="64">
                                      <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaCampoEditarSocio&id=<?php echo $frm[$key];?>&IDCampoEditarSocio=<? echo $r->IDCampoEditarSocio ?>&tabclub=parametros&tabparametro=camposperfil" ></a>                                </td>
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
