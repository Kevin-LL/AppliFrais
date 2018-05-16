<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Liste des fiches de frais à valider</h3>
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
	<div id="rechercherVisiteur">
		<form method="post" action="<?php echo base_url('c_comptable/rechercheVis/validationFiches');?>">
			<fieldset>
				<legend>Rechercher un visiteur</legend>
				<div class="formList">
					<p>
						<label class="formLabel" for="txtRecherche">Visiteur :</label>
						<input id="txtRecherche" class="input" name="recherche" size="20" maxlength="31" value="" type="text"/>
					</p>
					<p class="formButtonsArea">
						<input id="okRechercher" class="button" value="Rechercher" type="submit"/><input id="annulerRechercher" class="button" value="Annuler" type="reset"/>
					</p>
				</div>
				<span class="note">Note : la recherche s'effectue via un seul mot clé (identifiant, nom, prénom ou login).</span>
			</fieldset>
		</form>
	</div>
	<div id="fichesFrais">
		<?php
			$aucuneFicheDispo = true;
			
			foreach ($validationFiches as $uneFiche)
			{
				if (isset($uneFiche['mois']))
				{
					$aucuneFicheDispo = false;
				}
			}
			
			$fiches =
			'<form method="post" action="'.base_url('c_comptable/validSelect').'">
				<p class="formButtonsArea">
					<input id="selectController" class="checkbox" type="checkbox"/><input class="button" id="select" value="Tout cocher/décocher" onclick="selectFiches();" type="button"/><input id="okSigner" class="button" value="Valider la selection" onclick="return validSelect();" type="submit"/>
				</p>
				<table class="listeLegere">
					<thead>
						<tr>
							<th class="transparent"></th>
							<th>Mois</th>
							<th>Visiteur</th>
							<th>Montant</th>
							<th>Date modif.</th>
							<th colspan="3">Actions</th>
						</tr>
					</thead>
					<tbody>';
					foreach ($validationFiches as $uneFiche)
					{
						$action1 = anchor('c_comptable/modFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], 'Modifier', 'class="anchorCell" title="Modifier la fiche"');
						$action2 = anchor('c_comptable/validFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], 'Valider', 'class="anchorCell" title="Valider la fiche" onclick="return confirm(\'Voulez-vous vraiment valider cette fiche ?\');"');
						$action3 = anchor('c_comptable/ajouterMotifRefus/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], 'Refuser', 'class="anchorCell" title="Refuser la fiche"');
						
						$fiches.= 
						'<tr>
							<td class="transparent"><input id="'.$uneFiche['idUtilisateur'].'_'.$uneFiche['mois'].'" class="checkbox" name="lesFiches[]" value="'.$uneFiche['idUtilisateur'].'_'.$uneFiche['mois'].'" onchange="checkSelect();" type="checkbox"/><label class="customCheckbox" for="'.$uneFiche['idUtilisateur'].'_'.$uneFiche['mois'].'"></label></td>
							<td class="action alignCenter" data-th="Mois">'.anchor('c_comptable/voirFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], substr_replace($uneFiche['mois'], '-', 4, 0), 'class="anchorCell" title="Consulter la fiche"').'</td>
							<td class="text alignLeft" data-th="Visiteur">'.$uneFiche['idUtilisateur'].' '.$uneFiche['nom'].'</td>
							<td class="text alignRight" data-th="Montant">'.$uneFiche['montantValide'].'€</td>
							<td class="text alignCenter" data-th="Date modif.">'.$uneFiche['dateModif'].'</td>
							<td class="action alignCenter" data-th="Action">'.$action1.'</td>
							<td class="action alignCenter" data-th="Action">'.$action2.'</td>
							<td class="action alignCenter" data-th="Action">'.$action3.'</td>
						</tr>';
					}
					$fiches.=
					'</tbody>
				</table>
			</form>';
			
			if ($aucuneFicheDispo == true)
			{
				$fiches =
				'<p>
					Aucune fiche de frais disponible.
				</p>';
			}
			
			echo $fiches;
		?>
	</div>
</div>