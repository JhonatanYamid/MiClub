<?php
class SIMResources
{
	static $IMAGE = "IMAGE";
	static $FLASH = "FLASH";

	//MIME Types
	static $mimes = array(
		"text/plain" => "Archivo de Texto",
		"application/msword" => "MS Word",
		"application/pdf" => "Acrobat Reader",
		"application/vnd.ms-excel" => "MS Excel",
		"application/vnd.ms-powerpoint" => "MS PowerPoint",
		"application/ms-powerpoint" => "MS PowerPoint",
		"application/mspowerpoint" => "MS PowerPoint",
		"application/x-shockwave-flash" => "MacroMedia Flash",
		"text/html" => "Formato Web",
		"image/tiff" => "Archivo de Imagen",
		"image/gif" => "Archivo de Imagen",
		"image/jpeg" => "Archivo de Imagen",
		"audio/mpeg" => "Audio MP3",
		"audio/x-midi" => "Audio Secuencia MIDI",
		"audio/x-wav" => "Audio Audio WAV",
		"video/mpeg" => "Video MPEG",
		"video/vndvivo" => "Formato de Video",
		"video/quicktime" => "Video Quicktime",
		"video/x-msvideo" => "Video AVI",
		"video/x-ms-wmv" => "Video Windows Media",
		"application/acad" => "Formato Autocad",
		"application/vndms-project" => "MS-Project",
		"application/vnd.ms-project" => "MS-Project",
		"application/wordperfect51" => "Word Perfect 5.1",
		"application/octet-stream" => "Archivo de Texto",
		"application/x-gzip" => "Archivo Comprimido",
		"application/zip" => "Archivo Comprimido",
		"application/x-tar" => "Archivo Comprimido",
		"image/bmp" => "Archivo de Imagen",
		"image/png" => "Archivo de Imagen",
		"image/x-png" => "Archivo de Imagen",
		"text/rtf" => "Texto Enriquecido",
		"application/vnd.sun.xml.writer" => "Archivo SXW",
		"application/vnd.sun.xml.writer.global" => "Archivo SXG",
		"application/vnd.sun.xml.draw" => "Archivo SXD",
		"application/vnd.sun.xml.calc" => "Archivo SXC",
		"application/vnd.sun.xml.impress" => "Archivo SXI",
		"text/vcf" => "Archivo VCard",
		"application/x-gzip" => "Archivo TGZ",
		"application/x-gzip" => "Archivo GZ"
	);

	//Meses
	static $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

	//Cargos de my office
	/* static $cargo_my_office = array(
		"SVP QSC Bogota", "Manager Accounts Payable", "Supervisor Cash Management", "Supervisor Accounts Receivable", "Specialist Controlling", "Manager GL", "Specialist General Ledger", "Manager Human Resources", "Supervisor  IT", "Director Business Administration", "Manager Customer Service Segmented Vertical", "Specialist Customer Service Documentation", "Manager Customer Service Segmented Vertical", "Manager Customer Service CSS", "Supervisor Customer Service Segmented Vertical", "Supervisor Customer Service", "Director Customer Service LPE", "Manager Equipment Dispatch", "Supervisor Equipment Dispatch", "Director Operations", " Manager Operations Steering", "Specialist Operations Steering", " Manager Port Terminal Operations", "Supervisor Port Terminal Operations", "Supervisor PO Center", "Manager Transport Dispatch", "Supervisor Transport Dispatch", "Student", "Apprentice"
	); */
	static $cargo_my_office = array(

		"Specialist Controlling", "Manager Customer Service Segmented Vertical", "Manager Customer Service CSS", "Supervisor Equipment Disposition", "Manager Customer Service Segmented Vertical", "Senior Manager Customer Service Segmented Vertical", "Supervisor Equipment Disposition", "Senior Manager Accounts Receivable and Controlling", "Manager Customer Service Segmented Vertical", "Senior Coordinator Customer Service PPS 2", "Supervisor Equipment Disposition", "Supervisor PO Center", "Manager Transport Dispatch", "Senior Coordinator Customer Service PPS 2", "Sublead Customer Service Segmented Vertical", "Director Operations", "Manager Port Terminal Operations", "Manager Customer Service Segmented Vertical", "Supervisor Customer Service", "Senior Manager Human Resources", "Supervisor Customer Service Segmented Vertical", "Specialist General Ledger", "Senior Coordinator Customer Service PPS 2", "Manager Equipment Disposition", "Supervisor Accounts Receivable", "Supervisor Cash Management", "Manager Accounts Payable", "Supervisor  IT", "Specialist Customer Service Documentation", "Supervisor Customer Service Segmented Vertical", "Specialist HR and Administration", "Director Business Administration", "Manager Human Resources", "Supervisor Customer Service CSS", "Director Customer Service LPE", "Supervisor Port Terminal Operations", "Manager Customer Service Segmented Vertical", "SVP QSC Bogota", "Specialist Port Terminal Operations", "Manager Customer Service Segmented Vertical", "Supervisor Accounts Receivable", "Supervisor Transport Dispatch", "Senior Manager Customer Service CSS", "Specialist Operations Steering", "Manager Operations Steering", "Supervisor Accounts Receivable", "Student"

	);


