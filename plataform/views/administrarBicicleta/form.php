<?
    $arrAccesorios = array();

    if(SIMNet::req("action") == 'edit' || SIMNet::req("action") == 'admin'){
        $sqlAccesorios = "SELECT IDAccesoriosBicicleta, Nombre, Marca, Color, Estado FROM AccesoriosBicicleta WHERE IDBicicleta = ".SIMNet::reqInt("id");
        $qryAccesorios = $dbo->query($sqlAccesorios);

        while ($rAccesorios = $dbo->fetchArray($qryAccesorios)){
            $arrAccesorios[] = $rAccesorios;
        }
    }
    
?>
<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?php echo $titulo_accion; ?>
            <?php echo strtoupper(SIMReg::get("title")); ?>
        </h4>
    </div>

    <div class="widget-body">
        <div class="widget-main padding-4">
            <form class="form-horizontal formvalida" role="form" method="post" id="frmNuevaBicicleta" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

                <div class="widget-header widget-header-large">
                    <h3 class="widget-title grey lighter">
                        <i class="ace-icon fa fa-users green"></i>
                        Datos Basicos
                    </h3>
                </div>

                <?php
                if ($action != "admin") {
                ?>
                    <div class="form-group first ">
                        <div class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Nombre</label>
                            <div class="col-sm-8">
                                <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
                            </div>
                        </div>

                        <?php
                        $tipo = '';
                        $idInvitado = '';
                        $invitado = '';
                        $displaySocio = "inline";
                        $displayInvitado = "none";

                        if ($frm['IDInvitado']) {
                            $tipo = 2;
                            $idInvitado = $frm['IDInvitado'];
                        } else if ($frm['IDSocioInvitado']) {
                            $tipo = 1;
                            $idInvitado = $frm['IDSocioInvitado'];
                        }

                        if ($tipo == 1) {
                            $sql_socio_club = "Select * From SocioInvitado Where IDSocioInvitado = " . $idInvitado;
                            $qry_socio_club = $dbo->query($sql_socio_club);
                            $rData = $dbo->fetchArray($qry_socio_club);
                            $invitado = $rData["Nombre"];
                        } else if ($tipo == 2) {
                            $sql_socio_club = "Select * From Invitado Where IDInvitado = " . $idInvitado;
                            $qry_socio_club = $dbo->query($sql_socio_club);
                            $rData = $dbo->fetchArray($qry_socio_club);
                            $invitado = $rData["Nombre"] . " " . $rData["Apellido"];
                        }

                        if ($idInvitado > 0) {
                            $displaySocio = "none";
                            $displayInvitado = "display";
                        }

                        ?>

                        <div class="col-xs-12 col-sm-6">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <label class="control-label"><input <?php echo $idInvitado == "" ? 'checked' : ""; ?> type="radio" id="PersonaBicicleta" name="PersonaBicicleta" value="1"> Socio </label>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label"><input <?php echo $idInvitado > 0 ? 'checked' : ""; ?> type="radio" id="PersonaBicicleta" name="PersonaBicicleta" value="2"> Invitado </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group first ">
                        <div class="col-xs-12 col-sm-6 divSocio" style="display:<?php echo $displaySocio; ?>;">
                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>
                            <div class="col-sm-8">
                                <?php
                                $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                                $qry_socio_club = $dbo->query($sql_socio_club);
                                $r_socio = $dbo->fetchArray($qry_socio_club);
                                ?>

                                <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax" title="número de derecho" value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
                                <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" title="Socio">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 divInvitado" style="display:<?php echo $displayInvitado; ?>;">
                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Invitado </label>
                            <div class="col-sm-8">

                                <input type="text" id="Accion2" name="Accion2" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-invitadosIntegrados" title="Nombre invitado" value="<?php echo $invitado; ?>">
                                <input type="hidden" name="IDInvitado" value="<?php echo $idInvitado; ?>" id="IDInvitado">
                                <input type="hidden" name="TipoInvitado" value="<?php echo $tipo; ?>" id="TipoInvitado">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Foto</label>
                            <div class="col-sm-8">
                                <input name="Foto" id="Foto" class="" title="Foto" type="file" size="25" style="font-size: 10px">
                                <?  if (!empty($frm['Foto'])) {
                                    echo "<img src='" . BICICLETA_ROOT . $frm["Foto"] . "' width=100 height=100>";
                                    echo "<a href='administrarBicicleta.php?action=delfoto&foto=".$frm['Foto']."&campo=Foto&id=". $frm[$key]."' class='ace-icon glyphicon glyphicon-trash'>&nbsp;</a>";
                                }?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group first " style="display:<?php echo $displaySocio; ?>;">
                        <div class="col-xs-12 col-sm-12">
                            <label class="col-sm-2 control-label no-padding-right " for="form-field-1"> Localización </label>
                            <div class="col-sm-10">
                                <textarea id="Localizacion" name="Localizacion" placeholder="Localización de la bicicleta" class="col-xs-12"><?php echo $frm["Localizacion"]; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group first ">
                         <?php
                            $tipoCodigo = $dbo->getFields("Bicicleta", "TipoCodigo", "IDBicicleta = ".SIMNet::reqInt("id"));
                        ?> 

                        <div  class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> </label>
                            <div class="col-sm-8" style="vertical-align: middle;">
                                <label><input <?php echo $tipoCodigo == 1 ? 'checked' : ""; ?>  type="radio" id="TipoCodigo" name="TipoCodigo" value="1" /> Codigo de barras</label>
                            </div>
                        </div>

                        <div  class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"></label>
                            <div class="col-sm-8">
                                <label><input <?php echo $tipoCodigo == 2 ? 'checked' : ""; ?> type="radio" id="TipoCodigo" name="TipoCodigo" value="2" /> Codigo QR </label>
                            </div>
                        </div>
                    </div>

                <?php
                } else {
                ?>

                    <div class="form-group first ">
                        <div class="col-xs-12 col-sm-4">
                            Nombre: <?php echo $frm["Nombre"]; ?>
                        </div>
                        <div class="col-xs-12 col-sm-4">
                            Socio / Invitado: <?php echo $frm["Socio"]; ?>
                        </div>
                        <div class="col-xs-12 col-sm-4">
                            Codigo: <?php echo $frm["Codigo"]; ?>
                        </div>
                        <input type="hidden" id="Estado" name="Estado" value="<?php echo $frm["Estado"]; ?>" />
                        <input type="hidden" id="IDSocio" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" />
                    </div>
                    <div class="form-group first ">
                        <div class="col-xs-12 col-sm-4">
                            Localizacion: <?php echo $frm["Localizacion"]; ?>
                        </div>
                    </div>


                    <div class="form-group first ">

                        <div class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1">
                                <?php
                                if ($frm["Estado"] == 1)
                                    echo 'Recibe';
                                else
                                    echo 'Entrega';
                                ?> Tercero
                            </label>
                            <div class="col-sm-8">
                                <input style="height:30px;" checked="" type="radio" id="RecibeTercero" name="RecibeTercero" value="1">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1">
                                <?php
                                if ($frm["Estado"] == 1)
                                    echo 'Recibe';
                                else
                                    echo 'Entrega';
                                ?> Socio/Invitado
                            </label>
                            <div class="col-sm-8">
                                <input style="height:30px;" type="radio" id="RecibeTercero" name="RecibeTercero" value="0">
                            </div>
                        </div>

                    </div>

                    <div class="form-group first tercero">

                        <div class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1">Numero documento</label>

                            <div class="col-sm-8">
                                <input type="text" id="NumeroDocumentoTercero" name="NumeroDocumentoTercero" placeholder="Numero documento del tercero" class="col-xs-12" title="Numero documento del tercero" value="">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Nombre </label>
                            <div class="col-sm-8">
                                <input type="text" id="NombreTercero" name="NombreTercero" placeholder="Nombre del tercero" class="col-xs-12" title="Nombre del tercero" value="">
                            </div>
                        </div>
                    </div>

                    <?php if ($frm["Estado"] == 4 || $frm["Estado"] == 1){ ?>
                        <div  class="form-group first">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Lugar de Entrega </label>
                                <div class="col-sm-8"> 
                                    <?php
                                        $sql_lugar = "SELECT IDConfiguracionBicicletaLugar,Nombre FROM ConfiguracionBicicletaLugar WHERE IDClub = '" . SIMUser::get("club") . "' Order by Nombre";
                                        $result_lugar = $dbo->query($sql_lugar);
                                    ?> 
                                    <select name="IDConfiguracionBicicletaLugarEntrega" id="IDConfiguracionBicicletaLugarEntrega" class="form-control">
                                        <option value="">[Seleccione Lugar]</option> 
                                        <?php
                                            while ($row_lugar = $dbo->fetchArray($result_lugar)) { ?> 
                                                <option value="<?php echo $row_lugar["IDConfiguracionBicicletaLugar"];  ?>" <?php if ($frm["IDConfiguracionBicicletaLugarEntrega"] == $row_lugar["IDConfiguracionBicicletaLugar"]) echo "selected"; ?>><?php echo $row_lugar["Nombre"];  ?></option> <?php } ?>
                                    </select> 
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="form-group first ">

                        <div class="col-xs-12 col-sm-12">
                            <label class="col-sm-2 control-label no-padding-right " for="form-field-1"> Observaciones </label>
                            <div class="col-sm-10">
                                <textarea id="Observaciones" name="Observaciones" placeholder="Observaciones" class="col-xs-12"></textarea>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <hr>
                <?php

                $indexFinal = count($aPropiedades) - 1;
                foreach ($aPropiedades as $index => $propiedad) {

                    $content = "";
                    $valor = $aPropiedadesBicicleta[$propiedad["IDPropiedadesBicicleta"]] == "" ? 0 : $aPropiedadesBicicleta[$propiedad["IDPropiedadesBicicleta"]];

                    $contentProp = '<div  class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">'. $propiedad["Nombre"].'</label>

                                        <div class="col-sm-8">
                                            <input type="hidden" id="IDPropiedad[]" name="IDPropiedad[]" value="'.$propiedad["IDPropiedadesBicicleta"].'" >
                                            <input type="hidden" id="NombrePropiedad[]" name="NombrePropiedad[]" value="'.$propiedad["Nombre"].'" >
                                            <input type="text" id="Propiedad[]" name="Propiedad[]"  placeholder="Digite un valor para '.$propiedad["Nombre"].'" class="col-xs-12" value="'.$valor.'" >
                                        </div>
                                    </div>';
                    
                    $contentObservacion = '<div  class="col-xs-12 col-sm-6">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Observacion</label>

                                                <div class="col-sm-8">
                                                    <textarea id="Observacion[]" name="Observacion[]" placeholder="Digite la observacion para '.$propiedad["Nombre"].'" class="col-xs-12" >'.$propiedad["Observacion"].'</textarea>
                                                </div>
                                            </div>'; 

                    if ($frm["Estado"] != 3){
                        if ($index == 0) 
                            $content .= '<div class="form-group first ">';
                        
                        $content .= $contentProp; 

                        if ($index == $indexFinal || ($index + 1) % 2 == 0)
                            $content .= '</div>';
                        
                        if ($index != $indexFinal && ($index + 1) % 2 == 0) 
                            $content .= '<div class="form-group first ">';
                    }
                    else{
                        $content .= '<div class="form-group first ">'.$contentProp." ".$contentObservacion."</div>";
                    }
                    echo $content;
                } ?>

                <!--Admin Accesorios -->
                <div class="widget-header widget-header-large no-margin">
                    <h4 class="widget-title grey lighter">
                        <i class="ace-icon fas fa-golf-ball green"></i> <?= ucwords(SIMUtil::get_traduccion('', '', 'administraraccesorios', LANGSESSION));?>
                    </h4>
                </div>
                <div class="form-group first ">
                    <div class="col-xs-12 col-xs-12">
                        <center>
                            <div class="p first">
                                <table id="simple-table" class="table table-striped table-bordered table-hover" style="width:80%">
                                    <thead id="headAccesorios">
                                        <tr>
                                            <th style="width:30%"><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION);?></th>
                                            <th align='center' valign='middle' style="width:20%"><?= SIMUtil::get_traduccion('', '', 'Marca', LANGSESSION);?></th>
                                            <th align='center' valign='middle' style="width:20%"><?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION);?></th>
                                            <th align='center' valign='middle' style="width:20%"><?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION);?></th>
                                            <th align='center' valign='middle' style="width:10%"><?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION);?></th>
                                        </tr>
                                        <tr>
                                            <td><input style="width: 100%;" type="text" id="NombreAccesorio" name="NombreAccesorio" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="" /></td>
                                            <td><input type="text" id="Marca" name="Marca" placeholder="<?= SIMUtil::get_traduccion('', '', 'Marca', LANGSESSION); ?>" value="" /></td>
                                            <td><input type="text" id="Color" name="Color" placeholder="<?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION); ?>" value="" /></td>
                                            <td><input type="text" id="EstadoAccesorio" name="EstadoAccesorio" placeholder="<?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?>" value="" /></td>
                                            <td align='center' valign='middle'><button onclick="agregarAccesorio()" type='button' class='button_style'><i class='ace-icon fa fa-plus blue'></i></button></td>
                                        </tr>
                                    <thead>
                                    <tbody id="listaAccesorios"></tbody>
                                </table>
                            </div>
                            <input type="hidden" id="Accesorios" name="Accesorios" value="" />
                        </center>
                    </div>
                </div>

                <div class="clearfix form-actions">
                    <div class="col-xs-12 text-center">
                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                        <button class="btn btn-info btnRegistrarBicicleta form-control" type="button" rel="frmNuevaBicicleta">
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            <?php echo $titulo_accion; ?> Bicicleta
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
include("list");
include("cmp/footer_scripts.php");
?>

