<?
require("admin/config.inc.php");
include("plataform/procedures/consultaexento.php");
SIMUtil::cache();
session_start();

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script> -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script>
  function crear(nombre) {
    f = document.getElementById('doc')
    // f = document.getElementById('contact');
    // f = "<body><div style='background-color:red;'><p style='font-size:5pt;background-color:blue;'>Porr medio del presente documento, la FEDERACIÓN COLOMBIANA DE TRIATLÓN y su operador logístico EVENTOS DEPORTIVOS DE ALTO NIVEL SAS – XPORTIVA, identificada con Nit. 901.199.807-1 Certificamos que:</p></div></body>"
    // const pdf = new jsPDF('p', 'pt', 'a4');
    // pdf.html(f,{x:100}).then(() => pdf.save('fileName.pdf'));
    html2pdf().from(f).save();


    // doc.html("<h1>Buen día "+nombre+"</h1>, usted se encuentra exento",{
    //   x:10,
    //   y:10,
    // });
    // doc.save("CartaExento.pdf");
  }
</script>
<style>
  .slide_bijao {
    background: url("../img/bannercfoto.jpg") center center no-repeat;
    color: #FFFFFF;
    text-align: center;
    text-transform: uppercase;
    height: 300px;
  }

  .background-image {
    background-image: url('./plataform/images/bannerExento.png');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    width: 100%;
    height: 100%;

  }

  .field {
    font-size: 50px;
    padding-top: 40px;
    padding-bottom: 40px !important;
  }
</style>
</head>

<body>
  <!-- Start  section -->
  <!-- <div class="background-image"></div> -->
  <section id="contact" class="content h-100 d-flex align-items-center background-image" align="center">
    <div class="container-fluid my-auto " >
      <header class="text-white">
        <h1 class="fadeInDown font-weight-bold" style="font-size: 5rem;"><a name="contacto">Consultar Excepción</a></h1>
        <h5 class="fadeInUp" style="font-size: 3rem;" data-wow-delay="0.1s">Por favor ingrese el número de cedula.
        </h5>
      </header>
      <form class="row formvalida mt-4" method="post" action="<?php echo SIMUtil::lastURI() ?>">
        <div class="form-group col-12 justify-content-center">
          <input name="NumeroDocumento" type="text" placeholder="Numero Documento" class="form-control mandatory field input-lg" title="Numero Documento" required />
        </div>
        <div class="form-group col-12 justify-content-start">
          <input type="hidden" name="action" value="insert">
          <button class="btn btn-md btn-primary btn-block field" id="boton" style="background-color:#5D9732">Consultar</button>
        </div>
      </form>
      <div class="bg-white rounded py-4 px-5" style="font-size: 3rem;">
        <h5 id="result" style="font-size: 3rem;"><?= $texto ?></h5>
        <? if ($encontrado) : ?>
          <button class="btn btn-md btn-info btn-block" style="font-size: 3rem;" onclick="crear('<?= $nombre; ?>')">Exportar PDF</button>
        <?php endif ?>
      </div>

    </div>
    <div style="display:none">
      <div id="doc">
        <div class="container my-5">
          <div class="px-5 mx-2" style='font-family:Georgia,Times,"Times New Roman",serif;font-size:18px;'>
            <img src="./plataform/images/logos_patrocinadores.png" alt="" class="w-100 mb-5">
            <p class="text-justify">Por medio del presente documento, la FEDERACIÓN COLOMBIANA DE TRIATLÓN y su operador logístico EVENTOS DEPORTIVOS DE ALTO NIVEL SAS – XPORTIVA, identificada con Nit. 901.199.807-1 Certificamos que:<br><br>
              Nombre: <strong><?= $nombre; ?></strong><br>
              Documento de Identidad: <strong><?= $numerodocumento; ?></strong><br><br>
              Está inscrito(a) como competidor en la Copa Colombia de Triatlón – Xseries Tri San Andrés y Morgan Challenge, a realizarse en la isla de San Andrés del 24 al 26 de marzo de 2023; y que se encuentra incluído(a) en el listado de personas exoneradas de pago de la tarjeta de turismo de ingreso a la isla desde el 20 de marzo hasta el 25 de marzo de 2023, según circular No. del de Marzo de 2023 emitida por la Oficina de Control de Circulación y Residencia (OCCRE).<br><br>
              El control de ingreso a la isla lo hará la OCCRE en el aeropuerto internacional Gustavo Rojas Pinilla de San Andrés.<br><br><br>
              Juan Manuel Velasco<br>
              <strong>FEDERACIÓN COLOMBIANA DE TRIATLÓN</strong><br>
              (FEDECOLTRI)<br><br>

              Janos Kapitany<br>
              <strong>EVENTOS DEPORTIVOS DE ALTO NIVEL</strong><br>
              (XPORTIVA)<br>

            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- End contact section -->
  <?
  // include("cmp/footercolombia.php");
  ?>
</body>