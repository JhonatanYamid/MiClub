<?
	$array_value_elementos = array();
	$array_value[] = "0:Seleccione " . $datos_servicio[ "LabelElemento" ];
	foreach( $elementos["response"] as $key => $datos_elemento )
	{
		$array_value[] =  $datos_elemento[ "IDElemento" ] . ":" . $datos_elemento[ "Nombre" ];
	}//end for
?>
jQuery(grid_selector).jqGrid({
	



	
	url:'includes/async/reservas.async.php<?=$url_search ?>',
	datatype: "json",
	colNames:['<?=$datos_servicio[ "LabelElemento" ] ?>','Fecha', 'Hora', 'Socio' ],
	colModel:[
		{
			name:'IDElemento',
			index:'IDElemento', 
			align:"center",
			stype: "select",
			searchoptions: { 
				value: "<? echo implode(";", $array_value ) ?>"
			} 

        },
        {
			name:'Fecha',
			index:'Fecha', 
			align:"center",
			searchoptions: {
                // dataInit is the client-side event that fires upon initializing the toolbar search field for a column
                // use it to place a third party control to customize the toolbar
                dataInit: function (element) {
                    $(element).datepicker({
                        format: 'yyyy-mm-dd',
                        //minDate: new Date(2010, 0, 1),

                        
                    })
                    .on("changeDate", function(e) {
				        $(grid_selector).trigger('triggerToolbar');
				    });
                },
            }

        },
		{
			name:'Hora',
			index:'Hora', 
			align:"left",
			search: false
		},
		{
			name:'Socio',
			index:'Socio', 
			align:"left",
			searchoptions: {
				attr : { placeholder: "Número de derecho o número de documento" }
			}
		},
	],
	rowNum:100,
	rowList:[100,200,300],
	sortname: 'Hora',
	viewrecords: true,
	sortorder: "ASC",
	caption:"Reservas",
	height: "100%",
	width:855,
	multiselect: true,
	editurl: "includes/reservas.async.php",




	
	pager : pager_selector,
	altRows: true,
	//toppager: true,
	
	multiselect: true,
	//multikey: "ctrlKey",
    multiboxonly: true,

	loadComplete : function() {
		var table = this;
		setTimeout(function(){
			styleCheckbox(table);
			
			updateActionIcons(table);
			updatePagerIcons(table);
			enableTooltips(table);
		}, 0);

		preparaform();
	},

	



	

});