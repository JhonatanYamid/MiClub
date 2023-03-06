#!/usr/bin/php 

<?php

// 

	error_reporting(0);
	
//	include("sqlitepdo.class.php");
//	include("json_decode.php");
//	include("consBDEditor.php");
	
	$dbTotal = 1;
	$DB_URL = "http://www.simcolombia.com/Ipad/WSEE/";
	
	$URL_WS_ART = "http://www.elespectador.com/articulo_ipad";
	
	/************************* INCIO LEER  WS SECCIONES ***********************/

	$URL_EDICION = "http://www.elespectador.com/secciones_ipad";
	
//	$URL_EDICION = "http://static.elespectador.com/ipad/actual_05042011_secciones_ipad.html";
	
	$dataSecciones = get_data_ws($_GET,$URL_EDICION,''); // seccion_ipad?IdSec=101
	
	$dataSecciones = json_decode2($dataSecciones);
	
//dataSecciones['Secciones'][] = array('IdSec'=>'87','Seccion'=>'Principal');
	$temp = array_reverse($dataSecciones['Secciones']);
	$temp[] = array('IdSec'=>'87','Seccion'=>'Principal');
	$dataSecciones['Secciones'] = array_reverse($temp);
	
	if(empty($dataSecciones['Fecha']))
		exit;

	/************************* FIN LEER  WS SECCIONES ***********************/

	$meses =array("01"=>"Enero",
					"02"=>"Febrero",
					"03"=>"Marzo",
					"04"=>"Abril",
					"05"=>"Mayo",
					"06"=>"Junio",
					"07"=>"Julio",
					"08"=>"Agosto",
					"09"=>"Septiembre",
					"10"=>"Octubre",
					"11"=>"Noviembre",
					"12"=>"Diciembre");

	$arraydia =array("1"=>"LUNES",
					"2"=>"MARTES",
					"3"=>"MIERCOLES",
					"4"=>"JUEVES",
					"5"=>"VIERNES",
					"6"=>"SABADO",
					"7"=>"DOMINGO");

	
//print_r($dataSecciones);

	$dia = substr($dataSecciones['Fecha'],8,2);
	$mes = substr($dataSecciones['Fecha'],5,2);
	$ano = substr($dataSecciones['Fecha'],0,4);

	function weekday($fecha){
		$fecha=str_replace("/","-",$fecha);
		list($dia,$mes,$ano)=explode("-",$fecha);
		return (((mktime ( 0, 0, 0, $mes, $dia, $ano) - mktime ( 0, 0, 0, 7, 17, 2006))/(60*60*24))+700000) % 7;
	}

//	echo "DIA";
	$strDia = $arraydia[weekday(date("$dia/$mes/$ano"))];

	$txtFecha = "$dia de ".$meses[$mes]." $ano";

	$dbh = mysql_connect("localhost","Ediciones_ipad_EE", "a81nhayg2675"); //"Ediciones_ipad_EE", "a81nhayg2675");
	
	mysql_select_db("Ediciones_ipad_EE");
	
	$qry_del = "DELETE FROM EdicionesEditor where DATE_ADD(Fecha_Edicion, INTERVAL 1 DAY) < now() ";
	
	mysql_query($qry_del,$dbh);
	
//	$condicion = " ";

