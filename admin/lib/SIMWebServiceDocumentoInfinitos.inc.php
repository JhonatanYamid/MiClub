<?php
class SIMWebServiceDocumentoInfinitos
{
    public function get_documento_dinamico_infinito($IDClub, $IDSubmodulo, $IDModulo, $IDSocio)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $SQLTipoArchivo = "SELECT * FROM TipoArchivoInfinito WHERE IDClub = '$IDClub' AND IDModulo = '$IDModulo' AND Publicar = 'S' AND DirigidoA <> 'E' ORDER BY Orden";
        $QRYTipoArchivo = $dbo->query($SQLTipoArchivo);
        if ($dbo->rows($QRYTipoArchivo) > 0) {
            $message = $dbo->rows($QRYTipoArchivo) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($QRYTipoArchivo)) {

                $foto = "";
                $foto_icono = $r["Icono"] . "'";

                if (!empty($r["Icono"]) && empty($foto)) {
                    $foto = CLIENTE_ROOT . $r["Icono"];
                }

                $tipo_archivo["IDTipoArchivoInfinito"] = $r["IDTipoArchivoInfinito"];
                $tipo_archivo["Nombre"] = $r["Nombre"];
                $nombre_tipoarchivo = $r["NombreTipoArchivo"];
                if (!empty($nombre_tipoarchivo)) :
                    $tipo_archivo["Label"] = $nombre_tipoarchivo;
                else :
                    $tipo_archivo["Label"] = $r["Nombre"];
                endif;
                $tipo_archivo["Tipo"] = $r["Tipo"];
                $tipo_archivo["Icono"] = $foto;
                $tipo_archivo["SoloIcono"] = $r["SoloIcono"];

                //Consulto los archivos que tiene este tipo de archivo


                //para el gun club se ordena por el campo fecha
                if ($IDClub == 25) {
                    $orden = " Order by Fecha DESC";
                } else {
                    $orden = " Order by Orden DESC";
                }

                $response_archivo = array();
                $sql_archivo = "SELECT * FROM DocumentoInfinito WHERE Publicar = 'S' and IDClub = '$IDClub' AND IDModulo = '$IDModulo' AND IDTipoArchivoInfinito = '$r[IDTipoArchivoInfinito]' $orden";
                $qry_archivo = $dbo->query($sql_archivo);
                while ($r_archivo = $dbo->fetchArray($qry_archivo)) {
                
                            //aca vemos si aplica la clasificacion por grupo
              $idgrupo=$r_archivo["IDGrupoSocio"];
                   //evaluo si el socio esta en el grupo especifico
              $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$idgrupo'"); //evaluamos los grupos pero hay que recorrer los resultados
                $array_socios = explode("|||", $SociosGrupo);
                if (count($array_socios) >= 0) {
                    foreach ($array_socios as $id_socios => $datos_socio) {
                        $array_socios_noticias[] = $datos_socio;
                    }
                }
                
                 
                    
                   $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");
                   $tiposocio= $datos_socio["TipoSocio"];
                     
                   $tipos_socios= str_replace("|||",",","$r_archivo[IDTipoSocio]");
                   $tipos = substr($tipos_socios, 0, -1);
                    
                   $permiso="0";
                   $r_detalle = $dbo->query("SELECT * FROM TipoSocio WHERE IDTipoSocio IN ('" . $tipos . "')  ");
                        while ($row_detalle = $dbo->fetchArray($r_detalle)) {
 
                if($row_detalle["Nombre"] == $tiposocio):
                $permiso+=1;
                else:
                $permiso+=0;
                endif;
 
                        } 
                
                //si tiene el mismo tiposocio se agrega al array
                 if($permiso>=1){
                   $array_socios_noticias[] = $IDSocio;
                 }
               //si no hay opciones selecionadas se deja pasar para todos
                 if($r_archivo["IDGrupoSocio"]==0 and $r_archivo["IDTipoSocio"]==0){
                   $array_socios_noticias[] = $IDSocio;
                 }
                if (in_array($IDSocio, $array_socios_noticias)) {  
                 unset($array_socios_noticias);
                 $idgrupo="";
                 $permiso="";
                 
                 
                    $documento["IDClub"] = $r_archivo["IDClub"];
                    $documento["IDTipoArchivoInfinito"] = $r_archivo["IDTipoArchivoInfinito"];
                    $foto_servicio = "";
                    if (!empty($r["Icono"])) :
                        $foto_servicio = DOCUMENTO_ROOT . $r_archivo["Icono"];
                    else :
                        $foto_servicio = "";
                    endif;

                    if (empty($r_archivo["IDServicio"])) :
                        $servicio = "";
                        $id_servicio = "";
                    else :
                        $id_servicio = $r_archivo["IDServicio"];
                        $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r_archivo["IDServicio"] . "'");
                        $servicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        $icono_servicio = $dbo->getFields("Servicio", "Icono", "IDServicio = '" . $r_archivo["IDServicio"] . "'");

                        if (empty($foto_servicio)) :
                            if (!empty($icono_servicio)) :
                                $foto_servicio = SERVICIO_ROOT . $icono_servicio;
                            else :
                                $foto_servicio = "";
                            endif;
                        endif;
                    endif;

                    $documento["IDServicio"] = $id_servicio;
                    //$documento["Servicio"] = $servicio;
                    $documento["Servicio"] = $r_archivo["Nombre"];
                    $documento["IconoServicio"] = $foto_servicio;
                    $documento["IDDocumentoInfinito"] = $r_archivo["IDDocumentoInfinito"];
                    $documento["Titular"] = $r_archivo["Nombre"];
                    $documento["Subtitular"] = $r_archivo["Subtitular"];
                    $documento["Fecha"] = $r_archivo["Fecha"];
                    $documento["Descripcion"] = $r_archivo["Descripcion"];
                    $documento["MostrarPDFInternoAndroid"] = $r_archivo["MostrarPDFInternoAndroid"];

                    //ruta temporal =
                    //$ruta_temporal = str_replace("https", "http", DOCUMENTO_ROOT);
                    $ruta_temporal = DOCUMENTO_ROOT;
                    if (!empty($r_archivo["Archivo1"])) :
                        $archivo = $ruta_temporal . $r_archivo["Archivo1"];
                    else :
                        $archivo = "";
                    endif;
                    $documento["Documento"] = $archivo;

                    if (!empty($archivo))
                        array_push($response_archivo, $documento);
                } }
                //Fin consulto archivos

