
	/*
	 * Asigna el formato xml o json en funcion del fichero "biziConfig.txt"
	 */
	var formato = ""; // json o xml

	function setFormato(data){
		formato = data.trim();
	}
	$.get(
		"biziConfig.txt",
		function(data) {
		   setFormato(data);
		}
	);


	/*
	 * Dado un xml de estaciones, devuelve un Array de objetos Javascript que 
	 * contiene las mismas.
	 */
	function crearEstaciones (xml){
		parser=new DOMParser();
	xmlDoc=parser.parseFromString(xml,"text/xml");
		var estaciones = new Array();
		$('estacion', xmlDoc).each(function(){
          var id = $('id', this).text();
          var title = $('title', this).text();
					var disp = $('disponibles', this).text();
					var anclajes = $('anclajes', this).text();
					var lat = $('latitude', this).text();
					var long = $('longitude', this).text();
					
					var estacion = { "id" : id,
													 "title" : title,
													 "disponibles" : disp,
													 "anclajes" : anclajes,
													 "latitude" : lat,
													 "longitude" : long
													};
					estaciones.push(estacion);
      });

			return estaciones;

		}


		/*
		 * Añade los markers al mapa
		 */
		function aniadirMarkers(){
			var url;
			if(formato == "json"){
				url = "http://localhost/biziWS/api/estacionesJson";
			} else { // xml
				url = "http://localhost/biziWS/api/estacionesXML";
			}
			$.get(url, function(data){

				var estaciones;

				if(formato == "json"){
					estaciones = JSON.parse(data).estaciones;
				} else { // xml
					// Valido el XML llamando a validarXML.php
				  $.ajax({ url: 'validarXML.php',
		        data: {'xml': data},
		        type: 'post',
						async: false,
		        success: function(output) {
		        	if(output=="true"){
									estaciones = crearEstaciones(data);
							}else{
								alert("Documento XML de biziWS no es valido");
							}
		        }
				  });
					
				}

				$.each(estaciones, function(key, estacion) {
					var id = estacion.id;
					var nombre = estacion.title;
					var disp = estacion.disponibles;
					var anclajes = estacion.anclajes;
					var lat = estacion.latitude;
					var long = estacion.longitude;
					var icono = 'iconos/conbicis.png';
					if(disp==0){
						icono = 'iconos/sinbicis.png';
					} else if(anclajes==0){
						icono = 'iconos/sinanclajes.png';
					}
					var titulo = id+" - "+nombre;
					var marker = new google.maps.Marker({
			  		position: new google.maps.LatLng(long,lat),
			 			map: map,
						icon: icono,
			 		  title: titulo
	 			  });
					var contenido = "<span style=\"color:red;\">"+titulo+"</span></br></br>"+
													"<b>Aparcamientos: </b>"+anclajes+"</br>"+
													"<b>Bicis disponibles: </b>"+disp+"";
					marker.info = new google.maps.InfoWindow({
						content: contenido
					});

					google.maps.event.addListener(marker, 'click', function() {
						marker.info.open(map, marker);
						accionSeleccion(marker.title);
					});
				});

				
			});
			
		}

		var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();
		var map;
		/*
		 * Inicializa el mapa de Google
		 */
		function initialize() {
			directionsDisplay = new google.maps.DirectionsRenderer();
			var mapProp = {
				center:new google.maps.LatLng(41.6577211,-0.8770566),
				zoom:13
			};
			map = new google.maps.Map(document.getElementById("googleMap"),mapProp);

			aniadirMarkers();

			directionsDisplay.setMap(map);
		}
		google.maps.event.addDomListener(window, 'load', initialize);

		/*
		 * Calcula una ruta y la pinta en el mapa
		 */
		function calcRoute(origen,latlongdest, mode) {
			var start = origen;
			var array = latlongdest.split(",");
			var end = new google.maps.LatLng(array[1],array[0]);
			var tmode;
			if(mode=="WALKING"){
				tmode=google.maps.TravelMode.WALKING;
			}else if(mode=="DRIVING"){
				tmode=google.maps.TravelMode.DRIVING;
			}else if(mode=="BICYCLING"){
				tmode=google.maps.TravelMode.BICYCLING;
			}else if(mode=="TRANSIT"){
				tmode=google.maps.TravelMode.TRANSIT;
			}
			var request = {
				origin:start,
				destination:end,
				travelMode: tmode
			};
			directionsService.route(request, function(result, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					directionsDisplay.setDirections(result);
				}
			});
		}
