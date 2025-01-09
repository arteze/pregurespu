<?php
$n="pregurespu";
function obtener_preguntas($n) {
	$array = [];
	foreach (glob("$n/*.json") as $archivo) {
		$$n = json_decode(file_get_contents($archivo), true);
		array_push($array, $$n);
	}
	return $array;
}
function obtener_una_pregunta($ts,$n) {
	return json_decode(file_get_contents("$n/$n$ts.json"), true);
}
function obtener_una_respuesta($ts,$n) {
	return json_decode(file_get_contents("$n/$n$ts.json"), true);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$accion = $_GET['accion'] ?? '';
	$tipo = $_GET['tipo'] ?? '';
	$ts = $_GET['ts'] ?? '';
	switch ($accion) {
		case 'leer':
			switch ($tipo) {
				case 'preguntas':
					$json = obtener_preguntas("$n");
					echo json_encode($json);
					exit;
					break;
				case 'una_pregunta':
					$json = obtener_una_pregunta($ts,"$n");
					echo json_encode($json);
					exit;
					break;
				case 'una_respuesta':
					$json = obtener_una_respuesta($ts,"$n");
					echo json_encode($json);
					exit;
					break;
				case "$n":
					$preguntas = obtener_preguntas();
					$respuesta = obtener_una_respuesta($ts,$n);
					echo json_encode($json);
					exit;
					break;
			}
		case 'escribir':
			$pregunta = $_GET['pregunta'] ?? '';
			$respuesta = $_GET['respuesta'] ?? '';

			// Crear la carpeta si no existe
			if (!is_dir("$n")) {
				mkdir("$n", 0755);
			}

			$ts = time();

			// Generar nombre de archivo
			$nombre_archivo = "$n" . time() . '.json';

			// Crear un array con los datos de la pregunta
			$datos_pregunta = [
				'pregunta' => $pregunta
				,'respuesta' => $respuesta
				,'ts' => $ts
			];

			// Codificar los datos en formato JSON
			$json_data = json_encode($datos_pregunta);

			// Escribir el contenido en un archivo JSON
			$resultado = file_put_contents("$n/$nombre_archivo", $json_data);

			echo json_encode([
				'ts' => $resultado ? "$ts" : "Error al guardar"
			]);
			exit;
			break;
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title><?php echo "$n"; ?></title>
	<meta charset="UTF-8">
</head>
<body>
	<div id="form">
		<a for="pregunta">Pregunta:</a>
		<input type="text" id="pregunta">
		<a for="respuesta">Respuesta:</a>
		<textarea id="respuesta"></textarea>
		<button type="button" onclick="enviarPregunta()">Guardar</button>
	</div>
	<div style="display: flex;">
		<div style="width: 30%; border: 1px solid black; background-color: #8887" id="dp"></div>
		<div style="width: 70%; border: 1px solid black; background-color: #bbb7" id="dr"></div>
	</div>
</body>
	<script>
function ver_una_respuesta(e) {
	var o = e.target
	obtener_<?php echo "$n"; ?>( "leer", "una_respuesta", {ts: e.target.id} )
}
function crearDiv( elem, ts , texto , color ) {
	var div = document.createElement('div');
	var enlace = document.createElement('a');
	enlace.id = ts;
	enlace.textContent = texto;
	div.style.backgroundColor = color;
	if( elem == "dp" ){ enlace.href = "#"; }
	enlace.addEventListener("click", function(e) {
	  ver_una_respuesta(e);
	});
	div.appendChild(enlace);
	return div;
}

function mostrar_<?php echo "$n"; ?>(elem,ts,texto,color){
	var container = window[elem];
	var div = crearDiv( elem, ts, texto, color );
	var fp = document.createDocumentFragment();
	fp.appendChild(div);
	if( elem == "dr" ){ container.innerHTML = "" }
	container.appendChild(fp);
}
function obtener_<?php echo "$n"; ?>(accion,tipo,json) {
	var params = new URLSearchParams();
	var z = null
	params.append("accion",accion);
	params.append("tipo",tipo);
	if(json){
		z = json;
		z.ts && params.append("ts",z.ts );
		z.pregunta && params.append("pregunta",z.pregunta );
		z.respuesta && params.append("respuesta",z.respuesta );
	}
	fetch('index.php?' + params, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json'
		}
	}).then(response => {
		if (!response.ok) {
			throw new Error('Network response was not ok');
		}
		return response.json();
	}).then(x=>{
		if(accion=="leer"){
			if(tipo=="preguntas"){
				x.forEach( y => mostrar_<?php echo "$n"; ?>( "dp", y.ts, y.pregunta, "#ccce" ))
			}
			if(tipo=="una_pregunta"){
				mostrar_<?php echo "$n"; ?>( "dp", z.ts, z.pregunta, "#ccce" )
			}
			if(tipo=="una_respuesta"){
				mostrar_<?php echo "$n"; ?>( "dr", (x.ts), x.respuesta, "#ddde" )
			}
		}
		if(accion=="escribir"){
			x.ts && ( json.ts = x.ts )
			obtener_<?php echo "$n"; ?>("leer","una_pregunta",json)
		}
	})
}
function ver_las_preguntas(){
	obtener_<?php echo "$n"; ?>("leer","preguntas");
}
function enviarPregunta(pregunta){
	var json = {
		pregunta: window.pregunta.value
		, respuesta: window.respuesta.value
	}
	obtener_<?php echo "$n"; ?>("escribir","<?php echo "$n"; ?>",json)
}
window.i = 0
document.onreadystatechange = function(){
	++window.i
	if(	window.i==2){
		ver_las_preguntas()
	}
}
	</script>
</html>
