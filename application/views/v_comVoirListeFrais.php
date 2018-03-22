<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Consulter la fiche de frais du mois <?php echo $numAnnee."-".$numMois.' | Visiteur : '.$infosUtil['id'].' '.$infosUtil['nom'];?></h3>
</div>
<div id="contenuList">
	<div id="elementsForfaitises">
		<fieldset>
			<legend>Eléments forfaitisés</legend>
			<h4>Descriptif des éléments forfaitisés :</h4>
			<table class="listeLegere">
				<thead>
					<tr>
						<th>Libellé</th>
						<th>Quantité</th>
						<th>Montant</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($lesFraisForfait as $unFrais)
						{
							$idFrais = $unFrais['idfrais'];
							$libelle = $unFrais['libelle'];
							$quantite = $unFrais['quantite'];
							$montant = $unFrais['montant'];

							echo 
							'<tr>
								<td class="textData alignLeft" data-th="Libellé"><label for="montant'.$idFrais.'">'.$libelle.'</label></td>
								<td class="inputData alignLeft" data-th="Quantité"><input id="'.$idFrais.'" class="inputCell" name="lesFrais['.$idFrais.']" disabled="disabled" size="10" maxlength="3" value="'.$quantite.'" type="text"/></td>
								<td class="inputData alignRight" data-th="Montant"><input id="montant'.$idFrais.'" class="inputCell" name="lesMontants['.$idFrais.']" disabled="disabled" size="10" maxlength="6" value="'.$montant.'" type="text"/><span class="monnaie"></span></td>
								<td class="textData alignRight" data-th="Total"><label id="total'.$idFrais.'">'.number_format($quantite * $montant, 2).'€</label></td>
							</tr>';
						}
					?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div id="elementsHorsForfait">
		<fieldset>
			<legend>Elément(s) hors forfait</legend>
			<h4>Descriptif des éléments hors forfait :</h4>
			<table class="listeLegere">
				<thead>
					<tr>
						<th>Date</th>
						<th>Libellé</th>
						<th>Montant</th>
						<th>Justificatif [<?php echo $nbJustificatifs;?>]</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($lesFraisHorsForfait as $unFraisHorsForfait)
						{
							$id = $unFraisHorsForfait['id'];
							$idUtilisateur = $unFraisHorsForfait['idUtilisateur'];
							$mois = $unFraisHorsForfait['mois'];
							$date = $unFraisHorsForfait['date'];
							$libelle = $unFraisHorsForfait['libelle'];
							$montant = $unFraisHorsForfait['montant'];
							$justificatifNom = $unFraisHorsForfait['justificatifNom'];
							$justificatifFichier = $unFraisHorsForfait['justificatifFichier'];
							$idEtat = $unFraisHorsForfait['idEtat'];
							$libEtat = ' ['.$unFraisHorsForfait['libEtat'].']';
							$status = '';
								
							if (isset($justificatifFichier))
							{
								if ($justificatifFichier != NULL)
								{
									$justificatifNom = anchor('c_comptable/telJustificatif/'.$idUtilisateur.'/'.$mois.'/'.$id.'/'.$justificatifFichier, $justificatifNom, 'class="anchorText" title="Télécharger le justificatif" download');
								}
								else
								{
									$justificatifNom = 'Aucun';
								}
							}
							
							if ($idEtat == 'VA')
							{
								$status = ' valid';
							}
							elseif ($idEtat == 'RE')
							{
								$status = ' invalid';
							}

							echo
							'<tr>
								<td class="textData alignCenter'.$status .'" data-th="Date">'.$date.'</td>
								<td class="textData alignLeft'.$status .'" data-th="Libellé">'.$libelle.$libEtat.'</td>
								<td class="textData alignRight'.$status .'" data-th="Montant">'.$montant.'€</td>
								<td class="textData alignCenter'.$status .'" data-th="Justificatif">'.$justificatifNom.'</td>
							</tr>';
						}
					?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div id="totalFiche">
		TOTAL :
		<label id="totalFinal"><?php echo $infosFiche['montantValide'];?>€</label>
	</div>
</div>