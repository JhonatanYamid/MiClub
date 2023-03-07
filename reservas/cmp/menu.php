<div class="boxnreserva">
    <span><b>
            <?php if (SIMUser::get('club') == 15) {
                echo 'Bienvenido al sistema de pagos';
            } elseif (SIMUser::get('club') == 154) {
                echo '';
            } else {
                echo 'Bienvenido al sistema de reservas';
            }
            ?>
        </b></span><br>
    <span class="nombre"><?php echo $datos->Nombre . " " . $datos->Apellido; ?></span>
</div>
<div class="btn-salir">
    <a class="btn btn-success btnsalir vermas" href="validausuario.php?action=Salir&IDClub=<?php echo SIMUser::get("club"); ?>">
        <i class="fa fa-sign-out" aria-hidden="true"></i> SALIR</a>
</div>

<!-- <span><b>Bienvenido al sistema de reservas</b></span>
<div class="boxnreserva">
    <span class="nombre"><?php echo $datos->Nombre . " " . $datos->Apellido; ?></span>
</div>
<div class="boxnreserva">
    <a class="nreserva vermas" href="seccion.php">NUEVA RESERVA</a>
    <?php if (SIMUser::get("club") == 25) : //Gun Club	
    ?>
        <a class="nreserva vermas" href="canje.php">CANJES</a>
        <a class="nreserva vermas" href="pqr.php">PQR</a>
        <a class="nreserva vermas" href="domicilios.php">DOMICILIOS</a>
        <a class="nreserva vermas" href="http://biblioteca.gunclub.com.co:8084/libre.html">BIBLIOTECA</a>
        <a class="nreserva vermas" href="noticias.php">INFORMACION</a>
        <a class="nreserva vermas" href="movimientos.php">MOVIMIENTOS</a>
        <a class="nreserva vermas" href="extractos.php">EXTRACTO</a>
    <?php endif;    ?>
    <a class="btnsalir vermas" href="validausuario.php?action=Salir&IDClub=<?php echo SIMUser::get("club"); ?>">SALIR</a>
</div> -->