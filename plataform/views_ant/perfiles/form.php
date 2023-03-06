<div class="widget-box transparent" id="recent-box">
    <div class="widget-header">
        <h4 class="widget-title lighter smaller">
            <i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get( "title" ))?>
        </h4>


    </div>

    <div class="widget-body">
        <div class="widget-main padding-4">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->


                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>">

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Club </label>

                                <div class="col-sm-8">
                                    <select name="IDClub" id="IDClub" class="form-control">
										<option value="">SELECCIONE EL CLUB</option>
										<?php
										if (SIMUser::get("Nivel") == 0) :
											$condicion_club = "  1";
										else :
											$condicion_club = " IDClub = '" . SIMUser::get("club") . "' OR IDClubPadre = '" . SIMUser::get("club") . "'";
										endif;

										$sql_club_lista = "Select * From Club Where $condicion_club ";
										$qry_club_lista = $dbo->query($sql_club_lista);
										while ($r_club_lista = $dbo->fetchArray($qry_club_lista)) : ?>
											<option value="<?php echo $r_club_lista["IDClub"]; ?>" <?php if ($r_club_lista["IDClub"] == $frm["IDClub"]) echo "selected";  ?>><?php echo $r_club_lista["Nombre"]; ?></option>
										<?php
										endwhile;    ?>
									</select>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">



                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

                                <div class="col-sm-8">
                                    <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>">
                                </div>
                            </div>

                        </div>




                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>

                                <div class="col-sm-8">
                                    <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
                                </div>
                            </div>

                        </div>

                        <div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permiso Modulos </label>

                                <div class="col-sm-8">
                                    <?php 
                                        // Consulto los modulos disponibles del perfil
                                        $sql_modulo_perfil=$dbo->query("select * from ModuloPerfil where IDPerfil = '".$frm["IDPerfil"]."'");
                                        while($r_modulo_perfil=$dbo->object($sql_modulo_perfil)){
                                            $modulo_perfil[]=$r_modulo_perfil->IDModulo;
                                        }
                                        $arrayop = array();
                                        // consulto los modulos
                                        $query_modulos=$dbo->query("SELECT * FROM Modulo WHERE MostrarEnPerfiles = 'S' ORDER BY OrdenPerfiles ASC");
                                        while($r=$dbo->object($query_modulos)){
                                            $arraymodulos[$r->Nombre]=$r->IDModulo;  
                                        }
                                        echo SIMHTML::formCheckGroup( $arraymodulos , $modulo_perfil , "ModuloPerfil[]"); ?>
                                </div>
                            </div>
                        </div>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info-circle green"></i>
								Los siguientes permisos son en general para los modulos que se activen
							</h3>
						</div>

						<div class="form-group first ">

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permisos de Crear Registros </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoCrear"] , 'PermisoCrear' , "class='input'" ) ?>
								</div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permisos de Borrar Registros </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoBorrar"] , 'PermisoBorrar' , "class='input'" ) ?>
								</div>
                            </div>

                        </div>
						
						<div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permisos de Modificar Registros </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoModificar"] , 'PermisoModificar' , "class='input'" ) ?>
								</div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permisos de Exportar Registros </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoExportar"] , 'PermisoExportar' , "class='input'" ) ?>
								</div>
                            </div>

                        </div>						

                        <div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info-circle green"></i>
								Permisos para las reservas
							</h3>
						</div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permisos de eliminar reservas </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoEliminarReserva"] , 'PermisoEliminarReserva' , "class='input'" ) ?>
                                </div>
                            </div>                           

                        </div>				
						
                        

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir configuración de reservas </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoConfiguracion"] , 'PermisoConfiguracion' , "class='input'" ) ?>
								</div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir configuración general en reservas </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoGeneral"] , 'PermisoGeneral' , "class='input'" ) ?>
								</div>
                            </div>

                            
                        </div>	

                        <div class="form-group first ">
                            

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir configuración de disponibilidad en reservas </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoDisponibilidad"] , 'PermisoDisponibilidad' , "class='input'" ) ?>
								</div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir configuración de auxiliares en reservas </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoAuxiliares"] , 'PermisoAuxiliares' , "class='input'" ) ?>
								</div>
                            </div>
                        </div>		

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir configuración de elementos en reservas </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoElementos"] , 'PermisoElementos' , "class='input'" ) ?>
								</div>
                            </div>                            
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir configuración de Tipo Reserva en reservas </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoElementos"] , 'PermisoTipoReserva' , "class='input'" ) ?>
								</div>
                            </div>                            
                        </div>

                        <div class="form-group first ">
                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir configuración de preguntas reservas en reservas </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoPreguntas"] , 'PermisoPreguntas' , "class='input'" ) ?>
								</div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir fechas de cierre en reservas </label>

                                <div class="col-sm-8">
									<? echo SIMHTML::formradiogroup( SIMResources::$sinoNum , $frm["PermisoFechasCierre"] , 'PermisoFechasCierre' , "class='input'" ) ?>
								</div>
                            </div>   
                        </div>		
                        

                        <div class="clearfix form-actions">
                            <div class="col-xs-12 text-center">
                                <input type="hidden" name="ID" id="ID" value="<?php echo $frm[ $key ] ?>" />
                                <input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                <button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    <?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
                                </button>


                            </div>
                        </div>

                    </form>
                </div>
            </div>




        </div><!-- /.widget-main -->
    </div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>