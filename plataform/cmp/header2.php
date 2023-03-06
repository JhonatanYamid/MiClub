<style>
    .nav-lang-photo,
    .opt-lang-photo {
        width: 30px;
        max-width: 20px;
    }

    .Active {
        background: #fee188;
        color: #444;
    }
</style>
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
                                    Clubes
                                </a>

                            </li>
                        <?php } ?>


                        <?php

                        $permiso = SIMUtil::verificar_permiso_modulo("Usuario", SIMUser::get("IDPerfil"));
                        if ($permiso == 0) { ?>


                            <li class="">
                                <a href="usuarios.php">
                                    <i class="ace-icon fa "></i>
                                    Usuarios
                                </a>

                            </li>

                            <li class="">
                                <a href="ajustesregistros.php">
                                    <i class="ace-icon fa "></i>
                                    Unificar registros
                                </a>

                            </li>

                            <li class="">
                                <a href="perfilesclub.php">
                                    <i class="ace-icon fa "></i>
                                    Perfiles Club
                                </a>

                            </li>

                            <li class="">
                                <a href="configuraciongeneral.php">
                                    <i class="ace-icon fa "></i>
                                    Configuracion General3
                                </a>
                            </li>
                        <?php } ?>


                        <?
                        if (SIMUser::get("Nivel") == 0) {
                        ?>
                            <li class="">
                                <a href="modulos.php">
                                    <i class="ace-icon fa "></i>
                                    Modulos
                                </a>

                            </li>
                            <li class="">
                                <a href="perfiles.php">
                                    <i class="ace-icon fa "></i>
                                    Perfiles
                                </a>

                            </li>
                            <li class="">
                                <a href="serviciosmaestros.php">
                                    <i class="ace-icon fa "></i>
                                    Servicios
                                </a>

                            </li>
                            <li class="">
                                <a href="disenos.php">
                                    <i class="ace-icon fa "></i>
                                    Dise&ntilde;os
                                </a>

                            </li>
                            <li class="">
                                <a href="tipodocumentos.php">
                                    <i class="ace-icon fa "></i>
                                    Tipo Documentos
                                </a>

                            </li>
                            <li class="">
                                <a href="tipoarchivos.php">
                                    <i class="ace-icon fa "></i>
                                    Tipo Archivos
                                </a>

                            </li>

                            <li class="">
                                <a href="pais.php">
                                    <i class="ace-icon fa "></i>
                                    Pais
                                </a>

                            </li>

                            <li class="">
                                <a href="baseclubes.php">
                                    <i class="ace-icon fa "></i>
                                    Base clubes
                                </a>

                            </li>

                            <li class="">
                                <a href="categoriacaddies.php">
                                    <i class="ace-icon fa "></i>
                                    Categoria Caddies
                                </a>

                            </li>
                            <li class="">
                                <a href="reporteadmin.php">
                                    <i class="ace-icon fa "></i>
                                    Reportes
                                </a>

                            </li>


                            <li class="divider"></li>
                        <?
                        } //End if
                        ?>

                        <li>
                            <a href="cambiarclave.php?action=updateclave&IDUsuario=<?php echo base64_encode($datos->IDUsuario); ?>">
                                <i class="ace-icon fa fa-cogs"></i>
                                Cambiar Clave
                            </a>
                        </li>
                        <li>
                            <a href="validausuario.php?action=Salir">
                                <i class="ace-icon fa fa-power-off"></i>
                                Logout
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
                        $Lenguajes = $dbo->fetchAll("Lenguajes", "Publicar='S'", "array");
                        foreach ($Lenguajes as $indice => $valor) {
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