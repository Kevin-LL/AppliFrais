<div id="contenuTitre">
	<h3>Refus de ma fiche de frais du mois <?php echo $numAnnee."-".$numMois;?></h3>
</div>
<div id="contenuList">
	<div id="motifRefus">
		<p>	
			<?php
				if (isset($leMotifRefus['motifRefus']))
				{
					if ($leMotifRefus['motifRefus'] != NULL)
					{
						echo '<span class="refus">'.$leMotifRefus['motifRefus'].'</span>';
					}
					else
					{
						echo 'Erreur : aucun motif de refus disponible.';
					}
				}
			?>
		</p>
	</div>
</div>