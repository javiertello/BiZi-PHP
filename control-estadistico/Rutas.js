
		function leerRutas(interaccion){

				var url;

				if(interaccion=="SOAP"){

					url = "http://localhost/seguimiento-Bizi/clienteParseSOAP.php";
					datos={
						"funcion" : "getAccionesRuta"
					};
					datatype='text';
					$.ajax({
						data: datos,
						dataType: datatype,
						url: url,
						type: 'post',
						success: function(data){ 
          	  crearRuta(data); 
      	    }
					});

				} else { // XML

					url = "http://localhost/parseWS/api/accionesRuta";
					$.get(url, function(data){
						crearRuta(data);
					});

				}
				
		}

		function crearRuta(data){
			
			var acciones = JSON.parse(data).accionesRuta;
			var rutas = [];
			$.each(acciones, function(key, ruta) {
				var destino = ruta.estacion;
				var esta=false;
				var i = 0;
				while(!esta && i<rutas.length){
					if(rutas[i].nombre == destino){
						rutas[i].contador++;
						esta=true;
					}
					i++;
				}
				if(!esta){
					rutas.push({"nombre": destino, "contador": 1});
				}
			});
			rutas.sort(function(a,b){
				if(a.contador < b.contador)
					return 1;
				else if(a.contador > b.contador)
					return -1;
				return 0;		
			});
			rutas=rutas.slice(0,5);
			var labels = listarR(rutas,"etiquetas");
			var datos = listarR(rutas,"datos");
			var data = {
				labels: labels,
				datasets:[
					{
						label: "Cantidad de peticiones",
						fillColor: "#0000FF",
						strokeColor: "#2EFE2E",
						highlightFill: "rgba(220,220,220,0.75)",
						data: datos
					}
				]
			};
			var ctx = document.getElementById("myChart4").getContext("2d");
			var myBarChart = new Chart(ctx).Bar(data);
			legend(document.getElementById('g4'), data);
		}


		function listarR(rutas, que){
				var texto = [];
				if(que == "etiquetas"){
					for(var i=0;i<rutas.length;i++){
						texto.push(rutas[i].nombre);
					}
				}else{
					for(var i=0;i<rutas.length;i++){
						texto.push(rutas[i].contador);
					}
				}
				return texto;
		}
