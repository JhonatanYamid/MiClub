<div id="sidebar" class="sidebar responsive">
    <script type="text/javascript">
        try {
            ace.settings.check('sidebar', 'fixed')
        } catch (e) {}
    </script>

    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">&nbsp;</div>
        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>

            <span class="btn btn-info"></span>

            <span class="btn btn-warning"></span>

            <span class="btn btn-danger"></span>
        </div>
    </div><!-- /.sidebar-shortcuts -->

    <ul class="nav nav-list">

        <?php
        $con_sesion = "S";
        $club_selecc = SIMUser::get("club");
        $permiso = SIMUtil::verificar_permiso_modulo("Socio", SIMUser::get("IDPerfil"));
        if (isset($club_selecc) && $permiso == 0) { ?>
            <li class="<?php if ($script == "socios") echo "active"; ?>">
                <a href="socios.php">
                    <i class="menu-icon fa fa-user"></i>
                    <?php
                    if ($tipo_club == 1) : //Club 
                    ?>
                        <?= SIMUtil::get_traduccion('', '', 'Socios', LANGSESSION); ?>
                    <?php
                    elseif ($tipo_club == 2) : ?>
                        <?= SIMUtil::get_traduccion('', '', 'Propietarios', LANGSESSION); ?>
                    <?php
                    elseif ($tipo_club == 3) : ?>
                        <?= SIMUtil::get_traduccion('', '', 'Usuarios', LANGSESSION); ?>
                    <?php
                    else :
                        $con_sesion = "N";
                    endif; ?>
                </a>
                <b class="arrow"></b>
            </li>
        <?php
        } ?>

        <?php
        if ($con_sesion == "S") { ?>
            <?php
            if (SIMUser::get('club') == 220) {
                //solamente para los administradores del club y el superusuario IDPerfil = 0
                // $permiso = SIMUtil::verificar_permiso_modulo("PantallaAcceso2", SIMUser::get("IDPerfil"));
                if (isset($club_selecc)) { ?>
                    <li class="<?php if ($script == "PantallaAcceso") echo "active"; ?>">
                        <a href="../../acceso/accesoinvitado.php">
                            <i class="menu-icon  fa fa-car"></i>
                            <?= SIMUtil::get_traduccion('', '', 'PantallaAcceso', LANGSESSION); ?> 2
                        </a>
                        <b class="arrow"></b>
                    </li>
            <?php
                }
            } ?>
            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("PerfilesInfinito", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "registrosdinamicos" || $script == "configuracionregistrosdinamicos") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-users"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'PerfilesInfinito', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="registrosdinamicos.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Registros', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>



                        <li class="">
                            <a href="configuracionregistrosdinamicos.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                </li>
            <?php
            } ?>


            <?php
            //solamente para los administradores del club y el superusuario IDPerfil = 0
            $permiso = SIMUtil::verificar_permiso_modulo("BannerApp", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "banners") echo "active"; ?>">
                    <a href="banners.php">
                        <i class="menu-icon fa  fa-file-image-o"></i>
                        <?= SIMUtil::get_traduccion('', '', 'Splash', LANGSESSION); ?>
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php
            } ?>

            <?php
            //solamente para los administradores del club y el superusuario IDPerfil = 0
            $permiso = SIMUtil::verificar_permiso_modulo("Noticia", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>



                <li class="<?php if ($script == "seccionnoticias" || $script == "noticias" || $script == "seccionnoticias2" || $script == "noticias2" || $script == "seccionnoticias3" || $script == "noticias3" || $script == "seccionnoticiasinfinita" || $script == "noticiasinfinita" || $script == "configuracionnoticiasinfinita") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-newspaper-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Noticias', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Noticias', LANGSESSION); ?> 1 </span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="noticias.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Noticias', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="seccionnoticias.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'SeccionNoticias', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Noticias', LANGSESSION); ?> 2</span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="noticias2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Noticias', LANGSESSION); ?> 2
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="seccionnoticias2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'SeccionNoticias', LANGSESSION); ?> 2
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Noticias', LANGSESSION); ?> 3</span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="noticias3.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Noticias', LANGSESSION); ?> 3
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="seccionnoticias3.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'SeccionNoticias', LANGSESSION); ?> 3
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>


                        <?php
                        $sql = "SELECT * FROM Modulo WHERE Tipo = 'Noticias'";
                        $query = $dbo->query($sql);

                        while ($Datos = $dbo->fetchArray($query)) {
                            $ArrayidModulo[] = $Datos["IDModulo"];
                        }
                        $DatosArray = implode(",", $ArrayidModulo);

                        $IDClub = SIMUser::get("club");
                        $sql1 = "SELECT * FROM ClubModulo WHERE IDClub = '$IDClub' and IDModulo in ($DatosArray) and Activo = 'S'";

                        $query1 = $dbo->query($sql1);
                        while ($Datos2 = $dbo->fetchArray($query1)) {
                            $permiso = SIMUtil::verificar_permisos_IDModulo(SIMUser::get("IDPerfil"), $Datos2[IDModulo]);
                            if (isset($club_selecc) && $permiso == 0) :
                        ?>
                                <li class="">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <span class="menu-text"><?php echo $Datos2["TituloLateral"] ?></span>
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <ul class="submenu">
                                        <li class="">
                                            <a href="noticiasinfinita.php?IDModulo=<?php echo $Datos2["IDModulo"] ?>">
                                                <i class="menu-icon fa fa-caret-right"></i>
                                                <?= SIMUtil::get_traduccion('', '', 'Noticias', LANGSESSION); ?>

                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                        <li class="">
                                            <a href="seccionnoticiasinfinita.php?IDModulo=<?php echo $Datos2["IDModulo"] ?>">
                                                <i class="menu-icon fa fa-caret-right"></i>
                                                <?= SIMUtil::get_traduccion('', '', 'SeccionNoticias', LANGSESSION); ?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                    </ul>
                                </li>
                        <?php
                            endif;
                        } ?>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'NoticiasInfinitas', LANGSESSION); ?></span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">

                                <li class="">
                                    <a href="configuracionnoticiasinfinita.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>


                    </ul>
                </li>







            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Evento", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "seccioneventos" || $script == "eventos" || $script == "seccioneventos2" || $script == "eventos2" || $script == "configuracionevento") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-calendar-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Eventos', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Eventos', LANGSESSION); ?> 1</span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="eventos.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Eventos', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="seccioneventos.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'SeccionEventos', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="configuracionevento.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'ConfiguracionEventos', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Eventos', LANGSESSION); ?> 2</span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="eventos2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Eventos', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="seccioneventos2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'SeccionEventos', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="configuracionevento.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionEventos', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>






            <?php
            } ?>

            <?php
            //solamente para los administradores del club y el superusuario IDPerfil = 0
            $permiso = SIMUtil::verificar_permiso_modulo("CuotasSociales", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "cuotassociales" || $script == "configuracioncuotassociales"  || $script == "fechacuotassociales" || $script == "categoria") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-newspaper-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'CuotasSociales', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="cuotassociales.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'CuotasSociales', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="fechacuotassociales.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'FechasCuotasSociales', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="categoria.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'CategoriasSocios', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="configuracioncuotassociales.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionCuotassociales', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                <?php
            } ?>
                <?php
                //solamente para los administradores del club y el superusuario IDPerfil = 0
                $permiso = SIMUtil::verificar_permiso_modulo("Ingresos", SIMUser::get("IDPerfil"));
                if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "ingresos" || $script == "ingresossolicitud"  || $script == "ingresospreguntas" || $script == "configuracioningresos") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-newspaper-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Ingresos', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="ingresossolicitud.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Ingresossolicitud', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracioningresos.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionIngresos', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                <?php
                } ?>
                <?php
                //solamente para los administradores del club y el superusuario IDPerfil = 0
                $permiso = SIMUtil::verificar_permiso_modulo("TarjetaRotativa", SIMUser::get("IDPerfil"));
                if (isset($club_selecc) && $permiso == 0 && SIMUser::get('club') == 8 || SIMUser::get('club') == 220) { ?>
                <li class="<?php if ($script == "tipotarjetarotativa" || $script == "tarjetarotativa" || $script == "configuraciontarjetarotativa") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-ticket"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'tarjetarotativa', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="tipotarjetarotativa.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'tipotarjetarotativa', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="tarjetarotativa.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'tarjetarotativa', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <!-- <li class="">
                            <a href="configuraciontarjetarotativa.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuraciontarjetarotativa', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li> -->


                    </ul>
                <?php
                } ?>

                <?php
                $permiso = SIMUtil::verificar_permiso_modulo("Galeria", SIMUser::get("IDPerfil"));
                if (isset($club_selecc) && $permiso == 0) { ?>


                <li class="<?php if ($script == "secciongalerias" || $script == "galerias" || $script == "secciongalerias2" || $script == "galerias2") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-th-list"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Galerias', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Galeria', LANGSESSION); ?> 1</span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="galerias.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Galerias', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="secciongalerias.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'SeccionGalerias', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Galeria', LANGSESSION); ?> 2</span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="galerias2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Galerias', LANGSESSION); ?> 2
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="secciongalerias2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'SeccionGalerias', LANGSESSION); ?> 2
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>





            <?php
                } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Directorio", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "directorio" || $script == "directoriosocio" || $script == "categoriadirectorio" || $script == "categoriadirectoriosocio") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-book"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Directorios', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'DirectorioClub', LANGSESSION); ?></span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="directorio.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Directorio', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="categoriadirectorio.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Categorias', LANGSESSION); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'DirectorioSocio', LANGSESSION); ?></span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="directoriosocio.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Directorio', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="categoriadirectoriosocio.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Categorias', LANGSESSION); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Documento", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "documentos" || $script == "tipoarchivos" || $script == "documentos2" || script == "tipoarchivos2" || $script == "documentos3" || $script == "tipoarchivos3" || $script == "documentosinfinito" || $script == "tipoarchivosinfinito") echo "active"; ?> ">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-file-text-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Documentos', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-file-text-o"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Documentos', LANGSESSION); ?> 1</span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>
                            <ul class="submenu">
                                <li class="">
                                    <a href="documentos.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Archivos', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="tipoarchivos.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'TipoArchivo', LANGSESSION); ?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-file-text-o"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Documentos', LANGSESSION); ?> 2</span>

                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">

                                <li class="">
                                    <a href="documentos2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Archivos', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>


                                <li class="">
                                    <a href="tipoarchivos2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'TipoArchivo', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="">

                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-file-text-o"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Documentos', LANGSESSION); ?> 3</span>

                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">

                                <li class="">
                                    <a href="documentos3.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Archivos', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>


                                <li class="">
                                    <a href="tipoarchivos3.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'TipoArchivo', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="">

                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-file-text-o"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Documentosinfinitos', LANGSESSION); ?></span>

                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">


                                <?php $sqlDocumentos = "SELECT * FROM Modulo WHERE Tipo = 'Documentos'";
                                //echo $sql;
                                $queryDocumentos = $dbo->query($sqlDocumentos);


                                while ($DatosDocumentos = $dbo->fetchArray($queryDocumentos)) {
                                    $ArrayidModuloDocumentos[] = $DatosDocumentos["IDModulo"];
                                }
                                $DatosArrayDocumentos = implode(",", $ArrayidModuloDocumentos);
                                // print_r($DatosArray);
                                $IDClub = SIMUser::get("club");
                                $sqlDocumentos1 = "SELECT * FROM ClubModulo WHERE IDClub = '$IDClub' and IDModulo in ($DatosArrayDocumentos) and Activo = 'S'";
                                $qry = $dbo->query($sqlDocumentos1);

                                if ($dbo->rows($qry) == 0) :
                                    $sqlDocumentos1 = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '$IDClub' and IDModulo in ($DatosArrayDocumentos) and Activo = 'S'";
                                endif;
                                //echo $sql1;
                                $queryDocumentos1 = $dbo->query($sqlDocumentos1);
                                while ($DatosDocumentos2 = $dbo->fetchArray($queryDocumentos1)) {
                                    $permiso = SIMUtil::verificar_permisos_IDModulo(SIMUser::get("IDPerfil"), $DatosDocumentos2[IDModulo]);
                                    if (isset($club_selecc) && $permiso == 0) :
                                ?>
                                        <li class="">
                                            <a href="#" class="dropdown-toggle">
                                                <i class="menu-icon fa fa-caret-right"></i>
                                                <span class="menu-text"><?php echo $DatosDocumentos2["TituloLateral"] ?></span>
                                                <b class="arrow fa fa-angle-down"></b>
                                            </a>
                                            <ul class="submenu">
                                                <li class="">
                                                    <a href="documentosinfinito.php?IDModulo=<?php echo  $DatosDocumentos2["IDModulo"] ?>">
                                                        <i class="menu-icon fa fa-caret-right"></i>
                                                        <?= SIMUtil::get_traduccion('', '', 'Archivos', LANGSESSION); ?>

                                                    </a>
                                                    <b class="arrow"></b>
                                                </li>
                                                <li class="">
                                                    <a href="tipoarchivosinfinito.php?IDModulo=<?php echo  $DatosDocumentos2["IDModulo"] ?>">
                                                        <i class="menu-icon fa fa-caret-right"></i>
                                                        <?= SIMUtil::get_traduccion('', '', 'TipoArchivo', LANGSESSION); ?>
                                                    </a>
                                                    <b class="arrow"></b>
                                                </li>
                                            </ul>
                                        </li>
                                <?php
                                    endif;
                                } ?>
                            </ul>
                        </li>

                    </ul>
                </li>
            <?php
            }

            /* $permiso=SIMUtil::verificar_permiso_modulo( "Reglamentaciones2" , SIMUser::get("IDPerfil") );
		if( isset($club_selecc) && $permiso==0)
		{ ?>

            <li class="<?php if($script=="documentos2" || $script=="tipoarchivos2") echo "active"; ?>">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-file-text-o"></i>
                    <span class="menu-text">Documentos 2</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">

                    <li class="">
                        <a href="documentos2.php">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Archivos
                        </a>

                        <b class="arrow"></b>
                    </li>


                    <li class="">
                        <a href="tipoarchivos2.php">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Tipo Archivo
                        </a>

                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
        <?php
        } */

            /* $permiso=SIMUtil::verificar_permiso_modulo( "Reglamentaciones3" , SIMUser::get("IDPerfil") );
		if( isset($club_selecc) && $permiso==0)
		{ ?>

        <li class="<?php if($script=="documentos3" || $script=="tipoarchivos3") echo "active"; ?>">

            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-file-text-o"></i>
                <span class="menu-text">Documentos 3</span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">

                <li class="">
                    <a href="documentos3.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Archivos
                    </a>

                    <b class="arrow"></b>
                </li>


                <li class="">
                    <a href="tipoarchivos3.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Tipo Archivo
                    </a>

                    <b class="arrow"></b>
                </li>
            </ul>
        </li>
        <?php
        } */

            /*  $permiso = SIMUtil::verificar_permiso_modulo("Documento", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>


                <li class="<?php if ($script == "documentosinfinito" || $script == "tipoarchivosinfinito") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-file-text-o"></i>
                        <span class="menu-text">Documentos infinitos</span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">


                        <?php $sqlDocumentos = "SELECT * FROM Modulo WHERE Tipo = 'Documentos'";
                        //echo $sql;
                        $queryDocumentos = $dbo->query($sqlDocumentos);


                        while ($DatosDocumentos = $dbo->fetchArray($queryDocumentos)) {
                            $ArrayidModuloDocumentos[] = $DatosDocumentos["IDModulo"];
                        }
                        $DatosArrayDocumentos = implode(",", $ArrayidModuloDocumentos);
                        // print_r($DatosArray);
                        $IDClub = SIMUser::get("club");
                        $sqlDocumentos1 = "SELECT * FROM ClubModulo WHERE IDClub = '$IDClub' and IDModulo in ($DatosArrayDocumentos) and Activo = 'S'";
                        //echo $sql1;
                        $queryDocumentos1 = $dbo->query($sqlDocumentos1);
                        while ($DatosDocumentos2 = $dbo->fetchArray($queryDocumentos1)) {
                            //echo "hola" . $Datos2["Titulo"];


                        ?>
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text"><?php echo $DatosDocumentos2["TituloLateral"] ?></span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">
                                        <a href="documentosinfinito.php?IDModulo=<?php echo  $DatosDocumentos2["IDModulo"] ?>">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Archivos

                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <li class="">
                                        <a href="tipoarchivosinfinito.php?IDModulo=<?php echo  $DatosDocumentos2["IDModulo"] ?>">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Tipo Archivo
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php
            } */

            $permiso = SIMUtil::verificar_permiso_modulo("DocumentoPersonal", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "documentospersonal") echo "active"; ?>">
                    <a href="documentospersonal.php">
                        <i class="menu-icon fa fa fa-file-text-o"></i>
                        <?= SIMUtil::get_traduccion('', '', 'Documentospersonales', LANGSESSION); ?>
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Publicidad", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "plubicidad") echo "active"; ?>">
                    <a href="publicidad.php">
                        <i class="menu-icon fa fa-video-camera"></i>
                        <?= SIMUtil::get_traduccion('', '', 'BannerPublicidad', LANGSESSION); ?>
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Faqs", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "faqs" || $script == "faqssolicitudes"  || $script == "categoriafaqs" || $script == "configuracionfaqs") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-filter"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Faqs', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="faqssolicitudes.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Nuevaspreguntas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <?php if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1 || SIMUser::get("IDPerfil") == 14)) { ?>
                            <li class="">
                                <a href="faqs.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Faqs', LANGSESSION); ?>
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="">
                                <a href="categoriafaqs.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Categorias', LANGSESSION); ?>
                                </a>
                                <b class="arrow"></b>
                            </li>

                            <li class="">
                                <a href="configuracionfaqs.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                </a>
                                <b class="arrow"></b>
                            </li>
                        <?
                        } ?>

                    </ul>
                </li>
            <?php
            } ?>



            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Socios", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0 && $IDClub == 8) { ?>
                <li class="<?php if ($script == "gamegolfcourse") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-caret-right"></i>
                        <span class="menu-text">GameGolf</span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="">
                            <a href="gamegolfcourse.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Campos
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="gamegolfcategoriaformato.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Categoría
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="gamegolfformato.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Formatos
                            </a>
                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
            <?php
            } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Laboral", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "laboralvacaciones" || $script == "laboralpermisos"  || $script == "laboralcertificados" || $script == "configuracionlaboral" || $script == "laboralcompensaciones" || $script == "laboralextractos" || $script == "laboralvacacionespendientes") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-briefcase"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Laboral', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="laboralvacaciones.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Vacaciones', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="laboralpermisos.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Permisos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="laboralcertificados.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Certificados', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="laboralcompensaciones.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Compensaciones', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="laboralextractos.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Extractos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="laboralvacacionespendientes.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'CargaVacacionesPtes', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracionlaboral.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("CheckinLaboral", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "chekinlaboral" || $script == "configuracionchekinlaboral") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-th-list"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'CheckinLaboral', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="chekinlaboral.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Reporte', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="sitiotrabajo.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Sitiodetrabajo', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracionchekinlaboral.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("CheckinFuncionarios", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "checkinfuncionarios" || $script == "dianolaboral" || $script == "turnos" || $script == "planificaciondiaria") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-th-list"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'CheckinFuncionarios', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">

                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-th-list"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?></span>

                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">

                                <?php
                                $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoTurnos");
                                if (isset($club_selecc) && $permiso == 0) : ?>
                                    <li class="">
                                        <a href="turnos.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Turnos', LANGSESSION); ?>
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                <?php endif; ?>


                                <?php
                                $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoDiaNoLaboral");
                                if (isset($club_selecc) && $permiso == 0) : ?>
                                    <li class="">
                                        <a href="dianolaboral.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'dianolaboral', LANGSESSION); ?>
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoPlanificacionDiaria");
                                if (isset($club_selecc) && $permiso == 0) : ?>
                                    <li class="">
                                        <a href="planificaciondiaria.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'planificaciondiaria', LANGSESSION); ?>
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                <?php endif; ?>
                            </ul>

                        </li>

                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoCheckinFuncionarios");
                        if (isset($club_selecc) && $permiso == 0) : ?>
                            <li class="">
                                <a href="checkinfuncionarios.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'CheckinFuncionarios', LANGSESSION); ?>
                                </a>
                                <b class="arrow"></b>
                            </li>
                        <?php endif; ?>

                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoNovedades");
                        if (isset($club_selecc) && $permiso == 0) : ?>
                            <li class="">
                                <a href="novedadfuncionarios.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Novedades', LANGSESSION); ?>
                                </a>
                                <b class="arrow"></b>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php
            } ?>

            <!-- auxilios infinitos -->
            <?php
            //solamente para los administradores del club y el superusuario IDPerfil = 0
            $permiso = SIMUtil::verificar_permiso_modulo("Auxilios", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "tiempoparamisolicitudinfinito" || $script == "tiempoparamiinfinito"  || $script == "tiempoparamirechazoinfinito" || $script == "configuraciontiempoparamiinfinito" || $script == "tipoauxilioinfinito") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-newspaper-o"></i>
                        <span class="menu-text">Auxilio Infinito </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">


                        <?php
                        $sqlAuxilios = "SELECT * FROM Modulo WHERE Tipo = 'Auxilios'";
                        $queryAuxilios = $dbo->query($sqlAuxilios);

                        while ($DatosAuxilios = $dbo->fetchArray($queryAuxilios)) {
                            $ArrayidModuloAuxilios[] = $DatosAuxilios["IDModulo"];
                        }

                        $DatosArrayAuxilios = implode(",", $ArrayidModuloAuxilios);

                        $IDClub = SIMUser::get("club");
                        $sql1Auxilios = "SELECT * FROM ClubModulo WHERE IDClub = '$IDClub' and IDModulo in ($DatosArrayAuxilios) and Activo = 'S'";



                        $query1Auxilios = $dbo->query($sql1Auxilios);
                        while ($Datos2Auxilios = $dbo->fetchArray($query1Auxilios)) {

                        ?>

                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text"><?php echo $Datos2Auxilios["TituloLateral"] ?></span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">

                                        <a href="tiempoparamisolicitudinfinito.php?IDModulo=<?php echo $Datos2Auxilios["IDModulo"] ?>">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'TiemposSolicitud', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="tiempoparamiinfinito.php?IDModulo=<?php echo $Datos2Auxilios["IDModulo"] ?>">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Tiempos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>
                                    <li class="">
                                        <a href="tiempoparamirechazoinfinito.php?IDModulo=<?php echo $Datos2Auxilios["IDModulo"] ?>">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'TipoRechazos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>
                                    <li class="">
                                        <a href="configuraciontiempoparamiinfinito.php?IDModulo=<?php echo $Datos2Auxilios["IDModulo"] ?>">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguracionTiempos', LANGSESSION); ?>
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>



                        <?php

                        } ?>
                    </ul>
                </li>

            <?php
            } ?>




            <?php
            //solamente para los administradores del club y el superusuario IDPerfil = 0
            $permiso = SIMUtil::verificar_permiso_modulo("Auxilios", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "auxilios" || $script == "auxiliossolicitud"  || $script == "auxiliosrechazo" || $script == "configuracionauxilios" || $script == "tipoauxilio") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-exclamation-circle"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Auxilios', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="auxiliossolicitud.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'AuxiliosSolicitud', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="auxilios.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Auxilios', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="tipoauxilio.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TipoAuxilios', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="auxiliosrechazo.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TipoRechazos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="configuracionauxilios.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionAuxilios', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                <?php
            } ?>

                <?php
                //solamente para los administradores del club y el superusuario IDPerfil = 0
                $permiso = SIMUtil::verificar_permiso_modulo("AuxiliosInfinito1", SIMUser::get("IDPerfil"));
                if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "tiempoparamisolicitud" || $script == "tiempoparami"  || $script == "tiempoparamirechazo" || $script == "configuraciontiempoparami") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-newspaper-o"></i>
                        <span class="menu-text">Auxilio infinito 2</span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="tiempoparamisolicitud2.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TiemposSolicitud', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="tiempoparami2.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Tiempos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="tiempoparamirechazo2.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TipoRechazos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="configuraciontiempoparami2.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionTiempos', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                <?php
                } ?>


                <?php
                //solamente para los administradores del club y el superusuario IDPerfil = 0
                $permiso = SIMUtil::verificar_permiso_modulo("AuxiliosInfinito1", SIMUser::get("IDPerfil"));
                if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "tiempoparamisolicitud" || $script == "tiempoparami"  || $script == "tiempoparamirechazo" || $script == "configuraciontiempoparami") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-newspaper-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Tiemposparami', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="tiempoparamisolicitud.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TiemposSolicitud', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="tiempoparami.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Tiempos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="tiempoparamirechazo.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TipoRechazos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="configuraciontiempoparami.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionTiempos', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                <?php
                } ?>
                <?php
                $permiso = SIMUtil::verificar_permiso_modulo("Vacunacion", SIMUser::get("IDPerfil"));
                if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "vacunacion" || $script == "configuracionvacunacion" || $script == "vacunaentidad" || $script == "vacunacion" || $script == "configuracionvacunacion2" || $script == "vacunaentidad2" || $script == "dosis" || $script == "campovacunacion" || $script == "reportevacunacion2.php") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-plus-square"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'Vacunación', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-plus-square"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Vacunación', LANGSESSION); ?> 1</span>

                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">

                                <li class="">
                                    <a href="reportevacunacion2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Vacunación', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="socios.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'InfoSocio', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="vacunaentidad.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Entidades', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="preguntasvacunacion.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Preguntas', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="configuracionvacunacion.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-plus-square"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Vacunación', LANGSESSION); ?> 2</span>

                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">

                                <li class="">
                                    <a href="reportevacunacion2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Vacunación', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="socios.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'InfoSocio', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="campovacunacion.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'CampoVacunacion', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="vacunaentidad2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Entidades', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="dosis.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Dosis', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <li class="">
                                    <a href="configuracionvacunacion2.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            <?php
                } ?>

            <?php
            /* $permiso = SIMUtil::verificar_permiso_modulo("Vacunacion", SIMUser::get("IDPerfil"));
        if (isset($club_selecc) && $permiso == 0) { ?>

            <li class="<?php if ($script == "vacunacion" || $script == "configuracionvacunacion2" || $script == "vacunaentidad2" || $script == "dosis" || $script == "campovacunacion") echo "active"; ?>">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-plus-square"></i>
                    <span class="menu-text">Vacunación 2</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">

                    <li class="">
                        <a href="socios.php">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Vacunación
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li class="">
                        <a href="campovacunacion.php">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Campo Vacunación
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li class="">
                        <a href="vacunaentidad2.php">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Entidades
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li class="">
                        <a href="dosis.php">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Dosis
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li class="">
                        <a href="configuracionvacunacion2.php">
                            <i class="menu-icon fa fa-caret-right"></i>
                            Configuracion General
                        </a>

                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
        <?php
        }  */ ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Restaurante", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "restaurantes") echo "active"; ?>">
                    <a href="restaurantes.php">
                        <i class="menu-icon fa  fa-cutlery"></i>
                        <?= SIMUtil::get_traduccion('', '', 'Restaurante', LANGSESSION); ?>
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("RestauranteInfinito", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "restaurantesinfinito" || $script == "restaurantesinfinito") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-cutlery"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'RestauranteInfinito', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <?php $sqlRestaurantes = "SELECT IDModulo FROM Modulo WHERE Tipo = 'Restaurantes'";
                        //echo $sql;
                        $queryRestaurantes = $dbo->query($sqlRestaurantes);


                        while ($DatosRestaurantes = $dbo->fetchArray($queryRestaurantes)) {
                            $ArrayidModuloRestaurantes[] = $DatosRestaurantes["IDModulo"];
                        }
                        $DatosArrayRestaurantes = implode(",", $ArrayidModuloRestaurantes);

                        $IDClub = SIMUser::get("club");
                        $sql1Restaurantes = "SELECT TituloLateral,IDModulo FROM ClubModulo WHERE IDClub = '$IDClub' and IDModulo in ($DatosArrayRestaurantes) and Activo = 'S'";
                        //echo $sql1;
                        $query1Restaurantes = $dbo->query($sql1Restaurantes);
                        while ($DatosRestaurantes = $dbo->fetchArray($query1Restaurantes)) {


                        ?>
                            <li class="">
                                <a href="restaurantesinfinito.php?IDModulo=<?php echo  $DatosRestaurantes["IDModulo"] ?>">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?php echo $DatosRestaurantes["TituloLateral"] ?>

                                </a>
                                <b class="arrow"></b>
                            </li>

                        <?php
                        }

                        ?>
                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("ClubFechaCierre", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "fechascierre") echo "active"; ?>">
                    <a href="fechascierre.php">
                        <i class="menu-icon fa fa-calendar"></i>
                        <?= SIMUtil::get_traduccion('', '', 'FechasCierreClub', LANGSESSION); ?>
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php
            } ?>


            <!--
        <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Encuesta", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
        <li class="<?php if ($script == "encuestas") echo "active"; ?>">
            <a href="encuestas.php">
                <i class="menu-icon fa  fa-check-square-o"></i>
                Encuestas
            </a>

            <b class="arrow"></b>
        </li>
        <?php
            } ?>
        -->


            <?php

            $permiso = SIMUtil::verificar_permiso_modulo("Encuesta", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "encuestas" || $script == "configuracionencuesta") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-check-square-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'encuestas', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="encuestas.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'encuestas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracionencuesta.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("EncuestaGeneral1", SIMUser::get("IDPerfil"));
            $permiso2 = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoEncuestaInfinita");
            if (isset($club_selecc) && ($permiso == 0 || $permiso2 == 0)) { ?>
                <li class="<?php if ($script == "encuestavial" || $script == "configuracionencuestavial") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-check-square-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'EncuestaInfinita', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <?php $sqlEncuesta = "SELECT IDModulo FROM Modulo WHERE Tipo = 'Encuesta'";
                        //echo $sql;
                        $queryEncuesta = $dbo->query($sqlEncuesta);


                        while ($DatosEncuesta = $dbo->fetchArray($queryEncuesta)) {
                            $ArrayidModuloEncuesta[] = $DatosEncuesta["IDModulo"];
                        }
                        $DatosArrayEncuesta = implode(",", $ArrayidModuloEncuesta);
                        // print_r($DatosArray);
                        $IDClub = SIMUser::get("club");
                        $sql1Encuesta = "SELECT TituloLateral,IDModulo FROM ClubModulo WHERE IDClub = '$IDClub' and IDModulo in ($DatosArrayEncuesta) and Activo = 'S'";
                        //echo $sql1;
                        $query1Encuesta = $dbo->query($sql1Encuesta);
                        while ($DatosEncuesta = $dbo->fetchArray($query1Encuesta)) {
                            $permiso = SIMUtil::verificar_permisos_IDModulo(SIMUser::get("IDPerfil"), $DatosEncuesta[IDModulo]);
                            if ($permiso == 0) {
                        ?>
                                <li class="">
                                    <a href="encuestasinfinita.php?IDModulo=<?php echo  $DatosEncuesta["IDModulo"] ?>">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?php echo $DatosEncuesta["TituloLateral"] ?>

                                    </a>
                                    <b class="arrow"></b>
                                </li>

                        <?php
                            }
                        }
                        ?>

                        <!-- app empleados -->

                        <?php $sqlEncuestaEmpleado = "SELECT IDModulo FROM Modulo WHERE Tipo = 'Encuesta'";
                        //echo $sql;
                        $queryEncuestaEmpleado = $dbo->query($sqlEncuestaEmpleado);


                        while ($DatosEncuestaEmpleado = $dbo->fetchArray($queryEncuestaEmpleado)) {
                            $ArrayidModuloEncuestaEmpleado[] = $DatosEncuestaEmpleado["IDModulo"];
                        }
                        $DatosArrayEncuestaEmpleado = implode(",", $ArrayidModuloEncuestaEmpleado);
                        // print_r($DatosArray);
                        $IDClub = SIMUser::get("club");
                        $sql1EncuestaEmpleado = "SELECT TituloLateral,IDModulo FROM AppEmpleadoModulo WHERE IDClub = '$IDClub' and IDModulo in ($DatosArrayEncuestaEmpleado) and Activo = 'S'";
                        //echo $sql1;
                        $query1EncuestaEmpleado = $dbo->query($sql1EncuestaEmpleado);
                        while ($DatosEncuestaEmpleado = $dbo->fetchArray($query1EncuestaEmpleado)) {
                            $permiso = SIMUtil::verificar_permisos_IDModulo(SIMUser::get("IDPerfil"), $DatosEncuestaEmpleado[IDModulo]);
                            if ($permiso == 0 || (SIMUser::get("IDPerfil") <= 1)) {
                        ?>
                                <li class="">
                                    <a href="encuestasinfinita.php?IDModulo=<?php echo  $DatosEncuestaEmpleado["IDModulo"] ?>">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?php echo $DatosEncuestaEmpleado["TituloLateral"] ?>

                                    </a>
                                    <b class="arrow"></b>
                                </li>

                        <?php
                            }
                        }
                        ?>


                    </ul>
                </li>
            <?php
            } ?>



            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("EncuestaVial", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "encuestavial" || $script == "configuracionencuestavial") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-check-square-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'EncuestaVial', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="encuestavial.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'encuestas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracionencuestavial.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Diagnostico", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "diagnostico") echo "active"; ?>">
                    <a href="diagnostico.php">
                        <i class="menu-icon fa  fa-flask"></i>
                        <?= SIMUtil::get_traduccion('', '', 'Auto-Diagnostico', LANGSESSION); ?>
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php
            } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("RegistroContacto", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "registrocontacto" || $script == "configuracionestrecho") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-users"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'RegistroContacto', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="registrocontacto.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'RegistroContactos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracionestrecho.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Dotacion", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "dotaciones") echo "active"; ?>">
                    <a href="dotaciones.php">
                        <i class="menu-icon fa fa-briefcase"></i>
                        <?= SIMUtil::get_traduccion('', '', 'Dotacion', LANGSESSION); ?>
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Encuesta2", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "encuestas2") echo "active"; ?>">
                    <a href="encuestas2.php">
                        <i class="menu-icon fa  fa-check-square-o"></i>
                        <?= SIMUtil::get_traduccion('', '', 'EncuestaCalificacion', LANGSESSION); ?>
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Ruta", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "rutas") echo "active"; ?>">
                    <a href="rutas.php">
                        <i class="menu-icon fa  fa-globe"></i>
                        <?= SIMUtil::get_traduccion('', '', 'Transporte', LANGSESSION); ?>
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("EncuestaArbol", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "encuestaarbol" || $script == "configuracionencuestaarbol" || $script == "categoriaencuestaarbol" || $script == "areasocio") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-tachometer"></i>
                        <span class="menu-text">
                            <?php
                            $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '104' and IDClub = '" . $club_selecc . "' ");

                            if ($club_selecc == 138)
                                echo SIMUtil::get_traduccion('', '', 'CotizaciónEventos', LANGSESSION);
                            elseif ($club_selecc == 137)
                                echo SIMUtil::get_traduccion('', '', 'SolicitudparaEventos', LANGSESSION);
                            elseif (!empty($NombreModulo))
                                echo $NombreModulo;
                            else
                                echo SIMUtil::get_traduccion('', '', 'Movilidad', LANGSESSION);
                            ?>
                        </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <!-- <a href="encuestaarbol.php">
                        <i class="menu-icon fa  fa-tachometer"></i>

                    </a> -->
                    <b class="arrow"></b>
                    <ul class="submenu">

                        <li class="">
                            <a href="encuestaarbol.php">
                                <i class="menu-icon fa  fa-tachometer"></i>
                                <?php
                                $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '104' and IDClub = '" . $club_selecc . "' ");

                                if ($club_selecc == 138)
                                    echo SIMUtil::get_traduccion('', '', 'CotizaciónEventos', LANGSESSION);
                                elseif ($club_selecc == 137)
                                    echo SIMUtil::get_traduccion('', '', 'SolicitudparaEventos', LANGSESSION);
                                elseif (!empty($NombreModulo))
                                    echo $NombreModulo;
                                else
                                    echo SIMUtil::get_traduccion('', '', 'Movilidad', LANGSESSION);

                                ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="categoriaencuestaarbol.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Categoria Encuesta Arbol', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="areasocio.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Zona', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="configuracionencuestaarbol.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion Encuesta Arbol', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Votacion", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "votaciones" || $script == "votacionesevento") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-download"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Votaciones', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="votaciones.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Votaciones', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="votacionesevento.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Evento', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("NotificacionesGenerales", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "notificaciones" || $script == "gruposocio" || $script == "grupoempleado") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-envelope-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'NotifGenerales', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="notificaciones.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Notificacion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="gruposocio.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'GrupoSocios', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="grupoempleado.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'GrupoEmpleados', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
            <?php
            } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Domicilio", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php
                            if (
                                $script == "productos" || $script == "domicilios"  || $script == "categoriaproductos" || $script == "configuraciondomicilios" || $script == "restaurantedomicilio" ||
                                $script == "productos2" || $script == "domicilios2"  || $script == "categoriaproductos2" || $script == "configuraciondomicilios2" || $script == "restaurantedomicilio2" ||
                                $script == "productos3" || $script == "domicilios3"  || $script == "categoriaproductos3" || $script == "configuraciondomicilios3" || $script == "restaurantedomicilio3" ||
                                $script == "productos4" || $script == "domicilios4"  || $script == "categoriaproductos4" || $script == "configuraciondomicilios4" || $script == "restaurantedomicilio4"
                            )
                                echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-coffee"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Domicilios', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoDomicilio1");
                        if (isset($club_selecc) && $permiso == 0) : ?>
                            <li class="">

                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text">
                                        <?php
                                        $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '33' and IDClub = '" . $datos_club["IDClub"] . "' ");
                                        if (!empty($NombreModulo))
                                            echo $NombreModulo;
                                        else
                                            echo  SIMUtil::get_traduccion('', '', 'Domicilios', LANGSESSION) . "1";
                                        ?>
                                    </span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">
                                        <a href="domicilios.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Pedidos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>


                                    <li class="">
                                        <a href="productos.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="categoriaproductos.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'CategoriaProductos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="restaurantedomicilio.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Restaurantes', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="configuraciondomicilios.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoDomicilio2");
                        if (isset($club_selecc) && $permiso == 0) : ?>

                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text">
                                        <?php
                                        $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '98'and IDClub = '" . $datos_club["IDClub"] . "'  ");
                                        if (!empty($NombreModulo))
                                            echo $NombreModulo;
                                        else
                                            echo  SIMUtil::get_traduccion('', '', 'Domicilios', LANGSESSION) . "2";
                                        ?>

                                    </span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">
                                        <a href="domicilios2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Pedidos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="productos2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="categoriaproductos2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'CategoriaProductos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="restaurantedomicilio2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Restaurantes', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="configuraciondomicilios2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoDomicilio3");
                        if (isset($club_selecc) && $permiso == 0) : ?>

                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text">
                                        <?php
                                        $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '112' and IDClub = '" . $datos_club["IDClub"] . "' ");
                                        if (!empty($NombreModulo))
                                            echo $NombreModulo;
                                        else
                                            echo  SIMUtil::get_traduccion('', '', 'Domicilios', LANGSESSION) . "3";
                                        ?>

                                    </span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">
                                        <a href="domicilios3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Pedidos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>


                                    <li class="">
                                        <a href="productos3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="categoriaproductos3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'CategoriaProductos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="restaurantedomicilio3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Restaurantes', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="configuraciondomicilios3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoDomicilio4");
                        if (isset($club_selecc) && $permiso == 0) : ?>
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text">
                                        <?php
                                        $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '113' and IDClub = '" . $datos_club["IDClub"] . "' ");
                                        if (!empty($NombreModulo))
                                            echo $NombreModulo;
                                        else
                                            echo  SIMUtil::get_traduccion('', '', 'Domicilios', LANGSESSION) . "4";
                                        ?>

                                    </span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">
                                        <a href="domicilios4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Pedidos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>


                                    <li class="">
                                        <a href="productos4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="categoriaproductos4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'CategoriaProductos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="restaurantedomicilio4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Restaurantes', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="configuraciondomicilios4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Domicilio", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php
                            if (
                                $script == "productosusuario" || $script == "domiciliosusuarios"  || $script == "categoriaproductosusuario" || $script == "configuraciondomiciliosusuario" || $script == "restaurantedomiciliousuario" ||
                                $script == "productosusuario2" || $script == "domiciliosusuarios2"  || $script == "categoriaproductosusuario2" || $script == "configuraciondomiciliosusuario2" || $script == "restaurantedomiciliousuario2" ||
                                $script == "productosusuario3" || $script == "domiciliosusuarios3"  || $script == "categoriaproductosusuario3" || $script == "configuraciondomiciliosusuario3" || $script == "restaurantedomiciliousuario3" ||
                                $script == "productosusuario4" || $script == "domiciliosusuarios4"  || $script == "categoriaproductosusuario4" || $script == "configuraciondomiciliosusuario4" || $script == "restaurantedomiciliousuario4"
                            )
                                echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-coffee"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'DomiciliosFunc', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoDomicilio1");
                        if (isset($club_selecc) && $permiso == 0) : ?>
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text">
                                        <?php
                                        $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '33' and IDClub = '" . $datos_club["IDClub"] . "' ");
                                        if (!empty($NombreModulo))
                                            echo $NombreModulo;
                                        else
                                            echo  SIMUtil::get_traduccion('', '', 'Domicilios', LANGSESSION) . "1 Func.";

                                        ?>
                                    </span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">
                                        <a href="domiciliosusuarios.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Pedidos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>


                                    <li class="">
                                        <a href="productosusuario.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="categoriaproductosusuario.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'CategoriaProductos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="restaurantedomiciliousuario.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Restaurantes', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="configuraciondomiciliosusuario.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoDomicilio2");
                        if (isset($club_selecc) && $permiso == 0) : ?>
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text">
                                        <?php
                                        $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '98'and IDClub = '" . $datos_club["IDClub"] . "'  ");
                                        if (!empty($NombreModulo))
                                            echo $NombreModulo;
                                        else
                                            echo  SIMUtil::get_traduccion('', '', 'Domicilios', LANGSESSION) . "2 Func.";

                                        ?>

                                    </span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">
                                        <a href="domiciliosusuarios2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Pedidos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="productosusuario2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="categoriaproductosusuario2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'CategoriaProductos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="restaurantedomiciliousuario2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Restaurantes', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="configuraciondomiciliosusuario2.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoDomicilio3");
                        if (isset($club_selecc) && $permiso == 0) : ?>
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text">
                                        <?php
                                        $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '112' and IDClub = '" . $datos_club["IDClub"] . "' ");
                                        if (!empty($NombreModulo))
                                            echo $NombreModulo;
                                        else
                                            echo  SIMUtil::get_traduccion('', '', 'Domicilios', LANGSESSION) . "3 Func.";

                                        ?>

                                    </span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">
                                        <a href="domiciliosusuarios3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Pedidos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>


                                    <li class="">
                                        <a href="productosusuario3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="categoriaproductosusuario3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'CategoriaProductos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="restaurantedomiciliousuario3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Restaurantes', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="configuraciondomiciliosusuario3.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoDomicilio4");
                        if (isset($club_selecc) && $permiso == 0) : ?>
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text">
                                        <?php
                                        $NombreModulo = $dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '113' and IDClub = '" . $datos_club["IDClub"] . "' ");
                                        if (!empty($NombreModulo))
                                            echo $NombreModulo;
                                        else
                                            echo  SIMUtil::get_traduccion('', '', 'Domicilios', LANGSESSION) . "4 Func.";

                                        ?>

                                    </span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <ul class="submenu">
                                    <li class="">
                                        <a href="domiciliosusuarios4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Pedidos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>


                                    <li class="">
                                        <a href="productosusuario4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="categoriaproductosusuario4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'CategoriaProductos', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="restaurantedomiciliousuario4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Restaurantes', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="configuraciondomiciliosusuario4.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php
            } ?>

            <?php
            //Espacial atc recepcion puede ver Torneos
            if (SIMUser::get("IDUsuario") == 1716 || SIMUser::get("IDUsuario") == 46) { ?>
                <li <?php if ($script == "eventos") echo "active"; ?>>
                    <a href="eventos.php">
                        <i class="menu-icon fa fa-calendar"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'Torneos', LANGSESSION); ?> </span>
                    </a>

                    <b class="arrow"></b>
                </li>

            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Domiciliarios", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li <?php if ($script == "domiciliario") echo "active"; ?>>
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-bicycle"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Domiciliarios', LANGSESSION); ?></span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="domiciliario.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Domiciliarios', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuraciondomiciliario.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Configuracion domiciliario
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                    <b class="arrow"></b>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Casino", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "consumoalimentoscasino" || $script == "registraralimentoscasino") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-cutlery"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'AlimentosCasino', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="">
                            <a href="consumoalimentoscasino.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'CargaConsumos', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="registraralimentoscasino.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'RegistrarAlimentosCasino', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="registrarconsumoqr.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'registrarconsumoqr', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("BotonPanico", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "botonpanico") echo "active"; ?>">
                    <a href="botonpanico.php">
                        <i class="menu-icon fa fa-bell"></i>
                        <?= SIMUtil::get_traduccion('', '', 'BotonPanico', LANGSESSION); ?>
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php } ?>


            <?php
            //Reviso si tiene algun area de pqr asignada
            $permiso_pqr = 0;
            $sql_pqr_usu = "Select * From UsuarioArea Where IDUsuario = '" . SIMUser::get("IDUsuario") . "' Limit 1";
            $result_pqr_usu = $dbo->query($sql_pqr_usu);
            if ($dbo->rows($result_pqr_usu) > 0) :
                $permiso_pqr = 1;
            endif;
            $permiso = SIMUtil::verificar_permiso_modulo("Pqr", SIMUser::get("IDPerfil"));
            //solamente para los administradores del club y el superusuario IDPerfil = 0
            if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1 || $permiso_pqr == 1 || $permiso == 0)) {
            ?>

                <li class="<?php if ($script == "serviciospqr" || $script == "pqr" || $script == "tipopqr" || $script == "area" || $script == "reportepqr") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-comments-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'PqrsSocios', LANGSESSION); ?> </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="pqr.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'VerPqrs', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <?php
                        // si tiene permiso de las areas
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoAreasPqr");
                        //solamente para los administradores del club y el superusuario IDPerfil = 0
                        if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1) || $permiso == 0) { ?>
                            <li class="">
                                <a href="area.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Areas', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                        <?php
                        }; ?>

                        <?php
                        // si tiene permiso de las tipo pqr
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoTipoPqr");
                        //solamente para los administradores del club y el superusuario IDPerfil = 0
                        if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1) || $permiso == 0) { ?>

                            <li class="">
                                <a href="tipopqr.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'TipoPqr', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>
                        <?php
                        }; ?>

                        <?php
                        // si tiene permiso de las categoria pqr
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoCategoriaPqr");
                        //solamente para los administradores del club y el superusuario IDPerfil = 0
                        if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1) || $permiso == 0) { ?>
                            <li class="">
                                <a href="categoriapqr.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <!--<?= SIMUtil::get_traduccion('', '', 'categoriapqr', LANGSESSION); ?> -->
                                    Categoria Pqr
                                </a>

                                <b class="arrow"></b>
                            </li>
                        <?php
                        }; ?>

                        <li class="">
                            <a href="serviciospqr.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ServicioPqr', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="reportepqr.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ReportePqr', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="configuracionpqr.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
            <?php
            }
            ?>

            <?php
            //Reviso si tiene algun area de pqr asignada
            $permiso_pqr = 0;
            $sql_pqr_usu = "Select * From UsuarioAreaFuncionario Where IDUsuario = '" . SIMUser::get("IDUsuario") . "' Limit 1";
            $result_pqr_usu = $dbo->query($sql_pqr_usu);
            if ($dbo->rows($result_pqr_usu) > 0) :
                $permiso_pqr = 1;
            endif;
            $permiso = SIMUtil::verificar_permiso_modulo("Pqr", SIMUser::get("IDPerfil"));


            if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1 || $permiso_pqr == 1 || $permiso == 0)) {
            ?>

                <li class="<?php if ($script == "serviciospqrfuncionarios" || $script == "pqrfuncionario" || $script == "tipopqrfuncionario" || $script == "areafuncionario" || $script == "reportepqrfuncionario") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-comments-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'PqrsFuncionarios', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">


                        <li class="">
                            <a href="pqrfuncionario.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'VerPqrs', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <?php
                        // si tiene permiso de las areas pqr
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoAreasPqr");
                        //solamente para los administradores del club y el superusuario IDPerfil = 0
                        if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1 || $permiso == 0)) { ?>
                            <li class="">
                                <a href="areafuncionario.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Areas', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>
                        <?php
                        }; ?>

                        <?php
                        // si tiene permiso de las tipo pqr
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoTipoPqr");
                        //solamente para los administradores del club y el superusuario IDPerfil = 0
                        if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1) || $permiso == 0) { ?>

                            <li class="">
                                <a href="tipopqrfuncionario.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'TipoPqr', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>
                        <?php
                        }; ?>

                        <?php
                        // si tiene permiso de las categoria pqr
                        $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoCategoriaPqr");
                        //solamente para los administradores del club y el superusuario IDPerfil = 0
                        if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1) || $permiso == 0) { ?>
                            <li class="">
                                <a href="categoriapqr_func.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <!--<?= SIMUtil::get_traduccion('', '', 'categoriapqrfuncionario', LANGSESSION); ?> -->
                                    Categoria Pqr
                                </a>

                                <b class="arrow"></b>
                            </li>
                        <?php
                        } ?>

                        <li class="">
                            <a href="serviciospqrfuncionarios.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ServicioPqr', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <?php if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1) || SIMUser::get("IDPerfil") == 11 || SIMUser::get("IDPerfil") == 15) { ?>
                            <li class="">
                                <a href="reportepqrfuncionario.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ReportePqr', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>
                        <?php
                        } ?>
                    </ul>
                </li>

            <?php
            }
            ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Clasificado", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "clasificados" || $script == "seccionclasificados") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-gavel"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Clasificados', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="clasificados.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Clasificados', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="seccionclasificados.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Categorias', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracionclasificados.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Clasificado", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "clasificados2" || $script == "seccionclasificados2") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-gavel"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'Clasificados', LANGSESSION); ?> Func.</span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="clasificados2.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ClasificadosFuncionarios', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="seccionclasificados2.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'CategoriasFuncionarios', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracionclasificadosusuario.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("ObjetoPerdido", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "objetosperdidos" || $script == "seccionobjetosperdidos") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-exclamation-circle"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'ObjetosPerdidos', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="objetosperdidos.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Objetos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="seccionobjetosperdidos.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Categorias', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("AdminElementosToallas", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "objetosprestados" || $script == "categoriaobjetosprestados" || $script == "lugarobjetosprestados" || $script == "configuracionobjetosprestadosusuario" || $script == "configuracionobjetosprestadosadministrador") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-puzzle-piece"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'ObjetosPrestados', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="objetosprestados.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ObjetosPrestados', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="categoriaobjetosprestados.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Categorias', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="lugarobjetosprestados.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'LugarObjetosPrestados', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracionobjetosprestadosusuario.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionUsuario', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="configuracionobjetosprestadosadministrador.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionAdministrador', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("OfertaLaboral", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>


                <li class="<?php if ($script == "ofertassocios" || $script == "ofertasfuncionarios") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-briefcase"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'OfertaLaboral', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="<?php if ($script == "ofertassocios") echo "active"; ?>">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Parasocios', LANGSESSION); ?></span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="ofertassocios.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Ofertas', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="configuracionofertassocios.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="<?php if ($script == "ofertasfuncionarios") echo "active"; ?>">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Parafuncionarios', LANGSESSION); ?></span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="ofertasfuncionarios.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Ofertas', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="configuracionofertasfuncionarios.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>


            <?php
            } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Postulado", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "postulados") echo "active"; ?>">
                    <a href="postulados.php">
                        <i class="menu-icon fa  fa-eye"></i>
                        <?= SIMUtil::get_traduccion('', '', 'Postulados', LANGSESSION); ?>
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php
            } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Beneficio", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "beneficios" || $script == "seccionbeneficios") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-shopping-cart"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Beneficios', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="beneficios.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Beneficios', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="seccionbeneficios.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Categorias', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracionbeneficios.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>



            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Valet", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "valet" || $script == "configuracionvalet" || $script == "pagosvaletparking" || $script == "vehiculovaletparking") echo "active"; ?>">
                    <a href="" class="dropdown-toggle ">
                        <i class="menu-icon fa fa-car"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'ValetParking', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="valet.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ValetParking', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <?php if (SIMUser::get("Nivel") == 0 || SIMUser::get("IDPerfil") <= 1) : ?>


                            <li class="">
                                <a href="pagosvaletparking.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Tiposdepago', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="">
                                <a href="vehiculovaletparking.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Vehiculos', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>


                            <li class="">
                                <a href="configuracionvalet.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                        <?php endif; ?>

                    </ul>
                </li>

            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("CarPool", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "tipovehiculo" || $script == "motivoscalificacion" || $script == "configuracioncarros") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-taxi"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'CarrosCompartidos', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="motivoscalificacion.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Motivoscalificacion', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="tipovehiculo.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Tiposvehiculo', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracioncarros.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="historialviajes.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Historial', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } ?>




            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("CanjeSolicitud", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "canjes" || $script == "canjesolicitudes" || $script == "clubcanjes" || $script == "configuracioncanjes") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-exchange"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Canjes', LANGSESSION); ?> </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="canjesolicitudes.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Solicitudes', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="clubcanjes.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ClubesConvenio', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="listaclubcanje.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Listaclub', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="fechasbloqueocanje.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Fechasbloqueocanjes', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracioncanjes.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>

            <?php
            }


            $permiso = SIMUtil::verificar_permiso_modulo("Hotel", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "reservashotel" || $script == "cierrehotel" || $script == "tarifas" || $script == "temporadaalta" || $script == "temporadacortoplazo" || $script == "habitaciones" || $script == "torres" || $script == "tipohabitaciones" || $script == "tiporeservahotel" || $script == "configuracionhotel") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-key"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Hotel', LANGSESSION); ?> </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="reservashotel.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Reservas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="cierrehotel.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Cierres', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="tarifas.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Tarifas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="temporadaalta.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TemporadaAlta', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="temporadacortoplazo.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TemporadaaCortoPlazo', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="habitaciones.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Habitaciones', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="torres.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Torres', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="tipohabitaciones.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TipoHabitacion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="tiporeservahotel.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TipoReservaHotel', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="configuracionhotel.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'OtraConfiguracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>

            <?php
            }
            ?>



            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Reportes", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "reportes" || $script == "reportesocios" || $script == "reportereservas" || $script == "reporteinvitaciones" || $script == "reportelogs" || $script == "reportelogdatos" || $script == "reportevacunacion2" || $script == "reporteingresosalidavehiculos" || $script == "reportehistorialsocios" || $script == "reportelogs_admin" || $script == "reporteencuestaarbol1") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-file"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Reportes', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="registroDiagnostico.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Diagnostico', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="reporteDiagnostico.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'EstadísticasCovid', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reportevacunacion.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'EstadisticaVacunacion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reportevacunacion2.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'EstadisticaVacunacion', LANGSESSION); ?> 2
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reportesocios.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Sociosactivos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="reportefuncionarios.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Funcionariosactivos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>



                        <li class="">
                            <a href="reportereservas.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Reservas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reportereservadetalle.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Reservasxperiodo', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="reportereservassocio.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ReservasxSocio', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="reportereservaseliminadas.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ReservasEliminadas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reporteadmin.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ReporteServicios', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reporteintentos.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Intentodereservas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                        <li class="">
                            <a href="reporteinvitaciones.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Invitaciones', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="reportelogs.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'LogServicio', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="reportelogs_admin.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Log Administracion
                                <!-- <?= SIMUtil::get_traduccion('', '', 'Log Administracion', LANGSESSION); ?> -->
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="reportelogdatos.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'LogCambioDatos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reporteingresosalida.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ReporteIngreso/Salidas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reporteingresosalidavehiculos.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ReporteIngreso/SalidasVehiculos', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reporte_ocupacion_paraiso.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ReporteParaiso', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="reportehistorialsocios.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ReporteCuotasSociales', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="reportenosocios.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'reporteparticipantesexternos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reportenosocios2.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'reporteparticipantesexternos', LANGSESSION); ?>2
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="reporteencuestaarbol1.php?action=search">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Reporte encuesta arbol', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
            } //end if


            $permiso = SIMUtil::verificar_permiso_modulo("Puerta", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "puertas" || $script == "controlpuertas" || $script == "puertaubicacion") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-unlock"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'ControldePuertas', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="controlpuertas.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'AbrirPuerta', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="puertas.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ListaPuertas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="puertaubicacion.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'UbicacionPuertas', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>


                    </ul>
                </li>
                <?php
            }


            $permiso = SIMUtil::verificar_permiso_modulo("SocioInvitado", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) {

                if (SIMUser::get("club") != 9) : //Para mesa de yeguas se activa accesos
                ?>

                    <?php $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoInvitados");
                    if ($permiso == 0) { ?>
                        <li <?php if ($script == "invitados") echo "active"; ?>>
                            <a href="invitados.php">
                                <i class="menu-icon fa fa-users"></i>
                                <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'Invitados', LANGSESSION); ?> </span>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    <?php } ?>

                    <li <?php if ($script == "invitados") echo "active"; ?>>
                        <a href="registrocanesadmin.php">
                            <i class="menu-icon fa fa-users"></i>
                            <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'IngresoMascotas', LANGSESSION); ?> </span>
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 110) { ?>
                        <li <?php if ($script == "bquillanoresidentes") echo "active"; ?>>
                            <a href="bquillanoresidentes.php">
                                <i class="menu-icon fa fa-users"></i>
                                <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'InvNoresidentes', LANGSESSION); ?> </span>
                            </a>

                            <b class="arrow"></b>
                        </li>
                    <? } ?>


                    <li class="<?php if ($script == "fechasinvitados") echo "active"; ?>">
                        <a href="fechasinvitados.php">
                            <i class="menu-icon fa fa-calendar"></i>
                            <?= SIMUtil::get_traduccion('', '', 'FechasEspecialInv', LANGSESSION); ?>
                        </a>

                        <b class="arrow"></b>
                    </li>
                <?
                endif;
            } //end if


            if (SIMUser::get("club") == 9) : //Para mesa de yeguas se activa este reporte
                ?>
                <li <?php if ($script == "invitados") echo "active"; ?>>
                    <a href="invitadosespeciales.php?permiso=l">
                        <i class="menu-icon fa fa-users"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'ConsultaInvitadosSocio', LANGSESSION); ?> </span>
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php endif;    ?>

            <?php

            if ((SIMUser::get("club") == 56 &&  (SIMUser::get("Nivel") == 0 || SIMUser::get("IDPerfil") == 4)) ||  SIMUser::get("IDPerfil") == 44) : ?>
                <li <?php if ($script == "invitados") echo "active"; ?>>
                    <a href="autorizaciones.php?action=add">
                        <i class="menu-icon fa fa-users"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'NuevoContratista', LANGSESSION); ?> </span>
                    </a>
                    <b class="arrow"></b>
                </li>

            <?php endif; ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("SocioInvitado", SIMUser::get("IDPerfil"));
            $permiso1 = SIMUtil::verificar_permiso_modulo("PantallaAcceso", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && ($permiso == 0 || $permiso1 == 0)) {

            ?>
                <li <?php if ($script == "accesoinvitado") echo "active"; ?>>
                    <a href="accesoinvitado.php">
                        <i class="menu-icon fa fa-car"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'PantallaAcceso', LANGSESSION); ?> </span>
                    </a>

                    <b class="arrow"></b>
                </li>

            <?php
            }

            $permiso = SIMUtil::verificar_permiso_modulo("SocioInvitado", SIMUser::get("IDPerfil"));
            $permiso1 = SIMUtil::verificar_permiso_modulo("PantallaAccesoEvento", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && ($permiso == 0 || $permiso1 == 0)) {

            ?>
                <li <?php if ($script == "accesoevento") echo "active"; ?>>
                    <a href="accesoevento.php">
                        <i class="menu-icon fa fa-car"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'PantallaAccesoEvento', LANGSESSION); ?> </span>
                    </a>
                    <b class="arrow"></b>
                </li>

            <?php
            }

            $permiso = SIMUtil::verificar_permiso_modulo("SocioInvitado", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) {

            ?>

                <li <?php if ($script == "accesoinvitado") echo "active"; ?>>
                    <a href="accesoinvitadoaut.php">
                        <i class="menu-icon fa fa-key"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'AccesoAutomatico', LANGSESSION); ?> </span>
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php
            }
            $permiso = SIMUtil::verificar_permiso_modulo("SocioInvitado", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "invitadosespeciales" || $script == "autorizaciones" || $script == "invitadosgeneral" || $script == "reporteaccesos"  || $script == "reporteaccesosinv" || $script == "tipoinvitados" || $script == "clasificacioninvitados" || $script == "listaNegra" || $script == "accesoencuestaarbol" || $script == "accesoobjeto" || $script == "configuracionacceso" || $script == "tipoobjetos") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-car"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Accesos', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">


                        <li class="">
                            <a href="invitadosespeciales.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Invitados', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <?php if (SIMUser::get("IDPerfil") != 25) : ?>

                            <li class="">
                                <a href="invitadosespeciales.php?action=add">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    --<?= SIMUtil::get_traduccion('', '', 'NuevoInvitado', LANGSESSION); ?>
                                </a> <b class="arrow"></b>
                            </li>
                        <?php endif; ?>


                        <?php $permiso = SIMUtil::verificar_permisos_modulo_separados(SIMUser::get("IDPerfil"), "PermisoContratistas");
                        if ($permiso == 0) { ?>
                            <li class="">
                                <a href="autorizaciones.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Contratista', LANGSESSION); ?>
                                </a>
                                <b class="arrow"></b>
                            </li>
                        <?php } ?>
                        <?php if (SIMUser::get("IDPerfil") != 25) : ?>
                            <li class="">
                                <a href="autorizaciones.php?action=add">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    --<?= SIMUtil::get_traduccion('', '', 'NuevoContratista', LANGSESSION); ?>
                                </a> <b class="arrow"></b>
                            </li>
                        <?php endif; ?>

                        <?php if (SIMUser::get("IDPerfil") != 25) : ?>
                            <li class="">
                                <a href="invitadosgeneral.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'BaseInvitados/Contratista', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <!--
                    <li class="">
                    <a href="reportesalidas.php?action=search">
                    <i class="menu-icon fa fa-caret-right"></i>
                    Pendientes Salida
                    </a>

                    <b class="arrow"></b>
                    </li>
                    -->

                            <li class="">
                                <a href="reporteaccesosinv.php?action=search">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ReporteAutorizacionesInvitado', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="">
                                <a href="reporteaccesos.php?action=search">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ReporteAutorizacionesContratista', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="">
                                <a href="reporteingresosalida.php?action=search">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ReporteIngreso/Salidas', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="">
                                <a href="reporteocupacion.php?action=search">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'OcupacionClub', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="">
                                <a href="salidainvitado.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'RegistroSalidas', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="">
                                <a href="reporteinvitaciones.php?action=search">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'InvitacionesxSocio', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="">
                                <a href="listaNegra.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ListaNegra', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <?php if (SIMUser::get("Nivel") == 0 || SIMUser::get("IDPerfil") <= 1) : ?>

                                <!--   <li class="">
                                    <a href="tipoinvitados.php?action=search">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                       TipoInvitados
                                    </a>

                                    <b class="arrow"></b>
                                </li> -->

                                <li class="">
                                    <a href="clasificacioninvitados.php?action=search">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'ClasificacionInvitados', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>

                                <!-- <li class="">
                    <a href="listaRoja.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Lista Roja
                    </a>

                    <b class="arrow"></b>
                </li>
				<li class="">
                    <a href="listaAmarilla.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Lista Amarilla
                    </a>

                    <b class="arrow"></b>
                </li> -->
                                <li class="">
                                    <a href="accesoencuestaarbol.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'AccesoEncuestaArbol', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <!-- <li class="">
                                <a href="accesoobjeto.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'AccesoObjetos', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li> -->
                                <li class="">
                                    <a href="configuracionacceso.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'ConfiguracionAcceso', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                                <li class="">
                                    <a href="tipoobjetos.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Tipoobjeto', LANGSESSION); ?>
                                    </a>

                                    <b class="arrow"></b>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                    </ul>
                </li>

            <?php
            }
            $permiso = SIMUtil::verificar_permiso_modulo("Correspondencia", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) {
            ?>

                <li class="<?php if ($script == "tipocorrespondencia" || $script == "categoriacorrespondencia" || $script == "correspondencia" || $script == "configuracioncorrespondencia") echo "active"; ?>">
                    <a href="" class="dropdown-toggle ">
                        <i class="menu-icon fa fa-envelope-o"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Correspondencia', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="">
                            <a href="correspondencia.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Correspondencia', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <?php if (SIMUser::get("Nivel") == 0 || SIMUser::get("IDPerfil") <= 1) : ?>

                            <li class="">
                                <a href="tipocorrespondencia.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                            <li class="">
                                <a href="categoriacorrespondencia.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>
                            <li class="">
                                <a href="configuracioncorrespondencia.php">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                                </a>

                                <b class="arrow"></b>
                            </li>

                        <?php endif; ?>

                    </ul>
                </li>

            <?php
            }

            if (SIMUser::get("Nivel") == 0) {
            ?>
                <li <?= $menu_cms ?>>
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-users"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'CMS', LANGSESSION); ?> </span>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="parametros_cms.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Parametros', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="banners_cms.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Banner', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="servicios_cms.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Servicios', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="contactos_cms.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Contactos', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>

            <?
            } //end if
            ?>

            <li class="<?php if ($script == "configuracionjuegosdegolf") echo "active"; ?>">
                <a href="configuracionjuegosdegolf.php">
                    <i class="menu-icon fa fa-bookmark"></i>
                    Configuracion Juegos Golf
                </a>

                <b class="arrow"></b>
            </li>
            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("ClubCodigoPago", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "codigopago") echo "active"; ?>">
                    <a href="codigopagos.php">
                        <i class="menu-icon fa fa-bookmark"></i>
                        <?= SIMUtil::get_traduccion('', '', 'CodigoPago', LANGSESSION); ?>
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Talonera", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "talonera" || $script == "configuraciontalonera" || $script == "reportetalonera") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-th-list"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Talonera', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="reportetalonera.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TaloneraCompradas', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="talonera.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'CrearTalonera', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuraciontalonera.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionTalonera', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reporterecargatalonera.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Reporte', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                </li>
            <?php
            } ?>






            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Talonera", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "talonera" || $script == "configuraciontalonera" || $script == "reportetalonera") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-th-list"></i>
                        <span class="menu-text">Talonera Funcionario</span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="tiqueterafuncionarios.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'TaloneraCompradas', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="talonerafunc.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'CrearTalonera', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuraciontalonerafuncionario.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'ConfiguracionTalonera', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="configuracionconsumostalonera.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'configuracionconsumostalonera', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="reportetalonerafuncionario.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Reporte', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                </li>
            <?php
            } ?>







            <!--  <?php

                    $permiso = SIMUtil::verificar_permiso_modulo("Nuba", SIMUser::get("IDPerfil"));
                    if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "configuracionreservahorario") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-calendar"></i>
                        <span class="menu-text">Nuba</span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <li class="">
                            <a href="configuracionreservahorario.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php
                    } ?> -->

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Cursos", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "cursoedad" || $script == "cursonivel" || $script == "cursoentrenador" || $script == "cursotipo" || $script == "cursohorario" || $script == "cursocalendario" || $script == "cursoinscripcion" || $script == "cursosede") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-certificate"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Cursos', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <?php if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1)) { ?>
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?></span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>

                                <ul class="submenu">
                                    <li class="">
                                        <a href="cursoedad.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Edades', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="cursonivel.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Nivel', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="cursoentrenador.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Entrenador', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="cursotipo.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="cursosede.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Sede', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="cursohorario.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Horario', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="cursocalendario.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Calendario', LANGSESSION); ?>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                        <?php } ?>

                        <li class="">
                            <a href="cursoinscripcion.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Inscribir', LANGSESSION); ?>
                            </a>
                        </li>
                        <!--
            <li class="">
            <a href="cursoreporte.php">
            <i class="menu-icon fa fa-caret-right"></i>
            Reporte
            </a>
            </li>
            -->

                    </ul>
                </li>
            <?php } ?>

            <?php
            if (isset($club_selecc) && ($club_selecc == 8 || $club_selecc == 89)) { ?>
                <li class="<?php if ($script == "configuracionreconocimientos" || $script == "categoriareconocimientos" || $script == "reconocimiento" || $script == "gruporeconocimiento") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-gift"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Reconocimientos', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <?php if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1)) { ?>
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?></span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>

                                <ul class="submenu">
                                    <li class="">
                                        <a href="configuracionreconocimientos.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'ConfiguraciónMódulos', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="categoriareconocimientos.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Categorias', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="gruporeconocimiento.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Grupos', LANGSESSION); ?>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php } ?>

                        <li class="">
                            <a href="reconocimiento.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Reconocimientos', LANGSESSION); ?>
                            </a>
                        </li>
                        <!--
            <li class="">
            <a href="cursoreporte.php">
            <i class="menu-icon fa fa-caret-right"></i>
            Reporte
            </a>
            </li>
            -->

                    </ul>
                </li>
            <?php } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("CartasFormato", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "cartasformato" || $script == "cartasbase" || $script == "cartasenvio" || $script == "cartaslog") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa  fa-envelope"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Cartas', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">

                        <?php if (isset($club_selecc) && ($permiso == 0)) { ?>
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?></span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>

                                <ul class="submenu">
                                    <li class="">
                                        <a href="cartasformato.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'PlantillaCartas', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="cartasbase.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'BaseDatosCartera', LANGSESSION); ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="cartaslog.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?= SIMUtil::get_traduccion('', '', 'Historialdeenvios', LANGSESSION); ?>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                        <?php } ?>

                        <li class="">
                            <a href="cartasenvio.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'GenerarPdf/Email', LANGSESSION); ?>
                            </a>


                        </li>

                    </ul>
                </li>
            <?php } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("PagoRedeban", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "pagosredeban" || $script == "pagosplacetopay" || $script == "pagosplacetopay") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-credit-card"></i>
                        <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'Pagos', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="pagosredeban.php">
                                <i class="menu-icon fa fa-credit-card"></i>
                                <?= SIMUtil::get_traduccion('', '', 'PagosRedeban', LANGSESSION); ?>
                            </a>
                        </li>
                        <li class="">
                            <a href="pagosplacetopay.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'PagosPlacetopay', LANGSESSION); ?>
                            </a>
                        </li>
                        <li class="">
                            <a href="pagosecollect.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'PagosE-Collect', LANGSESSION); ?>
                            </a>
                        </li>
                        <li class="">
                            <a href="pagoslukapay.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'PagosLukaPay', LANGSESSION); ?>
                            </a>
                        </li>

                    </ul>
                </li>
            <?php } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("SocioAutorizacion", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "socioautorizado") echo "active"; ?>">
                    <a href="socioautorizado.php">
                        <i class="menu-icon fa  fa-filter"></i>
                        <?= SIMUtil::get_traduccion('', '', 'SocioReservaGolf', LANGSESSION); ?>
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>


            <?php

            $permiso = SIMUtil::verificar_permiso_modulo("Caddie", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "administrarCaddie" || $script == "configuraciontalegas" || $script == "configuraciontalegaslugar") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-flag"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Caddies', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <?php //if( isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1 || SIMUser::get("IDPerfil") == 24 || SIMUser::get("IDPerfil") == 130) ){ 
                        ?>
                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?></span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="administrarCategoriaCaddie.php?action=add">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="administrarCaddie.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'CrearCaddie', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="administrarPropiedadesTalega.php?action=add">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'PropiedadesTalega', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="exportarcaddies.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'ReporteAsignaciónCaddies', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="configuraciontalegas.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'ConfiguracionTalegas', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="configuraciontalegaslugar.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'TalegasLugar', LANGSESSION); ?>
                                    </a>
                                </li>

                                <li class="">
                                    <a href="campotalega.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'CamposDinamicos', LANGSESSION); ?>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class="">
                            <a href="administrarTalega.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Talega', LANGSESSION); ?>
                            </a>
                        </li>
                        <? //} 
                        ?>

                        <li class="">
                            <a href="administracionCaddie.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'AdministraciónCaddie', LANGSESSION); ?>
                            </a>
                        </li>

                    </ul>
                </li>
            <?php } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Caddie", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == 'solicitudesecaddie' || $script == "caddiesecaddie" || $script == "disponibilidadservicioscaddies" || $script == "elementoservicioscaddies" || $script == "servicioscaddie" || $script == "configuracioncaddies" || $script == "disponibilidadcaddie") echo "active"; ?>">

                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-th-list"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'e-Caddy', LANGSESSION); ?></span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>


                    <ul class="submenu">

                        <li class="">
                            <a href="solicitudesecaddie.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Solicitudesparacaddie', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="caddiesecaddie.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Caddies', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="categoriascaddie.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'CategoriasCaddies', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="disponibilidadservicioscaddies.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Disponibilidadservicios', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="disponibilidadcaddie.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Disponibilidad Caddie
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="elementoservicioscaddies.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Elementos', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="servicioscaddie.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Servicios', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="configuracioncaddies.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?>
                            </a>
                            <b class="arrow"></b>
                        </li>


                    </ul>
                </li>
            <?php
            } ?>

            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Bicicleta", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>

                <li class="<?php if ($script == "administrarPropiedadesBicicleta" || $script == "configuracionbicicletas" || $script == "configuracionbicicletalugar") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-bicycle"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Bicicletas', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <?php //if (isset($club_selecc) && (SIMUser::get("IDPerfil") <= 1 || SIMUser::get("IDPerfil") == 24)) { 
                        ?>
                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Configuracion', LANGSESSION); ?></span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <!--    <li class="">
                                        <a href="administrarCategoriaCaddie.php?action=add">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Categoría
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="administrarCaddie.php">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Crear Caddie
                                        </a>
                                    </li> -->
                                <li class="">
                                    <a href="administrarPropiedadesBicicleta.php?action=add">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'PropiedadesBicicleta', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="configuracionbicicletas.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'ConfiguracionBicicletas', LANGSESSION); ?>
                                    </a>
                                </li>

                                <li class="">
                                    <a href="configuracionbicicletalugar.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'ConfiguracionBicicletaLugar', LANGSESSION); ?>
                                    </a>
                                </li>

                                <li class="">
                                    <a href="campobicicleta.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'CamposDinamicos', LANGSESSION); ?>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class="">
                            <a href="administrarBicicleta.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Bicicleta', LANGSESSION); ?>
                            </a>
                        </li>
                        <? //} 
                        ?>

                        <!--  <li class="">
                            <a href="administracionCaddie.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Administración Caddie
                            </a>
                        </li> -->

                    </ul>
                </li>
            <?php } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Facturacion", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "facturacion") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-calculator"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Facturacion', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <!--  reportes -->

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"> Reportes </span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="facturacionreporte.php?action=mediospago">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        R. de medios de pago
                                    </a>
                                </li>
                                <li class="">
                                    <a href="facturacionreporte.php?action=productos">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        R. de productos
                                    </a>
                                </li>
                                <li class="">
                                    <a href="facturacionreporte.php?action=detalleventa">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        R. detalle de ventas
                                    </a>
                                </li>
                                <li class="">
                                    <a href="facturacionreporte.php?action=porvendedor">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        R. por vendedor
                                    </a>
                                </li>
                                <li class="">
                                    <a href="facturacionreporte.php?action=afiliadosactivos">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        R. de afiliados activos
                                    </a>
                                </li>
                                <li class="">
                                    <a href="facturacionreporte.php?action=afiliadosvencidos">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        R. de vencimientos
                                    </a>
                                </li>
                                <li class="">
                                    <a href="facturacionreporte.php?action=afiliadosnuevos">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        R. de afiliados nuevos
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- fin reportes -->

                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'ConfFacturación', LANGSESSION); ?></span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="descuentos.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'descuentos', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="impuestos.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'impuestos', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="mediospago.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'mediodepago', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="resolucionfactura.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Resoluciones', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="informacionfactura.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Informaciónfactura', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="vendedorfactura.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Vendedores', LANGSESSION); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'ConfProducto', LANGSESSION); ?></span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <ul class="submenu">
                                <li class="">
                                    <a href="categoriafacturacion.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Categoria', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="tipofacturacion.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="productofacturacion.php">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?= SIMUtil::get_traduccion('', '', 'productos', LANGSESSION); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="">
                            <a href="facturacion.php?action=add">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Facturacion', LANGSESSION); ?>
                            </a>
                        </li>
                        <li class="">
                            <a href="facturacionPolo.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'FacturacionPolo', LANGSESSION); ?>
                            </a>
                        </li>
                    </ul>
                </li>
            <?php } ?>


            <?php
            $permiso = SIMUtil::verificar_permiso_modulo("Triatlon", SIMUser::get("IDPerfil"));
            if (isset($club_selecc) && $permiso == 0) { ?>
                <li class="<?php if ($script == "carrera" || $script == "categoriatriatlon" || $script == "registrocorredores" || $script == "kit" || $script == "configuracionkits") echo "active"; ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-trophy"></i>
                        <span class="menu-text"><?= SIMUtil::get_traduccion('', '', 'Triatlon', LANGSESSION); ?> </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="carrera.php?action=add">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Carreras', LANGSESSION); ?>
                            </a>
                        </li>
                        <li class="">
                            <a href="categoriatriatlon.php?action=add">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Categorias', LANGSESSION); ?>
                            </a>
                        </li>
                        <li class="">
                            <a href="registrocorredor.php?action=add">
                                <i class="menu-icon fa fa-caret-right"></i>
                                <?= SIMUtil::get_traduccion('', '', 'registrodecorredores', LANGSESSION); ?>
                            </a>
                        </li>

                        <li class="">
                            <a href="kit.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Kit
                            </a>
                        </li>

                        <li class="">
                            <a href="configuracionkits.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Configuración kits
                            </a>
                        </li>
                    </ul>
                </li>
            <?php } ?>



            <?php
            if (count($datos_servicio) > 0) :
                foreach ($datos_servicio as $idservicio => $servicio) {
                    if ($idservicio == 4350 || $idservicio == 1484 || $idservicio == 5035 || $idservicio == 5039 || $idservicio == 7983 || $idservicio == 7973 || $idservicio == 10931)
                        $estilo_servicio = "style='color: #f80f00'";
                    else
                        $estilo_servicio = "";

            ?>

                    <li <?= $menu_reservas[$idservicio] ?>>

                        <?php if ($idservicio == 3575 || $idservicio == "28122") { //Para el polo las reservas de practicas la pantalla es especial
                            $link = "reservaspolo.php?ids=" . $idservicio;
                        } else {
                            $link = "reservas.php?ids=" . $idservicio;
                        }

                        if ($servicio[TipoSorteo] == 1) :
                            $link = "reservassorteo.php?ids=" . $idservicio;
                        endif;

                        ?>

                        <a href="<?= $link ?>" class="Reservas">
                            <i class="menu-icon fa fa-calendar"></i>
                            <span class="menu-text" <?php echo $estilo_servicio; ?>>
                                <?= SIMUtil::get_traduccion('', '', 'Reservas', LANGSESSION); ?>
                                <?

                                $id_servicio_mestro_menu = $servicio["IDServicioMaestro"];
                                $servicio["Nombre"] =  $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");
                                $servicio["NombrePersonalizado"] =  $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . SIMUser::get("club") . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");

                                if (!empty($servicio["NombrePersonalizado"]))
                                    echo $servicio["NombrePersonalizado"];
                                else
                                    echo $servicio["Nombre"];
                                ?>
                            </span>
                        </a>

                        <b class="arrow"></b>
                    </li>
            <?
                } //end for
            endif;
            ?>
        <?
        } ?>


        <li class="">
            <a href="cambiarclave.php?action=updateclave&IDUsuario=<?php echo base64_encode($datos->IDUsuario); ?>">
                <i class="menu-icon fa fa-cogs"></i>
                <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'CambiarClave', LANGSESSION); ?></span>
            </a>

            <b class="arrow"></b>
        </li>
        <li class="">
            <a href="validausuario.php?action=Salir">
                <i class="menu-icon fa fa-power-off"></i>
                <span class="menu-text"> <?= SIMUtil::get_traduccion('', '', 'Salir', LANGSESSION); ?> </span>
            </a>

            <b class="arrow"></b>
        </li>






    </ul><!-- /.nav-list -->

    <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>

    <script type="text/javascript">
        try {
            ace.settings.check('sidebar', 'collapsed')
        } catch (e) {}
    </script>
</div>