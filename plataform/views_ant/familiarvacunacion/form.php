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


                            <div id="familiar">
                                <h3>Familiar 1</h3>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="NombreVisitante">Tipo de documento</label>

                                <select name="TipoDocumento" id="TipoDocumento" class="form-control" value="<?php echo $frm["TipoDocumento"]; ?>" required>
                                    <option value="">Tipo de documento</option>
                                    <option value="CC" <?php if ($frm["TipoDocumento"] == "CC") echo "selected"; ?>>Cédula de ciudadania</option>
                                    <option value="TI" <?php if ($frm["TipoDocumento"] == "TI") echo "selected"; ?>>Tarjeta de identidad</option>
                                    <option value="CE" <?php if ($frm["TipoDocumento"] == "CE") echo "selected"; ?>>Cédula de extranjeria</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="NumeroDocumento">Numero Documento </label>
                                <input name="NumeroDocumento" id="NumeroDocumento" type="text" placeholder="NumeroDocumento" class="form-control " title="NumeroDocumento" value="<?php echo $frm["NumeroDocumento"]; ?>" required />
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Nombres">Nombres</label>
                                <input name="Nombres" id="Nombres" type="text" placeholder="Nombres" class="form-control " title="Nombres" value="<?php echo $frm["Nombres"]; ?>" required />
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Apellidos">Apellidos</label>
                                <input name="Apellidos" id="Apellidos" type="text" placeholder="Apellidos" class="form-control " title="Apellidos" value="<?php echo $frm["Apellidos"]; ?>" required />
                            </div>


                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="FechaDeNacimiento">Fecha De Nacimiento</label>
                                <input name="FechaDeNacimiento" id="FechaDeNacimiento" type="date" placeholder="FechaDeNacimiento" class="form-control " title="FechaDeNacimiento" value="<?php echo $frm["FechaDeNacimiento"]; ?>" max="<?php echo (date('Y-m-d')); ?>" required />
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Eps">Eps</label>
                                <input name="Eps" id="Eps" type="text" placeholder="Eps" class="form-control " title="Eps" value="<?php echo $frm["Eps"]; ?>" required />
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Parentesco">Parentesco</label>
                                <select name="Parentesco" id="Parentesco" class="form-control" required>
                                    <option value="">Parentesco</option>
                                    <option value="Conyuge" <?php if ($frm["Parentesco"] == "Conyuge") echo "selected"; ?>>Conyuge</option>
                                    <option value="Hijo(a)" <?php if ($frm["Parentesco"] == "Hijo(a)") echo "selected"; ?>>Hijo(a)</option>
                                </select>
                            </div>



                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="CiudadDondeReside">Ciudad Donde Reside</label>
                                <select name="CiudadDondeReside" id="CiudadDondeReside" class="form-control" required>
                                    <option value="">Ciudad donde reside</option>
                                    <option value="Arauca" <?php if ($frm["CiudadDondeReside"] == "Arauca") echo "selected"; ?>>Arauca</option>
                                    <option value="Armenia" <?php if ($frm["CiudadDondeReside"] == "Armenia") echo "selected"; ?>>Armenia</option>
                                    <option value="Barranquilla" <?php if ($frm["CiudadDondeReside"] == "Barranquilla") echo "selected"; ?>>Barranquilla</option>
                                    <option value="Bogotá D.C." <?php if ($frm["CiudadDondeReside"] == "Bogotá D.C.") echo "selected"; ?>>Bogotá D.C.</option>
                                    <option value="Bucaramanga" <?php if ($frm["CiudadDondeReside"] == "Bucaramanga") echo "selected"; ?>>Bucaramanga</option>
                                    <option value="Bugalagrande" <?php if ($frm["CiudadDondeReside"] == "Bugalagrande") echo "selected"; ?>>Bugalagrande</option>
                                    <option value="Cali" <?php if ($frm["CiudadDondeReside"] == "Cali") echo "selected"; ?>>Cali</option>
                                    <option value="Cartagena" <?php if ($frm["CiudadDondeReside"] == "Cartagena") echo "selected"; ?>>Cartagena</option>
                                    <option value="carmen" <?php if ($frm["CiudadDondeReside"] == "carmen") echo "selected"; ?>>carmen</option>
                                    <option value="Cucuta" <?php if ($frm["CiudadDondeReside"] == "Cucuta") echo "selected"; ?>>Cucuta</option>
                                    <option value="Duitama" <?php if ($frm["CiudadDondeReside"] == "Duitama") echo "selected"; ?>>Duitama</option>
                                    <option value="Florencia" <?php if ($frm["CiudadDondeReside"] == "Florencia") echo "selected"; ?>>Florencia</option>
                                    <option value="Ibagué" <?php if ($frm["CiudadDondeReside"] == "Ibagué") echo "selected"; ?>>Ibagué</option>
                                    <option value="Magangue" <?php if ($frm["CiudadDondeReside"] == "Magangue") echo "selected"; ?>>Magangue</option>
                                    <option value="Monpox" <?php if ($frm["CiudadDondeReside"] == "Monpox") echo "selected"; ?>>Monpox</option>
                                    <option value="Manizales" <?php if ($frm["CiudadDondeReside"] == "Manizales") echo "selected"; ?>>Manizales</option>
                                    <option value="Medellin" <?php if ($frm["CiudadDondeReside"] == "Medellin") echo "selected"; ?>>Medellin</option>
                                    <option value="Monteria" <?php if ($frm["CiudadDondeReside"] == "Monteria") echo "selected"; ?>>Monteria</option>
                                    <option value="Necocli" <?php if ($frm["CiudadDondeReside"] == "Necocli") echo "selected"; ?>>Necocli</option>
                                    <option value="Neiva" <?php if ($frm["CiudadDondeReside"] == "Neiva") echo "selected"; ?>>Neiva</option>
                                    <option value="Pasto" <?php if ($frm["CiudadDondeReside"] == "Pasto") echo "selected"; ?>>Pasto</option>
                                    <option value="Pereira" <?php if ($frm["CiudadDondeReside"] == "Pereira") echo "selected"; ?>>Pereira</option>
                                    <option value="Popayan" <?php if ($frm["CiudadDondeReside"] == "Popayan") echo "selected"; ?>>Popayan</option>
                                    <option value="Sincelejo" <?php if ($frm["CiudadDondeReside"] == "Sincelejo") echo "selected"; ?>>Sincelejo</option>
                                    <option value="Tunja" <?php if ($frm["CiudadDondeReside"] == "Tunja") echo "selected"; ?>>Tunja</option>
                                    <option value="Tumaco" <?php if ($frm["CiudadDondeReside"] == "Tumaco") echo "selected"; ?>>Tumaco</option>
                                    <option value="Valledupar" <?php if ($frm["CiudadDondeReside"] == "Valledupar") echo "selected"; ?>>Valledupar</option>
                                    <option value="Villavicenio" <?php if ($frm["CiudadDondeReside"] == "Villavicenio") echo "selected"; ?>>Villavicenio</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="CorreoElectronico">Correo Electronico</label>
                                <input name="CorreoElectronico" id="CorreoElectronico" type="email" placeholder="CorreoElectronico" class="form-control " title="CorreoElectronico" value="<?php echo $frm["CorreoElectronico"]; ?>" required />
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-sm-4 control-label no-padding-right" for="Celular">Celular</label>
                                <input name="Celular" id="Celular" type="text" placeholder="Celular" class="form-control " title="Celular" value="<?php echo $frm["Celular"]; ?>" required />
                            </div>

                            <h3>Familiar 2</h3>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="NombreVisitante">Tipo de documento</label>

                            <select name="TipoDocumento2" id="TipoDocumento2" class="form-control" value="<?php echo $frm["TipoDocumento2"]; ?>" required>
                                <option value="">Tipo de documento</option>
                                <option value="CC" <?php if ($frm["TipoDocumento2"] == "CC") echo "selected"; ?>>Cédula de ciudadania</option>
                                <option value="TI" <?php if ($frm["TipoDocumento2"] == "TI") echo "selected"; ?>>Tarjeta de identidad</option>
                                <option value="CE" <?php if ($frm["TipoDocumento2"] == "CE") echo "selected"; ?>>Cédula de extranjeria</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="NumeroDocumento">Numero Documento </label>
                            <input name="NumeroDocumento2" id="NumeroDocumento2" type="text" placeholder="NumeroDocumento" class="form-control " title="NumeroDocumento2" value="<?php echo $frm["NumeroDocumento2"]; ?>" required />
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="Nombres">Nombres</label>
                            <input name="Nombres2" id="Nombres2" type="text" placeholder="Nombres2" class="form-control " title="Nombres2" value="<?php echo $frm["Nombres2"]; ?>" required />
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="Apellidos2">Apellidos</label>
                            <input name="Apellidos2" id="Apellidos2" type="text" placeholder="Apellidos2" class="form-control " title="Apellidos2" value="<?php echo $frm["Apellidos2"]; ?>" required />
                        </div>


                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="FechaDeNacimiento2">Fecha De Nacimiento</label>
                            <input name="FechaDeNacimiento2" id="FechaDeNacimiento2" type="date" placeholder="FechaDeNacimiento2" class="form-control " title="FechaDeNacimiento2" value="<?php echo $frm["FechaDeNacimiento2"]; ?>" max="<?php echo (date('Y-m-d')); ?>" required />
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="Eps2">Eps</label>
                            <input name="Eps2" id="Eps2" type="text" placeholder="Eps2" class="form-control " title="Eps2" value="<?php echo $frm["Eps2"]; ?>" required />
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="Parentesco2">Parentesco</label>
                            <select name="Parentesco2" id="Parentesco2" class="form-control" required>
                                <option value="">Parentesco</option>
                                <option value="Conyuge" <?php if ($frm["Parentesco2"] == "Conyuge") echo "selected"; ?>>Conyuge</option>
                                <option value="Hijo(a)" <?php if ($frm["Parentesco2"] == "Hijo(a)") echo "selected"; ?>>Hijo(a)</option>
                            </select>
                        </div>



                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="CiudadDondeReside">Ciudad Donde Reside</label>
                            <select name="CiudadDondeReside2" id="CiudadDondeReside2" class="form-control" required>
                                <option value="">Ciudad donde reside</option>
                                <option value="Arauca" <?php if ($frm["CiudadDondeReside2"] == "Arauca") echo "selected"; ?>>Arauca</option>
                                <option value="Armenia" <?php if ($frm["CiudadDondeReside2"] == "Armenia") echo "selected"; ?>>Armenia</option>
                                <option value="Barranquilla" <?php if ($frm["CiudadDondeReside2"] == "Barranquilla") echo "selected"; ?>>Barranquilla</option>
                                <option value="Bogotá D.C." <?php if ($frm["CiudadDondeReside2"] == "Bogotá D.C.") echo "selected"; ?>>Bogotá D.C.</option>
                                <option value="Bucaramanga" <?php if ($frm["CiudadDondeReside2"] == "Bucaramanga") echo "selected"; ?>>Bucaramanga</option>
                                <option value="Bugalagrande" <?php if ($frm["CiudadDondeReside2"] == "Bugalagrande") echo "selected"; ?>>Bugalagrande</option>
                                <option value="Cali" <?php if ($frm["CiudadDondeReside2"] == "Cali") echo "selected"; ?>>Cali</option>
                                <option value="Cartagena" <?php if ($frm["CiudadDondeReside2"] == "Cartagena") echo "selected"; ?>>Cartagena</option>
                                <option value="carmen" <?php if ($frm["CiudadDondeReside2"] == "carmen") echo "selected"; ?>>carmen</option>
                                <option value="Cucuta" <?php if ($frm["CiudadDondeReside2"] == "Cucuta") echo "selected"; ?>>Cucuta</option>
                                <option value="Duitama" <?php if ($frm["CiudadDondeReside2"] == "Duitama") echo "selected"; ?>>Duitama</option>
                                <option value="Florencia" <?php if ($frm["CiudadDondeReside2"] == "Florencia") echo "selected"; ?>>Florencia</option>
                                <option value="Ibagué" <?php if ($frm["CiudadDondeReside2"] == "Ibagué") echo "selected"; ?>>Ibagué</option>
                                <option value="Magangue" <?php if ($frm["CiudadDondeReside2"] == "Magangue") echo "selected"; ?>>Magangue</option>
                                <option value="Monpox" <?php if ($frm["CiudadDondeReside2"] == "Monpox") echo "selected"; ?>>Monpox</option>
                                <option value="Manizales" <?php if ($frm["CiudadDondeReside2"] == "Manizales") echo "selected"; ?>>Manizales</option>
                                <option value="Medellin" <?php if ($frm["CiudadDondeReside2"] == "Medellin") echo "selected"; ?>>Medellin</option>
                                <option value="Monteria" <?php if ($frm["CiudadDondeReside2"] == "Monteria") echo "selected"; ?>>Monteria</option>
                                <option value="Necocli" <?php if ($frm["CiudadDondeReside2"] == "Necocli") echo "selected"; ?>>Necocli</option>
                                <option value="Neiva" <?php if ($frm["CiudadDondeReside2"] == "Neiva") echo "selected"; ?>>Neiva</option>
                                <option value="Pasto" <?php if ($frm["CiudadDondeReside2"] == "Pasto") echo "selected"; ?>>Pasto</option>
                                <option value="Pereira" <?php if ($frm["CiudadDondeReside2"] == "Pereira") echo "selected"; ?>>Pereira</option>
                                <option value="Popayan" <?php if ($frm["CiudadDondeReside2"] == "Popayan") echo "selected"; ?>>Popayan</option>
                                <option value="Sincelejo" <?php if ($frm["CiudadDondeReside2"] == "Sincelejo") echo "selected"; ?>>Sincelejo</option>
                                <option value="Tunja" <?php if ($frm["CiudadDondeReside2"] == "Tunja") echo "selected"; ?>>Tunja</option>
                                <option value="Tumaco" <?php if ($frm["CiudadDondeReside2"] == "Tumaco") echo "selected"; ?>>Tumaco</option>
                                <option value="Valledupar" <?php if ($frm["CiudadDondeReside2"] == "Valledupar") echo "selected"; ?>>Valledupar</option>
                                <option value="Villavicenio" <?php if ($frm["CiudadDondeReside2"] == "Villavicenio") echo "selected"; ?>>Villavicenio</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="CorreoElectronico">Correo Electronico</label>
                            <input name="CorreoElectronico2" id="CorreoElectronico2" type="email" placeholder="CorreoElectronico2" class=" form-control " title=" CorreoElectronico2" value="<?php echo $frm["CorreoElectronico2"]; ?>" required />
                        </div>

                        <div class="form-group col-md-6">
                            <label class="col-sm-4 control-label no-padding-right" for="Celular2">Celular</label>
                            <input name="Celular2" id="Celular2" type="text" placeholder="Celular2" class="form-control " title="Celular2" value="<?php echo $frm["Celular2"]; ?>" required />
                        </div>
                        <h3>Familiar 3</h3>
                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="NombreVisitante">Tipo de documento</label>

                    <select name="TipoDocumento3" id="TipoDocumento3" class="form-control" value="<?php echo $frm["TipoDocumento3"]; ?>" required>
                        <option value="">Tipo de documento</option>
                        <option value="CC" <?php if ($frm["TipoDocumento3"] == "CC") echo "selected"; ?>>Cédula de ciudadania</option>
                        <option value="TI" <?php if ($frm["TipoDocumento3"] == "TI") echo "selected"; ?>>Tarjeta de identidad</option>
                        <option value="CE" <?php if ($frm["TipoDocumento3"] == "CE") echo "selected"; ?>>Cédula de extranjeria</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="NumeroDocumento">Numero Documento </label>
                    <input name="NumeroDocumento3" id="NumeroDocumento3" type="text" placeholder="NumeroDocumento3" class="form-control " title="NumeroDocumento3" value="<?php echo $frm["NumeroDocumento3"]; ?>" required />
                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="Nombres3">Nombres</label>
                    <input name="Nombres3" id="Nombres3" type="text" placeholder="Nombres3" class="form-control " title="Nombres3" value="<?php echo $frm["Nombres3"]; ?>" required />
                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="Apellidos3">Apellidos</label>
                    <input name="Apellidos3" id="Apellidos3" type="text" placeholder="Apellidos3" class="form-control " title="Apellidos3" value="<?php echo $frm["Apellidos3"]; ?>" required />
                </div>


                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="FechaDeNacimiento2">Fecha De Nacimiento</label>
                    <input name="FechaDeNacimiento3" id="FechaDeNacimiento3" type="date" placeholder="FechaDeNacimiento3" class="form-control " title="FechaDeNacimiento3" value="<?php echo $frm["FechaDeNacimiento3"]; ?>" max="<?php echo (date('Y-m-d')); ?>" required />
                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="Eps3">Eps</label>
                    <input name="Eps3" id="Eps3" type="text" placeholder="Eps3" class="form-control " title="Eps3" value="<?php echo $frm["Eps3"]; ?>" required />
                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="Parentesco3">Parentesco</label>
                    <select name="Parentesco3" id="Parentesco3" class="form-control" required>
                        <option value="">Parentesco</option>
                        <option value="Conyuge" <?php if ($frm["Parentesco3"] == "Conyuge") echo "selected"; ?>>Conyuge</option>
                        <option value="Hijo(a)" <?php if ($frm["Parentesco3"] == "Hijo(a)") echo "selected"; ?>>Hijo(a)</option>
                    </select>
                </div>



                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="CiudadDondeReside3">Ciudad Donde Reside</label>
                    <select name="CiudadDondeReside3" id="CiudadDondeReside3" class="form-control" required>
                        <option value="">Ciudad donde reside</option>
                        <option value="Arauca" <?php if ($frm["CiudadDondeReside3"] == "Arauca") echo "selected"; ?>>Arauca</option>
                        <option value="Armenia" <?php if ($frm["CiudadDondeReside3"] == "Armenia") echo "selected"; ?>>Armenia</option>
                        <option value="Barranquilla" <?php if ($frm["CiudadDondeReside3"] == "Barranquilla") echo "selected"; ?>>Barranquilla</option>
                        <option value="Bogotá D.C." <?php if ($frm["CiudadDondeReside3"] == "Bogotá D.C.") echo "selected"; ?>>Bogotá D.C.</option>
                        <option value="Bucaramanga" <?php if ($frm["CiudadDondeReside3"] == "Bucaramanga") echo "selected"; ?>>Bucaramanga</option>
                        <option value="Bugalagrande" <?php if ($frm["CiudadDondeReside3"] == "Bugalagrande") echo "selected"; ?>>Bugalagrande</option>
                        <option value="Cali" <?php if ($frm["CiudadDondeReside3"] == "Cali") echo "selected"; ?>>Cali</option>
                        <option value="Cartagena" <?php if ($frm["CiudadDondeReside3"] == "Cartagena") echo "selected"; ?>>Cartagena</option>
                        <option value="carmen" <?php if ($frm["CiudadDondeReside3"] == "carmen") echo "selected"; ?>>carmen</option>
                        <option value="Cucuta" <?php if ($frm["CiudadDondeReside3"] == "Cucuta") echo "selected"; ?>>Cucuta</option>
                        <option value="Duitama" <?php if ($frm["CiudadDondeReside3"] == "Duitama") echo "selected"; ?>>Duitama</option>
                        <option value="Florencia" <?php if ($frm["CiudadDondeReside3"] == "Florencia") echo "selected"; ?>>Florencia</option>
                        <option value="Ibagué" <?php if ($frm["CiudadDondeReside3"] == "Ibagué") echo "selected"; ?>>Ibagué</option>
                        <option value="Magangue" <?php if ($frm["CiudadDondeReside3"] == "Magangue") echo "selected"; ?>>Magangue</option>
                        <option value="Monpox" <?php if ($frm["CiudadDondeReside3"] == "Monpox") echo "selected"; ?>>Monpox</option>
                        <option value="Manizales" <?php if ($frm["CiudadDondeReside3"] == "Manizales") echo "selected"; ?>>Manizales</option>
                        <option value="Medellin" <?php if ($frm["CiudadDondeReside3"] == "Medellin") echo "selected"; ?>>Medellin</option>
                        <option value="Monteria" <?php if ($frm["CiudadDondeReside3"] == "Monteria") echo "selected"; ?>>Monteria</option>
                        <option value="Necocli" <?php if ($frm["CiudadDondeReside3"] == "Necocli") echo "selected"; ?>>Necocli</option>
                        <option value="Neiva" <?php if ($frm["CiudadDondeReside3"] == "Neiva") echo "selected"; ?>>Neiva</option>
                        <option value="Pasto" <?php if ($frm["CiudadDondeReside3"] == "Pasto") echo "selected"; ?>>Pasto</option>
                        <option value="Pereira" <?php if ($frm["CiudadDondeReside3"] == "Pereira") echo "selected"; ?>>Pereira</option>
                        <option value="Popayan" <?php if ($frm["CiudadDondeReside3"] == "Popayan") echo "selected"; ?>>Popayan</option>
                        <option value="Sincelejo" <?php if ($frm["CiudadDondeReside3"] == "Sincelejo") echo "selected"; ?>>Sincelejo</option>
                        <option value="Tunja" <?php if ($frm["CiudadDondeReside3"] == "Tunja") echo "selected"; ?>>Tunja</option>
                        <option value="Tumaco" <?php if ($frm["CiudadDondeReside3"] == "Tumaco") echo "selected"; ?>>Tumaco</option>
                        <option value="Valledupar" <?php if ($frm["CiudadDondeReside3"] == "Valledupar") echo "selected"; ?>>Valledupar</option>
                        <option value="Villavicenio" <?php if ($frm["CiudadDondeReside3"] == "Villavicenio") echo "selected"; ?>>Villavicenio</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="CorreoElectronico3">Correo Electronico</label>
                    <input name="CorreoElectronico3" id="CorreoElectronico3" type="email" placeholder="CorreoElectronico3" class=" form-control " title=" CorreoElectronico3" value="<?php echo $frm["CorreoElectronico3"]; ?>" required />
                </div>

                <div class="form-group col-md-6">
                    <label class="col-sm-4 control-label no-padding-right" for="Celular3">Celular</label>
                    <input name="Celular3" id="Celular3" type="text" placeholder="Celular3" class="form-control " title="Celular3" value="<?php echo $frm["Celular3"]; ?>" required />
                </div>
                <h3>Familiar 4</h3>
            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="NombreVisitante4">Tipo de documento</label>

                <select name="TipoDocumento4" id="TipoDocumento4" class="form-control" value="<?php echo $frm["TipoDocumento4"]; ?>" required>
                    <option value="">Tipo de documento</option>
                    <option value="CC" <?php if ($frm["TipoDocumento4"] == "CC") echo "selected"; ?>>Cédula de ciudadania</option>
                    <option value="TI" <?php if ($frm["TipoDocumento4"] == "TI") echo "selected"; ?>>Tarjeta de identidad</option>
                    <option value="CE" <?php if ($frm["TipoDocumento4"] == "CE") echo "selected"; ?>>Cédula de extranjeria</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="NumeroDocumento4">Numero Documento </label>
                <input name="NumeroDocumento4" id="NumeroDocumento4" type="text" placeholder="NumeroDocumento4" class="form-control " title="NumeroDocumento4" value="<?php echo $frm["NumeroDocumento4"]; ?>" required />
            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="Nombres4">Nombres</label>
                <input name="Nombres4" id="Nombres4" type="text" placeholder="Nombres4" class="form-control " title="Nombres4" value="<?php echo $frm["Nombres4"]; ?>" required />
            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="Apellidos4">Apellidos</label>
                <input name="Apellidos4" id="Apellidos4" type="text" placeholder="Apellidos4" class="form-control " title="Apellidos4" value="<?php echo $frm["Apellidos4"]; ?>" required />
            </div>


            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="FechaDeNacimiento4">Fecha De Nacimiento</label>
                <input name="FechaDeNacimiento4" id="FechaDeNacimiento4" type="date" placeholder="FechaDeNacimiento4" class="form-control " title="FechaDeNacimiento4" value="<?php echo $frm["FechaDeNacimiento4"]; ?>" max="<?php echo (date('Y-m-d')); ?>" required />
            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="Eps4">Eps</label>
                <input name="Eps4" id="Eps4" type="text" placeholder="Eps4" class="form-control " title="Eps4" value="<?php echo $frm["Eps4"]; ?>" required />
            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="Parentesco4">Parentesco</label>
                <select name="Parentesco4" id="Parentesco4" class="form-control" required>
                    <option value="">Parentesco</option>
                    <option value="Conyuge" <?php if ($frm["Parentesco4"] == "Conyuge") echo "selected"; ?>>Conyuge</option>
                    <option value="Hijo(a)" <?php if ($frm["Parentesco4"] == "Hijo(a)") echo "selected"; ?>>Hijo(a)</option>
                </select>
            </div>



            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="CiudadDondeReside4">Ciudad Donde Reside</label>
                <select name="CiudadDondeReside4" id="CiudadDondeReside4" class="form-control" required>
                    <option value="">Ciudad donde reside</option>
                    <option value="Arauca" <?php if ($frm["CiudadDondeReside4"] == "Arauca") echo "selected"; ?>>Arauca</option>
                    <option value="Armenia" <?php if ($frm["CiudadDondeReside4"] == "Armenia") echo "selected"; ?>>Armenia</option>
                    <option value="Barranquilla" <?php if ($frm["CiudadDondeReside4"] == "Barranquilla") echo "selected"; ?>>Barranquilla</option>
                    <option value="Bogotá D.C." <?php if ($frm["CiudadDondeReside4"] == "Bogotá D.C.") echo "selected"; ?>>Bogotá D.C.</option>
                    <option value="Bucaramanga" <?php if ($frm["CiudadDondeReside4"] == "Bucaramanga") echo "selected"; ?>>Bucaramanga</option>
                    <option value="Bugalagrande" <?php if ($frm["CiudadDondeReside4"] == "Bugalagrande") echo "selected"; ?>>Bugalagrande</option>
                    <option value="Cali" <?php if ($frm["CiudadDondeReside4"] == "Cali") echo "selected"; ?>>Cali</option>
                    <option value="Cartagena" <?php if ($frm["CiudadDondeReside4"] == "Cartagena") echo "selected"; ?>>Cartagena</option>
                    <option value="carmen" <?php if ($frm["CiudadDondeReside4"] == "carmen") echo "selected"; ?>>carmen</option>
                    <option value="Cucuta" <?php if ($frm["CiudadDondeReside4"] == "Cucuta") echo "selected"; ?>>Cucuta</option>
                    <option value="Duitama" <?php if ($frm["CiudadDondeReside4"] == "Duitama") echo "selected"; ?>>Duitama</option>
                    <option value="Florencia" <?php if ($frm["CiudadDondeReside4"] == "Florencia") echo "selected"; ?>>Florencia</option>
                    <option value="Ibagué" <?php if ($frm["CiudadDondeReside4"] == "Ibagué") echo "selected"; ?>>Ibagué</option>
                    <option value="Magangue" <?php if ($frm["CiudadDondeReside4"] == "Magangue") echo "selected"; ?>>Magangue</option>
                    <option value="Monpox" <?php if ($frm["CiudadDondeReside4"] == "Monpox") echo "selected"; ?>>Monpox</option>
                    <option value="Manizales" <?php if ($frm["CiudadDondeReside4"] == "Manizales") echo "selected"; ?>>Manizales</option>
                    <option value="Medellin" <?php if ($frm["CiudadDondeReside4"] == "Medellin") echo "selected"; ?>>Medellin</option>
                    <option value="Monteria" <?php if ($frm["CiudadDondeReside4"] == "Monteria") echo "selected"; ?>>Monteria</option>
                    <option value="Necocli" <?php if ($frm["CiudadDondeReside4"] == "Necocli") echo "selected"; ?>>Necocli</option>
                    <option value="Neiva" <?php if ($frm["CiudadDondeReside4"] == "Neiva") echo "selected"; ?>>Neiva</option>
                    <option value="Pasto" <?php if ($frm["CiudadDondeReside4"] == "Pasto") echo "selected"; ?>>Pasto</option>
                    <option value="Pereira" <?php if ($frm["CiudadDondeReside4"] == "Pereira") echo "selected"; ?>>Pereira</option>
                    <option value="Popayan" <?php if ($frm["CiudadDondeReside4"] == "Popayan") echo "selected"; ?>>Popayan</option>
                    <option value="Sincelejo" <?php if ($frm["CiudadDondeReside4"] == "Sincelejo") echo "selected"; ?>>Sincelejo</option>
                    <option value="Tunja" <?php if ($frm["CiudadDondeReside4"] == "Tunja") echo "selected"; ?>>Tunja</option>
                    <option value="Tumaco" <?php if ($frm["CiudadDondeReside4"] == "Tumaco") echo "selected"; ?>>Tumaco</option>
                    <option value="Valledupar" <?php if ($frm["CiudadDondeReside4"] == "Valledupar") echo "selected"; ?>>Valledupar</option>
                    <option value="Villavicenio" <?php if ($frm["CiudadDondeReside4"] == "Villavicenio") echo "selected"; ?>>Villavicenio</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="CorreoElectronico4">Correo Electronico</label>
                <input name="CorreoElectronico4" id="CorreoElectronico4" type="email" placeholder="CorreoElectronico4" class=" form-control " title=" CorreoElectronico4" value="<?php echo $frm["CorreoElectronico4"]; ?>" required />
            </div>

            <div class="form-group col-md-6">
                <label class="col-sm-4 control-label no-padding-right" for="Celular4">Celular</label>
                <input name="Celular4" id="Celular4" type="text" placeholder="Celular4" class="form-control " title="Celular4" value="<?php echo $frm["Celular4"]; ?>" required />
            </div>
            <h3>Familiar 5</h3>
        </div>

        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="NombreVisitante5">Tipo de documento</label>

            <select name="TipoDocumento5" id="TipoDocumento5" class="form-control" value="<?php echo $frm["TipoDocumento5"]; ?>" required>
                <option value="">Tipo de documento</option>
                <option value="CC" <?php if ($frm["TipoDocumento5"] == "CC") echo "selected"; ?>>Cédula de ciudadania</option>
                <option value="TI" <?php if ($frm["TipoDocumento5"] == "TI") echo "selected"; ?>>Tarjeta de identidad</option>
                <option value="CE" <?php if ($frm["TipoDocumento5"] == "CE") echo "selected"; ?>>Cédula de extranjeria</option>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="NumeroDocumento5">Numero Documento </label>
            <input name="NumeroDocumento5" id="NumeroDocumento5" type="text" placeholder="NumeroDocumento5" class="form-control " title="NumeroDocumento5" value="<?php echo $frm["NumeroDocumento5"]; ?>" required />
        </div>

        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="Nombres5">Nombres</label>
            <input name="Nombres5" id="Nombres5" type="text" placeholder="Nombres5" class="form-control " title="Nombres5" value="<?php echo $frm["Nombres5"]; ?>" required />
        </div>

        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="Apellidos5">Apellidos</label>
            <input name="Apellidos5" id="Apellidos5" type="text" placeholder="Apellidos5" class="form-control " title="Apellidos5" value="<?php echo $frm["Apellidos5"]; ?>" required />
        </div>


        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="FechaDeNacimiento5">Fecha De Nacimiento</label>
            <input name="FechaDeNacimiento5" id="FechaDeNacimiento5" type="date" placeholder="FechaDeNacimiento5" class="form-control " title="FechaDeNacimiento5" value="<?php echo $frm["FechaDeNacimiento5"]; ?>" max="<?php echo (date('Y-m-d')); ?>" required />
        </div>

        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="Eps5">Eps</label>
            <input name="Eps5" id="Eps5" type="text" placeholder="Eps5" class="form-control " title="Eps5" value="<?php echo $frm["Eps5"]; ?>" required />
        </div>

        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="Parentesco5">Parentesco</label>
            <select name="Parentesco5" id="Parentesco5" class="form-control" required>
                <option value="">Parentesco</option>
                <option value="Conyuge" <?php if ($frm["Parentesco5"] == "Conyuge") echo "selected"; ?>>Conyuge</option>
                <option value="Hijo(a)" <?php if ($frm["Parentesco5"] == "Hijo(a)") echo "selected"; ?>>Hijo(a)</option>
            </select>
        </div>



        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="CiudadDondeReside5">Ciudad Donde Reside</label>
            <select name="CiudadDondeReside5" id="CiudadDondeReside5" class="form-control" required>
                <option value="">Ciudad donde reside</option>
                <option value="Arauca" <?php if ($frm["CiudadDondeReside5"] == "Arauca") echo "selected"; ?>>Arauca</option>
                <option value="Armenia" <?php if ($frm["CiudadDondeReside5"] == "Armenia") echo "selected"; ?>>Armenia</option>
                <option value="Barranquilla" <?php if ($frm["CiudadDondeReside5"] == "Barranquilla") echo "selected"; ?>>Barranquilla</option>
                <option value="Bogotá D.C." <?php if ($frm["CiudadDondeReside5"] == "Bogotá D.C.") echo "selected"; ?>>Bogotá D.C.</option>
                <option value="Bucaramanga" <?php if ($frm["CiudadDondeReside5"] == "Bucaramanga") echo "selected"; ?>>Bucaramanga</option>
                <option value="Bugalagrande" <?php if ($frm["CiudadDondeReside5"] == "Bugalagrande") echo "selected"; ?>>Bugalagrande</option>
                <option value="Cali" <?php if ($frm["CiudadDondeReside5"] == "Cali") echo "selected"; ?>>Cali</option>
                <option value="Cartagena" <?php if ($frm["CiudadDondeReside5"] == "Cartagena") echo "selected"; ?>>Cartagena</option>
                <option value="carmen" <?php if ($frm["CiudadDondeReside5"] == "carmen") echo "selected"; ?>>carmen</option>
                <option value="Cucuta" <?php if ($frm["CiudadDondeReside5"] == "Cucuta") echo "selected"; ?>>Cucuta</option>
                <option value="Duitama" <?php if ($frm["CiudadDondeReside5"] == "Duitama") echo "selected"; ?>>Duitama</option>
                <option value="Florencia" <?php if ($frm["CiudadDondeReside5"] == "Florencia") echo "selected"; ?>>Florencia</option>
                <option value="Ibagué" <?php if ($frm["CiudadDondeReside5"] == "Ibagué") echo "selected"; ?>>Ibagué</option>
                <option value="Magangue" <?php if ($frm["CiudadDondeReside5"] == "Magangue") echo "selected"; ?>>Magangue</option>
                <option value="Monpox" <?php if ($frm["CiudadDondeReside5"] == "Monpox") echo "selected"; ?>>Monpox</option>
                <option value="Manizales" <?php if ($frm["CiudadDondeReside5"] == "Manizales") echo "selected"; ?>>Manizales</option>
                <option value="Medellin" <?php if ($frm["CiudadDondeReside5"] == "Medellin") echo "selected"; ?>>Medellin</option>
                <option value="Monteria" <?php if ($frm["CiudadDondeReside5"] == "Monteria") echo "selected"; ?>>Monteria</option>
                <option value="Necocli" <?php if ($frm["CiudadDondeReside5"] == "Necocli") echo "selected"; ?>>Necocli</option>
                <option value="Neiva" <?php if ($frm["CiudadDondeReside5"] == "Neiva") echo "selected"; ?>>Neiva</option>
                <option value="Pasto" <?php if ($frm["CiudadDondeReside5"] == "Pasto") echo "selected"; ?>>Pasto</option>
                <option value="Pereira" <?php if ($frm["CiudadDondeReside5"] == "Pereira") echo "selected"; ?>>Pereira</option>
                <option value="Popayan" <?php if ($frm["CiudadDondeReside5"] == "Popayan") echo "selected"; ?>>Popayan</option>
                <option value="Sincelejo" <?php if ($frm["CiudadDondeReside5"] == "Sincelejo") echo "selected"; ?>>Sincelejo</option>
                <option value="Tunja" <?php if ($frm["CiudadDondeReside5"] == "Tunja") echo "selected"; ?>>Tunja</option>
                <option value="Tumaco" <?php if ($frm["CiudadDondeReside5"] == "Tumaco") echo "selected"; ?>>Tumaco</option>
                <option value="Valledupar" <?php if ($frm["CiudadDondeReside5"] == "Valledupar") echo "selected"; ?>>Valledupar</option>
                <option value="Villavicenio" <?php if ($frm["CiudadDondeReside5"] == "Villavicenio") echo "selected"; ?>>Villavicenio</option>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="CorreoElectronico5">Correo Electronico</label>
            <input name="CorreoElectronico5" id="CorreoElectronico5" type="email" placeholder="CorreoElectronico5" class=" form-control " title=" CorreoElectronico5" value="<?php echo $frm["CorreoElectronico5"]; ?>" required />
        </div>

        <div class="form-group col-md-6">
            <label class="col-sm-4 control-label no-padding-right" for="Celular5">Celular</label>
            <input name="Celular5" id="Celular5" type="text" placeholder="Celular5" class="form-control " title="Celular5" value="<?php echo $frm["Celular5"]; ?>" required />
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
            </form>-->
        </div>
    </div>
</div><!-- /.widget-main -->
</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>