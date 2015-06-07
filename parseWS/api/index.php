<?php
	require '../vendor/autoload.php';
	use Parse\ParseClient;
	use Parse\ParseObject;
	use Parse\ParseQuery;

	//require 'Slim/Slim.php';
	\Slim\Slim::registerAutoloader();

	$app = new \Slim\Slim();

	$app->get('/peticiones','getPeticiones');
	//$app->post('/peticiones','postPeticiones');
	$app->get('/accionesTiempo','getAccionesTiempo');
	$app->post('/accionesTiempo','postAccionesTiempo');
	$app->get('/accionesSeleccion','getAccionesSeleccion');
	$app->post('/accionesSeleccion','postAccionesSeleccion');
	$app->get('/accionesRuta','getAccionesRuta');
	$app->post('/accionesRuta','postAccionesRuta');
	$app->run();

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
		echo json_encode($anidado);
	}
	
	function postPeticiones() {
			
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
		echo json_encode($anidado);

	}
	
	function postAccionesTiempo() {
		
		$request = \Slim\Slim::getInstance()->request();
		$parametros = json_decode($request->getBody());
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
		$accionObject->set("ciudad", $parametros->ciudad);
		$accionObject->set("peticionId", $idPeticion);
		$accionObject->save();
		$idAccion = $accionObject->getObjectId();

		// Ahora actualizo peticion con ID de accion
		$peticionObject->set("accionId", $idAccion);
		$peticionObject->save();
		
	}
		

	function getAccionesSeleccion() {

		// Inicializo ParseClient
		ParseClient::initialize('key', 'key2', 		'key3');

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
		echo json_encode($anidado);

	}
	
	function postAccionesSeleccion() {

		$request = \Slim\Slim::getInstance()->request();
		$parametros = json_decode($request->getBody());
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
		$accionObject->set("estacion", $parametros->estacion);
		$accionObject->set("peticionId", $idPeticion);
		$accionObject->save();
		$idAccion = $accionObject->getObjectId();

		// Ahora actualizo peticion con ID de accion
		$peticionObject->set("accionId", $idAccion);
		$peticionObject->save();
	}
	
	function getAccionesRuta() {

		// Inicializo ParseClient
		ParseClient::initialize('key', 'key2', 		'key3');

		$query = new ParseQuery("CalcularRuta");
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
		echo json_encode($anidado);

	}

	function postAccionesRuta() {

		$request = \Slim\Slim::getInstance()->request();
		$parametros = json_decode($request->getBody());
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
		$accionObject->set("dirInicio", $parametros->dirInicio);
		$accionObject->set("estacion", $parametros->estacion);
		$accionObject->set("modo", $parametros->modo);
		$accionObject->set("peticionId", $idPeticion);
		$accionObject->save();
		$idAccion = $accionObject->getObjectId();

		// Ahora actualizo peticion con ID de accion
		$peticionObject->set("accionId", $idAccion);
		$peticionObject->save();
	}

?>
