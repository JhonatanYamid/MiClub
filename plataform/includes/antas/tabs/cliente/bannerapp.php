      <div id="BannerApp">

          <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

                  <?php
                  $action = "InsertarBannerApp";
				  
                  if( $_GET[	IDBannerApp] )
                  {  		
                          $EditBannerApp =$dbo->fetchAll("BannerApp","IDBannerApp = '".$_GET[IDBannerApp]."' ","array");
                          $action = "ModificaBannerApp";
                          ?>
                          <input type="hidden" name="IDBannerApp" id="IDBannerApp" value="<?php echo $EditBannerApp[IDBannerApp]?>" />
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
			            <td><input id=Nombre type=text size=25  name=Nombre class="input mandatory " title="Nombre" value="<?=$EditBannerApp[Nombre] ?>"></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Descripcion</td>
			            <td><textarea rows="5" id=Descripcion cols=60 wrap=virtual class="input mandatory" title="Descripcion" name=Descripcion><?=$EditBannerApp[Descripcion]?></textarea></td>
		              </tr>
			          <tr>
			            <td  class="columnafija" >Publicar</td>
			            <td>&nbsp;</td>
		              </tr>
			<tr>
			  <td class="columnafija">
			    Foto </td>
			  <td>
              <? if (!empty($EditBannerApp[Foto1])) {
					echo "<img src='".BANNERAPP_ROOT."$EditBannerApp[Foto1]'>";
					?>
			    <a
					href="<? echo "?mod=".SIMReg::get( "mod" )."&action=delfotoBannerApp&foto=$EditBannerApp[Foto1]&campo=Foto1&IDBannerApp=" . $EditBannerApp[IDBannerApp] ."&id=".SIMNet::get("id")."#BannerApp" ?>"> <img src='images/trash.png' border='0'></a>
			    <?
				}// END if
				?>
              <input name="Foto1" id=file class=""
					title="Foto1" type="file" size="25" style="font-size: 10px"></td>
			  </tr>
			<tr>
			  <td class="columnafija">&nbsp;</td>
			  <td>&nbsp;</td>
			  </tr>
            
			<tr>
			  <td colspan=2 align=center>
			    
			    <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit>
			    <input type="button" onclick="location.href='?mod=Cliente'" class="submit" value="Cancelar" name="submit">
			    <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
                <input type="hidden" name="ID" value="<?php echo SIMNet::get("id") ?>" />
                <input type="hidden" name="IDCliente" value="<?php echo SIMNet::get("id") ?>" />
                </td>
			  </tr>
			</table>
		</td>
	</tr>
</table>
                  
                  
            
  </form>
              <br />
            
              <table class="adminlist" width="100%">
                      <tr>
                              <th class="title" colspan="12"><?php echo strtoupper( "Link" ) . ": Listado"?></th>
                      </tr>
                      <tr>
                              <th align="center" valign="middle" width="64">Editar</th>
                              <th align="left">Nombre</th>
                              <th align="left">Descripcion</th>
                              <th align="center" valign="middle" width="64">Eliminar</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php

                              $r_BannerApp =& $dbo->all( "BannerApp" , "IDCliente = '" . SIMNet::get("id")  ."'");

                              while( $r = $dbo->object( $r_BannerApp ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td align="center" width="64">
                                      <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&IDBannerApp=" . $r->IDBannerApp ."&id=".SIMNet::get("id")."#BannerApp"?>"><img src='images/edit.png' border='0'></a>                                </td>
                              <td><? echo $r->Nombre?></td>
                              <td><? echo $r->Descripcion?></td>
                              <td align="center" width="64">
                                      <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaBannerApp&id=<?php echo SIMNet::get("id");?>&IDBannerApp=<? echo $r->IDBannerApp ?>&IDPosventa=<?php echo SIMNet::get("id_posventa") ?>"><img src='images/trash.png' border='0' /></a>                                </td>
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="12"></th>
                      </tr>
              </table>



</div>
