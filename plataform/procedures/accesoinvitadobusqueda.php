<?php 
//Consulta Autorizaciones
      $sqlAutorizacion = "SELECT I.*
        FROM Invitado I 
        INNER JOIN SocioAutorizacion SA ON I.IDInvitado=SA.IDInvitado
        WHERE  SA.FechaInicio <= CURDATE()
        AND SA.FechaFin >= CURDATE()      
        AND (I.Nombre LIKE '%$qryString%' OR I.Apellido LIKE '%$qryString%')
        AND  I.IDClub = " . SIMUser::get("club");       
      $queryAutorizaciones = $dbo->query($sqlAutorizacion);
      $autorizaciones  = $dbo->fetch($queryAutorizaciones);
      if(isset($autorizaciones["IDInvitado"])){
        $autorizaciones = [$autorizaciones];
      }
      

      //Consulta Invitados
      $sqlInvitado = "SELECT *
        FROM Invitado I 
        INNER JOIN SocioInvitado SI on I.IDInvitado=SI.IDInvitado 
        WHERE FechaIngreso = CURDATE() 
        AND (I.Nombre LIKE '%$qryString%' OR I.Apellido LIKE '%$qryString%')
        AND  I.IDClub = " . SIMUser::get("club"); 
      $queryInvitados = $dbo->query($sqlInvitado);
      $invitados = $dbo->fetch($queryInvitados);
      if(isset($invitados["IDInvitado"])){
        $invitados = [$invitados];
      }              

      //Consulta invitados especiales
      $sqlInvitadoEspecial = "SELECT *
        FROM Invitado I
        INNER JOIN SocioInvitadoEspecial SIE ON SIE.IDInvitado=I.IDInvitado 
        WHERE  FechaInicio <= CURDATE()
        AND FechaFin >= CURDATE()
        AND (I.Nombre LIKE '%$qryString%' OR I.Apellido LIKE '%$qryString%')
        AND  I.IDClub = " . SIMUser::get("club"); 
      $queryInvitadosEspecial = $dbo->query($sqlInvitadoEspecial);
      $invitadosEspecial = $dbo->fetch($queryInvitadosEspecial);
      if(isset($invitadosEspecial["IDInvitado"])){
        $invitadosEspecial = [$invitadosEspecial];
      }     

      //Consulta funcionarios
      $sqlusuario = "SELECT *
      FROM Usuario
      WHERE Nombre LIKE '%$qryString%'
      AND  IDClub = " . SIMUser::get("club"); 
      $queryUsuarios = $dbo->query($sqlusuario);
      $usuarios = $dbo->fetch($queryUsuarios);
      if(isset($usuarios["IDUsuario"])){
        $usuarios = [$usuarios];
      }  

      //Consulta Socio     
      $sqlSocio = "SELECT *
        FROM Socio
        WHERE (Nombre LIKE '%$qryString%' OR Apellido LIKE '%$qryString%')
        AND IDClub = " . SIMUser::get("club"); 
      $querySocio = $dbo->query($sqlSocio);
      $socios = $dbo->fetch($querySocio);
      if(isset($socios["IDSocio"])){
        $socios = [$socios];
      }

     
      