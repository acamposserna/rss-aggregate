<?php
	// Número máximo de noticas a mostrar de cada medio
	define('MAX_NOTICIAS', 5);
    $numero_noticias = MAX_NOTICIAS;
	
	// Zona horaria
	date_default_timezone_set('Europe/Madrid');
	
	// Formato de fecha y hora en español
	setlocale(LC_TIME, 'es_ES.UTF-8');
	
	// Array con las RSS de los medios
    $medios_json = file_get_contents('medios.json');
    $medios_por_tipo = json_decode($medios_json, true);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="title" content="Agregador de noticias">
    <meta name="description" content="Agregador de fuentes RSS">

    <title>Agregador de noticias</title>
    <link href="style.css" type="text/css" rel="stylesheet">
    <script src="funciones.js"></script>
</head>
<body onload="obtenerEstado()">
<div class="content">
    <h1>Filtros</h1>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" onsubmit="guardarEstado()">
        <table>
            <tr><td>
                <input type="checkbox" id="todosOff" name="todos" value="off" hidden>
                <input type="checkbox" id="todos" name="todos" value="on" onclick="cambiaTodos(this)" checked><label for="todos"> Todos</label><br>
                <?php foreach($medios_por_tipo as $tipo => $medios) { ?>
                    <input type="checkbox" id="<?=$tipo;?>Off" name="<?=$tipo;?>" value="off" hidden>
                    <input type="checkbox" id="<?=$tipo;?>" name="<?=$tipo;?>" value="on" onclick="cambioEstado(this)" checked><label for="<?=$tipo;?>"> <?=ucfirst($tipo);?></label>:
                    <?php foreach($medios as $medio) { ?>
                        <a href="<?=$medio['url'];?>" target="_blank"><?=$medio['nombre'];?></a> |
                <?php
                    }
                    echo "<br/>";
                }
                ?>
            </td><td>
                Número de noticias: <input type="number" name="numero" id="numero" min="1" max="10" value="<?=$numero_noticias?>" />
            </td></tr>
            <tr><td colspan="2">
                <input type="submit" value="Enviar">
            </td></tr>
        </table>
    </form>
    <h1>Resumen de noticias</h1>
<?php
    // Comprobamos si se ha pulsado el botón "submit"
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $categorias = [];
        $numero_noticias = $_POST['numero'] ?? MAX_NOTICIAS; // Número máximo de noticias a mostrar

        // Obtenemos las categorías a mostrar
        foreach ($_POST as $item => $value) {
            if ($value == "on") {
                $categorias[$item] = $value;
            }
        }

        // Filtramos los medios de las categorias seleccionadas
        $medios_on = array_intersect_key($medios_por_tipo, $categorias);
    }

    // Recorremos el array de medios seleccionados
	foreach ($medios_on as $tipo => $medios) {
        // Array para almacenar las noticias de todas las fuentes
        $feed = [];

        echo "<h2><u>" . ucfirst($tipo) . "</u></h2>";

        // Recorremos el array de medios de la categoría actual
        foreach ($medios as $medio) {
            // Obtenemos el fichero RSS del medio
    		if(@simplexml_load_file($medio['rss'])){
	    		$rss = simplexml_load_file($medio['rss']);
		    }
		
		    // Si obtenemos datos los procesamos
		    $i = 1;
		    if(!empty($rss)){
			
			// Datos de la fuente RSS
			$fuente = [];
			$fuente["medio"] = $rss->channel->title;
			$fuente["enlace"] = $rss->channel->link;

			// Procesamos las noticias recuperadas
			foreach ($rss->channel->item as $item) {
				
				$noticia = [];
				$noticia['titulo'] = $item->title;
				$noticia['enlace'] = $item->link;
				$noticia['texto'] = strip_tags($item->description);
				$noticia['fecha'] = ucfirst(strftime('%A, %e de %B de %Y',strtotime($item->pubDate)));
				
				$fuente['noticias'][] = $noticia;

				if($i >= $numero_noticias) break;

				$i++;
			}
			
			$feed[] = $fuente;
		}

        // Si hemos encontrado los RSS de las fuentes los procesamos
        if(!empty($feed)) {

            // Recorremos todas las fuentes RSS
            foreach($feed as $fuente) {
                echo "<h3>".$fuente["medio"]."</h3>";

                $noticias = $fuente["noticias"];

                // Si la fuente tiene noticias las procesamos
                if(!empty($noticias)) {

                    // Recorremos las noticias de la fuente
                    foreach($noticias as $noticia) {
                        // Mostramos la noticia
                    ?>
                        <div class="post">
                            <div class="post-head">
                                <h3><a href="<?php echo $noticia['enlace']; ?>" target="_blank"><?php echo $noticia['titulo']; ?></a></h3>
                                <span><?php echo $noticia['fecha']; ?></span>
                            </div>
                            <div class="post-content">
                                <?php echo implode(' ', array_slice(explode(' ', $noticia['texto']), 0, 20)) . "..."; ?> <a href="<?php echo $noticia['enlace']; ?>">Leer más</a>
                            </div>
                        </div>
                    <?php
                        }
                    }
                    else {
                        echo "<h3>No se han encontrado noticias</h3>";
                    }
                }
            }
            else {
                echo "<h2>No se han encontrado noticias</h2>";
            }
        }
    }
?>
</div>
</body>
</html>