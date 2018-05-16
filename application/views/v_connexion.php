<?php
	$this->load->helper('url');
	$path = base_url();
?>
<div id="contenuTitre">
	<h3>Identification utilisateur</h3>
</div>
<div id="contenuList">
	<?php
		if (isset($erreur))
		{
			echo
			'<div class="notify error">
				'.$erreur.'
				<span onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
	?>
	<div id="connexion">
		<form method="post" action="<?php echo $path.'c_default/connecter';?>">
			<div class="formList">
				<p>
					<label for="login" class="formLabel">Login*</label>
					<input id="login" class="input" name="login" required="required" maxlength="31" type="text"/>
				</p>
				<p>
					<label for="mdp" class="formLabel">Mot de passe*</label>
					<input id="mdp" class="input" name="mdp" required="required" maxlength="60" type="password"/>
				</p>
				<p class="formButtonsArea">
					<input class="button" name="valider" value="Valider" type="submit"/>
					<input class="button" name="annuler" value="Annuler" type="reset"/> 
				</p>
			</div>
		</form>
	</div>
	<div id="w3c">
		<img src="<?php echo img_url('html5valid.png');?>" alt="The W3C Markup Validation Service" title="The W3C Markup Validation Service">
		<img src="<?php echo img_url('css3valid.png');?>" alt="The W3C CSS Validation Service" title="The W3C CSS Validation Service">
	</div>
</div>