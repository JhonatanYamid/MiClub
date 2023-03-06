<form class="form-horizontal formvalida" role="form" method="post" name="frmservicio" id="frmservicio" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre</label>

            <div class="col-sm-8">
                <?php
                $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $_GET["ids"] . "'");

                $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . SIMUser::get("club") . "' and IDServicioMaestro = '" . $id_servicio_maestro . "'");
                if (empty($nombre_servicio_personalizado))
                    $nombre_servicio_personalizado = $nombre_servicio_personalizado;
                if (!empty($nombre_servicio_personalizado)) {
                    echo $nombre_servicio_personalizado;
                } else {
                    echo $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $frm["IDServicioMaestro"] . "'");
                }
                ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono</label>

            <div class="col-sm-8">
                <? if (!empty($frm["Icono"])) {
                    echo "<img src='" . SERVICIO_ROOT . "$frm[Icono]' width=55 >";
                ?>
                    <a href="<? echo $script . ".php?action=delfoto&foto=$frm[Icono]&campo=Icono&ids=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

                <?
                } // END if
                ?>
                <input name="Icono" id=file class="" title="Icono" type="file" size="25" style="font-size: 10px">
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Categorias</label>

            <div class="col-sm-8">
                <?php
               
                    $SQLCategorias = "SELECT IDCategoriasServicios, Nombre FROM CategoriasServicios WHERE IDClub = " . SIMUser::get("club");
                    $QRYCategorias = $dbo->query($SQLCategorias);
                ?>
                    <select name="CategoriaServicio" id="CategoriaServicio" class="form-control">
                        <option value="">[Seleccione la categoria del servicio]</option>
                        <?php
                        while ($Datos = $dbo->fetchArray($QRYCategorias)) { ?>
                            <option value="<?php echo $Datos["IDCategoriasServicios"];  ?>"><?php echo $Datos["Nombre"];  ?></option>
                        <?php } ?>
                    </select>               

                <br>
                <a id="agregar_categoriaservicio" href="#">Agregar</a> | <a id="borrar_categoriaservicio" href="#">Borrar</a>
                <br>
                <select name="CategoriasServicio[]" id="CategoriasServicioGrupo" class="col-xs-8" multiple>
                    <?php
                    $SQL = "SELECT * FROM CategoriasServiciosServicios WHERE IDServicio = $_GET[ids]";
                    $QRY = $dbo->query($SQL);
                    
                    while($Data = $dbo->fetchArray($QRY)):     
                        $InfoCategoria = $dbo->fetchAll("CategoriasServicios", "IDCategoriasServicios = $Data[IDCategoriasServicios]");     
                    ?>
                        <option value="<?php echo $InfoCategoria[IDCategoriasServicios] ?>"><?php echo $InfoCategoria[Nombre]; ?></option>
                    <?php
                    endwhile; ?>
                </select>
                <input type="hidden" name="CategoriasServicios" id="CategoriasServicios" value="">             
               

            </div>
        </div>     

    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Legal</label>

            <div class="col-sm-8">
                <textarea rows="2" cols="50" id="TextoLegal" title="Texto Legal" name="TextoLegal" class="input"><?php echo $frm["TextoLegal"] ?></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo limite para confirmar reserva en app</label>
            <div class="col-sm-8">
                <input id=MinutosReserva type=text size=25 name=MinutosReserva class="input mandatory " title="Minutos Reserva" value="<?= $frm["MinutosReserva"] ?>">
                minutos
            </div>
        </div>
    </div>
    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Grupos Maximo (turnos seguidos solo aplica en Golf)</label>
            <div class="col-sm-8">
                <input id=TurnosMaximo type=text size=25 name=TurnosMaximo class="input mandatory " title="Grupos Maximo" value="<?= $frm["TurnosMaximo"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar en app solo fecha disponibles? ( pantalla de fechas con contador, si marca No se mostraran 15 dias )</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SoloFechaDisponible"], 'SoloFechaDisponible', "class='input'") ?>
            </div>
        </div>
    </div>
    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Identificador de mismos servicios para disponibilidad</label>
            <div class="col-sm-8">
                <input id=IdentificadorServiciosPadres type=text size=25 name=IdentificadorServiciosPadres class="input mandatory " title="Grupos Maximo" value="<?= $frm["IdentificadorServiciosPadres"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar los mismo servicios</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["ValidarServiciosPadres"], 'ValidarServiciosPadres', "class='input'") ?>
            </div>
        </div>
    </div>
    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solo reservas seg&uacute;n Georeferenciacion?</label>
            <div class="col-sm-8">
                <span class="col-sm-8"><?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["Georeferenciacion"], "Georeferenciacion", "title=\"Georeferenciacion\"") ?> <span class="columnafija">
                        <div id="div_geo" <?php if ($frm["Georeferenciacion"] == "N" || empty($frm["Georeferenciacion"])) echo "style='display:none'";  ?>>
                            Latitud <input id=Latitud type=text size=25 name="Latitud" class="input" title="Latitud" value="<?= $frm["Latitud"] ?>">
                            Longitud <input id=Longitud type=text size=25 name="Longitud" class="input" title="Longitud" value="<?= $frm["Longitud"]; ?>">
                            Rango <input id=Rango type=text size=25 name=Rango class="input" title="Rango" value="<?= $frm["Rango"] ?>">(mts)
                            Mensaje Fuera Rango <input id="MensajeFueraRango" type=text size="25" name="MensajeFueraRango" class="input" title="Mensaje Fuera Rango" value="<?= $frm[MensajeFueraRango] ?>">

                        </div>
            </div>
        </div>


    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite multiple auxiliar(Ej.Tenis) ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MultipleAuxiliar"], 'MultipleAuxiliar', "class='input'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cuando es salones, maximo de invitados que puede tener el socio?</label>
            <div class="col-sm-8">
                <input id=MaximoInvitadosSalon type=text size=25 name=MaximoInvitadosSalon class="input mandatory " title="Maximo Invitados Salon" value="<?= $frm["MaximoInvitadosSalon"] ?>">
                invitados
            </div>
        </div>
    </div>


    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> En salones: numero de turnos a separar automaticamente para mantenimiento despues de reserva?</label>
            <div class="col-sm-8">
                <select name="TurnoMantenimiento" id="TurnoMantenimiento" class="form-control" required>
                    <option value=""></option>
                    <?php for ($i = 0; $i <= 3; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php if ($i == $frm["TurnoMantenimiento"]) echo "selected"; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>

                </select>

            </div>
        </div>

    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir mas de una reserva al mismo socio en la misma hora? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteReservaMismaHora"], 'PermiteReservaMismaHora', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar reservas a la misma hora solo en este servicio? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["ValidarReservasMismaHoraServicio"], 'ValidarReservasMismaHoraServicio', "class='input'") ?>
            </div>
        </div>

    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Capacidad de turnos por bloque (por ejemplo si es 2, es por que se tiene 3 masajistas pero solo 2 camillas, solo permite reservar max 2 turnos en esa hora) Si no aplica el valor debe ser cero (0)</label>
            <div class="col-sm-8">
                <input id=Cupo type=text size=25 name=CupoMaximoBloque class="input" title="Cupo Maximo por Bloque" value="<?= $frm["CupoMaximoBloque"] ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cuando socio elimina reserva bloquear por x minutos al grupo familiar para tomar esa reserva?</label>
            <div class="col-sm-8">
                <input id=BloquearMinutosGrupo type=text size=25 name=BloquearMinutosGrupo class="input " title="Bloquear minutos Grupo" value="<?= $frm["BloquearMinutosGrupo"] ?>">
                minutos
            </div>
        </div>

    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Abrir reservas siempre el Dia/Hora: </label>
            <div class="col-sm-8">
                <select name="DiaApertura" id="DiaApertura" class="form-control" required>
                    <option value="">Dia</option>
                    <option value="1" <?php if ($frm["DiaApertura"] == 1) echo "selected"; ?>>Lunes</option>
                    <option value="2" <?php if ($frm["DiaApertura"] == 2) echo "selected"; ?>>Martes</option>
                    <option value="3" <?php if ($frm["DiaApertura"] == 3) echo "selected"; ?>>Miercoles</option>
                    <option value="4" <?php if ($frm["DiaApertura"] == 4) echo "selected"; ?>>Jueves</option>
                    <option value="5" <?php if ($frm["DiaApertura"] == 5) echo "selected"; ?>>Viernes</option>
                    <option value="6" <?php if ($frm["DiaApertura"] == 6) echo "selected"; ?>>Sabado</option>
                    <option value="7" <?php if ($frm["DiaApertura"] == 7) echo "selected"; ?>>Domingo</option>
                </select>

                <input type="time" name="HoraApertura" id="HoraApertura" class="input" title="Hora Apertura" value="<?php echo $frm["HoraApertura"] ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite editar aux despues de reservar en app?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteEditarAuxiliar"], 'PermiteEditarAuxiliar', "class='input'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Establecer como área deportiva </label>
            <div class="col-sm-8">

                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["AreaDeportiva"], 'AreaDeportiva', "class='input'") ?>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Máximo de reservas en el año por invitado de socio si es zona deportiva</label>
            <div class="col-sm-8">
                <input id="MaxInvitadoAno" type="Number" size="3" name="MaxInvitadoAno" class="input" title=" Máximo de reservas en el año por invitado de socio" value="<?= $frm["MaxInvitadoAno"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Máximo de reservas en el mes por invitado de socio si es zona deportiva</label>
            <div class="col-sm-8">
                <input id="MaxInvitadoMes" type="Number" size="3" name="MaxInvitadoMes" class="input" title=" Máximo de reservas en el mes por invitado de socio" value="<?= $frm["MaxInvitadoMes"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar reservas activas por socio?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ValidarReservasActivas"], 'ValidarReservasActivas', "class='input'") ?>
            </div>
        </div>

    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar cantidad de reservas activas por socio semana?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ValidaReservasActivasSemana"], 'ValidaReservasActivasSemana', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero de reservas activas por socio semana</label>
            <div class="col-sm-8">
                <input id="NumeroReservasActivasSemana" type="Number" size="10" name="NumeroReservasActivasSemana" class="input" title=" Numero Reservas Activas" value="<?= $frm["NumeroReservasActivasSemana"] ?>">
            </div>
        </div>


    </div>
    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar cantidad de reservas activas por socio fin de semana?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ValidaReservasActivasFin"], 'ValidaReservasActivasFin', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero de reservas activas por socio fin de semana</label>
            <div class="col-sm-8">
                <input id="NumeroReservasActivasFin" type="Number" size="10" name="NumeroReservasActivasFin" class="input" title=" Numero Reservas Activas" value="<?= $frm["NumeroReservasActivasFin"] ?>">
            </div>
        </div>


    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir eliminar fuera del tiempo establecido en configuracion (en reporte saldra que se debe cobrar el turno)</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["ValidaEliminacionFueraHora"], 'ValidaEliminacionFueraHora', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje para reservas elminadas fuera de tiempo</label>

            <div class="col-sm-8">
                <textarea rows="2" cols="50" id="MensajeEliminacionFueraHora" title="MensajeEliminacionFueraHora" name="MensajeEliminacionFueraHora" class="input"><?php echo $frm["MensajeEliminacionFueraHora"] ?></textarea>
            </div>


        </div>

    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir al usuario agregar la reserva al calendario del celular?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["PermiteGuardarCalendarioDispositivo"], 'PermiteGuardarCalendarioDispositivo', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar cupos disponibles cuando permita mas de un cupo?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["MostrarCupoDisponible"], 'MostrarCupoDisponible', "class='input'") ?>
            </div>
        </div>

    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Comportamiento inicial de la reserva</label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formPopUp("ServicioInicial", "Nombre", "Nombre", "IDServicioInicial", $frm["IDServicioInicial"], "[Seleccione el Servicio Inicial]", "popup form-control", "title = \"Servicio Inicial\"") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir reservar mas de x turnos asi los turnos no sean seguidos (ej: Turno de las 8am- lapso - Turno: 8-30 dejar separar los dos turnos asi no sean seguidos)?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["PermiteReservaTurnosNoSeguidos"], 'PermiteReservaTurnosNoSeguidos', "class='input'") ?>
            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Confirmar Reserva
        </h3>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> La reserva se debe confirmar por parte del socio?</label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteReconfirmar"], 'PermiteReconfirmar', "class='input'")  ?>
            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Eliminacion reserva
        </h3>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar el boton de eliminar reserva para mi o para todos?</label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["EliminarParaTodosOParaMi"], 'EliminarParaTodosOParaMi', "class='input'")  ?>
            </div>
        </div>
    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Permisos Reserva
        </h3>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solo reservas con permisos</label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["permisoReserva"], 'permisoReserva', "class='input'")  ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permisos por tipo reserva</label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermisoReservaTipo"], 'PermisoReservaTipo', "class='input'")  ?>
            </div>
        </div>
    </div>



    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha y hora en la que es valida la lista</label>
        </div>
    </div>
    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Inicio</label>
            <div class="col-sm-8">
                <input type="time" name="horaInicioPermiso" id="horaInicioPermiso" class="input" title="Hora Apertura" value="<?php echo $frm["horaInicioPermiso"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Fin</label>
            <div class="col-sm-8">
                <input type="time" name="horaFinPermiso" id="horaFinPermiso" class="input" title="Hora Apertura" value="<?php echo $frm["horaFinPermiso"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Inicio</label>
            <div class="col-sm-8">
                <input type="date" name="fechaInicioPermiso" id="fechaInicioPermiso" class="input" title="Hora Apertura" value="<?php echo $frm["fechaInicioPermiso"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin</label>
            <div class="col-sm-8">
                <input type="date" name="fechaFinPermiso" id="fechaFinPermiso" class="input" title="Hora Apertura" value="<?php echo $frm["fechaFinPermiso"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar tiempo de reserva para tipo socio?</label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["ValidarTiempoTipoSocio"], 'ValidarTiempoTipoSocio', "class='input'")  ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar antes? (si es N se validara despues) </label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["ValidarAntes"], 'ValidarAntes', "class='input'")  ?>
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo para abrir la reserva por tipo de socio</label>
            <div class="col-sm-8">
                <input type="number" name="TiempoTipoSocio" id="TiempoTipoSocio" class="col-xs-4 " title="TiempoTipoSocio" value="<?php echo $frm["TiempoTipoSocio"] ?>">
                <select name="MedicionTiempoTipoSocio" id="MedicionTiempoTipoSocio" class="" title="Opcion Tiempo Despues">
                    <option value=""></option>
                    <option value="Minutos" <?php if ($frm["MedicionTiempoTipoSocio"] == "Minutos") echo "selected";  ?>>Minutos</option>
                    <option value="Horas" <?php if ($frm["MedicionTiempoTipoSocio"] == "Horas") echo "selected";  ?>>Horas</option>
                    <option value="Dias" <?php if ($frm["MedicionTiempoTipoSocio"] == "Dias") echo "selected";  ?>>Dias</option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Socio</label>

            <div class="col-sm-8">
                <?php
                if (SIMUser::get("IDPerfil") == 7) :
                    $tipo_socio = array("Canje" => "Canje",     "Cortesia" => "Cortesia", "Invitado" => "Invitado");
                    echo SIMHTML::formPopupArray($tipo_socio,  $frm["TipoSocio"], "TipoSocio",  "Seleccione tipo", "form-control");
                else :
                    $sql_tipo_socio = "SELECT TS.IDTipoSocio,Nombre FROM TipoSocio TS, ClubTipoSocio CTS WHERE TS.IDTipoSocio=CTS.IDTipoSocio AND IDClub = '" . SIMUser::get("club") . "' Order by Nombre";
                    $result_tipo_socio = $dbo->query($sql_tipo_socio);
                ?>
                    <select name="TipoSocio" id="TipoSocio" class="form-control">
                        <option value="">[Seleccione Tipo Socio]</option>
                        <?php
                        while ($row_tipo_soc = $dbo->fetchArray($result_tipo_socio)) { ?>
                            <option value="<?php echo $row_tipo_soc["Nombre"];  ?>" <?php if ($frm["TipoSocio"] == $row_tipo_soc["Nombre"]) echo "selected"; ?>><?php echo $row_tipo_soc["Nombre"];  ?></option>
                        <?php } ?>
                    </select>
                <?php
                endif;
                ?>

                <br>
                <a id="agregar_tiposocio" href="#">Agregar</a> | <a id="borrar_tiposocio" href="#">Borrar</a>
                <br>
                <select name="TipoSocioValida[]" id="TipoSocioValidarGrupo" class="col-xs-8" multiple>
                    <?php
                    $item = 1;
                    $array_invitados = explode("|||", $frm["TipoSocioValidar"]);
                    foreach ($array_invitados as $id_invitado => $datos_invitado) :
                        $array_datos_invitados = explode("-", $datos_invitado);
                        $item--;
                        $TipoSocio = $array_datos_invitados[1];
                    ?>
                        <option value="<?php echo "tipos-" . $TipoSocio; ?>"><?php echo $TipoSocio; ?></option>
                    <?php
                    endforeach; ?>
                </select>
                <input type="hidden" name="TipoSocioValidar" id="TipoSocioValidar" value="">
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Bloquear las reservas en un horario del dia especifico?</label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["BloquearReservasHorario"], 'BloquearReservasHorario', "class='input'")  ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje de bloqueo</label>
            <div class="col-sm-8">
                <textarea rows="2" cols="50" id="MensajeBloqueoHorario" title="Mensaje push reserva" name="MensajeBloqueoHorario" class="input"><?php echo $frm["MensajeBloqueoHorario"] ?></textarea>
            </div>
        </div>

    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Inicio Bloqueo</label>
            <div class="col-sm-8">
                <input type="time" name="HoraInicioBloqueo" id="HoraInicioBloqueo" class="input" title="Hora Apertura" value="<?php echo $frm["HoraInicioBloqueo"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Fin Bloqueo</label>
            <div class="col-sm-8">
                <input type="time" name="HoraFinBloqueo" id="HoraFinBloqueo" class="input" title="Hora Apertura" value="<?php echo $frm["HoraFinBloqueo"] ?>">
            </div>
        </div>
    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Colores
        </h3>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color para pantalla de tv: </label>
            <div class="col-sm-8">
                <input name="ColorTv" type="color" value="<?php if (empty($frm["ColorTv"])) {
                                                                echo "#FFFFFF";
                                                            } else {
                                                                echo $frm["ColorTv"];
                                                            }    ?>" />
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color letra al listar las horas: </label>

            <div class="col-sm-8">
                <input name="ColorLetraHora" type="color" value="<?php if (empty($frm["ColorLetraHora"])) {
                                                                        echo "#FFFFFF";
                                                                    } else {
                                                                        echo $frm["ColorLetraHora"];
                                                                    }    ?>" />
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color fondo al listar las horas: </label>

            <div class="col-sm-8">
                <input name="ColorFondoHora" type="color" value="<?php if (empty($frm["ColorFondoHora"])) {
                                                                        echo "#FFFFFF";
                                                                    } else {
                                                                        echo $frm["ColorFondoHora"];
                                                                    }    ?>" />
            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Pagos
        </h3>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pago reserva: </label>
            <div class="col-sm-8">
                <?php
                $sql_tipo_pago_servicio = "Select * From ServicioTipoPago Where IDServicio = '" . $_GET["ids"] . "'";
                $result_tipo_pago_servicio = $dbo->query($sql_tipo_pago_servicio);
                while ($row_tipo_pago_servicio = $dbo->fetchArray($result_tipo_pago_servicio)) :
                    $array_tipo_pago_servicio[] = $row_tipo_pago_servicio["IDTipoPago"];
                endwhile;
                $sql_tipo_pago = "Select * From TipoPago Where Publicar = 'S'";
                $result_tipo_pago = $dbo->query($sql_tipo_pago);
                while ($row_tipo_pago = $dbo->fetchArray($result_tipo_pago)) : ?>
                    <input type="checkbox" name="IDTipoPago[]" id="IDTipoPago" value="<?php echo $row_tipo_pago["IDTipoPago"]; ?>" <?php if (in_array($row_tipo_pago["IDTipoPago"], $array_tipo_pago_servicio)) echo "checked"; ?>><?php echo $row_tipo_pago["Nombre"]; ?><br>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor de la reserva $</label>
            <div class="col-sm-8">
                <input id=ValorReserva type=number size=25 name=ValorReserva class="input" title="Valor Reserva" value="<?= $frm["ValorReserva"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje pago reserva</label>
            <div class="col-sm-8">
                <input id=MensajePagoReserva type=text size=25 name=MensajePagoReserva class="input" title="Mensaje Pago Reserva" value="<?= $frm["MensajePagoReserva"] ?>">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero de reservas permitida por accion en un mismo dia:</label>
            <div class="col-sm-8">
                <input id=NumeroReservasPermitidaAccion type=text size=25 name=NumeroReservasPermitidaAccion class="input" title="Numero Reservas Permitida Accion" value="<?= $frm["NumeroReservasPermitidaAccion"] ?>">

            </div>
        </div>
    </div>

    <div class="form-group first">       

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> El valor de la reserva se calcula por elementos</label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["PermitePrecioElementos"], 'PermitePrecioElementos', "class='input'")  ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto boton Pagar reserva</label>
            <div class="col-sm-8">
                <input id=PagarTotalLabel type=text size=50 name=PagarTotalLabel class="input" title="Mensaje Pago Reserva" value="<?= $frm["PagarTotalLabel"] ?>">

            </div>
        </div>
       
    </div>   

    <div class="form-group first">       

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite abonar a las reservas</label>
            <div class="col-sm-8">
                <?php echo SIMHTML::formradiogroup(SIMResources::$sino, $frm["PermiteSistemaAbono"], 'PermiteSistemaAbono', "class='input'")  ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto boton abonos</label>
            <div class="col-sm-8">
                <input id=PagarAbonoLabel type=text size=50 name=PagarAbonoLabel class="input" title="Mensaje Pago Reserva" value="<?= $frm["PagarAbonoLabel"] ?>">

            </div>
        </div>
    </div>



    <?php
    if (SIMUser::get("club") == 8 || SIMUser::get("club") == 28) {
    ?>

        <div class="form-group first">
            <div class="col-xs-12 col-sm-6">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Precios personalizados para reserva</label>
                <div class="col-sm-8">
                    <?php echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["preciosVarios"], 'preciosVarios', "class='input'")  ?>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <!-- ACA IRIA TIPO RESERVA-->

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Notificacion mail y push
        </h3>
    </div>
    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email de notificaci&oacute;n de reserva separados por coma (,)</label>
            <div class="col-sm-8">
                <input id=EmailNotificacion type=text size=25 name=EmailNotificacion class="input" title="EmailNotificacion" value="<?= $frm["EmailNotificacion"] ?>">

            </div>
        </div>

    </div>


    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar mail a socio con confirmacion de reserva?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["NotificarSocioMailReserva"], 'NotificarSocioMailReserva', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar mail a socio con recordación de reserva x dias antes?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["NotificarSocioRecordacionReserva"], 'NotificarSocioRecordacionReserva', "class='input'") ?>
                Dias: <input id=DiasNotificacion type=Number size=3 name=DiasNotificacion class="input" title="Dias antes" value="<?= $frm["DiasNotificacion"] ?>">

            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar push al socio al crear reserva?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["NotificarSocioPushReserva"], 'NotificarSocioPushReserva', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje push al crear la reserva </label>
            <div class="col-sm-8">
                <textarea rows="2" cols="50" id="MensajePushReserva" title="Mensaje push reserva" name="MensajePushReserva" class="input"><?php echo $frm["MensajePushReserva"] ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje Notificacion al socio por mail:</label>
            <div class="col-sm-8">
                <!--<textarea rows="2" cols="50" id="TextoCorreoSocio" title="Texto Correo Socio" name="TextoCorreoSocio" class="input"><?php echo $frm["TextoCorreoSocio"] ?></textarea>-->
                <?php
                $oCuerpo = new FCKeditor("TextoCorreoSocio");
                $oCuerpo->BasePath = "js/fckeditor/";
                $oCuerpo->Height = 300;
                $oCuerpo->Width = 300;
                //$oCuerpo->EnterMode = "p";
                $oCuerpo->Value =  $frm["TextoCorreoSocio"];
                $oCuerpo->Create();
                ?>



            </div>
        </div>


        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje Recordación al socio por mail:</label>
            <div class="col-sm-8">
                <!--<textarea rows="2" cols="50" id="TextoCorreoSocio" title="Texto Correo Socio" name="TextoCorreoSocio" class="input"><?php echo $frm["TextoCorreoSocio"] ?></textarea>-->
                <?php
                $oCuerpo = new FCKeditor("TextoRecordacionSocio");
                $oCuerpo->BasePath = "js/fckeditor/";
                $oCuerpo->Height = 300;
                $oCuerpo->Width = 300;
                //$oCuerpo->EnterMode = "p";
                $oCuerpo->Value =  $frm["TextoRecordacionSocio"];
                $oCuerpo->Create();
                ?>



            </div>
        </div>
    </div>



    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar mail a socio cuando incumpla reserva?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["NotificarSocioReservaIncumplida"], 'NotificarSocioReservaIncumplida', "class='input'") ?>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje de reserva incumplida:</label>
            <div class="col-sm-8">
                <?php
                $oCuerpo = new FCKeditor("TextoCorreoReservaIncumplida");
                $oCuerpo->BasePath = "js/fckeditor/";
                $oCuerpo->Height = 300;
                $oCuerpo->Width = 300;
                //$oCuerpo->EnterMode = "p";
                $oCuerpo->Value =  $frm["TextoCorreoReservaIncumplida"];
                $oCuerpo->Create();
                ?>

            </div>
        </div>


    </div>


    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar recordatorio push x min antes de acabar tiempo de reserva?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteRecordatorio"], 'PermiteRecordatorio', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cuantos Minutos antes</label>
            <div class="col-sm-8">
                <input id=NotifMinutosAntes type=Number size=3 name=NotifMinutosAntes class="input" title="Minutos antes" value="<?= $frm["NotifMinutosAntes"] ?>">
            </div>
        </div>


    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje recordatorio</label>

            <div class="col-sm-8">
                <textarea rows="2" cols="50" id="MensajeNotifTerminaReserva" title="Mensaje Terminacion reserva" name="MensajeNotifTerminaReserva" class="input"><?php echo $frm["MensajeNotifTerminaReserva"] ?></textarea>
            </div>
        </div>
    </div>







    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir al admin reservar cualquier hora ? (ej. dar la posibilidad de reservar quienes estuvieron a las 11am despues de pasada la hora )</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteReservaCualquierHora"], 'PermiteReservaCualquierHora', "class='input'") ?>

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar push al socio cuando admin elimina reserva?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PushEliminaReserva"], 'PushEliminaReserva', "class='input'") ?>

            </div>
        </div>
    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir a un socio agregarse a un grupo cuando quede cupo? (pj: golf grupo max 3 y solo estan dos cualquier socio se puede unir al grupo )</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteAgregarGrupo"], 'PermiteAgregarGrupo', "class='input'") ?>

            </div>
        </div>



    </div>




    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Lista de espera ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteListaEspera"], 'PermiteListaEspera', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Lista de espera Auxiliares/Boleadores ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteListaEsperaAuxiliar"], 'PermiteListaEsperaAuxiliar', "class='input'") ?>
            </div>
        </div>

    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar push recordando tiempo límite de cancelacion? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["NotificarTiempoCancelacion"], 'NotificarTiempoCancelacion', "class='input'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Minutos antes de push recordando tiempo límite de cancelacion </label>
            <div class="col-sm-8">
                <input id=MinutosPushTiempoCancelacion type=Number step="30" size=3 name=MinutosPushTiempoCancelacion class="input" title="Minutos antes" value="<?= $frm["MinutosPushTiempoCancelacion"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje push recordando tiempo límite de cancelacion </label>
            <div class="col-xs-12 col-sm-6">
                <textarea rows="2" cols="50" id="MensajePushTiempoCancelacion" title="Mensaje Push Tiempo Cancelacion" name="MensajePushTiempoCancelacion" class="input"><?php echo $frm["MensajePushTiempoCancelacion"] ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Notificar Push al incumplir una reserva</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["NotificarPush"], 'NotificarPush', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje notificación push reserva incumplida</label>

            <div class="col-sm-8">
                <textarea rows="3" cols="25" id="MensajeNotificacion" title="MensajeNotificacion " name="MensajeNotificacion" class="input"><?php echo $frm["MensajeNotificacion"] ?></textarea>
            </div>
        </div>


    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Buscador
        </h3>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Filtro por elemento (busueda de canchas, peluqueras, etc) ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteFiltroElementoFechaPorTexto"], 'PermiteFiltroElementoFechaPorTexto', "class='input'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite Filtro Elemento Fecha Por Boton ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteFiltroElementoFechaPorBoton"], 'PermiteFiltroElementoFechaPorBoton', "class='input'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Buscador de Fechas ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBotonBuscar"], 'MostrarBotonBuscar', "class='input'") ?>
            </div>
        </div>
    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Encuesta Servicio
        </h3>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar push para que socio responda encuesta de satisfacci&oacute;n despues de cumplir turno ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["EnviarPushEncuesta"], 'EnviarPushEncuesta', "class='input'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar push para que socio responda encuesta al crear la reserva ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["EnviarPushEncuestaCrear"], 'EnviarPushEncuestaCrear', "class='input'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Seleccione encuesta</label>
            <div class="col-sm-8">
                <div class="col-sm-8">
                    <select name="IDEncuesta" id="IDEncuesta">
                        <option value=""></option>
                        <?php
                        $sql_encuesta = "SELECT *  FROM Encuesta Where Publicar = 'S' AND IDClub = '" . SIMUser::get("club") . "'";
                        $qry_encuesta = $dbo->query($sql_encuesta);
                        while ($r_encuesta = $dbo->fetchArray($qry_encuesta)) : ?>
                            <option value="<?php echo $r_encuesta["IDEncuesta"]; ?>" <?php if ($r_encuesta["IDEncuesta"] == $frm["IDEncuesta"]) echo "selected";  ?>><?php echo $r_encuesta["Nombre"]; ?></option>
                        <?php
                        endwhile;    ?>
                    </select>
                </div>
            </div>
        </div>
    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Configuración invitados externos
        </h3>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pedir cedula a invitados Externo ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteInvitadoExternoCedula"], 'PermiteInvitadoExternoCedula', "class='input'") ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pedir correo a invitados Externo ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteInvitadoExternoCorreo"], 'PermiteInvitadoExternoCorreo', "class='input'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pedir fecha de nacimiento a invitados Externo ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteInvitadoExternoFechaNacimiento"], 'PermiteInvitadoExternoFechaNacimiento', "class='input'") ?>
            </div>
        </div>        
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Invitado Externo Paga ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["InvitadoExternoPago"], 'InvitadoExternoPago', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Invitado Externo Pago </label>

            <div class="col-sm-8">
                <input id=LabelAuxiliar type=text size=25 name=LabelInvitadoExternoPago class="input" title="Label Invitado ExternoPago" value="<?= $frm[LabelInvitadoExternoPago] ?>">
            </div>
        </div>


    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Invitado Externo Valor </label>

            <div class="col-sm-8">
                <input id=LabelTipoReserva type=text size=25 name=InvitadoExternoValor class="input" title="Invitado Externo Valor" value="<?= $frm[InvitadoExternoValor] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar invitado externo en modulo de invitados (Solo valido si se pide cedula) </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ValidarInvitadoExternoModuloInvitado"], 'ValidarInvitadoExternoModuloInvitado', "class='input'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Crear Invitacion en modulo de invitados </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["CrearInvitacionExterno"], 'CrearInvitacionExterno', "class='input'") ?>
            </div>
        </div>
    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Otros
        </h3>
    </div>



    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pantalla reserva por elemento (carga rápida)?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PantallaReservaElemento"], 'PantallaReservaElemento', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite al admin repetir reservas al crearla?</label>
            <div class="col-sm-8">
                <div class="col-sm-8">
                    <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteAdminRepetirReserva"], 'PermiteAdminRepetirReserva', "class='input'") ?>
                </div>
            </div>
        </div>

    </div>
    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar ventana con los invitados de la reserva en el app?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PopInvitados"], 'PopInvitados', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar ventana con los inscritos de la reserva en el app?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["VerInscitosClaseApp"], 'VerInscitosClaseApp', "class='input'") ?>
            </div>
        </div>

    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar imagen descriptiva ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarImagenEncabezado"], 'MostrarImagenEncabezado', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto boton</label>
            <input id=TextoAbrirMapa type=text size=25 name=TextoAbrirMapa class="input  " title="Texto Abrir Mapa" value="<?= $frm["TextoAbrirMapa"] ?>">

            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen</label>

            <div class="col-sm-8">
                <? if (!empty($frm["ImagenEncabezado"])) {
                    echo "<img src='" . SERVICIO_ROOT . "$frm[ImagenEncabezado]' width=55 height=100 >";
                ?>
                    <a href="<? echo $script . " .php?action=delfoto&foto=$frm[ImagenEncabezado]&campo=ImagenEncabezado&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
                } // END if
                ?>
                <input name="ImagenEncabezado" id=file class="" title="ImagenEncabezado" type="file" size="25" style="font-size: 10px">
            </div>
        </div>

    </div>   


    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar edad para reservar ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ValidarEdad"], 'ValidarEdad', "class='input'") ?>
                <br>Solo permitir reservar entre las siguientes edades:<br>
                <input id=EdadMinima type=number size=5 name=EdadMinima class="input" title="Edad Minima" value="<?= $frm["EdadMinima"] ?>">
                y <input id=EdadMaxima type=number size=5 name=EdadMaxima class="input" title="Edad Maxima" value="<?= $frm["EdadMaxima"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite reservas el mismo dia ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ReservaMismoDia"], 'ReservaMismoDia', "class='input'") ?>
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar edad de los invitados para reservar ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ValidarEdadInvitados"], 'ValidarEdadInvitados', "class='input'") ?>
            </div>
        </div>        
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir reserva solo en estos horarios especificos? </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ValidarHorario"], 'ValidarHorario', "class='input'") ?>
                <br>Solo permitir reservar entre las siguientes horas:<br>
                <input id=HoraInicio type="time" size=5 name=HoraInicio class="input" title="" value="<?= $frm["HoraInicio"] ?>">
                y <input id=HoraFin type=time size=5 name=HoraFin class="input" title="" value="<?= $frm["HoraFin"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Identificador para consecutivo (ej Tenis:TN)</label>
            <div class="col-sm-8">
                <input id=IdentificadorServicio type=text size=25 name=IdentificadorServicio class="input" title="Identificador Servicio" value="<?= $frm["IdentificadorServicio"]; ?>">
            </div>
        </div>
    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar Horarios en acordeon?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["HorarioAcordeon"], 'HorarioAcordeon', "class='input'") ?>
            </div>

        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir Editar reserva al socio? (agregar/eliminar invitados) </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteEditarReserva"], 'PermiteEditarReserva', "class='input'") ?>
            </div>

        </div>


    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero de fechas a mostrar en pantalla donde se lista las fechas disponibles en el app?</label>
            <div class="col-sm-8">
                <input id=NumeroDiasMostrar type=number size=25 name=NumeroDiasMostrar class="input" title="Numero Dias" value="<?= $frm["NumeroDiasMostrar"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solo ver fechas x dias adelante (Cuando el servicio no permite reservar el mismo dia o el siguiente, por ejemplo 48 antes no muestra hoy ni mañana)</label>
            <div class="col-sm-8">
                <input id=NumeroDiasAdelante type=number size=25 name=NumeroDiasAdelante class="input" title="Numero Dias adelante" value="<?= $frm["NumeroDiasAdelante"] ?>">
            </div>
        </div>

    </div>


    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Si el servicio es de restaurante abrir el modulo de domicilios al terminar reserva?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteIrDomicilio"], 'PermiteIrDomicilio', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label boton abrir domicilio</label>
            <div class="col-sm-8">
                <input id=LabelPermiteIrDomicilio type=text size=25 name=LabelPermiteIrDomicilio class="input" title="Label Ir Domicilio" value="<?= $frm["LabelPermiteIrDomicilio"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Abrir el modulo de Domicilios: </label>
            <div class="col-sm-8">
                <select name="IDModuloDomicilio" id="IDModuloDomicilio" class="form-control" required>
                    <option value=""></option>
                    <option value="33" <?php if ("33" == $frm["IDModuloDomicilio"]) echo "selected"; ?>>Domicilios 1</option>
                    <option value="98" <?php if ("98" == $frm["IDModuloDomicilio"]) echo "selected"; ?>>Domicilios 2</option>
                    <option value="112" <?php if ("112" == $frm["IDModuloDomicilio"]) echo "selected"; ?>>Domicilios 3</option>
                    <option value="113" <?php if ("113" == $frm["IDModuloDomicilio"]) echo "selected"; ?>>Domicilios 4</option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solo permitir reservar con vacuna?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SoloVacunado"], 'SoloVacunado', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Validar la edad de los vacunados</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(SIMResources::$sinoNum, $frm["ValidarEdadVacunados"], 'ValidarEdadVacunados', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mayores de esta edad deben estar vacunados</label>
            <div class="col-sm-8">
                <input id=EdadVacunados type=number size=25 name=EdadVacunados class="input" title="EdadVacunados" value="<?= $frm["EdadVacunados"] ?>">
            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Adicionales en servicio por socio o invitado (pedir caddies, carritos)
        </h3>
    </div>


    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite configurar adicionales?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteAdicionarServicios"], 'PermiteAdicionarServicios', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite editar adicionales?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteEditarAdicionales"], 'PermiteEditarAdicionales', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Obligatorio pedir adicionales?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["AdicionalesObligatorio"], 'AdicionalesObligatorio', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Caddies
        </h3>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permite adicionar caddies?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteAdicionarCaddies"], 'PermiteAdicionarCaddies', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Permite editar caddies?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteEditarCaddies"], 'PermiteEditarCaddies', "class='input '") ?>
            </div>
        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Obligatorio seleccionar caddie?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ObligatorioSeleccionarCaddie"], 'ObligatorioSeleccionarCaddie', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label adiconar caddies </label>

            <div class="col-sm-8">
                <input id=LabelAdiconarCaddies type=text size=25 name=LabelAdiconarCaddies class="input" title="Label Adiconar Caddies" value="<?= $frm[LabelAdiconarCaddies] ?>">

            </div>
        </div>

    </div>
    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje caddies obligatorio </label>

            <div class="col-sm-8">
                <input id=MensajeCaddiesObligatorio type=text size=25 name=MensajeCaddiesObligatorio class="input" title="Mensaje Caddies Obligatorio" value="<?= $frm[MensajeCaddiesObligatorio] ?>">

            </div>
        </div>
    </div>




    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Labels Personalizados
        </h3>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Auxiliar (Boleador) </label>

            <div class="col-sm-8">
                <input id=LabelAuxiliar type=text size=25 name=LabelAuxiliar class="input" title="Label Auxiliar" value="<?= $frm[LabelAuxiliar] ?>">
                <br>(Label utilizado en el boton app)
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Tipo Turno (Sencillos, dobles, etc) </label>

            <div class="col-sm-8">
                <input id=LabelTipoReserva type=text size=25 name=LabelTipoReserva class="input" title="Label Tipo Reserva" value="<?= $frm[LabelTipoReserva] ?>">
                <br>(Label utilizado en el boton app)
            </div>
        </div>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pemite Asignar reserva a otro Beneficiario? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteBeneficiario"], 'PermiteBeneficiario', "class='input '") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label agregar beneficiario </label>

            <div class="col-sm-8">
                <input id=LabelBeneficiario type=text size=25 name=LabelBeneficiario class="input" title="Label Beneficiario" value="<?= $frm[LabelBeneficiario] ?>">
            </div>
        </div>


    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Invitado Socio (label app)</label>
            <div class="col-sm-8">
                <input id=Cupo type=text size=25 name=LabelElementoSocio class="input" title="Label Elemento Socio" value="<?= utf8_encode($frm["LabelElementoSocio"]) ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Invitado externo (label app)</label>
            <div class="col-sm-8">
                <input id=LabelElementoExterno type=text size=25 name=LabelElementoExterno class="input" title="Label Elemento Externo" value="<?= utf8_encode($frm["LabelElementoExterno"]) ?>">
            </div>
        </div>
    </div>

    <div class="form-group first">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label encabezado Invitado (label app)</label>
            <div class="col-sm-8">
                <input id=LabelEncabezadoInvitados type=text size=25 name=LabelEncabezadoInvitados class="input" title="Label Encabezado Invitados" value="<?= utf8_encode($frm["LabelEncabezadoInvitados"]) ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label encabezado Beneficiarios (label app)</label>
            <div class="col-sm-8">
                <input id=LabelEncabezadoBeneficiarios type=text size=25 name=LabelEncabezadoBeneficiarios class="input" title="Label Encabezado Beneficiarios" value="<?= utf8_encode($frm["LabelEncabezadoBeneficiarios"]) ?>">
            </div>
        </div>

    </div>

    <div class="form-group first">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar Boton Inscritos (ver quienes estan inscritos a una clase grupal) ?</label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBotonInscritos"], 'MostrarBotonInscritos', "class='input'") ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Boton Ver Inscritos</label>
            <div class="col-sm-8">
                <input id=LabelBotonInscritos type=text size=25 name=LabelBotonInscritos class="input" title="Label Boton Inscritos" value="<?= utf8_encode($frm["LabelBotonInscritos"]) ?>">
            </div>

        </div>
    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Boton agregar invitado </label>

            <div class="col-sm-8">
                <input id=LabelElementoBoton type=text size=25 name=LabelElementoBoton class="input" title="Label Elemento" value="<?= $frm[LabelElementoBoton] ?>">
                <br>(Label utilizado en el boton app)
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar en las reservas: </label>

            <div class="col-sm-8">
                <input type="radio" name="MostrarReserva" class="" value="NombreSocio" <?php if ($frm["MostrarReserva"] == "NombreSocio") echo "checked" ?>> Nombre de Socio
                <input type="radio" name="MostrarReserva" class="" value="Pesonalizado" <?php if ($frm["MostrarReserva"] == "Pesonalizado") echo "checked" ?>>Pesonalizado
                <input type="LabelPersonalizado" name="LabelPersonalizado" class="form-control" placeholder="Titulo a mostrar" value="<?php echo $frm["LabelPersonalizado"]; ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje al finalizar la reserva</label>
            <div class="col-sm-8">
                <textarea rows="2" cols="50" id="MensajeReservaGuardada" title="Mensaje Reserva Guardada" name="MensajeReservaGuardada" class="input"><?php echo $frm["MensajeReservaGuardada"] ?></textarea>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label titulo "Disponible" al listar las horas disponibles en el app: </label>
            <div class="col-sm-8">
                <input id="LabelDisponible" type=text size=25 name=LabelDisponible class="input" title="Label Disponible" value="<?= $frm["LabelDisponible"] ?>">
            </div>
        </div>
    </div>




    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label al lado de listado de horas (ej: 7am - Clases): </label>
            <div class="col-sm-8">
                <input id=LabelTituloHora type=text size=25 name=LabelTituloHora class="input" title="Label Titulo Hora" value="<?= $frm["LabelTituloHora"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar la duracion del turno donde se lista las horas </label>
            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarDuracionTurno"], 'MostrarDuracionTurno', "class='input'") ?>
            </div>
        </div>
    </div>


    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Boton Confirmar reserva</label>
            <div class="col-sm-8">
                <input id=LabelReconfimarBoton type=text size=25 name=LabelReconfimarBoton class="input" title="Label Reconfimar Boton" value="<?= $frm["LabelReconfimarBoton"] ?>">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Boton editar invitados </label>
            <div class="col-sm-8">
                <input id=LabelInvitados type=text size=25 name=LabelInvitados class="input" title="Label Invitados" value="<?= $frm["LabelInvitados"] ?>">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label FiltroElemento Fecha Por Texto</label>
            <div class="col-sm-8">
                <input id=LabelFiltroElementoFechaPorTexto type=text size=25 name=LabelFiltroElementoFechaPorTexto class="input" title="LabelFiltroElementoFechaPorTexto" value="<?= $frm["LabelFiltroElementoFechaPorTexto"] ?>" placeholder="Busque por nombre de cancha">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Filtro Elemento Fecha Por Boton </label>
            <div class="col-sm-8">
                <input id=LabelFiltroElementoFechaPorBoton type=text size=25 name=LabelFiltroElementoFechaPorBoton class="input" title="LabelFiltroElementoFechaPorBoton" value="<?= $frm["LabelFiltroElementoFechaPorBoton"] ?>" placeholder="Filtrar fechas por cancha">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label boton Eliminar Para Todos O Para Mi</label>
            <div class="col-sm-8">
                <input id=MensajeEliminarParaTodosOParaMi type=text size=25 name=MensajeEliminarParaTodosOParaMi class="input" title="MensajeEliminarParaTodosOParaMi" value="<?= $frm["MensajeEliminarParaTodosOParaMi"] ?>" placeholder="¿Quieres eliminar la reserva para ti o para todos los miembros de la reserva?">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Boton Eliminar Reserva </label>
            <div class="col-sm-8">
                <input id=BotonEliminarReserva type=text size=25 name=BotonEliminarReserva class="input" title="BotonEliminarReserva" value="<?= $frm["BotonEliminarReserva"] ?>" placeholder="Eliminar Reserva">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Eliminar Para Mi</label>
            <div class="col-sm-8">
                <input id=LabelEliminarParaMi type=text size=25 name=LabelEliminarParaMi class="input" title="LabelEliminarParaMi" value="<?= $frm["LabelEliminarParaMi"] ?>" placeholder="Eliminar para mi">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Eliminar Para Todos </label>
            <div class="col-sm-8">
                <input id=LabelEliminarParaTodos type=text size=25 name=LabelEliminarParaTodos class="input" title="LabelEliminarParaTodos" value="<?= $frm["LabelEliminarParaTodos"] ?>" placeholder="Eliminar para todos">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Boton Editar Adicionales</label>
            <div class="col-sm-8">
                <input id=BotonEditarAdicionales type=text size=25 name=BotonEditarAdicionales class="input" title="BotonEditarAdicionales" value="<?= $frm["BotonEditarAdicionales"] ?>" placeholder="Boton Editar Adicionales">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label Adicionales </label>
            <div class="col-sm-8">
                <input id=LabelAdicionales type=text size=25 name=LabelAdicionales class="input" title="LabelAdicionales" value="<?= $frm["LabelAdicionales"] ?>" placeholder="Label Adicionales">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Encabezado Adicionales</label>
            <div class="col-sm-8">
                <input id=EncabezadoAdicionales type=text size=25 name=EncabezadoAdicionales class="input" title="EncabezadoAdicionales" value="<?= $frm["EncabezadoAdicionales"] ?>" placeholder="Encabezado Adicionales">
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Label Seleccione Adicionales </label>
            <div class="col-sm-8">
                <input id=LabelSeleccioneAdicionales type=text size=25 name=LabelSeleccioneAdicionales class="input" title="LabelSeleccioneAdicionales" value="<?= $frm["LabelSeleccioneAdicionales"] ?>" placeholder="Label Seleccione Adicionales">
            </div>
        </div>
    </div>

    <div class="form-group first ">
        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mensaje Adicionales Obligatorio</label>
            <div class="col-sm-8">
                <input id=MensajeAdicionalesObligatorio type=text size=25 name=MensajeAdicionalesObligatorio class="input" title="MensajeAdicionalesObligatorio" value="<?= $frm["MensajeAdicionalesObligatorio"] ?>" placeholder="Mensaje Adicionales Obligatorio">
            </div>
        </div>

    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Reservas asociadas
        </h3>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-12">


            <table id="simple-table" class="table table-striped table-bordered table-hover">
                <tr>

                    <th>Servicios asociados para reservas multiples</th>

                </tr>
                <tbody id="listacontactosanunciante">


                    <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">

                        <td width="1000px">

                            <?php
                            $datos_guardados = explode("|||", $frm["IDServicioAsociado"]);
                            foreach ($datos_guardados as  $id_servicio) :
                                if (!empty($id_servicio))
                                    $array_datos_guardados[] = $id_servicio;
                            endforeach;
                            ?>

                            <select style="max-width:100% !important" multiple class="chosen-select" name="ServicioAsociado[]" id="ServicioAsociado" data-placeholder="Seleccione...">
                                <?php
                                if (count($datos_servicio) > 0) {
                                    foreach ($datos_servicio as $idservicio => $servicio) {

                                        $id_servicio_mestro_menu = $servicio["IDServicioMaestro"];
                                        $servicio["Nombre"] =  $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");
                                        $servicio["NombrePersonalizado"] =  $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . SIMUser::get("club") . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");

                                        if (!empty($servicio["NombrePersonalizado"]))
                                            $NombreServicio = $servicio["NombrePersonalizado"];
                                        else
                                            $NombreServicio = $servicio["Nombre"];


                                        if (count($array_datos_guardados) <= 0) :
                                            $seleccionar = "";
                                        elseif (in_array($servicio["IDServicio"], $array_datos_guardados)) :
                                            $seleccionar = "selected";
                                        else :
                                            $seleccionar = "";
                                        endif;
                                ?>
                                        <option value="<?php echo $servicio["IDServicio"] ?>" <?php echo $seleccionar; ?>>
                                            <?php echo $NombreServicio;  ?>
                                        </option>
                                <?php }
                                }
                                ?>
                            </select>


                        </td>





                    </tr>

                </tbody>

            </table>








        </div>



    </div>







    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
            <button class="btn btn-info btnEnviar" type="button" rel="frmservicio">
                <i class="ace-icon fa fa-check bigger-110"></i>
                <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
            </button>
        </div>
    </div>
</form>
<script>
    $("#MinutosPushTiempoCancelacion").change(function() {
        let value = $("#MinutosPushTiempoCancelacion").val();
        let modulo = $("#MinutosPushTiempoCancelacion").val() % 30;
        $("#MinutosPushTiempoCancelacion").val(value - modulo);
    });
</script>