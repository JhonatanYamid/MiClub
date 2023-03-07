<?php
require("../admin/config.inc.php");
SIMUtil::cache();

$_POST = SIMUtil::makeSafe($_POST);



//handler de sesion
$simsession_socio = new SIMSession(SESSION_LIMIT);


if (isset($_POST["action"]))
    $action = $_POST["action"];
else
    $action = $_GET["action"];



if ($_GET["IDClub"] == 28 || $_GET["IDClub"] == 249 ||  $_POST["IDClub"] == 28 ||  $_POST["IDClub"] == 249) {
    $link_index = "indexcurso.php";
} else {
    $link_index = "index.php";
}
require_once LIBDIR . "SIMWebServiceUsuarios.inc.php";
switch ($action) {

    case 'Iniciar':


        $login = SIMUtil::antiinjection($_POST["Usuario"]);
        $clave = SIMUtil::antiinjection($_POST["Password"]);

        $respuesta = SIMWebServiceUsuarios::valida_socio($login, $clave, $_POST["IDClub"], "24");

        $IDSocio = $respuesta["response"]["IDSocio"];

        $dbo = &SIMDB::get();

        $cliente_data = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'", "object");
        // var_dump($respuesta);
        // die();




        $simsession_socio->clean_cliente();

        if ($cliente_data) {
            $usuariosave = addslashes(serialize($cliente_data));
            if ($simsession_socio->crear_cliente($cliente_data->IDSocio, $usuariosave)) {        //si el usuario es club crea la sesion del club
                $_SESSION["club"] = $cliente_data->IDClub;
                // if ($_POST["IDClub"] == 8 || $_GET["IDClub"] == 8 || $_GET["IDClub"] == 28 ||  $_POST["IDClub"] == 28) {
                if ($_GET["IDClub"] == 28 ||  $_POST["IDClub"] == 249) {
                    header("location: cursoinscripcion.php");
                } elseif ($_POST["IDClub"] == 15) {
                    header("location: seccionpereira.php");
                } else {
                    header("location: seccion.php");
                }
            } else {
                header("location: " . $link_index . "?msg=noexiste&IDClub=" . $_POST["IDClub"]);
            }
        } else {
            header("location: " . $link_index . "?msg=noexiste&IDClub=" . $_POST["IDClub"]);
        }

        break;

    case 'Salir':
        $simsession_socio->eliminar_cliente();
        header("location: " . $link_index . "?msg=EX&IDClub=" . $_GET["IDClub"]);
        break;
    default:
        header("location: " . $link_index . "?msg=LI&IDClub=" . $_POST["IDClub"]);
        break;
}