	static $areassoycentral = array("1" => "Ventas", "2" => "Industrial", "3" => "Finanzas", "4" => "GH", "5" => "Mercadeo", "6" => "Artesanos");

	static $equiposlapradera = array("1" => "Portatiles", "2" => "Maquinaria", "3" => "Herramientas", "4" => "Taladros", "5" => "Otros");

	//Dias
	static $dias_semana = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");

	static $dias_semana_reporte_x_periodo = array("", "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");

	//Estado Pago
	static $EstadoPago = array("Pagado" => "Pagado", "Pendiente" => "Pendiente");

	//Estado Pago
	static $EstadoAuxilio = array("1" => "Pendiente", "2" => "Rechazado", "3" => "Aprobado", "4" => "Pendiente 2da Aprobación");

	//Estado Zeus
	static $EstadoZeus = array("0" => "SIN ESTADO ZEUS", "1" => "BLOQUEADO PARA CONSUMOS", "2" => "BLOQUEO PARA INGRESO CLUB", "3" => "BLOQUEO POR SUSPENCION", "4" => "SOCIOS SIN TARJETA", "5" => "SOCIO RETIRADO", "6" => "SOCIO INACTIVO");

	//Tipos de contratos
	static $tipo_contrato = array("Indefinido" => "Indefinido", "Temporal" => "Temporal", "Practicas" => "Practicas", "Fijo" => "Fijo", "Obra o labor" => "Obra o labor");

	//Estado civil
	static $estadoCivil = array("CA" => "Casado", "SO" => "Soltero", "SE" => "Separado", "VI" => "Viudo", "UL" => "Union Libre");

	// Formatos Fecha
	static $formatos_fecha = array("yyyy-MM-dd" => "Y-m-d", "yyyy/MM/dd" => "Y/m/d", "dd-MM-yyyy" => "d-m-Y", "dd/MM/yyyy" => "d/m/Y");

	// Formatos Hora
	static $formatos_hora = array("hh:mm:ss" => "H:i:s", "hh:mm" => "H:i", "hh:mm:ss a" => "h:i:s a", "hh:mm a" => "h:i a",);

	// Formatos hora y fecha

	static $formatos_fecha_hora = array(
		"yyyy-MM-dd hh:mm:ss" => "Y-m-d H:i:s",
		"yyyy-MM-dd hh:mm" => "Y-m-d H:i",
		"yyyy-MM-dd hh:mm:ss a" => "Y-m-d h:i:s a",
		"yyyy-MM-dd hh:mm a" => "Y-m-d h:i a",

		"yyyy/MM/dd hh:mm:ss" => "Y/m/d H:i:s",
		"yyyy/MM/dd hh:mm" => "Y/m/d H:i",
		"yyyy/MM/dd hh:mm:ss a" => "Y/m/d h:i:s a",
		"yyyy/MM/dd hh:mm a" => "Y/m/d h:i a",

		"dd-MM-yyyy hh:mm:ss" => "d-m-Y H:i:s",
		"dd-MM-yyyy hh:mm" => "d-m-Y H:i",
		"dd-MM-yyyy hh:mm:ss a" => "d-m-Y h:i:s a",
		"dd-MM-yyyy hh:mm a" => "d-m-Y h:i a",

		"dd/MM/yyyy hh:mm:ss" => "d/m/Y H:i:s",
		"dd/MM/yyyy hh:mm" => "d/m/Y H:i",
		"dd/MM/yyyy hh:mm:ss a" => "d/m/Y h:i:s a",
		"dd/MM/yyyy hh:mm a" => "d/m/Y h:i a"
	);


