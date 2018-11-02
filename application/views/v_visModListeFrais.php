<?php
	$this->load->helper('url');
	$this->load->helper('security');
?>
<div id="contenuTitre">
	<h3>Modifier ma fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0);?></h3>
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
								<td class="text alignLeft" data-th="Libellé"><label class="textCell" for="quantite'.$idFrais.'">'.$libelle.'</label></td>
								<td class="action alignLeft" data-th="Quantité"><input id="quantite'.$idFrais.'" class="inputCell" name="lesQuantites['.$idFrais.']" required="required" size="10" maxlength="3" value="'.xss_clean($quantite).'" oninput="modFicheCalcul();" type="text"/></td>
								<td class="text alignRight" data-th="Montant"><span id="montant'.$idFrais.'" class="textCell" name="lesMontants['.$idFrais.']">'.xss_clean($montant).'€</span></td>
								<td class="text alignRight" data-th="Total"><span id="'.$idFrais.'" class="textCell" name="lesTotaux['.$idFrais.']">'.number_format($quantite * $montant, 2).'€</span></td>
							</tr>';
						}
						$fraisForfait.=
						'</tbody>
					</table>
					<p class="formButtonsArea">
						<input id="okForfait" class="button" value="Enregistrer" type="submit"/><input id="annulerForfait" class="button" value="Annuler" onclick="resetForfait();" type="button"/>
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
							
							if ($justificatifFichier != null)
							{
								$justificatifNom = anchor('c_visiteur/telJustificatif/'.$mois.'/'.$id.'/'.$justificatifFichier, $justificatifNom, 'class="anchorText" title="Télécharger le justificatif"');
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
						<input id="txtDateHF" class="input datepicker" name="dateFrais" required="required" placeholder="JJ/MM/AAAA" size="10" maxlength="10" value="" type="text"/>
						<script>dateHorsForfait();</script>
					</p>
					<p>
						<label for="txtLibelleHF" class="formLabel">Libellé :</label>
						<input id="txtLibelleHF" class="input" name="libelle" required="required" size="25" maxlength="35" value="" type="text"/>
					</p>
					<p>
						<label for="txtMontantHF" class="formLabel">Montant :</label>
						<input id="txtMontantHF" class="input" name="montant" required="required" size="10" maxlength="6" value="" oninput="modFicheCalcul();" type="text"/><span class="monnaie"></span>
					</p>
					<p>
						<label for="buttonJustificatifHF" class="formLabel">Justificatif :</label>
						<input id="buttonJustificatifHF" class="uploadButton" name="justificatif" type="file"/>
					</p>
					<p class="formButtonsArea">
						<input id="okHorsForfait" class="button" value="Ajouter un frais" type="submit"/><input id="annulerHorsForfait" class="button" value="Annuler" onclick="resetHorsForfait();" type="button"/>
					</p>
				</div>
				<span class="note">Note : seuls les fichiers PDF sont acceptés.</span>
			</fieldset>
		</form>
	</div>
	<div id="totalFiche">
		TOTAL :
		<span id="totalFinal"><?php echo $infosFiche['montantValide'];?>€</span>
	</div>
</div>