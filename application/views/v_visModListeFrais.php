<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Modifier ma fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0);?></h3>
</div>
<div id="contenuList">
	<?php
		if (isset($notifyInfo))
		{
			echo
			'<div class="notify info">
				'.$notifyInfo.'
				<span onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
		if (isset($notifyError))
		{
			echo
			'<div class="notify error">
				'.$notifyError.'
				<span onclick="closeNotify(this);">&#10006;</span>
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
			'<form id="forfait" method="post" action="'.base_url('c_visiteur/majForfait').'">
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
								<td class="text alignLeft" data-th="Libellé"><label for="'.$idFrais.'">'.$libelle.'</label></td>
								<td class="action alignLeft" data-th="Quantité"><input id="'.$idFrais.'" class="inputCell" name="lesFrais['.$idFrais.']" required="required" size="10" maxlength="3" value="'.$quantite.'" oninput="modFicheCalcul();" type="text"/></td>
								<td class="action alignRight" data-th="Montant"><input id="montant'.$idFrais.'" class="inputCell" name="lesMontants['.$idFrais.']" disabled="disabled" size="10" maxlength="6" value="'.$montant.'" type="text"/><span class="monnaie"></span></td>
								<td class="text alignRight" data-th="Total"><span id="total'.$idFrais.'">0.00€</span></td>
							</tr>';
						}
						$fraisForfait.=
						'</tbody>
					</table>
					<p class="formButtonsArea">
						<input id="okForfait" class="button" value="Enregistrer" onclick="return validTotalFicheFrais();" type="submit"/><input id="annulerForfait" class="button" value="Annuler" onclick="resetForfait();" type="button"/>
					</p>
					<span class="note">Note : montants sous réserve de validation.</span>
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
		<form id="horsforfait" method="post" enctype="multipart/form-data" action="<?php echo base_url('c_visiteur/ajouteFrais');?>">
			<fieldset>
				<legend>Nouvel élément hors forfait</legend>
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
					<table id="horsForfaitListe" class="listeLegere">
						<thead>
							<tr>
								<th>Date</th>
								<th>Libellé</th>
								<th>Montant</th>
								<th>Justificatif ['.$nbJustificatifs.']</th>
								<th>Action</th>
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
								<td class="text alignRight'.$status .'" data-th="Montant"><label id="'.$id.'" name="lesMontantsHF['.$id.']">'.$montant.'</label>€</td>
								<td class="text alignCenter'.$status .'" data-th="Justificatif">'.$justificatifNom.'</td>
								<td class="action alignCenter" data-th="Action">'.anchor('c_visiteur/supprFrais/'.$id, "Supprimer ce frais", 'class="anchorCell" title="Suppression d\'une ligne de frais" onclick="return confirm(\'Voulez-vous vraiment supprimer ce frais ?\')"').'</td>
							</tr>';
						}
						$fraisHorsForfait.=
						'</tbody>
					</table>';
					
					if ($aucunFraisHorsForfaitDispo == true)
					{
						$fraisHorsForfait = '';
					}
					
					echo $fraisHorsForfait;
				?>
				<h4>Ajouter un élément hors forfait :</h4>
				<div class="formList">
					<p>
						<label for="txtDateHF" class="formLabel">Date :</label>
						<input id="txtDateHF" class="input datepicker" name="dateFrais" readonly="readonly" size="10" maxlength="10" value="" onfocus="this.blur();" type="text"/>
						<script>
							$(document).ready(function() {
								$(".datepicker").datepicker();
								$(".datepicker").datepicker('setDate', new Date());
							});
						</script>
					</p>
					<p>
						<label for="txtLibelleHF" class="formLabel">Libellé :</label>
						<input id="txtLibelleHF" class="input" name="libelle" required="required" size="25" maxlength="35" value="" type="text"/>
					</p>
					<p>
						<label for="txtMontantHF" class="formLabel">Montant :</label>
						<input id="txtMontantHF" class="input" name="montant" required="required" size="10" maxlength="6" value="" oninput="modFicheCalcul();" type="text"/>€
					</p>
					<p>
						<label for="buttonJustificatifHF" class="formLabel">Justificatif :</label>
						<input id="buttonJustificatifHF" class="uploadButton" name="justificatif" type="file"/>
					</p>
					<p class="formButtonsArea">
						<input id="okHorsForfait" class="button" value="Ajouter un frais" onclick="return validTotalFicheFrais() && validJustificatif() && validLignesHorsForfait();" type="submit"/><input id="annulerHorsForfait" class="button" value="Annuler" onclick="resetHorsForfait();" type="button"/>
					</p>
				</div>
				<span class="note">Note : seuls les fichiers PDF sont acceptés.</span>
			</fieldset>
		</form>
	</div>
	<div id="totalFiche">
		TOTAL :
		<span id="totalFinal">0.00€</span>
		<script>modFicheCalcul();</script>
	</div>
</div>