	//ABECEDARIO
	static $abecedario = array(
		"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L",
		"M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
		"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l",
		"m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"
	);



	static $abecedario_orden = array(
		"A1", "A2", "A3", "A4",
		"A5", "A6", "A7", "A8",
		"A9", "B1", "B2", "B3",
		"B4", "B5", "B6", "B7",
		"B8", "B9", "C1", "C2",
		"C3", "C4", "C5", "C6",
		"C7", "C8", "C9", "D1",
		"D2", "D3", "D4", "D5",
		"D6", "D7", "D8", "D9",
		"E1", "E2", "E3", "E4",
		"E5", "E6", "E7", "E8",
		"E9", "F1", "F2", "F3",
		"F4", "F5", "F6", "F7",
		"F8", "F9", "G1", "G2",
		"G3", "G4", "G5", "G6",
		"G7", "G8", "G9", "H1",
		"H2", "H3", "H4", "H5",
		"H6", "H7", "H8", "H9"

	);

	//si y no
	static $sino = array("S" => "S", "N" => "N");

	//si y no
	static $sinoNum = array("S" => "1", "N" => "0");

	//TipoCodigo
	static $tipocodigocarne = array("Barras" => "Codigo Barras", "QR" => "CodigoQr");

	//TipoCodigo
	static $datosCarne = array("Identificacion" => "Identificación", "Contacto" => "Contacto", "Codigocarne" => "Código carne", "AccionPadre" => "Acción Padre", "Accion" => "Acción");

	//Publicacion dirigida a Socios o Empleados
	static $dirigidoa = array("S" => "Socios", "E" => "Empleados", "T" => "Socios y Empleados");

	static $motivo_laboral = array("1" => "Familiar", "2" => "Salud", "3" => "Academico", "4" => "Personal", "5" => "Viaje", "6" => "Tramite", "7" => "Otro");

	static $tipo_certificado_laboral = array("1" => "Laboral", "2" => "Afiliacion EPS", "3" => "Afiliacion Cesantias", "4" => "Afiliacion ARL", "5" => "Otro");

	static $estado_checkin_laboral = array("1" => "Pendiente", "2" => "Aprobado", "3" => "No Aprobado");
	static $estado_laboral = array("1" => "Pendiente", "2" => "Aprobado", "3" => "No Aprobado", "4" => "Pendiente 2da Aprobación");

	//estado objeto prestado
	static $estado_objeto_prestado = array("1" => "Pendiente", "2" => "Entregado");



	//Tipo Cumplimiento de una reserva
	static $tipocumplimientoreserva = array("S" => "S", "N" => "N", "I" => "Incumplida sin sancion", "P" => "Parcial (no se presentaron todos los invitados)");

	//Tipo de invitacion
	static $tipoinvitado = array(
		"Diaria" => "Diaria:ingresa y sale una vez en el dia", "Invitacion" => "Invitacion", "Arriendo" => "Arriendo", "Balcones" => "Balcones", "Familiar" => "Familiar",
		"Amigo" => "Amigo", "Empleado" => "Empleado", "Otros" => "Otros", "Acceso casa" => "Acceso Casa", "Contratista" => "Contratista",
		"Acceso Sede Social" => "Acceso Sede Social", "Acceso Casa y Sede Social" => "Acceso Casa y Sede Social",
		"Mudanza" => "Mudanza", "Trabajador" => "Trabajador", "Permanente" => "Permanente:ingresa y sale mas de una vez en el dia"
	);

	//Tipo de autorizacion
	static $tipoautorizacion = array(
		"Auxiliar" => "Auxiliar", "Niñera" => "Niñera", "Conductor" => "Conductor", "Contratista" => "Contratista", "Escolta" => "Escolta",
		"Otro" => "Otro", "Diaria:ingresa y sale una vez en el dia" => "Diaria", "Permanente: ingresa y sale mas de una vez en el dia" => "Permanente",
		"Diaria." => "Diaria.", "Semanal." => "Semanal.", "Mensual." => "Mensual."
	);

