<?php
//exit;
//ini_set('display_errors', 1);
error_reporting(E_ERROR | E_PARSE);
//error_reporting( 0 );
date_default_timezone_set('America/Bogota');




if (!isset($_SESSION))
	session_start();

if (empty($_SESSION['Idioma'])) {
	$_SESSION['Idioma'] = 'Es';
}

define("LANGSESSION", $_SESSION['Idioma']);

define("VERSION", "4.0");


//Datos de acceso a la BD

//define( "DBHOST" , "10.187.88.45" );

//DEPLOY

define("DBHOST", "localhost");
define("DBNAME", "miclubappdev");
define("DBUSER", "miclubappdev");
define("DBPASS", "mn.vMUq2*91ncFsFYk");

define("DBHOSTMongo", "localhost:27017");
define("DBNAMEMongo", "LogsApp");
define("DBUSERMongo", "miclubapp01");
define("DBPASSMongo", "4P2231e2L3ebcg23");

//BD GameGolf
define("DBHOSTGame", "localhost");
define("DBNAMEGame", "golfgamedev");
define("DBUSERGame", "miempresapp2023");
define("DBPASSGame", "4St5Kbv-f345*ghtbvf");

/*
//Local
define( "DBHOST" , "localhost" );
define( "DBNAME" , "MiClub" );
define( "DBUSER" , "root" );
define( "DBPASS" , "root" );
*/

//Luker
define("HOST_LUKER", "190.216.205.217");
define("PORT_LUKER", "1521");
define("BASE_LUKER", "PDBCSLK");
define("USER_LUKER", "APPLUKER");
define("PASSWORD_LUKER", "#20EMP!ugs+");


//Datos de acceso a la BD SQL SERVER PRADERA
//define( "DBHOST_PRADERA" , "186.86.209.60:2210\sisclub384" );
define("DBHOST_PRADERA", "186.154.91.34:2210\sisclub384");
define("DBNAME_PRADERA", "sisclub384");
define("DBUSER_PRADERA", "nano");
define("DBPASS_PRADERA", "nano.2014");

//Datos de acceso a la BD SQL SERVER MEDELLIN
define("DBHOST_MEDELLIN", "190.0.53.38");
define("DBNAME_MEDELLIN", "COMANDA");
define("DBUSER_MEDELLIN", "miclub");
define("DBPASS_MEDELLIN", "#miclub**");

//Datos de acceso a la BD SQL SERVER PEREIRA
// define("DBHOST_PEREIRA", "201.236.240.245");
define("DBHOST_PEREIRA", "200.116.199.53");
define("DBNAME_PEREIRA", "IntegracionAppMiClub");
define("DBUSER_PEREIRA", "appmiclub");
define("DBPASS_PEREIRA", "123/appmiclub.*");
// define("API_PEREIRA", "http://201.236.240.245/APIClubSQL");
define("API_PEREIRA", "http://200.116.199.53/APIClubSQL");
define("USUARIO_API_PEREIRA", "appmiclub");
define("PASS_API_PEREIRA", "4ppM1Club.*");

//Datos de acceso ZEUS Club Colombia
define("URLZEUS_CLUBCLOLOMBIA", "http://190.144.32.3:82/wsZeusMiClub/ServiceWS.asmx?WSDL");
define("USUARIOZEUS_CLUBCOLOMBIA", "zeusclub");
define("CLAVEZEUS_CLUBCOLOMBIA", "clubes1*");

//Datos de acceso ZEUS Club Colombia
define("URLZEUS_ITALIANO", "http://190.85.109.61:804/MiClub/ServiceWS.asmx?WSDL");
define("USUARIOZEUS_ITALIANO", "zeusclub");
define("CLAVEZEUS_ITALIANO", "zeusclub");


//Datos de acceso ZEUS Chicala
define("URLZEUS_CHICALA", "http://186.96.100.194:804/wsZeusMyclub/ServiceWS.asmx?WSDL");
define("USUARIOZEUS_CHICALA", "wszeusclubes");
define("CLAVEZEUS_CHICALA", "zeusclubes");

//Datos de acceso ZEUS Arrayanes
define("URLZEUS_ARRAYANES", "http://186.31.134.46/WsZeusMiClub/ServiceWS.asmx?WSDL");
define("USUARIOZEUS_ARRAYANES", "wsclub");
define("CLAVEZEUS_ARRAYANES", "zclub2*");

