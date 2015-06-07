<?php
	include('htmldom/simple_html_dom.php');

	/*
	* Devuelve una lista de opciones: "<option value="XXXXX">Yyyyyyy</option>"
	* con los municipios de Zaragoza, a partir de la web del AEMET.
	*/
	function listar(){
		$html = file_get_html('http://www.aemet.es/es/eltiempo/prediccion/municipios?p=50&w=t');
		$aes = $html->find('table tbody tr td a');
		$options ="";
		foreach($aes as $a){
			$stringid = $a -> href;
			$id = explode("-id", $stringid)[1];
			$nombre = $a -> plaintext;

			if(strcmp($id,"50297")==0){ // Zaragoza selected
				$options .= "<option selected=\"selected\" value=\"".$id."\">".$nombre."</option>\n";
			}else{
				$options .= "<option value=\"".$id."\">".$nombre."</option>\n";
			}

		}
		echo $options;
	}
	
?>
