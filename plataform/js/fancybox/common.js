/**
 *Procedimientos y funciones de uso general
 *
 */
var nav4 = window.event ? true : false;
jQuery(document).ready(function () {
  $(".EstadoSolicitudCanje").change(function () {
    var estado;

    estado = $('select[name="IDEstadoCanjeSolicitud"] option:selected').text();
    // alert("hola hice un cambio" + estado);

    if (estado == "Aprobado") {
      $("#archivosCarnetVacunacion").show();
    } else if (estado != "Aprobado") {
      $("#archivosCarnetVacunacion").hide();
    }
  });

  $("EstadoLaboral").change();

  $(".EstadoLaboral").change(function () {
    var Estado = $(this).val();
    if (Estado == 3) {
      $("#ComentarioAprobacion").addClass("mandatory");
      $("#ComentarioAprobador").addClass("mandatory");
    } else {
      $("#ComentarioAprobacion").removeClass("mandatory");
      $("#ComentarioAprobador").removeClass("mandatory");
    }
  });

  $(".ChangeLang").click(function () {
    var idioma = $(this).attr("data-idioma");
    $.post("includes/async/set_idioma.async.php", { idioma: idioma }).done(
      function () {
        location.reload();
      }
    );
  });

  $(".chosen-selected").click(function () {
    var id = $(this).attr("rel");
    $("#" + id + " option").prop("selected", true);
    $("#" + id).trigger("chosen:updated");
  });
  $(".chosen-deselect").click(function () {
    var id = $(this).attr("rel");
    $("#" + id + " option").prop("selected", false);
    $("#" + id).trigger("chosen:updated");
  });

  $(".AccionPadre").focusout(function () {
    var accion = $(this).val();
    if (accion != null) {
      $.ajax({
        type: "POST",
        data: { accion: accion },
        dataType: "json",
        url: "includes/async/get_FechaFacturacionSocio.async.php",
        success: function (data) {
          $("#FechaFacturacion").val(data["FechaFacturacion"]);
          $("#FechaIngresoClub").val(data["FechaIngresoClub"]);
        },
      });
    }
  });

  $(".ActivaSNDisponibilidad").change(function () {
    var nombre_select = $(this).attr("name");
    var datos_dispo = nombre_select.split("_");
    var id_dispo = datos_dispo[1];
    var activo = $(this).val();
    $("#msgupdate" + id_dispo).html(
      "<span style='color:#FF0004'>Guardando, por favor espere...</span>"
    );
    jQuery.ajax({
      type: "POST",
      data: { IDDisponibilidad: id_dispo, Activo: activo },
      dataType: "json",
      url: "includes/async/activa_disponibilidad.async.php",
      success: function (data) {
        $("#msgupdate" + id_dispo).html("");
      },
    });
  });

  $(".CambiaEstadoProducto").change(function () {
    var id_producto = $(this).attr("producto");
    var id_pedido = $(this).attr("pedido");
    var id_estado = $(this).val();
    var version = $(this).attr("version");
    var id_club = $(this).attr("IDClub");

    $("#msgupdate" + id_producto).html(
      "<span style='color:#FF0004'>Guardando, por favor espere...</span>"
    );

    jQuery.ajax({
      type: "POST",
      data: {
        IDProducto: id_producto,
        IDPedido: id_pedido,
        IDEstado: id_estado,
        Version: version,
        IDClub: id_club,
      },
      dataType: "json",
      url: "includes/async/estado_producto.async.php",
      success: function (data) {
        $("#msgupdate" + id_producto).html("");
      },
    });
    $("#msgupdate" + id_producto).html("Guardado");
  });

  $("input[class='btnvotantepresente']").change(function () {
    var valor = $(this).val();
    var idvotante = $(this).attr("idvotante");
    var IDUsuario = $(this).attr("usuarioregistra");

    $("#msgupdate" + idvotante).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );
    jQuery.ajax({
      type: "POST",
      data: {
        IDVotacionVotante: idvotante,
        Presente: valor,
        IDUsuario: IDUsuario,
      },
      dataType: "json",
      url: "includes/async/ingresosalidavotante.async.php",
      success: function (data) {
        if (data == "ok") {
          $("#msgupdate" + idvotante).html("Guardado con exito");
          if (valor == "S") {
            $("#msgestadoregistro").html(
              "<span style='color:#69AA46'>REGISTRADO</span>"
            );
          } else {
            $("#msgestadoregistro").html(
              "<span style='color:#FF0004'>No Registrado</span>"
            );
          }
        } else {
          if (data == "poder") {
            $("#msgupdate" + idvotante).html(
              "<span style='color:#FA1212'>No es posible dar ingreso a una persona que otorgo el poder</span>"
            );
            $("#msgestadoregistro").html(
              "<span style='color:#FF0004'>NO Registrado</span>"
            );
          } else {
            if (data == "repetidocasa") {
              $("#msgupdate" + idvotante).html(
                "<span style='color:#FA1212'>Atencion: Ya ingreso alguien del mismo predio</span>"
              );
              $("#msgestadoregistro").html(
                "<span style='color:#FF0004'>NO Registrado</span>"
              );
            }
          }
        }
      },
    });
  });

  $(".btnEnviar").click(function () {
    var form = $(this).attr("rel");
    $("#" + form).submit();
  });

  $("#GenerarCodigo").click(function () {
    var texto = makeid();
    $("#Codigo").val(texto);
  });

  $("#masfotos").click(function () {
    $("#CargarImg").toggle("slow");
  });

  $(".seleccionar_todos_carta").click(function () {
    var conta = 1;
    var checkear;
    $(".clasecheckcarta").each(function () {
      if (conta == 1) {
        if ($(this).prop("checked")) {
          checkear = false;
        } else {
          checkear = true;
        }
      }
      $(this).prop("checked", checkear);
      conta = conta + 1;
    });
  });

  $(".seleccion_genera_pdf").click(function () {
    var id_seleccion = "";
    $(".clasecheckcarta").each(function () {
      if ($(this).prop("checked")) {
        id_seleccion = id_seleccion + "|" + $(this).prop("value");
      }
    });
    location.href =
      "views/cartasenvio/pdf.php?Modo=pdf&IDCartasBase=" + id_seleccion;
  });

  $(".seleccion_genera_mail").click(function () {
    var id_seleccion = "";
    $(".clasecheckcarta").each(function () {
      if ($(this).prop("checked")) {
        id_seleccion = id_seleccion + "|" + $(this).prop("value");
      }
    });
    location.href =
      "views/cartasenvio/pdf.php?Modo=email&IDCartasBase=" + id_seleccion;
  });

  $("#frmIngresoCaddie").submit(function () {
    var cedula = $("#NumeroDocumentoC").val();
    var idclub = $("#IDClubC").val();
    var mensaje = "";
    var tipo = "";
    jQuery.ajax({
      type: "POST",
      data: { Cedula: cedula, IDClub: idclub },
      dataType: "json",
      url: "includes/async/ingreso_caddie.async.php",
      success: function (data) {
        if (data == "noencontrado") {
          mensaje = "<br><br>El caddie no existe<br><br>";
          tipo = "warning";
        } else {
          if (data == "yaregistrado") {
            mensaje =
              "<br><br>El caddie ya habia sido registrado el dia de hoy<br><br>";
            tipo = "warning";
          } else {
            if (data == "vacio") {
              mensaje = "<br><br>Documento vacio por favor verifique.<br><br>";
              tipo = "warning";
            } else {
              mensaje = "<br><br>Registro exitoso<br><br>" + data;
              tipo = "success";
            }
          }
        }

        n = noty({
          text: mensaje,
          type: tipo,
          dismissQueue: true,
          layout: "topCenter",
          theme: "defaultTheme",
          modal: true,
          timeout: 1500,
          closeWith: ["button"],
          buttons: false,
          animation: {
            open: { height: "toggle" },
            close: { height: "toggle" },
            easing: "swing",
            speed: 500, // opening & closing animation speed
          },
        });

        document.frmIngresoCaddie.NumeroDocumentoC.focus();
        $("#NumeroDocumentoC").val("");

        //location.href = "accesoinvitado.php";
        return false;
      },
    });
    return false;
  });

  $("#frmUpdateInvitado").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });

    return true;
  });

  $("#frmUpdateInvitadoHotel").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmcanjesolicitudes").submit(function () {
    var detalle;
    $("#IDSocioBeneficiario").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#IDSocioBeneficiario").val() + $(el).val() + "|";
      $("#IDSocioBeneficiario").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmdiagnostico").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });
  $("#frmdiagnostico").submit(function () {
    var detalle;
    $("#UsuarioSeleccion").val("");
    $("#SocioInvitadoUsuario option").map(function (i, el) {
      detalle = $("#UsuarioSeleccion").val() + $(el).val() + "|||";
      $("#UsuarioSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });
  $("#frmdiagnostico").submit(function () {
    var detalle;
    $("#SeleccionGrupo").val("");
    $("#SocioInvitadoGrupo option").map(function (i, el) {
      detalle = $("#SeleccionGrupo").val() + $(el).val() + "|||";
      $("#SeleccionGrupo").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });
  $("#frmdiagnostico").submit(function () {
    var detalle;
    $("#SeleccionGrupoEmpleado").val("");
    $("#EmpleadoInvitadoGrupo option").map(function (i, el) {
      detalle = $("#SeleccionGrupoEmpleado").val() + $(el).val() + "|||";
      $("#SeleccionGrupoEmpleado").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#EditPermisoSocioModuloclubes").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmencuestas2").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmdotaciones").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmcaddie2").submit(function () {
    var detalle;
    $("#IDElemento").val("");
    $("#SocioInvitadoUsuario option").map(function (i, el) {
      detalle = $("#IDElemento").val() + $(el).val() + "|||";
      $("#IDElemento").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmreportetalonera").submit(function () {
    var detalle;
    $("#SociosPosibles").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#SociosPosibles").val() + $(el).val();

      $("#SociosPosibles").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmtelefonoemergencia").submit(function () {
    var Telefono = $("#TelefonoEmergencia").val();
    var IDClub = $("#IDClubTelefono").val();
    jQuery.ajax({
      type: "POST",
      data: { Telefono: Telefono, IDClub: IDClub },
      dataType: "json",
      url: "includes/async/actualiza_numero_emergencia.async.php",
      success: function (data) {
        alert("Numero actualizado con exito");
        return false;
      },
    });
    return false;
  });

  $(".marcarTodo").change(function () {
    var IDServicio = $(this).attr("alt");

    if ($(this).is(":checked")) {
      //$("input[type=checkbox]").prop('checked', true); //todos los check
      //$("#IDServicioElemento"+IDServicio+" input[type=checkbox]").prop('checked', true); //solo los del objeto #diasHabilitados
      $("#IDServicioElemento14 input[type=checkbox]").prop("checked", true); //solo los del objeto #diasHabilitados
    } else {
      //$("input[type=checkbox]").prop('checked', false);//todos los check
      //$("#IDServicioElemento"+IDServicio+" input[type=checkbox]").prop('checked', false);//solo los del objeto #diasHabilitados
      $("#IDServicioElemento14 input[type=checkbox]").prop("checked", false); //solo los del objeto #diasHabilitados
    }
  });

  $("#frmDeleteReserva").submit(function () {
    var detalle;
    var razon =
      $("#RazonCancelacion").val() +
      " Eliminada por: " +
      $("#UsuarioElimina").val();
    var IDReserva = $("#IDReservaGeneral").val();
    if (razon == "") {
      alert("Debe digitar la razon de la cancelacion");
      return false;
    } else {
      if (confirm("Esta seguro que desea cancelar la reserva?")) {
        jQuery.ajax({
          type: "POST",
          data: { IDReservaGeneral: IDReserva, Razon: razon },
          dataType: "json",
          url: "includes/async/cancela_reserva.async.php",
          success: function (data) {
            //alert("Reserva Cancelada con exito");
            alert(data);
            return false;
            //$("#grid-table<?=$key_elemento?>").trigger("reloadGrid");
            return false;
          },
        });
      } else {
        return false;
      }
    }
    return false;
  });

  $("#frmDeleteReservaSorteo").submit(function () {
    var detalle;
    var razon =
      $("#RazonCancelacion").val() +
      " Eliminada por: " +
      $("#UsuarioElimina").val();
    var IDReserva = $("#IDReservaSorteoElemento").val();
    if (razon == "") {
      alert("Debe digitar la razon de la cancelacion");
      return false;
    } else {
      if (confirm("Esta seguro que desea cancelar la inscripción al sorteo?")) {
        jQuery.ajax({
          type: "POST",
          data: { IDReservaSorteoElemento: IDReserva, Razon: razon },
          dataType: "json",
          url: "includes/async/cancela_reserva_sorteo.async.php",
          success: function (data) {
            alert(data);
            return false;
          },
        });
      } else {
        return false;
      }
    }
    return false;
  });

  $("#frmEliminaReservaMasiva").submit(function () {
    var razon =
      $("#RazonCancelacion").val() +
      " Eliminada por: " +
      $("#UsuarioElimina").val();
    var HoraIncio = $("#HoraInicio").val();
    var HoraFin = $("#HoraFin").val();
    var Fecha = $("#FechaReserva").val();
    var IDServicioElemento = $("#IDServicioElemento").val();
    var IDServicio = $("#ids").val();

    if (razon == "") {
      alert("Debe digitar la razon de la cancelacion");
      return false;
    } else {
      if (confirm("Esta seguro que quiere eliminar todas las reservas?")) {
        jQuery.ajax({
          type: "POST",
          data: {
            IDServicio: IDServicio,
            HoraIncio: HoraIncio,
            HoraFin: HoraFin,
            Fecha: Fecha,
            IDServicioElemento: IDServicioElemento,
            Razon: razon,
          },
          dataType: "json",
          url: "includes/async/cancela_reservamasivas.async.php",
          success: function (data) {
            //alert("Reserva Cancelada con exito");
            alert(data);
            return false;
            //$("#grid-table<?=$key_elemento?>").trigger("reloadGrid");
            return false;
          },
        });
      } else {
        return false;
      }
    }
    return false;
  });

  $("#frmReservaGeneral").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });

    return true;
  });

  $("#frmgruposocio").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });

    return true;
  });

  $("#frmrutas").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });

    return true;
  });

  $("#frmnotificaciones").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmnoticias").submit(function () {
    var detalle;
    $("#SeleccionGrupo").val("");
    $("#SocioInvitadoGrupo option").map(function (i, el) {
      detalle = $("#SeleccionGrupo").val() + $(el).val() + "|||";
      $("#SeleccionGrupo").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#SocioClubPermiso").submit(function () {
    var detalle;
    $("#SeleccionServicios").val("");
    $("#SocioServicio option").map(function (i, el) {
      detalle = $("#SeleccionServicios").val() + $(el).val() + "|||";
      $("#SeleccionServicios").val(detalle);
    });
    return true;
  });

  $("#frmtalonera").submit(function () {
    var detalle;
    $("#SeleccionServicios").val("");
    $("#SocioServicio option").map(function (i, el) {
      detalle = $("#SeleccionServicios").val() + $(el).val() + "|||";
      $("#SeleccionServicios").val(detalle);
    });
    return true;
  });

  $("#frmservicio").submit(function () {
    var detalle;
    $("#TipoSocioValidar").val("");
    $("#TipoSocioValidarGrupo option").map(function (i, el) {
      detalle = $("#TipoSocioValidar").val() + $(el).val() + "|||";
      $("#TipoSocioValidar").val(detalle);
    });
    return true;
  });

  $("#frmservicio").submit(function () {
    var detalle;
    $("#CategoriasServicios").val("");
    $("#CategoriasServicioGrupo option").map(function (i, el) {
      detalle = $("#CategoriasServicios").val() + $(el).val() + "|||";
      $("#CategoriasServicios").val(detalle);
    });
    return true;
  });

  $("#frmvotaciones").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });
