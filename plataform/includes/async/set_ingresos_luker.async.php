<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
$conn = SIMUtil::ConexionBDLuker();

$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

$frm_datosSocio  = $dbo->fetchAll("Socio", "IDSocio = " . $frm['Socio'], "array");

$frm_solicitud = $dbo->fetchAll($frm['Tabla'], "IDIngresosSolicitud=" . $frm['ID'], "array");

// Funcion para pasar los valores del array a Mayusculas
function UpCaseArray($frm)
{
    foreach ($frm as $i => $value) {
        $value = SIMHTML::limpiar_acentos($value);
        $frm[$i] = strtoupper($value);
    }
    return $frm;
}


// Funcion para pasar los valores del array a Mayusculas
$sql_DatosPersonales = "SELECT * FROM IngresosDatosPersonales WHERE IDIngresosSolicitud = " . $frm['ID'] . " ORDER BY IDIngresosDatosPersonales DESC LIMIT 1";
$q_DatosPersonales = $dbo->query($sql_DatosPersonales);
$frm_DatosPersonales = $dbo->assoc($q_DatosPersonales);
$frm_DatosPersonales = UpCaseArray($frm_DatosPersonales);



$sql_IngresosExperiencias = "SELECT * FROM IngresosExperiencias WHERE IDIngresosSolicitud = " . $frm['ID'] . " AND Empresa != '' ORDER BY IDIngresosExperiencias ASC";
$q_IngresosExperiencias = $dbo->query($sql_IngresosExperiencias);


$sql_IngresosBeneficiarios = "SELECT * FROM IngresosBeneficiarios WHERE IDIngresosSolicitud = " . $frm['ID'] . " AND NombreCompleto != '' ORDER BY IDIngresosBeneficiarios ASC";
$q_IngresosBeneficiarios = $dbo->query($sql_IngresosBeneficiarios);

$sql_IngresosEstudios = "SELECT * FROM IngresosEstudios WHERE IDIngresosSolicitud = " . $frm['ID'] . " ORDER BY IDIngresosEstudios ASC";
$q_IngresosEstudios = $dbo->query($sql_IngresosEstudios);

$sql_IngresosDeportes = "SELECT * FROM IngresosDeportes WHERE IDIngresosSolicitud = " . $frm['ID'] . " ORDER BY IDIngresosDeportes ASC";
$q_IngresosDeportes = $dbo->query($sql_IngresosDeportes);

$sql_IngresosIdiomas = "SELECT * FROM IngresosIdiomas WHERE IDIngresosSolicitud = " . $frm['ID'] . " AND Idioma != 0 ORDER BY IDIngresosIdiomas ASC";
$q_IngresosIdiomas = $dbo->query($sql_IngresosIdiomas);


$DepartamentoResidenciaID = explode("|", $frm_DatosPersonales['DepartamentoResidencia']);
$frm_DatosPersonales['DepartamentoResidencia'] = $DepartamentoResidenciaID[0];

if ($frm_DatosPersonales['MunicipioResidencia'] != '') {
    $MunicipioResidenciaID = explode("|", $frm_DatosPersonales['MunicipioResidencia']);
    $frm_DatosPersonales['MunicipioResidencia'] = $MunicipioResidenciaID[0];
}

$DepartamentoExpedicionID = explode("|", $frm_DatosPersonales['DeptoExpedicion']);
$frm_DatosPersonales['DeptoExpedicion'] = $DepartamentoExpedicionID[0];

if ($frm_DatosPersonales['MunicipioExpedicion'] != '') {
    $MunicipioExpedicionID = explode("|", $frm_DatosPersonales['MunicipioExpedicion']);
    $frm_DatosPersonales['MunicipioExpedicion'] = $MunicipioExpedicionID[0];
}

$DepartamentoNacimientoID = explode("|", $frm_DatosPersonales['DeptoNacimiento']);
$frm_DatosPersonales['DeptoNacimiento'] = $DepartamentoNacimientoID[0];

if ($frm_DatosPersonales['MunicipioNacimiento'] != '') {
    $MunicipioNacimientoID = explode("|", $frm_DatosPersonales['MunicipioNacimiento']);
    $frm_DatosPersonales['MunicipioNacimiento'] = $MunicipioNacimientoID[0];
}

$FechaMatricula = ($frm_DatosPersonales['FechaMatricula'] != '') ? "to_date('" . $frm_DatosPersonales['FechaMatricula'] . "','YYYY-MM-DD')" : "''";

