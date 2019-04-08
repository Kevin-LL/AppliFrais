<?php
	$this->load->helper('url');
	$this->load->helper('form');
?>
<div id="vue-titre">
	<h2>Liste des fiches de frais à valider</h2>
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
	<div id="fiches-frais-rechercher">
		<form method="post" action="<?php echo base_url('c_comptable/rechercheFiches');?>">
			<fieldset>
				<legend>Rechercher des fiches</legend>
				<div class="form-list">
					<p>
						<label class="form-label label-small" for="txt-visiteur">Visiteur :</label>
						<select id="txt-visiteur" class="input input-medium" name="visiteur">
							<option value="">Tous les visiteurs</option>
							<?php
								foreach ($lesVisiteurs as $unVisiteur)
								{
									echo
									'<option value="'.$unVisiteur['id'].'" '.set_select('visiteur', $unVisiteur['id']).'>'.$unVisiteur['id'].' '.$unVisiteur['nom'].'</option>';
								}
							?>
						</select>
					</p>
					<p>
						<label class="form-label label-small" for="txt-mois">Mois :</label>
						<input id="txt-mois" class="input input-medium" name="mois" placeholder="Tous les mois" maxlength="7" value="<?php echo set_value('mois');?>" type="text"/>
						<script>moisRechercher();</script>
					</p>
					<p class="form-buttons-container">
						<input id="rechercher-ok" class="button" value="Rechercher" type="submit"/><input id="rechercher-annuler" class="button" value="Annuler" type="reset"/>
						<input name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" type="hidden"/>
					</p>
				</div>
			</fieldset>
		</form>
	</div>
	<div id="fiches-frais">
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
				<p class="form-buttons-container">
					<input id="select-controller" class="checkbox" type="checkbox"/><input class="button" id="select" value="Tout cocher/décocher" onclick="selectFiches();" type="button"/><input id="valider-ok" class="button" value="Valider la sélection" onclick="return validSelect();" type="submit"/>
					<input name="'.$this->security->get_csrf_token_name().'" value="'.$this->security->get_csrf_hash().'" type="hidden"/>
				</p>
				<table class="liste-legere">
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
						$action1 = anchor('c_comptable/modFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], 'Modifier', 'class="cell-link" title="Modifier la fiche"');
						$action2 = anchor('c_comptable/validFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], 'Valider', 'class="cell-link" title="Valider la fiche" onclick="return confirm(\'Voulez-vous vraiment valider cette fiche ?\');"');
						$action3 = anchor('c_comptable/ajouterMotifRefus/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], 'Refuser', 'class="cell-link" title="Refuser la fiche"');
						
						$fiches.= 
						'<tr>
							<td class="transparent"><input id="'.$uneFiche['idUtilisateur'].'-'.$uneFiche['mois'].'" class="checkbox" name="lesFiches[]" value="'.$uneFiche['idUtilisateur'].'_'.$uneFiche['mois'].'" onchange="checkSelect();" type="checkbox"/><label class="checkbox-custom" for="'.$uneFiche['idUtilisateur'].'-'.$uneFiche['mois'].'"></label></td>
							<td class="action align-center" data-th="Mois">'.anchor('c_comptable/voirFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], substr_replace($uneFiche['mois'], '-', 4, 0), 'class="cell-link" title="Consulter la fiche"').'</td>
							<td class="text align-left" data-th="Visiteur"><span class="cell-text">'.$uneFiche['idUtilisateur'].' '.$uneFiche['nom'].'</span></td>
							<td class="text align-right" data-th="Montant"><span class="cell-text">'.$uneFiche['montantValide'].'€</span></td>
							<td class="text align-center" data-th="Date modif."><span class="cell-text">'.$uneFiche['dateModif'].'</span></td>
							<td class="action align-center" data-th="Action">'.$action1.'</td>
							<td class="action align-center" data-th="Action">'.$action2.'</td>
							<td class="action align-center" data-th="Action">'.$action3.'</td>
						</tr>';
					}
					$fiches.=
					'</tbody>
				</table>
			</form>';
			
			if ($aucuneFicheDispo == true)
			{
				$fiches =
				'<p>Aucune fiche de frais disponible.</p>';
			}
			
			echo $fiches;
		?>
	</div>
</div>