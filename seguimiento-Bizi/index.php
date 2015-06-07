<html>
	<head>
		<title>Sistema de seguimiento de estaciones Bizi de Zaragoza</title>
		
		<script>
			// Comprueba conexion a internet
			function comprobarConex(){
				if(!navigator.onLine){
					alert("No hay conexion a internet");
					window.location.assign("http://localhost/seguimiento-Bizi/sinconexion.html");
				}
			}
			comprobarConex();
		</script>
		<script type="text/javascript" src="jquery-2.1.4.min.js"></script>
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<script src="http://maps.googleapis.com/maps/api/js?key=APIKEY"></script>
		<script src="map.js"></script>
		<script src="parse.js"></script>
		<script>
			/*
			 * Llama a la funcion soap que devuelve la tabla de prediccion
			 */
			function llamadaSOAP (id, formato){
				var params = {
					"id" : id,
					"formato" : formato
				};
				$.ajax({
					data: params,
					url: 'clienteTiempoSOAP.php',
					type: 'post',
					beforeSend: function (response) {
						$("#tablaPredic").html("Procesando petición...</br></br></br></br></br>"+
															"</br></br></br></br></br></br></br></br></br></br></br>");
					},
					success: function (response) {
						$("#tablaPredic").html(response);
					}
				});
			}

		</script>

		<style type="text/css">
			.fondoa {
				background-color: #4F86D9;
				color: white;
			}
			.fondoac {
				background-color: #95B6E9;
				color: white;
			}
		</style>

	</head>
	<body>
			<div class="col-md-2 col-sm-2">
				<a href="http://localhost/control-estadistico"><ins>> Control estadístico</ins></a>
				<fieldset id="fielbizi">
					<legend><span style="color:red">Llegar a una estación Bizi</span></legend>
					</br>
				<form role="form" id="formBizi">
					<input class="form-control" type="text" id="dircomienzo" name="dircomienzo" placeholder="Dirección de comienzo"/>
					</br>
					Selecciona estación de Bizi:</br>
					<select class="form-control" name="estacion" id="estacion">
							<?php
								include('listarEstaciones.php');
						  ?>
					</select>
					</br>
					<input type="radio" name="modo" value="WALKING">Pie&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="modo" value="DRIVING" checked>Coche</br>
					<input type="radio" name="modo" value="TRANSIT">Transporte Público&nbsp;
					<input type="radio" name="modo" value="BICYCLING">Bici
					</br></br>
					<input type="button" class="btn btn-danger" href="javascript:;" 
						onclick="comprobarConex();
										 calcRoute($('#dircomienzo').val(),$('#estacion').val(), 
																$('input[name=modo]:checked', '#formBizi').val());
										 accionRuta($('#dircomienzo').val(),$('#estacion option:selected').text(),
																$('input[name=modo]:checked', '#formBizi').val());"
						 value="Buscar ruta"/>
				</form>
				</fieldset>
			</div>

			<div class="col-md-6 col-sm-6"></br>
				<div id="googleMap" style="width:650px;height:570px;"></div>
			</div>

			<div class="col-md-4 col-sm-4">
				<fieldset id="fieldpredic">
					<legend><span style="color:blue">Previsión Meteorológica</span></legend>
					</br>
					<div id="tablaPredic">
						<?php
							include('clienteTiempoSOAP.php');
						?>
					</div>

					<form role="form">
						Municipio: 
						<select class="form-control" name="idciudad" id="idciudad">
							<?php
								include('listarMunicipios.php');
								listar();
							?>
						</select></br>
						Formato:
						<select class="form-control" name="formato" id="formato">
							<option value="xml">xml</option>
							<option value="json">json</option>
						</select></br>
						<input class="btn btn-primary" type="button" href="javascript:;" 
						onclick="comprobarConex();
											llamadaSOAP($('#idciudad').val(), $('#formato').val());
										 accionTiempo($('#idciudad option:selected').text());return false;"
						 value="Obtener información meteorológica"/>
						<br>
					</form>

				</fieldset>
			</div>
	</body>
</html>