//Datos WS hub-buildings
define("URLHUB", "https://dev.hub-buildings.com/guest/apihub/guestToHub");
define("IDHUB", "0920");
define("ACCESSTOKENHUB", "ESB5500691034629");
define("PASSWORDTOKENHUB", "cae017a966773b342e3ad5b889d3f9f9624d56cf");


//Catolica Directorio activo
define("HOST_CATOLICA_DA", "http://portalweb.ucatolica.edu.co/easyWeb2/admin/ws/login/login.php");
define("HOST_CATOLICA_DATOS", "http://wso2dsvs.ucatolica.edu.co:2763/services/");
define("URL_CATOLICA_FOTOS", "https://regweb.ucatolica.edu.co/ifotos/");
//Catolica  Directorio activo

//Datos Barranquilla
define("URLBQUILLA", "http://190.242.128.108/");

//Datos Uruguay
define("URL_URUGUAY", "http://200.58.148.7:8080/soap/Nodum_Prod/services/forms/v1.2/");
define("USUARIO_URUGUAY", "109apps");
define("CLAVE_URUGUAY", "109apps");


//Servidor de correo
/*
define( "HOST_SMPTP" , "smtp.sendgrid.net" );
define( "PASSWORD_SMPT" , "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" );
define( "USER_SMTP" , "apikey" );
*/

define("HOST_SMTP", "localhost");
define("PASSWORD_SMPT", "zPh8v~274242");
define("USER_SMTP", "noreplay@miclubapp.com");
define("PUERTO_SMTP", "587");

//Pruebas
//define( "ENDPOINT_CONDADO" , "http://179.49.25.110:8082/WS_APPMOVIL.Service1.svc?wsdl" );
//Produccion
define("ENDPOINT_CONDADO", "https://servicios.qtgc.com:8443/WS_APPMOVIL.Service1.svc?wsdl");
// define("ENDPOINT_PLACE_TO_PAY_TEST", "https://test.placetopay.ec/");
define("ENDPOINT_PLACE_TO_PAY_TEST", "https://checkout-test.placetopay.com");
define("ENDPOINT_PLACE_TO_PAY", "https://secure.placetopay.ec/");

// LLAVE WEBSERVICE
define("KEY_SERVICES", "CEr0CLUB");
define("KEY_TOKEN", "MiClubApp#001.Tok20");
define("KEY_API", "f5df15f7efb574e035683a31db0ff50e27945c54c7ca328423b964b7b7386464");

// WEB SERVICE PLAYA AZUL
define("URL_PLAYA_AZUL", "https://api.clubplayaazul.com/api/v1/");
define("APIKEY_PLAYA_AZUL", "4d640f00d51294b79d7dd4203851b66e28453505");

// WEB SERVICE IPLER
define("API_IPLER", "http://portal.ipler.edu.co:8080/rest-service-siip2/");

//Datos de acceso WS Villa Peru
define("API_VILLA", "http://200.60.96.203:8081/SIDIGE.Web.Service.TabDocRest.svc/api/");
define("USUARIO_API_VILLA", "usuariows");
define("PASS_API_VILLA", "Sidigews");

// PASARELA WOMPI
define("TEST_WOMPI", "https://sandbox.wompi.co/v1");
define("WOMPI", "https://production.wompi.co/v1");

// WEB SERVICE SAP BELLAVISTA
define("WS_SAP_BELLAVISTA", "http://200.188.57.58/wsSAP/wsSAP.asmx");
define("LLAVE_SAP_BELLAVISTA", "#$%SAPCGB.2021WS&%");

// WEB SERVICE CAMURI GRANDE
define("WS_CAMURIGRANDE", "https://apirestcamuri.auditoriamovil.com/");

// WEB SERVICE LUKAPAY
define("URL_LUKAPAY_API_TEST", "https://bspaycoapi-qa.payco.net.ve/api/v1/");
define("URL_LUKAPAY_API", "https://lukaapi.payco.net.ve/api/v1/");

// WEB SERVICE VALLE ARRIBA
//define("WS_VALLE_ARRIBA","http://181.225.53.83:2525/apisocios/");
define("WS_VALLE_ARRIBA", "http://nube.vagcapp.com:2525/apisocios/");

//Directorio del administrador del sitio
define("DIRROOT", dirname(__FILE__) . "/");

