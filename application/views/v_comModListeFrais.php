<?php
	$this->load->helper('url');
	$this->load->helper('security');
?>
<div id="contenuTitre">
	<h3>Modifier la fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0).' | Visiteur : '.$infosUtil['id'].' '.$infosUtil['nom'];?></h3>
</div>
<div id="contenuList">
	<?php
		if (isset($notifySuccess))
		{
			echo
			'<div class="notify success">
				<div>
					<h4>Action(s) validée(s) :</h4>
					<ul>'.$notifySuccess.'</ul>
				</div>
				<span class="close" onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
		if (isset($notifyError))
		{
			echo
			'<div class="notify error">
				<div>
					<h4>Une erreur est survenue !</h4>
					<ul>'.$notifyError.'</ul>
				</div>
				<span class="close" onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
	?>
	<div id="elementsForfaitises">
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
			'<form id="forfait" method="post" action="'.base_url('c_comptable/majForfait').'">
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
						<tbody>';
						foreach ($lesFraisForfait as $unFraisForfait)
						{
							$idFrais = $unFraisForfait['idfrais'];
							$libelle = $unFraisForfait['libelle'];
							$quantite = $unFraisForfait['quantite'];
							$montant = $unFraisForfait['montant'];
							
							$fraisForfait.=
							'<tr>
								<td class="text alignLeft" data-th="Libellé"><label class="textCell" for="montant'.$idFrais.'">'.$libelle.'</label></td>
								<td class="text alignLeft" data-th="Quantité"><span id="quantite'.$idFrais.'" class="textCell" name="lesQuantites['.$idFrais.']">'.xss_clean($quantite).'</span></td>
								<td class="action alignRight" data-th="Montant"><input id="montant'.$idFrais.'" class="inputCell" name="lesMontants['.$idFrais.']" required="required" size="10" maxlength="6" value="'.xss_clean($montant).'" oninput="modFicheCalcul();" type="text"/><span class="monnaie"></span></td>
								<td class="text alignRight" data-th="Total"><span id="'.$idFrais.'" class="textCell" name="lesTotaux['.$idFrais.']">'.number_format($quantite * $montant, 2).'€</span></td>
							</tr>';
						}
						$fraisForfait.=
						'</tbody>
					</table>
					<p class="formButtonsArea">
						<input id="okForfait" class="button" value="Enregistrer" type="submit"/><input id="annulerForfait" class="button" value="Annuler" onclick="resetForfait();" type="button"/>
					</p>
				</fieldset>
			</form>';
			
			if ($aucunFraisForfaitDispo == true)
			{
				$fraisForfait =
				'<fieldset>
					<legend>Eléments forfaitisés</legend>
					<p>
						Aucun frais au forfait disponible.
					</p>
				</fieldset>';
			}
			
			echo $fraisForfait;
		?>
	</div>
	<div id="elementsHorsForfait">
		<fieldset>
			<legend>Validation des éléments hors forfait</legend>
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
							<th colspan="2">Actions</th>
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
							<td class="text alignRight'.$status .'" data-th="Montant"><span id="'.$id.'" class="textCell" name="lesMontantsHF['.$id.']">'.xss_clean($montant).'€</span></td>
							<td class="text alignCenter'.$status .'" data-th="Justificatif"><span class="textCell">'.xss_clean($justificatifNom).'</span></td>
							<td class="action alignCenter" data-th="Action">'.anchor('c_comptable/validFrais/'.$id, "Valider", 'class="anchorCell" title="Validation d\'une ligne de frais" onclick="return confirm(\'Voulez-vous vraiment valider ce frais ?\')"').'</td>
							<td class="action alignCenter" data-th="Action">'.anchor('c_comptable/refuFrais/'.$id, "Refuser", 'class="anchorCell" title="Refus d\'une ligne de frais" onclick="return confirm(\'Voulez-vous vraiment refuser ce frais ?\')"').'</td>
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