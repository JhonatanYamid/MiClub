<?
	include( "../admin/config.inc.php" );
	include( "cmp/seo.php" );

	if(!empty($_POST[FechaPolo])){
		$condicion_fecha=" and FechaInicio = '".$_POST[FechaPolo]."'";
	}
	else{
		$condicion_fecha=" and FechaInicio = '".date("Y-m-d")."'";
	}

	$sql_evento="Select * From Evento Where IDClub = 34  " . $condicion_fecha;

?>

<style media="screen">
	.datagrid table { border-collapse: collapse; text-align: left; width: 100%; } .datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #991821; -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; }.datagrid table td, .datagrid table th { padding: 13px 7px; }.datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #991821), color-stop(1, #80141C) );background:-moz-linear-gradient( center top, #991821 5%, #80141C 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#991821', endColorstr='#80141C');background-color:#991821; color:#FFFFFF; font-size: 15px; font-weight: bold; border-left: 1px solid #B01C26; } .datagrid table thead th:first-child { border: none; }.datagrid table tbody td { color: #80141C; border-left: 3px solid #F7CDCD;font-size: 12px;font-weight: normal; }.datagrid table tbody .alt td { background: #F7CDCD; color: #80141C; }.datagrid table tbody td:first-child { border-left: none; }.datagrid table tbody tr:last-child td { border-bottom: none; }.datagrid table tfoot td div { border-top: 1px solid #991821;background: #F7CDCD;} .datagrid table tfoot td { padding: 0; font-size: 12px } .datagrid table tfoot td div{ padding: 2px; }.datagrid table tfoot td ul { margin: 0; padding:0; list-style: none; text-align: right; }.datagrid table tfoot  li { display: inline; }.datagrid table tfoot li a { text-decoration: none; display: inline-block;  padding: 2px 8px; margin: 1px;color: #FFFFFF;border: 1px solid #991821;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #991821), color-stop(1, #80141C) );background:-moz-linear-gradient( center top, #991821 5%, #80141C 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#991821', endColorstr='#80141C');background-color:#991821; }.datagrid table tfoot ul.active, .datagrid table tfoot ul a:hover { text-decoration: none;border-color: #80141C; color: #FFFFFF; background: none; background-color:#991821;}div.dhtmlx_window_active, div.dhx_modal_cover_dv { position: fixed !important; }
</style>
	</head>

		<body>

			Consultar eventos del dia
			<form name="fechaconsulta" id="fechaconsulta" method="post" action="" enctype="multipart/form-data">
			<input type="date" name="FechaPolo" id="FechaPolo">
			<input type="submit" name="ConsultaFechaPolo" id="ConsultaFechaPolo" value="Consultar">
		</from>

			<div class="datagrid">
<table>
    <thead>
        <tr>
            <th colspan="2">Eventos del dia</th>
        </tr>
    </thead>
    <tbody>
				<?php
				$r_evento=$dbo->query($sql_evento);
				while($datos_evento = $dbo->fetchArray($r_evento)){ ?>
				<tr>
            <td align="center">
							<?php  echo $datos_evento["Titular"]; ?>
							<img src="<?php  echo IMGEVENTO_ROOT . $datos_evento["EventoFile"]; ?>" width="300" height="434">
            	</td>

        </tr>
			<?php } ?>
    </tbody>
</table>
</div>



		</body>
		</html>
