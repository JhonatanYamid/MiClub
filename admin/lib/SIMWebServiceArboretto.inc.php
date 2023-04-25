<?php

class SIMWebServiceArboretto
{

    // SECCION VEHICULO
    public function crear_vehiculo($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        // Código color vehiculo -- vehicleColor
        // 0-otro color, 1-blanco, 2- astilla, 3-gris, 4-negro, 5-rojo, 6-azul oscuro, 7- azul, 8-amarillo, 9-verde, 10- marrón, 11-rosa, 12- púrpura, 13- azul oscuro, 14-cian.

        // Grupo de vehiculos -- vehicleGroupIndexCode
        // 105:Arboretto

        // $JSon = '{
        //             "plateNo": "ABC123",
        //             "personId": "",
        //             "phoneNo": "",
        //             "vehicleColor": 1,
        //             "vehicleGroupIndexCode": "105",
        //             "personGivenName": "574110",
        //             "personFamilyName": "FAbian Lopez",
        //             "effectiveDate": "2023-03-03T00:00:00+08:00",
        //             "expiredDate": "2025-03-25T23:59:59+08:00"
        //         }';

        $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/vehicle/single/add", $JSon);

        // Respuesta
        // {
        // code: "0",
        // msg: "Success",
        // data: {
        // vehicleId: "2939",
        // plateNo: "abc123",
        // personName: "PersonaDePrueba Prueba 109 apps",
        // phoneNo: "22222222",
        // vehicleColor: 4,
        // vehicleGroupIndexCode: "105",
        // effectiveDate: "2023-03-03T00:00:00+08:00",
        // expiredDate: "2025-03-25T23:59:59+08:00"
        // }
        // }    
        return $response;
    }
    public function editar_vehiculo($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        // $JSon = '{
        //             "vehicleId": "2939",
        //             "plateNo": "ABC123",
        //             "personId": "2840",
        //             "phoneNo": "11111111",
        //             "vehicleColor": 4,
        //             "vehicleGroupIndexCode": "105",
        //             "personGivenName": "Vehiculo 1",
        //             "personFamilyName": "Prueba 109 apps",
        //             "effectiveDate": "2023-03-03T00:00:00+08:00",
        //             "expiredDate": "2025-03-25T23:59:59+08:00"
        //         }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/vehicle/single/update", $JSon);
        return $response;
    }
    public function borrar_vehiculo($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        // $JSon = '{
        //             "vehicleId": "2939"
        //         }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/vehicle/single/delete", $JSon);
        return $response;
    }
    public function listar_vehiculo($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/vehicle/advance/vehicleList", $JSon);
        return $response;
    }
    public function listar_vehiculos($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        $JSon = '{
            "pageNo": 1,
            "pageSize": 10,
            "vehicleGroupIndexCode": "105"
        }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/vehicle/vehicleList", $JSon);
        return $response;
    }
    // SECCION VISITANTE 
    public function crear_visitante($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        // Código proposito visita -- visitPurposeType
        // 0- negocios, 1-formación, 2-visita, 3-reunión, 4-otros.

        // Tipo Documento -- certificateType
        // 111:DNI, 414:Pasaporte, 335:licencia de conducir

        // $JSon = '{
        //             "receptionistId": "2840",
        //             "visitStartTime": "2023-03-17T15:00:00+08:00",
        //             "visitEndTime": "2023-03-18T16:00:00+08:00",
        //             "visitPurposeType": 2,
        //             "visitPurpose": "Prueba Visitante",
        //             "visitorInfoList": [
        //                 {
        //                     "VisitorInfo": {
        //                         "visitorFamilyName": "Prueba 109 apps 20230317",
        //                         "visitorGivenName": "Visitante",
        //                         "gender": 1,
        //                         "email": "",
        //                         "phoneNo": "1111111111",
        //                         "plateNo": "Abc123",
        //                         "companyName": "109 apps",
        //                         "certificateType": 111,
        //                         "certificateNo": "1234567895",
        //                         "remark": "Prueba visitante 2840"
        //                     }
        //                 }
        //             ]
        //         }';

        $response = self::send_api($IDClub, $IDSocio, "", "/api/visitor/v1/appointment", $JSon);

        // Respuesta
        // {
        // code: "0",
        // msg: "Success",
        // data: {
        // appointRecordId: "7621",
        // visitorId: "2842",
        // qrCodeImage: "iVBORw0KGgoAAAANSUhEUgAAANIAAADSAQMAAAAFVwwBAAAABlBMVEUAAAD///+l2Z/dAAAAAnRSTlP//8i138cAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAGXSURBVFiFzZjRjcMwDEMFeACP5NU9kgcIoBNJJU0P6D+DNqj78mGpFCU38vcVTuwKXHlWnrrHNXPkxlfTjM3a7b5iYTFTy3pfdmzH3LX7gfTW7ld9U4F5soqhUjxO3ZFrW3anG2pIU0ZNdLrrjlx/68WDscagg9frq/482ONQdIFUsf33MwOGGLhpBqMCWxKxFZML1BpGMFPFBqt1Y3Ud7D6U60p0yTdiuLHTGFZFUA+OI+1asSVvZa0thRF3DD7sLVmJeCCq/fiEC5tbi2RdqRlk158RYwPIIWWo94OFG1PxB31KOuaHlW4MASwUFfu9JKKHrJgcqhcYUFJtdbixbEvVtKek9yzlxJRfGcFFU0im3o4R1AJrGdZRxs1Yn0DgCDw77Y8mvFiGEt1T6ZKF9e9gxNCfOI9i3zKFeOKzYbT+HvU4ASCGy4/1mSQ6mNbHa0ZxYf2nAzsrJwBO0nd/MGI820uvdzzKuCOLnqEDttXHZkfWpyb1gxjPPGjEeh5lZ4V8B48o6cZaBz3oywg0EFix/H05sT9ZcTqZGw6wKAAAAABJRU5ErkJggg=="
        // }    
        // }

        return $response;
    }
    public function editar_visitante($IDClub, $IDSocio = "", $IDUsuario = "")
    {
        $JSon = '{
                    "appointRecordId": "7621",
                    "receptionistId": "2840",
                    "visitStartTime": "2023-03-04T15:00:00+08:00",
                    "visitEndTime": "2023-03-04T16:00:00+08:00",
                    "visitPurposeType": 2,
                    "visitPurpose": "Prueba Edición Visitante",
                    "visitorInfoList": [
                        {
                            "VisitorInfo": {
                                "visitorFamilyName": "Prueba 109 apps",
                                "visitorGivenName": "Visitante 2",
                                "gender": 1,
                                "email": "",
                                "phoneNo": "2222222222",
                                "plateNo": "Abc123",
                                "companyName": "109 apps",
                                "certificateType": 111,
                                "certificateNo": "1234567892",
                                "remark": "Prueba visitante"
                            }
                        }
                    ]
                }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/visitor/v1/appointment/update", $JSon);
        return $response;
    }
    public function borrar_visitante($IDClub, $IDSocio = "", $IDUsuario = "")
    {
        $JSon = '{
                    "appointRecordId": "7621"
                }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/visitor/v1/appointment/single/delete", $JSon);
        return $response;
    }
    public function listar_visitantes($IDClub, $IDSocio = "", $IDUsuario = "")
    {
        $JSon = '{
                    "pageNo": 1,
                    "pageSize": 300,
                    "searchCriteria": {
                        "visitorGroupID": "",
                        "identifiyCode": "",
                        "personName": "",
                        "companyName": ""
                    }
                }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/visitor/v1/visitor/visitorinfo", $JSon);
        return $response;
    }
    public function listar_visitante($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        // $JSon = '{
        //             "pageNo": 1,
        //             "pageSize": 20,
        //             "searchCriteria": {
        //                 "visitorGroupID": "",
        //                 "identifiyCode": "",
        //                 "personName": "",
        //                 "companyName": ""
        //             }
        //         }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/visitor/v1/visitor/visitorinfo", $JSon);
        return $response;
    }
    public function salida_visitante($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        // $JSon = '{
        //     "appointRecordId": "1"
        // }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/visitor/v1/visitor/out", $JSon);
        return $response;
    }

    // SECCION PERSONA (RESIDENTE,EMPLEADO)
    public function crear_persona($IDClub, $IDSocio = "", $IDUsuario = "", $Tipo)
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", "IDSocio=$IDSocio", "array");
        if ($datos_socio) {

            // Código Tipo persona -- orgIndexCode
            // 31:Residente, 33:Empleado domestico
            switch ($Tipo) {
                case 'Residente':
                    $TipoPersona = 31;
                    break;
                case 'Empleado':
                    $TipoPersona = 33;
                    break;

                default:
                    $TipoPersona = 31;
                    break;
            }
            $JSon = '{
                    "personCode": "' . $datos_socio['NumeroDocumento'] . '",
                    "personFamilyName": "' . $datos_socio['Apellido'] . '",
                    "personGivenName": "' . $datos_socio['Nombre'] . '",
                    "gender": 1,
                    "orgIndexCode": "' . $TipoPersona . '",
                    "remark": "' . $Tipo . '",
                    "phoneNo": "' . $datos_socio['Telefono'] . '",
                    "email": "' . $datos_socio['CorreoElectronico'] . '",
                    "beginTime": "' . date('Y-m-d') . 'T' . date('H:i:s') . '+00:00",
                    "endTime": "2030-05-26T15:00:00+08:00"
                }';

            $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/person/single/add", $JSon);
        } else {
            $response = '
            {
                code: "0",
                msg: "Failure",
                data: "null"
            }';
        }
        // Respuesta
        // {
        // code: "0",
        // msg: "Success",
        // data: "2844" -> Codigo interno persona
        // }
        $response = json_decode($response, true);
        return $response;
    }
    public function crear_persona_empleado($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", "IDSocio=$IDSocio", "array");
        if ($datos_socio) {

            // Código Tipo persona -- orgIndexCode
            // 31:Residente, 33:Empleado domestico

            $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/person/single/add", $JSon);
        } else {
            $response = '
            {
                code: "0",
                msg: "Failure",
                data: "null"
            }';
        }
        // Respuesta
        // {
        // code: "0",
        // msg: "Success",
        // data: "2844" -> Codigo interno persona
        // }
        $response = json_decode($response, true);
        return $response;
    }
    public function editar_persona($IDClub, $IDSocio = "", $IDUsuario = "", $Tipo)
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", "IDSocio=$IDSocio", "array");
        if ($datos_socio) {

            // Código Tipo persona -- orgIndexCode
            // 31:Residente, 33:Empleado domestico
            switch ($Tipo) {
                case 'Residente':
                    $TipoPersona = 31;
                    break;
                case 'Empleado':
                    $TipoPersona = 33;
                    break;

                default:
                    $TipoPersona = 31;
                    break;
            }
            $fecha = date('Y-m-d', strtotime($datos_socio['FechaTrCr']));
            $hora = date('H:i:s', strtotime($datos_socio['FechaTrCr']));
            if ($datos_socio['IDSocioSistemaExterno'] == 0) {
                $JSon = '{
                    "personCode": "' . $datos_socio['NumeroDocumento'] . '",
                    "personFamilyName": "' . $datos_socio['Apellido'] . '",
                    "personGivenName": "' . $datos_socio['Nombre'] . '",
                    "gender": 1,
                    "orgIndexCode": "' . $TipoPersona . '",
                    "remark": "' . $Tipo . '",
                    "phoneNo": "' . $datos_socio['Telefono'] . '",
                    "email": "' . $datos_socio['CorreoElectronico'] . '",
                    "beginTime": "' . date('Y-m-d') . 'T' . date('H:i:s') . '+00:00",
                    "endTime": "2030-05-26T15:00:00+08:00"
                }';
                $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/person/single/add", $JSon);
            } else {
                $JSon = '{
                    "personId": "' . $datos_socio['IDSocioSistemaExterno'] . '",
                    "personCode": "' . $datos_socio['NumeroDocumento'] . '",
                    "personFamilyName": "' . $datos_socio['Apellido'] . '",
                    "personGivenName": "' . $datos_socio['Nombre'] . '",
                    "orgIndexCode": "' . $TipoPersona . '",
                    "gender": 1,
                    "phoneNo": "' . $datos_socio['Telefono'] . '",
                    "remark": "' . $Tipo . '",
                    "email": "' . $datos_socio['CorreoElectronico'] . '",
                    "beginTime": "' . $fecha . 'T' . $hora . '+00:00",
                    "endTime": "2030-05-26T15:00:00+08:00"
                }';
                $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/person/single/update", $JSon);
            }
        } else {
            $response = '
            {
                code: "0",
                msg: "Failure",
                data: "null"
            }';
        }
        $response = json_decode($response, true);
        return $response;
    }
    public function borrar_persona($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        // $JSon = '{
        //         "personId": "' . $datos_socio['IDSocioSistemaExterno'] . '"
        //     }';
        // $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/person/single/delete", $JSon);
        $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/person/single/update", $JSon);

        return $response;
    }
    public function listar_persona($IDClub, $IDSocio = "", $IDUsuario = "", $JSon)
    {
        // $JSon = '{
        //             "pageNo": 1,
        //             "pageSize": 1,
        //             "personName": "Prueba 109 apps"
        //             }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/person/personList", $JSon);
        return $response;
    }
    public function listar_personas($IDClub = "", $IDSocio = "", $IDUsuario = "", $page = 1)
    {
        $JSon = '{
                    "pageNo": ' . $page . ',
                    "pageSize": 500,
                    "personName": ""
                    }';
        $response = self::send_api($IDClub, $IDSocio, "", "/api/resource/v1/person/personList", $JSon);
        return $response;
    }

    // EJECUTAR API HIKVISION
    public function send_api($IDClub, $IDSocio = "", $IDUsuario = "", $urlApi, $JSon)
    {

        $hash = hash_hmac('sha256', "POST\n*/*\napplication/json\nx-ca-key:26293790\n/artemis" . $urlApi, "06Q9yQuG6OHeM376s7Cx", true);
        $b64 = base64_encode($hash);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://arboretto.vision2cloud.com/artemis" . $urlApi,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $JSon,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'x-ca-key: 26293790',
                'x-ca-signature: ' . $b64,
                'x-ca-signature-headers: x-ca-key'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
