<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor ?>

<form class="form-horizontal formvalida" role="form" method="post" id="AppEmpleado<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

    <?php
			 $id_app_empleado = $dbo->getFields( "AppEmpleado" , "IDAppEmpleado" , "IDClub = '".$_GET[id]."'" );
			 //Si esta vacio es por que no se ha creado la conf de app para empleado, entmces la creo con el id para luego editarlo
			 if(empty($id_app_empleado)):
			 	$sql_inserta_conf_inicial = "Insert Into AppEmpleado (IDClub) Values ('".$_GET[id]."') ";
				$dbo->query($sql_inserta_conf_inicial);
			 endif;

                 		  $EditAppEmpleado =$dbo->fetchAll("AppEmpleado"," IDClub = '".$_GET[id]."' ","array");
                          $action = "ModificaAppEmpleado";
                          ?>
    <input type="hidden" name="IDAppEmpleado" id="IDAppEmpleado" value="<?php echo $EditAppEmpleado[IDAppEmpleado]?>" />
    <?php
                  ?>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Datos Basicos
        </h3>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color 1 </label>

            <div class="col-sm-8">
                <input name="Color1" type="color" value="<?php if (empty($EditAppEmpleado["Color1"])) { echo "#FFFFFF"; } else{ echo $EditAppEmpleado["Color1"]; }    ?>" />
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Color 2 </label>

            <div class="col-sm-8">
                <input name="Color2" type="color" value="<?php if (empty($EditAppEmpleado["Color2"])) { echo "#FFFFFF"; } else{ echo $EditAppEmpleado["Color2"]; }    ?>" />
            </div>
        </div>

    </div>




    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto </label>

            <div class="col-sm-8">
                <? if (!empty($EditAppEmpleado[Foto])) {
													echo "<img src='".CLUB_ROOT."$EditAppEmpleado[Foto]' width=55 >";
													?>
                <a href="<? echo $script." .php?action=delfoto&foto=$EditAppEmpleado[Foto]&campo=Foto&id=".$EditAppEmpleado[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
												}// END if
												?>
                <input name="Foto" id=file class="col-xs-12" title="Foto" type="file" size="25" style="font-size: 10px">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto Dise&ntilde;o </label>

            <div class="col-sm-8">

                <? if (!empty($EditAppEmpleado[FotoDiseno1])) {
												echo "<img src='".CLUB_ROOT."$EditAppEmpleado[FotoDiseno1]' width=55 >";
												?>
                <a href="<? echo $script." .php?action=delfoto&foto=$EditAppEmpleado[FotoDiseno1]&campo=FotoDiseno1&id=".$EditAppEmpleado[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

                <?
											}// END if
											?>
                <input name="FotoDiseno1" id=file class="col-xs-12" title="Foto" type="file" size="25" style="font-size: 10px">


            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Logo Club App </label>

            <div class="col-sm-8">
                <? if (!empty($EditAppEmpleado[FotoLogoApp])) {
												echo "<img src='".CLUB_ROOT."$EditAppEmpleado[FotoLogoApp]' width=55 >";
												?>
                <a href="<? echo $script." .php?action=delfoto&foto=$EditAppEmpleado[FotoLogoApp]&campo=FotoLogoApp&id=".$EditAppEmpleado[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
											}// END if
											?>
                <input name="FotoLogoApp" id=FotoLogoApp class="col-xs-12" title="FotoLogoApp" type="file" size="25" style="font-size: 10px">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Notificaciones </label>

            <div class="col-sm-8">
                <input id=EmailNotificaciones type=text size=25 name=EmailNotificaciones class="col-xs-12" title="EmailNotificaciones" value="<?=$EditAppEmpleado[EmailNotificaciones] ?>">
            </div>
        </div>

    </div>






    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-cloud-download green"></i>
            Control de Versiones
        </h3>
    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version App IOS </label>

            <div class="col-sm-8">
                <input id=Version type=text size=25 name=Version class="col-xs-12" title="Version" value="<?=$EditAppEmpleado[Version] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Es Esencial IOS? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditAppEmpleado["Esencial"] , 'Esencial' , "class='input'" ) ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version Message IOS </label>

            <div class="col-sm-8">
                <input id="VersionMessage" type=text size=25 name=VersionMessage class="col-xs-12" title="Version Message" value="<?=$EditAppEmpleado[VersionMessage] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version URL IOS </label>

            <div class="col-sm-8">
                <input id="VersionURLIOS" type=text size=25 name="VersionURLIOS" class="col-xs-12" title="Version URL IOS" value="<?=$EditAppEmpleado[VersionURLIOS] ?>">
            </div>
        </div>

    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version App Android </label>

            <div class="col-sm-8">
                <input id=VersionAndroid type=text size=25 name=VersionAndroid class="col-xs-12" title="Version Android" value="<?=$EditAppEmpleado[VersionAndroid] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Es Esencial Android? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditAppEmpleado["EsencialAndroid"] , 'EsencialAndroid' , "class='input'" ) ?>
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version Message Android </label>

            <div class="col-sm-8">
                <input id="VersionMessageAndroid" type=text size=25 name=VersionMessageAndroid class="col-xs-12" title="Version Message Android" value="<?=$EditAppEmpleado[VersionMessageAndroid] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Version URL Andoird </label>

            <div class="col-sm-8">
                <input id="VersionURLAndroid" type=text size=25 name=VersionURLAndroid class="col-xs-12" title="Version URL Android" value="<?=$EditAppEmpleado[VersionURLAndroid] ?>">
            </div>
        </div>

    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-paper-plane green"></i>
            Configuraci&oacute;n Push
        </h3>
    </div>



    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> API Google </label>

            <div class="col-sm-8">
                <input id=GCM_API_KEY type=text size=25 name=GCM_API_KEY class="col-xs-12" title="API Google" value="<?=$EditAppEmpleado["GCM_API_KEY"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ambiente Push </label>

            <div class="col-sm-8">
                <select name="PUSH_ENVIRONMENT" id="PUSH_ENVIRONMENT" class="popup mandatory" title="PUSH_ENVIRONMENT">
                    <option value="">[Seleccione el Ambiente Actual]</option>
                    <option value="prod" <? if( $EditAppEmpleado["PUSH_ENVIRONMENT"]=="prod" ) echo " selected='selected' " ?> >Produccion</option>
                    <option value="dev" <? if( $EditAppEmpleado["PUSH_ENVIRONMENT"]=="dev" ) echo " selected='selected' " ?> >Desarrollo</option>
                </select>
            </div>
        </div>

    </div>


    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Direccion del Certificado </label>

            <div class="col-sm-8">
                <input id=CERTIFICATES_DIR type=text size=25 name=CERTIFICATES_DIR class="col-xs-12" title="Certificado" value="<?=$EditAppEmpleado["CERTIFICATES_DIR"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Clave del Certificado </label>

            <div class="col-sm-8">
                <input id=APNS_PASSPHRASE type=text size=25 name=APNS_PASSPHRASE class="col-xs-12" title="Certificado" value="<?=$EditAppEmpleado["APNS_PASSPHRASE"] ?>">
            </div>
        </div>



    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Labels Pesonalizados
        </h3>
    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Terminos y condiciones (login app) </label>

            <div class="col-sm-8">
                <input id=LabelTerminos type=text size=25 name=LabelTerminos class="col-xs-12" title="Label Terminos" value="<?=$EditAppEmpleado["LabelTerminos"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Tipo Terminos y Condiciones </label>

            <div class="col-sm-8">
                <select name="TipoTerminos" id="TipoTerminos" class="form-control">
                    <option value=""></option>
                    <option value="Archivo" <?php if($EditAppEmpleado["TipoTerminos"]=="Archivo") echo "selected"; ?>>Descargar Archivo</option>
                    <option value="Texto" <?php if($EditAppEmpleado["TipoTerminos"]=="Texto") echo "selected"; ?>>Abrir texto</option>
                </select>
            </div>
        </div>


    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pdf terminos y condiciones </label>

            <div class="col-sm-8">
                <? if (!empty($EditAppEmpleado[ArchivoTerminos])) {
													echo "<a href='".CLUB_ROOT."$EditAppEmpleado[ArchivoTerminos]' target='_blank'>".$EditAppEmpleado[ArchivoTerminos]."</a>";
													?>
                <a href="<? echo $script." .php?action=delfoto&foto=$EditAppEmpleado[ArchivoTerminos]&campo=ArchivoTerminos&id=".$EditAppEmpleado[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                <?
												}// END if
												?>
                <input name="ArchivoTerminos" id=file class="col-xs-12" title="ArchivoTerminos" type="file" size="25" style="font-size: 10px">

            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label al invitar </label>

            <div class="col-sm-8">
                <input id=LabelInvitacion type=text size=25 name=LabelInvitacion class="col-xs-12" title="Label Invitacion" value="<?=$EditAppEmpleado["LabelInvitacion"] ?>">
            </div>
        </div>


    </div>

	<div class="form-group first ">        

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Autodiagnostico </label>

            <div class="col-sm-8">
                <input id=LabelDiagnostico type=text size=25 name=LabelDiagnostico class="col-xs-12" title="Label Diagnostico" value="<?=$EditAppEmpleado["LabelDiagnostico"] ?>">
            </div>
        </div>
    </div>


    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Otros datos
        </h3>
    </div>





    <div class="col-xs-12 col-sm-6">
        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo para carn&eacute;: </label>

        <div class="col-sm-8">
            <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$tipocodigocarne ) , $EditAppEmpleado["TipoCodigoCarne"] , 'TipoCodigoCarne' , "class='input '" ) ?>
        </div>
    </div>
    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Porcentaje Codigo Qr Android? </label>

            <div class="col-sm-8">
                <input id="PorcentajeQrAndroid" type=text size=25 name="PorcentajeQrAndroid" class="col-xs-12" title="Porcentaje Qr Android" value="<?=$EditAppEmpleado["PorcentajeQrAndroid"] ?>">
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Porcentaje Codigo Qr IOS: </label>

            <div class="col-sm-8">
                <input id="PorcentajeQrIOS" type=text size=25 name="PorcentajeQrIOS" class="col-xs-12" title="Porcentaje Qr IOS" value="<?=$EditAppEmpleado["PorcentajeQrIOS"] ?>">
            </div>
        </div>

    </div>

    <div class="form-group first ">

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Permitir a portero hacer invitacion?: </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditAppEmpleado["PermiteInvitacionPortero"] , 'PermiteInvitacionPortero' , "class='input'" ) ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Opciones tipo ingreso separadas por coma?: </label>

            <div class="col-sm-8">
                <input id="OpcionesIngreso" placeholder="Peatonal, Vehiculo, etc" type=text size=25 name="OpcionesIngreso" class="col-xs-12" title="OpcionesIngreso" value="<?=$EditAppEmpleado["OpcionesIngreso"] ?>">
            </div>
        </div>

    </div>

    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-info-circle green"></i>
            Publicidad
        </h3>
    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habilitar Publicidad? </label>

            <div class="col-sm-8">
                <? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $EditAppEmpleado["Publicidad"] , 'Publicidad' , "class='input '" ) ?>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo rotar Publicidad? </label>

            <div class="col-sm-8">
                <input id="TiempoPublicidad" type=text size=25 name="TiempoPublicidad" class="col-xs-12" title="Tiempo Publicidad" value="<?=$EditAppEmpleado["TiempoPublicidad"] ?>">segundos
            </div>
        </div>

    </div>

    <div class="form-group first ">



        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de Header App? </label>

            <div class="col-sm-8">
                <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Publicidad" <?php if($EditAppEmpleado["TipoHeaderApp"]=="Publicidad") echo "checked"; ?>> Publicidad (rota im&aacute;genes)
                <br>
                <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="Clasico" <?php if($EditAppEmpleado["TipoHeaderApp"]=="Clasico") echo "checked"; ?>> Clasico (imagen fija logo club)
                <br>
                <input type="radio" name="TipoHeaderApp" id="TipoHeaderApp" value="PublicidadFoto" <?php if($EditAppEmpleado["TipoHeaderApp"]=="PublicidadFoto") echo "checked"; ?>> PublicidadFoto (Publicidad mas foto)
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tiempo Rotar Publicidad Header? </label>

            <div class="col-sm-8">
                <input id="TiempoPublicidadHeader" type=text size=25 name="TiempoPublicidadHeader" class="col-xs-12" title="Tiempo Publicidad Header" value="<?=$EditAppEmpleado["TiempoPublicidadHeader"] ?>">segundos
            </div>
        </div>

    </div>



    <div class="form-group first">


        T&eacute;rminos y Condiciones

        <div class="col-sm-12">
            <?php
												$oCuerpo = new FCKeditor( "TerminosEmpleados" ) ;
												$oCuerpo->BasePath = "js/fckeditor/";
												$oCuerpo->Height = 400;
												//$oCuerpo->EnterMode = "p";
												$oCuerpo->Value =  $EditAppEmpleado["TerminosEmpleados"];
												$oCuerpo->Create() ;
											?>
        </div>


    </div>



    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-sitemap green"></i>
            Configuracion de Modulos
        </h3>
    </div>
    <div class="form-group first ">

        <div class="col-xs-12 col-sm-12">
            <?php
											  // Consulto los modulos disponibles del club
											  $sql_modulo_club=$dbo->query("select * from AppEmpleadoModulo where IDClub = '".$EditAppEmpleado[IDClub]."' and Activo = 'S'");
											  while($r_modulo_club=$dbo->object($sql_modulo_club)){
												  $modulo_appempleado[]=$r_modulo_club->IDModulo;
											  }
                        // Consulto los modulos disponibles del club
											  $sql_modulo=$dbo->query("select * from Modulo where 1");
											  while($r_modulo=$dbo->object($sql_modulo)){
												  $modulo_datos[$r_modulo->IDModulo]=$r_modulo->Nombre;
											  }
								?>
            <table id="simple-table" class="table table-striped table-bordered table-hover">
                <tr>
                    <th>Activo</th>
                    <th>Modulo</th>
                    <th>Titulo Club</th>
                    <th>Icono</th>
                    <th>Orden</th>
                    <th>Ubicacion</th>
                </tr>
                <tbody id="listacontactosanunciante">
                    <?php

                                                  $r_modulo =& $dbo->all( "AppEmpleadoModulo" , "IDClub = '".$EditAppEmpleado[IDClub]."'");

                                                  while( $r = $dbo->object( $r_modulo ) )
                                                  {
                                          ?>

                    <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                        <td aling="center">
                            <input type="checkbox" name="IDModulo<?php echo $r->IDModulo; ?>" id="IDModulo<?php echo $r->IDModulo; ?>" <?php if (in_array($r->IDModulo,$modulo_appempleado)) echo "checked"; ?>>
                        </td>
                        <td><?php echo $modulo_datos[$r->IDModulo]; ?></td>
                        <td>
                            <input id=Titulo<?php echo $r->IDModulo; ?> type=text size=25 name=Titulo<?php echo $r->IDModulo; ?> class="col-xs-12" title="Titulo" value="<?=$r->Titulo ?>" placeholder="Titulo Menu Central">
                            <input id=TituloLateral<?php echo $r->IDModulo; ?> type=text size=25 name=TituloLateral<?php echo $r->IDModulo; ?> class="col-xs-12" title="Titulo Lateral" value="<?=$r->TituloLateral ?>" placeholder="Titulo menu lateral">
                        </td>

                        <td>
                            <? if (!empty($r->Icono)) {
														echo "<img src='".MODULO_ROOT."$r->Icono' width=55 >";
														?>
                            <a href="<? echo $script." .php?action=delfotomoduloempleado&foto=$r->Icono&campo=Icono&idAppEmpleadoModulo=".$r->IDAppEmpleadoModulo; ?>&id=<?php echo $EditAppEmpleado[IDClub]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
                            <?
													}// END if
													?>
                            <input name="Icono<?php echo $r->IDModulo; ?>" id=Icono<?php echo $r->IDModulo; ?> class="col-xs-12" title="Icono" type="file" size="25" style="font-size: 10px">
                            <input type="hidden" name="ImagenOriginal<?php echo $r->IDModulo; ?>" id="ImagenOriginal<?php echo $r->IDModulo; ?>" value="<?php echo $r->Icono; ?>">
                        </td>
                        <td><input id=Orden<?php echo $r->IDModulo; ?> type=text size=25 name=Orden<?php echo $r->IDModulo; ?> class="col-xs-12" title="Orden" value="<?=$r->Orden ?>"></td>
                        <td>
                            <?php
												  unset($ubicacion_modulo);
                                                  if (!empty($r->Ubicacion)):
												  	$ubicacion_modulo = explode ("|",$r->Ubicacion);
												  endif;
												  ?>

                            <input type="checkbox" name="UbicacionModulo<?php echo $r->IDModulo; ?>[]" <?php if (in_array("Lateral",$ubicacion_modulo)) echo "checked"; ?> value="Lateral"> Menu Lateral app
                            <br><input type="checkbox" name="UbicacionModulo<?php echo $r->IDModulo; ?>[]" <?php if (in_array("Central",$ubicacion_modulo)) echo "checked"; ?> value="Central">Menu central app
                        </td>
                    </tr>
                    <?php
                                          }
                                          ?>
                </tbody>
                <tr>
                    <th class="texto" colspan="16"></th>
                </tr>
            </table>


        </div>
    </div>





    <div class="clearfix form-actions">
        <div class="col-xs-12 text-center">
            <input type="hidden" name="ID" id="ID" value="<?php echo $EditAppEmpleado[ $key ] ?>" />
            <input type="hidden" name="action" id="action" value="<?php echo $action?>" />
            <input type="hidden" name="IDClub" id="IDClub" value="<?php echo $EditAppEmpleado[ $key ]?>" />
            <input type="submit" class="submit" value="Guardar">

        </div>
    </div>

</form>