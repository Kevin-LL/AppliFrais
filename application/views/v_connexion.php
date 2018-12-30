<?php
	$this->load->helper('url');
?>
<div id="contenu-titre">
	<h3>Identification utilisateur</h3>
</div>
<div id="contenu-list">
	<?php
		if (isset($erreur))
		{
			echo
			'<div class="notify notify-error">
				<div>
					<h4>Une erreur est survenue !</h4>
					<ul>'.$erreur.'</ul>
				</div>
				<span class="notify-close" onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
	?>
	<div id="connexion">
		<form method="post" action="<?php echo base_url('c_default/connecter');?>">
			<div class="form-list">
				<p>
					<label class="form-label label-small" for="txt-login">Login*</label>
					<input id="txt-login" class="input input-medium" name="login" required="required" maxlength="31" value="" type="text"/>
				</p>
				<p>
					<label class="form-label label-small" for="txt-mdp">Mot de passe*</label>
					<input id="txt-mdp" class="input input-medium" name="mdp" required="required" maxlength="60" value="" type="password"/>
				</p>
				<p class="form-buttons-container">
					<input id="connexion-ok" class="button" value="Se connecter" type="submit"/><input id="connexion-annuler" class="button" value="Annuler" type="reset"/>
					<input name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" type="hidden"/>
				</p>
			</div>
		</form>
	</div>
	<div id="w3c">
		<img src="<?php echo img_url('html5valid.png');?>" alt="The W3C Markup Validation Service" title="The W3C Markup Validation Service">
		<img src="<?php echo img_url('css3valid.png');?>" alt="The W3C CSS Validation Service" title="The W3C CSS Validation Service">
	</div>
</div>