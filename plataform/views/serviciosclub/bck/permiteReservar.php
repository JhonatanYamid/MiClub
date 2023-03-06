<form class="form-horizontal formvalida" role="form" method="post" id="frmSocioPlano" name="frmSocioPlano" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
  <table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
      <td> 
        <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
            <td colspan="2"> 
              Estructura del Archivo 
            </td>
          </tr>
          <tr>
            <td>1</td>
            <td>*C&eacute;dula</td>
          </tr>
          <!-- <tr>
            <td>2</td>
            <td>*Handicap</td>
          </tr>   -->        
        </table>
      </td>
      <td valign="top"> 
        <table id="simple-table" class="table table-striped table-bordered table-hover">
          <tr>
            <td>Archivo Excel
              <br>
              <a class="link" href='https://www.miclubapp.com/plataform/views/serviciosclub/carga.xlsx' download="Ejemplo Carga Handicap">Ejemplo carga archivo</a>
            </td>
            <td><input name="file" type="file" size="20" class="form-control" required="required" /></td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
              <input type="hidden" name="action" id="action" value="cargarplano"/>
              <input type="submit" class="submit" value="Cargar">
            
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>

