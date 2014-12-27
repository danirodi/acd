<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<link rel="stylesheet" type="text/css" href="style/main.css"/>
	<link href="style/icon_16.png" rel="icon" />
	<link href="style/icon_128.png" sizes="128x128" rel="icon" />
	<title>Administración estructuras</title>
</head>
<body>
<header>
	<div id="header-menu">
		<a href="index.php" class="back"><img src="style/ic_chevron_left_24px_inverse.svg" alt="Menu" height="30"/><span class="label"> back</span></a>
	</div>
	<h1>ACD</h1>
</header>
<h2>Administración <span id="structure_name"><?=htmlspecialchars($titleName)?></span></h2>
<p><em>Error</em>: No se ha podido recuperar la estructura.</p>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>