//	if($dbTotal) // Impresa
//		$condicion .= " AND Impresa = 1 ";
//	else
//		$condicion .= " AND Impresa = 0 ";
		
	$qry_db = "";
	$qry 	= "SELECT * From EdicionesEditor WHERE TxtFecha = '$txtFecha'  ";
	
	$qid = mysql_query($qry,$dbh);
	

	if(mysql_num_rows($qid) == 0){

			$bdName = crear_bd($dataSecciones,$dbTotal);

			$bdName = $DB_URL.$bdName;
		
			$qry_db = "INSERT INTO EdicionesEditor 
							VALUES('',NOW(),'$dataSecciones[Fecha]','$dataSecciones[UrlPortada]','$txtFecha','$bdName','0','$strDia');";
					
	}
	else{ // Existe Edicion
		
			$result = mysql_fetch_object($qid);
			
			// Edicion Actual y Fue Actualizada
			
			// $result->Impresa == '0' &&
			
			if( $result->Fecha_Edicion != $dataSecciones['Fecha']){
			
					$bdName = crear_bd($dataSecciones,$dbTotal);

					$bdName = $DB_URL.$bdName;
		
					$qry_db = "UPDATE EdicionesEditor SET 
										Fecha_Generacion = NOW(),
										Fecha_Edicion = '$dataSecciones[Fecha]',
										Archivo = '$bdName',
										Dia = '$strDia',
										Impresa=0,
										Portada = '$dataSecciones[UrlPortada]'	
									WHERE IDEdicion = '$result->IDEdicion'
									";
			
			}
	
	}
	
	$qry_db;
	
		if(!empty($qry_db))
			mysql_query($qry_db,$dbh);


function crear_bd($dataSecciones,$dbTotal){


	/************************* CREAR BD ***********************/

	//$hora = str_replace(":","",substr($dataSecciones['Fecha'],11,8));
	
	$bdName = "EE2011_03_17EDITOR"; //"EE".str_replace("-","_",substr($dataSecciones['Fecha'],0,10))."_".$hora;

	if(!$dbTotal)
		$bdName = "ACTUAL".$bdName;

	#create new sqlite database object($dblink)
//	$dblink = new sqlitePDO("$bdName", "sqlite");	
	$dblink = new sqlitePDO("/var/www/vhosts/simcolombia.com/httpdocs/Ipad/WSEE/"."$bdName", "sqlite");	
	
	$bdName .= '.sqlite';
	
	#connect to sqlite3 database
	$dblink->connect();
	
	#query sqlite3 database
	
	$tablas = array();
	
	$tablas['Edicion'] = "CREATE TABLE Edicion (
											Fecha TEXT PRIMARY KEY,
											URLPortada TEXT DEFAULT '' )";
											
	$tablas['Secciones'] = "CREATE TABLE Secciones (
											ID INTEGER PRIMARY KEY autoincrement,
											IdSec INTEGER,
											Seccion TEXT )";
											
	$tablas['Articulo'] = "CREATE TABLE Articulo (
											Nid INTEGER PRIMARY KEY,
											IdSec integer DEFAULT '',
											IdSec2 integer DEFAULT '',
											Antetitulo TEXT DEFAULT '',
											Titulo TEXT DEFAULT '',
											Lead TEXT DEFAULT '',
											Cuerpo TEXT DEFAULT '',
											URLImagen TEXT DEFAULT '',
											URLAudio TEXT DEFAULT '',
											URLVideo TEXT DEFAULT '',
											Columnista TEXT DEFAULT '',
											FotoColumnista TEXT DEFAULT '',
											FotoColumnistaVer  TEXT DEFAULT '',
											Balcon CHAR(1) DEFAULT '',
											Url TEXT DEFAULT '',
											UrlImgHor TEXT DEFAULT '',
											UrlImgVer TEXT DEFAULT '',
											CreditoImg TEXT DEFAULT '',
											TituloImg TEXT DEFAULT '',
											Autor TEXT DEFAULT '',
											ImgAudio TEXT DEFAULT '',
											Sumario TEXT DEFAULT '',
											Editorial CHAR(1) DEFAULT ''
											)";

$tablas['Balcones'] = "CREATE TABLE Balcones (
											ID INTEGER PRIMARY KEY autoincrement,
											IdSec INTEGER,
											IdSec2 INTEGER,
											Titulo TEXT DEFAULT '',
											Cuerpo TEXT DEFAULT '',
											URLImagen TEXT DEFAULT '',
											Nid CHAR(12)  DEFAULT '')";

$tablas['Caricaturas'] = "CREATE TABLE Caricaturas (
											IdImg INTEGER,
											Titulo TEXT DEFAULT '',
											URLImagen TEXT DEFAULT '')";

