<?

	require( "admin/config.inc.php" );
	include( "plataform/procedures/home_site.php" );
	SIMUtil::cache();
	session_start();

	include( "cmp/seo.php" );


	$datos_club = $dbo->fetchAll( "Club", " IDClub = '" . $_REQUEST['extra2'] . "' ", "array" );
	$datos_reserva = $dbo->fetchAll( "ReservaGeneral", " IDReservaGeneral = '" . $_REQUEST['extra1'] . "' ", "array" );

?>

</head>
<body>


	<div id="home" class="jumbotron slide">
		<?
		foreach ($banners as $key_banner => $datos_banner) {
			# code...

		?>
			<div class="container">
				<h1><img src="img/logogrande.png" alt="<?=$datos_banner["Nombre"] ?>" /></h1>
	            <span><?=$datos_banner["Nombre"] ?></span>
			</div>
		<?
		}//end for
		?>
	</div>

	<!-- Start services section -->
	<section id="services">
		<div class="container">




        <?php
$ApiKey = $datos_club["ApiKey"];
$merchant_id = $_REQUEST['merchantId'];
$referenceCode = $_REQUEST['referenceCode'];
$TX_VALUE = $_REQUEST['TX_VALUE'];
$New_value = number_format($TX_VALUE, 1, '.', '');
$currency = $_REQUEST['currency'];
$transactionState = $_REQUEST['transactionState'];

$firma_codificada = md5($firma);
$firma_cadena = "$ApiKey~$merchant_id~$referenceCode~$New_value~$currency~$transactionState";
$firmacreada = md5($firma_cadena);

$firma = $_REQUEST['signature'];
$reference_pol = $_REQUEST['reference_pol'];
$cus = $_REQUEST['cus'];
$extra1 = $_REQUEST['description'];
$extra2 = $_REQUEST['extra2'];
$pseBank = $_REQUEST['pseBank'];
$lapPaymentMethod = $_REQUEST['lapPaymentMethod'];
$transactionId = $_REQUEST['transactionId'];

if ($_REQUEST['transactionState'] == 4 ) {
	$estadoTx = "Transacción aprobada";
}

else if ($_REQUEST['transactionState'] == 6 ) {
	$estadoTx = "Transacción rechazada";
}

else if ($_REQUEST['transactionState'] == 104 ) {
	$estadoTx = "Error";
}

else if ($_REQUEST['transactionState'] == 7 ) {
	$estadoTx = "Transacción pendiente";
}

else {
	$estadoTx=$_REQUEST['mensaje'];
}


//echo $_REQUEST['signature'] ."==". strtoupper($firmacreada);

//if (strtoupper($firma) == strtoupper($firmacreada)) { ?>

    <div class="container">

     <h2 class="wow fadeInDown">Respuesta transacci&oacute;n</h2>



       <div id="areaprint" class="box-frm row">
        <table width="90%" align="center" style="background-color:#FFFFFF">
        	<tr >
            	<td height="40px">Estado de la transaccion</td>
                <td><?php echo $estadoTx; ?></td>
            </tr>
            <tr>
            	<td height="40px">ID de la transaccion</td>
                <td><?php echo $transactionId; ?></td>
            </tr>
            <tr>
            	<td height="40px">Referencia de la venta:</td>
                <td><?php echo $reference_pol; ?></td>
            </tr>
            <tr>
            	<td height="40px">Referencia de la transaccion:</td>
                <td><?php echo $referenceCode; ?></td>
            </tr>
            <?php	if($pseBank != null) {	?>
            <tr>
            	<td height="40px">CUS:</td>
                <td><?php echo $referenceCode; ?></td>
            </tr>
             <tr>
            	<td height="40px">Banco:</td>
                <td><?php echo $referenceCode; ?></td>
            </tr>
			<?php } ?>
            <tr>
            	<td height="40px">Valor total:</td>
                <td>$<?php echo number_format($TX_VALUE); ?></td>
            </tr>
            <tr>
            	<td height="40px">Moneda:</td>
                <td><?php echo $currency; ?></td>
            </tr>
            <tr>
            	<td height="40px">Descripción</td>
                <td><?php echo ($extra1); ?></td>
            </tr>
            <tr>
            	<td height="40px">Entidad</td>
                <td><?php echo ($lapPaymentMethod); ?></td>
            </tr>
            <tr>
            	<td height="40px">Entidad</td>
                <td><?php echo ($lapPaymentMethod); ?></td>
            </tr>
            <tr>
              <td height="40px" colspan="2" align="center">
               <br>
               <?php
			   if(empty($datos_club["UrlSchema"])):
				   	$datos_club["UrlSchema"] = $dbo->getFields( "Club" , "UrlSchema" , "MerchantId = '" . $_REQUEST['usuario_id'] . "'");
			   endif;

			   ?>
               <a href="<?php echo $datos_club["UrlSchema"]; ?>show?module=reservations&section=2&detail=47&submodule=0" class="btnheadct btn btn-lg btn-primary button--ujarak">Regresar</a>
			   <!-- <a href="index.php?IDModulo=2&IDSocio=<?php echo $datos_reserva["IDSocio"]; ?>&IDClub=<?php echo $extra2; ?>" class="btnheadct btn btn-lg btn-primary button--ujarak">Regresar</a> -->
			   <br>&nbsp;


              </td>
            </tr>



        </table>


        </div>




  </div>

<?php
/*
}
else
{
?>
	<h1>Error validando firma digital.</h1>
<?php
}
*/
?>







<?
	include( "cmp/footer.php" );
?>
</body>
</html>
