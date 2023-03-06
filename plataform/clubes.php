<?
	include( "procedures/general.php" );
	include( "procedures/clubes.php" );
	include("cmp/seo.php");

?>
<link rel="stylesheet" href="assets/css/datepicker.min.css" />
<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
</head>

<body class="no-skin">
    <?
			include( "cmp/header.php" );
		?>
    <div class="main-container" id="main-container">
        <script type="text/javascript">
        try {
            ace.settings.check('main-container', 'fixed')
        } catch (e) {}
        </script>
        <?
				$menu_club = " class=\"active\" ";
				include( "cmp/menu.php" );
			?>
        <div class="main-content">
            <div class="main-content-inner">
                <div class="breadcrumbs" id="breadcrumbs">
                    <script type="text/javascript">
                    try {
                        ace.settings.check('breadcrumbs', 'fixed')
                    } catch (e) {}
                    </script> <?php include("cmp/breadcrumb.php"); ?>
                </div>
                <div class="page-content">
                    <div class="ace-settings-container" id="ace-settings-container"> <?php if(SIMUser::get("IDPerfil")==0): ?> <button class="btn btn-danger btnRedirect" rel="clubes.php?action=add">
                            <i class="ace-icon fa fa-file align-top bigger-125"></i> Nuevo Club </button> <?php endif; ?> </div>
                    <div class="page-header"> <?php include("cmp/migapan.php"); ?> </div><!-- /.page-header -->
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS --> 
							<?php
							if($newmode==""): ?> 
								<iv>
								<ul class="ace-thumbnails clearfix">
									<?
									if(SIMUser::get("IDPerfil")==0):

										foreach( $array_clubes as $idclub => $datos_club )
										{
											//traer servicios maestros activos
											//$sql_servicios = "SELECT ServicioMaestro.Nombre FROM ServicioClub , ServicioMaestro  WHERE ServicioClub.IDClub = '" . $idclub . "' AND ServicioClub.Activo = 'S' AND ServicioClub.IDServicioMaestro = ServicioMaestro.IDServicioMaestro ";
											//$qry_servicios = $dbo->query( $sql_servicios );

											// Si el club tienen hijos cambio el link para mostrarlos
											$sql_club_hijo="SELECT IDClub FROM Club WHERE IDClubPadre = '".$idclub."' LIMIT 1";
											$r_club_hijo=$dbo->query($sql_club_hijo);


											?>
											<li>
												<a href="set_club.php?id=<?=$idclub; ?>" title="administrar club">
													<img width="150" height="150" alt="150x150" src="<?=CLUB_ROOT . $datos_club["FotoLogoApp"] ?>" />
													<div class="text">
														<div class="inner"><?=$datos_club["Nombre"] ?></div>
													</div>
												</a>
												<div class="tags">
													<?
													$label = "";

													while( $r_servicios = $dbo->fetchArray( $qry_servicios ) )
													{
														$label = SIMUtil::repetition()?'label-success':'label-success'; //label-danger
														?>
														<span class="label-holder">
															<span class="label <?=$label ?>"><?=$r_servicios["Nombre"] ?></span>
														</span>
														<?
													}//end if

													?>
												</div>
												<div class="tools">
													<a href="set_club.php?id=<?=$idclub; ?>" title="administrar club">
														<i class="ace-icon fa fa-paperclip"></i>
													</a>
													<a href="clubes.php?action=edit&id=<?php echo $datos_club["IDClub"]; ?>" title="Editar información">
														<i class="ace-icon fa fa-pencil"></i>
													</a> 
													<?php
													if($dbo->rows($r_club_hijo)>0)
													{ ?> 
														<a href="clubes.php?id=<?=$idclub; ?>&Tipo=Padre" title="Ver SubConjuntos">
															<i class="ace-icon fa fa-sitemap"></i>
														</a> 
														<?php 
													}?>
													<!-- SOLO PARA LOS CLUBES HIJOS SE PODRA HACER UNA COPIA PARA EVITAR COPIAR CUBLES POR ERROR -->
													<?php	
													if(isset($_GET[Tipo]))
													{ 
														$clubpadre = $_GET[id];
														?> 
														<a href="clubes.php?action=copiar&clubpadre=<?=$clubpadre; ?>&id=<?php echo $datos_club["IDClub"]; ?>" title="Copiar">
															<i class="ace-icon fa fa-files-o"></i>
														</a>
														<?php 
													}?>												
												</div>
											</li>
												<?
										}//end for

									endif;
									?>
								</ul> 
								<?php 
							endif; ?>
							<?
							if(!empty($view))
								include( $view );
							?>
                        </div><!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    <?
				include("cmp/footer.php");
			?>
    </div><!-- /.main-container -->
    <?
			include( "cmp/footer_scripts.php" );
		?>
    <script type="text/javascript">
    var urlGrid = 'includes/async/EstadoSaludCRUD.async.php';
    var grid_selector = "#grid-table";
    var pager_selector = "#grid-pager";
    $('#submitEstadoSalud').click(function() {
        if ($("#NombreEstadoSalud").val() == '') {
            n = noty({
                text: "<br><br>Error debe ingresar un Nombre !<br><br>",
                type: 'warning',
                dismissQueue: true,
                layout: "topCenter",
                theme: 'defaultTheme',
                modal: true,
                timeout: 1500,
                closeWith: ['button'],
                buttons: false,
                animation: {
                    open: {
                        height: 'toggle'
                    },
                    close: {
                        height: 'toggle'
                    },
                    easing: 'swing',
                    speed: 500 // opening & closing animation speed
                }
            });
            $('#NombreEstadoSalud').focus();
            return false;
        }
        $.ajax({
            url: urlGrid,
            method: "POST",
            data: 'oper=insert&Nombre=' + $('#NombreEstadoSalud').val() + '&Descripcion=' + $('#DescripcionEstadoSalud').val(),
            type: 'json',
            async: true,
            success: function(data) {
                if (data.sucess) {
                    $(grid_selector).jqGrid('setGridParam', {
                        url: urlGrid,
                        datatype: "json"
                    }).trigger("reloadGrid");
                    $('#NombreEstadoSalud').val('');
                    $('#DescripcionEstadoSalud').val('');
                    n = noty({
                        text: "<br><br>" + data.msg + " !<br><br>",
                        type: 'sucess',
                        dismissQueue: true,
                        layout: "topCenter",
                        theme: 'defaultTheme',
                        modal: true,
                        timeout: 1500,
                        closeWith: ['button'],
                        buttons: false,
                        animation: {
                            open: {
                                height: 'toggle'
                            },
                            close: {
                                height: 'toggle'
                            },
                            easing: 'swing',
                            speed: 500 // opening & closing animation speed
                        }
                    });
                } else {
                    n = noty({
                        text: "<br><br>" + data.msg + " !<br><br>",
                        type: 'error',
                        dismissQueue: true,
                        layout: "topCenter",
                        theme: 'defaultTheme',
                        modal: true,
                        timeout: 1500,
                        closeWith: ['button'],
                        buttons: false,
                        animation: {
                            open: {
                                height: 'toggle'
                            },
                            close: {
                                height: 'toggle'
                            },
                            easing: 'swing',
                            speed: 500 // opening & closing animation speed
                        }
                    });
                }
            }
        });
        return false;
    });
    var lastsel;
    jQuery(grid_selector).jqGrid({
        url: urlGrid,
        datatype: "json",
        colNames: ['Estado', 'Descripci&oacute;n'],
        colModel: [{
            name: 'Nombre',
            index: 'Nombre',
            align: "left",
            width: '120',
            editable: true,
            sortable: false
        }, {
            name: 'Descripcion',
            index: 'Descripcion',
            align: "left",
            width: '240',
            editable: true,
            sortable: false
        }, ],
        rowNum: 100,
        rowList: [20, 40, 100],
        //	sortname: 'Nombre',
        viewrecords: true,
        //	sortorder: "DESC",
        caption: "",
        height: "100%",
        width: "500",
        altRows: true,
        //toppager: true,
        multiselect: false,
        editable: true,
        loadComplete: function() {
            var table = this;
            //	preparaform();
        },
        onSelectRow: function(id) {
            console.log(id);
            if (id && id !== lastsel && id > 2) {
                console.log(id);
                jQuery(grid_selector).jqGrid('restoreRow', lastsel);
                jQuery(grid_selector).jqGrid('editRow', id, true);
                lastsel = id;
            }
        },
        editurl: urlGrid
    });
    jQuery(grid_selector).jqGrid('navGrid', pager_selector, {
        edit: false,
        add: false,
        del: false
    });
    // ENd ready
    $(window).on('resize.jqGrid', function() {
        $(grid_selector).jqGrid('setGridWidth', $(".page-content").width());
    });
    var parent_column = $(grid_selector).closest('[class*="col-"]');
    $(document).one('ajaxloadstart.page', function(e) {
        $(grid_selector).jqGrid('GridUnload');
        $('.ui-jqdialog').remove();
    });
    </script>
</body>

</html>