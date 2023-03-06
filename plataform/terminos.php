<?
	require( "admin/config.inc.php" );
	include( "plataform/procedures/home_site.php" );
	SIMUtil::cache();
	session_start();

	include( "cmp/seo.php" );

?>
<script src="https://www.google.com/recaptcha/api.js?render=6Ld4o5gUAAAAAEPYDGbS0NOmjteW_uQfDV2DjTPc"></script>
<script>
grecaptcha.ready(function() {
		grecaptcha.execute('6Ld4o5gUAAAAAEPYDGbS0NOmjteW_uQfDV2DjTPc', {action: 'homepage'}).then(function(token) {
				 var recaptchaResponse = document.getElementById('recaptchaResponse');
				 recaptchaResponse.value = token;
		});
});
</script>
</head>
<body>


	<div id="home" class="jumbotron slide">
		<?
		foreach ($banners as $key_banner => $datos_banner) {
			# code...

		?>
			<div class="container">
				<h1><img src="img/logogrande.png" alt="<?=$datos_banner["Nombre"] ?>" /></h1>
	            <span><?=$datos_banner["Nombre"] ?></span>
				<a href="index.php#contacto" class="btnheadct btn btn-lg btn-primary button--ujarak">Contáctenos</a>
			</div>
		<?
		}//end for
		?>
	</div>

	<!-- Start services section -->
	<section id="services">
		<div class="container">

				<h2 class="wow fadeInDown">TÉRMINOS Y CONDICIONES</h2><p>Política de Protección de Datos Personales<br>
				</p>
				<p>Esta Política de Protección de Datos Personales se aplicará a todas las Bases de Datos y/o Archivos que contengan Datos Personales que sean objeto de Tratamiento por 109 apps 109 apps SAS .. considerada como responsable y/o encargada del tratamiento de Datos Personales.</p>
				<p>2. IDENTIFICACIÓN DEL RESPONSABLE DEL TRATAMIENTO DE DATOS PERSONALES<br>
				  109 apps 109 apps SAS .. sociedad con domicilio en la Calle 109 # 18b-31 of 305 de la ciudad de Bogotá D.C., Colombia identificada con el Número de Identificación Tributaria NIT 900953134-2<br>
				  Correo electrónico: info@109apps.com, teléfono (+571) 6580888.</p>
				<p>3. DEFINICIONES</p>
				<p>Base de Datos: Conjunto organizado de Datos Personales que sea objeto de Tratamiento. </p>
				<p>Dato Sensible: Información que afectan la intimidad de las personas o cuyo uso indebido puede generar discriminación (Origen racial o étnico, orientación policita, convicciones filosóficas o religio109 apps SAS , pertinencia a sindicatos u organizaciones sociales o derechos humanos, datos de salud, vida sexual y biométricos).</p>
				<p>Dato Personal: Cualquier información vinculada o que pueda asociarse a una o varias personas naturales determinadas o determinables.</p>
				<p>Autorización: Consentimiento previo, expreso e informado del Titular para llevar a cabo el Tratamiento de Datos Personales.</p>
				<p>Aviso de Privacidad: Comunicación verbal o escrita generada por el Responsable, dirigida al Titular para el Tratamiento de sus Datos Personales, mediante la cual se le informa acerca de la existencia de las Políticas de Tratamiento de información que le serán aplicables, la forma de acceder a las mismas y las finalidades del Tratamiento que se pretende dar a los datos personales.<br>
		  </p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>Encargado del Tratamiento: Persona natural o jurídica, pública o privada, que por sí misma o en asocio con otros, realice el Tratamiento de Datos Personales por cuenta del Responsable del Tratamiento. En los eventos en que el Responsable no ejerza como Encargado de la Base de Datos, se identificará expresamente quién será el Encargado.</p>
				<p>Responsable del Tratamiento: Persona natural o jurídica, pública o privada, que por sí misma o en asocio con otros, decida sobre la Base de Datos y/o el Tratamiento de los datos.</p>
				<p>Términos y Condiciones: marco general en el cual se establecen las condiciones para los participantes de actividades comerciales o afines.</p>
				<p>Titular: Persona natural cuyos Datos Personales sean objeto de Tratamiento. </p>
				<p>Tratamiento: Cualquier operación o conjunto de operaciones sobre Datos Personales, tales como la recolección, almacenamiento, uso, circulación o supresión.</p>
				<p>Transferencia: La transferencia de datos tiene lugar cuando el Responsable y/o Encargado del Tratamiento de datos personales, ubicado en Colombia, envía la información o los datos personales a un receptor, que a su vez es Responsable del Tratamiento y se encuentra dentro o fuera del país.</p>
				<p>Transmisión: Tratamiento de Datos Personales que implica la comunicación de los mismos dentro o fuera del territorio de la República de Colombia cuando tenga por objeto la realización de un Tratamiento por el Encargado por cuenta del Responsable.<br>
		  </p>
				<p>4. TRATAMIENTO<br>
				  109 apps SAS , actuando en calidad de Responsable del Tratamiento de Datos Personales, para el adecuado desarrollo de sus actividades, así como para el fortalecimiento de sus relaciones con terceros, recolecta, almacena, usa, circula y suprime Datos Personales correspondientes a personas naturales con quienes tiene o ha tenido relación, tales como, sin que la enumeración signifique limitación, trabajadores y familiares de éstos, accionistas, consumidores, clientes, distribuidores, proveedores, acreedores y deudores.</p>
				<p>5. FINALIDAD<br>
				  Los Datos Personales son objeto de Tratamiento por parte de 109 apps SAS  con las siguientes finalidades:<br>
				  5.1 Permitir el acceso a las plataformas y aplicación movil que se desprende del desarrollo de Mi Club. <br>
				  5.2 Llenado de solicitudes de los servicios de reservas, pedidos ofrecidos en el app de la plataforma mi club.<br>
				  5.4 Entregar el contido solicitado por el socio que es relevante a sus intereses..<br>
				  5.8 Prevenir y detectar el fraude u otras actividades ilegales o prohibidas.<br>
				  5.9 Velar por la seguridad e integridad  del servicio ofrecido a travez del sistema Mi Club.<br>
				  5.11 Mientras se facilita soporte técnico o consultoría respecto de algún producto.<br>
				  5. 12 Para formar parte del proceso de mantenimiento y modernización de nuestros productos. <br>
		  </p>
				<p> 6. DERECHOS DE LOS TITULARES DE LOS DATOS PERSONALES<br>
				  Las personas naturales cuyos Datos Personales sean objeto de Tratamiento por parte de 109 apps SAS , tienen los siguientes derechos, los cuales pueden ejercer en cualquier momento:<br>
				  6.1. Conocer los Datos Personales sobre los cuales 109 apps SAS  está realizando el Tratamiento. De igual manera, el Titular puede solicitar en cualquier momento, que sus datos sean actualizados o rectificados, por ejemplo, si encuentra que sus datos son parciales, inexactos, incompletos, fraccionados, induzcan a error, o aquellos cuyo Tratamiento esté expresamente prohibido o no haya sido autorizado.<br>
				  6.2. Solicitar prueba de la autorización otorgada a 109 apps SAS  para el Tratamiento de sus Datos Personales.<br>
				  6.3. Ser informado por 109 apps SAS , previa solicitud, respecto del uso que ésta le ha dado a sus Datos Personales.<br>
				  6.4. Presentar ante la Superintendencia de Industria y Comercio quejas por infracciones a lo dispuesto en la Ley de Protección de Datos Personales.<br>
				  6.5. Solicitar a 109 apps SAS  la supresión de sus Datos Personales y/o revocar la autorización otorgada para el Tratamiento de los mismos, mediante la presentación de un reclamo, de acuerdo con los procedimientos establecidos en el numeral 11 de esta Política. No obstante, la solicitud de supresión de la información y la revocatoria de la autorización no procederán cuando el Titular de la información tenga un deber legal o contractual de permanecer en la Base de Datos y/o Archivos, ni mientras se encuentre vigente la relación entre el Titular y 109 apps SAS , en virtud de la cual fueron recolectados sus datos.</p>
				<p>7. ÁREA RESPONSABLE DE LA IMPLEMENTACIÓN Y OBSERVANCIA DE ESTA POLÍTICA<br>
				  109 apps SAS  tiene a su cargo la labor de desarrollo, implementación, capacitación y observancia de ésta Política. Para el efecto, todos los funcionarios que realizan el Tratamiento de Datos Personales en las diferentes áreas de 109 apps SAS , están obligados a reportar estas Bases de Datos y a dar traslado a ésta de manera inmediata, de todas las peticiones, quejas o reclamos que reciban por parte de los Titulares de Datos Personales.<br>
		  </p>
				<p>8. AUTORIZACIÓN<br>
				  El club Farallones certifica que a 109 apps SAS que cuenta con la autorización para entregar los datos personales de sus socios y ser usados por la plataforma para acceso y servicios que en esta se prestan.<br>
		  </p>
				<p>IMPORTANTE: En ningún caso 109 apps SAS  asimilará el silencio del Titular a una conducta inequívoca. Cualquiera que sea el mecanismo utilizado por 109 apps SAS , es necesario que la autorización se conserve para poder ser consultada con posterioridad.</p>
				<p>&nbsp;</p>
				<p>9. DISPOSICIONES ESPECIALES PARA EL TRATAMIENTO DE DATOS PERSONALES DE NATURALEZA SENSIBLE.<br>
				  De acuerdo con la Ley de Protección de Datos Personales, se consideran como datos de naturaleza sensible aquellos que afectan la intimidad o cuyo uso indebido puede generar discriminación, tales como los relacionados con :<br>
				  Origen racial o étnico.<br>
				  Orientación política.<br>
				  Convicciones religiosa  / filosóficas.<br>
				  Pertenencia a sindicatos, a organizaciones sociales, a organizaciones de derechos<br>
				  humanos o a partidos políticos.<br>
				  Salud.<br>
				  Vida sexual.<br>
				  Datos biométricos (como la huella dactilar, la firma y la foto).<br>
				  El Tratamiento de los Datos Personales de naturaleza sensible está prohibido por la ley, salvo que se cuente con autorización expresa, previa e informada del Titular, entre otras excepciones consagradas en el Artículo 6o de la Ley 1581 de 2012.<br>
		  </p>
				<p>10. DISPOSICIONES ESPECIALES PARA EL TRATAMIENTO DE DATOS PERSONALES DE NIÑOS, NIÑAS Y ADOLESCENTES<br>
				  Según lo dispuesto por el Artículo 7o de la Ley 1581 de 2012 y el artículo 12 del Decreto 1377 de 2013, 109 apps SAS  sólo realizará el Tratamiento, esto es, la recolección, almacenamiento, uso, circulación y/o supresión de Datos Personales correspondientes a niños, niñas y adolescentes, siempre y cuando este Tratamiento responda y respete el interés superior de los niños, niñas y adolescentes y asegure el respeto de sus derechos fundamentales.<br>
		  </p>
				<p>11. PROCEDIMIENTO PARA ATENCIÓN Y RESPUESTA A PETICIONES, CONSULTAS, QUEJAS Y RECLAMOS DE LOS TITULARES DE DATOS PERSONALES<br>
				  Los Titulares de los Datos Personales que estén siendo recolectados, almacenados, utilizados, puestos en circulación por 109 apps SAS , podrán ejercer en cualquier momento sus derechos a conocer, actualizar, rectificar y suprimir información y revocar la autorización.<br>
				  Para el efecto, se seguirá el siguiente procedimiento, de conformidad con la Ley de Protección de Datos Personales:<br>
				  11. ATENCIÓN Y RESPUESTA A PETICIONES Y CONSULTAS:</p>
				<p>El Titular o sus beneficiario, podrán solicitar al respectivo club para que este haga la solicitud a 109 apps SAS, a través de los medios indicados más adelante:<br>
				  Información sobre los Datos Personales del Titular que son objeto de Tratamiento.<br>
				  Solicitar prueba de la autorización otorgada en la aplicación del club  para el Tratamiento de sus Datos Personales.<br>
  <br>
				  Información respecto del uso que se le ha dado por 109 apps SAS  a sus datos personales.<br>
				  Medios habilitados para la presentación de peticiones y consultas:<br>
				  109 apps SAS  ha dispuesto los siguientes medios para la recepción y atención de peticiones y consultas, todos los cuales permiten conservar prueba de las mismas:<br>
				  Comunicación dirigida a 109 apps 109 apps SAS . calle 109 # 18b-31 of 305 en la ciudad de Bogotá D.C.<br>
				  Solicitud presentada al correo electrónico: info@109 apps SAS .com.</p>
				<p>Atención y respuesta por parte de 109 apps SAS :<br>
				  Las peticiones y consultas serán atendidas en un término máximo de diez (10) días hábiles contados a partir de la fecha de recibo de las mismas. Cuando no fuere posible atender la petición o consulta dentro de dicho término, se informará al interesado, expresando los motivos de la demora y señalando en que se atenderá su petición o consulta, la cual en ningún caso podrá superar los cinco (5) días hábiles siguientes al vencimiento del primer término.<br>
		  </p>
				<p>12. INFORMACIÓN OBTENIDA EN FORMA PASIVA<br>
				  Cuando se accesa o utilizan los servicios contenidos dentro de los sitios web de 109 apps SAS , ésta podrá recopilar información en forma pasiva a través de tecnologías para el manejo de la información, a través de los cuales se recolecta información acerca del hardware y el software del equipo, dirección IP, tipo de dispositivo, sistema operativo, nombre de dominio, tiempo de acceso; mediante el uso de éstas herramientas no se recolectan directamente Datos Personales de los usuarios. También se recopilará información acerca de las secciones o servicios visitado en el app para efectos de conocer sus hábitos de navegación.</p>
				<p>13. SEGURIDAD DE LOS DATOS PERSONALES<br>
				  109 apps SAS , en estricta aplicación del Principio de Seguridad en el Tratamiento de Datos Personales, proporcionará las medidas técnicas, humanas y administrativas que sean necesarias para otorgar seguridad a los registros evitando su adulteración, pérdida, consulta, uso o acceso no autorizado o fraudulento. La obligación y responsabilidad de 109 apps SAS  se limita a disponer de los medios adecuados para este fin. 109 apps SAS  no garantiza la seguridad total de su información ni se responsabiliza por cualquier consecuencia derivada de fallas técnicas o del ingreso indebido por parte de terceros a la Base de Datos o Archivo en los que reposan los Datos Personales objeto de Tratamiento por parte de 109 apps SAS  y sus Encargados. 109 apps SAS  exigirá a los proveedores de servicios que contrata, la adopción y cumplimiento de las medidas técnicas, humanas y administrativas adecuadas para la protección de los Datos Personales en relación con los cuales dichos proveedores actúen como Encargados.<br>
		  </p>
				<p>14. TRANSFERENCIA, TRANSMISIÓN Y REVELACIÓN DE DATOS PERSONALES<br>
				  109 apps SAS  podrá entregar los Datos Personales a terceros no vinculados a 109 apps SAS  cuando: a. Se trate de contratistas en ejecución de contratos para el desarrollo de las actividades de 109 apps SAS ; b. Por transferencia a cualquier título de cualquier línea de negocio con la que se relaciona la información.<br>
				  En todo caso, en los contratos de transmisión de Datos Personales, que se suscriban entre 109 apps SAS  y los Encargados para el Tratamiento de Datos Personales, se exigirá que la información sea tratada conforme a esta Política de Protección de Datos Personales y se incluirán las siguientes obligaciones en cabeza del respectivo Encargado:<br>
				  • Dar Tratamiento, a nombre de 109 apps SAS  a los Datos Personales conforme los principios que los tutelan.<br>
				  • Salvaguardar la seguridad de las bases de datos en los que se contengan Datos Personales.<br>
				  • Guardar confidencialidad respecto del Tratamiento de los Datos Personales.</p>
				<p>15. LEGISLACIÓN APLICABLE<br>
				  Esta Política de Protección de Datos Personales, el Aviso de Privacidad, se rigen por lo dispuesto en la legislación vigente sobre protección de los Datos Personales a los que se refieren el Artículo 15 de la Constitución Política de Colombia, la Ley 1266 de 2008, la Ley 1581 de 2012, el Decreto 1377 de 2013, el Decreto 1727 de 2009 y demás normas que las modifiquen, deroguen o sustituyan.<br>
				  16. VIGENCIA<br>
				  Esta Política de Protección de Datos Personales está vigente desde el 1 de Abril  de 2016.</p>
<p>&nbsp;</p>

				</p>

		</div>
	</div>
</section>
<!-- End services section -->


<section class="section">
        <div class="container">
            <div class="columns">
                <div class="column is-half">

                    <form method="POST">

                        <h1 class="title">
                            reCAPTCHA v3 example
                        </h1>

                        <div class="field">
                            <label class="label">Name</label>
                            <div class="control">
                                <input type="text" name="name" class="input" placeholder="Name" required>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control">
                                <input type="email" name="email" class="input" placeholder="Email Address" required>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Message</label>
                            <div class="control">
                                <textarea name="message" class="textarea" placeholder="Message" required></textarea>
                            </div>
                        </div>

                        <div class="field is-grouped">
                            <div class="control">
                                <button type="submit" class="button is-link">Send Message</button>
                            </div>
                        </div>

                        <input type="text" name="recaptcha_response" id="recaptchaResponse">

                    </form>

                </div>
            </div>
        </div>
    </section>


<?
	include( "cmp/footer.php" );
?>
</body>
</html>
