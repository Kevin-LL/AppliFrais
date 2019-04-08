<?php
	$this->load->helper('url');
	$this->load->helper('security');
?>
<div id="vue-titre">
	<h2>Modifier la fiche de frais du mois <?php echo substr_replace($moisFiche, '-', 4, 0).' | Visiteur : '.$infosUtil['id'].' '.$infosUtil['nom'];?></h2>
</div>
<div id="vue-contenu">
	<?php
		if (isset($notifySuccess))
		{
			echo
			'<div class="notify notify-success">
				<div>
					<strong>Action(s) validée(s) :</strong>
					<ul>'.$notifySuccess.'</ul>
				</div>
				<span class="notify-close" onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
		if (isset($notifyError))
		{
			echo
			'<div class="notify notify-error">
				<div>
					<strong>Une erreur est survenue !</strong>
					<ul>'.$notifyError.'</ul>
				</div>
				<span class="notify-close" onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
	?>
	<div id="elements-forfaitises">
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
					<table class="liste-legere">
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
							$idFrais = $unFraisForfait['idfrais'];
							$libelle = $unFraisForfait['libelle'];
							$quantite = $unFraisForfait['quantite'];
							$montant = $unFraisForfait['montant'];
							
							$fraisForfait.=
							'<tr>
								<td class="text align-left" data-th="Libellé"><label class="cell-text" for="montant-'.strtolower($idFrais).'">'.$libelle.'</label></td>
								<td class="text align-left" data-th="Quantité"><span id="quantite-'.strtolower($idFrais).'" class="cell-text" data-name="lesQuantites['.$idFrais.']">'.xss_clean($quantite).'</span></td>
								<td class="action align-right" data-th="Montant"><input id="montant-'.strtolower($idFrais).'" class="cell-input" name="lesMontants['.$idFrais.']" required="required" maxlength="6" value="'.xss_clean($montant).'" oninput="modFicheCalculs();" type="text"/><span class="input-monnaie"></span></td>
								<td class="text align-right" data-th="Total"><span id="'.strtolower($idFrais).'" class="cell-text" data-name="lesTotaux['.$idFrais.']">'.number_format($quantite * $montant, 2).'€</span></td>
							</tr>';
						}
						$fraisForfait.=
						'</tbody>
					</table>
					<p class="form-buttons-container">
						<input id="forfait-ok" class="button" value="Enregistrer" type="submit"/><input id="forfait-annuler" class="button" value="Annuler" onclick="resetForfait();" type="button"/>
						<input name="'.$this->security->get_csrf_token_name().'" value="'.$this->security->get_csrf_hash().'" type="hidden"/>
					</p>
				</fieldset>
			</form>';
			
			if ($aucunFraisForfaitDispo == true)
			{
				$fraisForfait =
				'<fieldset>
					<legend>Eléments forfaitisés</legend>
					<p>Aucun frais au forfait disponible.</p>
				</fieldset>';
			}
			
			echo $fraisForfait;
		?>
	</div>
	<div id="elements-hors-forfait">
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
				'<table class="liste-legere">
					<caption>Descriptif des éléments hors forfait :</caption>
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
							$justificatifNom = anchor('c_comptable/telJustificatif/'.$idUtilisateur.'/'.$mois.'/'.$id.'/'.$justificatifFichier, $justificatifNom, 'class="link" title="Télécharger le justificatif"');
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
							<td class="text align-right'.$status .'" data-th="Montant"><span id="'.$id.'" class="cell-text" data-name="lesMontantsHF['.$id.']">'.xss_clean($montant).'€</span></td>
							<td class="text align-center'.$status .'" data-th="Justificatif"><span class="cell-text">'.xss_clean($justificatifNom).'</span></td>
							<td class="action align-center" data-th="Action">'.anchor('c_comptable/validFrais/'.$id, "Valider", 'class="cell-link" title="Validation d\'une ligne de frais" onclick="return confirm(\'Voulez-vous vraiment valider ce frais ?\')"').'</td>
							<td class="action align-center" data-th="Action">'.anchor('c_comptable/refuFrais/'.$id, "Refuser", 'class="cell-link" title="Refus d\'une ligne de frais" onclick="return confirm(\'Voulez-vous vraiment refuser ce frais ?\')"').'</td>
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
		<p>TOTAL : <span id="total-fiche"><?php echo $infosFiche['montantValide'];?>€</span></p>
	</div>
</div>