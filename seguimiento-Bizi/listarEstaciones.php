<?php
	/*
	 * Devuelve una lista de options que contienen las estaciones de 
	 * BiZi. Permite interactuar con el WS mediante mediante XML o JSON
	 */

	
	$formato = "";
	$formato = trim(file_get_contents("biziConfig.txt")); // xml o json

	if(strcmp($formato,"xml")==0){
		$xml = file_get_contents("http://localhost/biziWS/api/estacionesXML");
		// Valido XML
		$dom = new DOMDocument;
		$dom->loadXML($xml);
		if (!($dom->validate())) {
		    exit("Documento XML devuelto por biziWS no valido");
		}
		// Aqui el documento es valido
		$estaciones = new SimpleXMLElement($xml);
		$options ="";
		foreach($estaciones->estacion as $estacion){
			$title = $estacion->title;
			$LatLng = $estacion->latitude.",".$estacion->longitude;
			$options .= "<option value=\"".$LatLng."\">".$title."</option>\n";
		}
		echo $options;

	} else{ // json
		$json = file_get_contents("http://localhost/biziWS/api/estacionesJson");
		$result = json_decode($json, true);
		$estaciones = $result['estaciones'];
		$options ="";
		foreach($estaciones as $estacion){
			$title = $estacion['title'];
			$LatLng = $estacion['latitude'].",".$estacion['longitude'];
			$options .= "<option value=\"".$LatLng."\">".$title."</option>\n";
		}
		echo $options;
	}
	
?>
