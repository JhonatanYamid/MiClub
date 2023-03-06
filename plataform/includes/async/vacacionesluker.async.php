<?php
include("../../../admin/config.inc.php");

// $conn = SIMUtil::ConexionBDLuker();
// 

// $q = $conn->prepare("select * from EMPLEADO")

// require(LIBDIR . "SIMWebServiceAccesos.inc.php");
require(LIBDIR . "SIMWebServiceCampestrePereira.inc.php");
require(LIBDIR . "SIMWebServiceLaboral.inc.php");
// require(LIBDIR . "SIMWebServicePasarelaPagos.inc.php");
// require(LIBDIR . "SIMWebServiceValleArriba.inc.php");
// require(LIBDIR . "SIMWebServiceFacturas.inc.php");

// echo sha1('mariaesolis');
// die();

// require(LIBDIR . "SIMWebServiceReservas.inc.php");
// // require(LIBDIR . "SIMWebServiceCampestrePereira.inc.php");
// // echo '<pre>';
// var_dump(SIMUtil::Notificar_solicitud_laboral(8, 574110, "", 19, 'Certificado'));
// // var_dump(SIMPasarelaPagos::CredibancoRespuestaV2("4e9b1936-ce00-7a4a-8512-8ab80d8b1a2e"));
// // var_dump(SIMPasarelaPagos::CredibancoRespuestaV2("893c18fe-ad44-7d55-86cd-a5630d8b1a2e"));
// // var_dump(SIMWebServiceCampestrePereira::Factura("d44f17c7-703c-7cff-b3c3-416b0d8b1a2e"));
// print_r(SIMWebServiceCampestrePereira::Consumos("42084055"));
// print_r(SIMWebServiceCampestrePereira::EstadoCuenta("1088250896"));
print_r(SIMPasarelaPagos::CredibancoRespuestaV2("8ae7b854-8772-7f19-b072-078c0d6060ab"));
// // var_dump(SIMPasarelaPagos::CredibancoRespuestaV2("106cd85e-713b-7968-965c-6cab0d8b1a2e"));
// // var_dump(SIMWebServiceAccesos::get_campos_invitados(8, 742469));
// var_dump(SIMWebServiceAccesos::set_invitado(8, 574110, 115353454, "Prueba", "2022-12-13", '[{ "IDCampoFormularioInvitado":"44","Valor":"Invitado por derecho"}]', ""));
// var_dump(SIMWebServiceReservas::get_campos_invitado_externo(8, "", 742469, 2725));
// var_dump(SIMWebServiceAccesos::set_eliminar_invitado_v1(8, 574110, 357106));
// var_dump(SIMWebServiceAccesos::ingreso_salida_usuario(8, 451565, 'Salida'));
// var_dump(SIMWebServiceAccesos::ingreso_salida_usuario(8, 4527373, 'Entrada'));
// var_dump(SIMPasarelaPagos::getTokenLukaPay(8));
// var_dump(SIMPasarelaPagos::getLinkLukaPay(8));
// print_r(SIMWebServiceLaboral::get_laboral_calcula_fechafin(97, '2023-02-16', 3, 0, 3, '', 9857));
// var_dump(SIMWebServiceLaboral::save_AuxiliosSolicitud_infinito('IDSocio', 574110, 'Socio', 8, 11,));
// var_dump(SIMWebServiceValleArriba::Cuentas('P0147'));
// print_r(SIMWebServiceFacturas::get_factura_valle_arriba(183, 611260, "", ""));
// var_dump(SIMWebServicePasarelaLukaPay::SondaAPILukaPay(8, 1674676894));




die();

// exit;
