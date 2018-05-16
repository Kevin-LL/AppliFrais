<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Consulter ma fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0);?></h3>
</div>
<div id="contenuList">
	<div id="elementsForfaitises">
		<fieldset>
			<legend>Eléments forfaitisés</legend>
			<?php
				$aucunFraisForfaitDispo = true;
				
				foreach ($lesFraisForfait as $unFraisForfait)
				{
					if (isset($unFraisForfait['idfrais']))
					{
						$aucunFraisForfaitDispo = false;
					}
				}
				
				$fraisForfait =
				'<h4>Descriptif des éléments forfaitisés :</h4>
				<table class="listeLegere">
					<thead>
						<tr>
							<th>Libellé</th>
							<th>Quantité</th>
							<th>Montant</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>';
					foreach ($lesFraisForfait as $unFraisForfait)
					{
						$idFrais = $unFraisForfait['idfrais'];
						$libelle = $unFraisForfait['libelle'];
						$quantite = $unFraisForfait['quantite'];
						$montant = $unFraisForfait['montant'];
						
						$fraisForfait.=
						'<tr>
							<td class="text alignLeft" data-th="Libellé">'.$libelle.'</td>
							<td class="text alignLeft" data-th="Quantité">'.$quantite.'</td>
							<td class="text alignRight" data-th="Montant">'.$montant.'€</td>
							<td class="text alignRight" data-th="Total"><span id="total'.$idFrais.'">'.number_format($quantite * $montant, 2).'€</span></td>
						</tr>';
					}
					$fraisForfait.=
					'</tbody>
				</table>';
				
				if ($aucunFraisForfaitDispo == true)
				{
					$fraisForfait =
					'<p>
						Aucun frais au forfait disponible.
					</p>';
				}
				
				echo $fraisForfait;
			?>
		</fieldset>
	</div>
	<div id="elementsHorsForfait">
		<fieldset>
			<legend>Elément(s) hors forfait</legend>
			<?php
				$aucunFraisHorsForfaitDispo = true;
				
				foreach ($lesFraisHorsForfait as $unFraisHorsForfait)
				{
					if (isset($unFraisHorsForfait['id']))
					{
						$aucunFraisHorsForfaitDispo = false;
					}
				}
				
				$fraisHorsForfait =
				'<h4>Descriptif des éléments hors forfait :</h4>
				<table class="listeLegere">
					<thead>
						<tr>
							<th>Date</th>
							<th>Libellé</th>
							<th>Montant</th>
							<th>Justificatif ['.$nbJustificatifs.']</th>
						</tr>
					</thead>
					<tbody>';
					foreach ($lesFraisHorsForfait as $unFraisHorsForfait)
					{
						$id = $unFraisHorsForfait['id'];
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
							if ($justificatifFichier != null)
							{
								$justificatifNom = anchor('c_visiteur/telJustificatif/'.$mois.'/'.$id.'/'.$justificatifFichier, $justificatifNom, 'class="anchorText" title="Télécharger le justificatif"');
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
						
						$fraisHorsForfait.=
						'<tr>
							<td class="text alignCenter'.$status .'" data-th="Date">'.$date.'</td>
							<td class="text alignLeft'.$status .'" data-th="Libellé">'.$libelle.$libEtat.'</td>
							<td class="text alignRight'.$status .'" data-th="Montant">'.$montant.'€</td>
							<td class="text alignCenter'.$status .'" data-th="Justificatif">'.$justificatifNom.'</td>
						</tr>';
					}
					$fraisHorsForfait.=
					'</tbody>
				</table>';
				
				if ($aucunFraisHorsForfaitDispo == true)
				{
					$fraisHorsForfait =
					'<p>
						Aucun frais hors forfait disponible.
					</p>';
				}
				
				echo $fraisHorsForfait;
			?>
		</fieldset>
	</div>
	<div id="totalFiche">
		TOTAL :
		<span id="totalFinal"><?php echo $infosFiche['montantValide'];?>€</span>
	</div>
</div>