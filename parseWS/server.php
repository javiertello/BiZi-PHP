<?php
	
	/*
	 * Implementa las operaciones del WS SOAP que interactua con Parse
	 */

	require 'vendor/autoload.php';
	use Parse\ParseClient;
	use Parse\ParseObject;
	use Parse\ParseQuery;
	
	//a basic API class
	class MyAPI {
		  function getAccionesSeleccion() {

				// Inicializo ParseClient
				ParseClient::initialize'key', 'key2', 		'key3');

				$query = new ParseQuery("SeleccionarEstacion");
				$query->limit(1000);
				$resultado = $query->find();
				$peticiones = array();
				// Recorro consulta y meto campos interesantes a Array
				foreach($resultado as $peticion){
					$arr = array('objectId' => $peticion->getObjectId(), 'createdAt' => $peticion->getCreatedAt(),
								 'estacion' => $peticion->get("estacion"), 'peticionId' => $peticion->get("peticionId"));
					array_push($peticiones, $arr);
				}
		
				$anidado = array('accionesSeleccion' => $peticiones);
				$json = json_encode($anidado);
				$base64json = base64_encode($json);
				$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				$xml .= "<!DOCTYPE resultado [\n";
				$xml .= "<!ELEMENT resultado (#PCDATA)>\n";
				$xml .= "]>\n";
				$xml .= "<resultado>".$base64json."</resultado>";
				return $xml;

			}
			
			function getAccionesTiempo() {

				// Inicializo ParseClient
				ParseClient::initialize('key', 'key2', 		'key3');

				$query = new ParseQuery("ConsultarTiempo");
				$query->limit(1000);
				$resultado = $query->find();
				$peticiones = array();
				// Recorro consulta y meto campos interesantes a Array
				foreach($resultado as $peticion){
					$arr = array('objectId' => $peticion->getObjectId(), 'createdAt' => $peticion->getCreatedAt(),
								 'ciudad' => $peticion->get("ciudad"), 'peticionId' => $peticion->get("peticionId"));
					array_push($peticiones, $arr);
				}
		
				$anidado = array('accionesTiempo' => $peticiones);
				$json = json_encode($anidado);
				$base64json = base64_encode($json);
				$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				$xml .= "<!DOCTYPE resultado [\n";
				$xml .= "<!ELEMENT resultado (#PCDATA)>\n";
				$xml .= "]>\n";
				$xml .= "<resultado>".$base64json."</resultado>";
				return $xml;

			}

			function getPeticiones() {
				// Inicializo ParseClient
				ParseClient::initialize('key', 'key2', 		'key3');

				$query = new ParseQuery("Peticion");
				$query->limit(1000);
				$resultado = $query->find();
				$peticiones = array();
				// Recorro consulta y meto campos interesantes a Array
				foreach($resultado as $peticion){
					$arr = array('objectId' => $peticion->getObjectId(), 'createdAt' => $peticion->getCreatedAt(),
								 'ip' => $peticion->get("ip"), 'ciudad' => $peticion->get("ciudad"),
								 'accion' => $peticion->get("accion"), 'accionId' => $peticion->get("accionId"));
					array_push($peticiones, $arr);
				}
		
				$anidado = array('peticiones' => $peticiones);
				$json = json_encode($anidado);
				$base64json = base64_encode($json);
				$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				$xml .= "<!DOCTYPE resultado [\n";
				$xml .= "<!ELEMENT resultado (#PCDATA)>\n";
				$xml .= "]>\n";
				$xml .= "<resultado>".$base64json."</resultado>";
				return $xml;
			}
			
			function getAccionesRuta() {

				// Inicializo ParseClient
				ParseClient::initialize('key', 'key2', 		'key3');

				$query = new ParseQuery("CalcularRuta");
				$query->limit(1000);
				$resultado = $query->find();
				$peticiones = array();
				// Recorro consulta y meto campos interesantes a Array
				foreach($resultado as $peticion){
					$arr = array('objectId' => $peticion->getObjectId(), 'createdAt' => $peticion->getCreatedAt(),
								 'dirInicio' => $peticion->get("dirInicio"), 'peticionId' => $peticion->get("peticionId"),
								 'estacion' => $peticion->get("estacion"),'modo' => $peticion->get("modo"));
					array_push($peticiones, $arr);
				}
		
				$anidado = array('accionesRuta' => $peticiones);
				$json = json_encode($anidado);
				$base64json = base64_encode($json);
				$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				$xml .= "<!DOCTYPE resultado [\n";
				$xml .= "<!ELEMENT resultado (#PCDATA)>\n";
				$xml .= "]>\n";
				$xml .= "<resultado>".$base64json."</resultado>";
				return $xml;

			}
		
		
			function postAccionesTiempo($xmlp) {
				// CANONIZACION
				$xml = htmlspecialchars_decode($xmlp);
				$dom = new DOMDocument;
				$dom->loadXML($xml);
				if (!($dom->validate())) {
					exit("Documento XML no valido");
				}
				// Aqui el documento es valido
				$root = $dom->documentElement;
				$ciudadp = $root->childNodes[0]->nodeValue;
				
				// Obtengo la IP de la pet. HTTP
				$ip = "";
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				  $ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
				  $ip = $_SERVER['REMOTE_ADDR'];
				}
				// Obtengo la ciudad de la IP
				$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
				$ciudad = "";
				try{
					$ciudad =  $details->city;
				} catch (Exception $ee){ }

				// Inicializo ParseClient
				ParseClient::initialize('key', 'key2', 		'key3');

				// REGISTRO LA PETICION
				$peticionObject = ParseObject::create("Peticion");
				$peticionObject->set("ip", $ip);
				$peticionObject->set("ciudad", $ciudad);
				$peticionObject->set("accion", "ConsultarTiempo");
				$peticionObject->save();
				$idPeticion = $peticionObject->getObjectId();
	
				// REGISTRO LA ACCION "ConsultarTiempo"
				$accionObject = ParseObject::create("ConsultarTiempo");
				$accionObject->set("ciudad", $ciudadp);
				$accionObject->set("peticionId", $idPeticion);
				$accionObject->save();
				$idAccion = $accionObject->getObjectId();

				// Ahora actualizo peticion con ID de accion
				$peticionObject->set("accionId", $idAccion);
				$peticionObject->save();
		
			}


			function postAccionesSeleccion($xmlp) {

				// CANONIZACION
				$xml = htmlspecialchars_decode($xmlp);
				$dom = new DOMDocument;
				$dom->loadXML($xml);
				if (!($dom->validate())) {
					exit("Documento XML no valido");
				}
				// Aqui el documento es valido
				$root = $dom->documentElement;
				$estacion = $root->childNodes[0]->nodeValue;

				// Obtengo la IP de la pet. HTTP
				$ip = "";
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				// Obtengo la ciudad de la IP
				$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
				$ciudad = "";
				try{
					$ciudad =  $details->city;
				} catch (Exception $ee){ }

				// Inicializo ParseClient
				ParseClient::initialize('key', 'key2', 		'key3');

				// REGISTRO LA PETICION
				$peticionObject = ParseObject::create("Peticion");
				$peticionObject->set("ip", $ip);
				$peticionObject->set("ciudad", $ciudad);
				$peticionObject->set("accion", "SeleccionarEstacion");
				$peticionObject->save();
				$idPeticion = $peticionObject->getObjectId();
	
				// REGISTRO LA ACCION "SeleccionarEstacion"
				$accionObject = ParseObject::create("SeleccionarEstacion");
				$accionObject->set("estacion", $estacion);
				$accionObject->set("peticionId", $idPeticion);
				$accionObject->save();
				$idAccion = $accionObject->getObjectId();

				// Ahora actualizo peticion con ID de accion
				$peticionObject->set("accionId", $idAccion);
				$peticionObject->save();
			}
	
			function postAccionesRuta($xmlp) {

				// CANONIZACION
				$xml = htmlspecialchars_decode($xmlp);
				$dom = new DOMDocument;
				$dom->loadXML($xml);
				if (!($dom->validate())) {
					exit("Documento XML no valido");
				}
				// Aqui el documento es valido
				$root = $dom->documentElement;
				$dirInicio = $root->getElementsByTagName('dirInicio')[0]->nodeValue;
				$estacion = $root->getElementsByTagName('estacion')[0]->nodeValue;
				$modo = $root->getElementsByTagName('modo')[0]->nodeValue;

				// Obtengo la IP de la pet. HTTP
				$ip = "";
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				// Obtengo la ciudad de la IP
				$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
				$ciudad = "";
				try{
					$ciudad =  $details->city;
				} catch (Exception $ee){ }

				// Inicializo ParseClient
				ParseClient::initialize('key', 'key2', 		'key3');

				// REGISTRO LA PETICION
				$peticionObject = ParseObject::create("Peticion");
				$peticionObject->set("ip", $ip);
				$peticionObject->set("ciudad", $ciudad);
				$peticionObject->set("accion", "CalcularRuta");
				$peticionObject->save();
				$idPeticion = $peticionObject->getObjectId();
	
				// REGISTRO LA ACCION "SeleccionarEstacion"
				$accionObject = ParseObject::create("CalcularRuta");
				$accionObject->set("dirInicio", $dirInicio);
				$accionObject->set("estacion", $estacion);
				$accionObject->set("modo", $modo);
				$accionObject->set("peticionId", $idPeticion);
				$accionObject->save();
				$idAccion = $accionObject->getObjectId();

				// Ahora actualizo peticion con ID de accion
				$peticionObject->set("accionId", $idAccion);
				$peticionObject->save();
			}

	}
	
	//when in non-wsdl mode the uri option must be specified
	$options=array('uri'=>'http://localhost/parseWS');
	//create a new SOAP server
	$server = new SoapServer(NULL,$options);
	//attach the API class to the SOAP Server
	$server->setClass('MyAPI');
	//start the SOAP requests handler
	$server->handle();
	
?>
