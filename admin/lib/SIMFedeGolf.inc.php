<?php
class SIMFedeGolf
{
    public function set_emision_salida_fedegolf($rut, $campeonato)
    {
        if (!empty($rut) && $campeonato < 2) {

            $url = "http://www.fedegolf.cl/sistema/api/servicios.php?accion=emitesalida&rut=" . $rut . "&tknKey=1679091c5a880faf6fb5e6087eb1b2dc&campeonato=" . $campeonato . "&club=6&consumer=brisas";

            $curl = curl_init();
            // OPTIONS:
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // EXECUTE:
            $result = curl_exec($curl);
            if (!$result) {
                die("Connection Failure");
            }
            curl_close($curl);
            return $result;
        } else {
            return "Error de conexión";
        }
    }

    public function set_eliminar_salida_fedegolf($rut, $nro_boleta_club)
    {
        if (!empty($rut) && !empty($nro_boleta_club)) {
            $url = "http://www.fedegolf.cl/sistema/api/servicios.php?accion=eliminasalida&rut=" . $rut . "&tknKey=1679091c5a880faf6fb5e6087eb1b2dc&nro_boleta_club=" . $nro_boleta_club . "&club=6&consumer=brisas";

            $curl = curl_init();
            // OPTIONS:
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // EXECUTE:
            $result = curl_exec($curl);
            if (!$result) {
                die("Connection Failure");
            }
            curl_close($curl);
            return $result;
        } else {
            return "Error de conexión";
        }
    }
}


// 4638038
// 4638041
// 4638171
// 4638173