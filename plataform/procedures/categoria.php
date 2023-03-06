<?php
SIMReg::setFromStructure(array(
    "title" => "Categoria",
    "table" => "Categoria",
    "key" => "IDCategoria",
    "mod" => "HistorialSocios"
));


$script = "categoria";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

// Funcion para obteber los datos de Categoria
function get_Categoria($IDCategoria, $dbo)
{
    $SqlCategoria = "SELECT * FROM Categoria WHERE Categoria.IDCategoria = $IDCategoria";
    $resultCategoria = $dbo->query($SqlCategoria);
    $RowCategoria = $dbo->assoc($resultCategoria);
    return $RowCategoria;
}

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
            $frm['TipoSocio'] = implode('|', $frm['TipoSocio']);
            $frm['TipoSocio'] = ($frm['TipoSocio'] != '') ? '|' . $frm['TipoSocio'] . '|' : '';
            $frm['IDParentesco'] = implode('|', $frm['IDParentesco']);
            $frm['IDParentesco'] = ($frm['IDParentesco'] != '') ? '|' . $frm['IDParentesco'] . '|' : '';
            $frm['EstadoCivil'] = implode('|', $frm['EstadoCivil']);
            $frm['EstadoCivil'] = ($frm['EstadoCivil'] != '') ? '|' . $frm['EstadoCivil'] . '|' : '';


            if ($frm['CampoValidacion'] == 'Rango') {
                $Edad = explode('-', $frm['Edad']);
                $min = $Edad[0];
                $max = $Edad[1];
                $Edades = "|";
                for ($i = $min; $i <= $max; $i++) {
                    $Edades .= $i . "|";
                }
                $frm['Edad'] = $Edades;
            } else {
                $frm['Edad'] = '|' . $frm['Edad'] . '|';
            }

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);

            $insert_ClubCategoria = "INSERT INTO ClubCategoria (IDClub,IDCategoria) VALUES ('" . SIMUser::get('club') . "','" . $id . "')";
            $dbo->query($insert_ClubCategoria);

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        break;


    case "edit":
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":
        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
            $frm['TipoSocio'] = implode('|', $frm['TipoSocio']);
            $frm['TipoSocio'] = ($frm['TipoSocio'] != '') ? '|' . $frm['TipoSocio'] . '|' : '';
            $frm['IDParentesco'] = implode('|', $frm['IDParentesco']);
            $frm['IDParentesco'] = ($frm['IDParentesco'] != '') ? '|' . $frm['IDParentesco'] . '|' : '';
            $frm['EstadoCivil'] = implode('|', $frm['EstadoCivil']);
            $frm['EstadoCivil'] = ($frm['EstadoCivil'] != '') ? '|' . $frm['EstadoCivil'] . '|' : '';

            if ($frm['CampoValidacion'] == 'Rango') {
                $Edad = explode('-', $frm['Edad']);
                $cont = count($Edad);
                if ($cont < 2) {
                    SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Rangonovalido', LANGSESSION));
                    SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
                    exit;
                } else {
                    $min = $Edad[0];
                    $max = $Edad[1];
                    $Edades = "|";
                    for ($i = $min; $i <= $max; $i++) {
                        $Edades .= $i . "|";
                    }
                    $frm['Edad'] = $Edades;
                }
            } elseif ($frm['CampoValidacion'] == '>=') {
                $max = 100;
                $min = $frm['Edad'] + 1;
                $Edades = "|";
                for ($i = $min++; $i < $max; $i++) {
                    $Edades .= $i . "|";
                }
                $frm['Edad'] = $Edades;
            } elseif ($frm['CampoValidacion'] == '<=') {
                $max = $frm['Edad'];
                $min = 1;
                $Edades = "|";
                for ($i = $min; $i < $max; $i++) {
                    $Edades .= $i . "|";
                }
                $frm['Edad'] = $Edades;
            } elseif ($frm['CampoValidacion'] == '!=') {
                $max = 100;
                $min = 1;
                $Edades = "|";
                for ($i = $min; $i <= $max; $i++) {
                    if ($i != $frm['Edad']) {
                        $Edades .= $i . "|";
                    }
                }
                $frm['Edad'] = $Edades;
            } elseif ($frm['CampoValidacion'] == '==') {
                $frm['Edad'] = '|' . $frm['Edad'] . '|';
            }
            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;

        break;
    case "delete":
        die('hola');
        break;
    case "search":
        $view = "views/" . $script . "/list.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