//Directorio de las librerias
define("LIBDIR", DIRROOT . "lib/");

//Diretorio del sistema
define("SITEDIR", dirname($_SERVER["PHP_SELF"]));
//Direccion absoluta del sitio
//define( "URLROOT" , "http://" . $_SERVER["HTTP_HOST"] . "/" . substr( SITEDIR , 0 , ( strrpos( SITEDIR , "/" ) + 1 ) ) );
//define( "URLROOT" , "https://" . $_SERVER["HTTP_HOST"]  . "/" );

//Produccion
define("URLROOT", "https://" . $_SERVER['HTTP_HOST'] . "/");
//local
//define( "URLROOT" , "http://" . $_SERVER['SERVER_NAME'] . ":8888/ClubesIBM/" );


define("URLWEB", "https://www.miclubapp.com/web/");


//Titulo de la aplicacion
define("APP_TITLE", "Administrador de Clubes");

//tiempo limite de sesion
define("SESSION_LIMIT", 60);

define("DISENO_ROOT", URLROOT . "file/diseno/");
define("DISENO_DIR", DIRROOT . "../file/diseno/");

define("BANNERAPP_ROOT", URLROOT . "file/bannerapp/");
define("BANNERAPP_DIR", DIRROOT . "../file/bannerapp/");

define("REGISTROSINFINITO_ROOT", URLROOT . "file/registrosinfinito/");
define("REGISTROSINFINITO_DIR", DIRROOT . "../file/registrosinfinito/");


define("CAMPESTREMEDELLIN_ROOT", URLROOT . "file/campestremedellin/");
define("CAMPESTREMEDELLIN_DIR", DIRROOT . "../file/campestremedellin/");

define("ENTREGAKITS_ROOT", URLROOT . "file/entregakits/");
define("ENTREGAKITS_DIR", DIRROOT . "../file/entregakits/");

define("CLIMA_ROOT", URLROOT . "file/clima/");
define("CLIMA_DIR", DIRROOT . "../file/clima/");

define("EDITARPERFIL_ROOT", URLROOT . "file/editarperfil/");
define("EDITARPERFIL_DIR", DIRROOT . "../file/editarperfil/");

//objetos prestados
define("OBJETOSPRESTADOS_ROOT", URLROOT . "file/objetosprestados/");
define("OBJETOSPRESTADOS_DIR", DIRROOT . "../file/objetosprestados/");

//agenes vanderas de paises
define("PAIS_ROOT", URLROOT . "file/pais/");
define("PAIS_DIR", DIRROOT . "../file/pais/");



define("POPPUP_ROOT", URLROOT . "file/popup/");
define("POPPUP_DIR", DIRROOT . "../file/popup/");

define("CARPOL_ROOT", URLROOT . "file/carpool/");
define("CARPOL_DIR", DIRROOT . "../file/carpool/");

define("CLIENTE_ROOT", URLROOT . "file/cliente/");
define("CLIENTE_DIR", DIRROOT . "../file/cliente/");

define("CLUB_ROOT", URLROOT . "file/club/");
define("CLUB_DIR", DIRROOT . "../file/club/");

define("EXCELPEDIDO_ROOT", URLROOT . "file/pedidosexcel/");
define("EXCELPEDIDO_DIR", DIRROOT . "../file/pedidosexcel");

define("IMGSECCION_ROOT", URLROOT . "file/seccion/");
define("IMGSECCION_DIR", DIRROOT . "../file/seccion/");

define("SOCIO_ROOT", URLROOT . "file/socio/");
define("SOCIO_DIR", DIRROOT . "../file/socio/");

define("RESTAURANTEQR_ROOT", URLROOT . "file/restauranteqr/");
define("RESTAURANTEQR_DIR", DIRROOT . "../file/restauranteqr/");

define("SOCIOPLANO_ROOT", URLROOT . "file/socioplanos/");
define("SOCIOPLANO_DIR", DIRROOT . "../file/socioplanos/");
define("SOCIOPLANO_DIR_ADMIN", DIRROOT . "../../file/socioplanos/");

define("USUARIO_ROOT", URLROOT . "file/usuario/");
define("USUARIO_DIR", DIRROOT . "../file/usuario/");

define("MODULO_ROOT", URLROOT . "file/modulo/");
define("MODULO_DIR", DIRROOT . "../file/modulo/");

