<?php
	$this->load->helper('security');
?>
<div id="contenuTitre">
	<h3>Refus de ma fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0);?></h3>
</div>
<div id="contenuList">
	<div id="motifRefus">
		<p>	
			<?php
				if ($leMotifRefus != null)
				{
					echo '<span class="refus">'.xss_clean($leMotifRefus).'</span>';
				}
				else
				{
					echo 'Aucun motif de refus disponible.';
				}
			?>
		</p>
	</div>
</div>