	//MIME types validos
	static $mimeValidos = array(
		"image/gif",	//imagen gif
		"image/pjpeg",	//imagen jpeg
		"image/jpeg",	//imagen jpeg
		"image/png",	//imagen png
		"application/x-shockwave-flash", //Archivo compilado de flash
		"text/plain",	//Texto Plano
		"application/msword",	//Documento de MS Word
		"application/pdf",	//Documento de Adobe Acrobat
		"application/vnd.ms-excel",	//Hoja de calculo de MS Excel
		"application/vnd.ms-powerpoint",	//Presentacion de MS PowerPoint
		"text/rtf",	//Documento
		"text/css",	//Hoja de estilo en cascada
		"application/octet-stream",
		"audio/mpeg",
		"audio/x-midi",
		"audio/x-wav",
		"video/mpeg",
		"video/vndvivo",
		"video/quicktime",
		"video/x-msvideo",
		"video/x-ms-wmv",
		"application/acad",
		"text/vcf",
		"application/x-gzip",
		"application/x-gzip",
		"application/acad",
		"application/vndms-project",
		"application/vnd.ms-project",
		"application/wordperfect51",
		"application/vnd.sun.xml.writer",
		"application/vnd.sun.xml.writer.global",
		"application/vnd.sun.xml.draw",
		"application/vnd.sun.xml.calc",
		"application/vnd.sun.xml.impress",
		"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
		"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"

	);

	//MIME types de imagenes validos
	static $mimeImagenValidos = array(
		"image/gif",	//imagen gif
		"image/pjpeg",	//imagen jpeg
		"image/jpeg",	//imagen jpeg
		"image/png"		//imagen png
	);

	//MIME types graficos validos
	static $mimeGraficoValidos = array(
		"image/gif",	//imagen gif
		"image/pjpeg",	//imagen jpeg
		"image/jpeg",	//imagen jpeg
		"image/png",	//imagen png
		"application/x-shockwave-flash", //Archivo compilado de flash
		"audio/mpeg",
		"audio/x-midi",
		"audio/x-wav",
		"video/mpeg",
		"video/vndvivo",
		"video/quicktime",
		"video/x-msvideo",
		"video/x-ms-wmv",
		"application/acad",
		"application/vnd.sun.xml.draw"
	);



	static $mimeTypeFile = array(
		"application/x-shockwave-flash" => "FLASH",
		"image/gif" => "IMAGE",
		"image/jpeg" => "IMAGE",
		"image/png" => "IMAGE",
		"image/pjpeg" => "IMAGE",
		"image/gif" => "IMAGE"
	);

	//HOME MENU MENUHOME
	static $Ubicacion_c = array(
		"Home" => "Home",
		"Menu" => "Menu",
		"MenuHome" => "MenuHome"
	);


	//Ubicacion Secciones
	static $Ubicacion_s = array(
		"Menu" => "Menu",
		"Seccion" => "Seccion",
		"Left" => "Left",
		"Right" => "Right"
	);

	//Ubicacion Mensajes Web
	static $Ubicacion_mw = array(
		"Buscar" => "Buscar",
		"Login" => "Login",
		"MenuLeft" => "MenuLeft",
		"MenuCenter" => "MenuCenter",
		"MenuRight" => "MenuRight",
		"Encuesta" => "Encuesta",
		"Enviar" => "Enviar",
		"Directorio" => "Directorio",
		"Clima" => "Clima",
		"Clasificados" => "Clasificados",
		"Vacantes" => "Vacantes",
		"Foro" => "Foro",
		"VerMas" => "Ver m&aacutes",
		"BAvan" => "Busqueda Avanzada",
		"Enviar" => "Enviar"
	);



	//MIME types de video validos
	static $mimeVideoValidos = array(
		"application/x-shockwave-flash", //Archivo compilado de flash
		"audio/mpeg",
		"audio/x-midi",
		"audio/x-wav",
		"video/mpeg",
		"video/vndvivo",
		"video/quicktime",
		"video/x-msvideo",
		"video/x-ms-wmv"
	);

