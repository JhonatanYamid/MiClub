<?
	include( "../../procedures/general.php" );
	include( "../../procedures/invitadosgeneral.php" );

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Club</title>


		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../assets/font-awesome/4.2.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="../../assets/css/chosen.min.css" />

		<!-- page specific plugin styles -->
		<link rel="stylesheet" href="../../assets/css/datepicker.min.css" />
		<link rel="stylesheet" href="../../assets/css/ui.jqgrid.min.css" />


		<!-- page specific plugin styles -->
		<link rel="stylesheet" href="../../assets/css/jquery-ui.min.css" />


		<!-- text fonts -->
		<link rel="stylesheet" href="../../assets/fonts/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="../../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!-- 22cero2 styles -->
		<link rel="stylesheet" href="../../assets/css/22cero2.css"  />


		<!-- ace settings handler -->
		<script src="../../assets/js/ace-extra.min.js"></script>



	</head>

	<body class="no-skin">

              <table id="simple-table" class="table table-striped table-bordered table-hover">
                      <tr>
                        <td colspan="9">
                        	<a href="../../procedures/excel-reporteaccesosinv.php?IDClub=<?php echo SIMUser::get("club"); ?>&IDInvitado=<?php echo $_GET["IDInvitado"] ?>"><img src="../../assets/img/xls.gif" >Exportar</a>
                        </td>
                      </tr>
                      <tr>
                              <th>Tipo</th>
                               <th>Fecha Inicio Aut.</th>
                              <th>Fecha Fin Aut.</th>
                              <th>Tipo Movimiento</th>
                              <th>Fecha Movimiento</th>
                              <th>Cabeza Invitacion</th>
                              <th>Socio</th>
                              <th>Documento Socio</th>
                              <th>Accion</th>
                      </tr>
                      <tbody id="listacontactosanunciante">
                      <?php
							$sql_log_accesos = "Select *
												    From SocioInvitadoEspecial SIE, LogAcceso LA
													Where SIE.IDSocioInvitadoEspecial = LA.IDInvitacion
													And IDInvitado = '".$GET["IDInvitado"]."'
													Order By LA.IDLogAcceso DESC
													";
							   $query_log_accesos = $dbo->query($sql_log_accesos);
                              //$r_datos =& $dbo->all( "SocioInvitadoEspecial" , "IDInvitado = '" . $frm[$key]  ."' Order By IDSocioInvitadoEspecial DESC");

                              while( $r = $dbo->object( $query_log_accesos ) )
                              {
                      ?>

                      <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1'?>">
                              <td><?php echo $r->TipoInvitacion; ?></td>
                              <td><?php echo $r->FechaInicio; ?></td>
                              <td><?php echo $r->FechaFin; ?></td>
                              <td><?php
							  if($r->Entrada == "S"):
							  	echo "Entrada";
							  else:
							  	echo "Salida";
							  endif;
							  ?></td>
                              <td><?php
                              if($r->Entrada == "S"):
							  	echo $r->FechaIngreso;
							  else:
							  	echo $r->FechaSalida;
							  endif;
							  ?></td>
                              <td><?php echo $r->CabezaInvitacion; ?></td>
                              <td><?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$r->IDSocio."'" ); ?></td>
                              <td><?php echo $dbo->getFields( "Socio" , "NumeroDocumento" , "IDSocio = '".$r->IDSocio."'" ); ?></td>
                              <td><?php echo $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$r->IDSocio."'" ); ?></td>
                        </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                      <tr>
                              <th class="texto" colspan="19"></th>
                      </tr>
              </table>

            </body>
          </html>
