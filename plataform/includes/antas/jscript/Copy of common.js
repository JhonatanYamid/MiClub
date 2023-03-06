/**
*Procedimientos y funciones de uso general
*
*/
var nav4 = window.event ? true : false;
var exprcontacto = /\[contacto\]\[(.*)\]/;

jQuery( document ).ready(function(){
	/*
	 * Nuestro calendario 
	 */
	Date.firstDayOfWeek = 7;
	Date.format = 'yyyy-mm-dd';
	
	$('.calendar').datePicker();	
	/*
	 * Intervalo de fechas
	 */
	$( '#FechaInicio' ).bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if ( d ) 
			{
				d = new Date(d);
				$('#FechaFin').dpSetStartDate( d.addDays(1).asString() );
			}
		}
	);
	
	$( '#FechaFin' ).bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) 
			{
				d = new Date( d );
				$( '#FechaInicio' ).dpSetEndDate( d.addDays( -1 ).asString() );
			}
		}
	);
	
	
	
	/*
	 * Toggle de los shortcuts
	 */
	$( "#hidemenuleft" ).click(function(){
	    $("#shortcuts").toggle();
		$(".shortcuts").width( "10px" );
	});
	/*
	 * denegacion de campos
	 */	 
	 //solo numeros
	$( "input.onlynumber" ).keypress(function( evt ){
		var key = nav4 ? evt.keyCode : evt.which;
		return /[\d]/.test( String.fromCharCode( key ) );
	});
	
	//monetario
	$( "input.money" ).keyup(function(){
		this.value = number_format( this.value );
	});
	
	//solo letras
	$( "input.onlyword" ).keypress(function(){
		var key = nav4 ? evt.keyCode : evt.which;
		return /[\w]/.test( String.fromCharCode( key ) );
	});
	
	$( "form.formvalida" ).submit(function(){ return EvaluaReg( this ) });
	
	
	if( $.tabs )
		$( '#tabsform' ).tabs();
		

	$( "#contactos form#frmcontacto" ).submit(function(){
		return guardaContactoAnunciante( $( this ).attr( "rev" ) );
	});
	
	
	
	preparaContactoAnunciante();
	
	$( "select[class*=dependent]" ).each(function(){
		
		var qry = $( this ).attr( "class" ).match( /dependent\[(.*)\]/i );
		var _this =  this;
		
		$( "select#" + qry[1] ).change(function(){
			
			var idred = $( this ).val();
			$( _this ).find( "option[value!='']" ).remove();
			
			for( var id in Red[ idred ] )
				if( parseInt(id) )	
					$( _this ).append( '<option value="' + id + '">' + Red[ idred ][id] + '</option');
		});
		
	});
	
	
	$( "a[href^='#'].slide" ).click(function(){
		var elem2show = $( this ).attr( "href" ).substring( 1 );
		
		$( "#" + elem2show ).slideToggle( "fast" );		
		return false;
	});
	
	$( "input.generarpropuesta" ).click(function(){
		$( "input#action" ).val( "generarpropuesta" );
		return true;
	});
	
});


function addNotify()
{
	if( !window.contactonotify )
	{
		window.contactonotify = $( '<div id="volatilnotif" class="mensaje info">Obteniendo datos...</div>' )
					.css({ 
							"position":"absolute",
							"top" : "300px",
							"left" : "450px",
							"width" : "300px"
				}).get();
				
		$( document.body ).append( window.contactonotify );
	}
	else
		$( window.contactonotify ).show();
}


function preparaContactoAnunciante()
{
	$( "#contactos a.editcontact" ).click(function(){
		return contactoAnunciante( $( this ) , "read" , $( this ).attr( "rev" ) );
	});
	
	$( "#contactos a.deletecontact" ).click(function(){
		return contactoAnunciante( $( this ) , "delete" , $( this ).attr( "rev" ) );
	});
}

