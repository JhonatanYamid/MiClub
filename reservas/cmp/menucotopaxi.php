<header>
  <secction id="top_header">

  </secction>
  <div id="bottom_head">
    <div class="cont_central">

      <h1 id="logo">
        <a href="https://cotopaxi.k12.ec/">RESULTADO TRANSACCION</a>
      </h1>

      <div id="btn_menu_movil">
        <span></span>
        <span></span>
        <span></span>
      </div>

      <div id="btn_tels_movil"></div>

      <a href="https://academiacotopaxi.on.spiceworks.com/portal" id="btn_contacto_movil"></a>

      <div id="cont_tels_movil">
        <a href="tel:(593-2) 382 3270">(593-2) 382 3270</a>
      </div>

      <nav>
        <ul>
          <li>
            <a href="https://cotopaxi.k12.ec/">Inicio</a>
          </li>
          <li><a href="https://cotopaxi.k12.ec/">Cursos</a></li>

          <?php if (!empty($datos->IDSocio)) { ?>
            <li><a href="validausuario.php?action=Salir&IDClub=<?php echo SIMUser::get("club"); ?>">Salir</a></li>
          <?php } ?>


        </ul>
      </nav>

    </div>
  </div>
</header>