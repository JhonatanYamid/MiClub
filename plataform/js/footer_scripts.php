<!-- basic scripts -->

<!--[if !IE]> -->
<script src="assets/js/jquery.2.1.1.min.js"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="assets/js/jquery.1.11.1.min.js"></script>
<![endif]-->

<!--[if !IE]> -->
<script type="text/javascript">
	window.jQuery || document.write("<script src='assets/js/jquery.min.js'>" + "<" + "/script>");
</script>

<!-- <![endif]-->

<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='assets/js/jquery1x.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
<script type="text/javascript">
	if ('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>
<script src="assets/js/bootstrap.min.js"></script>

<!-- page specific plugin scripts -->
<!-- page specific plugin scripts -->
<script src="assets/js/bootstrap-datepicker.min.js"></script>
<script src="assets/js/jquery.jqGrid.min.js"></script>
<script src="assets/js/grid.locale-en.js"></script>

<!--[if lte IE 8]>
		  <script src="assets/js/excanvas.min.js"></script>
		<![endif]-->
<script src="assets/js/jquery-ui.custom.min.js"></script>
<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
<script src="assets/js/chosen.jquery.min.js"></script>
<script src="assets/js/jquery.easypiechart.min.js"></script>
<script src="assets/js/jquery.sparkline.min.js"></script>
<script src="assets/js/jquery.flot.min.js"></script>
<script src="assets/js/jquery.flot.pie.min.js"></script>
<script src="assets/js/jquery.flot.resize.min.js"></script>
<script src="assets/js/bootstrap-wysiwyg.min.js"></script>
<script src="assets/js/jquery.fancybox.min.js"></script>
<script src="assets/js/jquery.fancybox.js"></script>

<!-- ace scripts -->
<script src="assets/js/ace-elements.min.js"></script>
<script src="assets/js/ace.min.js"></script>


<!-- 22cero2 scripts -->
<script src="js/noty/jquery.noty.js"></script>
<script type="text/javascript" src="js/noty/layouts/topCenter.js"></script>
<script type="text/javascript" src="js/noty/themes/default.js"></script>

<script type="text/javascript" src="js/jquery.mockjax.js"></script>
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>

<!-- Char -->
<script src="assets/js/Chart.js-master/dist/Chart.bundle.js"></script>





<!-- inline scripts related to this page -->
<script type="text/javascript">
	jQuery(function($) {


		if (!ace.vars['touch']) {
			$('.chosen-select').chosen({
				allow_single_deselect: true
			});
			//resize the chosen on window resize

			$(window)
				.off('resize.chosen')
				.on('resize.chosen', function() {
					$('.chosen-select').each(function() {
						var $this = $(this);
						$this.next().css({
							'width': $this.parent().width()
						});
					})
				}).trigger('resize.chosen');
			//resize chosen on sidebar collapse/expand
			$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
				if (event_name != 'sidebar_collapsed') return;
				$('.chosen-select').each(function() {
					var $this = $(this);
					$this.next().css({
						'width': $this.parent().width()
					});
				})
			});


			$('#chosen-multiple-style .btn').on('click', function(e) {
				var target = $(this).find('input[type=radio]');
				var which = parseInt(target.val());
				if (which == 2) $('#form-field-select-4').addClass('tag-input-style');
				else $('#form-field-select-4').removeClass('tag-input-style');
			});
		}


		$(".habilita_elemento").change(function() {
			var valor = $(this).val();
			var elemento_con_tipo = $("#elemento_con_tipo").val();
			var atributo = "elemento_li_" + valor;
			if (elemento_con_tipo == "S") {
				$(".divhorarios").removeClass("in active");
				$(".elementos_servicio").removeClass("active");
				$(".divelementos").show();
				$(".elementos_servicio").hide();
				$(".elemento_li_" + valor).show();
			}
		});





		$('#recent-box [data-rel="tooltip"]').tooltip({
			placement: tooltip_placement
		});

		function tooltip_placement(context, source) {
			var $source = $(source);
			var $parent = $source.closest('.tab-content')
			var off1 = $parent.offset();
			var w1 = $parent.width();

			var off2 = $source.offset();
			//var w2 = $source.width();

			if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
			return 'left';
		}


		$('.dialogs,.comments').ace_scroll({
			size: 300
		});




		// Initialize ajax autocomplete:
		$('.autocomplete-ajax').autocomplete({
			serviceUrl: 'includes/async/socios-ajax.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido,
							data: dataItem.cell.IDSocio,
							tel: dataItem.cell.Celular,
							doc: dataItem.cell.NumeroDocumento
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#IDSocio').val(suggestion.data);
				$('#Telefono').val(suggestion.tel);
				$('#NumeroDoc').val(suggestion.doc);

				$('#btnGuardarFormularioSocio').prop('disabled', false);

				var pathname = window.location.pathname;

				if (pathname == "/plataform/encuestas.php") {
					$.ajax({
							url: 'includes/async/formularioencuestasrespuestaunavez.async.php',
							type: 'POST',
							dataType: 'json',
							data: ({
								'oper': 'ConsulIdSocio',
								'idSocio': suggestion.data,
								'IDEncuesta': '<?php echo ($_GET["id"]); ?>'
							}),
							beforeSend: function() {
								console.log('enviando datos al servidor para verificar si el socio realizo registro en el formulario');
							}
						})
						.done(function(respuesta) {
							console.log('success');
							console.log(respuesta);
							if (respuesta.resultado == "ok") {
								alert('el socio ya lleno el formulario');
								$('#btnGuardarFormularioSocio').prop('disabled', true);
							}
						})
						.fail(function(resp) {
							console.log('error');
							console.log(resp.responseText);
						})
						.always(function() {
							// console.log('Complete');
						})
				} else
					//console.log('idSocio Ajax: ' + suggestion.data);
					//alert('imprime la ruta actual: ' + pathname);
					if (pathname == "/plataform/reportetalonera.php") {
						$.ajax({
								url: 'includes/async/reportetalonera.async.php',
								type: 'POST',
								dataType: 'json',

								data: ({
										'oper': 'ConsulIdSocio',
										'idSocio': suggestion.data,

									})

									,
								beforeSend: function() {
									console.log('enviando datos al servidor para consultar beneficiarios');
								}
							})
							.done(function(data) {
								console.log('success');
								console.log(data);
								$.each(data, function(key, registro) {

									$("#AccionInvitadoUsuario").append('<option value=' + registro.idsocio + '-' + registro.nombre + '>' + registro.nombre + " " + registro.apellido + '</option>');
								});

							})
							.fail(function(resp) {
								console.log('error');
								console.log(resp.responseText);
							})
							.always(function() {
								// console.log('Complete');
							})
					} else
						//console.log('idSocio Ajax: ' + suggestion.data);
						//alert('imprime la ruta actual: ' + pathname);
						if (pathname == "/plataform/eventos.php" || pathname == "/plataform/eventos2.php") {
							$.ajax({
									url: 'includes/async/eventos.async.php',
									type: 'POST',
									dataType: 'json',

									data: ({
											'oper': 'ConsulIdSocio',
											'idSocio': suggestion.data,

										})

										,
									beforeSend: function() {
										console.log('enviando datos al servidor para consultar beneficiarios');
									}
								})
								.done(function(data) {
									console.log('success');
									console.log(data);
									$.each(data, function(key, registro) {

										$("#IDSocioBeneficiario").append('<option value=' + registro.idsocio + '>' + registro.nombre + " " + registro.apellido + '</option>');
									});

								})
								.fail(function(resp) {
									console.log('error');
									console.log(resp.responseText);
								})
								.always(function() {
									// console.log('Complete');
								})
						}


			}

		});
		// Initialize ajax autocomplete:
		$('.autocomplete-ajax-beneficiario').autocomplete({
			serviceUrl: 'includes/async/socios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido,
							data: dataItem.cell.IDSocio
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#IDSocioBeneficiario').val(suggestion.data);
			}

		});

		// Initialize ajax autocomplete:
		$('.autocomplete-ajax-funcionario').autocomplete({
			serviceUrl: 'includes/async/usuarios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre,
							data: dataItem.cell.IDUsuario
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#IDUsuarioCreacion').val(suggestion.data);
				$('#IDUsuario').val(suggestion.data);
			}

		});

		$('.autocomplete-ajax-add').autocomplete({
			serviceUrl: 'includes/async/usuarios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre,
							data: dataItem.cell.IDUsuario
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#SocioInvitadoUsuario1').append('<option value="usuario-' + suggestion.data + '">' + suggestion.value + '</option>');

				$('#AccionInvitadoUsuario1').val('');
				alert("Agregado a la lista");
			}

		});





		$('.autocompletepadre-ajax').autocomplete({
			serviceUrl: 'includes/async/sociospadre.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido,
							data: dataItem.cell.IDSocio,
							numaccion: dataItem.cell.Accion
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 4,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#IDSocio').val(suggestion.data);
				$('#IDSocioSalida').load('includes/async/estudiantesocio.async.php?IDSocio=' + suggestion.data);

			}

		});


		// Initialize ajax autocomplete:
		$('.autocomplete-ajax-funcionarioEncuestas').autocomplete({
			serviceUrl: 'includes/async/usuarios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre,
							data: dataItem.cell.IDUsuario
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#SocioInvitadoUsuario').append('<option value="usuario-' + suggestion.data + '">' + suggestion.value + '</option>');
				$('#AccionInvitadoUsuario').val('');
				alert("Agregado");
			}

		});

		// Initialize ajax autocomplete:
		$('.autocomplete-ajax-funcionarioConfiguracionGeneral').autocomplete({
			serviceUrl: 'includes/async/usuarios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre,
							data: dataItem.cell.IDUsuario
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#SocioInvitadoUsuario').append('<option value="' + suggestion.data + '">' + suggestion.value + '</option>');
				$('#AccionInvitadoUsuario').val('');
				alert("Agregado");
			}

		});

		$('.autocomplete-ajax-funcionario-laboralUsuario').autocomplete({
			serviceUrl: 'includes/async/usuarios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre,
							data: dataItem.cell.IDUsuario
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#IDUsuario').val(suggestion.data);
			}

		});
		$('.autocomplete-ajax-funcionario-laboralUsuarioAutoriza').autocomplete({
			serviceUrl: 'includes/async/usuarios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre,
							data: dataItem.cell.IDUsuario
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#IDUsuarioAutoriza').val(suggestion.data);
			}

		});



		// Initialize ajax autocomplete:
		$('.autocomplete-ajax_invitado').autocomplete({
			serviceUrl: 'includes/async/invitadossocio.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre,
							data: dataItem.cell.IDSocioInvitado,
							doc: dataItem.cell.NumeroDocumento
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 4,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				var id_campo = $(this).attr('alt');
				$('#IDSocioInvitado').val(suggestion.data);
				$('#Nombre' + id_campo).val(suggestion.value);
				$('#NumeroDocumento' + id_campo).val(suggestion.doc);
			}

		});


		// Initialize ajax autocomplete:
		$('.autocomplete-ajax_tblinvitado').autocomplete({
			serviceUrl: 'includes/async/tblinvitados.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.NombreSugerido,
							nom: dataItem.cell.Nombre,
							data: dataItem.cell.IDInvitado,
							doc: dataItem.cell.NumeroDocumento,
							ape: dataItem.cell.Apellido,
							tel: dataItem.cell.Telefono,
							fecnac: dataItem.cell.FechaNacimiento,
							email: dataItem.cell.Email,
							observaciones: dataItem.cell.Observaciones,
							tiposangre: dataItem.cell.TipoSangre
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 4,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				var id_campo = $(this).attr('alt');
				$('#IDInvitado' + id_campo).val(suggestion.data);
				$('#Nombre' + id_campo).val(suggestion.nom);
				$('#Apellido' + id_campo).val(suggestion.ape);
				$('#NumeroDocumento' + id_campo).val(suggestion.doc);
				$('#Telefono' + id_campo).val(suggestion.tel);
				$('#Email' + id_campo).val(suggestion.email);
				$('#FechaNacimiento' + id_campo).val(suggestion.fecnac);
				$('#Observaciones' + id_campo).val(suggestion.observaciones);
				$('#TipoSangre' + id_campo).val(suggestion.tiposangre);
				console.log('#IDInvitado' + id_campo);
			}

		});

		$('.autocomplete-ajax-votante').autocomplete({
			serviceUrl: 'includes/async/votacionesvotante.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre + " Predio: " + dataItem.cell.NumeroCasa + " Coeficiente: " + dataItem.cell.Coeficiente,
							data: dataItem.cell.IDVotacionVotante
						};
					})
				};
			},

			params: {
				"oper": "searchurl",
				"IDVotacionEvento": "<?php echo $_GET["IDVotacionEvento"]; ?>"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#IDVotacionVotante').val(suggestion.data);
			}

		});


		$('.autocomplete-ajax-socios').autocomplete({
			serviceUrl: 'includes/async/socios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido,
							data: dataItem.cell.IDSocio,
							numaccion: dataItem.cell.Accion
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 4,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				//$('#SocioInvitado').val($('#SocioInvitado').val()+suggestion.value+"-"+suggestion.numaccion+"\r");
				$('#SocioInvitado').append('<option value="socio-' + suggestion.data + '">' + suggestion.value + '</option>');
				$('#AccionInvitado').val('');
				alert("Agregado");

			}

		});

		$('.autocomplete-ajax-SocioTalonera').autocomplete({
			serviceUrl: 'includes/async/socios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido,
							data: dataItem.cell.IDSocio,
							numaccion: dataItem.cell.Accion
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 4,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				//$('#SocioInvitado').val($('#SocioInvitado').val()+suggestion.value+"-"+suggestion.numaccion+"\r");
				$('#SocioInvitado').append('<option value="' + suggestion.data + "-" + suggestion.value + "|" + '">' + suggestion.value + '</option>');
				$('#AccionInvitado').val('');
				alert("Agregado");

			}

		});

		$('.autocomplete-ajax-socios-beneficiarioscanjes').autocomplete({
			serviceUrl: 'includes/async/socios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido,
							data: dataItem.cell.IDSocio,
							numaccion: dataItem.cell.Accion
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 4,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				//$('#SocioInvitado').val($('#SocioInvitado').val()+suggestion.value+"-"+suggestion.numaccion+"\r");
				$('#SocioInvitado').append('<option value="' + suggestion.data + '">' + suggestion.value + '</option>');
				$('#AccionInvitado').val('');
				alert("Agregado");

			}

		});


		$('.autocomplete-ajax-socios-invitados').autocomplete({
			serviceUrl: 'includes/async/invitadosociovigente.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido + " " + dataItem.cell.Socio,
							data: dataItem.cell.IDSocio,
							numaccion: dataItem.cell.Accion
						};
					})
				};
			},

			params: {
				"oper": "searchurl",
				"FechaConsulta": "<?php echo $_GET["fecha"]; ?>"
			},

			paramName: "qryString",
			minChars: 4,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				//$('#SocioInvitado').val($('#SocioInvitado').val()+suggestion.value+"-"+suggestion.numaccion+"\r");
				$('#SocioInvitado').append('<option value="socio-' + suggestion.data + '">' + suggestion.value + '</option>');
				$('#AccionInvitado').val('');
				alert("Agregado");

			}

		});



		$('.autocomplete-ajax-usuarios').autocomplete({
			serviceUrl: 'includes/async/usuariosclub.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre,
							data: dataItem.cell.IDUsuario,
							numaccion: dataItem.cell.Email
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 4,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				//$('#SocioInvitado').val($('#SocioInvitado').val()+suggestion.value+"-"+suggestion.numaccion+"\r");
				$('#SocioInvitado').append('<option value="usuario-' + suggestion.data + '">' + suggestion.value + '</option>');
				$('#AccionInvitado').val('');
				alert("Agregado");

			}

		});

		$('.autocomplete-ajax-tabla').autocomplete({
			serviceUrl: 'includes/async/camposformulariosocio.async.php',
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response, function(dataItem) {
						return {
							value: dataItem,
							data: dataItem
						};
					})
				};
			},
			params: {
				"oper": "autocomplete",
				"tipo": "tabla"
			},
			paramName: "qryString",
			minChars: 1,
			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				$('#NombreTabla').val(suggestion.data);
			}
		});

		// Initialize ajax autocomplete:
		$('.autocomplete-ajax-pais').autocomplete({
			serviceUrl: 'includes/async/pais.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response, function(dataItem) {
						return {
							value: dataItem.Nombre,
							data: dataItem.IDPais,
						};
					})
				};
			},

			params: {
				"oper": "autocomplete",

			},

			paramName: "qryString",
			minChars: 1,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {

				$('#PaisesConveniosCanjes').append('<option value="' + suggestion.data + '-' + suggestion.value + '">' + suggestion.value + '</option>');
				$('#Paises').val('');
				//alert(suggestion);
				alert("Agregado");
			}

		});

		// Initialize ajax autocomplete:
		$('.autocomplete-ajax-ciudad').autocomplete({
			serviceUrl: 'includes/async/ciudad.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response, function(dataItem) {
						return {
							value: dataItem.Nombre,
							data: dataItem.IDCiudad,
						};
					})
				};
			},

			params: {
				"oper": "autocomplete",

			},

			paramName: "qryString",
			minChars: 1,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {

				$('#CiudadesConveniosCanjes').append('<option value="' + suggestion.data + '-' + suggestion.value + '">' + suggestion.value + '</option>');
				$('#Ciudades').val('');
				//alert(suggestion);
				alert("Agregado");
			}

		});

		$('.autocomplete-ajax-columna').autocomplete({
			serviceUrl: 'includes/async/camposformulariosocio.async.php',
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response, function(dataItem) {
						return {
							value: dataItem,
							data: dataItem
						};
					})
				};
			},
			params: {
				"oper": "autocomplete",
				"tipo": "campo",
				"tablaName": function() {
					var idInput = document.activeElement.id;
					var nomTabla = $('#NombreTabla').val();

					if (idInput == 'CK')
						nomTabla = "Socio";

					return nomTabla
				}
			},
			paramName: "qryString",
			minChars: 1,
			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				var idInput = $(this).attr("id");

				if (idInput == 'CK') {
					$('#CampoKey').val(suggestion.data);
				} else if (idInput == 'CN') {
					$('#CampoName').val(suggestion.data);
				} else if (idInput == 'CV') {
					$('#CampoValue').val(suggestion.data);
				}
			}

		});

		$('.autocomplete-ajax-cliente').autocomplete({
			serviceUrl: 'includes/async/facturacion.async.php',

			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response, function(dataItem) {
						return {
							value: dataItem.NumeroDocumento + "-" + dataItem.Nombre + " " + dataItem.Apellido,
							data: dataItem.IDSocio,
							documento: dataItem.NumeroDocumento,
							nombre: dataItem.Nombre + " " + dataItem.Apellido,
							direccion: dataItem.Direccion,
							telefono: dataItem.Telefono,
							celular: dataItem.Celular,
							accion: dataItem.Accion
						};
					})
				};
			},

			params: {
				"oper": "autocomplete",
				"tipo": "cliente",
			},

			paramName: "qryString",
			minChars: 1,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				objSocio = suggestion;
				arrBeneficiarios = new Array();
				arrProductos = new Array();
				arrMediosPago = new Array();
				reloadGrid();
				cargarPagos();

				$('#Buscar').val("");
				$(".limpiar input").each(function() {
					$(this).val("");
				});
				$(".limpiar label, .limpiar span").each(function() {
					$(this).text('');
				});

				$(".limpiarNum input").each(function() {
					$(this).val(0);
				});

				$(".limpiarNum span").each(function() {
					$(this).text('0');
				});

				$('#IDSocio').val(suggestion.data);
				$('#Accion').val(suggestion.accion);
				$('#documento').text(suggestion.documento);
				$('#nombreCliente').text(suggestion.nombre);
				$('#direccion').text(suggestion.direccion);

				var tel = suggestion.telefono;
				var cel = suggestion.celular;
				var telefono = tel + " - " + cel;

				if ((tel == '' && cel != '') || (tel != '' && cel == ''))
					telefono = telefono.replace("-", '');

				if (tel == cel)
					telefono = tel;

				$('#telefono').text(telefono);

				$("#divCliente").show("slow");
			}

		});

		$('.autocomplete-ajax-producto').autocomplete({
			serviceUrl: 'includes/async/facturacion.async.php',

			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response, function(dataItem) {
						return {
							value: dataItem.Codigo + " - " + dataItem.Nombre + ": " + Number(dataItem.Precio).toLocaleString(),
							data: dataItem.IDProductoFacturacion,
							objProducto: dataItem
						};
					})
				};
			},

			params: {
				"oper": "autocomplete",
				"tipo": "producto",
				"idClub": function() {
					return $("#IDClub").val();
				}
			},

			paramName: "qryString",
			minChars: 1,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				let hoy = new Date();
				objProducto = suggestion.objProducto;
				var precio = Number(objProducto.Precio);
				var valImpuesto = Number(objProducto.ValorImpuesto);

				$("#BuscarProducto").val("");
				$("#Cantidad").val(1);
				$("#valDescuento").val("");
				$("#Descuentos option[value='']").prop('selected', true);

				$("#IDProductoFacturacion").val(suggestion.data);
				$("#nmProducto").text("- " + objProducto.Nombre + "(" + objProducto.Tipo + ")");
				$("#codProducto").text(objProducto.Codigo);

				$(".precio").text(precio.toLocaleString(undefined, {
					maximumFractionDigits: 0
				}));
				$("#Precio").val(precio);

				$("#nmImpuesto").text(objProducto.Impuesto);
				$("#valImpuesto").val(valImpuesto);

				calcular();

				$(".fechas").hide();
				fechaInicio();

				arrBeneficiarios = new Array();

				if (objProducto.Beneficiarios == 'S') {
					$("#beneficiarios").show();
					var arrBenef = {
						'IDSocio': objSocio.data,
						'Nombre': objSocio.nombre,
						'Documento': objSocio.documento
					};
					adminBeneficiarios(arrBenef, 'add');
				} else {
					$("#beneficiarios").hide();
					$("#listaBeneficiarios").html("");

					if (objProducto.FechaActivacion == 'S') {
						$(".fechas").show();
					}
				}
				$("#divContentP").show();
			}
		});

		$('.autocomplete-ajax-beneficiarios').autocomplete({
			serviceUrl: 'includes/async/facturacion.async.php',

			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response, function(dataItem) {
						return {
							value: dataItem.NumeroDocumento + "-" + dataItem.Nombre + " " + dataItem.Apellido,
							data: dataItem.IDSocio,
							documento: dataItem.NumeroDocumento,
							nombre: dataItem.Nombre + " " + dataItem.Apellido,
						};
					})
				};
			},

			params: {
				"oper": "autocomplete",
				"tipo": "beneficiario",
				"accion": function() {
					return $('#Accion').val();
				}
			},

			paramName: "qryString",
			minChars: 1,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				arrBenef = {
					'IDSocio': suggestion.data,
					'Nombre': suggestion.nombre,
					'Documento': suggestion.documento
				};
				adminBeneficiarios(arrBenef, 'add');
				$("#BuscarBeneficiario").val("");
			}

		});

		$('.autocomplete-ajax-transferencia').autocomplete({
			serviceUrl: 'includes/async/historicoservicios.async.php',

			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response, function(dataItem) {
						return {
							value: dataItem.NumeroDocumento + "-" + dataItem.Nombre + " " + dataItem.Apellido,
							data: dataItem.IDSocio,
						};
					})
				};
			},

			params: {
				"oper": "modal",
				"tipo": "socio"
			},

			paramName: "qryString",
			minChars: 1,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				$("#IDSocioTransfiere").val(suggestion.data);
			}

		});


		$('.autocomplete-ajax-invitadosIntegrados').autocomplete({
			serviceUrl: 'includes/async/administrarTalega.async.php',

			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response, function(dataItem) {
						return {
							value: dataItem.Nombre,
							data: dataItem.Id,
							tipo: dataItem.Tipo
						};
					})
				};
			},

			params: {
				"oper": "autocomplete",
				"tipo": "socioInvitado"
			},

			paramName: "qryString",
			minChars: 1,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				$("#IDInvitado").val(suggestion.data);
				$("#tipoInvitado").val(suggestion.tipo);
			}

		});

		// Initialize ajax autocomplete:
		$('.autocomplete-ajax-push').autocomplete({
			serviceUrl: 'includes/async/socios.async.php',
			//lookup: countriesArray,
			dataType: "json",
			transformResult: function(response) {
				return {
					suggestions: $.map(response.rows, function(dataItem) {
						return {
							value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido,
							data: dataItem.cell.IDSocio
						};
					})
				};
			},

			params: {
				"oper": "searchurl"
			},

			paramName: "qryString",
			minChars: 3,

			lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function(suggestion) {
				console.log()
				$('#IDSocio').val(suggestion.data);
			}

		});



	})
</script>

<script src="js/sim.js"></script>
<script src="js/common.js"></script>