//configuraciongeneral
  $("#frmencuestas").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });
   $("#frmconfiguraciongeneral").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmconfiguraciongeneral").submit(function () {
    var detalle;
    $("#UsuarioSeleccion").val("");
    $("#SocioInvitadoUsuario option").map(function (i, el) {
      detalle = $("#UsuarioSeleccion").val() + $(el).val() + "|||";

      $("#UsuarioSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmconfiguracioncaddies").submit(function () {
    var detalle;
    $("#IDListaClub").val("");
    $("#ListaClubesCaddies option").map(function (i, el) {
      detalle = $("#IDListaClub").val() + $(el).val() + "|||";

      $("#IDListaClub").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmservicioscaddie").submit(function () {
    var detalle;
    $("#ClubesAplicaServicio").val("");
    $("#ListaClubesCaddies option").map(function (i, el) {
      detalle = $("#ClubesAplicaServicio").val() + $(el).val() + "|||";

      $("#ClubesAplicaServicio").val(detalle);
    });
    return true;
  });

  $("#frmconfiguracioncanjes").submit(function () {
    var detalle;
    $("#PaisesConvenios").val("");
    $("#PaisesConveniosCanjes option").map(function (i, el) {
      detalle = $("#PaisesConvenios").val() + $(el).val() + "|||";

      $("#PaisesConvenios").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmconfiguracioncanjes").submit(function () {
    var detalle;
    $("#CiudadesConvenios").val("");
    $("#CiudadesConveniosCanjes option").map(function (i, el) {
      detalle = $("#CiudadesConvenios").val() + $(el).val() + "|||";

      $("#CiudadesConvenios").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmreportetalonera").submit(function () {
    var detalle;
    $("#SociosPosibles").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#SociosPosibles").val() + $(el).val();

      $("#SociosPosibles").val(detalle);
    });
    return true;
  });

  $("#frmencuestas").submit(function () {
    var detalle;
    $("#UsuarioSeleccion").val("");
    $("#SocioInvitadoUsuario option").map(function (i, el) {
      detalle = $("#UsuarioSeleccion").val() + $(el).val() + "|||";
      $("#UsuarioSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });
  
  $("#frmconfiguraciongeneral").submit(function () {
    var detalle;
    $("#UsuarioSeleccion1").val("");
    $("#SocioInvitadoUsuario option").map(function (i, el) {
      detalle = $("#UsuarioSeleccion1").val() + $(el).val() + "|||";
      $("#UsuarioSeleccion1").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmnoticias").submit(function () {
    var detalle;
    $("#SeleccionGrupo").val("");
    $("#SocioInvitadoGrupo option").map(function (i, el) {
      detalle = $("#SeleccionGrupo").val() + $(el).val() + "|||";
      $("#SeleccionGrupo").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmencuestas").submit(function () {
    var detalle;
    $("#SeleccionGrupo").val("");
    $("#SocioInvitadoGrupo option").map(function (i, el) {
      detalle = $("#SeleccionGrupo").val() + $(el).val() + "|||";
      $("#SeleccionGrupo").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });
  $("#frmconfiguraciongeneral").submit(function () {
    var detalle;
    $("#SeleccionGrupo").val("");
    $("#SocioInvitadoGrupo option").map(function (i, el) {
      detalle = $("#SeleccionGrupo").val() + $(el).val() + "|||";
      $("#SeleccionGrupo").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmbeneficios").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmbeneficios").submit(function () {
    var detalle;
    $("#UsuarioSeleccion").val("");
    $("#SocioInvitadoUsuario option").map(function (i, el) {
      detalle = $("#UsuarioSeleccion").val() + $(el).val() + "|||";
      $("#UsuarioSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });
  $("#frmbeneficios").submit(function () {
    var detalle;
    $("#SeleccionGrupo").val("");
    $("#SocioInvitadoGrupo option").map(function (i, el) {
      detalle = $("#SeleccionGrupo").val() + $(el).val() + "|||";
      $("#SeleccionGrupo").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("#frmgrupoempleado").submit(function () {
    var detalle;
    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
      //$("#InvitadoSeleccion").val($(el).val());
      //$(el).attr('selected', 'selected');
    });
    return true;
  });

  $("input.txtPistola").keypress(function (evt) {
    var key = nav4 ? evt.keyCode : evt.which;
    get_datos_persona($(this).val(), $(this).attr("alt"));
    return false;
  });

  $("input.txtPistola").blur(function (evt) {
    get_datos_persona($(this).val(), $(this).attr("alt"));
    return false;
  }); //end function

  jQuery("#some_text_box").on("input", function () {
    get_datos_persona($(this).val(), $(this).attr("alt"));
  });

  jQuery("input.txtPistola").on("input propertychange paste", function () {
    get_datos_persona($(this).val(), $(this).attr("alt"));
    return false;
  });

  //jQuery('input.busqueda_acceso').on('input propertychange paste', function() {
  $("#busqueda_acceso").on("paste", function () {
    /*
    var buscar = $( "#busqueda_acceso").val();

    clearTimeout($(this).data('timeout'));
    $(this).data('timeout', setTimeout(function(){
        // your code
    alert(buscar);
      }, 200));
    //var buscar = $( "#busqueda_acceso").val();
    //alert(buscar);
    //$( "#frmfrmBuscar").submit();
    //return false;
    */
  });

  $("input[name=PredioIngresoSocio]").click(function (event) {
    var IDIngreso = $(event.target).val();

    var b = document.querySelector(".ingreso_accesov2");

    b.setAttribute("title", IDIngreso);
  });

  $(".PredioDirige").change(function () { });

  $(".ingreso_accesov2").click(function () {
    let id = $(this).attr("id");
    let numeroGrupoFamiliar = id.split("_");
    if (
      numeroGrupoFamiliar[1] != undefined &&
      numeroGrupoFamiliar[1] != undefined
    ) {
      numeroGrupoFamiliar = "_" + numeroGrupoFamiliar[1];
    } else {
      numeroGrupoFamiliar = "";
    }

    var lista_campo = "[";
    var lista_objetos = "[";
    var continuar = "S";
    var tipoacceso = $(this).attr("tipoacceso");

    if (tipoacceso == "unico") {
      var tipoform = "";
      var identificador_p = "";
    } else {
      var tipoform = "fam";
      var identificador_p = $(this).attr("title");
    }

    let numero_objetos = $("#numero_objetos").val();

    let array_objetos = [];
    for (i = 0; i < numero_objetos; i++) {
      if ($("#accesoobjeto_" + i).is(":checked")) {
        array_objetos.push($("#accesoobjeto_" + i).val());
      }
    }

    let totalPredioSocios = $("#totalPredioSocios").val();

    for (i = 1; i <= totalPredioSocios; i++) {
      if ($("#PredioIngresoSocio" + i).is(":checked")) {
        var PredioIngresoSocio = $("#PredioIngresoSocio" + i).val();
      }
    }

    lista_objetos += array_objetos;
    lista_objetos += "]";

    $(".frmcampos" + tipoform + identificador_p).each(function () {
      var tipocampo = $(this).attr("tipocampo");
      var nombrecampo = $(this).attr("name");
      var identificadorcampo = $(this).attr("identificadorpregunta");
      var obligatoriocampo = $(this).attr("obligatorio");
      var etiquetacampo = $(this).attr("etiqueta");
      var contador = 1;
      var valorcampo = "";

      switch (tipocampo) {
        case "textarea":
        case "text":
          if (obligatoriocampo == "S" && $(this).val() == "") {
            alert(etiquetacampo + " no puede estar vacio");
            continuar = "N";
          }
          lista_campo +=
            '{"IDPreguntaAcceso":"' +
            identificadorcampo +
            '","Valor":"' +
            $(this).val() +
            '"},';
          break;
        case "radio":
          if (valorcampo === undefined) {
            var indefinido = 1;
          } else {
            lista_campo +=
              '{"IDPreguntaAcceso":"' +
              identificadorcampo +
              '","Valor":"' +
              $("input:radio[name=" + nombrecampo + "]:checked").val() +
              '"},';
          }
          break;
        case "checkbox":
          valorcampo = $(
            "input:checkbox[name=" + nombrecampo + "]:checked"
          ).val();
          if (valorcampo === undefined) {
            var indefinido = 1;
          } else {
            lista_campo +=
              '{"IDPreguntaAcceso":"' +
              identificadorcampo +
              '","Valor":"' +
              $("input:checkbox[name=" + nombrecampo + "]:checked").val() +
              '"},';
          }
          contador++;
          break;
        case "select":
          alert(obligatoriocampo + "VALOR " + $(this).val());
          if (obligatoriocampo == "S" && $(this).val() == "") {
            alert(etiquetacampo + " no puede estar vacio");
            continuar = "N";
          }
          lista_campo +=
            '{"IDPreguntaAcceso":"' +
            identificadorcampo +
            '","Valor":"' +
            $("select[name=" + nombrecampo + "]").val() +
            '"},';
          break;

        default:
      }
    });

    lista_campo += "]";
    lista_campo = lista_campo.replace("},]", "}]");

    if (continuar == "N") {
      return false;
    }

    if ($(this).is(":checked")) {
      //Valido que si el vehiculo es otro lo debe registrar primero
      var MecanismoEntrada = $(
        "input:radio[name=MecanismoEntradaIngreso" +
        numeroGrupoFamiliar +
        "]:checked"
      ).val();
      if (MecanismoEntrada == undefined || MecanismoEntrada == "") {
        alert(
          "Debe seleccionar el mecanismo de entrada (Peatonal, Vehiculo, etc)."
        );
        return false;
      } else {
        if (MecanismoEntrada == "OtroVehiculo") {
          alert("Por Favor agregue el vehiculo");
          return false;
        } else {
          $(this).attr("disabled", true);
          //if(confirm("Esta seguro de registrar el ingreso?")){
          var modulo = $(this).attr("alt");
          var rowid = $(this).attr("title");

          var mecanismo = $(
            "input:radio[name=MecanismoEntradaIngreso" +
            numeroGrupoFamiliar +
            "]:checked"
          ).val();
          var placaVehiculo = "";
          if (numeroGrupoFamiliar == "") {
            inputPlaca = "Titular";
          } else {
            inputPlaca = numeroGrupoFamiliar;
          }
          if (
            $("#PlacaVehiculo" + inputPlaca) &&
            $("#PlacaVehiculo" + inputPlaca).val() != undefined &&
            $("#PlacaVehiculo" + inputPlaca).val() != "" &&
            mecanismo == "Vehiculo"
          ) {
            placaVehiculo = $("#PlacaVehiculo" + inputPlaca).val();
            mecanismo += " " + placaVehiculo;
          }
          switch (modulo) {
            case "SocioInvitado":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/ingreso_invitado.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  Objetos: lista_objetos,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/accesoobjetos.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "SocioAutorizacion":
              let numero_objetos = $("#numero_objetos").val();

              let array_objetos = [];
              for (i = 0; i < numero_objetos; i++) {
                if ($("#accesoobjeto_" + i).is(":checked")) {
                  array_objetos.push({
                    IDAccesoObjeto: $("#accesoobjeto_" + i).val(),
                  });
                }
              }

              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioAutorizacion: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  AccesoObjetos: array_objetos,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/ingreso_autorizacion.async.php",

                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  Objetos: lista_objetos,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/accesoobjetos.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "SocioInvitadoEspecial":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitadoEspecial: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/ingreso_invitadoespecial.async.php",

                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  Objetos: lista_objetos,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/accesoobjetos.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "Socio":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocio: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/ingreso_socio.async.php",

                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  Objetos: lista_objetos,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/accesoobjetos.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "Usuario":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDUsuario: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/ingreso_usuario.async.php",

                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  Objetos: lista_objetos,
                  PredioIngresoSocio: PredioIngresoSocio,
                },
                dataType: "json",
                url: "includes/async/accesoobjetos.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
          } //Fin switch
        }
      }
      //}
      //else{
      //	return false;
      //}
      return true;
    }
  });

  $(".ingreso_acceso").click(function () {
    if ($(this).is(":checked")) {
      //Valido que si el vehiculo es otro lo debe registrar primero
      var MecanismoEntrada = $(
        "input:radio[name=MecanismoEntradaIngreso]:checked"
      ).val();
      if (MecanismoEntrada == undefined || MecanismoEntrada == "") {
        alert(
          "Debe seleccionar el mecanismo de entrada (Peatonal, Vehiculo, etc)."
        );
        return false;
      } else {
        if (MecanismoEntrada == "OtroVehiculo") {
          alert("Por Favor agregue el vehiculo");
          return false;
        } else {
          $(this).attr("disabled", true);
          //if(confirm("Esta seguro de registrar el ingreso?")){
          var modulo = $(this).attr("alt");
          var rowid = $(this).attr("title");
          var mecanismo = $(
            "input:radio[name=MecanismoEntradaIngreso]:checked"
          ).val();
          switch (modulo) {
            case "SocioInvitado":
              jQuery.ajax({
                type: "POST",
                data: { IDSocioInvitado: rowid, Mecanismo: mecanismo },
                dataType: "json",
                url: "includes/async/ingreso_invitado.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "SocioAutorizacion":
              jQuery.ajax({
                type: "POST",
                data: { IDSocioAutorizacion: rowid, Mecanismo: mecanismo },
                dataType: "json",
                url: "includes/async/ingreso_autorizacion.async.php",

                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "SocioInvitadoEspecial":
              jQuery.ajax({
                type: "POST",
                data: { IDSocioInvitadoEspecial: rowid, Mecanismo: mecanismo },
                dataType: "json",
                url: "includes/async/ingreso_invitadoespecial.async.php",

                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "Socio":
              jQuery.ajax({
                type: "POST",
                data: { IDSocio: rowid, Mecanismo: mecanismo },
                dataType: "json",
                url: "includes/async/ingreso_socio.async.php",

                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
          } //Fin switch
        }
      }
      //}
      //else{
      //	return false;
      //}
      return true;
    }
  });

  $(".salida_accesov2").click(function () {
    let id = $(this).attr("id");
    let numeroGrupoFamiliar = id.split("_");
    if (
      numeroGrupoFamiliar[1] != undefined &&
      numeroGrupoFamiliar[1] != undefined
    ) {
      numeroGrupoFamiliar = "_" + numeroGrupoFamiliar[1];
    } else {
      numeroGrupoFamiliar = "";
    }

    var tipoacceso = $(this).attr("tipoacceso");

    if (tipoacceso == "unico") {
      var tipoform = "";
      var identificador_p = "";
    } else {
      var tipoform = "fam";
      var identificador_p = $(this).attr("title");
    }

    var lista_objetos = "[";
    var lista_campo = "[";

    let numero_objetos = $("#numero_objetos").val();

    let array_objetos = [];
    for (i = 0; i < numero_objetos; i++) {
      if ($("#salidaobjeto_" + i).is(":checked")) {
        array_objetos.push($("#salidaobjeto_" + i).val());
      }
    }

    lista_objetos += array_objetos;
    lista_objetos += "]";
    $(".frmcampos" + tipoform + identificador_p).each(function () {
      var tipocampo = $(this).attr("tipocampo");
      var nombrecampo = $(this).attr("name");
      var identificadorcampo = $(this).attr("identificadorpregunta");
      var contador = 1;
      var valorcampo = "";

      switch (tipocampo) {
        case "textarea":
        case "text":
          lista_campo +=
            '{"IDPreguntaAcceso":"' +
            identificadorcampo +
            '","Valor":"' +
            $(this).val() +
            '"},';
          break;
        case "radio":
          if (valorcampo === undefined) {
            var indefinido = 1;
          } else {
            lista_campo +=
              '{"IDPreguntaAcceso":"' +
              identificadorcampo +
              '","Valor":"' +
              $("input:radio[name=" + nombrecampo + "]:checked").val() +
              '"},';
          }
          break;
        case "checkbox":
          valorcampo = $(
            "input:checkbox[name=" + nombrecampo + "]:checked"
          ).val();
          if (valorcampo === undefined) {
            var indefinido = 1;
          } else {
            lista_campo +=
              '{"IDPreguntaAcceso":"' +
              identificadorcampo +
              '","Valor":"' +
              $("input:checkbox[name=" + nombrecampo + "]:checked").val() +
              '"},';
          }
          contador++;
          break;
        case "select":
          lista_campo +=
            '{"IDPreguntaAcceso":"' +
            identificadorcampo +
            '","Valor":"' +
            $("select[name=" + nombrecampo + "]").val() +
            '"},';
          break;

        default:
      }
    });
    lista_campo += "]";
    lista_campo = lista_campo.replace("},]", "}]");

    if ($(this).is(":checked")) {
      var MecanismoEntrada = $(
        "input:radio[name=MecanismoEntradaIngreso" +
        numeroGrupoFamiliar +
        "]:checked"
      ).val();
      if (MecanismoEntrada == undefined || MecanismoEntrada == "") {
        alert(
          "Debe seleccionar el mecanismo de entrada (Peatonal, Vehiculo, etc)."
        );
        return false;
      } else {
        if (MecanismoEntrada == "OtroVehiculo") {
          alert("Por Favor agregue el vehiculo");
          return false;
        } else {
          $(this).attr("disabled", true);
          //if(confirm("Esta seguro de registrar la salida?")){
          var modulo = $(this).attr("alt");
          var rowid = $(this).attr("title");
          var mecanismo = $(
            "input:radio[name=MecanismoEntradaIngreso" +
            numeroGrupoFamiliar +
            "]:checked"
          ).val();
          var placaVehiculo = "";
          if (numeroGrupoFamiliar == "") {
            inputPlaca = "Titular";
          } else {
            inputPlaca = numeroGrupoFamiliar;
          }
          if (
            $("#PlacaVehiculo" + inputPlaca) &&
            $("#PlacaVehiculo" + inputPlaca).val() != undefined &&
            $("#PlacaVehiculo" + inputPlaca).val() != "" &&
            mecanismo == "Vehiculo"
          ) {
            placaVehiculo = $("#PlacaVehiculo" + inputPlaca).val();
            mecanismo += " " + placaVehiculo;
          }
          switch (modulo) {
            case "SocioInvitado":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioAutorizacion: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                },
                dataType: "json",
                url: "includes/async/ingreso_invitado.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  Objetos: lista_objetos,
                },
                dataType: "json",
                url: "includes/async/accesoobjetos.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "SocioAutorizacion":
              let numero_objetos = $("#numero_objetos").val();

              let array_objetos = [];
              for (i = 0; i < numero_objetos; i++) {
                if ($("#accesoobjeto_" + i).is(":checked")) {
                  array_objetos.push({
                    IDAccesoObjeto: $("#accesoobjeto_" + i).val(),
                  });
                }
              }
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioAutorizacion: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  AccesoObjetos: array_objetos,
                },
                dataType: "json",
                url: "includes/async/ingreso_autorizacion.async.php",

                success: function (data) {
                  //alert("Salida registrada con exito");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return false;
                },
              });
              break;
            case "SocioInvitadoEspecial":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitadoEspecial: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                },
                dataType: "json",
                url: "includes/async/ingreso_invitadoespecial.async.php",

                success: function (data) {
                  //alert("Salida registrada con exito");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  Objetos: lista_objetos,
                },
                dataType: "json",
                url: "includes/async/accesoobjetos.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "Socio":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocio: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                },
                dataType: "json",
                url: "includes/async/ingreso_socio.async.php",

                success: function (data) {
                  //alert("Salida registrada con exito");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  Objetos: lista_objetos,
                },
                dataType: "json",
                url: "includes/async/accesoobjetos.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "Usuario":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDUsuario: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                },
                dataType: "json",
                url: "includes/async/ingreso_usuario.async.php",

                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitado: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                  OtrosCampos: lista_campo,
                  Objetos: lista_objetos,
                },
                dataType: "json",
                url: "includes/async/accesoobjetos.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
          }

          return true;
        }
      }
      //}
      //else{
      //	return false;
      //}
      return true;
    }
  });

  $(".salida_acceso").click(function () {
    if ($(this).is(":checked")) {
      var MecanismoEntrada = $(
        "input:radio[name=MecanismoEntradaIngreso]:checked"
      ).val();
      if (MecanismoEntrada == undefined || MecanismoEntrada == "") {
        alert(
          "Debe seleccionar el mecanismo de entrada (Peatonal, Vehiculo, etc)."
        );
        return false;
      } else {
        if (MecanismoEntrada == "OtroVehiculo") {
          alert("Por Favor agregue el vehiculo");
          return false;
        } else {
          $(this).attr("disabled", true);
          //if(confirm("Esta seguro de registrar la salida?")){
          var modulo = $(this).attr("alt");
          var rowid = $(this).attr("title");
          var mecanismo = $(
            "input:radio[name=MecanismoEntradaIngreso]:checked"
          ).val();
          switch (modulo) {
            case "SocioInvitado":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioAutorizacion: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                },
                dataType: "json",
                url: "includes/async/ingreso_invitado.async.php",
                success: function (data) {
                  //alert("Registro exitoso");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "SocioAutorizacion":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioAutorizacion: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                },
                dataType: "json",
                url: "includes/async/ingreso_autorizacion.async.php",

                success: function (data) {
                  //alert("Salida registrada con exito");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return false;
                },
              });
              break;
            case "SocioInvitadoEspecial":
              jQuery.ajax({
                type: "POST",
                data: {
                  IDSocioInvitadoEspecial: rowid,
                  Tipo: "Salida",
                  Mecanismo: mecanismo,
                },
                dataType: "json",
                url: "includes/async/ingreso_invitadoespecial.async.php",

                success: function (data) {
                  //alert("Salida registrada con exito");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
            case "Socio":
              jQuery.ajax({
                type: "POST",
                data: { IDSocio: rowid, Tipo: "Salida", Mecanismo: mecanismo },
                dataType: "json",
                url: "includes/async/ingreso_socio.async.php",

                success: function (data) {
                  //alert("Salida registrada con exito");
                  n = noty({
                    text: "<br><br>Registro exitoso<br><br>",
                    type: "success",
                    dismissQueue: true,
                    layout: "topCenter",
                    theme: "defaultTheme",
                    modal: true,
                    timeout: 1500,
                    closeWith: ["button"],
                    buttons: false,
                    animation: {
                      open: { height: "toggle" },
                      close: { height: "toggle" },
                      easing: "swing",
                      speed: 500, // opening & closing animation speed
                    },
                  });
                  document.frmfrmBuscar.qryString.focus();
                  //location.href = "accesoinvitado.php";
                  return true;
                },
              });
              break;
          }

          return true;
        }
      }
      //}
      //else{
      //	return false;
      //}
      return true;
    }
  });

  $(".PublicarCalificacionDirectorio").click(function () {
    if ($(this).is(":checked")) {
      var id_registro = $(this).attr("alt");
      var tabla = $(this).attr("lang");
      var publicar = $(
        "input:radio[name=Publicar" + id_registro + "]:checked"
      ).val();

      jQuery.ajax({
        type: "POST",
        data: { ID: id_registro, Publicar: publicar, Tabla: tabla },
        dataType: "json",
        url: "includes/async/actualiza_estado_calificacion.async.php",
        success: function (data) {
          alert("Estado actualizado con exito");
        },
      });

      return true;
    }
  });

  $("#IDTipoInvitado").change(function () {
    var id = $(this).val();
    $("#IDClasificacionInvitado").load(
      "includes/async/clasificacioninvitado.async.php?idtipo=" + id
    );
  });

  $("#IDServicioReporteRecargaTalonera").change(function () {
    var id = $(this).val();
    $("#IDElementoReporteRecargaTalonera").load(
      "includes/async/reporterecargatalonera.async.php?idservicio=" + id
    );
  });

  $("#IDClubReport").change(function () {
    var id = $(this).val();
    $("#IDServicioMaestro").load(
      "includes/async/servicioclub.async.php?idclub=" + id
    );
  });

  $("#IDPais").change(function () {
    var id = $(this).val();
    $("#IDCiudad").load(
      "includes/async/baseclubesciudad.async.php?idpais=" + id
    );
  });

  $("#IDServicioTalonera").change(function () {
    var id = $(this).val();
    $("#IDTalonera").load(
      "includes/async/reportetalonera.async.php?idtalonera=" + id
    );
  });

  $("#pais").change(function () {
    var pais = $(this).val();

    $("#ciudad").html("Cargando...");

    $.post(
      "plataform/includes/async/ciudades.async.php",
      { pais: pais },
      function (htmlexterno) {
        $("#ciudad").html(htmlexterno);
      }
    );
  });

  $("#buscarinvitado").click(function () {
    var Servicio = $(this).attr("rel");
    var Fecha = $(this).attr("fecha");
    var Invitado = $("#invitado").val();

    $("#DatosInvitado").html("Cargando...");

    $.post(
      "includes/async/buscarinvitadoreserva.async.php",
      { Servicio: Servicio, Invitado: Invitado, Fecha: Fecha },
      function (htmlexterno) {
        $("#DatosInvitado").html(htmlexterno);
      }
    );
  });

  $("#IDElementoSeleccion").change(function () {
    var id = $(this).val();
    var Fecha = $("#fecha").val();
    var ids = $("#ids").val();
    window.location.href =
      "reservas_admin.php?ids=" +
      ids +
      "&action=new&fecha=" +
      Fecha +
      "&IDElementoSelecc=" +
      id;
  });

  $(".btnShow").click(function () {
    var element = $(this).attr("rel");
    $("#" + element).removeClass("hide");
    $("#" + element).show(300);
    return false;
  });

  $("#NumeroDocumentoP").focus();

  $("#frmIngresoPistola #SegundoNombreP").blur(function () {
    var numerodocumento = parseInt($.trim($("#NumeroDocumentoP").val()), 10);
    var primerapellido = $.trim($("#PrimerApellidoP").val());
    var segundoapellido = $.trim($("#SegundoApellidoP").val());
    var primernombre = $.trim($("#PrimerNombreP").val());
    var segundonombre = $.trim($("#SegundoNombreP").val());

    if (numerodocumento > 0) {
      jQuery.ajax({
        type: "POST",
        data: {
          numerodocumento: numerodocumento,
          primerapellido: primerapellido,
          segundoapellido: segundoapellido,
          primernombre: primernombre,
          segundonombre: segundonombre,
        },
        dataType: "json",
        url: "includes/async/invitados_ingreso_pistola.async.php",
        success: function (data) {
          console.log(data);
          $("#contentMsgP").html(data.msg);
        },
      });
    }

    $("#frmIngresoPistola input").val("");
    $("#NumeroDocumentoP").focus();

    $("#grid-table").trigger("reloadGrid");
    $("#grid-tableingresado").trigger("reloadGrid");

    return false;
  });

  $("#opcvisualizacion").click(function () {
    $("#cargaexterna").html("Cargando...");
    //var Fecha = "2019-09-02";
    var Fecha = $("#fechaseleccion").val();
    var IDClubSeleccionado = $("#IDClubSeleccionado").val();
    var IDServicioSeleccionado = $("#IDServicioSeleccionado").val();
    var ElementosSeleccionado = $("#ElementosSeleccionado").val();
    $.post(
      "view_reserva_app.php",
      {
        fecha: Fecha,
        IDClub: IDClubSeleccionado,
        IDServicio: IDServicioSeleccionado,
        ElementosSeleccionado: ElementosSeleccionado,
      },
      function (htmlexterno) {
        $("#cargaexterna").html(htmlexterno);
      }
    );
  });

  $(".calendar_reservas")
    .datepicker({ format: "yyyy-mm-dd" })
    .on("changeDate", function (e) {
      var fecha = e.format();
      var contador = 1;

      var grillas = $("#grillas").val();

      var array_grillas = [];
      array_grillas = grillas.split(",");

      var total_grillas = array_grillas.length;

      $("#fechaseleccion").val(fecha);

      $(".calendar_reservas").datepicker("hide");

      $.each(array_grillas, function (index, value) {
        var grilla = "#grid-table" + value;
        $(grilla)
          .jqGrid("setGridParam", {
            postData: { fecha: fecha },
          })
          .trigger("reloadGrid");
      });

      var Fecha = fecha;
      var IDClubSeleccionado = $("#IDClubSeleccionado").val();
      var IDServicioSeleccionado = $("#IDServicioSeleccionado").val();
      var ElementosSeleccionado = $("#ElementosSeleccionado").val();
      var valor_selecc = $("#opcvisualizacion").attr("aria-expanded");
      if (valor_selecc == "true") {
        $.post(
          "view_reserva_app.php",
          {
            fecha: Fecha,
            IDClub: IDClubSeleccionado,
            IDServicio: IDServicioSeleccionado,
            ElementosSeleccionado: ElementosSeleccionado,
          },
          function (htmlexterno) {
            $("#cargaexterna").html(htmlexterno);
          }
        );
      }

      //$('#pestana').attr("aria-expanded", "false");

      $("#contentFechaActual").html(fecha);

      if (contador == 1) {
        $("#cargaexterna").html("Cargando...");
        $("#reservagrupos").html("Cargando...");
        /*
          $.post("view_reserva_app.php", {fecha: Fecha, IDClub: IDClubSeleccionado, IDServicio: IDServicioSeleccionado, ElementosSeleccionado: ElementosSeleccionado}, function(htmlexterno){
            $("#cargaexterna").html(htmlexterno);
          });
          */

        /*
    $.post("view_reserva_app_grupos.php", {fecha: Fecha, IDClub: IDClubSeleccionado, IDServicio: IDServicioSeleccionado}, function(htmlexterno){
      $("#reservagrupos").html(htmlexterno);
    });
    */
      }

      contador = 2;
      $('.divsemana').each(function (index, obj) {
        let style = this.getAttribute("style");

        if (style == "" || style == 'display: block;') {
          let iddiv = this.getAttribute("id");
          let elemento = iddiv.slice(7, -5);
          cargarsemana(elemento);
        }

      });
    });


  $(".calendar_reservas2")
    .datepicker({ format: "yyyy-mm-dd" })
    .on("changeDate", function (e) {
      var fecha = e.format();
      var contador = 1;

      var grillas = $("#grillas").val();

      var array_grillas = [];
      array_grillas = grillas.split(",");

      var total_grillas = array_grillas.length;

      $("#fechaseleccion").val(fecha);

      $(".calendar_reservas2").datepicker("hide");

      $.each(array_grillas, function (index, value) {
        var grilla = "#grid-table2" + value;
        $(grilla)
          .jqGrid("setGridParam", {
            postData: { fecha: fecha },
          })
          .trigger("reloadGrid");
      });

      var Fecha = fecha;
      var IDClubSeleccionado = $("#IDClubSeleccionado").val();
      var IDServicioSeleccionado = $("#IDServicioSeleccionado").val();
      var ElementosSeleccionado = $("#ElementosSeleccionado").val();
      var valor_selecc = $("#opcvisualizacion").attr("aria-expanded");
      if (valor_selecc == "true") {
        $.post(
          "view_reserva_app.php",
          {
            fecha: Fecha,
            IDClub: IDClubSeleccionado,
            IDServicio: IDServicioSeleccionado,
            ElementosSeleccionado: ElementosSeleccionado,
          },
          function (htmlexterno) {
            $("#cargaexterna").html(htmlexterno);
          }
        );
      }

      //$('#pestana').attr("aria-expanded", "false");

      $("#contentFechaActual").html(fecha);

      if (contador == 1) {
        $("#cargaexterna").html("Cargando...");
        $("#reservagrupos").html("Cargando...");
        /*
      $.post("view_reserva_app.php", {fecha: Fecha, IDClub: IDClubSeleccionado, IDServicio: IDServicioSeleccionado, ElementosSeleccionado: ElementosSeleccionado}, function(htmlexterno){
        $("#cargaexterna").html(htmlexterno);
      });
      */

        /*
      $.post("view_reserva_app_grupos.php", {fecha: Fecha, IDClub: IDClubSeleccionado, IDServicio: IDServicioSeleccionado}, function(htmlexterno){
        $("#reservagrupos").html(htmlexterno);
      });
      */
      }

      contador = 2;
    });

  preparaform();

  $("#searchCheckin, #buscarviajes").click(function () {
    var script = $(this).attr("rel");
    var FechaInicio = $("#FechaInicio").val();
    var FechaFin = $("#FechaFinal").val();
    var IDTipoVehiculo = $("#IDTipoVehiculo").val();
    var calificacioninicial = $("#calificacioninicial").val();
    var calificacionfinal = $("#calificacionfinal").val();
    var IDMotivosCalificacion = $("#IDMotivosCalificacion").val();
    var Persona = $("#Persona").val();
    var Estado = $("#Estado").val();

    location.href =
      script +
      "&inicio=" +
      FechaInicio +
      "&fin=" +
      FechaFin +
      "&IDTipoVehiculo=" +
      IDTipoVehiculo +
      "&calificacioninicial=" +
      calificacioninicial +
      "&calificacionfinal=" +
      calificacionfinal +
      "&IDMotivosCalificacion=" +
      IDMotivosCalificacion +
      "&Persona=" +
      Persona +
      "&Estado=" +
      Estado;
  });

});

function preparaform() {
  $("form.formvalida").submit(function () {
    //alert("aca");
    return EvaluaReg(this);
  });

  $(".calendar").datepicker({
    format: "yyyy-mm-dd",
  });

  $(".calendario_inicio_hotel")
    .datepicker({ format: "yyyy-mm-dd" })
    .on("changeDate", function (e) {
      var fecha_seleccionada = e.format();
      var fecha = new Date(fecha_seleccionada);
      var dias = 2; // Número de días a agregar
      fecha.setDate(fecha.getDate() + dias);
      var fechafin =
        fecha.getFullYear() +
        "-" +
        (fecha.getMonth() + 1) +
        "-" +
        fecha.getDate();
      $(".calendario_fin_hotel").val(fechafin);
    });

  $(".canchapolo").change(function () {
    var nombre_select = $(this).attr("name");
    var datos_socio = nombre_select.split("_");
    var id_socio = datos_socio[1];
    var id_reserva = datos_socio[2];
    var cancha = $(this).val();
    var nombre_select_equipo = id_socio + "_" + id_reserva;
    var equipo = $("#Equipo_" + nombre_select_equipo).val();
    if ($("#checkquintojugador_" + nombre_select_equipo).prop("checked")) {
      var quintojugador = "S";
    } else {
      var quintojugador = "N";
    }

    if (equipo != "" && cancha != "") {
      $("#msgcancha" + id_reserva).html(
        "<span style='color:#FF0004'>Guardando, por favor espere...</span>"
      );
      jQuery.ajax({
        type: "POST",
        data: {
          IDReserva: id_reserva,
          Cancha: cancha,
          Equipo: equipo,
          QuintoJugador: quintojugador,
        },
        dataType: "json",
        url: "includes/async/actualiza_cancha_equipo.async.php",
        success: function (data) {
          //alert("Guardado con exito");
          $("#msgcancha" + id_reserva).html("");
        },
      });
    }
  });

  $("input[class='btnincluirvotacionevento']").change(function () {
    var valor = $(this).val();
    var IDVotacion = $(this).attr("idvotacion");
    var IDVotacionEvento = $(this).attr("idvotacionevento");
    var IDUsuario = $(this).attr("usuarioregistra");

    $("#msgupdate" + IDVotacion).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );
    jQuery.ajax({
      type: "POST",
      data: {
        IDVotacion: IDVotacion,
        IDVotacionEvento: IDVotacionEvento,
        IDUsuario: IDUsuario,
        Valor: valor,
      },
      dataType: "json",
      url: "includes/async/votacionasociarevento.async.php",
      success: function (data) {
        $("#msgupdate" + IDVotacion).html("");
      },
    });
  });
  $("input[class='btnpublicarcomentario']").change(function () {
    var valor = $(this).val();
    var IDNoticiaComentario = $(this).attr("idnoticiacomentario");

    $("#msgupdate" + IDNoticiaComentario).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );
    jQuery.ajax({
      type: "POST",
      data: { IDNoticiaComentario: IDNoticiaComentario, Valor: valor },
      dataType: "json",
      url: "includes/async/publicarcomentario.async.php",
      success: function (data) {
        $("#msgupdate" + IDNoticiaComentario).html("");
      },
    });
  });

  $("input[class='btnpublicarcomentarionoticiainfinita']").change(function () {
    var valor = $(this).val();
    var IDNoticiaComentario = $(this).attr("idnoticiacomentario");

    $("#msgupdate" + IDNoticiaComentario).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );
    jQuery.ajax({
      type: "POST",
      data: { IDNoticiaComentario: IDNoticiaComentario, Valor: valor },
      dataType: "json",
      url: "includes/async/publicarcomentarionoticiainfinita.async.php",
      success: function (data) {
        $("#msgupdate" + IDNoticiaComentario).html("");
      },
    });
  });

  $("input[class='btncambiosocio']").change(function () {
    var valor = $(this).val();
    var IDSocio = $(this).attr("idsocio");
    var Campo = $(this).attr("campo");

    if (Campo == "Estado")
      $("#msgupdate" + IDSocio).html(
        "<span style='color:#FF0004'>Guardando...</span>"
      );
    else if (Campo == "PermiteReserva")
      $("#msgupdateReserva" + IDSocio).html(
        "<span style='color:#FF0004'>Guardando...</span>"
      );

    jQuery.ajax({
      type: "POST",
      data: { IDSocio: IDSocio, Valor: valor, Campo: Campo },
      dataType: "json",
      url: "includes/async/cambiosocio.async.php",
      success: function (data) {
        if (Campo == "Estado") $("#msgupdate" + IDSocio).html("");
        else if (Campo == "PermiteReserva")
          $("#msgupdateReserva" + IDSocio).html("");
      },
    });
  });

  $(".update_registro").click(function () {
    var ID = $(this).attr("id");
    var estado = $("input[name=Estado" + ID + "]:checked").val();
    var comentario = $("#ComentarioRevision" + ID).val();

    $("#msgupdate" + ID).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );
    jQuery.ajax({
      type: "POST",
      data: { ID: ID, estado: estado, comentario: comentario },
      dataType: "json",
      url: "includes/async/cambioEstadoCheckinLaboral.async.php",
      success: function (data) {
        $("#msgupdate" + ID).html("");
      },
    });
  });

  $(".update_registro_funcionarios").click(function () {
    var ID = $(this).attr("id");
    var estado = $("input[name=Estado" + ID + "]:checked").val();
    var comentario = $("#ComentarioRevision" + ID).val();

    $("#msgupdate" + ID).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );
    jQuery.ajax({
      type: "POST",
      data: {
        ID: ID,
        estado: estado,
        comentario: comentario,
        oper: "cambiaEstado",
      },
      dataType: "json",
      url: "includes/async/checkinfuncionariosDetalle.async.php",
      success: function (data) {
        $("#msgupdate" + ID).html("");
        jQuery("#grid-table").trigger("reloadGrid");
      },
    });
  });

  $(".btncambioreserva")
    .off()
    .change(function () {
      var valor = $(this).val();
      var IDReservaGeneral = $(this).attr("IDReservaGeneral");
      var Campo = $(this).attr("campo");

      $("#msgupdate" + IDReservaGeneral).html(
        "<span style='color:#FF0004'>Guardando...</span>"
      );

      jQuery.ajax({
        type: "POST",
        data: {
          IDReservaGeneral: IDReservaGeneral,
          Valor: valor,
          Campo: Campo,
        },
        dataType: "json",
        url: "includes/async/cambioreserva.async.php",
        success: function (data) {
          $("#msgupdate" + IDReservaGeneral).html("");
        },
      });
    });

  $("input[class='btnpublicarproducto']").change(function () {
    var valor = $(this).val();
    var IDProducto = $(this).attr("idproducto");
    var Version = $(this).attr("version");

    $("#msgupdateProducto" + IDProducto).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );

    jQuery.ajax({
      type: "POST",
      data: { IDProducto: IDProducto, Valor: valor, Version: Version },
      dataType: "json",
      url: "includes/async/cambioproducto.async.php",
      success: function (data) {
        $("#msgupdateProducto" + IDProducto).html("");
      },
    });
  });

  $("input[class='btnActivarTalonera']").change(function () {
    var valor = $(this).val();
    var IDTalonera = $(this).attr("idtalonera");
    var IDSocioTalonera = $(this).attr("idsociotalonera");
    var Version = $(this).attr("version");

    $("#msgupdateTalonera" + IDTalonera).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );

    jQuery.ajax({
      type: "POST",
      data: {
        IDTalonera: IDTalonera,
        Valor: valor,
        Version: Version,
        IDSocioTalonera: IDSocioTalonera,
      },
      dataType: "json",
      url: "includes/async/DesactivarTalonera.async.php",
      success: function (data) {
        $("#msgupdateTalonera" + IDTalonera).html("");
      },
    });
  });

  $("input[class='btnpublicarcategoria']").change(function () {
    var valor = $(this).val();
    var IDCategoria = $(this).attr("IDCategoria");
    var Version = $(this).attr("version");

    $("#msgupdatecategoria" + IDCategoria).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );

    jQuery.ajax({
      type: "POST",
      data: {
        IDCategoria: IDCategoria,
        Valor: valor,
        Tabla: "Categoria",
        Version: Version,
      },
      dataType: "json",
      url: "includes/async/cambiodomicilio.async.php",
      success: function (data) {
        $("#msgupdatecategoria" + IDCategoria).html("");
      },
    });
  });
  $("input[class='btnpublicarrestaurante']").change(function () {
    var valor = $(this).val();
    var IDRestaurante = $(this).attr("IDRestaurante");
    var Version = $(this).attr("version");

    $("#msgupdaterestaurante" + IDRestaurante).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );

    jQuery.ajax({
      type: "POST",
      data: {
        IDRestaurante: IDRestaurante,
        Valor: valor,
        Tabla: "Restaurante",
        Version: Version,
      },
      dataType: "json",
      url: "includes/async/cambiodomicilio.async.php",
      success: function (data) {
        $("#msgupdaterestaurante" + IDRestaurante).html("");
      },
    });
  });
  $("input[class='btnestadodomicilio']").change(function () {
    var valor = $(this).val();
    var IDDomicilio = $(this).attr("IDDomicilio");
    var Version = $(this).attr("version");

    $("#msgupdatedomi" + IDDomicilio).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );

    jQuery.ajax({
      type: "POST",
      data: {
        IDDomicilio: IDDomicilio,
        Valor: valor,
        Tabla: "Domicilio",
        Version: Version,
      },
      dataType: "json",
      url: "includes/async/cambiodomicilio.async.php",
      success: function (data) {
        $("#msgupdatedomi" + IDDomicilio).html("");
      },
    });
  });
  $("input[class='btnnotificadomicilio']").change(function () {
    var valor = $(this).val();
    var IDSocio = $(this).attr("IDSocio");
    var Version = $(this).attr("version");

    $("#msgupdatedomi" + IDDomicilio).html(
      "<span style='color:#FF0004'>Notificado...</span>"
    );

    jQuery.ajax({
      type: "POST",
      data: { IDSocio: IDSocio, Valor: valor, Tabla: "Notificacion" },
      dataType: "json",
      url: "includes/async/cambiodomicilio.async.php",
      success: function (data) {
        $("#msgupdatedomi" + IDDomicilio).html("");
      },
    });
  });

  $("input[class='btnmostrarresult']").change(function () {
    var valor = $(this).val();
    var IDVotacion = $(this).attr("idvotacion");
    var IDVotacionEvento = $(this).attr("idvotacionevento");
    var IDUsuario = $(this).attr("usuarioregistra");

    $("#msgupdatemostrar" + IDVotacion).html(
      "<span style='color:#FF0004'>Guardando...</span>"
    );
    jQuery.ajax({
      type: "POST",
      data: {
        IDVotacion: IDVotacion,
        IDVotacionEvento: IDVotacionEvento,
        IDUsuario: IDUsuario,
        Valor: valor,
      },
      dataType: "json",
      url: "includes/async/votacionmostrarresult.async.php",
      success: function (data) {
        $("#msgupdatemostrar" + IDVotacion).html("");
      },
    });
  });

  $(".equipopolo").change(function () {
    var nombre_select = $(this).attr("name");
    var datos_socio = nombre_select.split("_");
    var id_socio = datos_socio[1];
    var id_reserva = datos_socio[2];
    var equipo = $(this).val();
    var nombre_select_cancha = id_socio + "_" + id_reserva;
    var cancha = $("#Cancha_" + nombre_select_cancha).val();
    if ($("#checkquintojugador_" + nombre_select_cancha).prop("checked")) {
      var quintojugador = "S";
    } else {
      var quintojugador = "N";
    }

    if (equipo != "" && cancha != "") {
      $("#msgcancha" + id_reserva).html(
        "<span style='color:#FF0004'>Guardando, por favor espere...</span>"
      );
      jQuery.ajax({
        type: "POST",
        data: {
          IDReserva: id_reserva,
          Cancha: cancha,
          Equipo: equipo,
          QuintoJugador: quintojugador,
        },
        dataType: "json",
        url: "includes/async/actualiza_cancha_equipo.async.php",
        success: function (data) {
          //alert("Guardado con exito");
          $("#msgcancha" + id_reserva).html("");
        },
      });
    }
  });

  $("#btnRegistroPoder").click(function () {
    var IDVotadorPadre = $("#IDVotadorPadre").val();
    var IDClub = $("#IDClub").val();
    var IDVotacionEvento = $("#IDVotacionEvento").val();
    var IDUsuarioRegistra = $("#IDUsuarioRegistra").val();
    var IDVotadorOtorga = $("#IDVotacionVotante").val();

    $("#msgguardar").html(
      "<span style='color:#FF0004'>Guardando, por favor espere...</span>"
    );
    jQuery.ajax({
      type: "POST",
      data: {
        IDVotadorPadre: IDVotadorPadre,
        IDClub: IDClub,
        IDVotacionEvento: IDVotacionEvento,
        IDUsuarioRegistra: IDUsuarioRegistra,
        IDVotadorOtorga: IDVotadorOtorga,
      },
      dataType: "json",
      url: "includes/async/guardarpoder.async.php",
      success: function (data) {
        if (data == "ok") {
          $("#msgguardar").html(
            "<span style='color:#5DA85E'>Poder otorgado correctamente</span>"
          );
        } else {
          if (data == "repetido") {
            $("#msgguardar").html(
              "<span style='color:#FF0004'>El usuario seleccionado ya otorgo el poder a otra persona  o tiene el poder de otra persona , por favor verifique</span>"
            );
          } else {
            if (data == "consejero") {
              $("#msgguardar").html(
                "<span style='color:#FF0004'>ATENCION: No se puede otorgar el poder a un consejero! </span>"
              );
            } else {
              $("#msgguardar").html(
                "<span style='color:#FF0004'>Error general, intente mas tarde</span>"
              );
            }
          }
        }
      },
    });
  });

  $(".quinto_jugador").click(function () {
    var marcado = this.checked;
    var nombre_select = $(this).attr("name");
    var datos_socio = nombre_select.split("_");
    var id_socio = datos_socio[1];
    var id_reserva = datos_socio[2];
    var nombre_select_cancha = id_socio + "_" + id_reserva;

    var cancha = $("#Cancha_" + nombre_select_cancha).val();
    var equipo = $("#Equipo_" + nombre_select_cancha).val();
    if (equipo != "" && cancha != "" && marcado == true) {
      $("#msgcancha" + id_reserva).html(
        "<span style='color:#FF0004'>Guardando, por favor espere...</span>"
      );
      jQuery.ajax({
        type: "POST",
        data: {
          IDReserva: id_reserva,
          Cancha: cancha,
          Equipo: equipo,
          QuintoJugador: "S",
        },
        dataType: "json",
        url: "includes/async/actualiza_cancha_equipo.async.php",
        success: function (data) {
          //alert("Guardado con exito");
          $("#msgcancha" + id_reserva).html("");
        },
      });
    }
  });

  $("#btnequipopolo").click(function () {
    $("#btnequipopolo").attr(
      "href",
      "resumenpolo.php?Fecha=" + $("#fechaseleccion").val() + "&OptImprimir=1"
    );
  });

  $("#btnequipopolopino").click(function () {
    $("#btnequipopolopino").attr(
      "href",
      "resumenpolopino.php?Fecha=" +
      $("#fechaseleccion").val() +
      "&OptImprimir=1"
    );
  });

  $("#ConsultaFechaPolo").click(function () {
    var fecha = $("#FechaPolo").val();
    window.location.href = "resumenpolo.php?Fecha=" + fecha;
  });

  $("#ConsultaFechaPoloPino").click(function () {
    var fecha = $("#FechaPolo").val();
    window.location.href = "resumenpolopino.php?Fecha=" + fecha;
  });

  $(".calendar_nueva_reservas")
    .datepicker({ format: "yyyy-mm-dd" })
    .on("changeDate", function (e) {
      var fecha = e.format();
      var ids = $("#ids").val();

      location.href =
        "reservas_admin.php?ids=" + ids + "&action=new&fecha=" + fecha;
    });

  $(".guardar_fotogaleria").click(function () {
    var identificador_foto = $(this).attr("alt");
    var orden_foto = $("#Orden" + identificador_foto).val();
    var descripcion_foto = $("#Descripcion" + identificador_foto).val();
    //location.href = link;
    jQuery.ajax({
      type: "POST",
      data: {
        ID: identificador_foto,
        Orden: orden_foto,
        Descripcion: descripcion_foto,
      },
      dataType: "json",
      url: "includes/async/actualiza_foto.async.php",
      success: function (data) {
        alert("Datos guardados con exito");
        //window.location.href=redireccionar;
      },
    });
    return false;
  });

  $(".btnBuscarSocio").click(function () {
    var grillas = $("#grillas").val();
    var array_grillas = [];
    array_grillas = grillas.split(",");
    $.each(array_grillas, function (index, value) {
      var grilla = "#grid-table" + value;
      $(grilla)
        .jqGrid("setGridParam", {
          postData: {
            Accion: $("#Accion").val(),
            oper: "searchurl",
          },
        })
        .trigger("reloadGrid");

    });
    $('.divsemana').each(function (index, obj) {
      let style = this.getAttribute("style");

      if (style == "" || style == 'display: block;') {
        let iddiv = this.getAttribute("id");
        let elemento = iddiv.slice(7, -5);
        cargarsemana(elemento);
      }

    });
    return false;
  });

  $(".eliminar_registro").click(function () {
    var tabla = $(this).attr("rel");
    var id = $(this).attr("id");
    var redireccionar = $(this).attr("lang");
    if (confirm("Esta seguro que desea borrar el registro?")) {
      jQuery.ajax({
        type: "POST",
        data: { Tabla: tabla, ID: id },
        dataType: "json",
        url: "includes/async/elimina_registro.async.php",

        success: function (data) {
          if (data == "ok") {
            alert("Registro Eliminado con exito");
          } else {
            alert(data);
          }
          window.location.href = redireccionar + ".php";
        },
      });
    }

    return false;
  });

  $('.descargar-zip').click(function () {
    var id = $(this).attr("id");
    var socio = $(this).attr("data-socio");
    var redirect = $(this).attr("data-url");
    if (confirm("Esta seguro que desea descargar los archivos?")) {
      var url = redirect + "files/downloadZip.php?id=" + id + "&socio=" + socio;
      window.open(url, '_blank');
    }
    return false;
  });
  $(".exportar-luker").click(function () {
    var tabla = $(this).attr("rel");
    var id = $(this).attr("id");
    var socio = $(this).attr("data-socio");
    if (confirm("Esta seguro que desea exportar a Luker?")) {
      jQuery.ajax({
        type: "POST",
        data: { Tabla: tabla, ID: id, Socio: socio },
        dataType: "json",
        url: "includes/async/set_ingresos_luker.async.php",

        success: function (data) {
          if (data == "ok") {
            alert("Registro grabado con exito");
          } else {
            alert(data);
          }
          window.location.href = redireccionar + ".php";
        },
      });
    }

    return false;
  });

  $(".Copiar_Encuesta").click(function () {
    if (confirm("Esta seguro que desea copiar la encuesta?")) {
      return true;
    } else {
      return false;
    }
  });

  $("#EliminarSeleccion").click(function () {
    if (confirm("Esta seguro que desea borrar los registros seleccionados?")) {
      return true;
    } else {
      return false;
    }
  });

  $(".finalizar_aut").click(function () {
    var tabla = $(this).attr("rel");
    var id = $(this).attr("id");
    if (confirm("Esta seguro que desea finalizar la autorizacion?")) {
      jQuery.ajax({
        type: "POST",
        data: { Tabla: tabla, ID: id },
        dataType: "json",
        url: "includes/async/cancela_autorizacion.async.php",

        success: function (data) {
          alert(
            "La autorizacion se modificó con fecha final hasta el día de hoy"
          );
        },
      });
    }

    return false;
  });

  $(".cancelar_reserva").click(function () {
    var IDSocio = $(this).attr("rel");
    var IDReserva = $(this).attr("id");
    var IDClub = $(this).attr("lang");

    if (confirm("Esta seguro que desea cancelar la reserva?")) {
      jQuery.ajax({
        type: "POST",
        data: { IDSocio: IDSocio, IDReserva: IDReserva, IDClub: IDClub },
        dataType: "json",
        url: "includes/async/cancela_reserva.async.php",

        success: function (data) {
          alert("Reserva Cancelada con exito");
          window.location.href = redireccionar;
        },
      });
    }

    return false;
  });

  $("#frmdisponibilidad input[name='PermiteRepeticion']").click(function () {
    if ($(this).val() === "S") {
      $("#div_repeticion").show();
    } else if ($(this).val() === "N") {
      $("#div_repeticion").hide();
    }
  });

  $("#frmreservashotel input[name='CabezaReserva']").click(function () {
    if ($(this).val() === "Socio") {
      $("#div_NombreDuenoReserva").hide();
    } else if ($(this).val() === "Invitado") {
      $("#div_NombreDuenoReserva").show();
    }
  });

  $("#frmdisponibilidad input[name='PermiteReservaCumplirTurno']").click(
    function () {
      if ($(this).val() === "S") {
        $("#div_tiempo_despues").show();
      } else if ($(this).val() === "N") {
        $("#div_tiempo_despues").hide();
      }
    }
  );

  $("input[name='Georeferenciacion']").click(function () {
    if ($(this).val() == "S") {
      $("#div_geo").show();
    } else {
      $("#div_geo").hide();
    }
  });

  $("input[name='GeoreferenciacionContratista']").click(function () {
    if ($(this).val() == "S") {
      $("#div_geo_contratista").show();
    } else {
      $("#div_geo_contratista").hide();
    }
  });

  $(".btnRedirect").click(function () {
    var link = $(this).attr("rel");
    location.href = link;
    return false;
  });

  $("input[name=DirigidoAGeneral]").click(function () {
    var Opcion = $(this).val();
    switch (Opcion) {
      case "S": //Socio
        $("#SocioEspecifico").hide();
        $("#SocioGrupo").hide();
        $("#EmpleadoEspecifico").hide();
        $("#EmpleadoGrupo").hide();
        break;
      case "SE": // Socio Especifico
        $("#SocioEspecifico").show();
        $("#SocioGrupo").hide();
        $("#EmpleadoEspecifico").hide();
        $("#EmpleadoGrupo").hide();
        break;
      case "GS": //Grupo Socio
        $("#SocioEspecifico").hide();
        $("#SocioGrupo").show();
        $("#EmpleadoEspecifico").hide();
        $("#EmpleadoGrupo").hide();
        break;
      case "E": //Empleados
        $("#SocioEspecifico").hide();
        $("#SocioGrupo").hide();
        $("#EmpleadoEspecifico").hide();
        $("#EmpleadoGrupo").hide();
        break;
      case "EE": //Empleado Especifico
        $("#SocioEspecifico").hide();
        $("#SocioGrupo").hide();
        $("#EmpleadoEspecifico").show();
        $("#EmpleadoGrupo").hide();
        break;
      case "GE": //Grupo Empleado
        $("#SocioEspecifico").hide();
        $("#SocioGrupo").hide();
        $("#EmpleadoEspecifico").hide();
        $("#EmpleadoGrupo").show();
        break;
    }
  });

  $("#Tipo").change(function () {
    var tipo = $(this).val();

    if (tipo == "Pasadia") {
      $("#mostrarPasadia").show();
    } else if (tipo != "Pasadia") {
      $("#mostrarPasadia").hide();
    }
  });

  $("#personaCheckinSocio").click(function () {
    var personaCheckinSocio = $(this).checked;

    persona = $(this).is(":checked");
    if (persona == true) {
      $("#mostrarSocio").show();
      $("#mostrarUsuario").hide();
    }
  });

  $("#personaCheckinUsuario").click(function () {
    persona = $(this).is(":checked");
    if (persona == true) {
      $("#mostrarSocio").hide();
      $("#mostrarUsuario").show();
    }
  });

  $("#TipoSocio").change(function () {
    var tiposocio = $(this).val();

    $(".contentAuxiliar input").removeClass("mandatory");

    switch (tiposocio) {
      case "Beneficiario":
        $(".contentAuxiliar").addClass("hide");
        $(".contentBeneficiario").removeClass("hide");
        $(".contentBeneficiario input").addClass("mandatory");

        break;
      case "Canje":
        $(".contentAuxiliar").addClass("hide");
        $(".contentCanje").removeClass("hide");
        $(".contentCanje input").addClass("mandatory");

        break;
      case "Cortesia":
        $(".contentAuxiliar").addClass("hide");
        $(".contentCortesia").removeClass("hide");
        $(".contentCortesia input").addClass("mandatory");

        break;
      case "Invitado":
        $(".contentAuxiliar").addClass("hide");
        $(".contentInvitado").removeClass("hide");
        $(".contentInvitado input").addClass("mandatory");

        break;
    } //end switch

    return false;
  });

  $("#IDEstadoInvitado").change(function () {
    var estado = $(this).val();

    switch (estado) {
      case "3":
        $("#divrazonbloqueo").show();
        $("#RazonBloqueo").addClass("mandatory");

        break;
      default:
        $("#divrazonbloqueo").hide();
        $("#RazonBloqueo").removeClass("mandatory");
    } //end switch

    return false;
  });

  $("#AccionClick").change(function () {
    var tipoaccion = $(this).val();

    switch (tipoaccion) {
      case "Url":
        $("#Url").show();
        $("#CuerpoPublicidad").hide();
        break;
      case "WebView":
        $("#Url").hide();
        $("#CuerpoPublicidad").show();

        break;
      case "SinAccion":
        $("#Url").hide();
        $("#CuerpoPublicidad").hide();
        break;
    } //end switch

    return false;
  });

  /*
  //cargar socios en el select
  $("#frmReservaGeneral input#Accion").blur(function(){
    var accion = $(this).val();
    jQuery.ajax( {
      "type" : "POST",
      "data" : { "accion" : accion   },
      "dataType" : "json",
      "url" : "includes/async/get_beneficiarios.async.php" ,

      "success" : function( data ){
          $('#IDSocio')
            .find('option')
            .remove()
            .end()
;
          console.log( data );

          $.each( data.rows, function( index, value ) {

            console.log(value.Socio);
            $('#IDSocio')
              .append('<option value="' +  value.IDSocio + '">' + value.Socio + '</option>')
              .end()

          });



      }
    });

    return false;
  });
  */

  $(".AbrirPuerta").click(function () {
    var id_puerta = $(this).attr("id");

    jQuery.ajax({
      type: "POST",
      data: { IDPuerta: id_puerta },
      dataType: "json",
      url: "includes/async/abrirpuerta.async.php",
      success: function (data) {
        if (data == "ok") {
          n = noty({
            text: "<br><br>Puerta abierta<br><br>",
            type: "success",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
        } else {
          n = noty({
            text: "<br><br>Error de comunicacion con la puerta<br><br>",
            type: "warning",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
        }

        //location.href = "accesoinvitado.php";
        return true;
      },
    });
  });

  $(".brnReservaGeneral").click(function () {
    var elemento = $(this).attr("rel");
    var hora = $(this).attr("rev");
    var tee = $(this).attr("lang");
    var fila = $(this).attr("fila");
    $("#idelemento").val(elemento);
    $("#hora").val(hora);
    $("#tee").val(tee);
    $("#txtmsjreserva" + fila).html("Procesando, por favor espere...");
    $("#frmReservaGeneral").submit();
    return false;
  });

  $(".btnReservaHotel").click(function () {
    var IDHabitacion = $(this).attr("rel");
    var detalle;
    $("#IDHabitacion").val(IDHabitacion);
    $("#txtmsjreserva" + IDHabitacion).html("Procesando, por favor espere...");

    $("#InvitadoSeleccion").val("");
    $("#SocioInvitado option").map(function (i, el) {
      detalle = $("#InvitadoSeleccion").val() + $(el).val() + "|||";
      $("#InvitadoSeleccion").val(detalle);
    });

    $("#frmreservashotel").submit();

    return false;
  });

  $(".btnInscribirCurso").click(function () {
    var IDCursoHorario = $(this).attr("rel");
    var IDCursoCalendario = $(this).attr("calendario");
    var HoraDesde = $(this).attr("horadesde");
    var Consecutivo = $(this).attr("consecutivo");
    var Cupos = $(this).attr("cupos");
    var Valor = $(this).attr("valor");
    var detalle;
    $("#IDCursoHorario").val(IDCursoHorario);
    $("#IDCursoCalendario").val(IDCursoCalendario);
    $("#HoraDesde").val(HoraDesde);
    $("#Cupos").val(Cupos);
    $("#Valor").val(Valor);
    $("#txtmsjreserva" + Consecutivo).html("Procesando, por favor espere...");
    $("#frmInscribirCurso").submit();
    return false;
  });

  $("#agregar_invitado").click(function () {
    var elemento = $("#AccionInvitado").val();
    var id_elemento = "externo-" + $("#AccionInvitado").val();
    if (elemento != "") {
      $("#SocioInvitado").append(
        '<option value="' + id_elemento + '">' + elemento + "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#AccionInvitado").val("");
      alert("Invitado agregado");
    } else {
      alert("Por favor digite o seleccione un invitado");
    }
    return false;
  });

  $("#agregar_invitadoGrupoEmpleado").click(function () {
    var elemento = $("#IDGrupoUsuario").val();
    var id_elemento = "grupo-" + $("#IDGrupoUsuario").val();
    var lista = document.getElementById("IDGrupoUsuario");
    var indice = lista.selectedIndex;
    var grupo = lista.options[indice];
    var texto = grupo.text;
    if (elemento != "") {
      $("#EmpleadoInvitadoGrupo").append(
        '<option value="' + id_elemento + '">' + texto + "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#IDGrupoUsuario").val("");
      alert("Grupo agregado");
    } else {
      alert("Seleccione un grupo");
    }
    return false;
  });

  $("#agregar_empleado").click(function () {
    var elemento = $("#AccionInvitadoUsuario").val();
    var id_elemento = "externo-" + $("#").val();

    if (elemento != "") {
      $("#SocioInvitadoUsuario").append(
        '<option value="' + id_elemento + '">' + elemento + "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#AccionInvitadoUsuario").val("");
      alert("Empleado agregado");
    } else {
      alert("Por favor digite o seleccione un invitado");
    }
    return false;
  });
  
$("#add_empleados").click(function () {
    var elemento = $("#AccionInvitadoUsuario1").val();
    var id_elemento = "externo-" + $("#").val();

    if (elemento != "") {
      $("#SocioInvitadoU").append(
        '<option value="' + id_elemento + '">' + elemento + "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#AccionInvitadoUsuario1").val("");
      alert("Empleado agregado");
    } else {
      alert("Por favor digite o seleccione un invitado");
    }
    return false;
  });
  
  
  $("#agregar_club").click(function () {
    var elemento = $("#IDListaClubes").val();
    var id_elemento = $("#IDListaClubes").val();
    var lista = document.getElementById("IDListaClubes");
    var indice = lista.selectedIndex;
    var grupo = lista.options[indice];
    var texto = grupo.text;
    if (elemento != "") {
      $("#ListaClubesCaddies").append(
        '<option value="' + id_elemento + '">' + texto + "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#IDListaClubes").val("");
      alert("Club Agregado");
    } else {
      alert("Por favor digite o seleccione un Club");
    }
    return false;
  });

  $("#agregar_pais").click(function () {
    var elemento = $("#Paises").val();
    var id_elemento = "externo-" + $("#Paises").val();
    if (elemento != "") {
      $("#PaisesConveniosCanjes").append(
        '<option value="' +
        id_elemento +
        "-" +
        elemento +
        '">' +
        elemento +
        "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#Paises").val("");
      alert("Pais agregado");
    } else {
      alert("Por favor digite o seleccione un Pais");
    }
    return false;
  });

  $("#agregar_ciudad").click(function () {
    var elemento = $("#Ciudades").val();
    var id_elemento = "externo-" + $("#Ciudades").val();
    if (elemento != "") {
      $("#CiudadesConveniosCanjes").append(
        '<option value="' +
        id_elemento +
        "-" +
        elemento +
        '">' +
        elemento +
        "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#Ciudades").val("");
      alert("Ciudad agregada");
    } else {
      alert("Por favor digite o seleccione una Ciudad");
    }
    return false;
  });

  $("#agregar_invitadoGrupo").click(function () {
    var elemento = $("#IDGrupoSocio").val();
    var id_elemento = "grupo-" + $("#IDGrupoSocio").val();
    var lista = document.getElementById("IDGrupoSocio");
    var indice = lista.selectedIndex;
    var grupo = lista.options[indice];
    var texto = grupo.text;
    if (elemento != "") {
      $("#SocioInvitadoGrupo").append(
        '<option value="' + id_elemento + '">' + texto + "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#IDGrupoSocio").val("");
      alert("Grupo agregado");
    } else {
      alert("Seleccione un grupo");
    }
    return false;
  });

  $("#agregar_servicioclub").click(function () {
    var elemento = $("#IDServicio").val();
    var id_elemento = "servicio-" + $("#IDServicio").val();
    var lista = document.getElementById("IDServicio");
    var indice = lista.selectedIndex;
    var grupo = lista.options[indice];
    var texto = grupo.text;

    if (elemento != "") {
      $("#SocioServicio").append(
        '<option value="' + id_elemento + '">' + texto + "</option>"
      );
      $("#IDServicio").val("");
      alert("Servicio agregado");
    } else {
      alert("Seleccione un servicio");
    }
    return false;
  });

  $("#agregar_tiposocio").click(function () {
    var elemento = $("#TipoSocio").val();
    var id_elemento = "tipos-" + $("#TipoSocio").val();
    var lista = document.getElementById("TipoSocio");
    var indice = lista.selectedIndex;
    var grupo = lista.options[indice];
    var texto = grupo.text;
    if (elemento != "") {
      $("#TipoSocioValidarGrupo").append(
        '<option value="' + id_elemento + '">' + texto + "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#TipoSocio").val("");
      alert("Tipo Socio agregado");
    } else {
      alert("Seleccione un grupo");
    }
    return false;
  });

  $("#agregar_categoriaservicio").click(function () {
    var elemento = $("#CategoriaServicio").val();
    var id_elemento = $("#CategoriaServicio").val();
    var lista = document.getElementById("CategoriaServicio");
    var indice = lista.selectedIndex;
    var grupo = lista.options[indice];
    var texto = grupo.text;
    if (elemento != "") {
      $("#CategoriasServicioGrupo").append(
        '<option value="' + id_elemento + '">' + texto + "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#CategoriaServicio").val("");
      alert("Categoria agregada");
    } else {
      alert("Seleccione un grupo");
    }
    return false;
  });

  $("#borrar_invitado").click(function () {
    var index_del = "";
    $("#SocioInvitado option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#SocioInvitado option[value='" + index_del + "']").remove();
    });
    if (index_del == "") {
      alert("Seleccione un invitado");
    }
    return false;
  });

  $("#borrar_invitadoGrupoEmpleado").click(function () {
    var index_del = "";
    $("#EmpleadoInvitadoGrupo option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#EmpleadoInvitadoGrupo option[value='" + index_del + "']").remove();
      alert("Grupo Borrado");
    });
    if (index_del == "") {
      alert("Selecione un grupo a borrar");
    }
    return false;
  });

  $("#borrar_servicio").click(function () {
    var index_del = "";
    $("#SocioServicio option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#SocioServicio option[value='" + index_del + "']").remove();
    });
    if (index_del == "") {
      alert("Seleccione un servicio");
    }
    return false;
  });

  $("#borrar_empleado").click(function () {
    var index_del = "";
    $("#SocioInvitadoUsuario option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#SocioInvitadoUsuario option[value='" + index_del + "']").remove();
    });
    if (index_del == "") {
      alert("Seleccione un empleado");
    }
    return false;
  });
  $("#borrar_empleado1").click(function () {
    var index_del = "";
    $("#SocioInvitadoU option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#SocioInvitadoU option[value='" + index_del + "']").remove();
    });
    if (index_del == "") {
      alert("Seleccione un empleado");
    }
    return false;
  });

  $("#borrar_club").click(function () {
    var index_del = "";
    $("#ListaClubesCaddies option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#ListaClubesCaddies option[value='" + index_del + "']").remove();
    });
    if (index_del == "") {
      alert("Seleccione un club");
    }
    return false;
  });

  $("#borrar_pais").click(function () {
    var index_del = "";
    $("#PaisesConveniosCanjes option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#PaisesConveniosCanjes option[value='" + index_del + "']").remove();
    });
    if (index_del == "") {
      alert("Seleccione un pais");
    }
    return false;
  });

  $("#borrar_ciudad").click(function () {
    var index_del = "";
    $("#CiudadesConveniosCanjes option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#CiudadesConveniosCanjes option[value='" + index_del + "']").remove();
    });
    if (index_del == "") {
      alert("Seleccione una ciudad");
    }
    return false;
  });

  $("#borrar_servicio").click(function () {
    var index_del = "";
    $("#SocioServicio option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#SocioServicio option[value='" + index_del + "']").remove();
    });
    if (index_del == "") {
      alert("Seleccione un servicio");
    }
    return false;
  });

  $("#borrar_invitadoGrupo").click(function () {
    var index_del = "";
    $("#SocioInvitadoGrupo option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#SocioInvitadoGrupo option[value='" + index_del + "']").remove();
      alert("Grupo Borrado");
    });
    if (index_del == "") {
      alert("Selecione un grupo a borrar");
    }
    return false;
  });

  $("#borrar_tiposocio").click(function () {
    var index_del = "";
    $("#TipoSocioValidarGrupo option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#TipoSocioValidarGrupo option[value='" + index_del + "']").remove();
      alert("Tipo Socio Borrado");
    });
    if (index_del == "") {
      alert("Selecione un grupo a borrar");
    }
    return false;
  });

  $("#borrar_categoriaservicio").click(function () {
    var index_del = "";
    $("#CategoriasServicioGrupo option:selected").map(function (i, el) {
      index_del = $(el).val();
      $("#CategoriasServicioGrupo option[value='" + index_del + "']").remove();
      alert("Categoria Borrada");
    });
    if (index_del == "") {
      alert("Selecione un grupo a borrar");
    }
    return false;
  });

  $("#agregar_caddie2").click(function () {
    // var elemento = $("#AccionInvitadoUsuario").val();
    var select = document.getElementById("AccionInvitadoUsuario");
    var elemento = [...select.options].find((option) => option.selected).text;
    var id_elemento = $("#AccionInvitadoUsuario").val();

    if (elemento != "") {
      $("#SocioInvitadoUsuario").append(
        '<option value="' + id_elemento + '">' + elemento + "</option>"
      );
      //$('#SocioInvitado').val($('#SocioInvitado').val()+elemento+"-externo"+"\r");
      $("#AccionInvitadoUsuario").val("");

      alert("Elemento agregado");
    } else {
      alert("Por favor digite o seleccione un elemento");
    }
    return false;
  });

  $(".fancybox").fancybox({
    maxWidth: 800,
    maxHeight: 600,
    fitToView: false,
    width: "80%",
    height: "80%",
    autoSize: false,
    closeClick: false,
    openEffect: "none",
    closeEffect: "none",
    afterClose: function () {
      // USE THIS IT IS YOUR ANSWER THE KEY WORD IS "afterClose"
      $("#grid-table0").trigger("reloadGrid");
      $("#grid-table1").trigger("reloadGrid");
      $("#grid-table2").trigger("reloadGrid");
      $("#grid-table3").trigger("reloadGrid");
      $("#grid-table").trigger("reloadGrid");
      $("#grid-table-voto")
        .setGridParam({ page: 1, datatype: "json" })
        .trigger("reloadGrid");

      $('.divsemana').each(function (index, obj) {
        let style = this.getAttribute("style");

        if (style == "" || style == 'display: block;') {
          let iddiv = this.getAttribute("id");
          let elemento = iddiv.slice(7, -5);
          cargarsemana(elemento);
        }

      });
    },
  });


  $(".fancybox_vehiculo").fancybox({
    maxWidth: 800,
    maxHeight: 600,
    fitToView: false,
    width: "80%",
    height: "90%",
    autoSize: false,
    closeClick: false,
    openEffect: "none",
    closeEffect: "none",
    afterClose: function () {
      // USE THIS IT IS YOUR ANSWER THE KEY WORD IS "afterClose"
      window.location.reload();
    },
  });

  $("#iframe").fancybox({
    type: "iframe",
  });

  $(".noTabLink").click(function (e) {
    e.preventDefault();
    location.href = $(this).attr("href");
  });
}

