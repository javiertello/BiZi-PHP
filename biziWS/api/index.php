<?php
	require '../vendor/autoload.php';

	\Slim\Slim::registerAutoloader();

	class estacion{
		  public $id;
		  public $estado;
		  public $disponibles;
		  public $anclajes;
		  public $latitude;
		  public $longitude;
			public $title;
	 
		  function setId($iden){
		      $this->id=$iden;
		  }
		  function getId()
		  {
		      return $this->id;
		  }
			function setTitle($titulo){
		      $this->title=$titulo;
		  }
			function getTitle()
		  {
		      return $this->title;
		  }
		   
		  function setEstado($est){
		      $this->estado=$est;
		  }
		  function getEstado()
		  {
		      return $this->estado;
		  }
		   
		  function setDisponibles($disp){
		      $this->disponibles=$disp;
		  }
		  function getDisponibles()
		  {
		      return $this->disponibles;
		  }
		   
		  function setAnclajes($anclaje){
		      $this->anclajes=$anclaje;
		  }
		  function getAnclajes()
		  {
		      return $this->anclajes;
		  }
		   
		  function setLatitude($lat){
		      $this->latitude=$lat;
		  }
		  function getLatitude()
		  {
		      return $this->latitude;
		  }
		   
		  function setLongitude($lon){
		      $this->longitude=$lon;
		  }
		  function getLongitude()
		  {
		      return $this->longitude;
		  }
	 
	}
	 
	$app = new \Slim\Slim();
	 
	$app->get('/estacionesJson', 'getEstJson');
	$app->get('/estacionesXML', 'getEstXML');
	$app -> run();
	 
	 
	function getEstJson() {
	 
		  $url = 'http://www.zaragoza.es/api/recurso/urbanismo-infraestructuras/estacion-bicicleta.json?srsname=wgs84&rows=130';
		  $response = file_get_contents($url);
		  $result= json_decode($response, true);
		  $bicis = $result['result'];
			$estaciones = array();
		  foreach ($bicis as $bici) {
		      $estacion = new estacion();
		      $id = $bici['id'];
					$title = $bici['title'];
		      $estado = $bici['estado'];
		      $disponibles = $bici['bicisDisponibles'];
		      $anclajes = $bici['anclajesDisponibles'];
		      $geometry = $bici['geometry'];
		      $coordenadas = $geometry['coordinates'];
		      $latitude = $coordenadas[0];
		      $longitude = $coordenadas[1];
		      $estacion->setId($id);
					$estacion->setTitle($title);
		      $estacion->setEstado($estado);
		      $estacion->setDisponibles($disponibles);
		      $estacion->setAnclajes($anclajes);
		      $estacion->setLatitude($latitude);
		      $estacion->setLongitude($longitude);
		      $prueba = $estacion->getId();
		      array_push($estaciones, $estacion);
		  }
			$estaciones_ = array("estaciones"=>$estaciones);
		  echo json_encode($estaciones_);
	 
	}
	function getEstXML() {

	 $dtd = '<!DOCTYPE resultado [
            <!ELEMENT resultado (totalCount, start, rows, result)>
            <!ELEMENT totalCount (#PCDATA)>
            <!ELEMENT start (#PCDATA)>
            <!ELEMENT rows (#PCDATA)>
            <!ELEMENT result (estacion*)>
            <!ELEMENT estacion (id, uri, title, estado, bicisDisponibles, 
            anclajesDisponibles, geometry, lastUpdated,description, icon)>
            <!ELEMENT id (#PCDATA)>
            <!ELEMENT uri (#PCDATA)>
            <!ELEMENT title (#PCDATA)>
            <!ELEMENT estado (#PCDATA)>
            <!ELEMENT bicisDisponibles (#PCDATA)>
            <!ELEMENT anclajesDisponibles (#PCDATA)>
            <!ELEMENT geometry (type, coordinates)>
            <!ELEMENT type (#PCDATA)>
            <!ELEMENT coordinates (#PCDATA)>
            <!ELEMENT lastUpdated (#PCDATA)>
            <!ELEMENT description (#PCDATA)>
            <!ELEMENT p (#PCDATA)>
            <!ELEMENT icon (#PCDATA)>
            ]>';
			
		  $url = 'http://www.zaragoza.es/api/recurso/urbanismo-infraestructuras/estacion-bicicleta.xml?srsname=wgs84&rows=130';
		  $response = file_get_contents($url);
	
			// Valido el documento del AEMET
			$dom = new DOMDocument;
			$dom->loadXML($dtd.$response);
			if (!($dom->validate())) {
				  exit("Documento XML devuelto por el Ayuntamiento de Zgz no es valido");
			}
			// Aqui el documento es valido

			$result = new SimpleXMLElement($response);
			$xml = "<?xml version=\"1.0\"?>";
			$xml .= "<!DOCTYPE estaciones [\n";
			$xml .= "<!ELEMENT estaciones (estacion*)>\n";
			$xml .= "<!ELEMENT estacion (id, title, estado, disponibles, 
																		anclajes, latitude, longitude)>\n";
			$xml .= "<!ELEMENT id (#PCDATA)>\n";
			$xml .= "<!ELEMENT title (#PCDATA)>\n";
			$xml .= "<!ELEMENT estado (#PCDATA)>\n";
			$xml .= "<!ELEMENT disponibles (#PCDATA)>\n";
			$xml .= "<!ELEMENT anclajes (#PCDATA)>\n";
			$xml .= "<!ELEMENT latitude (#PCDATA)>\n";
			$xml .= "<!ELEMENT longitude (#PCDATA)>\n";
			$xml .= "]>\n";
			$xml .= "<estaciones>\n";
		  foreach($result->result->estacion as $bici):

		      $id = $bici->id;
					$title = $bici->title;
		      $estado = $bici->estado;
		      $disponibles = $bici->bicisDisponibles;
		      $anclajes = $bici->anclajesDisponibles;
		      $coordenadas = $bici->geometry->coordinates;
		      $coordinates = explode(",",$coordenadas);
		      $latitude = $coordinates[0];
		      $longitude = $coordinates[1];
					
					$xml .= "<estacion>\n";

		     	$xml .= "<id>".$id."</id>\n";
					$xml .= "<title>".$title."</title>\n";
					$xml .= "<estado>".$estado."</estado>\n";
					$xml .= "<disponibles>".$disponibles."</disponibles>\n";
					$xml .= "<anclajes>".$anclajes."</anclajes>\n";
					$xml .= "<latitude>".$latitude."</latitude>\n";
					$xml .= "<longitude>".$longitude."</longitude>\n";
					$xml .= "</estacion>\n";
		  endforeach;
			$xml .= "</estaciones>";
			echo $xml;  
	}

?>
