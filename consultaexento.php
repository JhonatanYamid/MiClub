<?
require("admin/config.inc.php");
include( "plataform/procedures/consultaexento.php" );
SIMUtil::cache();
session_start();

?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script>
  function crear(nombre) {
    const doc = new jsPDF();
    doc.text("Buen día "+nombre+", usted se encuentra exento", 10, 10);
    doc.save("CartaExento.pdf");
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
    position: fixed;
    left: 0;
    right: 0;
    z-index: 1;
    display: block;
    background-image: url('./plataform/images/bannerExento.jpg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    width: 100%;
    height: 100%;
    -webkit-filter: blur(5px);
    -moz-filter: blur(5px);
    -o-filter: blur(5px);
    -ms-filter: blur(5px);
    filter: blur(3px) brightness(40%);
  }

  .content {
    position: fixed;
    left: 0;
    right: 0;
    z-index: 9999;
    margin-left: 20px;
    margin-right: 20px;
  }
</style>
</head>

<body>
  <!-- Start  section -->
  <div class="background-image"></div>
  <section id="contact" class="content h-100 d-flex align-items-center" align="center">
    <div class="container my-auto ">
      <header class="text-white">
        <h1 class="fadeInDown font-weight-bold" style="font-size: 3rem;"><a name="contacto">Consultar Excepción</a></h1>
        <h5 class="fadeInUp" style="font-size: 1.3rem;" data-wow-delay="0.1s">Por favor ingrese el número de cedula.
        </h5>
      </header>
      <form class="row formvalida mt-4" method="post" action="<?php echo SIMUtil::lastURI() ?>">
        <div class="form-group offset-lg-2 col-lg-5 col-12 justify-content-center">
          <input name="NumeroDocumento" type="text" placeholder="Numero Documento" class="form-control mandatory input-lg" title="Numero Documento" required />
        </div>
        <div class="form-group col-lg-3 col-12 justify-content-start">
          <input type="hidden" name="action" value="insert">
          <button class="btn btn-md btn-primary btn-block" id="boton" style="background-color:#5D9732">Enviar</button>
        </div>
      </form>
      <div class="bg-white rounded py-4 px-5">
        <h5 id="result"><?= $texto ?></h5>
        <? if($encontrado): ?> 
        <button class="btn btn-md btn-info btn-block" onclick="crear('<?= $nombre; ?>')">Exportar PDF</button>
        <?php endif ?>
      </div>

    </div>
  </section>
  <!-- End contact section -->
  <?
  include("cmp/footercolombia.php");
  ?>
</body>