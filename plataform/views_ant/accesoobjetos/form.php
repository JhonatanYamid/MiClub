<?php



    $idSocio = $_GET["IDSocio"];
    $idInvitado = $_GET["IDInvitado"];

    $sql = "SELECT IDTipoObjeto, Nombre FROM TipoObjeto WHERE IDClub=" . SIMUser::get("club");
	$queryTiposObjeto = $dbo->query($sql);
	
	$tiposObjeto = [];
	while($row = $dbo->fetchArray($queryTiposObjeto)) {
		$tiposObjeto[] = ["id" => $row["IDTipoObjeto"], "value" => $row["Nombre"]];		
	}

    $action = "insertar";
    
    if( $_GET["IDAccesoObjeto"] )
    {

      
        $IDAccesoObjeto = $_GET["IDAccesoObjeto"];

        $frm =$dbo->fetchAll("AccesoObjeto"," IDAccesoObjeto=".$IDAccesoObjeto,"array");
        $action = "actualizar";
        ?>
        <input type="hidden" name="IDAccesoObjeto" id="IDAccesoObjeto" value="<?php echo $frm["IDAccesoObjeto"]?>"/>
        <?php
    }
?>
+

<form class="form-horizontal formvalida" role="form" method="post" id="" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data"> 
    <div class="form-group first">        
        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right">Tipo Objeto</label>
            <div class="col-sm-8">                
                <select id="IDTipoObjeto" name="IDTipoObjeto"> 
                    <option value=""></option>
                <?php foreach($tiposObjeto as $value):
                    echo '<option';
                    echo ($frm["IDTipoObjeto"]==$value['id'] ? ' selected' : "");
                    echo ' value="' . $value['id'] . '">' . $value['value'] .'</option>';														
                endforeach; ?>
                </select>
            </div>
        </div>
    <div>
    <div class="form-group first">
        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right">Color</label>
            <div class="col-sm-8">
                <input type="text" id="Campo1" name="Campo1" value="<?php echo $frm["Campo1"] ?>">
                <input type="hidden" id="IDInvitado" name="IDInvitado" value="<?php echo $idInvitado?>">
                <input type="hidden" id="IDSocio" name="IDSocio" value="<?php echo $idSocio?>">
                <input type="hidden" id="action" name="IDClub" value="<?php echo SIMUser::get("club")?>">
                <input type="hidden" id="action" name="IDUsuario" value="<?php echo SIMUser::get("IDUsuario")?>">
                <input type="hidden" id="action" name="action" value="<?php echo $action?>">
            </div>
        </div> 
    </div>      
    <div class="form-group first">
        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right">Serial</label>
            <div class="col-sm-8">
                
                <textarea name="Campo2" id="Campo2" cols="30" rows="10" ><?php echo  $frm['Campo2']; ?> </textarea>
            </div>
        </div>
    </div>
    
    <div class="form-group first">
        <div  class="col-xs-12 col-sm-12">     
         
       
            <input type="submit" class="btn btn-primary btn-block" value="<?php 
                                                            if ( $_GET["IDAccesoObjeto"]) echo 'Actualizar objeto';
                                                            else echo 'Guardar objeto'; ?>">            
        </div>
    </div>    
</form>
<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64">Editar</th>
        <th>Tipo Objeto</th>
        <th>Color</th>
        <th>Serial</th>
        <th align="center" valign="middle" width="64">Eliminar</th>
    </tr>
    <tbody id="listacontactosanunciante">
        <?php

        $r_datos =& $dbo->all( "AccesoObjeto" , "IDSocio=$IDSocio AND IDInvitado=$IDInvitado" );

        while( $r = $dbo->object( $r_datos ) )
        {
          
            ?>

            <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                <td align="center" width="64">
                    <a href="accesoobjetos.php?action=edit&id=<?php echo $r->IDAccesoObjeto?>&IDAccesoObjeto=<?php echo $r->IDAccesoObjeto?>&IDSocio=<?php echo $IDSocio?>&IDInvitado=<?php echo $idInvitado ?>" class="ace-icon glyphicon glyphicon-pencil"></a>                                </td>
                    <td><?php echo $r->IDTipoObjeto; ?></td>
                    <td><?php echo $r->Campo1; ?></td>
                    <td><?php echo $r->Campo2; ?></td>
                <td align="center" width="64">
                <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=Eliminar&id=<?php echo $r->IDAccesoObjeto;?>&IDAccesoObjeto=<? echo $r->IDAccesoObjeto ?>" ></a>                                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>       
</table>                    
                       
