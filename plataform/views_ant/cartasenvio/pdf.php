<?php
//require( "../../../admin/config.inc.php" );
include( "../../procedures/general_async.php" );
require_once ('../../../plataform/js/dompdf/dompdf_config.inc.php');


$array_id=explode("|",$_GET["IDCartasBase"]);
$script="cartasenvio";

foreach($array_id as $id_carta){
  if((int)$id_carta>0){


    $datos_carta = $dbo->fetchAll( "CartasBase", " IDCartasBase = '" . $id_carta . "'  ", "array" );
    $datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_carta["IDSocio"] . "'  ", "array" );
    $datos_club = $dbo->fetchAll( "Club", " IDClub = '" . $datos_socio["IDClub"] . "' ", "array" );
    $fecha_carta=SIMUtil::tiempo(date("Y-m-d"));
    //$valor_carta=number_format($datos_carta["PorVencer"],0,'','.');
    $valor_carta=number_format($datos_carta["NuevoSaldo"],0,'','.');
    $valor_general=number_format($datos_carta["GeneralValor"],0,'','.');
    $SumaSaldos=$datos_carta["Dia90"]+$datos_carta["Dia120"]+$datos_carta["Mas120"];
    $SaldoMas90=number_format($SumaSaldos,0,'','.');
    if($_GET["Modo"]=="email")
      $logoclub = '<img src="'.CLUB_ROOT.$datos_club[FotoLogoApp].'" width=150 height=134>';
    else
      $logoclub = '<img src="'.CLUB_DIR.$datos_club[FotoLogoApp].'" width=150 height=134>';

    $correo=$datos_socio["CorreoElectronico"];
    $correo="financiera@gunclub.com.co,cartera@gunclub.com.co,jorgechirivi@gmail.com";

    $IDPlantillaCarta="";
    // Consulto la plantilla
        if($datos_carta["Tipo"]=="Vitalicio" || $datos_carta["Tipo"]=="VITALICIOS"){
          $IDPlantillaCarta=4;
        }
        if($datos_carta["Tipo"]=="AUSENTES" && empty($IDPlantillaCarta) ){
          $IDPlantillaCarta=1;
        }
        elseif((int)$datos_carta["Mas120"]>0 && empty($IDPlantillaCarta)){
          $IDPlantillaCarta=6;
        }
        elseif((int)$datos_carta["Dia120"]>0 && empty($IDPlantillaCarta)){
          $IDPlantillaCarta=5;
        }
        elseif((int)$datos_carta["Dia90"]>0 && empty($IDPlantillaCarta)){
          $IDPlantillaCarta=5;
        }
        elseif((int)$datos_carta["Dia60"]>0 && empty($IDPlantillaCarta)){
          $IDPlantillaCarta=5;
        }
        elseif((int)$datos_carta["Dia30"]>0 && empty($IDPlantillaCarta)){
          $IDPlantillaCarta=3;
        }
        elseif((int)$datos_carta["PorVencer"]>0 && empty($IDPlantillaCarta)){
          $IDPlantillaCarta=3;
        }
    //Fin Consulta Plantilla

    if($IDPlantillaCarta>0){
      $html=$dbo->getFields( "CartasFormato", "Cuerpo", "IDCartasFormato = '".$IDPlantillaCarta."'");
      // Reemplazo etiquetas por valores
        $html = str_replace ("[AccionSocio]",$datos_socio["Accion"],$html);
        $html = str_replace ("[FechaCarta]",$fecha_carta,$html);
        $html = str_replace ("[NombreSocio]",utf8_decode($datos_carta["Nombres"]),$html);
        $html = str_replace ("[DireccionSocio]",$datos_socio["Direccion"],$html);
        $html = str_replace ("[ValorSaldo]",$valor_carta,$html);
        $html = str_replace ("[LogoClub]",$logoclub,$html);
        $html = str_replace ("[LogoClub]",$logoclub,$html);
        $html = str_replace ("[GeneralValor]",$valor_general,$html);
        $html = str_replace ("[SaldoMas90]",$SaldoMas90,$html);
        $html = str_replace ("[MesActual]",SIMResources::$meses[((int)date("m")-1)] ,$html);

        $pagina.=$html;
        $pagina.='<div style="page-break-after:always;"></div>';


        if($_GET["Modo"]=="email"){
            SIMUtil::envia_carta_financiera($datos_socio["IDClub"],$correo,$html);
            $usuario_crea=SIMUser::get("IDUsuario")." " . SIMUser::get("Nombre");
            $sql_log="INSERT INTO LogCartaEnviada (IDClub,IDSocio,Nombre, Accion,TextoCarta,UsuarioTrCr,FechaTrCr)
                      VALUES ('".$datos_socio["IDClub"]."','".$datos_socio["IDSocio"]."','".$datos_carta["Nombres"]."','".$datos_socio["Accion"]."','".$html."','".$usuario_crea."',NOW())";
            $dbo->query($sql_log);
        }
    }
 }
}

  if($_GET["Modo"]=="pdf" && $IDPlantillaCarta>0){
    // Instanciamos un objeto de la clase DOMPDF.
    $pdf = new DOMPDF();
    // Definimos el tamaño y orientación del papel que queremos.
    //$pdf->set_paper("A4", "portrait");
    $pdf->set_paper("A4", "portrait");
    // Cargamos el contenido HTML.
    $pdf->load_html(utf8_encode($pagina));
    // Renderizamos el documento PDF.
    $pdf->render();
    $pdf->stream("Carta".$datos_socio["Accion"].".pdf");
  }
  elseif($_GET["Modo"]=="email"){
        SIMHTML::jsRedirect( "../../".$script.".php?envio_carta_exitoso=S" );
    }
?>
