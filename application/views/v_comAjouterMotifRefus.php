<?php
	$this->load->helper('url');
	$this->load->helper('security');
?>
<div id="contenu-titre">
	<h3>Refuser la fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0).' | Visiteur : '.$infosUtil['id'].' '.$infosUtil['nom'];?></h3>
</div>
<div id="contenu-list">
	<?php
		if (isset($notifyError))
		{
			echo
			'<div class="notify notify-error">
				<div>
					<h4>Une erreur est survenue !</h4>
					<ul>'.$notifyError.'</ul>
				</div>
				<span class="notify-close" onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
	?>
	<div id="fiche-frais-refuser">
		<form method="post" action="<?php echo base_url('c_comptable/refuFiche');?>">
			<div class="form-list">
				<?php
					if ($leMotifRefus != null)
					{
						echo
						'<p>
							<label class="form-label label-small">Ancien motif :</label>
							<span class="refus">'.xss_clean($leMotifRefus).'</span>
						</p>';
					}
				?>
				<p>
					<label class="form-label label-small" for="txt-new-motif">Nouveau motif*</label>
					<input id="txt-new-motif" class="input input-large" name="motifRefus" required="required" maxlength="180" value="" type="text"/>
				</p>
				<p class="form-buttons-container">
					<input id="refuser-ok" class="button" value="Refuser" type="submit"/><input id="refuser-annuler" class="button" value="Annuler" type="reset"/>
					<input name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" type="hidden"/>
				</p>
			</div>
		</form>
	</div>
</div>