      <div id="Restaurante">

          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "InsertarRestaurante";
				  
                  if( $_GET[	IDRestaurante] )
                  {  		
                          $EditRestaurante =$dbo->fetchAll("Restaurante","IDRestaurante = '".$_GET[IDRestaurante]."' ","array");
                          $action = "ModificaRestaurante";
                          ?>
                          <input type="hidden" name="IDRestaurante" id="IDRestaurante" value="<?php echo $EditRestaurante[IDRestaurante]?>" />
                          <?php
                  }
                  ?>
                  
                  
                  
                  
            <table class="adminform">
			<tr>
				<th>&nbsp;Datos</th>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0" border="0" width="100%">
			          <tr>
			            <td  class="columnafija" >Nombre</td>
			            <td><input id=Accion type=number size=25  name=Accion class="input mandatory " title="Nombre" value="<?=$EditRestaurante[Nombre] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Fecha </td>
			            <td><input id="Fecha" type="text" size="10" title="Fecha" name="Fecha" class="input calendar" value="<?php echo $EditRestaurante["Fecha"] ?>" readonly /></td>
		              </tr>
			<tr>
			  <td class="columnafija"><? if (!empty($EditRestaurante[Foto])) {
					echo "<img src='".Restaurante_ROOT."$EditRestaurante[Foto]' width=55 >";
					?>
			    <a
					href="<? echo "?mod=".SIMReg::get( "mod" )."&action=delfotoRestaurante&foto=$EditRestaurante[Foto]&campo=Foto&IDRestaurante=" . $EditRestaurante[IDRestaurante] ."&id=".SIMNet::get("id")."#Restaurante" ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
			    Foto </td>
			  <td><input name="Foto" id=file class=""
					title="Foto" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
            
			<tr>
			  <td colspan=2 align=center>
			    
			    <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			    <input type="button" onclick="location.href='?mod=Cliente'" class="submit" value="Cancelar" name="submit">
			    <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
                <input type="hidden" name="ID" value="<?php echo SIMNet::get("id") ?>" />
                <input type="hidden" name="IDCliente" value="<?php echo SIMNet::get("id") ?>" /></td>
			  </tr>
			</table>
		</td>
	</tr>
</table>
                  
                  
            
  </form>
              <br />
            
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="14"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th align="left">Documento</th>
                              <th align="left">Nombre</th>
                              <th align="left">Email</th>
                              <th align="left">Accion</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_Restaurante =& $dbo->all( "Restaurante" , "IDCliente = '" . SIMNet::get("id")  ."'");

                              while( $r = $dbo->object( $r_Restaurante ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&IDRestaurante=" . $r->IDRestaurante ."&id=".SIMNet::get("id")."#Restaurante"?>"><img src='images/edit.png' border='0'></a>                                </td>
                              <td><? echo $r->NumeroDocumento?></td>
                              <td><? echo $r->Nombre?></td>
                              <td><? echo $r->Email; ?></td>
                              <td><? echo $r->Accion; ?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaRestaurante&id=<?php echo SIMNet::get("id");?>&IDRestaurante=<? echo $r->IDRestaurante ?>&IDPosventa=<?php echo SIMNet::get("id_posventa") ?>"><img src='images/trash.png' border='0' /></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="14"></th>
                      </tr>
              </table>



</div>
