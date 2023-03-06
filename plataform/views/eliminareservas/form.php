<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-comments-o orange"></i>ENVIAR NOTIFICACIÓN SOCIOS CON RESERVA PARA <?=strtoupper( SIMUtil::tiempo( date( "Y-m-d" ) ) )?>
        </h4>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <form class="form-horizontal formvalida" role="form" method="post" id="frmEliminaReservaMasiva" name="frmEliminaReservaMasiva" action="<?php echo SIMUtil::lastURI()?>">
                <div class="form-group first ">
                    <div class="col-sm-12">
                        <label for="Mensaje"> Hora Inicio </label><br>
                        <div class="col-sm-8">
                            <input type="time" id="HoraInicio" name="HoraInicio"  class="col-xs-12" value="<?php echo date("Y-m-d") ?>">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label for="Mensaje"> Hora Fin </label><br>
                        <div class="col-sm-8">
                            <input type="time" id="HoraFin" name="HoraFin"  class="col-xs-12" value="<?php echo date("Y-m-d") ?>">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label for="Mensaje"> Fecha para Eliminar </label><br>
                        <div class="col-sm-8">
                            <input type="text" id="FechaReserva" name="FechaReserva" placeholder="Fecha Reserva" class="col-xs-12 calendar" title="Fecha Reserva" value="<?php echo date("Y-m-d") ?>">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label for="Mensaje"> Elemento del cual se quiere eliminar </label><br>
                        <div class="col-sm-8">
                            <div class="col-sm-8"><?php echo SIMHTML::formPopUp( "ServicioElemento" , "Nombre" , "Nombre" , "IDServicioElemento" , $frm["IDElemento"] , "[Seleccione el elemento]" , "form-control" , "title = \"IDTipo Archivo\"", "AND IDServicio ='".$_GET['ids']."'" )?></div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label for="Mensaje"> Razón Eliminación Masica </label>
                        <textarea id="RazonCancelacion" name="RazonCancelacion" class="input form-control"></textarea>                       
                    </div>
                </div>
                <div class="clearfix form-actions">
                    <div class="col-xs-12 text-center">
                        <input type="hidden" name="action" value="insert">
                        <input type="hidden" id="ids" name="ids" value="<?=$ids?>">                        
                        <input type="hidden" name="UsuarioElimina" id="UsuarioElimina" value="<?php echo $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".SIMUser::get("IDUsuario")."'" ); ?>">
                        <input type="submit" name="elimina_reserva" id="elimina_reserva" value="Elimina Reserva"> </td>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.widget-main -->
<?
	include( "cmp/footer_scripts.php" );
?>