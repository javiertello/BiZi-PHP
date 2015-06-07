<?php
/*
 * Valida el xml que recibe por post. Devuelve "true" si valido, "false" en caso contrario
 */

	if(isset($_POST['xml'])){
		$xml = $_POST['xml'];
		$dom = new DOMDocument;
		$dom->loadXML($xml);
		if (!($dom->validate())) {
		    echo "false";
		} else{
				echo "true";
		}
	} else{
		exit("Falta xml por post (validarXML.php)");
	}
?>
