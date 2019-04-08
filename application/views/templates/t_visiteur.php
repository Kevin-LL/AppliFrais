<?php
	$this->load->helper('url');
?>
<!DOCTYPE HTML>
<html lang="fr">
	<head>
		<title>Intranet du Laboratoire Galaxy-Swiss Bourdin</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="icon" type="image/x-icon" href="<?php echo img_url('favicon.ico');?>">
		<link rel="stylesheet" type="text/css" href="<?php echo css_url('styles.css');?>">
		<link rel="stylesheet" type="text/css" href="<?php echo jquery_url('jquery-ui-1.12.1/jquery-ui.min.css');?>">
		<script src="<?php echo jquery_url('jquery-1.12.4.min.js');?>"></script>
		<script src="<?php echo jquery_url('jquery-ui-1.12.1/jquery-ui.min.js');?>"></script>
		<script src="<?php echo jquery_url('jquery-ui-1.12.1/datepicker-fr.js');?>"></script>
		<script src="<?php echo jquery_url('plugins/jquery-mask-plugin/src/jquery.mask.min.js');?>"></script>
		<script src="<?php echo js_url('functions.js');?>"></script>
	</head>
	<body>
		<header>
			<div id="logo">
				<?php
					$img = '<img src="'.img_url('logo.png').'" alt="Logo Galaxy-Swiss Bourdin" title="Laboratoire Galaxy-Swiss Bourdin">';
					$path = 'c_visiteur';
					echo anchor($path, $img);
				?>
			</div>
			<div id="titre">
				<h1>Gestion des frais de déplacements</h1>
			</div>
		</header>
		<main>
			<div id="menu-toggle">
				<input id="menu-toggle-button" value="Menu &#9776;" onclick="toggleMenu();" type="button"/>
			</div>
			<nav id="menu">
				<ul>
					<li>
						<ul id="utilisateur-nav">
							<li id="utilisateur-infos"><?php echo 'Visiteur :<br>'.$this->session->userdata('prenom').' '.$this->session->userdata('nom');?></li>
							<li><?php echo anchor('c_visiteur/monCompte', 'Mon compte', 'class="utilisateur-link" title="Consulter mon compte"');?></li>
							<li><?php echo anchor('c_visiteur/deconnecter', 'Se déconnecter', 'class="utilisateur-link utilisateur-deconnexion" title="Déconnexion"');?></li>
						</ul>
					</li>
					<li><?php echo anchor('c_visiteur', 'Accueil', 'class="menu-link" title="Page d\'accueil"');?></li>
					<li><?php echo anchor('c_visiteur/mesFiches', 'Mes fiches de frais', 'class="menu-link" title="Consulter mes fiches de frais"');?></li>
				</ul>
			</nav>
			<div id="contenu">
				<div id="vue">
					<?php echo $body;?>
				</div>
			</div>
		</main>
		<footer>
			<div id="block">
			</div>
			<div id="copyright">
				<p>Copyright &copy; 2009-<?php echo date("Y");?> Laboratoire Galaxy-Swiss Bourdin</p>
			</div>
		</footer>
	</body>
</html>