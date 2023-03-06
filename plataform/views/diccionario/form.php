<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Modulo"><?= SIMUtil::get_traduccion('', '', 'Modulo', LANGSESSION); ?> </label>
                                <div class="col-sm-8">
                                    <select name="Modulo" id="Modulo">
                                        <?
                                        $options = array(
                                            "", "Socios", "Banner", "Cuestionario Luker", "Bicicletas", "Talonera", "Alimentos Casino", "Registrar Alimentos",
                                            "Ingreso Mascotas", "Boton Panico", "Empleados Luker", "primadera", "Entre Lomas Censo", "Cumpleaños.",
                                            "Familiares Vacunacion", "Historial Socios", "Auxilios", "Tiempos para mi", "No residentes", "Noticias", "Eventos",
                                            "Eventos 2", "Galerias", "Galerias2", "Directorios", "Documentos", "Documentos 2", "Documentos 3", "Documentos personales",
                                            "Publicidad.", "Faqs", "Laboral", "Vacunación", "Vacunación 2", "Restaurante", "Fechas Cierre Club", "Encuestas",
                                            "Encuesta Vial", "Auto-Diagnostico", "Registro Contacto", "Dotación", "Encuesta Calificacion", "Transporte",
                                            "Movilidad", "Votaciones", "Notif. Generales", "Domicilios", "Domicilios Func.", "Domiciliarios", "Pqr Socios",
                                            "Pqr Funcionarios", "Clasificados", "Clasificados Func.", "Objetos Perdidos", "Oferta Laboral", "Postulados",
                                            "Beneficios", "Valet Parking", "Canjes", "Hotel", "Reportes", "Control de Puertas", "Invitados", "Fechas Especial Inv.",
                                            "Pantalla Acceso", "Acceso Automatico", "Accesos", "Correspondencia", "CMS", "Código Pago", "Cursos", "Reconocimientos",
                                            "Cartas", "Pagos", "Socio Reserva Golf", "Caddies", "Reservas"
                                        );
                                        foreach ($options as $option) {
                                            if ($frm["Modulo"] == $option) {
                                                echo '<option value="' . $option . '" selected="selected">' . $option . '</option>';
                                            } else {
                                                echo '<option value="' . $option . '">' . $option . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Es"> <?= SIMUtil::get_traduccion('', '', 'Español', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="Es" name="Es" placeholder="<?= SIMUtil::get_traduccion('', '', 'Español', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Español', LANGSESSION); ?>" value="<?php echo $frm["Es"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="En"> <?= SIMUtil::get_traduccion('', '', 'Inglés', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="En" name="En" placeholder="<?= SIMUtil::get_traduccion('', '', 'Inglés', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Inglés', LANGSESSION); ?>" value="<?php echo $frm["En"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Zh"> <?= SIMUtil::get_traduccion('', '', 'Chino', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="Zh" name="Zh" placeholder="<?= SIMUtil::get_traduccion('', '', 'Chino', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Chino', LANGSESSION); ?>" value="<?php echo $frm["Zh"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Pt"><?= SIMUtil::get_traduccion('', '', 'Portugués', LANGSESSION); ?> </label>
                                <div class="col-sm-8"><input type="text" id="Pt" name="Pt" placeholder="<?= SIMUtil::get_traduccion('', '', 'Portugués', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Portugués', LANGSESSION); ?>" value="<?php echo $frm["Pt"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">

                            <div class="clearfix form-actions">
                                <div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
                                                                                            else echo $frm["IDClub"];  ?>" />
                                    <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        <?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
                                    </button>
                                    <input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");

?>