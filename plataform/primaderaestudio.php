<?
include("procedures/general.php");
include("procedures/primaderaadmin.php");
include("cmp/seo.php");
?>
<div class="form-group col-md-12">

    <table class="table">

        <thead class="thead-dark">
            <div>
                <h3>III DATOS ESTUDIOS</h3>
            </div>
            <tr>

                <th>NIVEL DE ESTUDIO</th>
                <th>PAIS DONDE ESTUDIO</th>
                <th>INSTITUCION</th>
                <th>TITULO</th>
                <th>FECHA DE TERMINACION</th>
                <th>OBSERVACIONES</th>
                <th>ARCHIVO</th>

            </tr>
        </thead>

        <tbody id="tabla-beneficiarios">
            <?php
            $ID = $_GET['id'];
            $sql = "SELECT * FROM PrimaderaEstudio WHERE IDPrimaderaEmpleados='$ID'";

            $query = $dbo->query($sql);
            $estudiosTable = $dbo->fetch($query);
            $estudiosTable = isset($estudiosTable["IDPrimaderaEstudio"]) ? [$estudiosTable] : $estudiosTable;
            foreach ($estudiosTable as $estudio) {
                $beneficiarios[] = $estudio;
            ?>
                <tr>


                    <td><?php echo $estudio['NEST_CODIGO'] ?></td>
                    <td><?php echo $estudio['UGN1_CODIGO'] ?></td>
                    <td><?php echo $estudio['TERC_DOCUMENTO'] ?></td>
                    <td><?php echo $estudio['ESXB_TITULO'] ?></td>
                    <td><?php echo $estudio['ESXB_FECHA_RET'] ?></td>
                    <td><?php echo $estudio['ESXB_IDIOMAS'] ?></td>
                    <td><?php echo $estudio['Archivo'] ?></td>





                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <hr>