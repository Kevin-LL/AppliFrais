<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Détails de mon compte</h3>
</div>
<div id="contenuList">
	<?php
		if (isset($notifyInfo))
		{
			echo
			'<div class="notify info">
				'.$notifyInfo.'
				<span onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
		if (isset($notifyError))
		{
			echo
			'<div class="notify error">
				'.$notifyError.'
				<span onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
	?>
	<div id="generalUtilisateur">
		<fieldset>
			<legend>Général</legend>
			<div class="formList">
				<?php
					$id = $infosUtil['id'];
					$nom = $infosUtil['nom'];
					$prenom = $infosUtil['prenom'];
					$login = $infosUtil['login'];
					$dateEmbauche = $infosUtil['dateEmbauche'];
					
					echo
					'<p>
						<label class="formLabel">Groupe :</label>
						Visiteur
					</p>
					<p>
						<label class="formLabel">Identifiant :</label>
						'.$id.'
					</p>
					<p>
						<label class="formLabel">Nom :</label>
						'.$nom.'
					</p>
					<p>
						<label class="formLabel">Prénom :</label>
						'.$prenom.'
					</p>
					<p>
						<label class="formLabel">Login :</label>
						'.$login.'
					</p>
					<p>
						<label class="formLabel">Embauché le :</label>
						'.$dateEmbauche.'
					</p>';
				?>
			</div>
		</fieldset>
	</div>
	<div id="securiteUtilisateur">
		<form method="post" action="<?php echo base_url('c_visiteur/majSecurite');?>">
			<fieldset>
				<legend>Sécurité</legend>
				<div class="formList">
					<p>
						<label class="formLabel" for="txtCurrentMdp">Mot de passe actuel :</label>
						<input id="txtCurrentMdp" class="input" name="currentMdp" required="required" size="20" maxlength="60" value="" type="password"/>
					</p>
					<p>
						<label class="formLabel" for="txtNewMdp">Nouveau mot de passe :</label>
						<input id="txtNewMdp" class="input" name="newMdp" required="required" size="20" maxlength="60" value="" type="password"/>
					</p>
					<p class="formButtonsArea">
						<input id="okSecurite" class="button" value="Enregistrer" type="submit"/><input id="annulerSecurite" class="button" value="Annuler" type="reset"/>
					</p>
				</div>
			</fieldset>
		</form>
	</div>
	<div id="residenceUtilisateur">
		<form id="residence" method="post" action="<?php echo base_url('c_visiteur/majResidence');?>">
			<fieldset>
				<legend>Lieu de résidence</legend>
				<div class="formList">
					<?php
						$ville = $infosUtil['ville'];
						$cp = $infosUtil['cp'];
						$adresse = $infosUtil['adresse'];
						
						echo
						'<p>
							<label class="formLabel" for="txtVille">Ville :</label>
							<input id="txtVille" class="input" name="ville" required="required" size="20" maxlength="30" value="'.$ville.'" type="text"/>
						</p>
						<p>
							<label class="formLabel" for="txtCP">Code postal :</label>
							<input id="txtCP" class="input" name="cp" required="required" size="5" maxlength="5" value="'.$cp.'" oninput="checkCodePostal();" type="text"/>
						</p>
						<p>
							<label class="formLabel" for="txtAdresse">Adresse :</label>
							<input id="txtAdresse" class="input" name="adresse" required="required" size="20" maxlength="30" value="'.$adresse.'" type="text"/>
						</p>';
					?>
					<p class="formButtonsArea">
						<input id="okResidence" class="button" value="Enregistrer" onclick="return validCodePostal();" type="submit"/><input id="annulerResidence" class="button" value="Annuler" onclick="resetResidence();" type="button"/>
					</p>
				</div>
			</fieldset>
		</form>
	</div>
</div>