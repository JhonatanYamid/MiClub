
<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="assets/js/jquery.2.1.1.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery.1.11.1.min.js"></script>
<![endif]-->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='assets/js/jquery1x.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
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


				if(!ace.vars['touch']) {
					$('.chosen-select').chosen({allow_single_deselect:true});
					//resize the chosen on window resize

					$(window)
					.off('resize.chosen')
					.on('resize.chosen', function() {
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': $this.parent().width()});
						})
					}).trigger('resize.chosen');
					//resize chosen on sidebar collapse/expand
					$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
						if(event_name != 'sidebar_collapsed') return;
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': $this.parent().width()});
						})
					});


					$('#chosen-multiple-style .btn').on('click', function(e){
						var target = $(this).find('input[type=radio]');
						var which = parseInt(target.val());
						if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
						 else $('#form-field-select-4').removeClass('tag-input-style');
					});
				}


				$(".habilita_elemento").change(function(){
					var valor=$(this).val();
					var elemento_con_tipo=$("#elemento_con_tipo").val();
					var atributo="elemento_li_"+valor;
					if(elemento_con_tipo=="S"){
						$(".divhorarios").removeClass("in active");
						$(".elementos_servicio").removeClass("active");
						$(".divelementos").show();
						$(".elementos_servicio").hide();
						$(".elemento_li_"+valor).show();
					}
				});





				$('#recent-box [data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('.tab-content')
					var off1 = $parent.offset();
					var w1 = $parent.width();

					var off2 = $source.offset();
					//var w2 = $source.width();

					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}


				$('.dialogs,.comments').ace_scroll({
					size: 300
			    });




				// Initialize ajax autocomplete:
			    $('.autocomplete-ajax').autocomplete({
			        serviceUrl: 'includes/async/socios.async.php',
			        //lookup: countriesArray,
			        dataType: "json",
			        transformResult: function(response) {
				        return {
				            suggestions: $.map(response.rows, function(dataItem) {
				                return { value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido, data: dataItem.cell.IDSocio };
				            })
				        };
				    },

				    params: {
						"oper":"searchurl"
					},

					paramName: "qryString",
					minChars: 3,

			        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
			            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
			            return re.test(suggestion.value);
			        },
			        onSelect: function(suggestion) {
			        	console.log()
			            $('#IDSocio').val( suggestion.data );
			        }

			    });

				$('.autocomplete-ajax-socios').autocomplete({
			        serviceUrl: 'includes/async/socios.async.php',
			        //lookup: countriesArray,
			        dataType: "json",
			        transformResult: function(response) {
				        return {
				            suggestions: $.map(response.rows, function(dataItem) {
				                return { value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido, data: dataItem.cell.IDSocio, numaccion:dataItem.cell.Accion  };
				            })
				        };
				    },

				    params: {
						"oper":"searchurl"
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
						$('#SocioInvitado').append('<option value="socio-'+suggestion.data+'">'+suggestion.value+'</option>');
						$('#AccionInvitado').val('');
						alert("Agregado");

			        }

			    });

					$('.autocomplete-ajax-accion').autocomplete({
			        serviceUrl: 'includes/async/socios.async.php',
			        //lookup: countriesArray,
			        dataType: "json",
			        transformResult: function(response) {
				        return {
				            suggestions: $.map(response.rows, function(dataItem) {
				                return { value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido, data: dataItem.cell.IDSocio };
				            })
				        };
				    },

				    params: {
						"oper":"searchurlaccion"
					},

					paramName: "qryString",
					minChars: 3,

			        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
			            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
			            return re.test(suggestion.value);
			        },
			        onSelect: function(suggestion) {
			        	console.log()
			            $('#IDSocio').val( suggestion.data );
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
				                return { value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido, data: dataItem.cell.IDSocio };
				            })
				        };
				    },

				    params: {
						"oper":"searchurl"
					},

					paramName: "qryString",
					minChars: 3,

			        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
			            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
			            return re.test(suggestion.value);
			        },
			        onSelect: function(suggestion) {
			        	console.log()
			            $('#IDSocioBeneficiario').val( suggestion.data );
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
				                return { value: dataItem.cell.Nombre, data: dataItem.cell.IDUsuario };
				            })
				        };
				    },

				    params: {
						"oper":"searchurl"
					},

					paramName: "qryString",
					minChars: 3,

			        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
			            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
			            return re.test(suggestion.value);
			        },
			        onSelect: function(suggestion) {
			        	console.log()
			            $('#IDUsuarioCreacion').val( suggestion.data );
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
				                return { value: dataItem.cell.Nombre, data: dataItem.cell.IDSocioInvitado, doc: dataItem.cell.NumeroDocumento };
				            })
				        };
				    },

				    params: {
						"oper":"searchurl"
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
			            $('#IDSocioInvitado').val( suggestion.data );
						$('#Nombre'+id_campo).val( suggestion.value );
						$('#NumeroDocumento'+id_campo).val( suggestion.doc );
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
				                return { value: dataItem.cell.NombreSugerido, nom: dataItem.cell.Nombre, data: dataItem.cell.IDInvitado, doc: dataItem.cell.NumeroDocumento, ape: dataItem.cell.Apellido, tel: dataItem.cell.Telefono, fecnac: dataItem.cell.FechaNacimiento, email: dataItem.cell.Email, observaciones: dataItem.cell.Observaciones, tiposangre: dataItem.cell.TipoSangre };
				            })
				        };
				    },

				    params: {
						"oper":"searchurl"
					},

					paramName: "qryString",
					minChars: 4,

			        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
			            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
			            return re.test(suggestion.value) ;
			        },
			        onSelect: function(suggestion) {
			        	console.log()
			           var id_campo = $(this).attr('alt');
			            $('#IDInvitado'+id_campo).val( suggestion.data );
						$('#Nombre'+id_campo).val( suggestion.nom );
						$('#Apellido'+id_campo).val( suggestion.ape );
						$('#NumeroDocumento'+id_campo).val( suggestion.doc );
						$('#Telefono'+id_campo).val( suggestion.tel );
						$('#Email'+id_campo).val( suggestion.email );
						$('#FechaNacimiento'+id_campo).val( suggestion.fecnac );
						$('#Observaciones'+id_campo).val( suggestion.observaciones );
						$('#TipoSangre'+id_campo).val( suggestion.tiposangre );
			        }

			    });

					$('.autocomplete-ajax-votante').autocomplete({
			        serviceUrl: 'includes/async/votacionesvotante.async.php',
			        //lookup: countriesArray,
			        dataType: "json",
			        transformResult: function(response) {
				        return {
				            suggestions: $.map(response.rows, function(dataItem) {
				                return { value: dataItem.cell.Nombre + " Predio: " + dataItem.cell.NumeroCasa + " Coeficiente: " + dataItem.cell.Coeficiente, data: dataItem.cell.IDVotacionVotante };
				            })
				        };
				    },

				    params: {
						"oper":"searchurl"						
					},

					paramName: "qryString",
					minChars: 3,

			        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
			            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
			            return re.test(suggestion.value);
			        },
			        onSelect: function(suggestion) {
			        	console.log()
			            $('#IDVotacionVotante').val( suggestion.data );
			        }

			    });


				 


					$('.autocomplete-ajax-socios-invitados').autocomplete({
 			        serviceUrl: 'includes/async/invitadosociovigente.async.php',
 			        //lookup: countriesArray,
 			        dataType: "json",
 			        transformResult: function(response) {
 				        return {
 				            suggestions: $.map(response.rows, function(dataItem) {
 				                return { value: dataItem.cell.Nombre + " " + dataItem.cell.Apellido + " " + dataItem.cell.Socio, data: dataItem.cell.IDSocio, numaccion:dataItem.cell.Accion  };
 				            })
 				        };
 				    },

 				    params: {
 						"oper":"searchurl",
						"FechaConsulta":"<?php echo $_GET["fecha"]; ?>"
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
 						$('#SocioInvitado').append('<option value="socio-'+suggestion.data+'">'+suggestion.value+'</option>');
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
				                return { value: dataItem.cell.Nombre, data: dataItem.cell.IDUsuario, numaccion:dataItem.cell.Email  };
				            })
				        };
				    },

				    params: {
						"oper":"searchurl"
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
						$('#SocioInvitado').append('<option value="usuario-'+suggestion.data+'">'+suggestion.value+'</option>');
						$('#AccionInvitado').val('');
						alert("Agregado");

			        }

			    });



			})
		</script>

        <script src="js/sim.js"></script>
	    <script src="js/common.js"></script>
