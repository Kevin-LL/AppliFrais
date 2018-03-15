<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Liste de mes fiches de frais</h3>
</div>
<div id="contenuList">
	<?php 
		if (isset($notifyInfo)){ 
			echo
			'<div class="notify info">
				'.$notifyInfo.'
				<span onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
	?>
	<div id="fichesFrais">
		<table class="listeLegere">
			<thead>
				<tr>
					<th>Mois</th>
					<th>Etat</th>
					<th>Montant</th>
					<th>Date modif.</th>
					<th colspan="2">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ($mesFiches as $uneFiche){
						$action1 = '';
						$action2 = '';
						$motifRefusLink = '';
						$status = '';

						if ($uneFiche['id'] == 'CR' || $uneFiche['id'] == 'RE'){
							$action1 = anchor('c_visiteur/modFiche/'.$uneFiche['mois'], 'Modifier', 'class="anchorCell" title="Modifier la fiche"');
							$action2 = anchor('c_visiteur/signeFiche/'.$uneFiche['mois'], 'Signer', 'class="anchorCell" title="Signer la fiche" onclick="return confirm(\'Voulez-vous vraiment signer cette fiche ?\');"');
							if ($uneFiche['id'] == 'RE'){
								$status = ' invalid';
								$motifRefusLink = ',&nbsp;'.anchor('c_visiteur/voirMotifRefus/'.$uneFiche['mois'], 'voir le motif', 'class="anchorText" title="Consulter le motif de refus"');
							}
						}elseif ($uneFiche['id'] == 'CL' || $uneFiche['id'] == 'VA' || $uneFiche['id'] == 'RB'){
							$action1 = anchor('c_visiteur/impFiche/'.$uneFiche['mois'], 'Imprimer', 'class="anchorCell" title="Imprimer la fiche"');
							$action2 = '';
							if ($uneFiche['id'] == 'VA' || $uneFiche['id'] == 'RB'){
								$status = ' valid';
							}
						}elseif ($uneFiche['id'] == 'IN'){
							$action1 = anchor('c_visiteur/supprFiche/'.$uneFiche['mois'], 'Supprimer', 'class="anchorCell" title="Supprimer la fiche" onclick="return confirm(\'Voulez-vous vraiment supprimer cette fiche ?\');"');
							$action2 = '';
							$status = ' invalid';
						}
						
						echo 
						'<tr>
							<td class="anchorData alignCenter" data-th="Mois">'.anchor('c_visiteur/voirFiche/'.$uneFiche['mois'], $uneFiche['mois'] = substr_replace($uneFiche['mois'], '-', 4, 0), 'class="anchorCell" title="Consulter la fiche"').'</td>
							<td class="textData alignLeft'.$status.'" data-th="Etat">'.$uneFiche['libelle'].$motifRefusLink.'</td>
							<td class="textData alignRight'.$status.'" data-th="Montant">'.$uneFiche['montantValide'].'€</td>
							<td class="textData alignCenter'.$status.'" data-th="Date modif.">'.$uneFiche['dateModif'].'</td>
							<td class="anchorData alignCenter" data-th="Action">'.$action1.'</td>
							<td class="anchorData alignCenter" data-th="Action">'.$action2.'</td>
						</tr>';
					}
				?>
			</tbody>
		</table>
	</div>
</div>