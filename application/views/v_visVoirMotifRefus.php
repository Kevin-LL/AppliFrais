<div id="contenuTitre">
	<h3>Refus de ma fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0);?></h3>
</div>
<div id="contenuList">
	<div id="motifRefus">
		<p>	
			<?php
				if (isset($leMotifRefus))
				{
					if ($leMotifRefus != null)
					{
						echo '<span class="refus">'.$leMotifRefus.'</span>';
					}
					else
					{
						echo 'Aucun motif de refus disponible.';
					}
				}
			?>
		</p>
	</div>
</div>