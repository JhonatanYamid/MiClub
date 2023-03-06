      <div id="ClubTipoSocio">
<form name="frmTipoSocio" id="frmTipoSocio" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">
      <table id="simple-table" class="table table-striped table-bordered table-hover">
      	<tr>
      		<td>


		<table id="simple-table" class="table table-striped table-bordered table-hover">
        	<tr>
            	<td valign="top">

                  <table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
                  <tr>
                    <td width="26%">Tipo Socio </td>
                    <td width="74%">

                      <?php
                      $r_valor_tabla_tiposocio =& $dbo->all( "TipoSocio" , "Publicar = 'S'");
                      while( $r_valor = $dbo->object( $r_valor_tabla_tiposocio ) ):
                      $array_tiposocio[]= $r_valor;
                      endwhile;

                      $datos_guardados =& $dbo->all( "ClubTipoSocio" , "IDClub = '".$frm["IDClub"]."'");
                      while( $r_guardado = $dbo->object( $datos_guardados ) ):
                      $array_datos_guardados[]= $r_guardado->IDTipoSocio;
                      endwhile;
                      ?>

                      <select style="width:1200px !important!" multiple class=" chosen-select form-control" name="TipoSocio<?php echo $r->IDTipoSocio; ?>[]" id="TipoSocio<?php echo $r->IDTipoSocio; ?>" data-placeholder="Seleccione...">
                          <?php
                            foreach( $array_tiposocio as $id => $r_valor){
                                if(count($array_datos_guardados)<=0):
                                      $seleccionar = "";
                                elseif(in_array($r_valor->IDTipoSocio,$array_datos_guardados)):
                                      $seleccionar = "selected";
                                else:
                                      $seleccionar = "";
                                endif;
                                ?>
                                <option value="<?php echo $r_valor->IDTipoSocio ?>" <?php echo $seleccionar; ?>>
                                  <?php  echo $r_valor->Nombre;  ?>
                                </option>
                            <?php } ?>
                      </select>

                    </td>
                  </tr>
                  <tr>
                    <td>Categoria Socios</td>
                    <td>
                      <?php
                      $r_valor_tabla_cat =& $dbo->all( "Categoria" , "Publicar = 'S'");
                      while( $r_valor = $dbo->object( $r_valor_tabla_cat ) ):
                      $array_cat[]= $r_valor;
                      endwhile;
                      unset($array_datos_guardados);
                      $datos_guardados =& $dbo->all( "ClubCategoria" , "IDClub = '".$frm["IDClub"]."'");
                      while( $r_guardado = $dbo->object( $datos_guardados ) ):
                      $array_datos_guardados[]= $r_guardado->IDCategoria;
                      endwhile;
                      ?>

                      <select style="width:1200px !important!" multiple class=" chosen-select form-control" name="Categoria<?php echo $r->IDCategoria; ?>[]" id="Categoria<?php echo $r->IDCategoria; ?>" data-placeholder="Seleccione...">
                          <?php
                            foreach( $array_cat as $id => $r_valor){
                                if(count($array_datos_guardados)<=0):
                                      $seleccionar = "";
                                elseif(in_array($r_valor->IDCategoria,$array_datos_guardados)):
                                      $seleccionar = "selected";
                                else:
                                      $seleccionar = "";
                                endif;
                                ?>
                                <option value="<?php echo $r_valor->IDCategoria ?>" <?php echo $seleccionar; ?>>
                                  <?php  echo $r_valor->Nombre;  ?>
                                </option>
                            <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Parentescos Socios</td>
                    <td>
                      <?php
                      $r_valor_tabla_paren =& $dbo->all( "Parentesco" , "Publicar = 'S'");
                      while( $r_valor = $dbo->object( $r_valor_tabla_paren ) ):
                      $array_paren[]= $r_valor;
                      endwhile;
                      unset($array_datos_guardados);
                      $datos_guardados =& $dbo->all( "ClubParentesco" , "IDClub = '".$frm["IDClub"]."'");
                      while( $r_guardado = $dbo->object( $datos_guardados ) ):
                      $array_datos_guardados[]= $r_guardado->IDParentesco;
                      endwhile;
                      ?>

                      <select style="width:1200px !important!" multiple class=" chosen-select form-control" name="Parentesco<?php echo $r->IDParentesco; ?>[]" id="Parentesco<?php echo $r->IDParentesco; ?>" data-placeholder="Seleccione...">
                          <?php
                            foreach( $array_paren as $id => $r_valor){
                                if(count($array_datos_guardados)<=0):
                                      $seleccionar = "";
                                elseif(in_array($r_valor->IDParentesco,$array_datos_guardados)):
                                      $seleccionar = "selected";
                                else:
                                      $seleccionar = "";
                                endif;
                                ?>
                                <option value="<?php echo $r_valor->IDParentesco ?>" <?php echo $seleccionar; ?>>
                                  <?php  echo $r_valor->Nombre;  ?>
                                </option>
                            <?php } ?>
                      </select>
                      </td>
                  </tr>

                  <tr>
                    <td>Datos carné</td>
                    <td>
                      <?php
                      $r_valor_tabla_campos =& $dbo->all( "CampoCarne" , "IDCampoCarne > 0 ");
                      while( $r_valor = $dbo->object( $r_valor_tabla_campos ) ):
                      $array_campo[]= $r_valor;
                      endwhile;
                      unset($array_datos_guardados);
                      $datos_guardados =$dbo->getFields( "Club" , "CampoCarne" , "IDClub = '" . $frm[ $key ] . "'");
                      $array_datos_guardados= explode("|||",$datos_guardados);
                      ?>

                      <select style="width:1200px !important!" multiple class=" chosen-select form-control" name="CampoCarne<?php echo $r->IDCampoEditarSocio; ?>[]" id="CampoCarne<?php echo $r->IDCampoEditarSocio; ?>" data-placeholder="Seleccione...">
                          <?php
                            foreach( $array_campo as $id => $r_valor){
                                if(count($array_datos_guardados)<=0):
                                      $seleccionar = "";
                                elseif(in_array($r_valor->IDCampoCarne,$array_datos_guardados)):
                                      $seleccionar = "selected";
                                else:
                                      $seleccionar = "";
                                endif;
                                ?>
                                <option value="<?php echo $r_valor->IDCampoCarne ?>" <?php echo $seleccionar; ?>>
                                  <?php  echo $r_valor->Nombre;  ?>
                                </option>
                            <?php } ?>
                      </select>
                      </td>
                  </tr>
                  <tr>
                          <td align="center">&nbsp;</td>
                  </tr>
                  </table>
                  <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $frm[ $key ]?>" />
                  <input type="hidden" name="action" id="action" value="UpdateTipoSocio" />

                </td>
                <td valign="top">

              </td>
            </tr>
        </table>

        </td>
        </tr>
        <tr>
        	<td align="center"><input type="submit" class="submit" value="Guardar"></td>
        </tr>

        </table>
</form>

</div>
