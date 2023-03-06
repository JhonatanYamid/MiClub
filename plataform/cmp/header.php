<div id="navbar" class="navbar navbar-default">
    <script type="text/javascript">
        try {
            ace.settings.check('navbar', 'fixed')
        } catch (e) {}
    </script>

    <div class="navbar-container" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>
        </button>

        <div class="navbar-header pull-left">
            <a href="index.php" class="navbar-brand"><img src="assets/img/logo-interno.png" /></a>
        </div>






        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">




                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <?php if (empty(SIMUser::get("Foto"))) : ?>
                            <img class="nav-user-photo" src="assets/avatars/avatar2.png" alt="<?= SIMUser::get("Nombre") ?>" />
                        <?php else : ?>
                            <img class="nav-user-photo" src="<?php echo USUARIO_ROOT . SIMUser::get("Foto") ?>" alt="<?= SIMUser::get("Nombre") ?>" />
                        <?php endif; ?>
                        <span class="user-info">
                            <small><?= SIMUtil::get_traduccion('', '', 'Bienvenido', LANGSESSION); ?>,</small>
                            <?= SIMUser::get("Nombre") ?>
                        </span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">

                        <?php
                        if (SIMUser::get("Nivel") == 0) {
                        ?>
                            <li class="">
                                <a href="clubes.php?ver=t">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Clubes', LANGSESSION); ?>
                                </a>

                            </li>

                            <li class="">
                                <a href="categoriaservicios.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Categoriasparalosservicios', LANGSESSION); ?>
                                </a>
                            </li>

                            <li class="">
                                <a href="configuraciondiccionario.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ConfiguracionDiccionario', LANGSESSION); ?>
                                </a>
                            </li>
                            <li class="">
                                <a href="diccionario.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Diccionario', LANGSESSION); ?>
                                </a>
                            </li>

                            <li class="">
                                <a href="tipoinvitados.php?action=search">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'TipoInvitados', LANGSESSION); ?>
                                </a>
                            </li>



                            <li class="">
                                <a href="camposformulariosocio.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ConfiguracionFormularioSocios', LANGSESSION); ?>
                                </a>
                            </li>

                            <li class="">
                                <a href="gruposformulariosocio.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'GruposFormularioSocios', LANGSESSION); ?>
                                </a>
                            </li>




                        <?php }

                        $permiso = SIMUtil::verificar_permiso_modulo("Usuario", SIMUser::get("IDPerfil"));
                        $permiso1 = SIMUtil::verificar_permiso_modulo("CreaUsuario", SIMUser::get("IDPerfil"));
                        if ($permiso == 0 || $permiso1 == 0) { ?>

                            <li class="">
                                <a href="usuarios.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Usuarios', LANGSESSION); ?>
                                </a>

                            </li>
                        <?php } ?>

                        <?php
                        $permiso = SIMUtil::verificar_permiso_modulo("Sancion", SIMUser::get("IDPerfil"));
                        if ($permiso == 0) { ?>
                            <li class="">
                                <a href="sanciones.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'SancionesServicios', LANGSESSION); ?>
                                </a>

                            </li>

                        <?php } ?>

                        <?php

                        $permiso = SIMUtil::verificar_permiso_modulo("Usuario", SIMUser::get("IDPerfil"));
                        if ($permiso == 0) { ?>

                            <li class="">
                                <a href="ajustesregistros.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Unificarregistros', LANGSESSION); ?>
                                </a>

                            </li>

                            <li class="">
                                <a href="permisoservicio.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'PermisosReservas', LANGSESSION); ?>
                                </a>

                            </li>

                            <li class="">
                                <a href="perfilesclub.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'PerfilesClub', LANGSESSION); ?>
                                </a>

                            </li>



                            <li class="">
                                <a href="modulosclub.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ModulosClub', LANGSESSION); ?>
                                </a>

                            </li>


                            <li class="">
                                <a href="tiposocio.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'TipoSocios', LANGSESSION); ?>
                                </a>
                            </li>

                            <li class="">
                                <a href="parentesco.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Parentesco', LANGSESSION); ?>
                                </a>
                            </li>


                            <li class="">
                                <a href="cumpleannos.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ConfigCumpleaños', LANGSESSION); ?>
                                </a>

                            </li>

                            <li class="">
                                <a href="configuraciongeneral.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'ConfiguracionGeneral', LANGSESSION); ?>
                                </a>
                            </li>

                            <li class="">
                                <a href="areausuario.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'AreaUsuario', LANGSESSION); ?>
                                </a>
                            </li>

                            <!-- <li class="">
							<a href="categoriaservicios.php">
								<i class="ace-icon fa "></i>
								Categorias para los servicios
							</a>
						</li> -->

                            <li class="">
                                <a href="festivos.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Festivos', LANGSESSION); ?>
                                </a>
                            </li>

                            <li class="">
                                <a href="contratoclub.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Contratos', LANGSESSION); ?>
                                </a>

                            </li>
                        <?php } ?>


                        <?
                        if (SIMUser::get("Nivel") == 0) {
                        ?>

                            <li class="">
                                <a href="modulos.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Modulos', LANGSESSION); ?>
                                </a>

                            </li>
                            <li class="">
                                <a href="perfiles.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Perfiles', LANGSESSION); ?>
                                </a>

                            </li>
                            <li class="">
                                <a href="serviciosmaestros.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Servicios', LANGSESSION); ?>
                                </a>

                            </li>
                            <li class="">
                                <a href="disenos.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Diseños', LANGSESSION); ?>
                                </a>

                            </li>
                            <li class="">
                                <a href="tipodocumentos.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'TipoDocumentos', LANGSESSION); ?>
                                </a>

                            </li>
                            <li class="">
                                <a href="tipoarchivos.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'TipoArchivos', LANGSESSION); ?>
                                </a>

                            </li>

                            <li class="">
                                <a href="pais.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Pais', LANGSESSION); ?>
                                </a>

                            </li>

                            <li class="">
                                <a href="paisesindicativo.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'PaisIndicativo', LANGSESSION); ?>
                                </a>

                            </li>

                            <li class="">
                                <a href="ciudad.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Ciudad', LANGSESSION); ?>
                                </a>

                            </li>

                            <li class="">
                                <a href="baseclubes.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Baseclubes', LANGSESSION); ?>
                                </a>

                            </li>

                            <li class="">
                                <a href="categoriacaddies.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'CategoriaCaddies', LANGSESSION); ?>
                                </a>

                            </li>
                            <li class="">
                                <a href="reporteadmin.php">
                                    <i class="ace-icon fa "></i>
                                    <?= SIMUtil::get_traduccion('', '', 'Reportes', LANGSESSION); ?>
                                </a>

                            </li>


                            <li class="divider"></li>
                        <?
                        } //End if
                        ?>

                        <li>
                            <a href="cambiarclave.php?action=updateclave&IDUsuario=<?php echo base64_encode($datos->IDUsuario); ?>">
                                <i class="ace-icon fa fa-cogs"></i>
                                <?= SIMUtil::get_traduccion('', '', 'CambiarClave', LANGSESSION); ?>
                            </a>
                        </li>
                        <li>
                            <a href="validausuario.php?action=Salir">
                                <i class="ace-icon fa fa-power-off"></i>
                                <?= SIMUtil::get_traduccion('', '', 'Logout', LANGSESSION); ?>
                            </a>
                        </li>

                    </ul>
                </li>
            </ul>
        </div>

        <div class="navbar-header pull-right">
            <ul class="nav ace-nav">
                <li class="light-transparent">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-lang-photo" src="assets/images/flags/<?= LANGSESSION ?>.png" alt="<?= LANGSESSION ?>" />
                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">

                        <?php
                        $Diccionario = $dbo->fetchAll('ConfiguracionDiccionario', 'Publicar="S"', 'array');
                        foreach ($Diccionario as $indice => $valor) {
                        ?>
                            <li class="">
                                <a href="#" class="ChangeLang <?= (LANGSESSION == $valor['Abreviatura']) ? 'Active' : ''; ?>" data-idioma="<?= $valor['Abreviatura']; ?>">
                                    <img class="opt-lang-photo" src="assets/images/flags/<?= $valor['Abreviatura']; ?>.png" alt="<?= $valor['Nombre'] ?>" />
                                    <?= $valor['Nombre']; ?>
                                </a>
                            </li>
                        <?php
                        }
                        ?>

                        <li class="divider"></li>
                    </ul>
                </li>
            </ul>
        </div>



    </div><!-- /.navbar-container -->
</div>