function contactoAnunciante( obj , statuss , context )
{
	jQuery.ajax( {
		"type" : "POST",
		"data" : { "IDContacto" : obj.attr( "rel" ) , "contactoestado" : statuss , "context" : context },
		"dataType" : "json",
		"url" : "includes/anunciante/contacto.async.php" ,
		
		"beforeSend" : function(){
			
			$( "#volatilnotif" ).html( "Obteniendo Informaci&oacute;n del Contacto..." );
			
			$('html,body').animate({scrollTop: 0 }, 500);
			
			addNotify();
		},
		 
		"success" : function( data ){
			data = data.column;
			
			if( !data.deleteok )
			{
				$( "div#contactos input" ).each(function(){
					var _this = $( this );
					
					if( _this.attr( "type" ) != "hidden" && _this.attr( "type" ) != "submit" )
						_this.val( data[ _this.attr( "id" ).match( exprcontacto )[1] ] );
					else
					{
						if( _this.attr( "id" ) == "contactoestado" )
							_this.val( "update" );
						else if( _this.attr( "type" ) == "hidden" )
							_this.val( data[ _this.attr( "id" ) ] );
					}
					
				});
			}
			else
			{
				$( "div#contactos input" ).each(function(){
					var _this = $( this );
					
					if( _this.attr( "type" ) != "hidden" && _this.attr( "type" ) != "submit" )
						_this.val( "" );
					else
					{
						if( _this.attr( "id" ) == "contactoestado" )
							_this.val( "insert" );
					}
				});
				
				$( "#listacontactosanunciante a[rel="+ data.IDContacto + "]" ).parent().parent().remove();
				$( "#volatilnotif" ).html( "Contacto Borrado!" );
			}
			
			setTimeout( function(){$( "#volatilnotif" ).fadeOut( "slow" )} , 600 );
		}
	});
	
	return false;
}

function guardaContactoAnunciante( context )
{
	var myforminputs = $( "div#contactos input" ).get();
	var params = {};
	var tmpname = "";
	var myfield;

	for( var i = 0 ; i < myforminputs.length ; i++ )
	{
		myfield = $( myforminputs[i] );

		if( myfield.attr( "type" ) != "submit" )
		{
			if( tmpname = myfield.attr( "id" ).match( exprcontacto ) )
				params[ tmpname[1] ] = myfield.val();
			else
				params[ myfield.attr( "id" ) ] = myfield.val();
			
			if( myfield.attr( "type" ) != "hidden" ) myfield.val( "" );
				
		}
	}	
	
	params[ "ID" + context ] = $( "#frm input#ID" ).val();
	
	/*if( $( "#contactos form#frmcontacto" ).attr( "rev" ) == "Anunciante" )
		params[ "IDAnunciante" ] = $( "#frm input#ID" ).val();
	else
	{
		if( $( "#contactos form#frmcontacto" ).attr( "rev" ) == "Anunciante" )
			params[ "IDAgencia" ] = $( "#frm input#ID" ).val();
	}*/
	
	params[ "context" ] = context;
	
	//return false;
	jQuery.ajax( {
		"type" : "POST",
		"data" : params,
		"dataType" : "json",
		"url" : "includes/anunciante/contacto.async.php" ,
		
		"beforeSend" : function(){
			addNotify();
		},
		 
		"success" : function( data ){
			data = data.column; 
			
			if( $( "div#contactos input#contactoestado" ).val() == "update" )
			{
				$( "#listacontactosanunciante a[rel="+ data.IDContacto + "]" ).parent().parent().remove();
				$( "div#contactos input#contactoestado" ).val( "insert" )
			}
			
			var html = '<tr>';
			html += '<td align="center" width="64"><a href="#" rel="' + data.IDContacto + '" class="editcontact" rev="' + context + '"><img src="images/edit.png" border="0" /></a></td>';
			html += '<td>' +  data.Apellido + " " + data.Nombre + '</td>';
			html += '<td>' +  data.Direccion + '</td>';
			html += '<td>' +  data.Telefono + '</td>';
			html += '<td>' +  data.Ciudad + '</td>';
			html += '<td>' +  data.Email + '</td>';
			html += '<td>' +  data.Cargo + '</td>';
			html += '<td>' +  data.Celular + '</td>'; 
			html += '<td align="center" width="64"><a href="#" rel="' + data.IDContacto + '" class="deletecontact" rev="' + context + '"><img src="images/trash.png" border="0" /></a></td>';
			html += '</tr>';
			
			$( "#listacontactosanunciante" ).append( html );
			
			preparaContactoAnunciante();
			
			$( "#volatilnotif" ).html( "Contacto Guardado!!" );
			
			setTimeout( function(){
				$( "#volatilnotif" ).fadeOut( "slow" );
			} , 700);
		}
	});
	return false;
}



