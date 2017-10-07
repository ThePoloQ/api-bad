<?php

include_once('../config/config.php');

class ffbadSoap{
    
  public static function connect(){

    $clientSOAP = null;

    $wsdl = 'https://ws.ffbad.com/FFBAD-WS.wsdl';

    $params = array(
      'soap_version' => SOAP_1_2,
      'trace' => 0,
      'wsdl_cache' => 0,
      'exceptions' => 0,
      'stream_context' => stream_context_create(array(
        'ssl' => array(
          'ciphers' => 'SHA256',
          'verify_peer'   => false,
        )
      ))
    );
  
    $clientSOAP = new SoapClient($wsdl, $params);
    
    return $clientSOAP;
  }
  
}
