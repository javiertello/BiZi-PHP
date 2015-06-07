			function leerPeticiones(interaccion){
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
          	  crearPeticiones(data); 
      	    }
					});

				} else { // XML

					url = "http://localhost/parseWS/api/peticiones";
					$.get(url, function(data){
						crearPeticiones(data);
					});

				}
			}
	
			
			function crearPeticiones(data){
				var peticiones = JSON.parse(data).peticiones;
				var contadorTiempo = 0;
				var contadorEstacion = 0;
				var contadorRuta = 0;
				$.each(peticiones, function(key, peticion) {
					var accion = peticion.accion;
					if(accion.trim() == "ConsultarTiempo"){
						contadorTiempo++;
					}
					else if(accion.trim() == "SeleccionarEstacion"){
						contadorEstacion++;
					}
					else if (accion.trim() == "CalcularRuta"){
						contadorRuta++;
					}
				});
				var data = [{
						value: contadorTiempo,
						color: "#F7464A",
						highlight: "#FF5A5E",
						label: "Peticiones Meteorologicas"
					},
					{
						value: contadorEstacion,
						color: "#46BFBD",
						highlight: "#5AD3D1",
						label: "Peticiones de Estaciones"
					},
					{
						value: contadorRuta,
						color: "#FDB45C",
						highlight: "#FFC870",
						label: "Peticiones de Rutas"
					}
				]
				
				var ctx = document.getElementById("myChart1").getContext("2d");
				var myPieChart = new Chart(ctx).Pie(data,{
					animateScale: true
				});
				legend(document.getElementById('pie'), data);
			}
