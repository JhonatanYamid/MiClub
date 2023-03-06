<?php
include "../../procedures/general_async.php";

$param = $_POST['param'];
$paramAreas = $_POST['paramAreas'];
$paramMes = $_POST['paramMes'];
$paramlineal = $_POST['paramlineal'];
$fechainiciolineal = $_POST['iniciolineal'];
$fechafinlineal = $_POST['finallineal'];
$fechainiciobarrasestado = $_POST['iniciobarraestado'];
$fechafinbarrasestado = $_POST['finalbarrasestado'];
$fechainiciobarrasmes = $_POST['iniciopqrmes'];
$fechafinbarrasmes = $_POST['finpqrmes'];
$fechainiciobarrasareas = $_POST['fechainicialareas'];
$fechafinbarrasareas = $_POST['fechafinpqrareas'];

$wherebarrasestado =  " and DATE(pqr.FechaTrCr) >= '$fechainiciobarrasestado' and DATE(pqr.FechaTrCr) <= '$fechafinbarrasestado'";

$whereLineal = " and DATE(FechaTrCr) >= '$fechainiciolineal' and DATE(FechaTrCr) <= '$fechafinlineal'";

$wherebarrasmes = " and DATE(FechaTrCr) >= '$fechainiciobarrasmes' and DATE(FechaTrCr) <= '$fechafinbarrasmes'";

$whereAreas = " and DATE(pqr.FechaTrCr) >= '$fechainiciobarrasareas' and DATE(pqr.FechaTrCr) <= '$fechafinbarrasareas'";

if ($paramlineal == "graficolineal") {
    function TraerDatosGraficoLineal()
    {
        global $whereLineal, $fechafinlineal, $fechainiciolineal;
        $dbo = &SIMDB::get();

        if (!empty($fechainiciolineal) && !empty($fechafinlineal)) {

            $sql3 = "
        SELECT WEEK(FechaTrCr) semana, COUNT(FechaTrCr) as total_fechas FROM Pqr
         WHERE FechaTrCr > DATE_SUB(NOW(), INTERVAL 30 DAY) and IDClub=' " . SIMUser::get("club") . "'
         and IDPqrEstado <> 3" . $whereLineal .  " GROUP BY Semana ORDER BY Semana ASC";

            // echo $sql3;
        } else  if (empty($fechainiciolineal) && empty($fechafinlineal)) {


            $sql3 = "
        SELECT WEEK(FechaTrCr) semana, COUNT(FechaTrCr) as total_fechas FROM Pqr
         WHERE FechaTrCr > DATE_SUB(NOW(), INTERVAL 30 DAY) and IDClub=' " . SIMUser::get("club") . "'
         and IDPqrEstado <> 3 GROUP BY Semana ORDER BY Semana ASC";
        }
        $arreglo3 = array();
        $datos3 = $dbo->query($sql3);
        while ($Datos3 = $dbo->fetchArray($datos3)) {
            $arreglo3[] = $Datos3;
        }
        return $arreglo3;
    }

    $consulta3 = TraerDatosGraficoLineal();

    echo json_encode($consulta3);
}

if ($param == "barras") {
    function TraerDatosGraficoBar()
    {
        global $fechainiciobarrasestado, $fechafinbarrasestado, $wherebarrasestado;

        $dbo = &SIMDB::get();
        if (!empty($fechainiciobarrasestado) && !empty($fechafinbarrasestado)) {

            $sql = "SELECT pqre.Descripcion, COUNT(pqre.Descripcion) as Descripcion1
            FROM Pqr as pqr inner join PqrEstado as pqre 
            on pqr.IDPqrEstado = pqre.IDPqrEstado
             WHERE IDClub='" . SIMUser::get("club") . "'" . $wherebarrasestado . "GROUP BY pqre.Descripcion";
            //echo $sql;
        } else if (empty($fechainiciobarrasestado) && empty($fechafinbarrasestado)) {

            $sql = "SELECT pqre.Descripcion, COUNT(pqre.Descripcion) as Descripcion1
            FROM Pqr as pqr inner join PqrEstado as pqre 
            on pqr.IDPqrEstado = pqre.IDPqrEstado WHERE IDClub='" . SIMUser::get("club") . "' GROUP BY pqre.Descripcion";
        }



        $arreglo = array();
        $datos = $dbo->query($sql);
        while ($Datos = $dbo->fetchArray($datos)) {
            $arreglo[] = $Datos;
        }
        return $arreglo;
    }

    $consulta = TraerDatosGraficoBar();

    echo json_encode($consulta);
}
if ($paramMes == "barrasmes") {
    function TraerDatosGraficoBarMes()
    {
        global $wherebarrasmes, $fechainiciobarrasmes, $fechafinbarrasmes;
        $dbo = &SIMDB::get();
        if (!empty($fechainiciobarrasmes) && !empty($fechafinbarrasmes)) {
            $sql2 = "
            SELECT COUNT(FechaTrCr),CONCAT(MONTHNAME(FechaTrCr), ' ',YEAR(FechaTrCr) ) AS FECHA 
            FROM Pqr WHERE IDClub='" . SIMUser::get("club") . "'" . $wherebarrasmes . "GROUP BY MONTH(FechaTrCr)
             order BY FechaTrCr ASC";
        } else if (empty($fechainiciobarrasmes) && empty($fechafinbarrasmes)) {

            $sql2 = "
        SELECT COUNT(FechaTrCr),CONCAT(MONTHNAME(FechaTrCr), ' ',YEAR(FechaTrCr) ) AS FECHA 
        FROM Pqr WHERE IDClub='" . SIMUser::get("club") . "' GROUP BY MONTH(FechaTrCr)
         order BY FechaTrCr ASC";
        }
        $arreglo2 = array();
        $datos2 = $dbo->query($sql2);
        while ($Datos2 = $dbo->fetchArray($datos2)) {
            $arreglo2[] = $Datos2;
        }
        return $arreglo2;
    }

    $consulta2 = TraerDatosGraficoBarMes();

    echo json_encode($consulta2);
}

if ($paramAreas == "barrasareas") {
    function TraerDatosGraficoBarAreas()
    {
        global $whereAreas, $fechainiciobarrasareas, $fechafinbarrasareas;
        $dbo = &SIMDB::get();
        if (!empty($fechainiciobarrasareas) && !empty($fechafinbarrasareas)) {

            $sql1 = "SELECT a.Nombre, COUNT(pqr.IDArea) as conteoArea
            FROM Pqr as pqr inner join Area as a on pqr.IDArea = a.IDArea
             WHERE pqr.IDClub='" . SIMUser::get("club") . "'" . $whereAreas . " AND Activo='S' GROUP BY pqr.IDArea";
        } else if (empty($fechainiciobarrasareas) && empty($fechafinbarrasareas)) {

            $sql1 = "SELECT a.Nombre, COUNT(pqr.IDArea) as conteoArea
             FROM Pqr as pqr inner join Area as a on pqr.IDArea = a.IDArea
              WHERE pqr.IDClub='" . SIMUser::get("club") . "' AND Activo='S' GROUP BY pqr.IDArea";
        }


        $arreglo1 = array();
        $datos1 = $dbo->query($sql1);
        while ($Datos1 = $dbo->fetchArray($datos1)) {
            $arreglo1[] = $Datos1;
        }
        return $arreglo1;
    }

    $consulta1 = TraerDatosGraficoBarAreas();

    echo json_encode($consulta1);
}