define("IMGNOTICIA_ROOT", URLROOT . "file/noticia/");
define("IMGNOTICIA_DIR", DIRROOT . "../file/noticia/");

define("IMGEVENTO_ROOT", URLROOT . "file/evento/");
define("IMGEVENTO_DIR", DIRROOT . "../file/evento/");

define("DIRECTORIO_ROOT", URLROOT . "file/directorio/");
define("DIRECTORIO_DIR", DIRROOT . "../file/directorio/");

define("ELEMENTOS_ROOT", URLROOT . "file/elementos/");
define("ELEMENTOS_DIR", DIRROOT . "../file/elementos/");

define("GALERIA_ROOT", URLROOT . "file/galeria/");
define("GALERIA_DIR", DIRROOT . "../file/galeria/");

define("DOCUMENTO_ROOT", URLROOT . "file/documento/");
define("DOCUMENTO_DIR", DIRROOT . "../file/documento/");

define("OFERTA_ROOT", URLROOT . "file/oferta/");
define("OFERTA_DIR", DIRROOT . "../file/oferta/");

define("PQR_ROOT", URLROOT . "file/pqr/");
define("PQR_DIR", DIRROOT . "../file/pqr/");

define("LABORAL_ROOT", URLROOT . "file/laboral/");
define("LABORAL_DIR", DIRROOT . "../file/laboral/");

define("SERVICIO_ROOT", URLROOT . "file/servicio/");
define("SERVICIO_DIR", DIRROOT . "../file/servicio/");

define("IMGINVITADO_ROOT", URLROOT . "file/invitado/");
define("IMGINVITADO_DIR", DIRROOT . "../file/invitado/");

define("IMGPRODUCTO_ROOT", URLROOT . "file/producto/");
define("IMGPRODUCTO_DIR", DIRROOT . "../file/producto/");

define("PUBLICIDAD_ROOT", URLROOT . "file/publicidad/");
define("PUBLICIDAD_DIR", DIRROOT . "../file/publicidad/");

define("EXTRACTOS_ROOT", URLROOT . "file/ExtractosRincon/");
define("EXTRACTOS_DIR", DIRROOT . "../file/ExtractosRincon/");

define("EXTRACTOSTMP_DIR", DIRROOT . "../file/ExtractosTMP/");
define("EXTRACTOSTMP_ROOT", URLROOT . "file/ExtractosTMP/");

define("EXTRACTOSRINCON_DIR",  "/home/http/clubes/clubrincon/upload/");
define("EXTRACTOSRANCHO_DIR",  "/home/http/clubes/clubrancho/upload/");
define("EXTRACTOSGUAYMARAL_DIR",  "/home/http/clubes/clubguaymaral/upload/");
define("EXTRACTOSGUN_DIR",  "/home/http/clubes/clubgunclub/upload/");
define("EXTRACTOSANAPOIMA_DIR",  "/home/http/clubes/clubanapoima/upload/");
define("EXTRACTOSARRAYANES_DIR",  "/home/http/clubes/clubarrayanes/upload/");
define("EXTRACTOSPAYANDE_DIR",  "/home/http/clubes/clubpayande/upload/");
define("EXTRACTOSBTCC_DIR",  "/home/http/clubes/bogotatennisclub/upload/");

//define( "EXTRACTOSGUN_DIR" , DIRROOT . "../file/extractos/gun/" );
//define( "EXTRACTOSGUN_ROOT" , URLROOT . "file/extractos/gun/" );

define("CLASIFICADOS_ROOT", URLROOT . "file/clasificados/");
define("CLASIFICADOS_DIR", DIRROOT . "../file/clasificados/");

define("OBJETOSPERDIDOS_ROOT", URLROOT . "file/objetosperdidos/");
define("OBJETOSPERDIDOS_DIR", DIRROOT . "../file/objetosperdidos/");

define("CADDIE_ROOT", URLROOT . "file/caddie/");
define("CADDIE_DIR", DIRROOT . "../file/caddie/");

define("TALEGA_ROOT", URLROOT . "file/talega/");
define("TALEGA_DIR", DIRROOT . "../file/talega/");


define("TALONERA_ROOT", URLROOT . "file/talonera/");
define("TALONERA_DIR", DIRROOT . "../file/talonera/");

define("BICICLETA_ROOT", URLROOT . "file/bicicleta/");
define("BICICLETA_DIR", DIRROOT . "../file/bicicleta/");