	//MIME types de audio validos
	static $mimeAudioValidos = array(
		"audio/mpeg" => "Audio MP3",
		"audio/x-midi" => "Audio Secuencia MIDI",
		"audio/x-wav" => "Audio Audio WAV"
	);


	//MIME types de documento validos
	static $mimeDocsValidos = array(

		"text/plain",	//Texto Plano
		"application/msword",	//Documento de MS Word
		"application/pdf",	//Documento de Adobe Acrobat
		"application/vnd.ms-excel",	//Hoja de calculo de MS Excel
		"application/vnd.ms-powerpoint",	//Presentacion de MS PowerPoint
		"text/rtf",	//Documento
		"text/vcf",
		"application/x-gzip",
		"application/x-gzip",
		"application/octet-stream",
		"application/acad",
		"application/vndms-project",
		"application/vnd.ms-project",
		"application/wordperfect51",
		"application/vnd.sun.xml.writer",
		"application/vnd.sun.xml.writer.global",
		"application/vnd.sun.xml.draw",
		"application/vnd.sun.xml.calc",
		"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
		"application/vnd.sun.xml.impress"
	);

	//arreglo de mensajes
	static $mensajes = 	array(
		"insertarexito" => array("msg" => "El registro se ha ingresado con exito", "type" => "info alert alert-block alert-success"),
		"guardarexito" => array("msg" => "Los cambios han sido guardados satisfactoriamente", "type" => "info alert alert-block alert-success"),
		"eliminarexito" => array("msg" => "El registro se ha eliminado con exito", "type" => "info alert-block alert-success"),
		"error" => array("msg" => "Ha ocurrido un error durante el proceso", "type" => "error alert alert-danger"),
		"duplicada" => array("msg" => "La propuesta ha sido duplicada con exito", "type" => "info")
	);

	//errores de sesion
	static $session = array(
		"NSA" => "No tienes una sesi&oacute;n activa",
		"XS" => "Tu sesi&oacute;n ha expirado",
		"LI" => "Verifica tu usuario y contrase&ntilde;a",
		"EX" => "Sesi&aacute;n terminada con exito"
	);

	static $medidas = array(
		"IM" => "Impresiones",
		"CL" => "Clicks",
		"EN" => "Env&iacute;os"
	);

	static $cortes = array(
		"Mensual" => "Mensual",
		"Trimestral" => "Trimestral"
	);

	static $inversionmin = array(
		"IM" => "Impresiones",
		"VM" => "Inversi&oacute;n M&iacute;nima"
	);

	static $efectividad = array(
		"EF" => "Efectividad Clicks",
		"EA" => "Efectividad en Aperturas",
		"PC" => "Porcentaje de Cumplimiento"
	);

	static $niveles_acceso = array(
		"0" => "Administrador",
		"1" => "Contenidos"

	);

	static $documentos_propuesta = array(
		"Presupuesto" => "Presupuesto",
		"Brief" => "Brief",
		"Solicitud de Cotizacion" => "Solicitud de Cotizacion",
		"Propuesta" => "Propuesta",
		"Aprobacion" => "Aprobacion",
		"Cronograma" => "Cronograma",
	);

	static $canchas_polo = array(
		"1" => "1",
		"2" => "2",
		"3" => "3",
		"4" => "4",
		"5" => "5",
		"6" => "6",
		"7" => "7",
		"8" => "8",
	);
	static $canchas_polo_pino = array(
		"1" => "1",
		"2" => "2",
		"3" => "3"
	);

	static $equipos_polo = array(
		"blanco" => "Blanco",
		"rojo" => "Rojo",
		"blancorojo" => "Azul"
	);

	static $estado_talega = array("1" => "Disponible", "2" => "En Campo", "3" => "Entregada", "4" => "Solicitada");


	static $tipo_socio = array(
		"Socio" => "Socio",
		"Titular" => "Titular",
		"Conyuge" => "Conyuge",
		"Beneficiario" => "Beneficiario",
		"Canje" => "Canje",
		"Cortesia" => "Cortesia",
		"Invitado" => "Invitado",
		"Propietario" => "Propietario",
		"Residente" => "Residente",
		"Empleado" => "Empleado",
		"Contratista" => "Contratista",
		"Padre" => "Padre",
		"Madre" => "Madre",
		"Empleado" => "Empleado",
		"Estudiante" => "Estudiante",
		"Socio Otro Club" => "Socio Otro Club",


	);



