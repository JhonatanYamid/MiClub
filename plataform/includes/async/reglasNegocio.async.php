<?php
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
$frm = SIMUtil::makeSafe($_POST);

$ReglaNegocio = $dbo->fetchAll('DetalleConfiguracionCuotasSociales', 'IDDetalleConfiguracionCuotasSociales = ' . $frm['id'], 'array');

switch ($frm['criterio']) {
    case 'EstadoCivil':
        $ValorCriterio = ($ReglaNegocio['CampoCriterio'] == 'EstadoCivil') ? $ReglaNegocio['ValorCriterio'] : '';
        $EstadoCivil = SIMResources::$estadoCivilRN;
        $html = '<select name="ValorCriterio' . $frm['cont'] . '" id="ValorCriterio' . $frm['cont'] . '" class="form-control" required>';
        foreach ($EstadoCivil as $key => $value) {
            $selected = ($key == $ValorCriterio) ? "selected" : '';
            $html .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $html .= "</select>";
        echo $html;
        break;
    case 'Edad':
        $ValorCriterio = ($ReglaNegocio['CampoCriterio'] == 'Edad') ? $ReglaNegocio['ValorCriterio'] : '';
        $html = '<input type="text" name="ValorCriterio' . $frm['cont'] . '" id="ValorCriterio' . $frm['cont'] . '" min="1" max="100" class="form-control" value="' . $ValorCriterio . '" required>';
        $html .= "<label>Rango (Delimitar con '-'. Ejem: 1-2)</label>";
        echo $html;
        break;
    case 'IDCategoria':
        $ValorCriterio = ($ReglaNegocio['CampoCriterio'] == 'IDCategoria') ? $ReglaNegocio['ValorCriterio'] : '';
        $sql_cat_socio = "SELECT C.IDCategoria,Nombre FROM Categoria C, ClubCategoria CC WHERE C.IDCategoria=CC.IDCategoria AND CC.IDClub = '" . SIMUser::get("club") . "' Order by Nombre";
        $result_cat_socio = $dbo->query($sql_cat_socio);

        $html = '<select name="ValorCriterio' . $frm['cont'] . '" id="ValorCriterio' . $frm['cont'] . '" class="form-control" required>
                    <option value="">[Seleccione Categoria]</option>';
        while ($row_cat_soc = $dbo->fetchArray($result_cat_socio)) {
            $selected = ($row_cat_soc['Nombre'] == $ValorCriterio) ? "selected" : '';
            $html .= '<option value="' . $row_cat_soc['Nombre'] . '" ' . $selected . ' >' . $row_cat_soc['Nombre'] . '</option>';
        }
        $html .= '</select>';
        echo $html;
        break;
    case 'TipoSocio':
        $ValorCriterio = ($ReglaNegocio['CampoCriterio'] == 'TipoSocio') ? $ReglaNegocio['ValorCriterio'] : '';
        $sql_tipo_socio = "SELECT T.IDTipoSocio,Nombre FROM TipoSocio T WHERE Publicar = 'S' Order by Nombre";
        $result_tipo_socio = $dbo->query($sql_tipo_socio);

        $html = '<select name="ValorCriterio' . $frm['cont'] . '" id="ValorCriterio' . $frm['cont'] . '" class="form-control" required>
                    <option value="">[Seleccione Tipo Socio]</option>';
        while ($row_tipo_soc = $dbo->fetchArray($result_tipo_socio)) {
            $selected = ($row_tipo_soc['Nombre'] == $ValorCriterio) ? "selected" : '';
            $html .= '<option value="' . $row_tipo_soc['Nombre'] . '" ' . $selected . ' >' . $row_tipo_soc['Nombre'] . '</option>';
        }
        $html .= '</select>';
        echo $html;
        break;
    case 'IDParentesco':
        $ValorCriterio = ($ReglaNegocio['CampoCriterio'] == 'IDParentesco') ? $ReglaNegocio['ValorCriterio'] : '';
        $sql_parentesco = "SELECT P.IDParentesco,Nombre FROM Parentesco P WHERE Publicar = 'S' Order by Nombre";
        $result_parentesco = $dbo->query($sql_parentesco);

        $html = '<select name="ValorCriterio' . $frm['cont'] . '" id="ValorCriterio' . $frm['cont'] . '" class="form-control" required>
                    <option value="">[Seleccione Parentesco]</option>';
        while ($row_parentesco = $dbo->fetchArray($result_parentesco)) {
            $selected = ($row_parentesco['Nombre'] == $ValorCriterio) ? "selected" : '';
            $html .= '<option value="' . $row_parentesco['Nombre'] . '" ' . $selected . ' >' . $row_parentesco['Nombre'] . '</option>';
        }
        $html .= '</select>';
        echo $html;
        break;

    default:
        # code...
        break;
}