$tablas['Delabios'] = "CREATE TABLE Delabios (
											Nid INTEGER  PRIMARY KEY,
											IdSec INTEGER,
											Sumario TEXT DEFAULT '',
											Cuerpo  TEXT DEFAULT '',
											URLImagen TEXT DEFAULT ''
											)";


$tablas['Destacado'] = "CREATE TABLE Destacado (
											Nid INTEGER,
											IdSec INTEGER,
											IdSec2 INTEGER,
											URLImagen TEXT DEFAULT '',
											CreditoImg TEXT DEFAULT '',
											TituloImg TEXT DEFAULT '',
											PRIMARY KEY(Nid,IdSec,IdSec2)
											)";
/* 
										,
											Antetitulo TEXT DEFAULT '',
											Titulo TEXT DEFAULT '',
											Lead TEXT DEFAULT '',
											URLImagen TEXT DEFAULT '',
											Audio TEXT DEFAULT '',
											Video TEXT DEFAULT '',
											IdSec2 integer,
											
*/		


$tablas['MasHome'] = "CREATE TABLE MasHome (
											ID INTEGER PRIMARY KEY autoincrement,
											Nid INTEGER,
											IdSec INTEGER,
											IdSec2 INTEGER,
											URLImagen TEXT DEFAULT ''
											)";
											
/*
										,
											Antetitulo TEXT DEFAULT '',
											Titulo TEXT DEFAULT '',
											Lead TEXT DEFAULT '',
											URLImagen TEXT DEFAULT '',
											URLAudio TEXT DEFAULT '',
											URLVideo TEXT DEFAULT '',
											IdSec2 integer,
											Edicion integer,
											Cuerpo TEXT DEFAULT ''



*/
											
$tablas['NotasPasan'] = "CREATE TABLE NotasPasan (
											ID INTEGER PRIMARY KEY autoincrement,
											Nid INTEGER,
											IdSec integer,
											URLImagen TEXT DEFAULT '',
											IdSec2 integer);";

/**
											Antetitulo TEXT DEFAULT '',
											Titulo TEXT DEFAULT '',
											Lead TEXT DEFAULT '',
											URLImagen TEXT DEFAULT '',
											UrlAudio TEXT DEFAULT '',
											UrlVideo TEXT DEFAULT '',
											Cuerpo TEXT DEFAULT ''
*/

$tablas['Cartas'] = "CREATE TABLE Cartas (
											ID INTEGER PRIMARY KEY autoincrement,
											Nid INTEGER,
											IdSec INTEGER)";

$tablas['Galeria'] = "CREATE TABLE Galeria (
											Nid INTEGER,
											IdImg INTEGER,
											Titulo TEXT DEFAULT '',
											Credito  TEXT DEFAULT '',
											URLImagen TEXT DEFAULT '');";
														 

$tablas['Monedas'] = "CREATE TABLE Monedas (
											ID INTEGER PRIMARY KEY autoincrement,
											Nombre TEXT DEFAULT '',
											Valor TEXT DEFAULT '',
											Cambio TEXT DEFAULT '',
											Porcentaje TEXT DEFAULT '',
											Compra TEXT DEFAULT '',
											Venta TEXT)";
											
$tablas['Acciones'] = "CREATE TABLE Acciones (
											ID INTEGER PRIMARY KEY autoincrement,
											Nombre TEXT DEFAULT '',
											Valor TEXT DEFAULT '',
											Cambio TEXT DEFAULT '',
											Porcentaje TEXT DEFAULT '',
											MasValorizada INTEGER,
											MenosValorizada INTEGER)";
														 
$tablas['RentaFija'] = "CREATE TABLE RentaFija (
											ID INTEGER PRIMARY KEY autoincrement,
											Nombre TEXT DEFAULT '',
											Valor TEXT DEFAULT '',
											Tasa TEXT DEFAULT '',
											Monto TEXT DEFAULT '')";
														 
														 
