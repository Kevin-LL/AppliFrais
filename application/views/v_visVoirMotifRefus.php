<?php
	$this->load->helper('security');
?>
<div id="vue-titre">
	<h2>Refus de ma fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0);?></h2>
</div>
<div id="vue-contenu">
	<div id="motif-refus">
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