$insert_datosPersonales = "INSERT INTO TLK_EMPLEADO_NV (IDLUKEREMPLEADO,EMP_CEDULA,EMP_TIPO_IDENTIF,UGN1_CODIGO_IDENT,UGN2_CODIGO_IDENT,UGN3_CODIGO_IDENT,BENEF_CAMPO_ALF4,EMP_NOMBRE,EMP_APELLIDO1,EMP_APELLIDO2,EMP_FECHA_NACI,UGN1_CODIGO_NACI,UGN2_CODIGO_NACI,UGN3_CODIGO_NACI,EMP_TIPO_SANGRE,EMP_SANGRE_RH,EMP_ESTADO_CIVIL,TIEM_CAMPO_ALF1,EMP_PROFESION,EMP_MATRICULA_PROFESIONAL,EMP_FECHA_MATRICULA,UGN1_CODIGO_RESID,UGN2_CODIGO_RESID,UGN3_CODIGO_RESID,EMP_DIRECCION,EMP_TELEFONO,EMP_BARRIO,EMP_VIVIENDA,EMP_PERSONA_ACUDIENTE,EMP_TELEFONO_ACUDIENTE,EMP_DIRECCION_ACUDIENTE,TIEM_CAMPO_ALF2,TIEM_CAMPO_ALF3,TIEM_CAMPO_ALF4,BENEF_CAMPO_NUM3,BENEF_CAMPO_IND1,BENEF_CAMPO_IND2,BENEF_CAMPO_IND3,BENEF_CAMPO_NUM1,BENEF_CAMPO_IND5,BENEF_CAMPO_IND6,BENEF_CAMPO_ALF1,BENEF_CAMPO_ALF2,BENEF_CAMPO_ALF3,BENEF_CAMPO_ALF5,BENEF_CAMPO_NUM2,BENEF_CAMPO_NUM4,BENEF_CAMPO_NUM5,EPS,AFP,FCES,EMP_PERSONA_VIV1,EMP_TELEFONO_VIV1,EMP_PARENTESCO_VIV1,EMP_PERSONA_VIV2,EMP_TELEFONO_VIV2,EMP_PARENTESCO_VIV2,EMP_PERSONA_VIV3,EMP_TELEFONO_VIV3,EMP_PARENTESCO_VIV3,EMP_PERSONA_VIV4,EMP_TELEFONO_VIV4,EMP_PARENTESCO_VIV4,EMP_PERSONA_VIV5,EMP_TELEFONO_VIV5,EMP_PARENTESCO_VIV5,CIUDAD_NAC_EXT,CIUDAD_DOC_EXT,EMP_SEXO) VALUES (
'" . $frm_DatosPersonales['IDIngresosDatosPersonales'] . "',
'" . $frm_datosSocio['NumeroDocumento'] . "',
'" . $frm_DatosPersonales['TipoIdentificacion'] . "',
'" . $frm_DatosPersonales['PaisExpedicion'] . "',
'" . $frm_DatosPersonales['DeptoExpedicion'] . "',
'" . $frm_DatosPersonales['MunicipioExpedicion'] . "',
'" . $frm_DatosPersonales['FechaExpedicion'] . "',
'" . $frm_DatosPersonales['Nombre'] . "',
'" . $frm_DatosPersonales['PrimerApellido'] . "',
'" . $frm_DatosPersonales['SegundoApellido'] . "',
to_date('" . $frm_DatosPersonales['FechaNacimiento'] . "','YYYY-MM-DD'),
'" . $frm_DatosPersonales['PaisNacimiento'] . "',
'" . $frm_DatosPersonales['DeptoNacimiento'] . "',
'" . $frm_DatosPersonales['MunicipioNacimiento'] . "',
'" . $frm_DatosPersonales['GrupoSanguineo'] . "',
'" . $frm_DatosPersonales['RH'] . "',
'" . $frm_DatosPersonales['EstadoCivil'] . "',
'" . $frm_DatosPersonales['NivelEducativo'] . "',
'" . $frm_DatosPersonales['Profesion'] . "',
'" . $frm_DatosPersonales['Matricula'] . "',
$FechaMatricula,
'" . $frm_DatosPersonales['PaisResidencia'] . "',
'" . $frm_DatosPersonales['DepartamentoResidencia'] . "',
'" . $frm_DatosPersonales['MunicipioResidencia'] . "',
'" . $frm_DatosPersonales['DireccionResidencia'] . " " . $frm_DatosPersonales['DireccionResidencia1'] . "',
'" . $frm_DatosPersonales['TelefonoResidencia'] . "',
'" . $frm_DatosPersonales['BarrioResidencia'] . "',
'" . $frm_DatosPersonales['TieneVivienda'] . "',
'" . $frm_DatosPersonales['NombreEmergencia'] . "',
'" . $frm_DatosPersonales['TelefonoEmergencia'] . "',
'" . $frm_DatosPersonales['DireccionEmergencia'] . "',
'" . $frm_DatosPersonales['RolFamilia'] . "',
'" . $frm_DatosPersonales['LocalidadResidencia'] . "',
'" . $frm_DatosPersonales['ZonaVivienda'] . "',
'" . $frm_DatosPersonales['EstratoSocioeconomico'] . "',
'" . $frm_DatosPersonales['CabezaHogar'] . "',
'" . $frm_DatosPersonales['Enfermedad'] . "',
'" . $frm_DatosPersonales['Discapacidad'] . "',
'" . $frm_DatosPersonales['PorcentajeDiscapacidad'] . "',
'" . $frm_DatosPersonales['TieneInteresAdquirirVivienda'] . "',
'" . $frm_DatosPersonales['ComparteDomicilioConyugue'] . "',
'" . $frm_DatosPersonales['CorreoElectronicoPersonal'] . "',
'" . $frm_DatosPersonales['ClaseVivienda'] . "',
'" . $frm_DatosPersonales['TipoVivienda'] . "',
'" . $frm_DatosPersonales['CualEnfermedad'] . "',
'" . $frm_DatosPersonales['CuantasPersonasVive'] . "',
'" . $frm_DatosPersonales['DependientesEconomicos'] . "',
'" . $frm_DatosPersonales['CuantosHijosTiene'] . "',
'" . $frm_DatosPersonales['EPS'] . "',
'" . $frm_DatosPersonales['AFP'] . "',
'" . $frm_DatosPersonales['FCES'] . "',
'" . $frm_DatosPersonales['NombreFamiliar1'] . "',
'" . $frm_DatosPersonales['TelefonoFamiliar1'] . "',
'" . $frm_DatosPersonales['ParentescoFamiliar1'] . "',
'" . $frm_DatosPersonales['NombreFamiliar2'] . "',
'" . $frm_DatosPersonales['TelefonoFamiliar2'] . "',
'" . $frm_DatosPersonales['ParentescoFamiliar2'] . "',
'" . $frm_DatosPersonales['NombreFamiliar3'] . "',
'" . $frm_DatosPersonales['TelefonoFamiliar3'] . "',
'" . $frm_DatosPersonales['ParentescoFamiliar3'] . "',
'" . $frm_DatosPersonales['NombreFamiliar4'] . "',
'" . $frm_DatosPersonales['TelefonoFamiliar4'] . "',
'" . $frm_DatosPersonales['ParentescoFamiliar4'] . "',
'" . $frm_DatosPersonales['NombreFamiliar5'] . "',
'" . $frm_DatosPersonales['TelefonoFamiliar5'] . "',
'" . $frm_DatosPersonales['ParentescoFamiliar5'] . "',
'" . $frm_DatosPersonales['MunicipioNacimientoExt'] . "',
'" . $frm_DatosPersonales['MunicipioExpedicionExt'] . "',
'" . $frm_DatosPersonales['Genero'] . "')";

