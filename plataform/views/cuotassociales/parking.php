    <div class="widget-header widget-header-large">
        <h3 class="widget-title grey lighter">
            <i class="ace-icon fa fa-check-circle green"></i>
            Reservas Parking
        </h3>
    </div>
    <table id="simple-table" class="table table-striped table-bordered table-hover">
        <tr>
            <th>Acci&oacute;n Titular</th>
            <th>N&uacute;mero Documento</th>
            <th>Nombre</th>
            <th>Parking</th>
            <th>Estado reserva</th>
            <th>Fecha inicio</th>
            <th>Fecha Fin</th>
            <th>Saldo</th>
        </tr>
        <tbody id="">
            <?php
            $sql_lockers = "SELECT CONCAT(s.Nombre,s.Apellido) AS NombreSocio, se.Nombre,s.NumeroDocumento,se.Nombre,se.Valor3,rg.Fecha,rg.Hora,rg.FechaCumplida FROM ReservaGeneral rg, Socio s, ServicioElemento se WHERE rg.IDServicio = 38812 AND rg.IDSocio=s.IDSocio and rg.IDServicioElemento=se.IDServicioElemento AND IDEstadoReserva IN (1) AND (s.AccionPadre in ('" . $frm['Accion'] . "') or s.Accion in ('" . $frm['Accion'] . "'))  ORDER BY Fecha DESC";
            $q_FamiliaresSocio = $dbo->query($sql_lockers);
            while ($r = $dbo->assoc($q_FamiliaresSocio)) {
                $Fecha = $r['Fecha'] . ' ' . $r['Hora'];
                $FechaFin = ($r['FechaFin'] != '0000-00-00') ? $r['FechaFin'] . ' ' . $r['HoraFin'] : date('Y-m-d h:i:s');
                $SaldoLocker = SaldoLocker($r['Valor3'], $Fecha, $FechaFin);
            ?>
                <tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
                    <td><?php echo $frm['Accion'] ?></td>
                    <td><?php echo $r['NumeroDocumento']; ?></td>
                    <td><?php echo $r['NombreSocio']; ?></td>
                    <td><?php echo $r['Nombre']; ?></td>
                    <td><?= ($r['FechaCumplida'] != '0000-00-00 00:00:00') ? 'Finalizado' : 'Ocupado'; ?></td>
                    <td><?php echo $r['Fecha'] . ' ' . $r['Hora']; ?></td>
                    <td><?= ($r['FechaCumplida'] != '0000-00-00 00:00:00') ? $r['FechaCumplida'] : '--'; ?></td>
                    <td>$<?php echo $r['Valor3']; ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
        <tr>
            <th class="texto" colspan="16"></th>
        </tr>
    </table>