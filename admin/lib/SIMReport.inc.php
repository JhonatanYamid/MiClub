<?php
class SIMReport
{
	
function view_data_report($title,$sql="",$exporxls=""){

Global $viewgraph;


$now_date = date('m-d-Y H:i');
$dbo =& SIMDB::get();
$result = $dbo->query($sql);



$col =1;


$TituloGraphini = "";

$numregs = $dbo->rows($result);

if($numregs == 0){
	$strreport .= "<br><span class=titulo>La busqueda no arrojo resultados</span>";
	
	return 0;
}

$strreport .= "<table class=adminlist><tr>";

for ($i = 0; $i < $dbo->fields($result); $i++) {
		$colname = $dbo->fieldName($result,$i);
	
		if(substr($colname,0,4) == "TOT_" || substr($colname,0,4) == "CNT_" || substr($colname,0,4) == "COD_"){
			$titcol = str_replace("_"," ",substr($colname,4,strlen($colname)));
			
				
			$strtxt .= "$titcol\t"; // TXT
			
			if(empty($TituloGraphini))
				$TituloGraphini = $titcol;
		
			 if($viewgraph && substr($colname,0,4) <> "COD_"){
		 		  $titcol.= "&nbsp;&nbsp;<a href=\"#\" onclick=\"setcolreport('$titcol','$col')\"><img src='iconos/stats.gif' border='0'></a>";
			
				$col++;
			 }
			$colname = $titcol;
			
		}else{
			$colname = str_replace("_"," ",$colname);
			
			$strtxt .= "$colname\t"; // TXT
		}
			$strreport .= "<th>$colname</th>";

} // END FOR

$strreport .= "</tr>";

$strtxt .= "\r"; // TXT

$numcols = $dbo->fields($result);
$col = 0;
$arrayTOT = array();
$arrayParam = array();

    while($row = $dbo->row($result)){
       $strreport .= "<tr class=";
       $strreport .=  SIMUtil::repetition()?'row0>':'row1>';
       
        for($j=0; $j < $numcols; $j++){
        
        	if($j==0)
        		$col = substr($row[$j],0,10);
        
        		$coltype = mysql_field_type($result,$j);
        		$columnamesub = substr($dbo->fieldName($result,$j),0,4);
        
        
        	if(($coltype == "real" || $coltype == "int") && $columnamesub <> "COD_"){
        		$strreport .= "<td align=right>";

        			if($columnamesub == "CNT_") // Si es solo count sin decimales
        				$valueformat = number_format($row[$j],0,'.',',');
        			else
        				$valueformat = SIMReport::formatnum($row[$j]);
        				
        		$strreport .= $valueformat;
        		
        		$strtxt .= "$valueformat\t"; // TXT
        		
        		$strreport .= "</td>";
        	}
        	else{
        		$strreport .= "<td>".$row[$j]."</td>";
        	
        		$strtxt .= $row[$j]."\t";	
        	}
        
        	if($columnamesub == "TOT_" || $columnamesub == "CNT_"){
        		$arrayTOT[$j] += $row[$j];
        		$arrayParam[$col][$j] = $row[$j];
        	}
        	else
        		$arrayTOT[$j] = "STR";
        	
        } // END FOR
         $strreport .= "</tr>";
     
         $strtxt .= "\r"; // TXT
     
        // $col++;
    
    } // END WHILE
    
     $strreport .= "<tr class=total><td>Total :</td>";
   
   	 $strtxt .= "Total :\t"; // TXT
   	 
	   for($i=0;$i< $numcols;$i++){
	   
	   		if(!empty($arrayTOT[$i]) && $arrayTOT[$i] <> "STR") {
	  	 		$strreport .= "<td align=right>";
			 	$strreport .= SIMReport::formatnum($arrayTOT[$i]);
			 	
			 	$strtxt .= SIMReport::formatnum($arrayTOT[$i])."\t"; // TXT
			 	 
			   	$strreport .= "</td>";
			}elseif($arrayTOT[$i] == "STR" AND $i>0){
				$strreport .= "<td></td>";
			
				 $strtxt .= "\t"; // TXT
			}
				
	   	} // END for
    
     $strreport .= "</tr>";
     
$strreport .= "</table>";


$arreglo_aux = Array(); 
foreach( $arrayParam as $keymaster => $value ) 
   foreach( $value as $key => $elemento  ) 
       $arreglo_aux[$key][$keymaster] = $elemento; 
       
  
    $col =1;
		for($i=0; $i<= count($arreglo_aux);$i++){
			$str_param = '';
			if(!empty($arreglo_aux[$i])){
				
				foreach($arreglo_aux[$i] AS $key => $value )
					$str_param[] = "$key-$value";
				
				$strreport .= "<input type=hidden name=DATA$col value=\"".implode(",",$str_param)."\">";

				$col++;
			} //end if	
		}
	
	$strreport .= "<script>document.formdata.TituloGraphini.value='$TituloGraphini';</script>";
	
	return array("numregs"=>$numregs,"strreport"=>$strreport,"strtxt"=>$strtxt);
} // End function




function view_data_report_array($title,$array="",$exporxls=""){

Global $viewgraph, $Mes_array_report, $array_mostrar, $numerocols, $array_cols, $meses, $columnas, $total;


$now_date = date('m-d-Y H:i');


$col =1;


$TituloGraphini = "";

$numregs = count($array);

if($numregs == 0){
	$strreport .= "<br><span class=titulo>La busqueda no arrojo resultados</span>";
	
	return 0;
}

$strreport .= "<table class=adminlist><tr>";

	foreach( $array_mostrar as $key => $value )
	{
		$strtxt .= $value."\t";
		$strreport .= "<th>$value</th>";
	}//end for
	
	$ok = 0;
	//print_r( $array );
	foreach( $meses as $mesmostrar => $mes )
	{
		$strtxt .= $Mes_array_report[$mesmostrar]."\t";
		//$titcol.= "&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"setcolreport('$mesmostrar','$col')\"><img src='iconos/stats.gif' border='0'></a>";
		$strreport .= "<th>".$titcol." ".$Mes_array_report[$mesmostrar]."</th>";
		$col++;
	}//end for
	
	$strreport .= "</tr>";
	$strtxt .= "\r"; // TXT
	
	//MOSTRAR DATOS ARRAY
	
		foreach( $array as $keyvalor => $valor )
		{
			$strreport .= "<tr class=";
	       	$strreport .= repetition()?'row0>':'row1>';
	       
	        foreach( $array_mostrar as $key => $value )
			{
				$strtxt .= $columnas[$value][$keyvalor]."\t";
				$strreport .= "<td>".$columnas[$value][$keyvalor]."</td>";
			}//end for
	        
	        
	        //MOSTRAR AHORA LOS VALORES
	        foreach( $valor as $mesventa => $numerovendido )
	        {
	        	$strtxt .= $numerovendido."\t";
				$strreport .= "<td>".number_format( $numerovendido,0,'.',',')."</td>";
				
				$arrayTOT[$mesventa] += $numerovendido;
	        	$arrayParam[$keyvalor][$mesventa] = $numerovendido;
				
	        }//end for
	        
	         $strreport .= "</tr>";
	     
	         $strtxt .= "\r"; // TXT
	     
	        // $col++;
	    
	    } // END FOR

//MOSTRAR TOTAL
    
     $strreport .= "<tr class=";
   
   	 $strtxt .= "Total :\t"; // TXT
   	 

	       $strreport .= repetition()?'row0>':'row1>';
	       $strreport .= "><th>Total :</th>";
	       
	        for( $i = 0; $i < $numerocols - 1; $i++ )
			{
				$strtxt .= "\t";
				$strreport .= "<th></th>";
			}//end for
	        
	        //MOSTRAR AHORA LOS VALORES TOTALES
	        foreach( $total as $mesventa => $tmes )
	        {
	        	$strtxt .= $tmes."\t";
				$strreport .= "<th>".number_format( $tmes,0,'.',',')."</th>";
				
	        }//end for
	        
	         $strreport .= "</tr>";
	     
	         $strtxt .= "\r"; // TXT
	     
	        // $col++;
	    
    
     $strreport .= "</tr>";
     
$strreport .= "</table>";


$arreglo_aux = Array(); 
foreach( $arrayParam as $keymaster => $value ) 
   foreach( $value as $key => $elemento  ) 
       $arreglo_aux[$key][$keymaster] = $elemento; 
       
  
    $col =1;
		for($i=0; $i<= count($arreglo_aux);$i++){
			$str_param = '';
			if(!empty($arreglo_aux[$i])){
				
				foreach($arreglo_aux[$i] AS $key => $value )
					$str_param[] = "$key-$value";
				
				$strreport .= "<input type=hidden name=DATA$col value=\"".implode(",",$str_param)."\">";

				$col++;
			} //end if	
		}
	
	$strreport .= "<script>document.formdata.TituloGraphini.value='$TituloGraphini';</script>";
	
	return array("numregs"=>count( $array ),"strreport"=>$strreport,"strtxt"=>$strtxt);
} // End function


/*
function view_data_report($title,$sql="",$exporxls=""){

Global $viewgraph;


$now_date = date('m-d-Y H:i');

$result = $dbo->query($sql);

$file_type = "vnd.ms-excel";
$file_ending = "xls";

$col =1;


$TituloGraphini = "";

$numregs = db_num_rows($result);

if($numregs == 0){
	echo "<br><span class=titulo>La busqueda no arrojo resultados</span>";
	
	return 0;
}

echo "<table class=adminlist><tr>";

for ($i = 0; $i < db_num_fields($result); $i++) {
		$colname = db_field_name($result,$i);
	
		if(substr($colname,0,4) == "TOT_" || substr($colname,0,4) == "CNT_"){
			$titcol = str_replace("_"," ",substr($colname,4,strlen($colname)));
			
			if(empty($TituloGraphini))
				$TituloGraphini = $titcol;
		
			 if($viewgraph)
		 		  $titcol.= "&nbsp;&nbsp;<a href=\"#\" onclick=\"setcolreport('$titcol','$col')\"><img src='iconos/stats.gif' border='0'></a>";
			
			$colname = $titcol;
			$col++;
		}else
			$colname = str_replace("_"," ",$colname);
			
		echo "<th>$colname</th>";

} // END FOR

echo "</tr>";

$numcols = db_num_fields($result);
$col = 0;
$arrayTOT = array();
$arrayParam = array();

    while($row = db_fetch_row($result)){
       echo "<tr class=";
       echo repetition()?'row0>':'row1>';
       
        for($j=0; $j < $numcols; $j++){
        
        	if($j==0)
        		$col = substr($row[$j],0,10);
        
        		$coltype = mysql_field_type($result,$j);
        		$columnamesub = substr(db_field_name($result,$j),0,4);
        
        
        	if($coltype == "real" || $coltype == "int"){
        		echo "<td align=right>";

        			if($columnamesub == "CNT_") // Si es solo count sin decimales
        				print_num($row[$j],"",0);
        			else
        				print_num($row[$j],"");
        				
        		echo "</td>";
        	}
        	else
        		echo "<td>".$row[$j]."</td>";
        
        	if($columnamesub == "TOT_" || $columnamesub == "CNT_"){
        		$arrayTOT[$j] += $row[$j];
        		$arrayParam[$col][$j] = $row[$j];
        	}
        	else
        		$arrayTOT[$j] = "STR";
        	
        } // END FOR
         echo "</tr>";
         
        // $col++;
    
    } // END WHILE
    
     echo "<tr class=total><td>Total :</td>";
   
	   for($i=0;$i< $numcols;$i++){
	   
	   		if(!empty($arrayTOT[$i]) && $arrayTOT[$i] <> "STR") {
	  	 		echo "<td align=right>";
			 	print_num($arrayTOT[$i],"");
			   	echo "</td>";
			}elseif($arrayTOT[$i] == "STR" AND $i>0)
				echo "<td></td>";
				
	   	} // END for
    
     echo "</tr>";
     
echo "</table>";


$arreglo_aux = Array(); 
foreach( $arrayParam as $keymaster => $value ) 
   foreach( $value as $key => $elemento  ) 
       $arreglo_aux[$key][$keymaster] = $elemento; 
       
  
    $col =1;
		for($i=0; $i<= count($arreglo_aux);$i++){
			$str_param = '';
			if(!empty($arreglo_aux[$i])){
				
				foreach($arreglo_aux[$i] AS $key => $value )
					$str_param[] = "$key-$value";
				
				echo "<input type=hidden name=DATA$col value=\"".implode(",",$str_param)."\">";

				$col++;
			} //end if	
		}
		
	echo "<script>document.formdata.TituloGraphini.value='$TituloGraphini';</script>";
	
	return $numregs;
} // End function
********/

function yScaleCallback($aVal) { 
return number_format($aVal); 
}

/*
function formpopup($table,$field,$order,$name,$value,$style) {

$popup = "<select name=\"$name\" class=\"$style\">";
$popup .= "<option value=\"\">[ Seleccione ]</option>";

$qry = $dbo->query(" SELECT * FROM $table GROUP BY $field  ORDER BY $order ");

while ($r = db_fetch_object($qry) ) {
$popup .= "<option value=".$r->$name;

$popup .= (($r->$name==$value) ? " selected" : "");

$popup .=  " >".$r->$field."</option>";

} // End while

$popup .= "</select>";

return $popup;

} // End function


function formpopuparray($options,$selection,$name,$style) {

$checkgroup = "<select name='$name' class=$style><option value=''>[Seleccione]</option>";

while(list($key,$val) = each($options)) {
	$checkgroup .= "<option value=\"".$key."\"";
	if (!empty($selection) && $selection == $key) 
	$checkgroup .= " selected";
	
	$checkgroup .= "> ".$val."</option>";
} // end while

$checkgroup .= "</select>";
return $checkgroup;
}
*/

function formatnum($value){
Global $numdec;

return number_format($value,$numdec,'.',',');


}
  


function group_datapie($p){

Global $maxdata;

$data = array();
$param = explode(",",$p);


$numrows = 0;
foreach($param AS $valueparam){
	list($label,$value) = explode("-",$valueparam);	

	if(!empty($label) && !empty($value))
		$data[$label] = $value;
}

arsort($data);

$totalotros =  0;

	if(count($data) > $maxdata){
		foreach($data AS $value){		
				if($cont >= $maxdata)	
					$totalotros +=$value;
			$cont++;	
		}
	}

if(count($data) > $maxdata){
	//array_splice($data,$maxdata);
	$data = array_slice_key($data,0,$maxdata);

	$data["OTROS"] = $totalotros;
}

return $data;

}

function array_slice_key($array, $offset, $len=-1){ 

   if (!is_array($array)) 
       return FALSE; 

   $length = $len >= 0? $len: count($array);
   $keys = array_slice(array_keys($array), $offset, $length);
   foreach($keys as $key) {
       $return[$key] = $array[$key];
   }
  
   return $return; 
}

function header_download($file,$filename){
 // BEGIN extra headers to resolve IE caching bug (JRP 9 Feb 2003)
 // [http://bugs.php.net/bug.php?id=16173]
 header("Pragma: ");
 header("Cache-Control: ");
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
 
 header("Content-Length: ".filesize($file)); 
 header("Content-Type: application/vnd.ms-excel");
 header("Content-Disposition: attachment; filename={$filename}"); 

 readfile($file);

}


function export2xlsSQL($ReportName,$User,$sql=""){

//define date for title: EDIT this to create the time-format you need
$now_date = date('m-d-Y H:i');

$result = $dbo->query($sql);

$filename = $ReportName.$User.".xls";

$title = "Datos $ReportName Fecha $now_date \r Generados por : $User\r";


 header("Pragma: ");
 header("Cache-Control: ");
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Content-Type: application/vnd.ms-excel");

header("Content-Disposition: attachment; filename=$filename");


echo("$title\n");

//define separator (defines columns in excel & tabs in word)
$sep = "\t"; //tabbed character

//start of printing column names as names of MySQL fields
for ($i = 0; $i < db_num_fields($result); $i++) {
echo mysql_field_name($result,$i) . "\t";
}
print("\n");
//end of printing column names

//start while loop to get data

    while($row = db_fetch_row($result))
    {
        //set_time_limit(60); // HaRa
        $schema_insert = "";
        for($j=0; $j < db_num_fields($result);$j++)
        {
            if(!isset($row[$j]))
                $schema_insert .= "NULL".$sep;
            elseif ($row[$j] != "")
                $schema_insert .= "$row[$j]".$sep;
            else
                $schema_insert .= "".$sep;
        }
        $schema_insert = str_replace($sep."$", "", $schema_insert);
		//this corrects output in excel when table fields contain \n or \r
		//these two characters are now replaced with a space
		$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
        $schema_insert .= "\t";
        print(trim($schema_insert));
        print "\n";
    }

} // End function

}
?>