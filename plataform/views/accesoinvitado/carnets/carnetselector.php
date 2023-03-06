<?php
$datos = $_GET;
$idClub = $datos["id_club"];
$idInvitación = $datos["id_invitacion"];
$fotoPersona = str_replace("^", "/" ,$datos['foto_persona']);
$fotoPersona = str_replace("~", "_" ,$fotoPersona);

//Generar Codigo QR
if($idClub==34):
	$parametros_codigo_qr = $datos["numero_documento"];
else:
	$parametros_codigo_qr = $datos["numero_documento"]."\r\n";
endif;

$qr=SIMUtil::generar_qr($idInvitación, $parametros_codigo_qr);

$qr = str_replace("<hr/>", "", $qr);
?>

<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-vcard orange"></i>Seleccione carné a imprimir:
		</h4>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->      
			
			
            <table class="table table-striped table-bordered table-hover">
                <tr>
					<td class="imprimir-carnet2">
						<?php include('views/accesoinvitado/carnets/carnet2.php'); ?>
					</td>                                    
                </tr>				  	
                <tr> 
					<td class="imprimir-carnet1">
						<?php include('views/accesoinvitado/carnets/carnet1.php'); ?>
					</td>                      
                </tr>
			</table>               
			  

		</div><!-- /.widget-main -->
	</div>
</div>

<?
	include( "cmp/footer_scripts.php" );
?>
<script src="assets/js/jquery.print.js"></script>
<script>
	$(".imprimir-carnet1").click(() => {		
		$(".carnet1").print({
        	globalStyles: true,
        	mediaPrint: false,
        	stylesheet: null,
        	noPrintSelector: ".no-print",
        	iframe: true,
        	append: null,
        	prepend: null,
        	manuallyCopyFormValues: true,
        	deferred: $.Deferred(),
        	timeout: 750,
        	title: null,
        	doctype: '<!doctype html>'
	});	
});

</script>
<script>
$(".imprimir-carnet2").click(() => {		
		$(".carnet2").print({
        	globalStyles: true,
        	mediaPrint: false,
        	stylesheet: null,
        	noPrintSelector: ".no-print",
        	iframe: true,
        	append: null,
        	prepend: null,
        	manuallyCopyFormValues: true,
        	deferred: $.Deferred(),
        	timeout: 750,
        	title: null,
        	doctype: '<!doctype html>'
		});
	});
</script>