$stmt = $conn->prepare($insert_datosPersonales);
// var_dump($stmt->execute());
$stmt->execute();

while ($frm_Experiencia = $dbo->fetchArray($q_IngresosExperiencias)) {

    $frm_Experiencia = UpCaseArray($frm_Experiencia);

    $insertExperiencia = "INSERT INTO tlk_explab_nv (IDLUKEREMPLEADO,ELXE_NOM_EMPRE,ELXE_DIR_EMPRE,ELXE_TEL_EMPRE,ELXE_FEC_INGRE,ELXE_FEC_RETIR,ELXE_JEFE,ELXE_CARGO,CAUSA_CODIGO,OPERACION) VALUES (
        " . $frm_datosSocio['NumeroDocumento'] . ",
    '" . $frm_Experiencia['Empresa'] . "',
    '" . $frm_Experiencia['Direccion'] . "',
    '" . $frm_Experiencia['Telefono'] . "',
    to_date('" . $frm_Experiencia['FechaIngreso'] . "','YYYY-MM-DD'),
    to_date('" . $frm_Experiencia['FechaRetiro'] . "','YYYY-MM-DD'),
    '" . $frm_Experiencia['JefeInmediato'] . "',
    '" . $frm_Experiencia['CargoDesempenado'] . "',
    " . $frm_Experiencia['CausaRetiro'] . ",'INS')";
    $stmt = $conn->prepare($insertExperiencia);
    $stmt->execute();
    // echo '<pre>';
    // var_dump($stmt->execute());
    // var_dump("Ex");
    // die();
    unset($insert);
}
// die();
$contBeneficario = 1;
while ($frm_Beneficiarios = $dbo->fetchArray($q_IngresosBeneficiarios)) {

    $frm_Beneficiarios = UpCaseArray($frm_Beneficiarios);

    $DeptoExpedicion = explode('|', $frm_Beneficiarios['DeptoExpedicion']);
    $frm_Beneficiarios['DeptoExpedicion'] = $DeptoExpedicion[0];

    $MunicipioExpedicion = explode('|', $frm_Beneficiarios['MunicipioExpedicion']);
    $frm_Beneficiarios['MunicipioExpedicion'] = $MunicipioExpedicion[0];

    $DeptoNacimiento = explode('|', $frm_Beneficiarios['DeptoNacimiento']);
    $frm_Beneficiarios['DeptoNacimiento'] = $DeptoNacimiento[0];

    $MunicipioNacimiento = explode('|', $frm_Beneficiarios['MunicipioNacimiento']);
    $frm_Beneficiarios['MunicipioNacimiento'] = $MunicipioNacimiento[0];

    $insertBeneficiario = "INSERT INTO tlk_beneficiario_nv (IDLUCKERBENEFICIARIO,IDLUKEREMPLEADO,NOMBRE,APELLIDO1,APELLIDO2,RELAC_FAM,SEXO,TIPO_IDENT,IDENT_NUM,UGN1_CODIGO_IDENT,UGN2_CODIGO_IDENT,UGN3_CODIGO_IDENT,BENE_TIPO_SANGRE,BENE_SANGRE_RH,FEC_NACIO,UGN1_CODIGO_NACI,UGN2_CODIGO_NACI,UGN3_CODIGO_NACI,EST_CIVIL,PROFESION,DIRECCION,TELEFONO,BENEF_CAMPO_IND4,BENEF_CAMPO_IND2,BENEF_CAMPO_IND3,BENEF_CAMPO_ALF5,BENEF_CAMPO_NUM1,RESIDE_EMPLEADO,DEPENDIENTE,BENE_ESTADO,OPERACION,AFILIAR_CCF,CIUDAD_NACBEN_EXT,CIUDAD_EXPBEN_EXT) VALUES (" . $frm_Beneficiarios['IDIngresosBeneficiarios'] . ",
    " . $frm_datosSocio['NumeroDocumento'] . ",
    '" . $frm_Beneficiarios['NombreCompleto'] . "',
    '" . $frm_Beneficiarios['PrimerApellido'] . "',
    '" . $frm_Beneficiarios['SegundoApellido'] . "',
    " . $frm_Beneficiarios['Parentesco'] . ",
    '" . $frm_Beneficiarios['Genero'] . "',
    '" . $frm_Beneficiarios['TipoDocumento'] . "',
    " . $frm_Beneficiarios['NumeroDocumento'] . ",
    " . $frm_Beneficiarios['PaisExpedicion'] . ",
    '" . $frm_Beneficiarios['DeptoExpedicion'] . "',
    '" . $frm_Beneficiarios['MunicipioExpedicion'] . "',
    '" . $frm_Beneficiarios['GrupoSanguineo'] . "',
    '" . $frm_Beneficiarios['RH'] . "',
    to_date('" . $frm_Beneficiarios['FechaNacimiento'] . "','YYYY-MM-DD'),
    " . $frm_Beneficiarios['PaisNacimiento'] . ",
    '" . $frm_Beneficiarios['DeptoNacimiento'] . "',
    '" . $frm_Beneficiarios['MunicipioNacimiento'] . "',
    '" . $frm_Beneficiarios['EstadoCivil'] . "',
    " . $frm_Beneficiarios['Profesion'] . ",
    '" . $frm_Beneficiarios['Direccion'] . "',
    " . $frm_Beneficiarios['Telefono'] . ",
    '" . $frm_Beneficiarios['BeneficiarioSalud'] . "',
    '" . $frm_Beneficiarios['EnfermedadDiagnosticada'] . "',
    '" . $frm_Beneficiarios['TieneDiscapacidad'] . "',
    '" . $frm_Beneficiarios['Cual'] . "',
    '" . $frm_Beneficiarios['PorcentajeDiscapacidad'] . "',
    '" . $frm_Beneficiarios['ResideConEmpleado'] . "',
    '" . $frm_Beneficiarios['DependeEconomicamente'] . "',
    'ACT','INS',
    '" . $frm_Beneficiarios['AfiliarACaja'] . "',
    '" . $frm_Beneficiarios['MunicipioNacimientoExt'] . "',
    '" . $frm_Beneficiarios['MunicipioExpedicionExt'] . "')";
    $contBeneficario++;
    $stmt = $conn->prepare($insertBeneficiario);
    $stmt->execute();
    // echo '<pre>';
    // var_dump($stmt->execute());
    // var_dump("Ben");

    unset($insert);
    unset($DeptoExpedicion);
    unset($MunicipioExpedicion);
    unset($DeptoNacimiento);
    unset($MunicipioNacimiento);
}
while ($frm_Estudios = $dbo->fetchArray($q_IngresosEstudios)) {

    $frm_Estudio = UpCaseArray($frm_Estudios);

    $DeptoEstudio = explode('|', $frm_Estudio['Depto']);
    $frm_Estudio['Depto'] = $DeptoEstudio[0];

    $MunicipioEstudio = explode('|', $frm_Estudio['Ciudad']);
    $frm_Estudio['Ciudad'] = $MunicipioEstudio[0];

    $insertEstudios = "INSERT INTO tlk_estudios_nv (IDLUCKERESTUDIOBENEF,IDLUKEREMPLEADO,NEST_CODIGO,UGN1_CODIGO,UGN2_CODIGO,UGN3_CODIGO,TERC_DOCUMENTO,ESXB_DESC_ADIC_INSTITU,ESXB_TITULO,ESXB_IDIOMAS,ESXB_FECHA_RET,OPERACION,CIUDAD_INST_EXT) VALUES (" . $frm_Estudio['IDIngresosEstudios'] . ",'" . $frm_datosSocio['NumeroDocumento'] . "'," . $frm_Estudio['NivelEstudio'] . "," . $frm_Estudio['Pais'] . ",'" . $frm_Estudio['Depto'] . "','" . $frm_Estudio['Ciudad'] . "','" . $frm_Estudio['Institucion'] . "','" . $frm_Estudio['DescripcionInstitucion'] . "','" . $frm_Estudio['Titulo'] . "','" . $frm_Estudio['DescripcionTitulo'] . "',to_date('" . $frm_Estudio['FechaGraduacion'] . "','YYYY-MM-DD'),'INS','" . $frm_Estudio['CiudadExt'] . "')";

    $stmt = $conn->prepare($insertEstudios);
    $stmt->execute();
    // echo '<pre>';
    // var_dump("Est");
    // var_dump($stmt->execute());

    unset($insert);
}

