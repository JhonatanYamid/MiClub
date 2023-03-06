<?php
header('Content-Type: text/txt; charset=UTF-8');
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();


if ($_POST["Tabla"] == "Domicilio") {

    $Version = $_POST["Version"];

    $sql_cambio = "UPDATE Domicilio".$Version." SET  IDEstadoDomicilio = '" . $_POST["Valor"] . "' WHERE IDDomicilio = '" . $_POST["IDDomicilio"] . "'";

    $dbo->query($sql_cambio);
}elseif($_POST["Tabla"] == "Notificacion"){

    //traer todos los socios del club que tengan token
    $sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND  IDSocio = '".$_POST["IDSocio"]."' AND Token <> '' and Token <> '2byte' Limit 1";
    //$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND IDSocio = '5533' AND Token <> '' and Token <> '2byte' Limit 1";

   $qry_socios = $dbo->query( $sql_socios );
   $notificaciones = $dbo->rows( $qry_socios );

   $datos_club = $dbo->fetchAll( "Club", " IDClub = '" . SIMUser::get("club") . "' ", "array" );

   while( $r_socios = $dbo->fetchArray( $qry_socios ) )
   {
       $users = array( array( "id" => $r_socios["IDSocio"],
           "idclub"=>$r_socios["IDClub"],
           "registration_key"=>$r_socios["Token"] ,
           "deviceType"=>$r_socios["Dispositivo"] )

       );

        $EstadoSocio = $dbo->getFields( "EstadoDomicilio", "Nombre", "IDEstadoDomicilio = '" . $_POST["Valor"] . "' " );

        $message = "Estimado Socio, le informamos que su pedido esta en estado: " . $EstadoSocio;

        $custom = array( "titulo" => "Notificaciones " . $datos_club["Nombre"],
            'idseccion'    => 0,
            'tipo'         => 'General',
            'iddetalle'   => 0);

       ///enviar notificación
       SIMUtil::sendAlerts($users, $message, $custom);

       //Guardo el log
       $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha) Values ('".SIMNet::reqInt("id")."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW())");

   }//end while
}elseif($_POST["Tabla"] == "Categoria"){

    $Version = $_POST["Version"];

    $sql_cambio = "UPDATE CategoriaProducto".$Version." SET  Publicar = '" . $_POST["Valor"] . "' WHERE IDCategoriaProducto = '" . $_POST["IDCategoria"] . "'";

    $dbo->query($sql_cambio);
}elseif($_POST["Tabla"] == "Restaurante"){

    $Version = $_POST["Version"];

    $sql_cambio = "UPDATE RestauranteDomicilio".$Version." SET  Publicar = '" . $_POST["Valor"] . "' WHERE IDRestauranteDomicilio = '" . $_POST["IDRestaurante"] . "'";

    $dbo->query($sql_cambio);
}
?>
["ok"]