	//Atributo para el modulo de busqueda
	static $modulos_2_buscar = array(
		"Noticia" => array(
			"string"  => array("Titular", "Introduccion", "Cuerpo", "URL")
		),
		"NoticiaModulo" => array(
			"string"  => array("Nombre", "Descripcion", "URL")
		),
		"CategoriaProducto" => array(
			"string"  => array("Nombre", "Introduccion", "Descripcion", "URL")
		),
		"Producto" => array(
			"string"  => array("Nombre", "Descripcion")
		),
		"Testimonio" => array(
			"string"  => array("Cliente", "Introduccion", "Descripcion", "URL")
		)


	);

	// Periodicidad Auxilios
	static $PeriodicidadAuxilios = array("Único pago" => "Único pago", "2 veces al año" => "2 veces al año", "3 veces al año" => "3 veces al año", "4 veces al año" => "4 veces al año", "12 veces al año" => "12 veces al año", "Anual" => "Anual", "Una vez por tipo de auxilio al año" => "Una vez por tipo de auxilio al año", "Acontecimiento (Nacimiento,muerte,etc)" => "Acontecimiento (Nacimiento,muerte,etc)",);

	// Estado Socio Chicureo
	static $EstadoChicureo = array(
		"Prospecto" => "Prospecto",
		"Vigente" => "Vigente",
		"Reincorporado" => "Reincorporado",
		"Eximido" => "Eximido",
		"Pasivo" => "Pasivo",
		"Renunciado" => "Renunciado",
		"Fallecido" => "Fallecido",
		"Suspendido" => "Suspendido",
		"Sancionado" => "Sancionado",
		"Expulsado" => "Expulsado",
		"Eliminado" => "Eliminado",
	);

	static $unidadTiempo = array(
		1 => "Horas",
		2 => "Días",
		3 => "Meses",
	);

	static $DiasSemana = array(
		0 => "D",
		1 => "L",
		2 => "M",
		3 => "MI",
		4 => "J",
		5 => "V",
		6 => "S",
	);

	/*
	static $MetodoPago = array(
		"Efectivo",
		"T. Debito",
		"T. Credito",
		"Cheque"
	);
	*/

	/*
	static $PeriodicidadCuotasSociales = array(
		"Mensual",
		"Trimestal",
		"Semestral",
		"Anual"
	);
	*/

	static $CriterioReglasNegocio = array('EstadoCivil' => 'Estado Civil', 'Edad' => 'Edad', 'IDCategoria' => 'Categoria', 'TipoSocio' => 'Tipo Socio', 'IDParentesco' => 'Parentesco');
	static $ValidacionReglasNegocio = array('>=' => 'Mayor a', '<=' => 'Menor a', '==' => 'Igual a', '!=' => 'Diferente de
	', 'Rango' => 'Rango');
	// Periodicidad Cuotas Sociales
	static $PeriodicidadCuotasSociales = array("1" => "Mensual", "3" => "Trimestral", "6" => "Semestral", "12" => "Anual");
	// Metodos de pago
	static $MetodoPago = array("PAC" => "PAC", "Cheque" => "Cheque", "Tarjeta cr&eacute;dito" => "Tarjeta cr&eacute;dito", "Transferencia" => "Transferencia", "Deposito" => "Deposito", "Canje" => "Canje");
	// Prioridad Categorias Cuotas Sociales
	static $PrioridadCategoria = array("1" => "Alta", "2" => "Media", "3" => "Baja");
	// Reglas de negocio / Cuotas Sociales End //

	// Periodicidad Auxilios
	// Periodicidad Tiempo para mi
	static $PeriodicidadAuxiliosInfinito = array("Anual" => "Anual");

	static $EstadoIngresos = array(
		1 => "Pendiente",
		2 => "Revisado"
	);
	static $arrIDClubLuker = array(
		95, 96, 97, 98, 122, 169
	);
}
