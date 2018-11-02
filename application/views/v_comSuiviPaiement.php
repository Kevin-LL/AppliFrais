<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Liste des fiches de frais mises en paiement</h3>
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
	<div id="rechercherVisiteur">
		<form method="post" action="<?php echo base_url('c_comptable/rechercheVis');?>">
			<fieldset>
				<legend>Rechercher un visiteur</legend>
				<div class="formList">
					<p>
						<label class="formLabel" for="txtRecherche">Visiteur :</label>
						<select id="txtRecherche" class="input" name="recherche">
							<option value="">Tous les visiteurs</option>
							<?php
								foreach ($lesVisiteurs as $unVisiteur)
								{
									echo
									'<option value="'.$unVisiteur['id'].'">'.$unVisiteur['id'].' '.$unVisiteur['nom'].'</option>';
								}
							?>
						</select>
					</p>
					<p class="formButtonsArea">
						<input id="okRechercher" class="button" value="Rechercher" type="submit"/><input id="annulerRechercher" class="button" value="Annuler" type="reset"/>
					</p>
				</div>
			</fieldset>
		</form>
	</div>
	<div id="fichesFrais">
		<?php
			$aucuneFicheDispo = true;
			
			foreach ($suiviPaiement as $uneFiche)
			{
				if (isset($uneFiche['mois']))
				{
					$aucuneFicheDispo = false;
				}
			}
			
			$fiches = 
			'<form method="post" action="'.base_url('c_comptable/rembourseSelect').'">
				<p class="formButtonsArea">
					<input id="selectController" class="checkbox" type="checkbox"/><input class="button" id="select" value="Tout cocher/décocher" onclick="selectFiches();" type="button"/><input id="okSigner" class="button" value="Rembourser la selection" onclick="return validSelect();" type="submit"/>
				</p>
				<table class="listeLegere">
					<thead>
						<tr>
							<th class="transparent"></th>
							<th>Mois</th>
							<th>Visiteur</th>
							<th>Montant</th>
							<th>Date modif.</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>';
					foreach ($suiviPaiement as $uneFiche)
					{
						$action = anchor('c_comptable/rembourseFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], 'Rembourser', 'class="anchorCell" title="Rembourser la fiche" onclick="return confirm(\'Voulez-vous vraiment rembourser cette fiche ?\');"');
						
						$fiches.= 
						'<tr>
							<td class="transparent"><input id="'.$uneFiche['idUtilisateur'].'_'.$uneFiche['mois'].'" class="checkbox" name="lesFiches[]" value="'.$uneFiche['idUtilisateur'].'_'.$uneFiche['mois'].'" onchange="checkSelect();" type="checkbox"/><label class="customCheckbox" for="'.$uneFiche['idUtilisateur'].'_'.$uneFiche['mois'].'"></label></td>
							<td class="action alignCenter" data-th="Mois">'.anchor('c_comptable/voirFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], substr_replace($uneFiche['mois'], '-', 4, 0), 'class="anchorCell" title="Consulter la fiche"').'</td>
							<td class="text alignLeft" data-th="Visiteur"><span class="textCell">'.$uneFiche['idUtilisateur'].' '.$uneFiche['nom'].'</span></td>
							<td class="text alignRight" data-th="Montant"><span class="textCell">'.$uneFiche['montantValide'].'€</span></td>
							<td class="text alignCenter" data-th="Date modif."><span class="textCell">'.$uneFiche['dateModif'].'</span></td>
							<td class="action alignCenter" data-th="Action">'.$action.'</td>
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