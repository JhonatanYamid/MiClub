<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get("title")) ?>
        </h4>
    </div>
    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data" novalidate>
                        <div class="form-group first ">
                                <div  class="col-xs-12 col-sm-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Modulo"> Modulo </label>
                                    <div class="col-sm-8">
                                        <select name = "Modulo" id="Modulo">
                                            <?
                                                $options = array("","Socios","Banner","Cuestionario Luker","Bicicletas","Talonera","Alimentos Casino","Registrar Alimentos",
                                                                "Ingreso Mascotas","Boton Panico","Empleados Luker","primadera","Entre Lomas Censo","Cumpleaños.",
                                                                "Familiares Vacunacion","Historial Socios","Auxilios","Tiempos para mi","No residentes","Noticias","Eventos",
                                                                "Eventos 2","Galerias","Galerias2","Directorios","Documentos","Documentos 2","Documentos 3","Documentos personales",
                                                                "Publicidad.","Faqs","Laboral","Vacunación","Vacunación 2","Restaurante","Fechas Cierre Club","Encuestas",
                                                                "Encuesta Vial","Auto-Diagnostico","Registro Contacto","Dotación","Encuesta Calificacion","Transporte",
                                                                "Movilidad","Votaciones","Notif. Generales","Domicilios","Domicilios Func.","Domiciliarios","Pqr Socios",
                                                                "Pqr Funcionarios","Clasificados","Clasificados Func.","Objetos Perdidos","Oferta Laboral","Postulados",
                                                                "Beneficios","Valet Parking","Canjes","Hotel","Reportes","Control de Puertas","Invitados","Fechas Especial Inv.",
                                                                "Pantalla Acceso","Acceso Automatico","Accesos","Correspondencia","CMS","Código Pago","Cursos","Reconocimientos",
                                                                "Cartas","Pagos","Socio Reserva Golf","Caddies","Reservas");
                                                foreach($options as $option){
                                                    if($frm["Modulo"] == $option){
                                                        echo '<option value="' .$option. '" selected="selected">' .$option. '</option>';
                                                    }else{
                                                        echo '<option value="' .$option. '">' .$option. '</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombre"> Nombre </label>
                                <div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Es"> Español </label>
                                <div class="col-sm-8"><input type="text" id="Es" name="Es" placeholder="Es" class="col-xs-12 mandatory" title="Es" value="<?php echo $frm["Es"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="En"> Inglés </label>
                                <div class="col-sm-8"><input type="text" id="En" name="En" placeholder="En" class="col-xs-12 mandatory" title="En" value="<?php echo $frm["En"]; ?>"></div>
                            </div>
                        </div>
                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Zh"> Chino </label>
                                <div class="col-sm-8"><input type="text" id="Zh" name="Zh" placeholder="Zh" class="col-xs-12 mandatory" title="Zh" value="<?php echo $frm["Zh"]; ?>"></div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Pt"> Portugués </label>
                                <div class="col-sm-8"><input type="text" id="Pt" name="Pt" placeholder="Pt" class="col-xs-12 mandatory" title="Pt" value="<?php echo $frm["Pt"]; ?>"></div>
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
                                    <?php echo $titulo_accion; ?> <?php echo SIMReg::get("title") ?>
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