<header>
<secction id="top_header">

</secction>
<div id="bottom_head">
   <div class="cont_central">

       <h1 id="logo">
           <a href="https://www.ligadetenisdebogota.com/">RESULTADO TRANSACCION</a>
       </h1>

       <div id="btn_menu_movil">
          <span></span>
          <span></span>
          <span></span>
       </div>

       <div id="btn_tels_movil"></div>

       <a href="https://www.ligadetenisdebogota.com/contacto.html" id="btn_contacto_movil"></a>

       <div id="cont_tels_movil">
           <a href="tel:0313110405">311 0405</a>
           <a href="tel:0313112964">311 2964</a>
       </div>

       <nav>
           <ul>
           <li>
             <!--<a href="cursoinscripcion.php?IDClub=<?php echo $_GET["IDClub"]; ?>">Inicio</a></li>-->
             <a href="https://www.ligadetenisdebogota.com/">Inicio</a></li>
           <!--<li><a href="cursoinscripcion.php?IDClub=<?php echo $_GET["IDClub"]; ?>">Cursos</a></li>-->
           <li><a href="https://www.ligadetenisdebogota.com/vive-el-tenis">Cursos</a></li>

           <?php if(!empty($datos->IDSocio)){ ?>
             <li><a href="validausuario.php?action=Salir&IDClub=<?php echo SIMUser::get("club");?>">Salir</a></li>
           <?php } ?>


           </ul>
       </nav>

   </div>
</div>
</header>
