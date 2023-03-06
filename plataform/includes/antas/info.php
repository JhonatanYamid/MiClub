<?php 

phpinfo();
exit;


						require_once('lib/nusoap2/nusoap.php');

                        $wsdl = "http://190.157.5.99/wsZeusContabilidad/ServiceWS.asmx?WSDL";     
                        $soap = new nusoap_client($wsdl,'wsdl');         
                        $soap->useHTTPPersistentConnection();

                        $parametros['Request']['Body']  = '<generatortoken><security user="gunclub" password="123456" /></generatortoken>';

                        $result = $soap->call("SessionIDToken",$parametros); 

                        echo "TOKEN: " . $token = $result['SessionIDTokenResult']['SessionID'];
						
						$dia = date('j');
						if($dia >= 14){
							$mes = date('m',strtotime("-1 month"));
							$month_ini = new DateTime("first day of this month");
						}
						else{
							$mes = date('m',strtotime("-2 month"));
							$month_ini = new DateTime("first day of last month");
						}
						
						
						
						echo $codigo = "0365";
						
                        $parametros2['Request']['SessionID'] =  $token ;
                        $parametros2['Request']['DynamicProperty'] = 2000;
                        $parametros2['Request']['Action'] = 'Contabilidad';
                        $parametros2['Request']['Body']  = '<Parametros><Periodo>2017'.$mes.'</Periodo><AccionInicial>'.$codigo.'</AccionInicial><AccionFinal>'.$codigo.'</AccionFinal></Parametros>';
						
                        $total = $soap->call("StandardCommunicationSOAP",$parametros2); 
                        
						$xml = simplexml_load_string($total['StandardCommunicationSOAPResult']['Body']);
						
						echo '<pre>';
							print_r($xml);
						echo '</pre>';

?>