while ($frm_Deportes = $dbo->fetchArray($q_IngresosDeportes)) {

    $frm_Deportes = UpCaseArray($frm_Deportes);
    $insertDeportes = "INSERT INTO tlk_deportes_nv (IDLUKEREMPLEADO,EMPDEP_DEPORTE,EMPDEP_ESTADO,OPERACION) VALUES ('" . $frm_datosSocio['NumeroDocumento'] . "','" . $frm_Deportes['Deporte'] . "','" . $frm_Deportes['Estado'] . "','INS')";

    $stmt = $conn->prepare($insertDeportes);
    $stmt->execute();
    // echo '<pre>';
    // var_dump("Dep");
    // var_dump($stmt->execute());

    unset($insert);
}

while ($frm_Idiomas = $dbo->fetchArray($q_IngresosIdiomas)) {

    $frm_Idiomas = UpCaseArray($frm_Idiomas);
    $insertIdiomas = "INSERT INTO tlk_idiomas_nv (IDLUKEREMPLEADO,EMPIDI_IDIOMA,EMPIDI_CUALIDAD,EMPIDI_ESTADO,OPERACION) VALUES ('" . $frm_datosSocio['NumeroDocumento'] . "'," . $frm_Idiomas['Idioma'] . ",'" . $frm_Idiomas['Cualidad'] . "','" . $frm_Idiomas['Estado'] . "','INS')";
    $stmt = $conn->prepare($insertIdiomas);
    $stmt->execute();
    // echo '<pre>';
    // var_dump("Idio");
    // var_dump($stmt->execute());

    unset($insert);
}
?>
["ok"]