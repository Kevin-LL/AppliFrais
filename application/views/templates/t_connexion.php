<?php
	$this->load->helper('url');
	$v_path = base_url('application/views');
?>
<!DOCTYPE HTML>
<html lang="fr">
	<head>
		<title>Intranet du Laboratoire Galaxy-Swiss Bourdin</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="icon" type="image/png" href="<?php echo img_url('favicon.png');?>">
		<link rel="stylesheet" type="text/css" href="<?php echo css_url('styles.css');?>">
		<script src="<?php echo js_url('functions.js');?>"></script>
	</head>
	<body>
		<header>
			<div id="logo">
				<?php
					$img = '<img src="'.img_url('logo.png').'" alt="Laboratoire Galaxy-Swiss Bourdin" title="Laboratoire Galaxy-Swiss Bourdin">';
					$path = 'c_default/';
					echo anchor($path, $img);
				?>
			</div>
			<div id="titre">
				<h1>Gestion des frais de déplacements</h1>
			</div>
		</header>
		<main>
			<div id="contenu">
				<?php echo $body;?>
			</div>
		</main>
		<footer>
			<div id="copyright">
				<h5>Copyright &copy; 2009-<?php echo date("Y");?> Laboratoire Galaxy-Swiss Bourdin</h5>
			</div>
		</footer> 
	</body>
</html> 