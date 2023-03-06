<div class="row">
    <div class="col-sm-12">

        <div id="accordion" class="accordion-style1 panel-group">


            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSalud">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Estado Salud
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "EstadoSalud") echo "in"; ?>" id="collapseSalud">
                    <div class="panel-body">
                        <?php include("views/clubes/EstadoSalud.php");
                        ?>
                    </div>
                </div>
            </div>



            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Modalidades Esqui
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tab"] == "modalidad") echo "in"; ?>" id="collapseThree">
                    <div class="panel-body">
                        <?php include("modalidades.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse8">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Tipo, Categoria, Parentesco, Datos Carne
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "tiposocioclub") echo "in"; ?>" id="collapse8">
                    <div class="panel-body">
                        <?php include("tiposocioclub.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse6">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Campos Directorio Club
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "camposdirectorioclub") echo "in"; ?>" id="collapse6">
                    <div class="panel-body">
                        <?php include("camposdirectorioclub.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse7">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Campos Directorio Socios
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "camposdirectoriosocio") echo "in"; ?>" id="collapse7">
                    <div class="panel-body">
                        <?php include("camposdirectoriosocio.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse14">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Configuracion Directorio Socios...
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "configuraciondirectoriosocio") echo "in"; ?>" id="collapse14">
                    <div class="panel-body">
                        <?php include("configuraciondirectoriosocios.php"); ?>
                    </div>
                </div>
            </div>



            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse15">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Configuracion Directorio
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "configuraciondirectorio") echo "in"; ?>" id="collapse15">
                    <div class="panel-body">
                        <?php include("configuraciondirectorio.php"); ?>
                    </div>
                </div>
            </div>



            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse16">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Configuracion Galeria
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "configuraciongaleria") echo "in"; ?>" id="collapse16">
                    <div class="panel-body">
                        <?php include("configuraciongaleria.php"); ?>
                    </div>
                </div>
            </div>


            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse17">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Configuracion Galeria Empleados
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "configuraciongaleriaempleados") echo "in"; ?>" id="collapse17">
                    <div class="panel-body">
                        <?php include("configuraciongaleriaempleados.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse10">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Campos Editar Perfil Socio
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "camposperfil") echo "in"; ?>" id="collapse10">
                    <div class="panel-body">
                        <?php include("camposperfil.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse13">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Campos Editar Perfil Funcionario
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "camposperfilfuncionario") echo "in"; ?>" id="collapse13">
                    <div class="panel-body">
                        <?php include("camposperfilfuncionario.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse11">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Campos Registro Contacto
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "camposregistrocontacto") echo "in"; ?>" id="collapse11">
                    <div class="panel-body">
                        <?php include("camposregistrocontacto.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse12">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Campos Contacto Externo
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "camposcontactoexterno") echo "in"; ?>" id="collapse12">
                    <div class="panel-body">
                        <?php include("camposcontactoexterno.php"); ?>
                    </div>
                </div>
            </div>


            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse5">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Configuraci&oacute;n Accesos
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "accesos") echo "in"; ?>" id="collapse5">
                    <div class="panel-body">
                        <?php include("accesos.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsenot">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Configuraci&oacute;n Noticias
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "noticias") echo "in"; ?>" id="collapsenot">
                    <div class="panel-body">
                        <?php include("confignoticias.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse9">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Campos formulario acceso
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "camposacceso") echo "in"; ?>" id="collapse9">
                    <div class="panel-body">
                        <?php include("camposacceso.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsefour">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Ubicacion Caddies
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tab"] == "ubicacion") echo "in"; ?>" id="collapsefour">
                    <div class="panel-body">
                        <?php include("ubicacioncaddie.php"); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsereservas">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Configuración modulo de reservas
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "configuracionreservas") echo "in"; ?>" id="collapsereservas">
                    <div class="panel-body">
                        <?php include("configuracionreservas.php"); ?>
                    </div>
                </div>
            </div>

  <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsefacturacion">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Configuración facturacion
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "configuracionfacturacion") echo "in"; ?>" id="collapsefacturacion">
                    <div class="panel-body">
                        <?php include("configuracionfacturacion.php"); ?>
                    </div>
                </div>
            </div>
            
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsepreguntasinvitados">
                            <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                            &nbsp;Preguntas modulo de invitados
                        </a>
                    </h4>
                </div>

                <div class="panel-collapse collapse <?php if ($_GET["tabparametro"] == "preguntasinvitados") echo "in"; ?>" id="collapsepreguntasinvitados">
                    <div class="panel-body">
                        <?php include("preguntasinvitados.php"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.col -->

</div>
