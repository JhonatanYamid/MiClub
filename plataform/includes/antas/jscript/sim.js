function EvaluaReg( formEval )
{
	var fields = $( formEval ).find( ".mandatory" ).get();
	
	for( var i = 0 ; i < fields.length ; i++ )
	{
		if( $( fields[i] ).val() == "" )
		{
			alert( "El campo " + $( fields[i] ).attr( "title" ) + " se encuentra vacio y es obligario" );
			return false;
		}
	}
	
	return true;
}


function EvaluaRegConClave( formEval )
{
	var fields = $( formEval ).find( ".mandatory" ).get();
	
	for( var i = 0 ; i < fields.length ; i++ )
	{
		if( $( fields[i] ).val() == "" )
		{
			alert( "El campo " + $( fields[i] ).attr( "title" ) + " se encuentra vacio y es obligario" );
			return false;
		}
	}
	

	//Valido Claves
	var clave=$( formEval ).find("#Password").val();
	var reclave=$( formEval ).find("#Password2").val();
	
	if (clave==""){
		alert("Debe digitar una clave");	
		return false;
	}
	if (reclave==""){
		alert("Debe repetir la clave");	
		return false;
	}

	
	if(clave!=reclave){
		alert('Las claves no coinciden, por favor verifique');
		return false;
	}
	
	
	
	
	return true;
}


function number_format(num)
{
	var tmpnum = [], cont = 1;
	num = "" + num.replace( /[^\d\.]/gi , '' );

	for(var i = (num.length -1 ); i >= 0 ; i--)
	{
		tmpnum[tmpnum.length] = num.charAt(i);
		cont++;
		if(cont == 4 && i != 0)
		{
			cont = 1;
			tmpnum[tmpnum.length] = ",";
		}
	}//end for
	tmpnum.reverse();
	return tmpnum.join("");
}
