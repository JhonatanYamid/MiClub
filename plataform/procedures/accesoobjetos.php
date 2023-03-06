
<?php

SIMReg::setFromStructure(array(
    "title" => "AccesoObjetos",
    "table" => "accesoobjeto",
    "key" => "IDAccesoObjeto",
    "mod" => "accesoobjetos"
));


//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

$action = SIMNet::req("action");

$IDSocio = SIMNet::req("IDSocio");
$IDInvitado = SIMNet::req("IDInvitado");
$IDUsuario = SIMNet::req("IDUsuario");
$script = 'accesoobjetos';
switch ($action) {

    case "insertar":
        $frm = SIMUtil::varsLOG($_POST);
        //Verifico que no exista la placa

        $objeto = $dbo->getFields("AccesoObjeto", "Campo1", "Campo1 = '" . $frm["Campo1"] . "' and IDSocio = '" . $frm["IDSocio"] . "' and IDInvitado = " . $frm["IDInvitado"]);
        if (empty($pbjeto)) :
            $id = $dbo->insert($frm, "AccesoObjeto", "IDAccesoObjeto");
            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
        else :
            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Elobjetoyaexisteporfavorverifique', LANGSESSION));
        endif;
        SIMHTML::jsRedirect("accesoobjetos.php?IDUsuario=$IDUsuario&IDSocio=$IDSocio&IDInvitado=$IDInvitado");
        exit;
        break;

    case "actualizar":


        $frm = SIMUtil::varsLOG($_POST);
        /* , $_GET["IDInvitado"] */
        $IDInvitado = $_GET["IDInvitado"];
        $IDSocio = $_GET["IDSocio"];
        $frm['IDInvitado'] = $IDInvitado;
        $id = $dbo->update($frm, "AccesoObjeto",  "IDAccesoObjeto",  SIMNet::reqInt("id"));
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect("accesoobjetos.php?IDUsuario=$IDUsuario&IDSocio=$IDSocio&IDInvitado=$IDInvitado");
        exit;
        break;

    case "edit":
        $id = $frm["IDAccesoObjetos"];
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);



            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroActualizadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect("accesoobjetos.php");
            /* 	if (isset($_POST['Estudio'])) { 
						  
							SIMHTML::jsRedirect("primaderaestudio.php?IDSocio=" . $frm["IDSocio"]);
						 } */



            //SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;

        break;

    case "Eliminar":
        $id = $dbo->query("DELETE FROM AccesoObjeto WHERE IDAccesoObjeto = '" . $_GET["IDAccesoObjeto"] . "' LIMIT 1");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitosa', LANGSESSION));
        SIMHTML::jsRedirect("accesoobjetos.php?IDUsuario=$IDUsuario&IDSocio=$IDSocio&IDInvitado=$IDInvitado");
        exit;
        break;
}

$view = "views/accesoobjetos/form.php"
?>