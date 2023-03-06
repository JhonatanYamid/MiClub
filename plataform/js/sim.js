function EvaluaReg(formEval) {
	var fields = $(formEval).find(".mandatory").get();

	for (var i = 0; i < fields.length; i++) {

		// if (fields[i].type == 'checkbox' || fields[i].type == 'radio')//if( $( fields[i] + ":checkbox" ) )
		if (fields[i].type == 'checkbox')//if( $( fields[i] + ":checkbox" ) )
		{
			if (!$(fields[i]).is(":checked")) {
				alert("El campo " + $(fields[i]).attr("title") + " se encuentra vacio y es obligatorio");
				$(fields[i]).focus();
				return false
			}
		}
		else {

			if ($(fields[i]).val() == "") {

				n = noty({
					text: $(fields[i]).attr("title") + " es requerido",
					type: 'alert',
					dismissQueue: true,
					layout: "topCenter",
					theme: 'defaultTheme',
					modal: true,
					closeWith: ['button'],
					buttons: [{
						addClass: 'btn btn-primary', text: "Ok", onClick: function ($noty) {
							$noty.close();
						}
					}
					],


				});

				//alert( "El campo " + $( fields[i] ).attr( "title" ) + " se encuentra vacio y es obligario" );
				//$("p.error").show();
				return false;
			}


		}

		if ($(fields[i]).hasClass("valmail"))//Validar mail
		{
			var expresion = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
			if (!expresion.test($(fields[i]).val())) {
				alert("El campo " + $(fields[i]).attr("title") + " no es un mail valido");
				$(fields[i]).focus();
				return false;
			}
		}


	}


	return true;
}






function number_format(num) {
	var tmpnum = [], cont = 1;
	num = "" + num.replace(/[^\d\.]/gi, '');

	for (var i = (num.length - 1); i >= 0; i--) {
		tmpnum[tmpnum.length] = num.charAt(i);
		cont++;
		if (cont == 4 && i != 0) {
			cont = 1;
			tmpnum[tmpnum.length] = ",";
		}
	}//end for
	tmpnum.reverse();
	return tmpnum.join("");
}