define("CORRESPONDENCIA_ROOT", URLROOT . "file/correspondencia/");
define("CORRESPONDENCIA_DIR", DIRROOT . "../file/correspondencia/");

//CERTIFICADOS DE VACUNAS
define("VACUNA_ROOT", URLROOT . "file/vacuna/");
define("VACUNA_DIR", DIRROOT . "../file/vacuna/");

//FOTOS MASCOTAS
define("MASCOTA_ROOT", URLROOT . "file/mascota/");
define("MASCOTA_DIR", DIRROOT . "../file/mascota/");

//ARCHIVOS PLANIFICACION LABORAL DIARIA
define("PLANIFICACIONDIARIA_ROOT", URLROOT . "file/planificaciondiaria/");
define("PLANIFICACIONDIARIA_DIR", DIRROOT . "../file/planificaciondiaria/");

//ARCHIVOS TRIATLON
define("TRIATLON_ROOT", URLROOT . "file/triatlon/");
define("TRIATLON_DIR", DIRROOT . "../file/triatlon/");

//PARAMETRO ENVIO DE CORREOS
define("SENDER", "info@22cero2.com");
define("FROMNAME", "Clubes");

//PUSH
define('PUSH_ENVIRONMENT', 'prod'); // dev|prod
define('APNS_PASSPHRASE', '');
// Google
define('GCM_API_KEY', 'AIzaSyBvis04WXeKaI9hb7CL_HE-InX60t1cD-c');
// Apple
define('CERTIFICATES_DIR', DIRROOT . '/certificates/lagartos/Production/');

//CONFIGURACION FACTURACION

//RUTA ARCHIVOS DE LA FACTURA
define("FACTURA_ROOT", URLROOT . "file/factura/");
define("FACTURA_DIR", DIRROOT . "../file/factura/");

//archivos txt para estupendo
define("ESTUPENDO_ROOT", URLROOT . "file/factura/estupendo/");
define("ESTUPENDO_DIR", DIRROOT . "../file/factura/estupendo/");

define("URL_CARGAR_ESTUPENDO", "https://pruebas.estupendo.com.co/api/cargarDocumentoTxt"); //Pruebas
//define("URL_CARGAR_ESTUPENDO", "https://pruebas.estupendo.com.co/api/cargarDocumentoTxt");//Produccion
define("URL_CONSULTAR_ESTUPENDO", "https://pruebas.estupendo.com.co/api/consultarEstadoDocumento"); //Pruebas
//define("URL_CONSULTAR_ESTUPENDO", "https://app.estupendo.com.co/api/consultarEstadoDocumento");//Produccion
define('USER_ESTUPENDO', 'jpcabrera@bodytechactive.com');
define('PSW_ESTUPENDO', 'estupendo');

//FIN CONFIGURACION FACTURACION

//CONFIGURACION INGRESOS
define("INGRESOS_ROOT", URLROOT . "ingresos/files/");
define("INGRESOS_DIR", URLROOT . "ingresos/");
define("INGRESOS_FILES", "/home/http/miclubappdev/httpdocs/ingresos/files/");
//FIN CONFIGURACION INGRESOS


//Datos Clinica Obyrne
define("URL_CLINICA", "https://obyrne.pacificmedicalsuite.com/demo/pacific-sch/api.php");
define("URL_CLINICA_PACIENTES", "https://obyrne.pacificmedicalsuite.com/demo/pacific-gbl/api.php");
define("CUENTA_CLINICA", "1");
define("TOKEN_CLINICA", '$2y$10$Zr/k8sePIc1ZdiereVFjKO0Om5aMBuMDFNe4XIFGOS1TwwE8v9tai');

// Ruta Pasarela Mercado pago
define("MERCADO_PAGO_DIR", DIRROOT . "lib/mercadopago/");


$tipoDocEstupendo = array(
	'Registro civil' => 11,
	'Tarjeta de identidad' => 12,
	'Cédula de ciudadanía' => 13,
	'Tarjeta de extranjería' => 21,
	'Cédula de extranjería' => 22,
	'NIT' => 31,
	'Pasaporte' => 41,
	'Documento de identificación extranjero' => 42,
	'NIT de otro país' => 50,
	'NUIP *' => 91
);
//FIN ESTUPENDO

