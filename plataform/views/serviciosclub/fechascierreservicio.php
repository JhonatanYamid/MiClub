<div id="ServicioCierre">
  <form name="frmpro" id="frmpro" action="?mod=<?php echo SIMReg::get( "mod" )?>" method="post" class="formvalida" enctype="multipart/form-data">

    <?php
    $action = "InsertarServicioCierre";

    if( $_GET[IDServicioCierre] )
    {
      $EditServicioCierre =$dbo->fetchAll("ServicioCierre"," IDServicioCierre = '".$_GET["IDServicioCierre"]."' ","array");
      $action = "ModificaServicioCierre";
      ?>
      <input type="hidden" name="IDServicioCierre" id="IDServicioCierre" value="<?php echo $EditServicioCierre["IDServicioCierre"]?>" />
      <?php
    }
    ?>

    <table id="simple-table" class="table table-striped table-bordered table-hover">
      <tr>
        <th colspan="4">FECHAS DE CIERRE DEL SERVICIO</th>
      </tr>
      <tr>
        <td width="26%">Fecha Inicio</td>
        <td width="27%"><input id="FechaInicio" type="text" size="10" title="Fecha Inicio" name="FechaInicio" class="input mandatory calendar" value="<?php echo $EditServicioCierre["FechaInicio"] ?>" readonly /></td>
        <td width="17%">Fecha Fin</td>
        <td width="74%"><input id="FechaFin" type="text" size="10" title="Fecha Fin" name="FechaFin" class="input mandatory calendar" value="<?php echo $EditServicioCierre["FechaFin"] ?>" readonly /></td>
      </tr>
      <tr>
        <td width="26%">o todos los (opcional):</td>
        <td colspan="3">
          <?php
          $array_dias=explode("|",$EditServicioCierre["Dias"]);
          array_pop($array_dias);
          foreach($Dia_array as $id_dia => $dia):  ?>
            <input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if(in_array($id_dia,$array_dias) && $dia!="") echo "checked"; ?>><?php echo $dia; ?>
            <?php 
          endforeach;?>
        </td>
      </tr>
      <tr>
        <td>Hora Inicio</td>
        <td><input id="HoraInicio" type="time" size="10" title="Hora Inicio" name="HoraInicio" class="input mandatory" value="<?php echo $EditServicioCierre["HoraInicio"] ?>"  /></td>
        <td>Hora Fin</td>
        <td><input id="HoraFin" type="time" size="10" title="Hora Fin" name="HoraFin" class="input mandatory" value="<?php echo $EditServicioCierre["HoraFin"] ?>"  /></td>
      </tr>
      <tr>
        <td>Elementos</td>
        <td colspan="3">
          <table class="table table-striped table-bordered table-hover">
            <tr>
              <?php
              $array_elementos_guardados=explode("|",$EditServicioCierre["IDServicioElemento"]);
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
      <?php
      //Solo para golf pregunto tee
      $IDServicioMaestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $_GET["ids"] . "'");
      if($IDServicioMaestro==15 || $IDServicioMaestro==27 || $IDServicioMaestro==28 || $IDServicioMaestro==30):
        $tee1_aplica = $EditServicioCierre["Tee1"];
        $tee10_aplica = $EditServicioCierre["Tee10"];
        ?>
        <tr>
          <td>Tee</td>
          <td colspan="3">
            <input type="checkbox" name="Tee1" id="Tee1" class="" value="S" <?php if($tee1_aplica=="S") echo "checked"; ?> > Tee1
            <input type="checkbox" name="Tee10" id="Tee10" class="" value="S" <?php if($tee10_aplica=="S") echo "checked"; ?>> Tee 10
          </td>
        </tr>
        <?php 
      endif; 
      ?>
      <tr>
        <td>Descripcion (se muestra en el app a los socios)</td>
        <td colspan="3">
          <input id="Descripcion" type="text" title="Descripcion" name="Descripcion" class="form-control mandatory" value="<?php echo $EditServicioCierre["Descripcion"] ?>" />
        </td>
      </tr>
      <tr>
        <td>Descripcion Interna ( no se muestra en el app a los socios)</td>
        <td colspan="3">
          <input id="DescripcionInterna" type="text" title="DescripcionInterna" name="DescripcionInterna" class="form-control" value="<?php echo $EditServicioCierre["DescripcionInterna"] ?>" />
        </td>
      </tr>
      <tr>
        <td>Mas informacion del cierre ( cuando el socio pulse sobre el titulo del cierre se mostrará esta info )</td>
        <td colspan="3">
          <input id="MasInformacionCierre" type="text" title="MasInformacionCierre" name="MasInformacionCierre" class="form-control" value="<?php echo $EditServicioCierre["MasInformacionCierre"] ?>" />
        </td>
      </tr>
      <tr>
        <td>Fecha de Cierre por sorteo?</td>
        <td colspan="3">
        <? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $EditServicioCierre["CierrePorSorteo"] , ' CierrePorSorteo' , "class='input'" ) ?>

        </td>
      </tr>
      <tr>
        <td align="center"><input type="submit" class="submit" value="Agregar"> </td>
      </tr>
    </table>

    <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $frm[ $key ]?>" />
    <input type="hidden" name="action" id="action" value="<?php echo $action?>" />

  </form>

    <br/>

    <form name="frmbuscarfecha" id="frmbuscarfecha" action="" method="post" class="formvalida" enctype="multipart/form-data">
      <table class="table table-striped table-bordered table-hover">
        <tr>
          <td>
            Buscar por fecha: 
            <input id="FechaBusqueda" type="text" size="10" title="Fecha Busqueda" name="FechaBusqueda" class="input calendar" value="<?php echo $_GET["FechaBusqueda"] ?>" readonly />
            <input type="submit" id="btnbuscarfecha"  name="btnbuscarfecha" value="Buscar">
            <input type="hidden" name="ids" id="ids" value="<?php echo $_GET["ids"]; ?>">
            <input type="hidden" name="action" id="action" value="BuscadorFecha">
            <input type="hidden" name="tab" id="tab" value="fechas">
          </td>
        </tr>
      </table>
    </form>

    <form name="frmdescargar" id="frmdescargar" action="procedures/excel-fechasCierre.php" method="post" class="formvalida" enctype="multipart/form-data">
      <table class="table table-striped table-bordered table-hover">
        <tr>
          <td>            
            <input type="submit" value="Descargar fechas de cierre">
            <input type="hidden" name="ids" id="ids" value="<?php echo $_GET["ids"]; ?>"> 
          </td>
        </tr>
      </table>               
    </form>

    <form name="frmselecc" id="frmselecc" action="" method="post" class="formvalida" enctype="multipart/form-data">
      <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
          <th align="center" valign="middle" >Editar</th>
          <th width="30%">Fecha </th>
          <th width="30%">Hora</th>
          <th width="30%">Descripcion</th>
          <th width="30%">Elementos</th>
          <th width="30%">Creado por </th>
          <th width="30%">Editado por </th>
          <th align="center" valign="middle" >Eliminar</th>
          <th align="center" valign="middle" >Eliminar Seleccion</th>
        </tr>
        <tbody id="listacontactosanunciante">
          <?php
          $IDClubS = $dbo->getFields( "Servicio" , "IDClub" , "IDServicio = '" . $_GET["ids"] . "'");
          if(!empty($_GET["FechaBuscar"]))
          {
            $condicion_fecha=" and FechaInicio<= '".$_GET["FechaBuscar"]."' and FechaFin >= '".$_GET["FechaBuscar"]."' ";
          }

          if($IDClubS==25)
          {
            $condicion_historial=" and  FechaFin >= curdate() ";
          }

          $r_documento =& $dbo->all( "ServicioCierre" , "IDServicio = '".$frm[$key]."' " . $condicion_historial .$condicion_fecha." Order by FechaInicio ASC");

          while( $r = $dbo->object( $r_documento ) )
          {
            ?>
            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
              <td align="center" width="64">
                <a href="<?php echo "?mod=" . SIMReg::get( "mod" ) . "&action=edit&ids=" . $_GET["ids"] ."&IDServicioCierre=".$r->IDServicioCierre?>&tab=fechas" class="ace-icon glyphicon glyphicon-pencil"></a>                               
              </td>
              <td><?php echo $r->FechaInicio . " - " . $r->FechaFin;; ?></td>
              <td><?php echo $r->HoraInicio  . " - " . $r->HoraFin; ; ?></td>
              <td><?php echo $r->Descripcion . " " . $r->DescripcionInterna ; ?></td>
              <td>
                <?php
                $array_elementos_guardados=explode("|",$r->IDServicioElemento);
                $r_elemento =& $dbo->all( "ServicioElemento" , "IDServicio = '" . $frm[$key]  ."'");
                while( $row_elemento = $dbo->object( $r_elemento ) )
                {
                  if(in_array( $row_elemento->IDServicioElemento,$array_elementos_guardados)) echo $row_elemento->Nombre;                
                }
                  if($r->Tee1=="S")
                  echo " - Tee 1";
                  elseif($r->Tee10=="S")
                  echo " - Tee 10"
                ?>
              </td>
              <td><?php echo $r->UsuarioTrCr . "<br>".$r->FechaTrCr; ?></td>
              <td><?php echo $r->UsuarioTrEd . "<br>".$r->FechaTrEd;  ?></td>
              <td align="center" width="64">
                <a href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaServicioCierre&ids=<?php echo $_GET["ids"];?>&IDServicioCierre=<? echo $r->IDServicioCierre ?>&tab=fechas" class="ace-icon glyphicon glyphicon-remove"></a>                                
              </td>
              <td align="center"><input type="checkbox" name="SeleccFechaCierre[]" value="<? echo $r->IDServicioCierre ?>"></td>
            </tr>
            <?php
          }
          ?>
          <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
            <td align="center" width="64"></td>
            <td align="center" width="64"></td>
            <td align="center" width="64"></td>
            <td align="center" width="64"></td>
            <td align="center" width="64"></td>
            <td align="center" width="64"></td>
            <td>              
            </td>
            <td>
            </td>
            <td align="center">
              <input type="submit" id="EliminarSeleccion" value="Eliminar Seleccion" >
              <input type="hidden" name="ids" id="ids" value="<?php echo $_GET["ids"]; ?>">
              <input type="hidden" name="action" id="action" value="EliminaSeleccionFecha">
            </td>            
          </tr>
        </tbody>
      </table>
    </form>

</div>
