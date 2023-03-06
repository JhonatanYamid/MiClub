<?php
    $idSocio = $_GET["IDSocio"];
    $idInvitado = $_GET["idInvitado"];

    $sql = "SELECT IDTipoObjeto, Nombre FROM TipoObjeto WHERE IDClub=" . SIMUser::get("club");
	$queryTiposObjeto = $dbo->query($sql);
	
	$tiposObjeto = [];
	while($row = $dbo->fetchArray($queryTiposObjeto)) {
		$tiposObjeto[] = ["id" => $row["IDTipoObjeto"], "value" => $row["Nombre"]];		
	}

    $action = "IDAccesoObjeto ";
    
    if( $_GET["IDAccesoObjeto"] )
    {

        $IDAccesoObjeto = $_GET["IDAccesoObjeto"];

        $frm =$dbo->fetchAll("AccesoObjeto"," IDAccesoObjeto=","array");
        $action = "ModificaVehiculo";
        ?>
        <input type="hidden" name="IDVehiculo" id="IDVehiculo" value="<?php echo $frm["IDAccesoObjeto"]?>" />
        <?php
    }
?>

<form class="form-horizontal formvalida" role="form" method="post" id="" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data"> 
    <div class="form-group first">        
        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right">Tipo Objeto</label>
            <div class="col-sm-8">                
                <select id="objeto_IDTipoObjeto" name="objeto_IDTipoObjeto"> 
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
            <label class="col-sm-4 control-label no-padding-right">Objeto</label>
            <div class="col-sm-8">
                <input type="text" id="objeto_Campo1" name="objeto_Campo1" value="">
                <input type="hidden" id="objeto_IDInvitado" name="objeto_IDInvitado" value="">
                <input type="hidden" id="objeto_IDSocio" name="objeto_IDSocio" value="">
            </div>
        </div> 
    </div>      
    <div class="form-group first">
        <div  class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right">Descripción</label>
            <div class="col-sm-8">
                <textarea name="objeto_Campo2" id="objeto_Campo2" cols="30" rows="10"></textarea>
            </div>
        </div>
    </div>
</form>
<br />
<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tr>
        <th align="center" valign="middle" width="64">Editar</th>
        <th>Tipo Objeto</th>
        <th>Nombre</th>
        <th>Descripción</th>
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
                    <a href="<?php echo $script.".php" . "?action=edit&id=" . $frm[$key] ."&IDVehiculo=".$r->IDVehiculo?>&tabsocio=vehiculos" class="ace-icon glyphicon glyphicon-pencil"></a>                                </td>
                    <td><?php echo $r->TipoObjeto; ?></td>
                    <td><?php echo $r->Campo1; ?></td>
                    <td><?php echo $r->Campo2; ?></td>
                <td align="center" width="64">
                <a class="ace-icon glyphicon glyphicon-remove confirma_eliminacion" href="?mod=<?php echo SIMReg::get( "mod" )?>&action=EliminaVehiculo&id=<?php echo $frm[$key];?>&IDVehiculo=<? echo $r->IDVehiculo ?>&tabsocio=vehiculos" ></a>                                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>       
</table>                    
                       