$tablas['OtrosIndicadores'] = "CREATE TABLE OtrosIndicadores (
											ID INTEGER PRIMARY KEY autoincrement,
											Nombre TEXT DEFAULT '',
											Valor TEXT DEFAULT '',
											Cambio TEXT DEFAULT '',
											Porcentaje TEXT)";
																									 										 
														 
	foreach($tablas AS $key => $value){
	
			$qry = "DROP TABLE IF EXISTS $key";
		
			$dblink->query($qry);
			
			$dblink->query($value);		
	}


/************************* INCIO INSERT  WS SECCIONES ***********************/

	if(!empty($dataSecciones['UrlPortada']) && !empty($dataSecciones['Fecha'])){
			$qry = "INSERT OR IGNORE INTO Edicion VALUES('$dataSecciones[Fecha]','$dataSecciones[UrlPortada]')";
			$dblink->query($qry);
	}
	
	//print_r($dataSecciones['Secciones'] );

	foreach($dataSecciones['Secciones'] AS $value ){
	//	print_r('secciones');	
		
			$IdSec = $value['IdSec'];
	
			$qry = "INSERT OR IGNORE INTO Secciones (IdSec, Seccion) VALUES('$value[IdSec]','$value[Seccion]')";	
			
			$dblink->query($qry);
		
			 $URL_WS_SECC = "http://www.elespectador.com/seccion_ipad?IdSec=$IdSec";
			
			$dataSecc = get_data_ws($_GET,$URL_WS_SECC,''); //seccion_ipad?IdSec=101
		
			$dataSecc = json_decode2($dataSecc);
			
			//if($IdSec == 87)	
			//print_r($IdSec);	
			
				if(!empty($dataSecc['Seccion'][0]['Destacado'])){
					
					print_r('destacado');
					foreach($dataSecc['Seccion'][0]['Destacado']['Articulo'] AS $Articulo){

						
						if(!empty($Articulo['Nid'])){
																		
							if(empty($Articulo['IDSec']))
								$Articulo['IDSec'] = $IdSec;
							
							 $qry = "INSERT  OR IGNORE INTO Destacado VALUES('$Articulo[Nid]','$IdSec','$Articulo[IDSec]','$Articulo[URLImagen]','$Articulo[CreditoImg]','$Articulo[TituloImg]')";
							print_r($qry);
							$dblink->query($qry);
			
							insert_articulo($dblink,$dbTotal,$Articulo['Nid'],$IdSec,$Articulo,'0','0');
						
						} // end if
					} // end foreach
				} // end if Destacado
			
		

				if(!empty($dataSecc['Seccion'][0]['Balcones'])){
			
//print_r('balcones');

					foreach($dataSecc['Seccion'][0]['Balcones']['Articulo'] AS $Articulo){
							

						if(!empty($Articulo['Cuerpo'])){
													
							$Articulo = array_map('str_replace_char',$Articulo);
											
							if(empty($Articulo['IDSec']))
								$Articulo['IDSec'] = $IdSec;
							
							$esBalcon = 1;

							if($IdSec == 87){
								$esBalcon = 0;
print_r('IdSec'.$IdSec.'esbalcon'.$esBalcon);
							}
								
		
							$qry = "INSERT OR IGNORE INTO Balcones (IdSec,IdSec2,Titulo,Cuerpo,URLImagen,Nid)
 VALUES('$IdSec','$Articulo[IDSec]','$Articulo[Titulo]','$Articulo[Cuerpo]','$Articulo[URLImagen]','$Articulo[Nid]')";
					


							$dblink->query($qry);
							
							
							insert_articulo($dblink,$dbTotal,$Articulo['Nid'],$IdSec,$Articulo,'0',$esBalcon);
												
						} // end if
					} // end foreach
					
				} // end if balcones
				

		
				if(!empty($dataSecc['Seccion'][0]['MasHome'])){
					print_r('mashome');
					foreach($dataSecc['Seccion'][0]['MasHome']['Articulo'] AS $Articulo){
						
						if(!empty($Articulo['Nid'])){
						
							if(empty($Articulo['IDSec']))
								$Articulo['IDSec'] = $IdSec;
						
					
				
							$qry = "INSERT OR IGNORE INTO  MasHome (Nid,IdSec,IdSec2,URLImagen ) VALUES('$Articulo[Nid]','$IdSec','$Articulo[IDSec]','$Articulo[URLImagen]')";
						//if($IdSec == 87)
						//	echo $qry;

							$dblink->query($qry);
							
							insert_articulo($dblink,$dbTotal,$Articulo['Nid'],$IdSec,$Articulo,'0','0');
							
						} // end if
					} // end foreach
					
				} // end if MasHome
			
		//	echo $IdSec;	
			

//if($IdSec == 101)	
				
				
				if(!empty($dataSecc['Seccion'][0]['NotasPasan'])){
					print_r('notaspasan');
					
					foreach($dataSecc['Seccion'][0]['NotasPasan']['Articulo'] AS $Articulo){
						
						if(!empty($Articulo['Nid'])){
						
							if(empty($Articulo['IDSec']))
								$Articulo['IDSec'] = $IdSec;
								
							$qry = "INSERT OR IGNORE INTO NotasPasan (Nid,IdSec,IdSec2,URLImagen)  VALUES('$Articulo[Nid]','$IdSec','$Articulo[IDSec]','$Articulo[URLImagen]')";
									
							$dblink->query($qry);
								 
							insert_articulo($dblink,$dbTotal,$Articulo['Nid'],$IdSec,$Articulo,'0','0');
	
						} // end if
					} // end foreach
				} // end if NotasPasan
						
					
						
				if(!empty($dataSecc['Seccion'][0]['Editorial'])){
					print_r('editorial');
				
					foreach($dataSecc['Seccion'][0]['Editorial']['Articulo'] AS $Articulo){
						
						if(!empty($Articulo['Nid'])){
					
							if(empty($Articulo['IDSec']))
								$Articulo['IDSec'] = $IdSec;
							 $qry = "INSERT  OR IGNORE INTO Destacado VALUES('$Articulo[Nid]','$IdSec','101','$Articulo[URLImagen]','$Articulo[CreditoImg]','$Articulo[TituloImg]')";
							
					
							$dblink->query($qry);
								 
							insert_articulo($dblink,$dbTotal,$Articulo['Nid'],$IdSec,$Articulo,'1','0');
	
						} // end if
					} // end foreach
					
				
				} // end if Editorial
				
				
				
			
				if(!empty($dataSecc['Seccion'][0]['Delabios'])){
					print_r('delabios  ');
				//	print_r($dataSecc['Seccion'][0]['Delabios']['Articulo']);				
				
					$cont = 1;
					foreach($dataSecc['Seccion'][0]['Delabios']['Articulo'] AS $Articulo){
						
						if(!empty($Articulo['Cuerpo'])){
							
							
							$arrayArt = array_map('str_replace_char',$Articulo);

							$qry = "INSERT OR IGNORE INTO Delabios VALUES($cont,'101','$arrayArt[Sumario]','$arrayArt[Cuerpo]','$arrayArt[URLImagen]')";
				
							$dblink->query($qry);
								 
						//	insert_articulo($dblink,$dbTotal,$cont,$IdSec,$Articulo,'0');
	
						} // end if
						
						$cont++;
					} // end foreach
				} // end if Editorial
			
			
				if(!empty($dataSecc['Seccion'][0]['Cartas'])){
					print_r('cartas  ');
					foreach($dataSecc['Seccion'][0]['Cartas']['Articulo'] AS $Articulo){
						
						if(!empty($Articulo['Nid'])){
						
							$Nid = $Articulo['Nid'];
							
							$qry = "INSERT OR IGNORE INTO Cartas (Nid,IdSec) VALUES('$Nid','$IdSec')";
					
							$dblink->query($qry);
							
							if(empty($Articulo['IDSec']))
								$Articulo['IDSec'] = $IdSec;
								 
							insert_articulo($dblink,$dbTotal,$Nid,$IdSec,$Articulo,'0','0');
	
						} // end if
					} // end foreach
				} // end if Editorial
			
			
				if(!empty($dataSecc['Seccion'][0]['Caricaturas'])){
					print_r('caricatura  ');			
					$IdImg = 1;
					
					foreach($dataSecc['Seccion'][0]['Caricaturas']['Articulo'] AS $Articulo){
						
						if(!empty($Articulo['Titulo'])){
							
							$arrayArt = array_map('str_replace_char',$Articulo);
							
							$qry = "INSERT OR IGNORE INTO Caricaturas VALUES('$IdImg','$arrayArt[Titulo]','$arrayArt[URLImagen]')";
					
							$dblink->query($qry);
								 
							$IdImg++;
					
						} // end if
					} // end foreach
				} // end if Destacado
			
	} // end for secciones

