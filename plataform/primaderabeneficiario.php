<?
include("procedures/general.php");
include("procedures/primaderaadmin.php");
include("cmp/seo.php");
?>
<div class="form-group col-md-12">

    <table class="table">

        <thead class="thead-dark">
            <div>
                <h3>III DATOS BENEFICIARIOS</h3>
            </div>
            <tr>

                <th>Nombre</th>
                <th>APELLIDO1</th>
                <th>APELLIDO2</th>
                <th>PARENTESCO</th>
                <th>SEXO</th>
                <th>TIPO DE IDENTIDAD</th>
                <th>NUMERO DE DOCUMENTO</th>
                <th>PAIS DE EXPEDICION</th>
                <th>GRUPO SANGUINEO</th>
                <th>RH</th>
                <th>FECHA DE NACIMIENTO</th>
                <th>PAIS DE NACIMIENTO</th>
                <th>ESTADO CIVIL</th>
                <th>BENEFICIARIO SALUD</th>
                <th>PROFESION</th>
                <th>DIRECCION</th>
                <th>TELEFONO</th>
                <th>LE HAN DIAGNOSTICADO ALGUNA ENFERMEDAD?</th>
                <th>CUAL ENFERMEDAD?</th>
                <th>TIENE ALGUNA DISCAPACIDAD?</th>
                <th>CUAL DISCAPACIDAD?</th>
                <th>COMPARTE DOMICILIO?</th>
                <th>ACTIVO</th>
                <th>MOTIVO DE INACTIVACION</th>
                <th>DEPENDE ECONOMICAMENTE</th>
                <th>ARCHIVO</th>


            </tr>
        </thead>

        <tbody id="tabla-beneficiarios">
            <?php
            $ID = $_GET['id'];
            $sql = "SELECT * FROM PrimaderaBeneficiario WHERE IDPrimaderaEmpleados='$ID'";

            $query = $dbo->query($sql);
            $beneficiariosTable = $dbo->fetch($query);
            $beneficiariosTable = isset($beneficiariosTable["IDPrimaderaBeneficiario"]) ? [$beneficiariosTable] : $beneficiariosTable;
            foreach ($beneficiariosTable as $beneficiario) {
                $beneficiarios[] = $beneficiario;
            ?>
                <tr>


                    <td><?php echo $beneficiario['NOMBRE'] ?></td>
                    <td><?php echo $beneficiario['APELLIDO1'] ?></td>
                    <td><?php echo $beneficiario['APELLIDO2'] ?></td>
                    <td><?php echo $beneficiario['RELAC_FAM'] ?></td>
                    <td><?php echo $beneficiario['SEXO'] ?></td>
                    <td><?php echo $beneficiario['TIPO_IDENT'] ?></td>
                    <td><?php echo $beneficiario['IDENT_NUM'] ?></td>
                    <td><?php echo $beneficiario['UGN1_CODIGO_IDENT'] ?></td>
                    <td><?php echo $beneficiario['BENE_TIPO_SANGRE'] ?></td>
                    <td><?php echo $beneficiario['BENE_SANGRE_RH'] ?></td>
                    <td><?php echo $beneficiario['FEC_NACIO'] ?></td>
                    <td><?php echo $beneficiario['UGN1_CODIGO_NACI'] ?></td>
                    <td><?php echo $beneficiario['EST_CIVIL'] ?></td>
                    <td><?php echo $beneficiario['BENEF_CAMPO_IND4'] ?></td>
                    <td><?php echo $beneficiario['PROFESION'] ?></td>
                    <td><?php echo $beneficiario['DIRECCION'] ?></td>
                    <td><?php echo $beneficiario['TELEFONO'] ?></td>
                    <td><?php echo $beneficiario['BENEF_CAMPO_IND2'] ?></td>
                    <td><?php echo $beneficiario['BENEF_CAMPO_ALF5'] ?></td>
                    <td><?php echo $beneficiario['BENEF_CAMPO_IND3'] ?></td>
                    <td><?php echo $beneficiario['BENEF_CAMPO_NUM1'] ?></td>
                    <td><?php echo $beneficiario['RESIDE_EMPLEADO'] ?></td>
                    <td><?php echo $beneficiario['BENE_ESTADO'] ?></td>
                    <td><?php echo $beneficiario['MOTIVO_INACTIVACION'] ?></td>
                    <td><?php echo $beneficiario['DEPENDIENTE'] ?></td>
                    <td><?php echo $beneficiario['ARCHIVO'] ?></td>





                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <hr>