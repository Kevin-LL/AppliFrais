<?php
	$this->load->helper('url');
	$this->load->helper('security');
?>
<div id="contenuTitre">
	<h3>Consulter la fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0).' | Visiteur : '.$infosUtil['id'].' '.$infosUtil['nom'];?></h3>
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
						$libelle = $unFraisForfait['libelle'];
						$quantite = $unFraisForfait['quantite'];
						$montant = $unFraisForfait['montant'];
						
						$fraisForfait.=
						'<tr>
							<td class="text alignLeft" data-th="Libellé"><span class="textCell">'.$libelle.'</span></td>
							<td class="text alignLeft" data-th="Quantité"><span class="textCell">'.xss_clean($quantite).'</span></td>
							<td class="text alignRight" data-th="Montant"><span class="textCell">'.xss_clean($montant).'€</span></td>
							<td class="text alignRight" data-th="Total"><span class="textCell">'.number_format($quantite * $montant, 2).'€</span></td>
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
						
						if ($justificatifFichier != null)
						{
							$justificatifNom = anchor('c_comptable/telJustificatif/'.$idUtilisateur.'/'.$mois.'/'.$id.'/'.$justificatifFichier, $justificatifNom, 'class="anchorText" title="Télécharger le justificatif"');
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
							<td class="text alignCenter'.$status .'" data-th="Date"><span class="textCell">'.xss_clean($date).'</span></td>
							<td class="text alignLeft'.$status .'" data-th="Libellé"><span class="textCell">'.xss_clean($libelle).$libEtat.'</span></td>
							<td class="text alignRight'.$status .'" data-th="Montant"><span class="textCell">'.xss_clean($montant).'€</span></td>
							<td class="text alignCenter'.$status .'" data-th="Justificatif"><span class="textCell">'.xss_clean($justificatifNom).'</span></td>
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