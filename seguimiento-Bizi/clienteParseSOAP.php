 <?php
 /*
 * Se encarga de interactuar con el WS SOAP de Parse. Recibe una serie de 
 * parámetros por POST y en función de ellos llama a una operación SOAP u a otra
 */

	$options = array('location' => 'http://localhost/parseWS/server.php',
										'uri' => 'http://localhost/parseWS');
	//create an instante of the SOAPClient (the API will be available)
	$api = new SoapClient(NULL, $options);
	//call an API method

	if(!isset($_POST["funcion"])){
		exit("Funcion no especificada");
	}

	$funcion = $_POST['funcion'];

	if(strcmp($funcion,"postAccionesTiempo")==0){
		if(!isset($_POST["ciudad"])){
			exit("Falta parametro POST ciudad");
		}
		postAccionesTiempo($_POST['ciudad']);
	} elseif(strcmp($funcion,"postAccionesSeleccion")==0){
		if(!isset($_POST["estacion"])){
			exit("Falta parametro POST estacion");
		}
		postAccionesSeleccion($_POST['estacion']);
	} elseif(strcmp($funcion,"postAccionesRuta")==0){
		if(!(isset($_POST["dirInicio"]) && isset($_POST["estacion"]) && 
				isset($_POST["modo"]))){
			exit("Falta parametro POST modo, dirInicio o estacion");
		}
		postAccionesRuta($_POST['dirInicio'], $_POST['estacion'], $_POST['modo']);
	} elseif(strcmp($funcion,"getPeticiones")==0){
		getPeticiones();
	} elseif(strcmp($funcion,"getAccionesTiempo")==0){
		getAccionesTiempo();
	} elseif(strcmp($funcion,"getAccionesSeleccion")==0){
		getAccionesSeleccion();
	} elseif(strcmp($funcion,"getAccionesRuta")==0){
		getAccionesRuta();
	}
		
	function postAccionesTiempo($ciudad){
		global $api;
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= "<!DOCTYPE root [\n";
		$xml .= "<!ELEMENT root (ciudad)>\n";
		$xml .= "<!ELEMENT ciudad (#PCDATA)>\n";
		$xml .= "]>\n";
		$xml .= "<root>";
		$xml .= "<ciudad>".$ciudad."</ciudad>";
		$xml .= "</root>";
		echo $api->postAccionesTiempo($xml);
	}

	function postAccionesSeleccion($estacion){
		global $api;
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= "<!DOCTYPE root [\n";
		$xml .= "<!ELEMENT root (estacion)>\n";
		$xml .= "<!ELEMENT estacion (#PCDATA)>\n";
		$xml .= "]>\n";
		$xml .= "<root>";
		$xml .= "<estacion>".$estacion."</estacion>";
		$xml .= "</root>";
		echo $api->postAccionesSeleccion($xml);
	}

	function postAccionesRuta($dirInicio, $estacion, $modo){
		global $api;
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= "<!DOCTYPE root [\n";
		$xml .= "<!ELEMENT root (dirInicio, estacion, modo)>\n";
		$xml .= "<!ELEMENT dirInicio (#PCDATA)>\n";
		$xml .= "<!ELEMENT estacion (#PCDATA)>\n";
		$xml .= "<!ELEMENT modo (#PCDATA)>\n";
		$xml .= "]>\n";
		$xml .= "<root>";
		$xml .= "<dirInicio>".$dirInicio."</dirInicio>";
		$xml .= "<estacion>".$estacion."</estacion>";
		$xml .= "<modo>".$modo."</modo>";
		$xml .= "</root>";
		echo $api->postAccionesRuta($xml);
	}
	

	function getPeticiones(){
		global $api;
		$xml = $api->getPeticiones();
		$dom = new DOMDocument;
		$dom->loadXML($xml);
		if (!($dom->validate())) {
			exit("Documento XML no valido");
		}
		// Aqui el documento es valido
		$root = $dom->documentElement;
		$json = base64_decode($root->nodeValue);
		echo $json;
	}
	
	function getAccionesSeleccion(){
		global $api;
		$xml = $api->getAccionesSeleccion();
		$dom = new DOMDocument;
		$dom->loadXML($xml);
		if (!($dom->validate())) {
			exit("Documento XML no valido");
		}
		// Aqui el documento es valido
		$root = $dom->documentElement;
		$json = base64_decode($root->nodeValue);
		echo $json;
	}

	function getAccionesTiempo(){
		global $api;
		$xml = $api->getAccionesTiempo();
		$dom = new DOMDocument;
		$dom->loadXML($xml);
		if (!($dom->validate())) {
			exit("Documento XML no valido");
		}
		// Aqui el documento es valido
		$root = $dom->documentElement;
		$json = base64_decode($root->nodeValue);
		echo $json;
	}

	function getAccionesRuta(){
		global $api;
		$xml = $api->getAccionesRuta();
		$dom = new DOMDocument;
		$dom->loadXML($xml);
		if (!($dom->validate())) {
			exit("Documento XML no valido");
		}
		// Aqui el documento es valido
		$root = $dom->documentElement;
		$json = base64_decode($root->nodeValue);
		echo $json;
	}
	


 ?>