$(document).ready(function () {
  //Botón de copiar datos de ingreso al resto de grupo familiar
  $(".CopiarGrupoFamiliar").click(function () {
    let placa = $("#PlacaVehiculoTitular").val();

    $(".PlacaVehiculo").val(placa);

    let tipoEntrada = $(":input[name=MecanismoEntradaIngreso]:checked").val();

    if (typeof contadorFamiliar === "undefined") {
      contadorFamiliar = 0;
    }
    for (let i = 0; i < contadorFamiliar; i++) {
      if (tipoEntrada == "Vehiculo") {
        $("#PlacaVehiculo_" + i).show();
      } else {
        $("#PlacaVehiculo_" + i).hide();
      }
    }

    $("." + tipoEntrada).attr("checked", true);
  });
  //Fin Botón de copiar datos de ingreso al resto de grupo familiar

  //Ocultar y mostrar campo placa
  if (
    $("#PlacaVehiculoTitular") == undefined ||
    $("#PlacaVehiculoTitular") == null ||
    $("#PlacaVehiculoTitular") == ""
  ) {
    $("#PlacaVehiculoTitular").hide();
  }

  $("#VehiculoTitular").click(function () {
    $("#PlacaVehiculoTitular").show();
  });

  $("#PeatonalTitular").click(function () {
    $("#PlacaVehiculoTitular").hide();
  });

  if (typeof contadorFamiliar === "undefined") {
    contadorFamiliar = 0;
  }
  for (let i = 0; i < contadorFamiliar; i++) {
    $("#PlacaVehiculo_" + i).hide();
    $("#Vehiculo_" + i).click(function () {
      $("#PlacaVehiculo_" + i).show();
    });

    $("#Peatonal_" + i).click(function () {
      $("#PlacaVehiculo_" + i).hide();
    });
  }
  //Fin Ocultar y mostrar campo placa
});

