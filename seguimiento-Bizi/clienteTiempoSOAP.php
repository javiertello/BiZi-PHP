<?php

	/*
	 * Dado un formato {xml | json} y un id correspondiente a un municipio
	 * Español, devuelve una tabla HTML con la predicción de los 3 siguientes 
	 * días, haciendo uso de 3 servicios WEB SOAP.
	 */
	try{
		$id = "";
		$formato = "";
		
		// Cojo variables del POST
		if(isset($_POST["id"]) AND isset($_POST["formato"])){
			$id = $_POST["id"];
			$formato = $_POST["formato"];
		}else{
			// al cargar pagina por defecto Zaragoza
			$id = "50297";
			$formato = "xml";
		}

		// Create the SoapClient instance 
		$url = "http://localhost:8080/axis/services/AemetProyect?wsdl";
	
		$client = new SoapClient($url, array("trace" => 1, "exception" => 1,
				"use" => SOAP_LITERAL, "style" => SOAP_DOCUMENT,'cache_wsdl' => WSDL_CACHE_NONE));
		
		// Creo XML
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= "<!DOCTYPE id [\n";
		$xml .= "<!ELEMENT id (#PCDATA)>\n";
		$xml .= "]>\n";
		$xml .= "<id>".$id."</id>";
		
		// Codifico XML en Base64
		$Base64xml = base64_encode($xml);

		// Llamada SOAP a DescargarInfoTiempo
		$Base64xmlaemet = $client -> __soapCall("DescargarInfoTiempo",array($Base64xml));

		// Valido documento y extraigo campo resultado	
		$xmlaemet = base64_decode($Base64xmlaemet);
		$dom = new DOMDocument;
		$dom->loadXML($xmlaemet);
		if (!($dom->validate())) {
		    exit("Documento XML devuelto por DescargarInfoTiempo no valido");
		}
		// Aqui el documento es valido
		$root = $dom->documentElement;
		$rootvalue = $root->nodeValue;

		if(strcmp($formato,"json")==0){ // Formato json

			// Construyo mensaje JSON
			$jsondata = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			$jsondata .= "<!DOCTYPE aemet [\n";
			$jsondata .= "<!ELEMENT aemet (#PCDATA)>\n";
			$jsondata .= "]>\n";
			$jsondata .= "<aemet>".$rootvalue."</aemet>";
			$Base64jsondata = base64_encode($jsondata);
			
			// Peticion SOAP a GenerarJSON
			$Base64jsonaemet = $client -> __soapCall("GenerarJSON",array($Base64jsondata));
			// Valido documento y extraigo campo resultado	
			$jsonaemet = base64_decode($Base64jsonaemet);
			$dom = new DOMDocument;
			$dom->loadXML($jsonaemet);
			if (!($dom->validate())) {
			    exit("Documento XML devuelto por GenerarJSON no valido");
			}
			// Aqui el documento es valido
			$root = $dom->documentElement;
			$json = $root->nodeValue;
		
			$datos = $json;
		}else{ //formato == xml
			$formato = "xml"; // Por si cliente malintencionado
			$datos = $rootvalue;
		}
		// LLamamos a GenerarHTML
		$xmlaenviar = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xmlaenviar .= "<!DOCTYPE raiz [\n";
		$xmlaenviar .= "<!ELEMENT raiz (formato, content)>\n";
		$xmlaenviar .= "<!ELEMENT formato (#PCDATA)>\n";
		$xmlaenviar .= "<!ELEMENT content (#PCDATA)>\n";
		$xmlaenviar .= "]>\n";
		$xmlaenviar .= "<raiz>\n";
		$xmlaenviar .= "<formato>".$formato."</formato>\n";
		$xmlaenviar .= "<content>".$datos."</content>\n";
		$xmlaenviar .= "</raiz>";
		
		$Base64aenviar = base64_encode($xmlaenviar);
		$Base64xmlhtml = $client -> __soapCall("GenerarHTML",array($Base64aenviar));
		$xmlhtml = base64_decode($Base64xmlhtml);

		// Validamos de nuevo XML devuelto por servicio
		$dom = new DOMDocument;
		$dom->loadXML($xmlhtml);
		if (!($dom->validate())) {
		    exit("Documento XML devuelto por GenerarHTML no valido");
		}
		// Aqui el documento es valido
		$root = $dom->documentElement;
		$rootvalue = $root->nodeValue;

		echo base64_decode($rootvalue);

	} catch (SoapFault $ex){
		//$error = "SOAP Fault: (faultcode: {$ex->faultcode}\n"
		//					."faultstring: {$ex->faultstring})";
		$error = "Hubo un problema, inténtelo más tarde</br>";
		echo $error;
	} catch (Exception $e){
		//$error = "Exception: {$e->faultstring}";
		$error = "Hubo un problema grave, inténtelo más tarde";
		echo $error;
	}

?>
