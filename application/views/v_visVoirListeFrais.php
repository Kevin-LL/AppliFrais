<?php
	$this->load->helper('url');
	$this->load->helper('security');
?>
<div id="vue-titre">
	<h2>Consulter ma fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0);?></h2>
</div>
<div id="vue-contenu">
	<div id="elements-forfaitises">
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
				'<table class="liste-legere">
					<caption>Descriptif des éléments forfaitisés :</caption>
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
						$libelle = $unFraisForfait['libelle'];
						$quantite = $unFraisForfait['quantite'];
						$montant = $unFraisForfait['montant'];
						
						$fraisForfait.=
						'<tr>
							<td class="text align-left" data-th="Libellé"><span class="cell-text">'.$libelle.'</span></td>
							<td class="text align-left" data-th="Quantité"><span class="cell-text">'.xss_clean($quantite).'</span></td>
							<td class="text align-right" data-th="Montant"><span class="cell-text">'.xss_clean($montant).'€</span></td>
							<td class="text align-right" data-th="Total"><span class="cell-text">'.number_format($quantite * $montant, 2).'€</span></td>
						</tr>';
					}
					$fraisForfait.=
					'</tbody>
				</table>';
				
				if ($aucunFraisForfaitDispo == true)
				{
					$fraisForfait =
					'<p>Aucun frais au forfait disponible.</p>';
				}
				
				echo $fraisForfait;
			?>
		</fieldset>
	</div>
	<div id="elements-hors-forfait">
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
				'<table class="liste-legere">
					<caption>Descriptif des éléments hors forfait :</caption>
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
						
						if ($justificatifFichier != null)
						{
							$justificatifNom = anchor('c_visiteur/telJustificatif/'.$mois.'/'.$id.'/'.$justificatifFichier, $justificatifNom, 'class="link" title="Télécharger le justificatif"');
						}
						else
						{
							$justificatifNom = 'Aucun';
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
							<td class="text align-center'.$status .'" data-th="Date"><span class="cell-text">'.xss_clean($date).'</span></td>
							<td class="text align-left'.$status .'" data-th="Libellé"><span class="cell-text">'.xss_clean($libelle).$libEtat.'</span></td>
							<td class="text align-right'.$status .'" data-th="Montant"><span class="cell-text">'.xss_clean($montant).'€</span></td>
							<td class="text align-center'.$status .'" data-th="Justificatif"><span class="cell-text">'.xss_clean($justificatifNom).'</span></td>
						</tr>';
					}
					$fraisHorsForfait.=
					'</tbody>
				</table>';
				
				if ($aucunFraisHorsForfaitDispo == true)
				{
					$fraisHorsForfait =
					'<p>Aucun frais hors forfait disponible.</p>';
				}
				
				echo $fraisHorsForfait;
			?>
		</fieldset>
	</div>
	<div id="fiche-frais-total">
		<p>TOTAL : <?php echo $infosFiche['montantValide'];?>€</p>
	</div>
</div>