/****************************** ALMACENAR INDICADORES ******************************/

$URL_WS = "http://www.elespectador.com/indicadores_ipad";
						//$URL_WS = "http://eed6.elespectador.com/indicadores_ipad";
					//	$URL_WS = "http://static.elespectador.com.s3.amazonaws.com/ipad/".date("dmY")."_indicadores_ipad.html";
				$dataInd = get_data_ws($_GET,$URL_WS,''); //seccion_ipad?IdSec=101
						
				$dataInd = json_decode2($dataInd);
				
			//	print_r($dataInd);
				
				if(is_array($dataInd['Dolar'])){
//print_r('dolar ');	
						 	$qry = "INSERT OR IGNORE INTO Monedas (Nombre,Valor,Cambio,Porcentaje,Compra,Venta) VALUES('Dolar','". $dataInd['Dolar']['TRMValor'] ."','"
													. $dataInd['Dolar']['TRMCambio'] ."','". $dataInd['Dolar']['TRMPorcentaje'] ."','"
													. $dataInd['Dolar']['Compra'] ."','". $dataInd['Dolar']['Venta'] ."')";
							$dblink->query($qry);
				}
				
				if(is_array($dataInd['Euro'])){
//print_r('euro ');
							$qry = "INSERT OR IGNORE INTO Monedas (Nombre,Valor,Cambio,Porcentaje,Compra,Venta) VALUES('Euro','". $dataInd['Euro']['Valor'] ."','"
													. $dataInd['Euro']['Cambio'] ."','". $dataInd['Euro']['Porcentaje'] ."','','')";
							$dblink->query($qry);
				}
				
				if(is_array($dataInd['Acciones'])){
//print_r('acciones ');
					foreach($dataInd['Acciones'] AS $Accion){
												
							$qry = "INSERT OR IGNORE INTO Acciones  (Nombre,Valor,Cambio,Porcentaje,MasValorizada,MenosValorizada) VALUES('$Accion[Nombre]','$Accion[Valor]','$Accion[Cambio]','$Accion[Porcentaje]','','')";
				
							$dblink->query($qry);
					}
				}
				
				if(is_array($dataInd['MasValorizada'])){
//print_r('mas ');
							$qry = "INSERT OR IGNORE INTO Acciones (Nombre,Valor,Cambio,Porcentaje,MasValorizada,MenosValorizada) VALUES('". $dataInd['MasValorizada']['Nombre'] ."','". $dataInd['MasValorizada']['Valor'] ."','"
															. $dataInd['MasValorizada']['Cambio'] ."','". $dataInd['MasValorizada']['Porcentaje'] ."','1','')";
															//print_r($qry);
							$dblink->query($qry);
				}						
				
				if(is_array($dataInd['MenosValorizada'])){
//print_r('menos ');					
							$qry = "INSERT OR IGNORE INTO Acciones (Nombre,Valor,Cambio,Porcentaje,MasValorizada,MenosValorizada) VALUES('". $dataInd['MenosValorizada']['Nombre'] ."','". $dataInd['MenosValorizada']['Valor'] ."','"
															. $dataInd['MenosValorizada']['Cambio'] ."','". $dataInd['MenosValorizada']['Porcentaje'] ."','','1')";
							$dblink->query($qry);
				}
				
				if(is_array($dataInd['RentaFija'])){
//print_r('renta ');						
							$qry = "INSERT OR IGNORE INTO RentaFija (Nombre,Valor,Tasa,Monto) VALUES('DTF','". $dataInd['RentaFija']['DTF'] ."','','')";
							$dblink->query($qry);
							
								$qry = "INSERT OR IGNORE INTO RentaFija (Nombre,Valor,Tasa,Monto) VALUES('TES','". $dataInd['RentaFija']['Tes_Valor'] ."','"
																	. $dataInd['RentaFija']['Tes_tasa'] ."','". $dataInd['RentaFija']['Tes_monto'] ."')";
								$dblink->query($qry);
				}
				
				if(is_array($dataInd['Otros'])){
//print_r('otros ');
					foreach($dataInd['Otros'] AS $Accion){
												
							$qry = "INSERT OR IGNORE INTO OtrosIndicadores (Nombre,Valor,Cambio,Porcentaje) VALUES('$Accion[Nombre]','$Accion[Valor]','$Accion[Cambio]','$Accion[Porcentaje]')";
				
							$dblink->query($qry);
					}
				}
				
				if(!empty($dataInd['Uvr'])){
//print_r('uvr');
							$qry = "INSERT OR IGNORE INTO OtrosIndicadores (Nombre,Valor,Cambio,Porcentaje) VALUES('UVR','$dataInd[Uvr]','','')";
							$dblink->query($qry);				
				}