$Mes_array = array(
	"01" => "Enero",
	"02" => "Febrero",
	"03" => "Marzo",
	"04" => "Abril",
	"05" => "Mayo",
	"06" => "Junio",
	"07" => "Julio",
	"08" => "Agosto",
	"09" => "Septiembre",
	"10" => "Octubre",
	"11" => "Noviembre",
	"12" => "Diciembre"
);

$Dia_array = array(
	"1" => "Lunes",
	"2" => "Martes",
	"3" => "Miercoles",
	"4" => "Jueves",
	"5" => "Viernes",
	"6" => "Sabado",
	"0" => "Domingo"
);



require(LIBDIR . "SIMDB.inc.php");
require(LIBDIR . "SIMDBMongo.inc.php");
require(LIBDIR . "SIMSession.inc.php");
require(LIBDIR . "SIMSessionCliente.inc.php");
require(LIBDIR . "SIMResources.inc.php");
require(LIBDIR . "SIMUtil.inc.php");
require(LIBDIR . "SIMReport.inc.php");
require(LIBDIR . "SIMFile.inc.php");
require(LIBDIR . "SIMLog.inc.php");
require(LIBDIR . "SIMHTML.inc.php");
require(LIBDIR . "SIMNet.inc.php");
require(LIBDIR . "SIMNotify.inc.php");
require(LIBDIR . "SIMArchivos.php");
require(LIBDIR . "class.phpmailer.php");
require(LIBDIR . "SIMReg.inc.php");
require(LIBDIR . "SIMUser.inc.php");
require(LIBDIR . "json.php");
require(LIBDIR . "SIMWebService.inc.php");
require(LIBDIR . "SIMWebServiceApp.inc.php");
require(LIBDIR . "SIMWebServiceFedegolf.inc.php");
require(LIBDIR . "SIMWebServiceZeus.inc.php");
require(LIBDIR . "SIMWebServiceTest.inc.php");
require(LIBDIR . "SIMWebServiceSMS.inc.php");
require(LIBDIR . "SIMWebServiceHotel.inc.php");
require(LIBDIR . "SIMWebServiceToken.inc.php");
require(LIBDIR . "SIMPasarelaPagos.inc.php");
//require( LIBDIR . "SIMWebServiceLaboral.inc.php" );



//Librerias y Clases
require(LIBDIR . "buildNav.php");




$array_tablas_participante = array("1" => "Participante");
$array_tablas_tiempos = array("1" => "TiempoCarrera");


// FILTRAR INJECT
if (empty($_POST))
	foreach ($_GET as $key => $val) {
		if ($key != "email" && $key != "TokenID")
			$_GET[$key] = SIMUtil::antiinjection($val);
	}




$DB_DEBUG = true;
$DB_DIE_ON_FAIL = true;

$dbo = &SIMDB::get();
$dblink = $dbo->connect(DBHOST, DBNAME, DBUSER, DBPASS);


$dboMongo = &SIMDBMongo::get();
$dblinkMongo = $dboMongo->connect(DBHOSTMongo, DBNAMEMongo, DBUSERMongo, DBPASSMongo);



//Mensajes Web
$MensajesWeb = $dbo->all("MensajeWeb", "IDMensajeWeb > 0 ");
while ($r = $dbo->fetchArray($MensajesWeb))
	$ArrayMensajesWeb[$r["IDTipoClub"]][$r["Codigo"]] = $r["Nombre"];

if ($_GET["IDClub"])
	$IDClubSelecc = SIMNet::req("IDClub");
else
	$IDClubSelecc = $_POST["IDClub"];

//Zona Horaria Club
if (!empty($IDClubSelecc)) {
	$IDZonaHorariaClub = $dbo->getFields("ConfiguracionClub", "IDZonaHoraria", "IDClub = '" . $IDClubSelecc . "' ");
} elseif (!empty($_SESSION["club"])) {
	$IDZonaHorariaClub = $dbo->getFields("ConfiguracionClub", "IDZonaHoraria", "IDClub = '" . $_SESSION["club"] . "' ");
} else {
	$IDZonaHorariaClub = 1;
}


$ZonaHorariaClub = $dbo->getFields("ZonaHoraria", "ZonaHoraria", "IDZonaHoraria = '" . $IDZonaHorariaClub . "' ");
if (!empty($ZonaHorariaClub))
	date_default_timezone_set($ZonaHorariaClub);
else
	date_default_timezone_set('America/Bogota');
