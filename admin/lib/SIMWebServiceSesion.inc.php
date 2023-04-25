<?php

class SIMWebServiceSesion
{    
    public function valida_sesion($IDValidar,$TipoApp,$TokenSesion)
    {
        $dbo = &SIMDB::get();             

        $array_token=explode("-",$TokenSesion);
        $IDValidar=$array_token[0];

        if ( !empty($IDValidar) && !empty($TokenSesion) ) {           
            if($TipoApp=="Empleado"){
                $sql_token_activo="SELECT Token FROM UsuarioTokenSesion WHERE IDUsuario = ".$IDValidar." and  Activo = 1  LIMIT 1";                
                //$sql_token_activo="SELECT Token FROM UsuarioTokenSesion WHERE Activo = 1 and Token = '".$TokenSesion."'  LIMIT 1";
            }
            else{
                $sql_token_activo="SELECT Token FROM SocioTokenSesion WHERE IDSocio = ".$IDValidar." and  Activo = 1  LIMIT 1";
                //$sql_token_activo="SELECT Token FROM SocioTokenSesion WHERE Activo = 1 and Token = '".$TokenSesion."'  LIMIT 1";
            }

            //echo $sql_token_activo;
            //verifico que el codigo exista y no haya sido utilizado
            $r_token_activo=$dbo->query($sql_token_activo);
            $row_token_sesion=$dbo->fetchArray($r_token_activo);
            if($row_token_sesion["Token"]!=$TokenSesion){
            //if(empty($row_token_sesion["Token"])){ 
                header('HTTP/1.1 401 Unauthorized', true, 401);
                $respuesta["message"] = "Su usuario ya tiene la sesion abierta en otro dispositivo!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            else{
                $respuesta["message"] = "token valido";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            }            
        }
        else{
            header('HTTP/1.1 401 Unauthorized', true, 401);
            $respuesta["message"] = "Faltan parametros de la sesion!";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }    
}
