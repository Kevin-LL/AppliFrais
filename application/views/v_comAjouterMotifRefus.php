<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Refuser la fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0).' | Visiteur : '.$infosUtil['id'].' '.$infosUtil['nom'];?></h3>
</div>
<div id="contenuList">
	<div id="refuserFiche">
		<form method="post" action="<?php echo base_url('c_comptable/refuFiche/'.$infosUtil['id'].'/'.$moisFiche);?>">
			<div class="formList">
				<?php
					if (isset($leMotifRefus))
					{
						if ($leMotifRefus != null)
						{
							echo
							'<p>
								<label class="formLabel">Ancien motif :</label>
								<span class="refus">'.$leMotifRefus.'</span>
							</p>';
						}
					}
				?>
				<p>
					<label class="formLabel" for="txtMotifRefus">Nouveau motif :</label>
					<input id="txtMotifRefus" class="input" name="motifRefus" required="required" size="40" maxlength="180" value="" type="text"/>
				</p>
				<p class="formButtonsArea">
					<input id="okRefuser" class="button" value="Refuser" type="submit"/><input id="annulerRefuser" class="button" value="Annuler" type="reset"/>
				</p>
			</div>
		</form>
	</div>
</div>