return $bdName;

}


function get_data_art($Nid){

	Global $URL_WS_ART;

		$dataArt = array();

		$dataArt = get_data_ws($_GET,$URL_WS_ART."?Nid=$Nid","Nid=$Nid"); //seccion_ipad?IdSec=101
						
		return json_decode2($dataArt);

}


function insert_articulo($dblink,$dbTotal,$Nid,$IdSec,$Articulo,$Editorial,$Balcon){

			if($dbTotal){	

				if(empty($Nid))
					return 0;

				$dataArt = get_data_art($Nid);

			//	echo $Nid;
			//	print_r($dataArt);					

				if($dataArt['Articulo'][0]['Nid'] != ''){
					$arrayArticulo = $dataArt[Articulo][0];


				//	if($Nid == "243719" || $Nid == "243721")
				//		print_r($arrayArticulo);

					//	$Articulo[Cuerpo] =  
					//if($Nid == "243719" || $Nid == "243721")
					//	echo str_ireplace("'","",$Articulo[Cuerpo][0]); // $Articulo[Cuerpo][0];

					//	$Cuerpo = str_ireplace("'"," ** ",$arrayArticulo[Cuerpo][0]); // $arrayArticulo[Cuerpo][0];

					if(is_array($arrayArticulo[Cuerpo])){
					//	echo "ES ARRAY";
						$Cuerpo = $arrayArticulo[Cuerpo][0];

					}
					else
						$Cuerpo = $arrayArticulo[Cuerpo];

					$arrayArticulo[Cuerpo] = $Cuerpo;

					//if($Nid == "243719" || $Nid == "243721"){
						//echo $Cuerpo;
					//	print_r($Articulo);
				//	}


				}

					//	exit;

			}

			$Articulo = array_map('str_replace_char',$arrayArticulo);

		//	if($Nid == "243695")
		//				print_r($Articulo);

		if(empty($Articulo[IDSec]))
			$Articulo[IDSec] = $IdSec;

		if($IdSec==87){	
			$IdSec = $Articulo[IDSec];
			//$Balcon = 0;
		}
		if(!empty($Articulo[Nid])){
			$qry = "INSERT OR IGNORE  INTO Articulo VALUES('".$Articulo[Nid]."','"
												.$IdSec."','".$Articulo[IDSec]."','".$Articulo[Antetitulo]."','"
												.$Articulo[Titulo]."','".$Articulo[Lead]."','"
												.$Articulo[Cuerpo]."','".$Articulo[URLImagen]."','"
												.$Articulo[UrlAudio]."','".$Articulo[UrlVideo]."','"
												.$Articulo[Columnista]."','".$Articulo[FotoColumnista]."','"
												.$Articulo[FotoColumnistaVer]."','$Balcon','".$Articulo[Url]."','"
												.$Articulo[UrlImgHor]."','".$Articulo[UrlImgVer]."','"
												.$Articulo[CreditoImg]."','".$Articulo[TituloImg]."','"
												.$Articulo[Autor]."','"
												.$Articulo[ImgAudio]."','".$Articulo[Sumario]."','$Editorial')";

			$dblink->query($qry);
		}

				if(is_array($Articulo['Galeria'])){

					$IdImg = 1;

					foreach($Articulo['Galeria'] AS $Img){
					//	print_r($Img);

						if(!empty($Img['UrlImgHor'])){

							$arrayGal = array_map('str_replace_char',$Img);

						 	$qry = "INSERT INTO Galeria VALUES('$Articulo[Nid]','$IdImg','$arrayGal[Titulo]','$arrayGal[Credito]','$arrayGal[UrlImgHor]')";

							$dblink->query($qry);

							$IdImg++;

						} // end if
					} // end foreach
				} // end if Destacado

	return 0;

}

