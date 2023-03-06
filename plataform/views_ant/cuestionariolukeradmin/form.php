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
                    <form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
                        <div class="form-group first ">





                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="NivelDeRolAlCualPerteneceDentroDeLaOrganizacion">Nivel de rol al cual pertenece dentro de la  organización: </label>
                                    <div class="select col-sm-8">
                                        <select name="NivelDeRolAlCualPerteneceDentroDeLaOrganizacion" id="NivelDeRolAlCualPerteneceDentroDeLaOrganizacion" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Junta directiva" <?php if ($frm["NivelDeRolAlCualPerteneceDentroDeLaOrganizacion"] == "Junta directiva") echo "selected"; ?>>Junta directiva</option>
                                            <option value="Gerencia" <?php if ($frm["NivelDeRolAlCualPerteneceDentroDeLaOrganizacion"] == "Gerencia") echo "selected"; ?>>Gerencia</option>
                                            <option value="Directores" <?php if ($frm["NivelDeRolAlCualPerteneceDentroDeLaOrganizacion"] == "Directores") echo "selected"; ?>>Directores</option>
                                            <option value="Jefes o lider profesional" <?php if ($frm["NivelDeRolAlCualPerteneceDentroDeLaOrganizacion"] == "Jefes o lider profesional") echo "selected"; ?>>Jefes o lider profesional</option>
                                            <option value="Analista-auxiliares -mercaderistas" <?php if ($frm["NivelDeRolAlCualPerteneceDentroDeLaOrganizacion"] == "Analista-auxiliares -mercaderistas") echo "selected"; ?>> Analista-auxiliares -mercaderistas</option>
                                            <option value="Coordinador" <?php if ($frm["NivelDeRolAlCualPerteneceDentroDeLaOrganizacion"] == "Coordinador") echo "selected"; ?>>Coordinador</option>
                                            <option value=" Operativo" <?php if ($frm["NivelDeRolAlCualPerteneceDentroDeLaOrganizacion"] == "Operativo") echo "selected"; ?>> Operativo</option>
                                            <option value=" técnico" <?php if ($frm["NivelDeRolAlCualPerteneceDentroDeLaOrganizacion"] == "técnico") echo "selected"; ?>> técnico</option>

                                        </select>

                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="NombreDelProcesoOAreaDondeSeEncuentraSuRol">Nombre del Proceso o área dónde se encuentra su rol: </label>
                                    <div class="select col-sm-8">
                                        <select name="NombreDelProcesoOAreaDondeSeEncuentraSuRol" id="NombreDelProcesoOAreaDondeSeEncuentraSuRol" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Comercial-nuevos negocios" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Comercial-nuevos Negocios") echo "selected"; ?>>Comercial-nuevos negocios</option>
                                            <option value="Mercadeo-marcas" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Mercadeo-marcas") echo "selected"; ?>>Mercadeo-marcas</option>
                                            <option value="Marcas de canal" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Marcas de canal") echo "selected"; ?>>Marcas de canal</option>
                                            <option value="Innovación" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Innovación") echo "selected"; ?>>Innovación</option>
                                            <option value="Cadena de abastecimiento" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Cadena de abastecimiento") echo "selected"; ?>> Cadena de abastecimiento</option>
                                            <option value="Logística" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Logística") echo "selected"; ?>>Logística</option>
                                            <option value="Gestión" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Gestión") echo "selected"; ?>> Gestión</option>
                                            <option value="Talento" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Talento") echo "selected"; ?>> Talento</option>
                                            <option value="Cultura y  servicios de apoyo" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Cultura y  servicios de apoyo") echo "selected"; ?>>Cultura y  servicios de apoyo</option>
                                            <option value="Compras" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Compras") echo "selected"; ?>> Compras</option>
                                            <option value="Tesoreria-contabilidad" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Tesoreria-contabilidad") echo "selected"; ?>> Tesoreria-contabilidad</option>
                                            <option value="Juridica" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Juridica") echo "selected"; ?>> Juridica</option>
                                            <option value="Tecnología e información" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Tecnología e información") echo "selected"; ?>> Tecnología e información</option>
                                            <option value="Sostenibilidad" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Sostenibilidad") echo "selected"; ?>> Sostenibilidad</option>
                                            <option value="Financiera-commodities" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Financiera-Commodities") echo "selected"; ?>> Financiera-commodities</option>
                                            <option value="Desarrollo agrícola" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Desarrollo agrícola") echo "selected"; ?>> Desarrollo agrícola</option>
                                            <option value="Marcas" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Marcas") echo "selected"; ?>>Marcas</option>
                                            <option value="Servicios de apoyo" <?php if ($frm["NombreDelProcesoOAreaDondeSeEncuentraSuRol"] == "Servicios de apoyo") echo "selected"; ?>>Servicios de apoyo</option>

                                        </select>

                                    </div>

                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="NivelDeEstratoSocieconomico">Nivel de Estrato Socio economico</label>
                                    <div class="select col-sm-8">
                                        <select name="NivelDeEstratoSocieconomico" id="NivelDeEstratoSocieconomico" class="form-control" required>
                                            <option value=""></option>
                                            <option value="1" <?php if ($frm["NivelDeEstratoSocieconomico"] == "1") echo "selected"; ?>>1</option>
                                            <option value="2" <?php if ($frm["NivelDeEstratoSocieconomico"] == "2") echo "selected"; ?>>2</option>
                                            <option value="3" <?php if ($frm["NivelDeEstratoSocieconomico"] == "3") echo "selected"; ?>>3</option>
                                            <option value="4" <?php if ($frm["NivelDeEstratoSocieconomico"] == "4") echo "selected"; ?>>4</option>
                                            <option value="5" <?php if ($frm["NivelDeEstratoSocieconomico"] == "5") echo "selected"; ?>>5</option>
                                            <option value="6" <?php if ($frm["NivelDeEstratoSocieconomico"] == "6") echo "selected"; ?>>6</option>


                                        </select>

                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="NumeroDePersonasQueComponenElNucleoFamiliarPrincipal">Número de Personas que componen el núcleo familiar principal</label>
                                    <div class="col-sm-8"><input type="number" name="NumeroDePersonasQueComponenElNucleoFamiliarPrincipal" id="NumeroDePersonasQueComponenElNucleoFamiliarPrincipal" class="form-control" value="<?php echo $frm["NumeroDePersonasQueComponenElNucleoFamiliarPrincipal"] ?>" required></div>


                                </div>

                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="NumeroDePersonasDeLaFamiliaQueTienenUnTrabajo">Número de personas de la familia  que tienen un trabajo temporal,  fijo o indefinido. Núcleo Familiar Principal</label>
                                    <div class="col-sm-8"><input type="number" name="NumeroDePersonasDeLaFamiliaQueTienenUnTrabajo" id="NumeroDePersonasDeLaFamiliaQueTienenUnTrabajo" class="form-control" value="<?php echo $frm["NumeroDePersonasDeLaFamiliaQueTienenUnTrabajo"] ?>" required></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="RangoIngresosTotalesGrupoFamiliarMensualmente">¿Dentro de qué rango se encuentra los ingresos totales del grupo familiar mensualmente?</label>
                                    <div class="select col-sm-8">
                                        <select name="RangoIngresosTotalesGrupoFamiliarMensualmente" id="RangoIngresosTotalesGrupoFamiliarMensualmente" class="form-control" required>
                                            <option value=""></option>
                                            <option value="1.000.001 - 1.500.000" <?php if ($frm["RangoIngresosTotalesGrupoFamiliarMensualmente"] == "1.000.001 - 1.500.000") echo "selected"; ?>>1.000.001 - 1.500.000</option>
                                            <option value="1.501.000 - 2.500.000" <?php if ($frm["RangoIngresosTotalesGrupoFamiliarMensualmente"] == "1.501.000 - 2.500.000") echo "selected"; ?>>1.501.000 - 2.500.000</option>
                                            <option value="2.500.000 - 3.500.000" <?php if ($frm["RangoIngresosTotalesGrupoFamiliarMensualmente"] == "2.500.000 - 3.500.000") echo "selected"; ?>>2.500.000 - 3.500.000</option>
                                            <option value="Mayor a 3.500.000" <?php if ($frm["RangoIngresosTotalesGrupoFamiliarMensualmente"] == "Mayor a 3.500.000") echo "selected"; ?>>Mayor a 3.500.000</option>

                                        </select>

                                    </div>

                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="TipoDeVivienda">Tipo de Vivienda</label>
                                    <div class="select col-sm-8">
                                        <select name="TipoDeVivienda" id="TipoDeVivienda" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Vivienda propia" <?php if ($frm["TipoDeVivienda"] == "Vivienda propia") echo "selected"; ?>>Vivienda propia</option>
                                            <option value="Arrendada" <?php if ($frm["TipoDeVivienda"] == "Arrendada") echo "selected"; ?>>Arrendada</option>
                                            <option value="Compartida" <?php if ($frm["TipoDeVivienda"] == "Compartida") echo "selected"; ?>>Compartida</option>

                                        </select>

                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="NivelDeEstudio">Nivel De Estudio</label>
                                    <div class="select col-sm-8">
                                        <select name="NivelDeEstudio" id="NivelDeEstudio" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Primaria" <?php if ($frm["NivelDeEstudio"] == "Primaria") echo "selected"; ?>>Primaria</option>
                                            <option value="Secundaria" <?php if ($frm["NivelDeEstudio"] == "Secundaria") echo "selected"; ?>>Secundaria</option>
                                            <option value="Técnica" <?php if ($frm["NivelDeEstudio"] == "Técnica") echo "selected"; ?>>Técnica</option>
                                            <option value="Tecnológica" <?php if ($frm["NivelDeEstudio"] == "Tecnológica") echo "selected"; ?>>Tecnológica</option>
                                            <option value="Pregrado" <?php if ($frm["NivelDeEstudio"] == "Pregrado") echo "selected"; ?>>Pregrado</option>
                                            <option value="Posgrado" <?php if ($frm["NivelDeEstudio"] == "Posgrado") echo "selected"; ?>>Posgrado</option>

                                        </select>

                                    </div>

                                </div>

                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="DepartamentoDeNacimiento">Departamento de Nacimiento</label>
                                    <div class="select col-sm-8">
                                        <select name="DepartamentoDeNacimiento" id="DepartamentoDeNacimiento" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Amazonas" <?php if ($frm["DepartamentoDeNacimiento"] == "Amazonas") echo "selected"; ?>>Amazonas</option>
                                            <option value="Antioquia" <?php if ($frm["DepartamentoDeNacimiento"] == "Antioquia") echo "selected"; ?>>Antioquia</option>
                                            <option value="Arauca" <?php if ($frm["DepartamentoDeNacimiento"] == " Arauca") echo "selected"; ?>> Arauca</option>
                                            <option value="Atlántico" <?php if ($frm["DepartamentoDeNacimiento"] == "Atlántico") echo "selected"; ?>>Atlántico</option>
                                            <option value="Bogotá" <?php if ($frm["DepartamentoDeNacimiento"] == "Bogotá") echo "selected"; ?>>Bogotá</option>
                                            <option value="Bolívar" <?php if ($frm["DepartamentoDeNacimiento"] == "Bolívar") echo "selected"; ?>>Bolívar</option>
                                            <option value="Boyacá" <?php if ($frm["DepartamentoDeNacimiento"] == "Boyacá") echo "selected"; ?>>Boyacá</option>
                                            <option value=" Caldas" <?php if ($frm["DepartamentoDeNacimiento"] == " Caldas") echo "selected"; ?>> Caldas</option>
                                            <option value="Caquetá" <?php if ($frm["DepartamentoDeNacimiento"] == "Caquetá") echo "selected"; ?>>Caquetá</option>
                                            <option value="Casanare" <?php if ($frm["DepartamentoDeNacimiento"] == "Casanare") echo "selected"; ?>>Casanare</option>
                                            <option value="Cauca" <?php if ($frm["DepartamentoDeNacimiento"] == "Cauca") echo "selected"; ?>>Cauca</option>
                                            <option value="Cesar" <?php if ($frm["DepartamentoDeNacimiento"] == "Cesar") echo "selected"; ?>>Cesar</option>
                                            <option value="Chocó" <?php if ($frm["DepartamentoDeNacimiento"] == "Chocó") echo "selected"; ?>>Chocó</option>
                                            <option value=" Córdoba" <?php if ($frm["DepartamentoDeNacimiento"] == " Córdoba") echo "selected"; ?>> Córdoba</option>
                                            <option value="Cundinamarca" <?php if ($frm["DepartamentoDeNacimiento"] == "Cundinamarca") echo "selected"; ?>>Cundinamarca</option>
                                            <option value="Guainía" <?php if ($frm["DepartamentoDeNacimiento"] == "Guainía") echo "selected"; ?>>Guainía</option>
                                            <option value="Guaviare" <?php if ($frm["DepartamentoDeNacimiento"] == "Guaviare") echo "selected"; ?>>Guaviare</option>
                                            <option value="Huila" <?php if ($frm["DepartamentoDeNacimiento"] == "Huila") echo "selected"; ?>>Huila</option>
                                            <option value="La Guajira" <?php if ($frm["DepartamentoDeNacimiento"] == "La Guajira") echo "selected"; ?>>La Guajira</option>
                                            <option value="Magdalena" <?php if ($frm["DepartamentoDeNacimiento"] == "Magdalena") echo "selected"; ?>>Magdalena</option>
                                            <option value="Meta" <?php if ($frm["DepartamentoDeNacimiento"] == "Meta") echo "selected"; ?>>Meta</option>
                                            <option value="Nariño" <?php if ($frm["DepartamentoDeNacimiento"] == "Nariño") echo "selected"; ?>>Nariño</option>
                                            <option value="Norte de santander" <?php if ($frm["DepartamentoDeNacimiento"] == "Norte de santander") echo "selected"; ?>>Norte de santander</option>
                                            <option value="Putumayo" <?php if ($frm["DepartamentoDeNacimiento"] == "Putumayo") echo "selected"; ?>>Putumayo</option>
                                            <option value="Quindío" <?php if ($frm["DepartamentoDeNacimiento"] == "Quindío") echo "selected"; ?>>Quindío</option>
                                            <option value="Risaralda" <?php if ($frm["DepartamentoDeNacimiento"] == "Risaralda") echo "selected"; ?>>Risaralda</option>
                                            <option value="San andrés y providencia" <?php if ($frm["DepartamentoDeNacimiento"] == "San andrés y providencia") echo "selected"; ?>> San andrés y providencia</option>
                                            <option value="Santander" <?php if ($frm["DepartamentoDeNacimiento"] == "Santander") echo "selected"; ?>>Santander</option>
                                            <option value="Sucre" <?php if ($frm["DepartamentoDeNacimiento"] == "Sucre") echo "selected"; ?>>Sucre</option>
                                            <option value="Tolima" <?php if ($frm["DepartamentoDeNacimiento"] == "Tolima") echo "selected"; ?>>Tolima</option>
                                            <option value="Valle del cauca" <?php if ($frm["DepartamentoDeNacimiento"] == "Valle del cauca") echo "selected"; ?>>Valle del cauca</option>
                                            <option value="Vaupés" <?php if ($frm["DepartamentoDeNacimiento"] == "Vaupés") echo "selected"; ?>>Vaupés</option>
                                            <option value="Vichada" <?php if ($frm["DepartamentoDeNacimiento"] == "Vichada") echo "selected"; ?>>Vichada</option>

                                        </select>

                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="MunicipioDeNacimiento">Municipio De Nacimiento</label>
                                    <div class="col-sm-8"><input name="MunicipioDeNacimiento" id="MunicipioDeNacimiento" type="text" class="form-control mandatory" title="MunicipioDeNacimiento" value="<?php echo $frm["MunicipioDeNacimiento"] ?>" required /></div>
                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="RangoDeEdad">Rango de Edad</label>
                                    <div class="select col-sm-8">
                                        <select name="RangoDeEdad" id="RangoDeEdad" class="form-control" required>
                                            <option value=""></option>
                                            <option value="0-18" <?php if ($frm["RangoDeEdad"] == "0-18") echo "selected"; ?>>0-18</option>
                                            <option value="18-24" <?php if ($frm["RangoDeEdad"] == "18-24") echo "selected"; ?>>18-24</option>
                                            <option value="24-30" <?php if ($frm["RangoDeEdad"] == "24-30") echo "selected"; ?>>24-30</option>
                                            <option value="30-36" <?php if ($frm["RangoDeEdad"] == "30-36") echo "selected"; ?>>30-36</option>
                                            <option value="37- 42" <?php if ($frm["RangoDeEdad"] == "37- 42") echo "selected"; ?>>37- 42</option>
                                            <option value="43-49" <?php if ($frm["RangoDeEdad"] == "43-49") echo "selected"; ?>>43-49</option>
                                            <option value=">50" <?php if ($frm["RangoDeEdad"] == ">50") echo "selected"; ?>>>50</option>

                                        </select>

                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="GrupoEtnico">Perteneces o te identificas con algún grupo étnico</label>
                                    <div class="select col-sm-8">
                                        <select name="GrupoEtnico" id="GrupoEtnico" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Negro(a), mulato(a) o afrodescendiente" <?php if ($frm["GrupoEtnico"] == "Negro(a), mulato(a) o afrodescendiente") echo "selected"; ?>>Negro(a), mulato(a) o afrodescendiente</option>
                                            <option value="Indígena" <?php if ($frm["GrupoEtnico"] == "Indígena") echo "selected"; ?>>Indígena</option>
                                            <option value="Raizal del archipiélago de san andrés" <?php if ($frm["GrupoEtnico"] == "Raizal del archipiélago de san andrés") echo "selected"; ?>>Raizal del archipiélago de san andrés</option>
                                            <option value="ROM o gitano" <?php if ($frm["GrupoEtnico"] == "ROM o gitano") echo "selected"; ?>>ROM o gitano</option>
                                            <option value="Otro" <?php if ($frm["GrupoEtnico"] == "Otro") echo "selected"; ?>>Otro</option>
                                            <option value="Ninguno" <?php if ($frm["GrupoEtnico"] == "Ninguno") echo "selected"; ?>>Ninguno</option>

                                        </select>

                                    </div>

                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-6 control-label no-padding-right" for="PertenecesAlgunGrupo">Perteneces o te identificas con algún grupo siguiente:</label>
                                    <div class="col-sm-8">
                                        <br>
                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Mujer cabeza de familia" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                                        for ($i = 0; $i < count($data); $i++) {
                                                                                                                                                            if ($data[$i] == "Mujer cabeza de familia") {
                                                                                                                                                                echo "checked";
                                                                                                                                                            }
                                                                                                                                                        } ?>> &nbsp; Mujer cabeza de familia <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Comunidad LGBTIQ" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                                for ($i = 0; $i < count($data); $i++) {
                                                                                                                                                    if ($data[$i] == "Comunidad LGBTIQ") {
                                                                                                                                                        echo "checked";
                                                                                                                                                    }
                                                                                                                                                } ?>> &nbsp; Comunidad LGBTIQ <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Adulto mayor" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                                                if ($data[$i] == "Adulto mayor") {
                                                                                                                                                    echo "checked";
                                                                                                                                                }
                                                                                                                                            } ?>> &nbsp; Adulto mayor <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Víctima conflicto armado" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                                        for ($i = 0; $i < count($data); $i++) {
                                                                                                                                                            if ($data[$i] == "Víctima conflicto armado") {
                                                                                                                                                                echo "checked";
                                                                                                                                                            }
                                                                                                                                                        } ?>> &nbsp; Víctima conflicto armado <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Persona en situación de discapacidad" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                                                    for ($i = 0; $i < count($data); $i++) {
                                                                                                                                                                        if ($data[$i] == "Persona en situación de discapacidad") {
                                                                                                                                                                            echo "checked";
                                                                                                                                                                        }
                                                                                                                                                                    } ?>> &nbsp; Persona en situación de discapacidad <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Desplazado" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                                                if ($data[$i] == "Desplazado") {
                                                                                                                                                    echo "checked";
                                                                                                                                                }
                                                                                                                                            } ?>> &nbsp;Desplazado <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Migrante" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                        for ($i = 0; $i < count($data); $i++) {
                                                                                                                                            if ($data[$i] == "Migrante") {
                                                                                                                                                echo "checked";
                                                                                                                                            }
                                                                                                                                        } ?>> &nbsp;Migrante <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Desmovilizado" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                                for ($i = 0; $i < count($data); $i++) {
                                                                                                                                                    if ($data[$i] == "Desmovilizado") {
                                                                                                                                                        echo "checked";
                                                                                                                                                    }
                                                                                                                                                } ?>> &nbsp;Desmovilizado <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Veterano de guerra" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                                    for ($i = 0; $i < count($data); $i++) {
                                                                                                                                                        if ($data[$i] == "Veterano de guerra") {
                                                                                                                                                            echo "checked";
                                                                                                                                                        }
                                                                                                                                                    } ?>> &nbsp;Veterano de guerra <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Hombre de cabeza de familia" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                                                                if ($data[$i] == "Hombre de cabeza de familia") {
                                                                                                                                                                    echo "checked";
                                                                                                                                                                }
                                                                                                                                                            } ?>> &nbsp;Hombre de cabeza de familia <br><br>

                                        <input type="checkbox" name="PertenecesAlgunGrupo[]" id="PertenecesAlgunGrupo" value="Ninguno" <?php $data = explode(",", $frm["PertenecesAlgunGrupo"]);
                                                                                                                                        for ($i = 0; $i < count($data); $i++) {
                                                                                                                                            if ($data[$i] == "Ninguno") {
                                                                                                                                                echo "checked";
                                                                                                                                            }
                                                                                                                                        } ?>> &nbsp;Ninguno <br><br>

                                    </div>

                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Discapacidad">Si tienes alguna discapacidad, selecciona la que más se acerca:</label>
                                    <div class="select col-sm-8">
                                        <select name="Discapacidad" id="Discapacidad" class="form-control">
                                            <option value=""></option>
                                            <option value="Discapacidad física" <?php if ($frm["Discapacidad"] == "Discapacidad física") echo "selected"; ?>>Discapacidad física</option>
                                            <option value="Discapacidad intelectual" <?php if ($frm["Discapacidad"] == "Discapacidad intelectual") echo "selected"; ?>>Discapacidad intelectual</option>
                                            <option value="Discapacidad sensorial" <?php if ($frm["Discapacidad"] == "Discapacidad sensorial") echo "selected"; ?>>Discapacidad sensorial</option>
                                            <option value="Discapacidad múltiple" <?php if ($frm["Discapacidad"] == "Discapacidad múltiple") echo "selected"; ?>>Discapacidad múltiple</option>
                                            <option value="Otro" <?php if ($frm["Discapacidad"] == "otro") echo "selected"; ?>>Otro</option>

                                        </select>
                                        <br>
                                        <br>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="CualDiscapacidad">Cual Otra Discapacidad</label>
                                        <div class="col-sm-8"> <input name="CualDiscapacidad" id="CualDiscapacidad" type="text" class="form-control mandatory" title="CualDiscapacidad" value="<?php echo $frm["CualDiscapacidad"] ?>" /></div>
                                    </div>


                                </div>
                            </div>


                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Genero">con qué género te identificas más:</label>
                                    <div class="select col-sm-8">
                                        <select name="Genero" id="Genero" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Hombre" <?php if ($frm["Genero"] == "Hombre") echo "selected"; ?>>Hombre</option>
                                            <option value="Mujer" <?php if ($frm["Genero"] == "Mujer") echo "selected"; ?>>Mujer</option>
                                            <option value="Hombre transgenero" <?php if ($frm["Genero"] == "Hombre transgenero") echo "selected"; ?>>Hombre transgenero</option>
                                            <option value="Mujer transgenero" <?php if ($frm["Genero"] == "Mujer transgenero") echo "selected"; ?>>Mujer transgenero</option>
                                            <option value="Transexual" <?php if ($frm["Genero"] == "Transexual") echo "selected"; ?>>Transexual</option>
                                            <option value="Travesti" <?php if ($frm["Genero"] == "Travesti") echo "selected"; ?>>Travesti</option>
                                            <option value="Queer" <?php if ($frm["Genero"] == "Queer") echo "selected"; ?>>Queer</option>
                                            <option value="Otro" <?php if ($frm["Genero"] == "Otro") echo "selected"; ?>>Otro</option>

                                        </select>
                                        <br><br>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="CualGenero">Cual Genero</label>
                                        <div class="col-sm-8"><input name="CualGenero" id="CualGenero" type="text" class="form-control mandatory" title="CualGenero" value="<?php echo $frm["CualGenero"] ?>" /></div>
                                    </div>


                                </div>


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="OrientacionSexual">Con cuál orientación sexual te identificas más:</label>
                                    <div class="select col-sm-8">
                                        <select name="OrientacionSexual" id="OrientacionSexual" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Heterosexual" <?php if ($frm["OrientacionSexual"] == "Heterosexual") echo "selected"; ?>>Heterosexual</option>
                                            <option value="Bisexual" <?php if ($frm["OrientacionSexual"] == "Bisexual") echo "selected"; ?>>Bisexual</option>
                                            <option value="Homesexual" <?php if ($frm["OrientacionSexual"] == "Homesexual") echo "selected"; ?>>Homesexual</option>

                                        </select>

                                    </div>

                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="MadreOPadreCabezaDeFamilia"> Eres madre o padre cabeza de familia </label>
                                    <div class="select col-sm-8">
                                        <select name="MadreOPadreCabezaDeFamilia" id="MadreOPadreCabezaDeFamilia" class="form-control" required>
                                            <option value=""></option>
                                            <option value="Si" <?php if ($frm["MadreOPadreCabezaDeFamilia"] == "Si") echo "selected"; ?>>Si</option>
                                            <option value="No" <?php if ($frm["MadreOPadreCabezaDeFamilia"] == "No") echo "selected"; ?>>No</option>


                                        </select>

                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="NumeroHijos">Número de  hijos</label>
                                    <div class="select col-sm-8">
                                        <select name="NumeroHijos" id="NumeroHijos" class="form-control" required>
                                            <option value=""></option>
                                            <option value="0" <?php if ($frm["NumeroHijos"] == "0") echo "selected"; ?>>0</option>
                                            <option value="1" <?php if ($frm["NumeroHijos"] == "1") echo "selected"; ?>>1</option>
                                            <option value="2" <?php if ($frm["NumeroHijos"] == "2") echo "selected"; ?>>2</option>
                                            <option value="3" <?php if ($frm["NumeroHijos"] == "3") echo "selected"; ?>>3</option>
                                            <option value="4" <?php if ($frm["NumeroHijos"] == "4") echo "selected"; ?>>4</option>
                                            <option value="5" <?php if ($frm["NumeroHijos"] == "5") echo "selected"; ?>>5</option>
                                            <option value="6" <?php if ($frm["NumeroHijos"] == "6") echo "selected"; ?>>6</option>
                                            <option value="7" <?php if ($frm["NumeroHijos"] == "7") echo "selected"; ?>>7</option>
                                            <option value="8" <?php if ($frm["NumeroHijos"] == "8") echo "selected"; ?>>8</option>
                                            <option value="9" <?php if ($frm["NumeroHijos"] == "9") echo "selected"; ?>>9</option>
                                            <option value="10" <?php if ($frm["NumeroHijos"] == "10") echo "selected"; ?>>10</option>
                                            <option value="11" <?php if ($frm["NumeroHijos"] == "11") echo "selected"; ?>>11</option>
                                            <option value="12" <?php if ($frm["NumeroHijos"] == "12") echo "selected"; ?>>12</option>
                                            <option value="13" <?php if ($frm["NumeroHijos"] == "13") echo "selected"; ?>>13</option>
                                            <option value="14" <?php if ($frm["NumeroHijos"] == "14") echo "selected"; ?>>14</option>
                                            <option value="15" <?php if ($frm["NumeroHijos"] == "15") echo "selected"; ?>>15</option>


                                        </select>

                                    </div>

                                </div>

                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="RangoEdadHijos">Rango de Edad de los hijos</label>
                                    <div class="select col-sm-8">
                                        <select name="RangoEdadHijos" id="RangoEdadHijos" class="form-control" required>
                                            <option value=""></option>
                                            <option value="0-5" <?php if ($frm["RangoEdadHijos"] == "0-5") echo "selected"; ?>>0-5</option>
                                            <option value="5-10" <?php if ($frm["RangoEdadHijos"] == "5-10") echo "selected"; ?>>5-10</option>
                                            <option value="10-15" <?php if ($frm["RangoEdadHijos"] == "0-5") echo "selected"; ?>>10-15</option>
                                            <option value="15-20" <?php if ($frm["RangoEdadHijos"] == "15-20") echo "selected"; ?>>15-20</option>
                                            <option value=">20" <?php if ($frm["RangoEdadHijos"] == ">20") echo "selected"; ?>>>20</option>

                                        </select>

                                    </div>

                                </div>


                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="CuidadoDeLosHijos">Durante las horas laborales  quién se hace cargo del cuidado de los hijos:</label>
                                    <div class="select col-sm-8">
                                        <select name="CuidadoDeLosHijos" id="CuidadoDeLosHijos" class="form-control">
                                            <option value=""></option>
                                            <option value="Familiares" <?php if ($frm["CuidadoDeLosHijos"] == "Familiares") echo "selected"; ?>>Familiares</option>
                                            <option value="Guarderia" <?php if ($frm["CuidadoDeLosHijos"] == "Guarderia") echo "selected"; ?>>Guarderia</option>
                                            <option value="Cuidadoras o cuidadores pagos" <?php if ($frm["CuidadoDeLosHijos"] == "Cuidadoras o cuidadores pagos") echo "selected"; ?>>Cuidadoras o cuidadores pagos</option>
                                            <option value="Comedores comunitarios" <?php if ($frm["CuidadoDeLosHijos"] == "Comedores comunitarios") echo "selected"; ?>>Comedores comunitarios</option>
                                            <option value="Otros" <?php if ($frm["CuidadoDeLosHijos"] == "Otros") echo "selected"; ?>>Otros</option>

                                            <br><br>
                                        </select>

                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="CualCuidadoDeLosHijos">Cuales otros</label>
                                        <div class="col-sm-8"><input name="CualCuidadoDeLosHijos" id="CualCuidadoDeLosHijos" type="text" class="form-control mandatory" title="CualCuidadoDeLosHijos" value="<?php echo $frm["CualCuidadoDeLosHijos"] ?>" /></div>
                                    </div>


                                </div>

                            </div>


                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Emprendimientos">Tienes emprendimientos, negocios propios o del núcleo familiar</label>
                                    <div class="select col-sm-8">
                                        <select name="Emprendimientos" id="Emprendimientos" class="form-control">
                                            <option value=""></option>
                                            <option value="Si" <?php if ($frm["Emprendimientos"] == "Si") echo "selected"; ?>>Si</option>
                                            <option value="No" <?php if ($frm["Emprendimientos"] == "No") echo "selected"; ?>>No</option>


                                        </select>
                                        <br><br>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="CualEmprendimientos">Cual </label>
                                        <div class="col-sm-8"><input name="CualEmprendimientos" id="CualEmprendimientos" type="text" class="form-control mandatory" title="CualEmprendimientos" value="<?php echo $frm["CualEmprendimientos"] ?>" /></div>
                                    </div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="ServiciosDeSalud">Posees servicios de salud adicionales como planes complementarios, medicina prepagada, subsidio por familiares u otros</label>
                                    <div class="select col-sm-8">
                                        <select name="ServiciosDeSalud" id="ServiciosDeSalud" class="form-control">
                                            <option value=""></option>
                                            <option value="Si" <?php if ($frm["ServiciosDeSalud"] == "Si") echo "selected"; ?>>Si</option>
                                            <option value="No" <?php if ($frm["ServiciosDeSalud"] == "No") echo "selected"; ?>>No</option>


                                        </select>

                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="CualServiciosDeSalud">Cual </label>
                                        <div class="col-sm-8"><input name="CualServiciosDeSalud" id="CualServiciosDeSalud" type="text" class="form-control mandatory" title="CualServiciosDeSalud" value="<?php echo $frm["CualServiciosDeSalud"] ?>" /></div>
                                    </div>


                                </div>
                            </div>

                            <div class="form-group first ">
                                <div class="form-group col-md-6">
                                    <label class="col-sm-7 control-label no-padding-right" for="TemasDeInteres">Cuáles de los siguientes temas son de tu interés en temas de voluntariado(Maximo 3)</label>
                                    <div class="col-sm-8">
                                        <br>
                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Enseñanza") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Enseñanza">Enseñanza <br><br>

                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Ciencias y tecnología") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Ciencias y tecnología">Ciencias y tecnología<br><br>

                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Social") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Social">Social<br><br>

                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Socio sanitario") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Socio sanitario">Socio sanitario<br><br>

                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Ambiental") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Ambiental">Ambiental<br><br>

                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Comunitario") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Comunitario">Comunitario<br><br>

                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Culturales y artisticos") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Culturales y artisticos">Culturales y artisticos<br><br>

                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Personas con capacidades diferentes") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Personas con capacidades diferentes">Personas con capacidades diferentes<br><br>

                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Construcción") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Construcción">Construcción<br><br>

                                        <input type="checkbox" name="TemasDeInteres[]" id="TemasDeInteres" <?php $data = explode(",", $frm["TemasDeInteres"]);
                                                                                                            for ($i = 0; $i < count($data); $i++) {
                                                                                                                if ($data[$i] == "Otro") {
                                                                                                                    echo "checked";
                                                                                                                }
                                                                                                            } ?> value="Otro">Otro:<br><br>

                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="col-sm-4 control-label no-padding-right" for="CualTemaDeInteres">Cual Tema De Interes</label>
                                        <div class="col-sm-8"> <input name="CualTemaDeInteres" id="CualTemaDeInteres" type="text" class="form-control mandatory" title="CualTemaDeInteres" value="<?php echo $frm["CualTemaDeInteres"] ?>" /></div>
                                    </div>


                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-7 control-label no-padding-right" for="NumeroHijos">Cuáles de los siguientes son temas de interés y que realizas dentro de tu tiempo libre (Máximo 3)</label>
                                    <div class="col-sm-8">
                                        <br>
                                        <input type="checkbox" name="TemasDeInteresTiempoLibre[]" id="TemasDeInteresTiempoLibre" <?php $data1 = explode(",", $frm["TemasDeInteresTiempoLibre"]);
                                                                                                                                    for ($i = 0; $i < count($data1); $i++) {
                                                                                                                                        if ($data1[$i] == "Deportes") {
                                                                                                                                            echo "checked";
                                                                                                                                        }
                                                                                                                                    } ?> value="Deportes">Deportes<br><br>

                                        <input type="checkbox" name="TemasDeInteresTiempoLibre[]" id="TemasDeInteresTiempoLibre" <?php $data1 = explode(",", $frm["TemasDeInteresTiempoLibre"]);
                                                                                                                                    for ($i = 0; $i < count($data1); $i++) {
                                                                                                                                        if ($data1[$i] == "Cocina") {
                                                                                                                                            echo "checked";
                                                                                                                                        }
                                                                                                                                    } ?> value="Cocina">Cocina<br><br>

                                        <input type="checkbox" name="TemasDeInteresTiempoLibre[]" id="TemasDeInteresTiempoLibre" <?php $data1 = explode(",", $frm["TemasDeInteresTiempoLibre"]);
                                                                                                                                    for ($i = 0; $i < count($data1); $i++) {
                                                                                                                                        if ($data1[$i] == "Lectura") {
                                                                                                                                            echo "checked";
                                                                                                                                        }
                                                                                                                                    } ?> value="Lectura">Lectura<br><br>

                                        <input type="checkbox" name="TemasDeInteresTiempoLibre[]" id="TemasDeInteresTiempoLibre" <?php $data1 = explode(",", $frm["TemasDeInteresTiempoLibre"]);
                                                                                                                                    for ($i = 0; $i < count($data1); $i++) {
                                                                                                                                        if ($data1[$i] == "Pintura") {
                                                                                                                                            echo "checked";
                                                                                                                                        }
                                                                                                                                    } ?> value="Pintura">Pintura<br><br>

                                        <input type="checkbox" name="TemasDeInteresTiempoLibre[]" id="TemasDeInteresTiempoLibre" <?php $data1 = explode(",", $frm["TemasDeInteresTiempoLibre"]);
                                                                                                                                    for ($i = 0; $i < count($data1); $i++) {
                                                                                                                                        if ($data1[$i] == "Teatro") {
                                                                                                                                            echo "checked";
                                                                                                                                        }
                                                                                                                                    } ?> value="Teatro">Teatro<br><br>

                                        <input type="checkbox" name="TemasDeInteresTiempoLibre[]" id="TemasDeInteresTiempoLibre" <?php $data1 = explode(",", $frm["TemasDeInteresTiempoLibre"]);
                                                                                                                                    for ($i = 0; $i < count($data1); $i++) {
                                                                                                                                        if ($data1[$i] == "Ejercicio y alimentación fitness") {
                                                                                                                                            echo "checked";
                                                                                                                                        }
                                                                                                                                    } ?> value="Ejercicio y alimentación fitness">Ejercicio y alimentación fitness<br><br>

                                        <input type="checkbox" name="TemasDeInteresTiempoLibre[]" id="TemasDeInteresTiempoLibre" <?php $data1 = explode(",", $frm["TemasDeInteresTiempoLibre"]);
                                                                                                                                    for ($i = 0; $i < count($data1); $i++) {
                                                                                                                                        if ($data1[$i] == "Otro") {
                                                                                                                                            echo "checked";
                                                                                                                                        }
                                                                                                                                    } ?> value="Otro">Otro:<br><br>

                                    </div>


                                    <div class="form-group col-md-6">
                                        <label class="col-sm-5 control-label no-padding-right" for="CualTemaDeInteresTiempoLibre">Cual Tema De Interes en tiempo Libre</label>
                                        <div class="col-sm-7"> <input name="CualTemaDeInteresTiempoLibre" id="CualTemaDeInteresTiempoLibre" type="text" class="form-control mandatory" title="CualTemaDeInteresTiempoLibre" value="<?php echo $frm["CualTemaDeInteresTiempoLibre"] ?>" /></div>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group first ">

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Nombre">Nombre(Opcional y confidencial)</label>
                                    <div class="col-sm-8"> <input name="Nombre" id="Nombre" type="text" class="form-control mandatory" title="Nombre" value="<?php echo $frm["Nombre"] ?>" /></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-sm-4 control-label no-padding-right" for="Comentario">Algún comentario que nos quieran dejar</label>
                                    <div class="col-sm-8"><input name="Comentario" id="Comentario" type="text" class="form-control mandatory" title="Comentario" value="<?php echo $frm["Comentario"] ?>" /></div>
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