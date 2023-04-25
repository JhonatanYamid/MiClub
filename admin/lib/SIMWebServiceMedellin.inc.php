<?php
    class SIMWebServiceMedellin
    {
        public function NotificaVacuna($IDVacuna)
        {
            $dbo = SIMDB::get();

            $sqlVacuna = "SELECT IDSocio, Certificado FROM Vacuna2 WHERE IDVacuna = $IDVacuna";
            $qryVacuna = $dbo->query($sqlVacuna);
            $datos_vacuna = $dbo->fetchArray($qryVacuna);

            $sqlSocio = "SELECT NumeroDocumento FROM Socio WHERE IDSocio = $datos_vacuna[IDSocio]";
            $qrySocio = $dbo->query($sqlSocio);
            $datos_socio = $dbo->fetchArray($qrySocio);
            
            $campopdf = "SELECT Valor FROM VacunaCampoVacunacion2 WHERE IDVacuna = $IDVacuna AND IDCampoVacunacion = 17";
            $qrycampo = $dbo->query($campopdf);
            $pdf = $dbo->fetchArray($qrycampo);

            $numeroDocumento = $datos_socio[NumeroDocumento];
            $fechaRegistro = date("d/m/Y");
            $link1 = VACUNA_ROOT . $datos_vacuna[Certificado];
            $link2 = VACUNA_ROOT . $pdf[Valor];

            $server = '190.0.53.38';
            try {
                $hostname = $server;
                $port = "";
                $dbname = DBNAME_MEDELLIN;
                $username = DBUSER_MEDELLIN;
                $pw = DBPASS_MEDELLIN;
                $dbh = new PDO("dblib:host=$hostname;dbname=$dbname", "$username", "$pw");
            } catch (PDOException $e) {
                
                echo $respuesta["message"] = "Lo sentimos no hay conexion a la base";
                exit;
            }
           
            $Insert = "INSERT INTO app_registrovacuna (numeroDocumento, fechaRegistro, link1, link2, sincronizado) VALUES ('$numeroDocumento','$fechaRegistro','$link1','$link2','0');";
            $dbh->query($Insert);
            // echo "<br><br>";

            return "Rgistrado en Base de datos Medellin";

        }
    }