<?php
	// Número máximo de noticas a mostrar de cada medio
	define('MAX_NOTICIAS', 5);
	
	// Zona horaria
	date_default_timezone_set('Europe/Madrid');
	
	// Formato de fecha y hora en español
	setlocale(LC_TIME, 'es_ES.UTF-8');
	
	// Array con las RSS de los medios
	$urls = [
		"https://feeds.elpais.com/mrss-s/pages/ep/site/elpais.com/portada",
		"https://e00-elmundo.uecdn.es/elmundo/rss/portada.xml"
	];
	
	// Array para almacenar las noticias de todas las fuentes
	$feed = [];
	
	// Recorremos el array de RSS
	foreach($urls as $url) {
		
		// Obtenemos el fichero RSS
		if(@simplexml_load_file($url)){
			$rss = simplexml_load_file($url);
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
				$noticia['texto'] = $item->description;
				$noticia['fecha'] = ucfirst(strftime('%A, %e de %B de %Y',strtotime($item->pubDate)));
				
				$fuente['noticias'][] = $noticia;

				if($i >= MAX_NOTICIAS) break;

				$i++;
			}
			
			$feed[] = $fuente;
		}
	}
?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        
        <title>Noticias</title>
        <link href="style.css" type="text/css" rel="stylesheet">
    </head>
    <body>
        <div class="content">
			<h1>Resumen de noticias</h1>
		<?php
			// Si hemos hemos encontrado los RSS de las fuentes los procesamos
			if(!empty($feed)) {
				
				// Recorremos todas las fuentes RSS
				foreach($feed as $fuente) {
					
					echo "<h2>".$fuente["medio"]."</h2>";
					
					$noticias = $fuente["noticias"];
					
					// Si la fuente tiene noticias las procesamos
					if(!empty($noticias)) {
						
						// Recorremos las noticias de la fuente
						foreach($noticias as $noticia) {
							// Mostramos la noticia
		?>
							<div class="post">
								<div class="post-head">
									<h2><a href="<?php echo $noticia['enlace']; ?>" target="_blank"><?php echo $noticia['titulo']; ?></a></h2>
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
		?>
		</div>
    </body>
</html>
