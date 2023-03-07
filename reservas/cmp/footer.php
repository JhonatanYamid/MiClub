<script src="assets/js/jquery.2.1.1.min.js"></script>
<script>
	$(document).ready(function() {
		// Accion: ver/ocultar detalle de factura
		$('.detalle').click(function(e) {
			var id = $(this).attr('data-id');
			if ($('.factura' + id).hasClass('active')) {
				$('.detalle').html('<b>Ver Detalle</b>');
				$('.detalle-factura').removeClass('active');
			} else {
				$('.detalle').html('<b>Ver Detalle</b>');
				$(this).html('<i class="fas fa-caret-up"></i>');
				$('.detalle-factura').removeClass('active');
				$('.factura' + id).addClass('active');
			}
		});

		$('.check-factura').change(function() {


			var Valor = $(this).attr('data-valor');
			var dataIdFacturas = [];
			var numeroFacturas = [];
			var sumaFacturas = 0;
			$('.check-factura').each(function() {
				if ($(this).prop('checked')) {
					dataIdFacturas.push($(this).attr("data-Id"));
					numeroFacturas.push($(this).attr("data-numeroFactura"));
					sumaFacturas += parseInt($(this).attr("data-valor"));
				}
			});
			dataIdFacturas = dataIdFacturas.join();
			numeroFacturas = numeroFacturas.join();
			console.log(dataIdFacturas);
			console.log(numeroFacturas);
			$('.suma-pagar-factura').text("Pagar $" + sumaFacturas.toLocaleString("es-CO"));
			$('.Factura').val(numeroFacturas);

			if ($('.check-factura').is(':checked')) {
				$('.texto-seleccion').hide();
				$('.suma-pagar-factura').show();
			} else {
				$('.texto-seleccion').show();
				$('.suma-pagar-factura').hide();
			}

		});
		$('.check-consumos').change(function() {


			var Valor = $(this).attr('data-valor');
			var dataIdFacturas = [];
			var numeroFacturas = [];
			var sumaFacturas = 0;
			$('.check-consumos').each(function() {
				if ($(this).prop('checked')) {
					dataIdFacturas.push($(this).attr("data-Id"));
					numeroFacturas.push($(this).attr("data-numeroFactura"));
					sumaFacturas += parseInt($(this).attr("data-valor"));
				}
			});
			dataIdFacturas = dataIdFacturas.join();
			numeroFacturas = numeroFacturas.join();
			console.log(dataIdFacturas);
			console.log(numeroFacturas);
			$('.suma-pagar-factura').text("Pagar $" + sumaFacturas.toLocaleString("es-CO"));
			$('.ConsumoId').val(dataIdFacturas);

			if ($('.check-factura').is(':checked')) {
				$('.texto-seleccion').hide();
				$('.suma-pagar-factura').show();
			} else {
				$('.texto-seleccion').show();
				$('.suma-pagar-factura').hide();
			}

		});

		$('.suma-pagar-factura').click(function() {
			if ($('.check-factura').is(':checked')) {
				$('#frmpagoVarias').submit();
			}
		});

		// Accion: redireccion de pagina con la seleccion del usuario
		$('.ir-facturas').click(function() {
			var link = $(this).attr("data-action");
			location.href = "seccionpereira.php?view=" + link;
		});
		$('.ir-reservas').click(function() {
			var link = $(this).attr("data-action");
			location.href = "seccionpereira.php?view=" + link;
		});

	});
</script>
<script src="js/noty/jquery.noty.js"></script>
<script type="text/javascript" src="js/noty/layouts/topCenter.js"></script>
<script type="text/javascript" src="js/noty/themes/default.js"></script>

<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

<script src="dist/js/bootstrap.min.js"></script>
<script src="dist/js/bootstrap.js"></script>

<script src="js/sim.js"></script>
<script src="js/common.js"></script>

<script src="js/classie.js"></script>
<script src="js/selectFx.js"></script>
<script>
	(function() {
		[].slice.call(document.querySelectorAll('select.cs-select')).forEach(function(el) {
			new SelectFx(el);
		});
	})();
</script>