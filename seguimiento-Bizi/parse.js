			/*
			 * Le envia al WS de parse una accion "SeleccionarEstacion"
			 */

			var interaccion = ""; // REST o SOAP

			function setInteraccion(data){
				interaccion = data.trim();
			}
			$.get(
				"parseConfig.txt",
				function(data) {
				   setInteraccion(data);
				}
			);

			function accionSeleccion (estacion){
				var url = "";
				var datos;
				var datatype;
				if(interaccion == "REST"){
					url = 'http://localhost/parseWS/api/accionesSeleccion';
					datos=JSON.stringify({
						"estacion": estacion
					});
					datatype='json';

				} else { // SOAP
					url = 'clienteParseSOAP.php';
					datos={
						"funcion" : "postAccionesSeleccion",
						"estacion": estacion
					};
					datatype='text';
				}
				
				$.ajax({
					data: datos,
					dataType: datatype,
					url: url,
					type: 'post'
				});
				
			}

			/*
			 * Le envia al WS de parse una accion "ConsultarTiempo"
			 */
			function accionTiempo (ciudad){

				var url = "";
				var datos;
				var datatype;
				if(interaccion == "REST"){
					url = 'http://localhost/parseWS/api/accionesTiempo';
					datos=JSON.stringify({
						"ciudad": ciudad
					});
					datatype='json';

				} else { // SOAP
					url = 'clienteParseSOAP.php';
					datos={
						"funcion" : "postAccionesTiempo",
						"ciudad": ciudad
					};
					datatype='text';
				}
				
				$.ajax({
					data: datos,
					dataType: datatype,
					url: url,
					type: 'post'
				});
			}

			/*
			 * Le envia al WS de parse una accion "CalcularRuta"
			 */
			function accionRuta (dirInicio, estacion, modo){

				var url = "";
				var datos;
				var datatype;
				if(interaccion == "REST"){
					url = 'http://localhost/parseWS/api/accionesRuta';
					datos=JSON.stringify({
						"dirInicio": dirInicio,
						"estacion" : estacion,
						"modo" : modo
					});
					datatype='json';

				} else { // SOAP
					url = 'clienteParseSOAP.php';
					datos={
						"funcion" : "postAccionesRuta",
						"dirInicio": dirInicio,
						"estacion" : estacion,
						"modo" : modo
					};
					datatype='text';
				}
				
				$.ajax({
					data: datos,
					dataType: datatype,
					url: url,
					type: 'post'
				});
			}