<script type="text/javascript">

    let arrAccesorios = <? echo json_encode($arrAccesorios); ?>;
    cargarAccesorios();
    
    $(".btnRegistrarBicicleta").on("click", function(e) {
        
        var propiedadesVacias = 0;
        
        if ($("[name='Propiedad[]']").length > 0) {
            $.each($("[name='Propiedad[]']"), function(index, objeto) {
                if ($(objeto).val() == "") {
                    propiedadesVacias++;
                }
            });
        } else {
            propiedadesVacias++;
        }

        var continua = 0;
        if ($("[name='PersonaBicicleta']").is(":checked")) {
            if ($("[name='PersonaBicicleta']:checked").val() == 1 && $("#IDSocio").val() == "") {
                continua++;
                alert("Debe seleccionar un socio");
            } else if ($("[name='PersonaBicicleta']:checked").val() == 2 && $("#IDInvitado").val() == "") {
                continua++;
                alert("Debe seleccionar un invitado");
            }
        }

        var admin = 0;
        if ($("input[name='RecibeTercero']:checked").val() == 1) {
            if ($("#NumeroDocumentoTercero").val() == "" || $("#NombreTercero").val() == "") {
                admin++;
                alert("Debe digitar un numero de documento para el tercero y su nombre");
            }
        }

        if (propiedadesVacias > 0 || admin > 0) {
            alert("Debe registrar un valor en las propiedades de la bicicleta");
        }


        if (propiedadesVacias == 0 && admin == 0 && continua == 0) {
            var form = $(this).attr("rel");
            $("#" + form).submit();
        }

    });

    $("input[name='RecibeTercero']").on("click", function() {
        var valor = $(this).val();
        if (valor == 1)
            $(".tercero").show("slow");
        else
            $(".tercero").hide("slow");

    });


    $("#Accion").on("keyup", function() {
        $("#IDSocio").val("");
    });

    $("#Accion").on("blur", function() {
        if ($("#IDSocio").val() == "")
            $("#Accion").val("");
    });


    $("#Accion2").on("keyup", function() {
        $("#IDInvitado").val("");
        $("#TipoInvitado").val("");
    });

    $("#Accion2").on("blur", function() {
        if ($("#IDInvitado").val() == "")
            $("#Accion2").val("");
    });


    $("[name='PersonaBicicleta']").on("click", function() {
        if ($("#PersonaBicicleta:checked").val() == 1) {
            $(".divSocio").show("slow");
            $(".divInvitado").hide("slow");
        } else {
            $(".divSocio").hide("slow");
            $(".divInvitado").show("slow");
        }

    });

    function agregarAccesorio(){

        let nombre = $("#NombreAccesorio").val();
        let marca = $("#Marca").val();
        let color = $("#Color").val();
        let estado = $("#EstadoAccesorio").val();

        if(nombre == ""){
            alert('<?= SIMUtil::get_traduccion('', '', 'elnombredelaccesorionopuedeservacio', LANGSESSION);?>');
        }else{

            $("#NombreAccesorio").val("");
            $("#Marca").val("");
            $("#Color").val("");
            $("#EstadoAccesorio").val("");

            let arrAccesorio= {
                "Nombre": nombre,
                "Marca": marca,
                "Color": color,
                "Estado": estado
            }

            arrAccesorios.push(arrAccesorio);
            cargarAccesorios();
        }
    }

    function eliminarAccesorio(keyAccesorio){
        arrAccesorios.splice(keyAccesorio, 1);
        cargarAccesorios();
    }

    function cargarAccesorios(){
        $("#listaAccesorios").html("");
        $("#Accesorios").val("");
        console.log(arrAccesorios);
        if(arrAccesorios.length != 0){
            for(let key in arrAccesorios){

                body = "<tr>";
                body += "<td>"+arrAccesorios[key].Nombre+"</td>";
                body += "<td>"+arrAccesorios[key].Marca+"</td>";
                body += "<td>"+arrAccesorios[key].Color+"</td>";
                body += "<td>"+arrAccesorios[key].Estado+"</td>";
                body += "<td align='center' valign='middle'><button onclick=\"eliminarAccesorio("+key+")\" type='button' class='button_style'><i class='ace-icon fa fa-trash red'></i></button></td>";
                body += "</tr>";

                $("#listaAccesorios").append(body);
            }

            $("#Accesorios").val(JSON.stringify(arrAccesorios));
        }
    }
</script>