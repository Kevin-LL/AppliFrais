<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Refuser la fiche de frais du mois <?php echo $numAnnee."-".$numMois.' | Visiteur : '.$infosUtil['id'].' '.$infosUtil['nom'];?></h3>
</div>
<div id="contenuList">
	<div id="refuserFiche">
		<form id="refuser" method="post" action="<?php echo base_url('c_comptable/refuFiche/'.$infosUtil['id'].'/'.$numAnnee.$numMois);?>">
			<div class="formList">
				<?php
					if (isset($leMotifRefus['motifRefus']))
					{
						if ($leMotifRefus['motifRefus'] != NULL)
						{
							echo
							'<p>
								<label class="formLabel">Ancien motif :</label>
								<span class="refus">'.$leMotifRefus['motifRefus'].'</span>
							</p>';
						}
					}
				?>
				<p>
					<label class="formLabel" for="txtMotifRefus">Nouveau motif :</label>
					<input id="txtMotifRefus" class="input" name="motifRefus" required="required" size="40" maxlength="180" value="" type="text"/>
				</p>
				<p class="formButtonsArea">
					<input class="button" id="okRefus" value="Refuser" size="20" type="submit"/><input class="button" id="annulerRefus" value="Annuler" size="20" type="reset"/>
				</p>
			</div>
		</form>
	</div>
</div>