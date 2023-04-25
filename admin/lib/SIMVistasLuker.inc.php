<?php
class SIMVistasLuker
{
    function get_data_vista_luker($nombre_vista)
    {
        $conn = &SIMDB::get();

        switch ($nombre_vista) {
            case 'vlk_paises_atg':
                $sql = "SELECT * FROM vlk_paises_atg ORDER BY UGN1_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $paises = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $paises[] = [
                        "id" => "{$datos["UGN1_CODIGO"]}",
                        "value" => $datos["UGN1_NOMBRE"]
                    ];
                }
                return $paises;
                break;
            case 'vlk_loc_bogo_atg':

                $sql = "SELECT * FROM vlk_loc_bogo_atg ORDER BY DESCRIPCION ASC";
                $q_sql = $conn->query($sql);
                $localidades = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $localidades[] = [
                        "id" => $datos["IDLocalidad"],
                        "value" => $datos["Descripcion"]
                    ];
                }
                return $localidades;
                break;
            case 'VLK_ESTCIV_ATG':
                $sql = "SELECT * FROM VLK_ESTCIV_ATG ORDER BY EST_DESCRIPCION ASC";
                $q_sql = $conn->query($sql);
                $estadosCivil = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $estadosCivil[] = [
                        "id" => "{$datos["EST_CODIGO"]}",
                        "value" => $datos["EST_DESCRIPCION"]
                    ];
                }
                return $estadosCivil;
                break;
            case 'vlk_tipo_viv_atg':
                $sql = "SELECT * FROM vlk_tipo_viv_atg ORDER BY DESCRIPCION ASC";
                $q_sql = $conn->query($sql);
                $tiposVivienda = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $tiposVivienda[] = [
                        "id" => $datos["Descripcion"],
                        "value" => $datos["Descripcion"]
                    ];
                }
                return $tiposVivienda;
                break;
            case 'vlk_clase_viv_atg':
                $sql = "SELECT * FROM vlk_clase_viv_atg  ORDER BY DESCRIPCION ASC";
                $q_sql = $conn->query($sql);
                $clasesVivienda = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $clasesVivienda[] = [
                        "id" => $datos["Descripcion"],
                        "value" => $datos["Descripcion"]
                    ];
                }
                return $clasesVivienda;
                break;
            case 'vlk_nivacademico_atg':
                $sql = "SELECT * FROM vlk_nivacademico_atg ORDER BY DESCRIPCION ASC";
                $q_sql = $conn->query($sql);
                $nivelesAcademicos = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $nivelesAcademicos[] = [
                        "id" => "{$datos["CODIGO"]}",
                        "value" => $datos["DESCRIPCION"]
                    ];
                    SIMUser::set("club", $_SESSION['club']);
                    SIMUser::set("IDSocio", $_SESSION['IDSocio']);
                }
                return $nivelesAcademicos;
                break;
            case 'vlk_profesiones_atg':
                $sql = "SELECT * FROM vlk_profesiones_atg ORDER BY PROFESION_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $profesiones = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $profesiones[] = [
                        "id" => "{$datos["PROFESION_CODIGO"]}",
                        "value" => $datos["PROFESION_NOMBRE"]
                    ];
                }
                return $profesiones;
                break;
            case 'vlk_rol_fam_atg':
                $sql = "SELECT * FROM vlk_rol_fam_atg ORDER BY DESCRIPCION ASC";
                $q_sql = $conn->query($sql);
                $rolesFamiliar = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $rolesFamiliar[] = [
                        "id" => $datos["Descripcion"],
                        "value" => $datos["Descripcion"]
                    ];
                }
                return $rolesFamiliar;
                break;
            case 'vlk_zona_viv_atg';
                $sql = "SELECT * FROM vlk_zona_viv_atg ORDER BY DESCRIPCION ASC";
                $q_sql = $conn->query($sql);
                $zonas = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $zonas[] = [
                        "id" => "{$datos["CODIGO"]}",
                        "value" => $datos["DESCRIPCION"]
                    ];
                }
                return $zonas;
                break;
            case 'VLK_EST_NIVEL_ATG':

                $sql = "SELECT * FROM VLK_EST_NIVEL_ATG ORDER BY NIVEL_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $nivelesEstudio = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $nivelesEstudio[] = [
                        "id" => "{$datos["NIVEL_CODIGO"]}",
                        "value" => $datos["NIVEL_NOMBRE"]
                    ];
                }
                return $nivelesEstudio;
                break;

            case 'vlk_relac_fam':
                $sql = "SELECT * FROM  vlk_relac_fam ORDER BY NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $relacionesFamiliar = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $relacionesFamiliar[] = [
                        "id" => "{$datos["CODIGO"]}",
                        "value" => $datos["NOMBRE"]
                    ];
                }
                return $relacionesFamiliar;
                break;
            case 'vlk_instituciones_atg':
                $sql = "SELECT * FROM  vlk_instituciones_atg ORDER BY TERC_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $instituciones = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $instituciones[] = [
                        "id" => "{$datos["TERC_DOCUMENTO"]}",
                        "value" => $datos["TERC_NOMBRE"]
                    ];
                }
                return $instituciones;
                break;
            case 'vlk_depto_atg':
                $sql = "SELECT * FROM  vlk_depto_atg ORDER BY UGN2_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $departamento = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $departamento[] = [
                        "id" => "{$datos["UGN2_CODIGO"]}|{$datos['UGN2_NOMBRE']}",
                        "value" => $datos["UGN2_NOMBRE"]
                    ];
                }
                return $departamento;
                break;
            case 'vlk_ciudad_atg':
                $sql = "SELECT * FROM  vlk_ciudad_atg ORDER BY UGN3_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $ciudad = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $ciudad[] = [
                        "id" => "{$datos["UGN3_CODIGO"]}|{$datos["UGN3_NOMBRE"]}",
                        "value" => $datos["UGN3_NOMBRE"]
                    ];
                }
                return $ciudad;
                break;
            case 'vlk_tip_doc_ident':
                $sql = "SELECT * FROM  vlk_tip_doc_ident ORDER BY NOMBRE_TIP_DOC ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["COD_TIP_DOC"]}",
                        "value" => $datos["NOMBRE_TIP_DOC"]
                    ];
                }
                return $data;
                break;
            case 'vlk_deportes_atg':
                $sql = "SELECT * FROM  vlk_deportes_atg ORDER BY DEPORTE_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $deportes = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $deportes[] = [
                        "id" => "{$datos["DEPORTE_CODIGO"]}",
                        "value" => $datos["DEPORTE_NOMBRE"]
                    ];
                }
                return $deportes;
                break;
            case 'vlk_idiomas_atg':
                $sql = "SELECT * FROM  vlk_idiomas_atg ORDER BY IDIOMA_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $idiomas = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $idiomas[] = [
                        "id" => "{$datos["IDIOMA_CODIGO"]}",
                        "value" => $datos["IDIOMA_NOMBRE"]
                    ];
                }
                return $idiomas;
                break;
            case 'vlk_causa_retiro':
                $sql = "SELECT * FROM  vlk_causa_retiro ORDER BY CAUSA_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["CAUSA_CODIGO"]}",
                        "value" => $datos["CAUSA_NOMBRE"]
                    ];
                }
                return $data;
                break;
            case 'vlk_eps_atg':
                $sql = "SELECT * FROM  vlk_eps_atg ORDER BY EPS_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["EPS_CODIGO"]}",
                        "value" => $datos["EPS_NOMBRE"]
                    ];
                }
                return $data;
                break;
            case 'vlk_afp_atg':
                $sql = "SELECT * FROM  vlk_afp_atg ORDER BY AFP_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["AFP_CODIGO"]}",
                        "value" => $datos["AFP_NOMBRE"]
                    ];
                }
                return $data;
                break;
            case 'vlk_fces_atg':
                $sql = "SELECT * FROM  vlk_fces_atg ORDER BY FCES_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["FCES_CODIGO"]}",
                        "value" => $datos["FCES_NOMBRE"]
                    ];
                }
                return $data;
                break;
            case 'vlk_sociedad_nv':
                $sql = "SELECT * FROM  vlk_sociedad_nv GROUP BY COD_SOCIEDAD ORDER BY SOCIEDAD_RAZON_SOCIAL ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["COD_SOCIEDAD"]}",
                        "value" => $datos["COD_SOCIEDAD"] . "-" . $datos["SOCIEDAD_RAZON_SOCIAL"]
                    ];
                }
                return $data;
                break;
            case 'vlk_negocio_nv':
                $sql = "SELECT * FROM  vlk_estructura_nv GROUP BY COD ORDER BY AGENCIA ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["COD"]}",
                        "value" => $datos["COD"] . "-" . $datos["NEGOCIO"]
                    ];
                }
                return $data;
                break;
            case 'vlk_area_nv':
                $sql = "SELECT * FROM  vlk_estructura_nv GROUP BY AR ORDER BY AGENCIA ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["AR"]}",
                        "value" => $datos["AREA"]
                    ];
                }
                return $data;
                break;
            case 'vlk_division_nv':
                $sql = "SELECT * FROM  vlk_estructura_nv GROUP BY DI ORDER BY AGENCIA ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["DI"]}",
                        "value" => $datos["DIVISION"]
                    ];
                }
                return $data;
                break;
            case 'vlk_depto_nv':
                $sql = "SELECT * FROM  vlk_estructura_nv GROUP BY DE ORDER BY AGENCIA ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["DE"]}",
                        "value" => $datos["DEPTO"]
                    ];
                }
                return $data;
                break;
            case 'vlk_agencia_nv':
                $sql = "SELECT * FROM  vlk_estructura_nv GROUP BY AG ORDER BY AGENCIA ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["AG"]}",
                        "value" => $datos["AGENCIA"]
                    ];
                }
                return $data;
                break;
            case 'vlk_localidad_nv':
                $sql = "SELECT * FROM  vlk_sociedad_nv ORDER BY SOCIEDAD_RAZON_SOCIAL ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => "{$datos["COD_LOCALIDAD"]}",
                        "value" => $datos["COD_LOCALIDAD"] . "-" . $datos["NOMBRE_LOCALIDAD"]
                    ];
                }
                return $data;
                break;
            case 'vlk_cargos_nv':
                $sql = "SELECT * FROM  vlk_cargos_nv ORDER BY CARGO_NOMBRE ASC";
                $q_sql = $conn->query($sql);
                $data = [];
                while ($datos = $conn->fetchArray($q_sql)) {
                    $data[] = [
                        "id" => $datos['CARGO_CODIGO'] . "-" . $datos['GRADO_CODIGO'],
                        "value" => $datos["GRADO_NOMBRE"]
                    ];
                }
                return $data;
                break;


            default:
                # code...
                break;
        }
    }
    /*function get_data_vista_luker($nombre_vista)
    {
        $conn = SIMUtil::ConexionBDLuker();
        switch ($nombre_vista) {
            case 'vlk_paises_atg':
                $sql = "SELECT * FROM vlk_paises_atg ORDER BY UGN1_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $paises = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $paises[] = [
                        "id" => "{$datos["UGN1_CODIGO"]}",
                        "value" => $datos["UGN1_NOMBRE"]
                    ];
                }
                return $paises;
                break;
            case 'vlk_loc_bogo_atg':

                $sql = "SELECT * FROM vlk_loc_bogo_atg ORDER BY DESCRIPCION ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $localidades = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $localidades[] = [
                        "id" => $datos["DESCRIPCION"],
                        "value" => $datos["DESCRIPCION"]
                    ];
                }
                return $localidades;
                break;
            case 'VLK_ESTCIV_ATG':
                $sql = "SELECT * FROM VLK_ESTCIV_ATG ORDER BY EST_DESCRIPCION ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $estadosCivil = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $estadosCivil[] = [
                        "id" => "{$datos["EST_CODIGO"]}",
                        "value" => $datos["EST_DESCRIPCION"]
                    ];
                }
                return $estadosCivil;
                break;
            case 'vlk_tipo_viv_atg':
                $sql = "SELECT * FROM vlk_tipo_viv_atg ORDER BY DESCRIPCION ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $tiposVivienda = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tiposVivienda[] = [
                        "id" => $datos["DESCRIPCION"],
                        "value" => $datos["DESCRIPCION"]
                    ];
                }
                return $tiposVivienda;
                break;
            case 'vlk_clase_viv_atg':
                $sql = "SELECT * FROM vlk_clase_viv_atg  ORDER BY DESCRIPCION ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $clasesVivienda = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $clasesVivienda[] = [
                        "id" => $datos["DESCRIPCION"],
                        "value" => $datos["DESCRIPCION"]
                    ];
                }
                return $clasesVivienda;
                break;
            case 'vlk_nivacademico_atg':
                $sql = "SELECT * FROM vlk_nivacademico_atg ORDER BY DESCRIPCION ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $nivelesAcademicos = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $nivelesAcademicos[] = [
                        "id" => "{$datos["CODIGO"]}",
                        "value" => $datos["DESCRIPCION"]
                    ];
                }
                return $nivelesAcademicos;
                break;
            case 'vlk_profesiones_atg':
                $sql = "SELECT * FROM vlk_profesiones_atg ORDER BY PROFESION_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $profesiones = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $profesiones[] = [
                        "id" => "{$datos["PROFESION_CODIGO"]}",
                        "value" => $datos["PROFESION_NOMBRE"]
                    ];
                }
                return $profesiones;
                break;
            case 'vlk_rol_fam_atg':
                $sql = "SELECT * FROM vlk_rol_fam_atg ORDER BY DESCRIPCION ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $rolesFamiliar = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $rolesFamiliar[] = [
                        "id" => $datos["DESCRIPCION"],
                        "value" => $datos["DESCRIPCION"]
                    ];
                }
                return $rolesFamiliar;
                break;
            case 'vlk_zona_viv_atg';
                $sql = "SELECT * FROM vlk_zona_viv_atg ORDER BY DESCRIPCION ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $zonas = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $zonas[] = [
                        "id" => "{$datos["CODIGO"]}",
                        "value" => $datos["DESCRIPCION"]
                    ];
                }
                return $zonas;
                break;
            case 'VLK_EST_NIVEL_ATG':

                $sql = "SELECT * FROM VLK_EST_NIVEL_ATG ORDER BY NIVEL_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $nivelesEstudio = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $nivelesEstudio[] = [
                        "id" => "{$datos["NIVEL_CODIGO"]}",
                        "value" => $datos["NIVEL_NOMBRE"]
                    ];
                }
                return $nivelesEstudio;
                break;

            case 'vlk_relac_fam':
                $sql = "SELECT * FROM  vlk_relac_fam ORDER BY NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $relacionesFamiliar = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $relacionesFamiliar[] = [
                        "id" => "{$datos["CODIGO"]}",
                        "value" => $datos["NOMBRE"]
                    ];
                }
                return $relacionesFamiliar;
                break;
            case 'vlk_instituciones_atg':
                $sql = "SELECT * FROM  vlk_instituciones_atg ORDER BY TERC_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $instituciones = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $instituciones[] = [
                        "id" => "{$datos["TERC_DOCUMENTO"]}",
                        "value" => $datos["TERC_NOMBRE"]
                    ];
                }
                return $instituciones;
                break;
            case 'vlk_depto_atg':
                $sql = "SELECT * FROM  vlk_depto_atg ORDER BY UGN2_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $departamento = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $departamento[] = [
                        "id" => "{$datos["UGN2_CODIGO"]}",
                        "value" => $datos["UGN2_NOMBRE"]
                    ];
                }
                return $departamento;
                break;
            case 'vlk_ciudad_atg':
                $sql = "SELECT * FROM  vlk_ciudad_atg ORDER BY UGN3_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $ciudad = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $ciudad[] = [
                        "id" => "{$datos["UGN3_CODIGO"]}",
                        "value" => $datos["UGN3_NOMBRE"]
                    ];
                }
                return $ciudad;
                break;
            case 'vlk_tip_doc_ident':
                $sql = "SELECT * FROM  vlk_tip_doc_ident ORDER BY NOMBRE_TIP_DOC ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = [
                        "id" => "{$datos["COD_TIP_DOC"]}",
                        "value" => $datos["NOMBRE_TIP_DOC"]
                    ];
                }
                return $data;
                break;
            case 'vlk_deportes_atg':
                $sql = "SELECT * FROM  vlk_deportes_atg ORDER BY DEPORTE_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $deportes = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $deportes[] = [
                        "id" => "{$datos["DEPORTE_CODIGO"]}",
                        "value" => $datos["DEPORTE_NOMBRE"]
                    ];
                }
                return $deportes;
                break;
            case 'vlk_idiomas_atg':
                $sql = "SELECT * FROM  vlk_idiomas_atg ORDER BY IDIOMA_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $idiomas = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $idiomas[] = [
                        "id" => "{$datos["IDIOMA_CODIGO"]}",
                        "value" => $datos["IDIOMA_NOMBRE"]
                    ];
                }
                return $idiomas;
                break;
            case 'vlk_causa_retiro':
                $sql = "SELECT * FROM  vlk_causa_retiro ORDER BY CAUSA_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = [
                        "id" => "{$datos["CAUSA_CODIGO"]}",
                        "value" => $datos["CAUSA_NOMBRE"]
                    ];
                }
                return $data;
                break;
            case 'vlk_eps_atg':
                $sql = "SELECT * FROM  vlk_eps_atg ORDER BY EPS_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = [
                        "id" => "{$datos["EPS_CODIGO"]}",
                        "value" => $datos["EPS_NOMBRE"]
                    ];
                }
                return $data;
                break;
            case 'vlk_afp_atg':
                $sql = "SELECT * FROM  vlk_afp_atg ORDER BY AFP_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = [
                        "id" => "{$datos["AFP_CODIGO"]}",
                        "value" => $datos["AFP_NOMBRE"]
                    ];
                }
                return $data;
                break;
            case 'vlk_fces_atg':
                $sql = "SELECT * FROM  vlk_fces_atg ORDER BY FCES_NOMBRE ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $data = [];
                while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = [
                        "id" => "{$datos["FCES_CODIGO"]}",
                        "value" => $datos["FCES_NOMBRE"]
                    ];
                }
                return $data;
                break;


            default:
                # code...
                break;
        }
    }*/
}
