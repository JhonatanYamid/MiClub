
<?
    $arrPalos = array();

    if(SIMNet::req("action") == 'edit' || SIMNet::req("action") == 'admin'){
        $sqlPalos = "SELECT IDTalegaPalos, NombrePalo, Marca, Color, Estado FROM TalegaPalos WHERE IDtalega = ".SIMNet::reqInt("id");
        $qryPalos = $dbo->query($sqlPalos);

        while ($rPalos = $dbo->fetchArray($qryPalos)){
            $arrPalos[] = $rPalos;
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
            <form class="form-horizontal formvalida" role="form" method="post" id="frmNuevaTalega" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

                <div class="widget-header widget-header-large">
                    <h3 class="widget-title grey lighter">
                        <i class="ace-icon fa fa-users green"></i>
                        Datos Basicos
                    </h3>
                </div>

                <?php if ($action != "admin") { ?>
                    <div  class="form-group first ">
                        <div  class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> Nombre</label>

                            <div class="col-sm-8">
                                <input type="text" id="nombre" name="nombre" placeholder="Nombre de la talega" class="col-xs-12 mandatory" title="nombre de talega" value="<?php echo $frm["nombre"]; ?>" >
                            </div>
                        </div>
                        <?php
                            $tipo = '';
                            $idInvitado = '';
                            $invitado = '';
                            $displaySocio = "inline";
                            $displayInvitado = "none";

                            if($frm['IDInvitado'])
                            {
                                $tipo = 2;
                                $idInvitado = $frm['IDInvitado'];

                            }
                            else if($frm['IDSocioInvitado'])
                            {
                                $tipo = 1;
                                $idInvitado = $frm['IDSocioInvitado'];
                            }

                            if($tipo == 1)
                            {
                                $sql_socio_club = "Select * From SocioInvitado Where IDSocioInvitado = ".$idInvitado;
                                $qry_socio_club = $dbo->query($sql_socio_club);
                                $rData = $dbo->fetchArray($qry_socio_club);
                                $invitado = $rData["Nombre"];
                            }
                            else if($tipo == 2)
                            {
                                $sql_socio_club = "Select * From Invitado Where IDInvitado = ".$idInvitado;
                                $qry_socio_club = $dbo->query($sql_socio_club);
                                $rData = $dbo->fetchArray($qry_socio_club);
                                $invitado = $rData["Nombre"]." ".$rData["Apellido"];
                            }

                            if($idInvitado > 0)
                            {
                                    $displaySocio = "none";
                                    $displayInvitado = "display";
                            }
                        ?>
                        <div  class="col-xs-12 col-sm-6">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <label class="control-label" ><input <?php echo $idInvitado == "" ? 'checked' : ""; ?> type="radio" id="personaTalega" name="personaTalega" value="1" > Socio </label>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label" ><input <?php echo $idInvitado > 0 ? 'checked' : ""; ?> type="radio" id="personaTalega" name="personaTalega" value="2" > Invitado </label>
                            </div>
                        </div>

                    </div>

                    <div  class="form-group first ">

                        <div  class="col-xs-12 col-sm-6 divSocio" style="display:<?php echo $displaySocio; ?>;">
                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>
                            <div class="col-sm-8">
                                <?php
                                $sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
                                $qry_socio_club = $dbo->query($sql_socio_club);
                                $r_socio = $dbo->fetchArray($qry_socio_club);
                                ?>

                                <input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax" title="número de derecho"  value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>" >
                                <input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio"  title="Socio">
                            </div>
                        </div>

                        <div  class="col-xs-12 col-sm-6 divInvitado" style="display:<?php echo $displayInvitado; ?>;">
                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Invitado </label>
                            <div class="col-sm-8">

                                <input type="text" id="Accion2" name="Accion2" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-invitadosIntegrados" title="Nombre invitado" value="<?php echo $invitado; ?>" >
                                <input type="hidden" name="IDInvitado" value="<?php echo $idInvitado; ?>" id="IDInvitado">
                                <input type="hidden" name="tipoInvitado" value="<?php echo $tipo; ?>" id="tipoInvitado">
                            </div>
                        </div>

                    </div>

                    <div  class="form-group first " style="display:<?php echo $displaySocio; ?>;">

                        <div  class="col-xs-12 col-sm-12">
                            <label class="col-sm-2 control-label no-padding-right " for="form-field-1"> Localización </label>
                            <div class="col-sm-10">
                                <textarea id="localizacion" name="localizacion" placeholder="Localización de la talega" class="col-xs-12" ><?php echo $frm["localizacion"]; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div  class="form-group first ">
                        <?php
                            $sqlCod = "SELECT tipoCodigo FROM Talega WHERE IDTalega = ". SIMNet::reqInt("id");
                            $resultCod = $dbo->query($sqlCod);

                            while($rowCod = $dbo->fetchArray($resultCod)){
                                $tipoCodigo = $rowCod['tipoCodigo'];
                            }
                        ?> 

                        <div  class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"> </label>
                            <div class="col-sm-8" style="vertical-align: middle;">
                                <label><input <?php echo $tipoCodigo == 1 ? 'checked' : ""; ?>  type="radio" id="tipoCodigo" name="tipoCodigo" value="1" /> Codigo de barras</label>
                            </div>
                        </div>

                        <div  class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1"></label>
                            <div class="col-sm-8">
                                <label><input <?php echo $tipoCodigo == 2 ? 'checked' : ""; ?> type="radio" id="tipoCodigo" name="tipoCodigo" value="2" /> Codigo QR </label>
                            </div>
                        </div>

                    </div>

                    <?php
                } else {
                    ?>
                    <div  class="form-group first ">
                        <div  class="col-xs-12 col-sm-4">
                            Nombre: <?php echo $frm["nombre"]; ?>
                        </div>
                        <div  class="col-xs-12 col-sm-4">
                            Socio / Invitado: <?php echo $frm["socio"]; ?>
                        </div>
                        <div  class="col-xs-12 col-sm-4">
                            Codigo: <?php echo $frm["codigo"]; ?>
                        </div>
                        <input type="hidden" id="estado" name="estado" value="<?php echo $frm["estado"]; ?>" />
                        <input type="hidden" id="idSocio" name="idSocio" value="<?php echo $frm["IDSocio"]; ?>" />
                    </div>
                    <div  class="form-group first ">
                        <div  class="col-xs-12 col-sm-4">
                            Localizacion: <?php echo $frm["localizacion"]; ?>
                        </div>
                    </div>

                    <div  class="form-group first ">
                        <div  class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1">
                                <?php
                                if ($frm["estado"] == 1 || $frm["estado"] == 4)
                                    echo 'Recibe';
                                else
                                    echo 'Entrega';
                                ?> Tercero
                            </label>
                            <div class="col-sm-8">
                                <input style="height:30px;" checked="" type="radio" id="recibeTercero" name="recibeTercero" value="1" >
                            </div>
                        </div>

                        <div  class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1">
                                <?php
                                if ($frm["estado"] == 1 || $frm["estado"] == 4)
                                    echo 'Recibe';
                                else
                                    echo 'Entrega';
                                ?> Socio/Invitado
                            </label>
                            <div class="col-sm-8">
                                <input style="height:30px;" type="radio" id="recibeTercero" name="recibeTercero" value="0">
                            </div>
                        </div>
                    </div>

                    <div  class="form-group first tercero">
                        <div  class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right " for="form-field-1">Numero documento</label>

                            <div class="col-sm-8">
                                <input type="text" id="numeroDocumentoTercero" name="numeroDocumentoTercero" placeholder="Numero documento del tercero" class="col-xs-12" title="Numero documento del tercero" value="" >
                            </div>
                        </div>

                        <div  class="col-xs-12 col-sm-6">
                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Nombre </label>
                            <div class="col-sm-8">
                                <input type="text" id="nombreTercero" name="nombreTercero" placeholder="Nombre del tercero" class="col-xs-12" title="Nombre del tercero" value="" >
                            </div>
                        </div>
                    </div>


                    <?php if ($frm["estado"] == 4 || $frm["estado"] == 1){ ?>
                        <div  class="form-group first">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Lugar de Entrega </label>
                                <div class="col-sm-8"> 
                                    <?php
                                        $sql_lugar = "SELECT IDConfiguracionTalegasLugar,Nombre FROM ConfiguracionTalegasLugar WHERE IDClub = '" . SIMUser::get("club") . "' Order by Nombre";
                                        $result_lugar = $dbo->query($sql_lugar);
                                    ?> 
                                    <select name="IDConfiguracionTalegasLugarEntrega" id="IDConfiguracionTalegasLugarEntrega" class="form-control">
                                        <option value="">[Seleccione Lugar]</option> 
                                        <?php
                                            while ($row_lugar = $dbo->fetchArray($result_lugar)) { ?> 
                                                <option value="<?php echo $row_lugar["IDConfiguracionTalegasLugar"];  ?>" <?php if ($frm["IDConfiguracionTalegasLugarEntrega"] == $row_lugar["IDConfiguracionTalegasLugar"]) echo "selected"; ?>><?php echo $row_lugar["Nombre"];  ?></option> <?php } ?>
                                    </select> 
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div  class="form-group first ">

                        <div  class="col-xs-12 col-sm-12">
                            <label class="col-sm-2 control-label no-padding-right " for="form-field-1"> Observaciones </label>
                            <div class="col-sm-10">
                                <textarea id="observaciones" name="observaciones" placeholder="Observaciones" class="col-xs-12" ></textarea>
                            </div>
                        </div>
                    </div>

                    <?php
                }
                ?>
                <hr>
                <?php
                $indexFinal = count($aPropiedades) - 1;
                foreach ($aPropiedades AS $index => $propiedad) {
                    $content = "";
                    $valor = $aPropiedadesTalega[$propiedad["IDPropiedadesTalega"]] == "" ? 0 : $aPropiedadesTalega[$propiedad["IDPropiedadesTalega"]];

                    $contentProp = '<div  class="col-xs-12 col-sm-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">'. $propiedad["nombre"].'</label>

                                        <div class="col-sm-8">
                                            <input type="hidden" id="idPropiedad[]" name="idPropiedad[]" value="'.$propiedad["IDPropiedadesTalega"].'" >
                                            <input type="hidden" id="nombrePropiedad[]" name="nombrePropiedad[]" value="'.$propiedad["nombre"].'" >
                                            <input type="text" id="propiedad[]" name="propiedad[]"  placeholder="Digite un valor para '.$propiedad["nombre"].'" class="col-xs-12" value="'.$valor.'" >
                                        </div>
                                    </div>';
                    
                    $contentObservacion = '<div  class="col-xs-12 col-sm-6">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Observacion</label>

                                                <div class="col-sm-8">
                                                    <textarea id="observacion[]" name="observacion[]" placeholder="Digite la observacion para '.$propiedad["nombre"].'" class="col-xs-12" >'.$propiedad["Observacion"].'</textarea>
                                                </div>
                                            </div>';

                    if ($frm["estado"] != 3){
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
                <!--Admin Palos -->
                <div class="widget-header widget-header-large no-margin">
                    <h4 class="widget-title grey lighter">
                        <i class="ace-icon fas fa-golf-ball green"></i> <?= ucwords(SIMUtil::get_traduccion('', '', 'administrarpalos', LANGSESSION));?>
                    </h4>
                </div>
                <div class="form-group first ">
                    <div class="col-xs-12 col-xs-12">
                        <center>
                            <div class="p first">
                                <table id="simple-table" class="table table-striped table-bordered table-hover" style="width:80%">
                                    <thead id="headPalos">
                                        <tr>
                                            <th style="width:30%"><?= SIMUtil::get_traduccion('', '', 'nombrepalo', LANGSESSION);?></th>
                                            <th align='center' valign='middle' style="width:20%"><?= SIMUtil::get_traduccion('', '', 'Marca', LANGSESSION);?></th>
                                            <th align='center' valign='middle' style="width:20%"><?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION);?></th>
                                            <th align='center' valign='middle' style="width:20%"><?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION);?></th>
                                            <th align='center' valign='middle' style="width:10%"><?= SIMUtil::get_traduccion('', '', 'Accion', LANGSESSION);?></th>
                                        </tr>
                                        <tr>
                                            <td><input style="width: 100%;" type="text" id="NombrePalo" name="NombrePalo" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombrepalo', LANGSESSION); ?>" value="" /></td>
                                            <td><input type="text" id="Marca" name="Marca" placeholder="<?= SIMUtil::get_traduccion('', '', 'Marca', LANGSESSION); ?>" value="" /></td>
                                            <td><input type="text" id="Color" name="Color" placeholder="<?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION); ?>" value="" /></td>
                                            <td><input type="text" id="Estado" name="Estado" placeholder="<?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?>" value="" /></td>
                                            <td align='center' valign='middle'><button onclick="agregarPalo()" type='button' class='button_style'><i class='ace-icon fa fa-plus blue'></i></button></td>
                                        </tr>
                                    <thead>
                                    <tbody id="listaPalos"></tbody>
                                </table>
                            </div>
                            <input type="hidden" id="Palos" name="Palos" value="" />
                        </center>
                    </div>
                </div>
                
                <div class="clearfix form-actions">
                    <div class="col-xs-12 text-center">
                        <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                        <button class="btn btn-info btnRegistrarTalega form-control" type="button" rel="frmNuevaTalega" >
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            <?php echo $titulo_accion; ?> TALEGA
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
include( "list" );
include( "cmp/footer_scripts.php" );
?>

<script type="text/javascript">

    let arrPalos = <? echo json_encode($arrPalos); ?>;
    cargarPalos();
    
    $(".btnRegistrarTalega").on("click", function (e)
    {
        var propiedadesVacias = 0;
        if($("[name='propiedad[]']").length > 0)
        {
        $.each($("[name='propiedad[]']"), function (index, objeto)
        {
            if ($(objeto).val() == "")
            {
                propiedadesVacias++;
            }

        });
        }
        else
        {
            propiedadesVacias++;
        }

        var continua = 0;
        if($("[name='personaTalega']").is(":checked"))
        {
            if($("[name='personaTalega']:checked").val() == 1 && $("#IDSocio").val() == "")
            {
                continua++;
                alert("Debe seleccionar un socio");
            }
            else if($("[name='personaTalega']:checked").val() == 2 && $("#IDInvitado").val() == "")
            {
                continua++;
                 alert("Debe seleccionar un invitado");
            }
        }

        var admin = 0;
        if ($("input[name='recibeTercero']:checked").val() == 1)
        {
            if ($("#numeroDocumentoTercero").val() == "" || $("#nombreTercero").val() == "")
            {
                admin++;
                alert("Debe digitar un numero de documento para el tercero y su nombre");
            }
        }

        if(propiedadesVacias > 0 || admin > 0)
        {
             alert("Debe registrar un valor en las propiedades de la talega");
        }


        if (propiedadesVacias == 0 && admin == 0 && continua == 0)
        {
            var form = $(this).attr("rel");
            $("#" + form).submit();
        }

    });

    $("input[name='recibeTercero']").on("click", function ()
    {
        var valor = $(this).val();
        if (valor == 1)
            $(".tercero").show("slow");
        else
            $(".tercero").hide("slow");

    });


    $("#Accion").on("keyup", function ()
    {
        $("#IDSocio").val("");
    });

    $("#Accion").on("blur", function ()
    {
        if ($("#IDSocio").val() == "")
            $("#Accion").val("");
    });


    $("#Accion2").on("keyup", function ()
    {
        $("#IDInvitado").val("");
        $("#tipoInvitado").val("");
    });

    $("#Accion2").on("blur", function ()
    {
        if ($("#IDInvitado").val() == "")
            $("#Accion2").val("");
    });


    $("[name='personaTalega']").on("click", function()
    {
        if($("#personaTalega:checked").val() == 1)
        {
            $(".divSocio").show("slow");
            $(".divInvitado").hide("slow");
        }
        else
        {
            $(".divSocio").hide("slow");
            $(".divInvitado").show("slow");
        }

    });

    function agregarPalo(){

        let nombrePalo = $("#NombrePalo").val();
        let marca = $("#Marca").val();
        let color = $("#Color").val();
        let estado = $("#Estado").val();

        if(nombrePalo == ""){
            alert('<?= SIMUtil::get_traduccion('', '', 'elnombredelpalonopuedeservacio', LANGSESSION);?>');
        }else{

            $("#NombrePalo").val("");
            $("#Marca").val("");
            $("#Color").val("");
            $("#Estado").val("");

            let arrPalo= {
                "NombrePalo": nombrePalo,
                "Marca": marca,
                "Color": color,
                "Estado": estado
            }

            arrPalos.push(arrPalo);
            cargarPalos();
        }
    }

    function eliminarPalo(keyPalo){
        arrPalos.splice(keyPalo, 1);
        cargarPalos();
    }

    function cargarPalos(){
        $("#listaPalos").html("");
        $("#Palos").val("");
        console.log(arrPalos);
        if(arrPalos.length != 0){
            for(let key in arrPalos){

                body = "<tr>";
                body += "<td>"+arrPalos[key].NombrePalo+"</td>";
                body += "<td>"+arrPalos[key].Marca+"</td>";
                body += "<td>"+arrPalos[key].Color+"</td>";
                body += "<td>"+arrPalos[key].Estado+"</td>";
                body += "<td align='center' valign='middle'><button onclick=\"eliminarPalo("+key+")\" type='button' class='button_style'><i class='ace-icon fa fa-trash red'></i></button></td>";
                body += "</tr>";

                $("#listaPalos").append(body);
            }
        
            $("#Palos").val(JSON.stringify(arrPalos));
        }
    }

</script>