                $tipo_archivo["Documentos"] = $response_archivo;
                array_push($response, $tipo_archivo);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_documento_funcionario_dinamico_infinito($IDClub, $IDModulo)
    {
        $dbo = &SIMDB::get();

        $response = array();
        //$sql = "SELECT TA.*,CTA.Icono,CTA.NombreTipoArchivo FROM TipoArchivo TA,ClubTipoArchivo CTA  WHERE TA.IDTipoArchivoInfinito=CTA.IDTipoArchivoInfinito and  CTA.IDClub = '".$IDClub."' and Activo = 'S' ORDER BY Nombre";
        $sql = "SELECT * FROM TipoArchivoInfinito WHERE IDClub = '$IDClub' AND Publicar = 'S' AND IDModulo = '$IDModulo' AND (DirigidoA = 'E' or DirigidoA = 'T') ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $foto = "";
                $foto_icono = $r["Icono"] . "'";

                if (!empty($r["Icono"]) && empty($foto)) {
                    $foto = CLIENTE_ROOT . $r["Icono"];
                }

                $tipo_archivo["IDTipoArchivoInfinito"] = $r["IDTipoArchivoInfinito"];
                $tipo_archivo["Nombre"] = $r["Nombre"];
                $nombre_tipoarchivo = $r["NombreTipoArchivo"];
                if (!empty($nombre_tipoarchivo)) :
                    $tipo_archivo["Label"] = $nombre_tipoarchivo;
                else :
                    $tipo_archivo["Label"] = $r["Nombre"];
                endif;
                $tipo_archivo["Tipo"] = $r["Tipo"];
                $tipo_archivo["Icono"] = $foto;


                //para el gun club se ordena por fecha de creacion
                if ($IDClub == 25) {
                    $orden = " Order by FechaTrCr DESC";
                } else {
                    $orden = " Order by Orden DESC";
                }

                //Consulto los archivos que tiene este tipo de archivo
                $response_archivo = array();
                $sql_archivo = "SELECT * FROM DocumentoInfinito WHERE Publicar = 'S' and IDClub = '$IDClub' AND IDModulo = '$IDModulo' AND IDTipoArchivoInfinito = '$r[IDTipoArchivoInfinito]' $orden";
                $qry_archivo = $dbo->query($sql_archivo);
                while ($r_archivo = $dbo->fetchArray($qry_archivo)) {
                    $documento["IDClub"] = $r_archivo["IDClub"];
                    $documento["IDTipoArchivoInfinito"] = $r_archivo["IDTipoArchivoInfinito"];
                    $foto_servicio = "";
                    if (!empty($r["Icono"])) :
                    //$foto_servicio = DOCUMENTO_ROOT . $r_archivo["Icono"];
                    else :
                        $foto_servicio = "";
                    endif;

                    if (empty($r_archivo["IDServicio"])) :
                        $servicio = "";
                        $id_servicio = "";
                    else :
                        $id_servicio = $r_archivo["IDServicio"];
                        $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r_archivo["IDServicio"] . "'");
                        $servicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        $icono_servicio = $dbo->getFields("Servicio", "Icono", "IDServicio = '" . $r_archivo["IDServicio"] . "'");

                        if (empty($foto_servicio)) :
                            if (!empty($icono_servicio)) :
                                $foto_servicio = SERVICIO_ROOT . $icono_servicio;
                            else :
                                $foto_servicio = "";
                            endif;
                        endif;
                    endif;

                    $documento["IDServicio"] = $id_servicio;
                    //$documento["Servicio"] = $servicio;
                    $documento["Servicio"] = $r_archivo["Nombre"];
                    $documento["IconoServicio"] = $foto_servicio;
                    $documento["IDDocumentoInfinito"] = $r_archivo["IDDocumentoInfinito"];
                    $documento["Titular"] = $r_archivo["Nombre"];
                    $documento["Subtitular"] = $r_archivo["Subtitular"];
                    $documento["Fecha"] = $r_archivo["Fecha"];
                    $documento["Descripcion"] = $r_archivo["Descripcion"];
                    //ruta temporal =
                    //$ruta_temporal = str_replace("https", "http", DOCUMENTO_ROOT);
                    $ruta_temporal = DOCUMENTO_ROOT;
                    if (!empty($r_archivo["Archivo1"])) :
                        $archivo = $ruta_temporal . $r_archivo["Archivo1"];
                    else :
                        $archivo = "";
                    endif;
                    $documento["Documento"] = $archivo;

                    array_push($response_archivo, $documento);
                }
                //Fin consulto archivos

                $tipo_archivo["Documentos"] = $response_archivo;

                array_push($response, $tipo_archivo);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function
}
