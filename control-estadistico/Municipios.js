			
			function leerMunicipios(interaccion){
				var url;

				if(interaccion=="SOAP"){

					url = "http://localhost/seguimiento-Bizi/clienteParseSOAP.php";
					datos={
						"funcion" : "getAccionesTiempo"
					};
					datatype='text';
					$.ajax({
						data: datos,
						dataType: datatype,
						url: url,
						type: 'post',
						success: function(data){ 
          	  crearMunicipios(data); 
      	    }
					});

				} else { // XML

					url = "http://localhost/parseWS/api/accionesTiempo";
					$.get(url, function(data){
						crearMunicipios(data);
					});

				}
			}



			function crearMunicipios(data){

				var acciones = JSON.parse(data).accionesTiempo;
				var municipios = [];
				$.each(acciones, function(key, accion) {
					var municipio = accion.ciudad;
					var esta=false;
					var i = 0;
					while(!esta && i<municipios.length){
						if(municipios[i].nombre == municipio){
							municipios[i].contador++;
							esta=true;
						}
						i++;
					}
					if(!esta){
						municipios.push({"nombre": municipio, "contador": 1});
					}
				});
				municipios.sort(function(a,b){
					if(a.contador < b.contador)
						return 1;
					else if(a.contador > b.contador)
						return -1;
					return 0;		
				});
				municipios=municipios.slice(0,10);
				/*for(var i=0;i<municipios.length;i++){
					document.write(municipios[i].nombre);
					document.write(":");
					document.write(municipios[i].contador);
				}*/
				var labels = listarMuni(municipios,"etiquetas");
				var datos = listarMuni(municipios,"datos");
				var data = {
					labels: labels,
					datasets:[
						{
							label: "Número de peticiones",
							fillColor: "#F7464A",
							strokeColor: "#46BFBD",
							highlightFill: "rgba(220,220,220,0.75)",
							data: datos
						}
					]
				};
				var ctx = document.getElementById("myChart2").getContext("2d");
				var myBarChart = new Chart(ctx).Bar(data);
				legend(document.getElementById('g2'), data);

			}


			function listarMuni(municipios, que){
				var texto = [];
				if(que == "etiquetas"){
					for(var i=0;i<municipios.length;i++){
						texto.push(municipios[i].nombre);
					}
				}else{
					for(var i=0;i<municipios.length;i++){
						texto.push(municipios[i].contador);
					}
				}
				return texto;
			}
