#!/usr/bin/php -q
<?php

// include("/home/http/miempresapp/app/admin/config.inc.php");


$sql_socios = "SELECT IDSocio,NumeroDocumento,IDClub FROM Socio WHERE IDClub in (95,96,97,98,122) ";
$r_socios = $dbo->query($sql_socios);
while ($row_socios = $dbo->fetchArray($r_socios)) {
    $array_socios[$row_socios["NumeroDocumento"]] = $row_socios;
}

//$sql_inactivar="UPDATE Socio SET IDEstadoSocio = 2 WHERE IDClub in (95,96,97,98,122)";
$sql_inactivar = "UPDATE Socio SET IDEstadoSocio = 2 WHERE IDClub in (95,96,97)";
$dbo->query($sql_inactivar);


$array_empresa["SOLUCIONES"] = 97;
$array_empresa["CHOCOLATE"] = 96;
$array_empresa["COLOMBIA"] = 95;
$array_empresa["AGRICOLA"] = "";
$array_empresa["FUNDACION"] = "122";
$array_empresa["NECOCLI"] = "";
$array_empresa["HUILA"] = "";
//teceros?



$tns = "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = " . HOST_LUKER . ")(PORT = " . PORT_LUKER . ")))
	(CONNECT_DATA = (SERVICE_NAME = " . BASE_LUKER . ")))";
try {
    $conn = new PDO("oci:dbname=" . $tns, USER_LUKER, PASSWORD_LUKER);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}

$sql = "SELECT * FROM EMPLEADOS_APPS";

$stmt = $conn->prepare($sql);
$stmt->execute();
while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($datos);
    die();
    echo $Cedula = $datos["CEDULA"];
    echo "<br><br>";
    $Codigo = $datos["CODIGO"];
    $Nombre = $datos["NOMBRE"];
    $Apellido = $datos["APELLIDO1"] . " " . $datos["APELLIDO2"];
    $Sexo = $datos["SEXO"];
    $Cargo = $datos["CARGO"];
    $CodigoGerencia = $datos["CODIG_GERENCIA"];
    $Negocio = $datos["NEGOCIO"];
    $Area = $datos["AREA"];
    $Division = $datos["DIVISION"];
    $Ciudad = $datos["CIUDAD"];
    $Departamento = $datos["DEPARTAMENTO"];
    $Agencia = $datos["AGENCIA"];
    $Correo = $datos["CORREO"];
    $FechaIngreso = $datos["FECHA_INGRESO"];
    $FechaNacimiento = $datos["FECHA_NACIMIENTO"];
    $Direccion = $datos["DIRECCION"];
    $Telefono = $datos["TELEFONO"];
    $Eps = $datos["EPS"];
    $Estado = $datos["ESTADO"];
    $TipoContrato = $datos["TIPO_CONTRATO"];
    $NombreJefe = $datos["NOMBRE_JEFE"];
    $CorreoJefe = $datos["CORREO_JEFE"];
    $DocumentoJefe = $datos["CEDULA_JEFE"];
    $IDSocio = $array_socios[$Cedula]["IDSocio"];

    $IDEstado = 1;

    $IDClub = $array_empresa[$Negocio];

    if (!empty($IDClub)) {
        if (empty($IDSocio)) {
            $sql_inserta = "INSERT INTO Socio (IDClub,IDEstadoSocio,NumeroDocumento,Accion,Nombre,Apellido,CorreoElectronico,TipoSocio,Empresa,Cargo,Area,Division,Departamento,Agencia,TipoContrato, NombreJefe, CodigoEmpleado, Email, Clave, SolicitaEditarPerfil, UsuarioTrCr, FechaTrCr,CorreoJefe,DocumentoJefe)
											VALUES ('$IDClub','$IDEstado','$Cedula','$Cedula','$Nombre','$Apellido','$Correo','$TipoContrato','$Negocio','$Cargo','$Area','$Division','$Departamento',
															'$Agencia','$TipoContrato','$NombreJefe','$Codigo','$Cedula',sha1('$Cedula'),'S','Cron',NOW(),'$CorreoJefe','$DocumentoJefe')";
            $dbo->query($sql_inserta);
            // echo "<br>" . $sql_inserta;
            $insertados++;
        } else {

            //Verifico si cambia la empresa en ese caso se debe forzar al usuario que cierre sesion
            if ($array_socios[$Cedula]["IDClub"] != $IDClub) {
                $cierre = ", SolicitarCierreSesion = 'S' ";
            } else {
                $cierre = "";
            }

            $sql_actualiza = "UPDATE Socio
								SET IDClub = '" . $IDClub . "', IDEstadoSocio = '" . $IDEstado . "',  Accion = '" . $Cedula . "', Nombre = '" . $Nombre . "', Apellido = '$Apellido',
								CorreoElectronico='" . $Correo . "', TipoSocio='" . $TipoContrato . "',Empresa='" . $Negocio . "',Cargo='" . $Cargo . "',Area='" . $Area . "',Division='" . $Division . "',Departamento='" . $Departamento . "',
								Agencia='" . $Agencia . "',TipoContrato='" . $TipoContrato . "',NombreJefe='" . $NombreJefe . "',CodigoEmpleado='" . $Codigo . "',
								UsuarioTrEd='CRON',FechaTrEd=NOW(),CorreoJefe='$CorreoJefe',DocumentoJefe='$DocumentoJefe' " . $cierre . "
								WHERE IDSocio = '" . $IDSocio . "';";
            //echo "ESTADO " . $Cedula . " : " . $datos["ESTADO"];
            $dbo->query($sql_actualiza);
            // echo "<br>" . $sql_actualiza;
            $actualizados++;
        }
    } else {
        echo "<br>Documento sin empresa: " . $Cedula;
    }
}

echo "<br>Actualizados:" . $actualizados;
echo "<br>Insertados:" . $insertados;
echo "<br>fin";
exit;





?>