function str_replace_char($str){

	return str_replace("'","",$str);

}

function get_data_ws($_GET,$URL_WS,$param){


foreach ($_GET as $key => $value) {
            if ($key != "ws") {  // ignore this particular $_GET value
                $querystring .= $key."=".$value.'&';
            }
}

$uri =  '/'.$_GET['ws'].'?'.$querystring;

$proxyTarget = $URL_WS;//.$uri.$param;

$connection = curl_init($proxyTarget);

if(!$connection)
	echo "FAIL";

$headers = array();
$headers[] = 'Connection: Keep-Alive'; 
//$headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
$user_agent = '"Mozilla/5.0"';

//curl_setopt($connection, CURLOPT_HTTPHEADER,$headers ); 

curl_setopt($connection, CURLOPT_HEADER, false);
//curl_setopt($connection, CURLOPT_USERAGENT, $user_agent);
//curl_setopt($connection, CURLOPT_ENCODING,"UTF-8");

curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);


$data = curl_exec($connection);
curl_close($connection);

return $data;

}


function json_decode2($json)
{
    $comment = false;
    $out = '$x=';
  
    for ($i=0; $i<strlen($json); $i++)
    {
        if (!$comment)
        {
            if (($json[$i] == '{') || ($json[$i] == '['))       $out .= ' array(';
            else if (($json[$i] == '}') || ($json[$i] == ']'))   $out .= ')';
            else if ($json[$i] == ':')    $out .= '=>';
            else                         $out .= $json[$i];          
        }
        else $out .= $json[$i];
        if ($json[$i] == '"' && $json[($i-1)]!="\\")    $comment = !$comment;
    }
    eval($out . ';');
    return $x;
}

class sqlitePDO {

	#instantiate variables;
	
	private $dbase;
	private $result;
	private $link;
	private $row;
	public  $ext;
	private $qry;
	
	function __construct($dbase,$ext) {
		$this->dbase = $dbase;
		$this->link	 = $link;
		$this->ext   = $ext;	
			
	}
	
	function connect() {
			$this->link = new PDO("sqlite:".$this->dbase.".".$this->ext);
			$this->link->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}
	
	#Query sqlite database results.
	function query($qry) {				
		$this->result = $this->link->query($qry);
		echo $this->result->execute;
	}

}


?>
