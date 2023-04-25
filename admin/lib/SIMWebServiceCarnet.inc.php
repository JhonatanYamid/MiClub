<?php
class SIMWebServiceCarnet
{
    public function  get_configuracion_carnetv2($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();


        $sql = "SELECT ColorBgCirculoLabels,LogoIzquierda,ColorBgLineaQr FROM ConfiguracionCarnet  WHERE IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($DatosConfiguracion = $dbo->fetchArray($qry)) {

                $configuracionCarnet["ColorBgCirculoLabels"] = $DatosConfiguracion["ColorBgCirculoLabels"];
                $configuracionCarnet["LogoIzquierda"] =  CLUB_ROOT . $DatosConfiguracion["LogoIzquierda"];
                $configuracionCarnet["ColorBgLineaQr"] = $DatosConfiguracion["ColorBgLineaQr"];
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracionCarnet;
        } //End if
        else {
            $respuesta["message"] = "Configuracion no esta activa";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }
}