function subTotalPlan()
{
	var _suma = [], _total = 0;

	$( "input.subtotal", this.parentNode.parentNode ).each(function(){
		_suma.push( parseFloat( getNum( $( this ).val() ) ) );
	});
	
	_total = ( _suma[0] * _suma[1]) / 1000;
	
	if( !isNaN(_total) )	
		$( "input[id*='param[detalle][SubTotal]']" , this.parentNode.parentNode ).val( number_format( _total ) );
	return true;
}


function totalPlan()
{
	var _total = 0;
	var subtotal = parseFloat( getNum( $( "input[id*='param[detalle][SubTotal]']" , this.parentNode.parentNode ).val() ) );
	var descuento = parseFloat( getNum( $( "input[id*='param[detalle][Descuento]']" , this.parentNode.parentNode ).val() ) );
	
	if( descuento )
		_total = subtotal - ( (subtotal * descuento) / 100 );
	else
		_total = subtotal;
		
	if( !isNaN(_total) )	
		$( "input[id*='param[detalle][Total]']" , this.parentNode.parentNode ).val( number_format( _total ) );
	return true;
}

function visitasEsperadasPlan()
{
	var _total = 0;
	
	var efectividad = parseFloat( getNum( $( "input[id*='param[detalle][Efectividad]']" , this.parentNode.parentNode ).val() ) );
	
	var cantidad = parseFloat( getNum( $( "input[id*='param[detalle][Cantidad]']" , this.parentNode.parentNode ).val() ) );
	
	_total = cantidad * efectividad;
	
	if( !isNaN(_total) )
		$( "input[id*='param[detalle][VisitasEsperadas]']" , this.parentNode.parentNode ).val( number_format( _total ) );
	return true;
}


function costoxvisita()
{
	var _total = 0;
	var totalsitio = parseFloat( getNum( $( "input[id*='param[detalle][Total]']" , this.parentNode.parentNode ).val() ) );
	var visitasesperadas = parseFloat( getNum( $( "input[id*='param[detalle][VisitasEsperadas]']" , this.parentNode.parentNode ).val() ) );
	
	_total = totalsitio / visitasesperadas;
	
	if( !isNaN(_total) )
		$( "input[id*='param[detalle][CostoxVisita]']" , this.parentNode.parentNode ).val( _total );
	return true;
}


function checkall( obj , selector )
{
	var checked_status = obj.checked;
	$( selector ).each(function(){
		this.checked = checked_status;
	});
}

function number_format( num )
{
	var tmpnum = [], 
	cont = 1 , 
	charlen = 0;
	
	var fparts = [];
	
	num = getNum( num ).toString();
	
	if( num.indexOf(".") != -1 )
	{
		fparts = num.split(".");
		num = fparts[0];	
	}

	fparts = fparts[1] || fparts[1] == "" ? "." + fparts[1] : "";
		
	charlen = num.length - 1;
	
	for( var i = charlen ; i >= 0 ; i-- )
	{
		tmpnum.unshift( num.charAt( i ) );
			
		if( cont == 3 && i != 0 )
		{
			cont = 1;
			tmpnum.unshift( "," );
			continue;
		}
		cont++;
	}
	
	tmpnum.push( fparts );		
	return tmpnum.join("");
}


function acceptNum(evt){ 
	var key = !nav4 ? evt.which : evt.keyCode; 
	return (key <= 13 || (key >= 46 && key <= 57));
}

function getNum(strNum)
{
	num = strNum.toString().replace(/\$|\,/g,'');
	return isNaN( num ) ? 0 : num;
}

