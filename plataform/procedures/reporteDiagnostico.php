 <?php
 $tipoReporte = empty( $_SESSION["TipoRepDiagnostico"] )? "" : $_SESSION["TipoRepDiagnostico"];

	SIMReg::setFromStructure( array(
						"title" => "DashBoard Diagn&oacute;stico",
						"table" => "Diagnostico",
						"key" => "IDDiagnostico",
						"mod" => "Reportes",

	) );

	$script = "reporteDiagnostico";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

	//Verificar permisos
	SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

 $idClub = SIMReg::get( "club" );

  if(empty($tipoReporte) || empty($idClub)){
    $view = "views/".$script."/form.php";
 //  exit;
 }

if(!empty($tipoReporte) && !empty($idClub)){
  $view = "views/".$script."/list.php";

 if($tipoReporte == "Socio"){
		     $sql_preguntas_perfil = "SELECT count(CED.IDCampoEditarSocio) AS Total,
                               CED.IDCampoEditarSocio,CED.Nombre, SCES.Valor
                               FROM CampoEditarSocio CED, SocioCampoEditarSocio SCES
                               WHERE SCES.IDCampoEditarSocio=CED.IDCampoEditarSocio
                                AND CED.IDClub = '".SIMReg::get( "club" )."'
                                AND CED.Tipo NOT IN ('checkbox','text','textarea','number','date','time','email')
                                AND SCES.Valor <> ''
                               GROUP BY SCES.IDCampoEditarSocio,SCES.Valor
                               ORDER BY CED.Orden";

		     $r_preguntas = $dbo->query($sql_preguntas_perfil);

		     while($r = $dbo->fetchArray($r_preguntas)){
			     $array_data[$r["IDCampoEditarSocio"]]["Pregunta"] = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ",substr(str_replace(",","-",$r["Nombre"]),0,90)."...");
			     $array_data[$r["IDCampoEditarSocio"]]["Opcion"][] = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ",substr(str_replace(",","/",$r["Valor"]),0,70));
			     $array_data[$r["IDCampoEditarSocio"]]["TotalxPregunta"][] = !empty($r["Total"])? $r["Total"] : "0";

		     }

 	     $sql_estadosSalud = "SELECT COUNT(S.IDEstadoSalud) AS Total,S.IDEstadoSalud,ES.Nombre AS Estado
                               FROM Socio S
                               LEFT JOIN EstadoSalud ES ON ES.IDEstadoSalud = S.IDEstadoSalud
                               WHERE S.IDClub = '".SIMReg::get( "club" )."'
                               AND S.IDEstadoSocio = 1
                               GROUP BY S.IDEstadoSalud";

		      $r_estadoSalud = $dbo->query($sql_estadosSalud);

		     while($r = $dbo->fetchArray($r_estadoSalud)){

            $array_dataEstadoSalud[$r["IDEstadoSalud"]] = $r["Estado"].",".$r["Total"];

		     }


		      $sql_estadosTipoEmpleado = "SELECT COUNT(S.TipoSocio) AS Total,S.TipoSocio
                                     FROM Socio S
                                     WHERE S.IDClub = '".SIMReg::get( "club" )."'
                                     AND S.IDEstadoSocio = 1
                                     GROUP BY S.TipoSocio";

		      $r_TipoEmpleado = $dbo->query($sql_estadosTipoEmpleado);

		     while($r = $dbo->fetchArray($r_TipoEmpleado)){

			    $array_dataTipo[$r["TipoSocio"]] = $r["TipoSocio"].",".$r["Total"];
		     }

		     $sql_estadoSocio = "SELECT COUNT(S.IDEstadoSocio) AS Total,S.IDEstadoSocio,ES.Nombre AS Estado
                            FROM Socio S
                             LEFT JOIN EstadoSocio ES ON ES.IDEstadoSocio = S.IDEstadoSocio
                            WHERE S.IDClub = '".SIMReg::get( "club" )."'
                                  AND S.IDEstadoSocio <> 0
                            GROUP BY S.IDEstadoSocio";

		      $r_EstadoSocio = $dbo->query($sql_estadoSocio);

		     while($r = $dbo->fetchArray($r_EstadoSocio)){
			    $array_dataEstado[$r["IDEstadoSocio"]] = $r["Estado"].",".$r["Total"];
		     }

		     $sql_TotalDiagnosticos = "SELECT count(IDSocio) AS Total,DIA
                                   FROM(
                                     SELECT S.IDSocio,DATE(DR.FechaTrCr) AS DIA
                                      FROM Diagnostico D
                                      INNER JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
                                      INNER JOIN Socio S ON DR.IDSocio = S.IDSocio
                                     WHERE D.IDClub = '".SIMReg::get( "club" )."'
                                           AND DATE(DR.FechaTrCr) BETWEEN CURDATE()-INTERVAL 2 WEEK AND CURDATE()
                                           AND DR.TipoUsuario = 'Socio'
                                     GROUP BY S.IDSocio,DIA
                                   )
                                  AS Filas
                                  GROUP BY DIA
                                  ORDER BY DIA";

		      $r_totalDiagnosticos = $dbo->query($sql_TotalDiagnosticos);

		     while($r = $dbo->fetchArray($r_totalDiagnosticos)){
			    $array_dataTotalDiagnosticos["DIA"][] = $r["DIA"];
			    $array_dataTotalDiagnosticos["Total"][] = $r["Total"];
		     }
    //  $view = "views/".$script."/list.php";
	}elseif($tipoReporte == "Funcionario"){

    $sql_preguntas_perfil = "SELECT count(CED.IDCampoEditarUsuario) AS Total,
                                  CED.IDCampoEditarUsuario,CED.Nombre, SCES.Valor
                                  FROM CampoEditarUsuario CED, UsuarioCampoEditarUsuario SCES
                                  WHERE SCES.IDCampoEditarUsuario=CED.IDCampoEditarUsuario
                                   AND CED.IDClub = '".SIMReg::get( "club" )."'
                                   AND CED.Tipo NOT IN ('checkbox','text','textarea','number','date','time','email')
                                   AND SCES.Valor <> ''
                                  GROUP BY SCES.IDCampoEditarUsuario,SCES.Valor
                                  ORDER BY CED.Orden";

   		     $r_preguntas = $dbo->query($sql_preguntas_perfil);
            $array_data = array();

   		     while($r = $dbo->fetchArray($r_preguntas)){
   			     $array_data[$r["IDCampoEditarUsuario"]]["Pregunta"] = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ",substr(str_replace(",","-",$r["Nombre"]),0,90)."...");
   			     $array_data[$r["IDCampoEditarUsuario"]]["Opcion"][] = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ",substr(str_replace(",","/",$r["Valor"]),0,70));
   			     $array_data[$r["IDCampoEditarUsuario"]]["TotalxPregunta"][] = !empty($r["Total"])? $r["Total"] : "0";

   		     }
	/*	} // End if
    */

  //  if($tipoReporte == "Funcionario" && !empty($idClub)){

           $sql_preguntas_perfil = "SELECT count(CED.IDCampoEditarUsuario) AS Total,
                                 CED.IDCampoEditarUsuario,CED.Nombre, SCES.Valor
                                 FROM CampoEditarUsuario CED, UsuarioCampoEditarUsuario SCES
                                 WHERE SCES.IDCampoEditarUsuario = CED.IDCampoEditarUsuario
                                  AND CED.IDClub = '".SIMReg::get( "club" )."'
                                  AND CED.Tipo NOT IN ('checkbox','text','textarea','number','date','time','email')
                                  AND SCES.Valor <> ''
                                 GROUP BY SCES.IDCampoEditarUsuario,SCES.Valor
                                 ORDER BY CED.Orden";

           $r_preguntas = $dbo->query($sql_preguntas_perfil);
           $array_data = array();
           while($r = $dbo->fetchArray($r_preguntas)){
             $array_data[$r["IDCampoEditarUsuario"]]["Pregunta"] = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ",substr(str_replace(",","-",$r["Nombre"]),0,90)."...");
             $array_data[$r["IDCampoEditarUsuario"]]["Opcion"][] = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ",substr(str_replace(",","/",$r["Valor"]),0,70));
             $array_data[$r["IDCampoEditarUsuario"]]["TotalxPregunta"][] = !empty($r["Total"])? $r["Total"] : "0";

           }


 	     $sql_estadosSalud = "SELECT COUNT(S.IDEstadoSalud) AS Total,S.IDEstadoSalud,ES.Nombre AS Estado
                               FROM Socio S
                               LEFT JOIN EstadoSalud ES ON ES.IDEstadoSalud = S.IDEstadoSalud
                               WHERE S.IDClub = '".SIMReg::get( "club" )."'
                               AND S.IDEstadoSocio = 1
                               GROUP BY S.IDEstadoSalud";

		      $r_estadoSalud = $dbo->query($sql_estadosSalud);

		     while($r = $dbo->fetchArray($r_estadoSalud)){

            $array_dataEstadoSalud[$r["IDEstadoSalud"]] = $r["Estado"].",".$r["Total"];

		     }


		      $sql_estadosTipoEmpleado = "SELECT COUNT(S.TipoSocio) AS Total,S.TipoSocio
                                     FROM Socio S
                                     WHERE S.IDClub = '".SIMReg::get( "club" )."'
                                     AND S.IDEstadoSocio = 1
                                     GROUP BY S.TipoSocio";

		      $r_TipoEmpleado = $dbo->query($sql_estadosTipoEmpleado);

		     while($r = $dbo->fetchArray($r_TipoEmpleado)){

			    $array_dataTipo[$r["TipoSocio"]] = $r["TipoSocio"].",".$r["Total"];
		     }

		     $sql_estadoSocio = "SELECT COUNT(S.IDEstadoSocio) AS Total,S.IDEstadoSocio,ES.Nombre AS Estado
                            FROM Socio S
                             LEFT JOIN EstadoSocio ES ON ES.IDEstadoSocio = S.IDEstadoSocio
                            WHERE S.IDClub = '".SIMReg::get( "club" )."'
                                  AND S.IDEstadoSocio <> 0
                            GROUP BY S.IDEstadoSocio";

		      $r_EstadoSocio = $dbo->query($sql_estadoSocio);

		     while($r = $dbo->fetchArray($r_EstadoSocio)){
			    $array_dataEstado[$r["IDEstadoSocio"]] = $r["Estado"].",".$r["Total"];
		     }

		     $sql_TotalDiagnosticos = "SELECT count(IDSocio) AS Total,DIA
                                   FROM(
                                     SELECT S.IDSocio,DATE(DR.FechaTrCr) AS DIA
                                      FROM Diagnostico D
                                      INNER JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
                                      INNER JOIN Socio S ON DR.IDSocio = S.IDSocio
                                     WHERE D.IDClub = '".SIMReg::get( "club" )."'
                                           AND DATE(DR.FechaTrCr) BETWEEN CURDATE()-INTERVAL 2 WEEK AND CURDATE()
                                           AND DR.TipoUsuario = 'Socio'
                                     GROUP BY S.IDSocio,DIA
                                   )
                                  AS Filas
                                  GROUP BY DIA
                                  ORDER BY DIA";

		      $r_totalDiagnosticos = $dbo->query($sql_TotalDiagnosticos);
		     while($r = $dbo->fetchArray($r_totalDiagnosticos)){
			    $array_dataTotalDiagnosticos["DIA"][] = $r["DIA"];
			    $array_dataTotalDiagnosticos["Total"][] = $r["Total"];

		     }
      } // End if
} // End if !empty($tipoReporte)

?>
