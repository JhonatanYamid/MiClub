<div id="ValorReservaElemento">
  <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

    <?php
    $action = "InsertarValorReservaElemento";

    if( $_GET[IDValorReservaElemento] )
    {
      $EditValorReservaElemento =$dbo->fetchAll("ValorReservaElemento"," IDValorReservaElemento = '".$_GET["IDValorReservaElemento"]."' ","array");
      $action = "ModificaValorReservaElemento";
      ?>
      <input type="hidden" name="IDValorReservaElemento" id="IDValorReservaElemento" value="<?php echo $EditValorReservaElemento["IDValorReservaElemento"]?>" />
      <?php
    }
    ?>

    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <th colspan="4"> VALORES POR ELEMENTO</th>
      </tr>

      <tr>        
          <td width="26%">Nombre</td>
          <td colspan="3">
            <input id="Nombre" type="text" size="100" title="Nombre" name="Nombre" class="input mandatory" value="<?php echo $EditValorReservaElemento["Nombre"] ?>"  />
          </td>        
      </tr>

      <tr>        
          <td width="26%">Descripcion</td>
          <td colspan="3">
            <input id="Descripcion" type="text" size="100" title="Descripcion" name="Descripcion" class="input mandatory" value="<?php echo $EditValorReservaElemento["Descripcion"] ?>"  />
          </td>        
      </tr>

      <tr>        
          <td width="26%">Valor Reserva</td>
          <td colspan="3">
            <input id="Valor" type="number" size="100" title="Valor" name="Valor" class="input mandatory" value="<?php echo $EditValorReservaElemento["Valor"] ?>"  />
          </td>        
      </tr>
      
      <tr>
        <td width="26%">Dias para el valor:</td>
        <td colspan="3">
          <?php
          $array_dias=explode("|",$EditValorReservaElemento["Dias"]);
          array_pop($array_dias);
          foreach($Dia_array as $id_dia => $dia):  ?>
            <input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if(in_array($id_dia,$array_dias) && $dia!="") echo "checked"; ?>><?php echo $dia; ?>
            <?php 
          endforeach;?>
        </td>
      </tr>
      <tr>
        <td>Hora Inicio</td>
        <td><input id="HoraInicio" type="time" size="10" title="Hora Inicio" name="HoraInicio" class="input mandatory" value="<?php echo $EditValorReservaElemento["HoraInicio"] ?>"  /></td>
        <td>Hora Fin</td>
        <td><input id="HoraFin" type="time" size="10" title="Hora Fin" name="HoraFin" class="input mandatory" value="<?php echo $EditValorReservaElemento["HoraFin"] ?>"  /></td>
      </tr>
      <tr>
        <td>Elementos</td>
        <td colspan="3">
          <table class="table table-striped table-bordered table-hover">
            <tr>
              <?php
              $array_elementos_guardados=explode("|",$EditValorReservaElemento["IDServicioElemento"]);
              $r_elemento =& $dbo->all( "ServicioElemento" , "IDServicio = '" . $frm[$key]  ."' Order by Orden ");

              $contador_elementos = 0;
              while( $r = $dbo->object( $r_elemento ) ): ?>
                <td>
                  <input type="checkbox" name="IDServicioElemento[]" id="IDServicioElemento" value="<?php echo $r->IDServicioElemento; ?>" <?php if(in_array( $r->IDServicioElemento,$array_elementos_guardados)) echo "checked"; ?>><?php echo $r->Nombre; ?>
                </td>
                <?php
                $contador_elementos++;
                if($contador_elementos==4):
                  echo "</tr><tr>";
                  $contador_elementos=0;
                endif;
              endwhile; ?>
            </tr>
          </table>
        </td>
      </tr> 

      <tr>        
          <td width="26%">Activo</td>
          <td colspan="3">
            <?php echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $EditValorReservaElemento["Activo"], 'Activo', "class='input'")  ?>
          </td>        
      </tr>     
      <tr>
        <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
        <input type="hidden" name="action" id="action" value="<?php echo $action?>" />

        <td align="center"><input type="submit" class="submit" value="Agregar"> </td>
      </tr>
    </table>

    

  </form>
  <br/>
  <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
          <th align="center" valign="middle" width="64">Editar</th>
          <th>Nombre</th>
          <th>Descripcion</th>
          <th>Valor</th>          
          <th align="center" valign="middle" width="64">Eliminar</th>
      </tr>
      <tbody id="listacontactosanunciante"> <?php
        $r_documento =& $dbo->all( "ValorReservaElemento" , "IDServicio = '" . $frm[$key]  ."'");
          while( $r = $dbo->object( $r_documento ) )
          {?> 
              <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                <td align="center" width="64">
                    <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&ids=" . $_GET[ids] ."&IDValorReservaElemento=".$r->IDValorReservaElemento?>&tab=valorelemento" class="ace-icon glyphicon glyphicon-pencil"></a>
                </td>
                <td><?php echo $r->Nombre; ?></td>
                <td><?php echo $r->Descripcion; ?></td>
                <td><?php echo $r->Valor; ?></td>
                <td align="center" width="64">
                    <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaValorReservaElemento&ids=<?php echo $_GET[ids];?>&IDValorReservaElemento=<? echo $r->IDValorReservaElemento ?>&tab=valorelemento"></a>
                </td>
              </tr> <?php
          }?> 
      </tbody>
      <tr>
          <th class="texto" colspan="15"></th>
      </tr>
  </table>
</div>