function ingreso_automatico() {
  var modulo = $("#ModuloAcceso").val();
  var rowid = $("#IdentificadorAcceso").val();
  var mecanismo = "Peatonal";

  switch (modulo) {
    case "SocioInvitado":
      jQuery.ajax({
        type: "POST",
        data: { IDSocioInvitado: rowid, Mecanismo: mecanismo },
        dataType: "json",
        url: "includes/async/ingreso_invitado.async.php",
        success: function (data) {
          //alert("Registro exitoso");
          n = noty({
            text: "<br><br>Registro exitoso<br><br>",
            type: "success",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
          document.frmfrmBuscar.qryString.focus();
          //location.href = "accesoinvitado.php";
          return true;
        },
      });
      break;
    case "SocioAutorizacion":
      jQuery.ajax({
        type: "POST",
        data: { IDSocioAutorizacion: rowid, Mecanismo: mecanismo },
        dataType: "json",
        url: "includes/async/ingreso_autorizacion.async.php",

        success: function (data) {
          //alert("Registro exitoso");
          n = noty({
            text: "<br><br>Registro exitoso<br><br>",
            type: "success",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
          document.frmfrmBuscar.qryString.focus();
          //location.href = "accesoinvitado.php";
          return true;
        },
      });
      break;
    case "SocioInvitadoEspecial":
      jQuery.ajax({
        type: "POST",
        data: { IDSocioInvitadoEspecial: rowid, Mecanismo: mecanismo },
        dataType: "json",
        url: "includes/async/ingreso_invitadoespecial.async.php",

        success: function (data) {
          //alert("Registro exitoso");
          n = noty({
            text: "<br><br>Registro exitoso<br><br>",
            type: "success",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
          document.frmfrmBuscar.qryString.focus();
          //location.href = "accesoinvitado.php";
          return true;
        },
      });
      break;
    case "Socio":
      jQuery.ajax({
        type: "POST",
        data: { IDSocio: rowid, Mecanismo: mecanismo },
        dataType: "json",
        url: "includes/async/ingreso_socio.async.php",

        success: function (data) {
          //alert("Registro exitoso");
          n = noty({
            text: "<br><br>Registro exitoso<br><br>",
            type: "success",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
          document.frmfrmBuscar.qryString.focus();
          //location.href = "accesoinvitado.php";
          return true;
        },
      });
      break;
    case "Usuario":
      jQuery.ajax({
        type: "POST",
        data: { IDUsuario: rowid, Mecanismo: mecanismo },
        dataType: "json",
        url: "includes/async/ingreso_usuario.async.php",

        success: function (data) {
          //alert("Registro exitoso");
          n = noty({
            text: "<br><br>Registro exitoso<br><br>",
            type: "success",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
          document.frmfrmBuscar.qryString.focus();
          //location.href = "accesoinvitado.php";
          return true;
        },
      });
      break;
  } //Fin switch
}

function salida_automatico() {
  var modulo = $("#ModuloAcceso").val();
  var rowid = $("#IdentificadorAcceso").val();
  var mecanismo = "Peatonal";

  switch (modulo) {
    case "SocioAutorizacion":
      jQuery.ajax({
        type: "POST",
        data: {
          IDSocioAutorizacion: rowid,
          Tipo: "Salida",
          Mecanismo: mecanismo,
        },
        dataType: "json",
        url: "includes/async/ingreso_autorizacion.async.php",

        success: function (data) {
          //alert("Salida registrada con exito");
          n = noty({
            text: "<br><br>Salida registrada<br><br>",
            type: "warning",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
          document.frmfrmBuscar.qryString.focus();
          //location.href = "accesoinvitado.php";
          return false;
        },
      });
      break;
    case "SocioInvitadoEspecial":
      jQuery.ajax({
        type: "POST",
        data: {
          IDSocioInvitadoEspecial: rowid,
          Tipo: "Salida",
          Mecanismo: mecanismo,
        },
        dataType: "json",
        url: "includes/async/ingreso_invitadoespecial.async.php",

        success: function (data) {
          //alert("Salida registrada con exito");
          n = noty({
            text: "<br><br>Salida registrada<br><br>",
            type: "warning",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
          document.frmfrmBuscar.qryString.focus();
          //location.href = "accesoinvitado.php";
          return true;
        },
      });
      break;
    case "Socio":
      jQuery.ajax({
        type: "POST",
        data: { IDSocio: rowid, Tipo: "Salida", Mecanismo: mecanismo },
        dataType: "json",
        url: "includes/async/ingreso_socio.async.php",

        success: function (data) {
          //alert("Salida registrada con exito");
          n = noty({
            text: "<br><br>Salida registrada<br><br>",
            type: "warning",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
          document.frmfrmBuscar.qryString.focus();
          //location.href = "accesoinvitado.php";
          return true;
        },
      });
      break;

    case "Usuario":
      jQuery.ajax({
        type: "POST",
        data: { IDUsuario: rowid, Tipo: "Salida", Mecanismo: mecanismo },
        dataType: "json",
        url: "includes/async/ingreso_usuario.async.php",

        success: function (data) {
          //alert("Salida registrada con exito");
          n = noty({
            text: "<br><br>Salida registrada<br><br>",
            type: "warning",
            dismissQueue: true,
            layout: "topCenter",
            theme: "defaultTheme",
            modal: true,
            timeout: 1500,
            closeWith: ["button"],
            buttons: false,
            animation: {
              open: { height: "toggle" },
              close: { height: "toggle" },
              easing: "swing",
              speed: 500, // opening & closing animation speed
            },
          });
          document.frmfrmBuscar.qryString.focus();
          //location.href = "accesoinvitado.php";
          return true;
        },
      });
      break;
  }
}

function get_datos_persona(valor, contador) {
  var res = valor.split("	");
  var Documento = res[0];
  var Nombre = res[3] + " " + res[4];
  var Apellido = res[1] + " " + res[2];
  var FechaNacimiento = res[6];
  if (FechaNacimiento != "") {
    FechaNacimiento =
      FechaNacimiento.substr(0, 4) +
      "-" +
      FechaNacimiento.substr(4, 2) +
      "-" +
      FechaNacimiento.substr(6, 2);
  }
  var TipoSangre = res[7];
  $("#NumeroDocumento" + contador).val(Documento);
  $("#Nombre" + contador).val(Nombre);
  $("#Apellido" + contador).val(Apellido);
  $("#FechaNacimiento" + contador).val(FechaNacimiento);
  $("#TipoSangre" + contador).val(TipoSangre);

  //Si es una busqueda en invitados
  if (contador == 100) {
    jQuery.ajax({
      type: "POST",
      data: {
        numerodocumento: Documento,
        primerapellido: Apellido,
        primernombre: Nombre,
      },
      dataType: "json",
      url: "includes/async/invitadosesp_ingreso_pistola.async.php",
      success: function (data) {
        console.log(data);
        $("#contentMsgP").html(data.msg);
      },
    });
    $("#grid-table").trigger("reloadGrid");
    $("#grid-tableingresado").trigger("reloadGrid");
  }

  //Si es una busqueda en autorizaciones
  if (contador == 150) {
    jQuery.ajax({
      type: "POST",
      data: {
        numerodocumento: Documento,
        primerapellido: Apellido,
        primernombre: Nombre,
      },
      dataType: "json",
      url: "includes/async/invitadosaut_ingreso_pistola.async.php",
      success: function (data) {
        console.log(data);
        $("#contentMsgP").html(data.msg);
      },
    });
    $("#grid-table").trigger("reloadGrid");
    $("#grid-tableingresado").trigger("reloadGrid");
  }

  return false;
} //end function

function makeid() {
  var text = "";
  var possible =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789#!%*&";

  for (var i = 0; i < 10; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));

  return text;
}

function load_pagina(page) {
  $.ajax({
    type: "GET",
    url: page,
    success: function (data) {
      try {
        $("#divresumenpolo").html(data);
      } catch (err) {
        alert(err);
      }
    },
  });
}
