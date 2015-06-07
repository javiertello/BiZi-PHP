			
			function leerDiarias(interaccion){

				var url;

				if(interaccion=="SOAP"){

					url = "http://localhost/seguimiento-Bizi/clienteParseSOAP.php";
					datos={
						"funcion" : "getPeticiones"
					};
					datatype='text';
					$.ajax({
						data: datos,
						dataType: datatype,
						url: url,
						type: 'post',
						success: function(data){ 
          	  crearDiario(data); 
      	    }
					});

				} else { // XML

					url = "http://localhost/parseWS/api/peticiones";
					$.get(url, function(data){
						crearDiario(data);
					});

				}
				
			}

			function crearDiario(data){

				var peticiones = JSON.parse(data).peticiones;
				var fechas = [];
				$.each(peticiones, function(key, peticion) {
					var accion = peticion.accion;
					var created = peticion.createdAt.date;
					var fecha = "" + created.split(" ",1);
					var esta = false;
					var index = 0;
					while(index < fechas.length && !esta){
						if(fechas[index].fecha==fecha){
							esta=true;
							if(accion.trim() == "ConsultarTiempo"){
								fechas[index].contadorTiempo++;
							}
							else if(accion.trim() == "SeleccionarEstacion"){
								fechas[index].contadorEstacion++;
							}
							else if(accion.trim() == "CalcularRuta"){
								fechas[index].contadorRuta++;
							}
						}
						index++;
					}
					if(!esta){
						fechas.push({"fecha":fecha,"contadorTiempo":0,"contadorEstacion":0,"contadorRuta":0});
					}	
				});
				
				var labels = listar(fechas,"fechas");
				var contTiempo = listar(fechas,"tiempo");
				var contEstacion = listar(fechas,"estacion");
				var contRuta = listar(fechas,"ruta");
				var num = 7;
				if(labels.length > num){
					labels = labels.slice(-num);
					contTiempo = contTiempo.slice(-num);
					contEstacion = contEstacion.slice(-num);
					contRuta = contRuta.slice(-num);
				}

				var data = {
					labels: labels,
					datasets:[
						{
							label: "Peticiones Meteorologicas",
							fillColor: "#F2C1A5",
							strokeColor: "#F7464A",
							pointColor: "#F7464A",
							pointStrokeColor: "#fff",
							pointHighlightFill: "#fff",
							pointHighlightStroke: "rgba(220,220,220,1)",
							data: contTiempo
						},
						{
							label: "Peticiones de Estaciones",
							fillColor: "rgba(146,187,205,0.2)",
							strokeColor: "#1321DB",
							pointColor: "#1321DB",
							pointStrokeColor: "#fff",
							pointHighlightFill: "#fff",
							pointHighlightStroke: "rgba(151,187,205,1)",
							data: contEstacion
						},
						{
							label: "Peticiones de Rutas",
							fillColor: "rgba(151,187,205,0.2)",
							strokeColor: "#4DFA09",
							pointColor: "#28F21A",
							pointStrokeColor: "#fff",
							pointHighlightFill: "#fff",
							pointHighlightStroke: "rgba(151,187,205,1)",
							data: contRuta
						}		
					]
				}
				var ctx = document.getElementById("myChart3").getContext("2d");
				var myLineChart = new Chart(ctx).Line(data);
				legend(document.getElementById("g3"), data);
			}

			function listar(fechas, que){
				var texto = [];
				if(que == "fechas"){
					for(var i=0;i<fechas.length;i++){
						texto.push(fechas[i].fecha);
					}
				}else if(que=="tiempo"){
					for(var i=0;i<fechas.length;i++){
						texto.push(fechas[i].contadorTiempo);
					}
				}else if(que=="estacion"){
					for(var i=0;i<fechas.length;i++){
						texto.push(fechas[i].contadorEstacion);
					}
				}else{
					for(var i=0;i<fechas.length;i++){
						texto.push(fechas[i].contadorRuta);
					}
				}
				return texto;
			}
