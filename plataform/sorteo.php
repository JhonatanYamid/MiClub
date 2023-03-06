<?
include("procedures/general.php");
include("procedures/sorteo.php");


include("cmp/seo.php");



?>
</head>

<body class="no-skin">
    <?
    include("cmp/header.php");
    ?>
    <div class="main-container" id="main-container">
        <script type="text/javascript">
        try {
            ace.settings.check('main-container', 'fixed')
        } catch (e) {}
        </script>
        <?
        $menu_reservas[$ids] = " class=\"active\" ";
        include("cmp/menu.php");
        $ids = $_GET["ids"];
        ?>
        <div class="main-content">
            <div class="main-content-inner">
                <div class="breadcrumbs" id="breadcrumbs">
                    <script type="text/javascript">
                    try {
                        ace.settings.check('breadcrumbs', 'fixed')
                    } catch (e) {}
                    </script>
                    <ul class="breadcrumb">
                        <li>
                            <i class="ace-icon fa fa-home home-icon"></i>
                            <a href="reservas.php?ids=<?= $_GET["ids"] ?>">Home</a>
                        </li>
                        <li>
                            <a href=""><?= $datos_club["Nombre"] ?></a>
                        </li>
                        <li class="active"><a href="reservas.php?ids=<?= $_GET["ids"] ?>">Reservas <?= $nombre_servicio_personalizado; ?></a></li>
                    </ul><!-- /.breadcrumb -->
                </div>
                <div class="page-content">
                    <div class="page-header">
                        <h1>
                            <i class="ace-icon fa fa-angle-double-right"></i> Reservas <?= $nombre_servicio_personalizado; ?> <small>
                                <i class="ace-icon fa fa-angle-double-right"></i> Listado de Reservas Eliminadas </small>
                        </h1>
                    </div><!-- /.page-header -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="tabbable">
                                <ul class="nav nav-tabs" id="myTab">

                                    <?php if($datos_servicio[$_GET["ids"]][TipoSorteo] == 1):?>										
                                    <li>
                                        <a data-toggle="tab" class="noTabLink" href="reservassorteo.php?action=edit&ids=<?= $_GET["ids"] ?>">
                                        <i class="green ace-icon fa fa-trophy bigger-120"></i> Inscritos Sorteo </a>
                                    </li>
                                    <?php else: ?> 
                                    <li>
                                        <a data-toggle="tab" href="reservas.php?ids=<?php echo $_GET["ids"]; ?>">
                                            <i class="green ace-icon fa fa-calendar bigger-120"></i>
                                            Reservas
                                        </a>
                                    </li>
                                    <?php endif; ?>								


                                    <?php								
                                    $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoConfiguracion");
                                    if (
                                        SIMUser::get("IDPerfil") <= 2 || SIMUser::get("IDPerfil") == 21 || SIMUser::get("IDPerfil") == 22 || SIMUser::get("IDPerfil") == 23 || SIMUser::get("IDPerfil") == 27 || SIMUser::get("IDPerfil") == 31
                                        || SIMUser::get("IDPerfil") == 32 || SIMUser::get("IDPerfil") == 30 || SIMUser::get("IDPerfil") == 10 || SIMUser::get("IDPerfil") == 7 || $Permiso == 1
                                    ) : ?>
                                        <li>
                                            <a data-toggle="tab" class="noTabLink" href="serviciosclub.php?action=edit&ids=<?= $ids ?>">
                                                <i class="green ace-icon fa fa-gear bigger-120"></i>
                                                Configuración
                                            </a>
                                        </li>

                                        <?php if($datos_servicio[$_GET["ids"]][TipoSorteo] == 1):?>
                                        <li class="active">
                                            <a data-toggle="tab" class="noTabLink" href="sorteo.php?action=edit&ids=<?= $_GET["ids"] ?>">
                                            <i class="green ace-icon fa fa-trophy bigger-120"></i> Sorteo </a>
                                        </li>										
                                        <?php endif; ?> 
                                        
                                    <?php endif;
                                    $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoExportar");
                                    if ($Permiso == 1 && $datos_servicio[$_GET["ids"]][TipoSorteo] == 0) :
                                    ?>
                                        <li>
                                            <a data-toggle="tab" class="noTabLink" href="exportareserva.php?action=edit&ids=<?= $ids ?>">
                                                <i class="green ace-icon fa fa-download bigger-120"></i>
                                                Exportar Reservas
                                            </a>
                                        </li>
                                        <li>
                                            <a data-toggle="tab" class="noTabLink" href="exportarsanciones.php?action=edit&ids=<?= $ids ?>">
                                                <i class="green ace-icon fa fa-download bigger-120"></i>
                                                Exportar Sanciones
                                            </a>
                                        </li>

                                        <li>
                                            <a data-toggle="tab" class="noTabLink" href="exportareservaeliminada.php?action=edit&ids=<?= $ids ?>">
                                                <i class="green ace-icon fa fa-download bigger-120"></i>
                                                Exportar Reservas Eliminadas
                                            </a>
                                        </li>										

                                    <?php endif; ?>
                                    <?php if($datos_servicio[$_GET["ids"]][TipoSorteo] == 0):?>
                                    <li>
                                        <a data-toggle="tab" class="noTabLink" href="listaespera.php?action=edit&ids=<?= $ids ?>">
                                            <i class="green ace-icon fa fa-bell-o bigger-120"></i>
                                            Inscritos Lista de espera
                                        </a>
                                    </li>
                                    <?php endif; ?>


                                    <?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 112) { ?>
                                        <li>
                                            <a data-toggle="tab" class="noTabLink" href="cargamasivareservas.php?action=edit&ids=<?= $ids ?>">
                                                <i class="green ace-icon fa fa-bell-o bigger-120"></i>
                                                Cargar reservas
                                            </a>
                                        </li>
                                    <?php } ?>

                                </ul>
                                <div class="tab-content">
                                    <div id="home" class="tab-pane fade in active">
                                        <div class="widget-box transparent" id="recent-box">
                                            <div class="widget-body">
                                                <div class="widget-main padding-4">
                                                    <div class="row">
                                                        <div class="col-sm-12 widget-container-col ui-sortable">
                                                            <form class="form-horizontal formvalida" role="form" method="POST" name="frm" id="frm" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data"> 
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Servicio donde se haran las reservas del sorteo </label>
                                                                        <div class="col-sm-8">
                                                                            <select name="IDServicioAsociado" class="form-control"> 
                                                                                <option>[SELECCIONA UNA OPCION]</option>
                                                                                <?php
                                                                                $SQLServicio = "SELECT * from ServicioClub Where IDClub = '".SIMUser::get("club")."' and Activo='S'";
                                                                                $QRYServicio = $dbo->query($SQLServicio);
                                                                                while($Datos = $dbo->fetchArray($QRYServicio)):
                                                                                    $IDServicio = $dbo->getFields("Servicio", "IDServicio", "IDServicioMaestro = '$Datos[IDServicioMaestro]' and IDClub = '$Datos[IDClub]'");
                                                                                    if(!empty($Datos[TituloServicio]))	{
                                                                                        $NombreServicio = $Datos[TituloServicio];
                                                                                    }
                                                                                    else{
                                                                                        $NombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '$Datos[IDServicioMaestro]'");
                                                                                    }
                                                                                    if($IDServicio != $_GET[ids]):
                                                                                        ?>                                                                                   
                                                                                        <option value="<?php echo $IDServicio ?>" <?php if($IDServicio==$frm[IDServicioAsociado]) echo "selected"?>> <?=$NombreServicio?> </option>
                                                                                        <?php
                                                                                    endif;
                                                                                endwhile;
                                                                                
                                                                            ?> </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 " title="Nombre" value="<?php echo $frm["Nombre"] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cantidad Turnos en los que puede estar el socio </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="number" id="CantidadTurnos" name="CantidadTurnos" placeholder="" class="col-xs-12 " title="fecha fin" value="<?php echo $frm["CantidadTurnos"] ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Minimo Invitados (sin incluir socio) </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="number" id="MinimoInvitadosSeleccion" name="MinimoInvitadosSeleccion" placeholder="" class="col-xs-12 " title="Minimo Invitados Seleccion" value="<?php echo $frm["MinimoInvitadosSeleccion"] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Intro SeleccionFecha </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="IntroSeleccionFecha" name="IntroSeleccionFecha" placeholder="" class="col-xs-12 " title="Intro Seleccion Fecha" value="<?php echo $frm["IntroSeleccionFecha"] ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Maximo Invitados (sin incluir socio) </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="number" id="MaximoInvitadosSeleccion" name="MaximoInvitadosSeleccion" placeholder="" class="col-xs-12 " title="MaximoInvitadosSeleccion" value="<?php echo $frm["MaximoInvitadosSeleccion"] ?>">
                                                                        </div>
                                                                    </div>                                                                    
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Minutos Reserva </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="number" id="MinutosReserva" name="MinutosReserva" placeholder="" class="col-xs-12 " title="MinutosReserva" value="<?php echo $frm["MinutosReserva"] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Dias Adelante </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="number" id="NumeroDiasAdelante" name="NumeroDiasAdelante" placeholder="" class="col-xs-12 " title="Numero Dias Adelante" value="<?php echo $frm["NumeroDiasAdelante"] ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Boton Agregar Invitado </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="LabelBotonAgregarInvitado" name="LabelBotonAgregarInvitado" placeholder="" class="col-xs-12 " title="Label BotonAgregar Invitado" value="<?php echo $frm["LabelBotonAgregarInvitado"] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Agregar Invitado Socio </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="LabelAgregarInvitadoSocio" name="LabelAgregarInvitadoSocio" placeholder="" class="col-xs-12 " title="Label Agregar Invitado Socio" value="<?php echo $frm["LabelAgregarInvitadoSocio"] ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Agregar Invitado Externo </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="LabelAgregarInvitadoExterno" name="LabelAgregarInvitadoExterno" placeholder="" class="col-xs-12 " title="Label Agregar Invitado Externo" value="<?php echo $frm["LabelAgregarInvitadoExterno"] ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Intro Agregar Invitado </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="IntroAgregarInvitado" name="IntroAgregarInvitado" placeholder="" class="col-xs-12 " title="Intro Agregar Invitado" value="<?php echo $frm["IntroAgregarInvitado"] ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Sorteo </label>
                                                                        <div class="col-sm-8">
                                                                            
                                                                            <select name="HoraSorteo" id="HoraSorteo">
                                                                                <option value=""></option>
                                                                                <option value="01:00:00" <?php if ($frm["HoraSorteo"] == "01:00:00") echo "selected"; ?>>01:00:00</option>
                                                                                <option value="02:00:00" <?php if ($frm["HoraSorteo"] == "02:00:00") echo "selected"; ?>>02:00:00</option>
                                                                                <option value="03:00:00" <?php if ($frm["HoraSorteo"] == "03:00:00") echo "selected"; ?>>03:00:00</option>
                                                                                <option value="04:00:00" <?php if ($frm["HoraSorteo"] == "04:00:00") echo "selected"; ?>>04:00:00</option>
                                                                                <option value="05:00:00" <?php if ($frm["HoraSorteo"] == "05:00:00") echo "selected"; ?>>05:00:00</option>
                                                                                <option value="06:00:00" <?php if ($frm["HoraSorteo"] == "06:00:00") echo "selected"; ?>>06:00:00</option>
                                                                                <option value="07:00:00" <?php if ($frm["HoraSorteo"] == "07:00:00") echo "selected"; ?>>07:00:00</option>
                                                                                <option value="08:00:00" <?php if ($frm["HoraSorteo"] == "08:00:00") echo "selected"; ?>>08:00:00</option>
                                                                                <option value="09:00:00" <?php if ($frm["HoraSorteo"] == "09:00:00") echo "selected"; ?>>09:00:00</option>
                                                                                <option value="10:00:00" <?php if ($frm["HoraSorteo"] == "10:00:00") echo "selected"; ?>>10:00:00</option>
                                                                                <option value="11:00:00" <?php if ($frm["HoraSorteo"] == "11:00:00") echo "selected"; ?>>11:00:00</option>
                                                                                <option value="12:00:00" <?php if ($frm["HoraSorteo"] == "12:00:00") echo "selected"; ?>>12:00:00</option>
                                                                                <option value="13:00:00" <?php if ($frm["HoraSorteo"] == "13:00:00") echo "selected"; ?>>13:00:00</option>
                                                                                <option value="14:00:00" <?php if ($frm["HoraSorteo"] == "14:00:00") echo "selected"; ?>>14:00:00</option>
                                                                                <option value="15:00:00" <?php if ($frm["HoraSorteo"] == "15:00:00") echo "selected"; ?>>15:00:00</option>
                                                                                <option value="16:00:00" <?php if ($frm["HoraSorteo"] == "16:00:00") echo "selected"; ?>>16:00:00</option>
                                                                                <option value="17:00:00" <?php if ($frm["HoraSorteo"] == "17:00:00") echo "selected"; ?>>17:00:00</option>
                                                                                <option value="18:00:00" <?php if ($frm["HoraSorteo"] == "18:00:00") echo "selected"; ?>>18:00:00</option>
                                                                                <option value="19:00:00" <?php if ($frm["HoraSorteo"] == "19:00:00") echo "selected"; ?>>19:00:00</option>
                                                                                <option value="20:00:00" <?php if ($frm["HoraSorteo"] == "20:00:00") echo "selected"; ?>>20:00:00</option>
                                                                                <option value="21:00:00" <?php if ($frm["HoraSorteo"] == "21:00:00") echo "selected"; ?>>21:00:00</option>
                                                                                <option value="22:00:00" <?php if ($frm["HoraSorteo"] == "22:00:00") echo "selected"; ?>>22:00:00</option>
                                                                                <option value="23:00:00" <?php if ($frm["HoraSorteo"] == "23:00:00") echo "selected"; ?>>23:00:00</option>
                                                                                <option value="24:00:00" <?php if ($frm["HoraSorteo"] == "24:00:00") echo "selected"; ?>>24:00:00</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dia Sorteo </label>
                                                                        <div class="col-sm-8">
                                                                            <?php
                                                                           
                                                                            array_pop($array_dias);
                                                                            foreach ($Dia_array as $id_dia => $dia) :  ?>
                                                                                <input type="radio" name="DiaSorteo" id="DiaSorteo" value="<?php echo $id_dia; ?>" <?php if ($frm[DiaSorteo] == $id_dia) echo "checked"; ?>><?php echo $dia; ?>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div>
                                                                   
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite Invitado Externo Cedula</label>
                                                                        <div class="col-sm-8"> <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteInvitadoExternoCedula"], "PermiteInvitadoExternoCedula", "title=\"Permite Invitado Externo Cedula\"") ?> </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite Invitado Externo Correo</label>
                                                                        <div class="col-sm-8"> <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteInvitadoExternoCorreo"], "PermiteInvitadoExternoCorreo", "title=\"Permite Invitado Externo Correo\"") ?> </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite Invitado Externo Fecha Nacimiento</label>
                                                                        <div class="col-sm-8"> <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteInvitadoExternoFechaNacimiento"], "PermiteInvitadoExternoFechaNacimiento", "title=\"Permite Invitado Externo Fecha Nacimiento\"") ?> </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Solo Icono</label>
                                                                        <div class="col-sm-8"> <?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["SoloIcono"], "SoloIcono", "title=\"Solo Icono\"") ?> </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono </label>
                                                                        <input name="Icono" id=file class="" title="Icono" type="file" size="25" style="font-size: 10px">
                                                                        <div class="col-sm-8">
                                                                            <? if (!empty($frm["Icono"])) {
                                                                                echo "<img src='" . SERVICIO_ROOT . $frm["Icono"] . "' width='150px' height='150px' >";
                                                                            ?>
                                                                            <a href="<? echo $script . " .php?action=delfoto&foto=$frm[Icono]&campo=Icono&ids=" . $_GET[" ids"]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                                                                            <?
                                                                            } // END if
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo</label>
                                                                        <div class="col-sm-8">
                                                                            <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["Activo"], 'Activo', "class='input'") ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group first ">
                                                                    <script>
                                                                        function sortear() {
                                                                                var FechaFija = document.getElementById("FechaSorteo").value;                                                                                
                                                                                window.location.href = "sorteocron.php?FechaFija=" + FechaFija + "&IDServicio=" + <?php echo $_GET[ids]?>;
                                                                                return false;
                                                                            }                                                                        
                                                                    </script>

                                                                    <div class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha a Sortear </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="FechaSorteo" name="" placeholder="" class="col-xs-12 calendar" title="Fecha Sorteo" value="">
                                                                            <br><br>
                                                                            <button class="btn btn-info btnEnviar" type="button" onClick="sortear();"> Sortear </button>
                                                                        </div>
                                                                    </div>                                                                    
                                                                </div>
                                                                
                                                                <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                                                <input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
                                                                <input type="hidden" name="IDServicio" id="IDServicio" value="<?php echo $_GET["ids"]; ?>">
                                                                <button class="btn btn-info btnEnviar" type="button" rel="frm"> Guardar </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div><!-- /.widget-main -->
                                            </div><!-- /.widget-body -->
                                        </div><!-- /.widget-box -->
                                        <script type="text/javascript">
                                        var $path_base = "."; //in Ace demo this will be used for editurl parameter
                                        </script>
                                        <!-- PAGE CONTENT ENDS -->
                                    </div> <!-- end tab -->
                                </div>
                            </div>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->
        <?
        include("cmp/footer.php");
        ?>
    </div><!-- /.main-container -->
    <?
    include("cmp/footer_grid.php");
    ?>
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
    jQuery(function($) {
        var grid_selector = "";
        var pager_selector = ""; < ? foreach($elementos[$ids] as $key_elemento => $datos_elemento) {
                $grillas[] = $key_elemento; ? > grid_selector = "#grid-table<?= $key_elemento ?>";
                pager_selector = "#grid-pager<?= $key_elemento ?>";
                jQuery(grid_selector).jqGrid({
                    url: 'includes/async/reservas.async.php?idservicio=<?= $ids ?>&idelemento=<?= $datos_elemento["IDElemento"] ?><?= $url_search ?>',
                    datatype: "json",
                    colNames: ['Fecha', 'Hora', 'Socio', 'Cancelar Reserva.'],
                    colModel: [{
                        name: 'Fecha',
                        index: 'Fecha',
                        align: "center"
                    }, {
                        name: 'Hora',
                        index: 'Hora',
                        align: "left",
                        search: false
                    }, {
                        name: 'Socio',
                        index: 'Socio',
                        align: "left",
                        searchoptions: {
                            attr: {
                                placeholder: "Número de derecho o número de documento"
                            }
                        }
                    }, {
                        name: 'Cancelar',
                        index: 'Cancelar',
                        align: "center",
                        search: false
                    }, ],
                    rowNum: 100,
                    rowList: [100, 200, 300],
                    sortname: 'Hora',
                    viewrecords: true,
                    sortorder: "ASC",
                    caption: "Reservas",
                    height: "100%",
                    width: 855,
                    multiselect: false,
                    editurl: "includes/reservas.async.php",
                    pager: pager_selector,
                    altRows: true,
                    //toppager: true,
                    //multikey: "ctrlKey",
                    multiboxonly: true,
                    loadComplete: function() {
                        var table = this;
                        setTimeout(function() {
                            styleCheckbox(table);
                            updateActionIcons(table);
                            updatePagerIcons(table);
                            enableTooltips(table);
                        }, 0);
                        preparaform();
                    },
                    onSelectRow: function(id) {
                        //var IDSocio = $(this).attr("rel");
                        //var IDReserva = $(this).attr("id");
                        //var IDClub = $(this).attr("lang");
                        if (confirm("Esta seguro que desea cancelar la reserva?")) {
                            jQuery.ajax({
                                "type": "POST",
                                "data": {
                                    "IDReservaGeneral": id
                                },
                                "dataType": "json",
                                "url": "includes/async/cancela_reserva.async.php",
                                "success": function(data) {
                                    alert("Reserva Cancelada con exito");
                                    $("#grid-table<?= $key_elemento ?>").trigger("reloadGrid");
                                    return false;
                                }
                            });
                        }
                        return false;
                    },
                });
                $(grid_selector).jqGrid('setGridWidth', $("#grillasReserva .tab-content").width());
                $(grid_selector).jqGrid('sortGrid', 'Fecha', true, 'asc');
                $(grid_selector).jqGrid('sortGrid', 'Hora', true, 'asc');
                //resize to fit page size
                $(window).on('resize.jqGrid', function() {
                    $(grid_selector).jqGrid('setGridWidth', $("#grillasReserva .tab-content").width());
                })
                //resize on sidebar collapse/expand
                var parent_column = $(grid_selector).closest('[class*="col-"]');
                $(document).on('settings.ace.jqGrid', function(ev, event_name, collapsed) {
                    if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
                        //setTimeout is for webkit only to give time for DOM changes and then redraw!!!
                        setTimeout(function() {
                            $(grid_selector).jqGrid('setGridWidth', parent_column.width());
                        }, 0);
                    }
                }); < ?
            } //end for
            ? >
            var datePick = function(elem) {
                jQuery(elem).datepicker();
            }
        $(window).triggerHandler('resize.jqGrid'); //trigger window resize to make the grid get the correct size
        //enable search/filter toolbar
        jQuery(grid_selector).jqGrid('filterToolbar', {
            defaultSearch: true,
            stringResult: true
        })
        jQuery(grid_selector).filterToolbar({});
        //switch element when editing inline
        function aceSwitch(cellvalue, options, cell) {
            setTimeout(function() {
                $(cell).find('input[type=checkbox]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');
            }, 0);
        }
        //enable datepicker
        function pickDate(cellvalue, options, cell) {
            setTimeout(function() {
                $(cell).find('input[type=text]').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });
            }, 0);
        }
        //navButtons
        jQuery(grid_selector).jqGrid('navGrid', pager_selector, { //navbar options
            edit: false,
            editicon: 'ace-icon fa fa-pencil blue',
            add: false,
            addicon: 'ace-icon fa fa-plus-circle purple',
            del: false,
            delicon: 'ace-icon fa fa-trash-o red',
            search: true,
            searchicon: 'ace-icon fa fa-search orange',
            refresh: true,
            refreshicon: 'ace-icon fa fa-refresh green',
            view: true,
            viewicon: 'ace-icon fa fa-search-plus grey',
        }, {
            //edit record form
            //closeAfterEdit: true,
            //width: 700,
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        }, {
            //new record form
            //width: 700,
            closeAfterAdd: true,
            recreateForm: true,
            viewPagerButtons: false,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_edit_form(form);
            }
        }, {
            //delete record form
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                if (form.data('styled')) return false;
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                style_delete_form(form);
                form.data('styled', true);
            },
            onClick: function(e) {
                //alert(1);
            }
        }, {
            //search form
            recreateForm: true,
            afterShowSearch: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
                style_search_form(form);
            },
            afterRedraw: function() {
                style_search_filters($(this));
            },
            multipleSearch: true,
            /**
            multipleGroup:true,
            showQuery: true
            */
        }, {
            //view record form
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
            }
        })

        function style_edit_form(form) {
            //enable datepicker on "sdate" field and switches for "stock" field
            form.find('input[name=sdate]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            })
            form.find('input[name=stock]').addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');
            //don't wrap inside a label element, the checkbox value won't be submitted (POST'ed)
            //.addClass('ace ace-switch ace-switch-5').wrap('<label class="inline" />').after('<span class="lbl"></span>');
            //update buttons classes
            var buttons = form.next().find('.EditButton .fm-button');
            buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide(); //ui-icon, s-icon
            buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
            buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')
            buttons = form.next().find('.navButton a');
            buttons.find('.ui-icon').hide();
            buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
            buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');
        }

        function style_delete_form(form) {
            var buttons = form.next().find('.EditButton .fm-button');
            buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide(); //ui-icon, s-icon
            buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
            buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
        }

        function style_search_filters(form) {
            form.find('.delete-rule').val('X');
            form.find('.add-rule').addClass('btn btn-xs btn-primary');
            form.find('.add-group').addClass('btn btn-xs btn-success');
            form.find('.delete-group').addClass('btn btn-xs btn-danger');
        }

        function style_search_form(form) {
            var dialog = form.closest('.ui-jqdialog');
            var buttons = dialog.find('.EditTable')
            buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
            buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
            buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
        }

        function beforeDeleteCallback(e) {
            var form = $(e[0]);
            if (form.data('styled')) return false;
            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
            style_delete_form(form);
            form.data('styled', true);
        }

        function beforeEditCallback(e) {
            var form = $(e[0]);
            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
            style_edit_form(form);
        }
        //it causes some flicker when reloading or navigating grid
        //it may be possible to have some custom formatter to do this as the grid is being created to prevent this
        //or go back to default browser checkbox styles for the grid
        function styleCheckbox(table) {
            /**
            	$(table).find('input:checkbox').addClass('ace')
            	.wrap('<label />')
            	.after('<span class="lbl align-top" />')


            	$('.ui-jqgrid-labels th[id*="_cb"]:first-child')
            	.find('input.cbox[type=checkbox]').addClass('ace')
            	.wrap('<label />').after('<span class="lbl align-top" />');
            */
        }
        //unlike navButtons icons, action icons in rows seem to be hard-coded
        //you can change them like this in here if you want
        function updateActionIcons(table) {
            /**
            var replacement =
            {
            	'ui-ace-icon fa fa-pencil' : 'ace-icon fa fa-pencil blue',
            	'ui-ace-icon fa fa-trash-o' : 'ace-icon fa fa-trash-o red',
            	'ui-icon-disk' : 'ace-icon fa fa-check green',
            	'ui-icon-cancel' : 'ace-icon fa fa-times red'
            };
            $(table).find('.ui-pg-div span.ui-icon').each(function(){
            	var icon = $(this);
            	var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
            	if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
            })
            */
        }
        //replace icons with FontAwesome icons like above
        function updatePagerIcons(table) {
            var replacement = {
                'ui-icon-seek-first': 'ace-icon fa fa-angle-double-left bigger-140',
                'ui-icon-seek-prev': 'ace-icon fa fa-angle-left bigger-140',
                'ui-icon-seek-next': 'ace-icon fa fa-angle-right bigger-140',
                'ui-icon-seek-end': 'ace-icon fa fa-angle-double-right bigger-140'
            };
            $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function() {
                var icon = $(this);
                var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
                if ($class in replacement) icon.attr('class', 'ui-icon ' + replacement[$class]);
            })
        }

        function enableTooltips(table) {
            $('.navtable .ui-pg-button').tooltip({
                container: 'body'
            });
            $(table).find('.ui-pg-div').tooltip({
                container: 'body'
            });
        }
        //var selr = jQuery(grid_selector).jqGrid('getGridParam','selrow');
        $(document).one('ajaxloadstart.page', function(e) {
            $(grid_selector).jqGrid('GridUnload');
            $('.ui-jqdialog').remove();
        });
    });
    </script>
    <input type="hidden" name="grillas" id="grillas" value="<?= implode(",", $grillas) ?>">
</body>

</html>