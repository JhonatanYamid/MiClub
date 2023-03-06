<?
require("admin/config.inc.php");
include("plataform/procedures/registrosociodatosarsa.php");
SIMUtil::cache();
session_start();


include("cmp/seo.php");

$logo_club = CLUB_ROOT . $dbo->getFields("Club", "FotoDiseno1", "IDClub = '" . $_GET["IDClub"] . "'");
$tamano = getimagesize($ruta_logo_club);
$ancho = $tamano[0];              //Ancho
$alto = $tamano[1];
if ($ancho > 155) :
    $tamano_logo = 'width="155" height="80"';
endif;


?>

<body>

    <!-- Start  section -->
    <section id="contact">

        <div class="container">

            <header>
                <img class="boxlogo" src="<?php echo $logo_club; ?>" width="100" height="100" />
                <h2 style="color:#5D9732">Registro Socios</h2>


            </header>

            <?php if (!empty($_GET['mensaje'])) : ?>
                <center>
                    <h4 style="color:black"><?php echo $_GET['mensaje'] ?></h4>
                </center>
            <?php else : ?>

                <form class="row formvalida" method="post" action="<?php echo SIMUtil::lastURI() ?>">


                    <div class="form-group col-md-6">
                        <label class="col-sm-6 control-label no-padding-right" for="Nombre">Nombre</label>
                        <input type="text" name="Nombre" id="Nombre" placeholder="Nombre" class="form-control" title="Nombre" required>


                    </div>

                    <div class="form-group col-md-6">
                        <label class="col-sm-6 control-label no-padding-right" for="Apellido">Apellidos</label>
                        <input type="text" name="Apellido" id="Apellido" placeholder="Apellido" class="form-control" title="Apellido" required>


                    </div>

                    <div class="form-group col-md-6">
                        <label class="col-sm-6 control-label no-padding-right" for="Apellido">Tipo Documento</label>
                        <input type="text" name="TipoDocumento" id="TipoDocumento" placeholder="Tipo Documento" class="form-control mandatory" title="Tipo Documento" required>


                    </div>

                    <div class="form-group col-md-6">
                        <label class="col-sm-6 control-label no-padding-right" for="Email">Número De Documento <br> (Será su usuario y clave para el App)</label>
                        <input type="text" name="Email" id="Email" placeholder="Número De Documento" class="form-control" title="NumeroDocumento" required>
                        <h5 id="mensajeNumeros" style="color: red;" class="text-center"></h5>

                    </div>




                    <div class="form-group col-md-6">
                        <label class="col-sm-6 control-label no-padding-right" for="Direccion">Dirección Completa</label>
                        <input type="text" name="Direccion" id="Direccion" placeholder="Dirección Completa " class="form-control" title="Direccion" required>


                    </div>

                    <div class="form-group col-md-6">
                        <label class="col-sm-6 control-label no-padding-right" for="CorreoElectronico">Correo Electrónico </label>
                        <input type="email" name="CorreoElectronico" id="CorreoElectronico" placeholder="Correo Electrónico " class="form-control" title="Correo Electrónico" data-validation="email" required>
                        <div id="xmail" class="hide">
                            <h6 class="text-danger">Ingresa un email valido</h6>
                        </div>
                    </div>


                    <div class="form-group col-md-6">
                        <label class="col-sm-6 control-label no-padding-right" for="Telefono">Teléfono</label>
                        <input type="text" name="Telefono" id="Telefono" placeholder="Teléfono" class="form-control mandatory" title="Teléfono">


                    </div>


                    <div class="form-group col-md-6">
                        <label class="col-sm-6 control-label no-padding-right" for="Celular">Celular</label>
                        <input type="text" name="Celular" id="Celular" placeholder="Celular" class="form-control mandatory" title="Celular" required>


                    </div>

        </div>


        <div class="form-group col-md-12 ">

            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $_GET['IDClub'] ?>" />
            <input type="hidden" name="IDSocio" value="<?php echo $datos_socio["IDSocio"]; ?>" id="IDSocio" title="Socio">


        </div>



        <div class="form-group col-md-12">
            <input type="hidden" name="action" value="insert">
            <input type="hidden" name="IDEstadoSocio" value="1">
            <input type="hidden" name="PermiteReservar" value="S">
            <button class="btn btn-lg btn-primary" style="background-color:#5D9732">Enviar</button>
        </div>

        </form>
    <?php endif; ?>

    </section>



</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    //funcion que solo permite numeros en el documento
    function solonumeros(e) {

        key = e.keyCode || e.which;
        teclado = String.fromCharCode(key);
        numeros = "0123456789";
        especiales = "8-37-38-46"; //array
        teclado_especial = false;

        for (var i in especiales) {
            if (key == especiales[i]) {
                teclado_especial = true;
                mensaje.textContent = "";
            }
        }

        if (numeros.indexOf(teclado) == -1 && !teclado_especial) {
            return false;
        }
    }

    // funcion para validar el correo
    function caracteresCorreoValido(email, div) {
        // console.log(email);
        //var email = $(email).val();


        var caract = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);

        if (caract.test(email) == false) {
            $(div).hide().removeClass('hide').slideDown('fast');

            document.getElementById("CorreoElectronico").value = "";
            document.getElementById("CorreoElectronico").focus();
            return false;
        } else {
            $(div).hide().addClass('hide').slideDown('slow');

            return true;
        }
    }

    // cuando pierde el foco, este valida si lo que esta en el campo de texto si es un correo o no y muestra una respuesta
    $('form').find('input[type=email]').blur(function() {
        caracteresCorreoValido($(this).val(), '#xmail')
    });
</script>

</html>