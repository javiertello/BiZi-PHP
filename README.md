# Aplicación PHP de seguimiento de estaciones BiZi

Aplicación Web PHP que lista en un mapa las estaciones BiZi de Zaragoza, así como la posibilidad de llegar a una estación desde una estación determinada, pintando la ruta en el mapa. Interactúa con la API Javascript de Google Maps y descarga los datos del repositorio del ayuntamiento de Zaragoza. Muestra también la predicción meteorológica de los 3 siguientes días.

Antes de ejecutarlo, es necesario lanzar el WS SOAP de predicción( [Servicio SOAP de predicción](https://github.com/javiertello/PrediccionSOAP) ) y lanzarlo en [http://localhost:8080](http://localhost:8080) (En un contenedor de aplicaciones Tomcat, haciendo uso de [Apache Axis](http://axis.apache.org/).

Para desplegarlo, copiar el contenido del repositorio en un contenedor que soporte PHP (Apache por ejemplo).
La aplicación Web estará disponible en [http://localhost/seguimiento-Bizi](http://localhost/seguimiento-Bizi)

También contiene control estadístico de la aplicación (Almacena las acciones en Parse). Está disponible en: [http://localhost/control-estadistico](http://localhost/control-estadistico)

Captura de la aplicación:
![Captura App](http://https://github.com/javiertello/BiZi-PHP/blob/master/captura_final.png)
