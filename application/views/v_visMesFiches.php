<?php
	$this->load->helper('url');
?>
<div id="contenu-titre">
	<h3>Liste de mes fiches de frais</h3>
</div>
<div id="contenu-list">
	<?php
		if (isset($notifySuccess))
		{
			echo
			'<div class="notify notify-success">
				<div>
					<h4>Action(s) validée(s) :</h4>
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
					<h4>Une erreur est survenue !</h4>
					<ul>'.$notifyError.'</ul>
				</div>
				<span class="notify-close" onclick="closeNotify(this);">&#10006;</span>
			</div>';
		}
	?>
	<div id="fiches-frais">
		<?php
			$toutesFichesSignees = true;
			$aucuneFicheDispo = true;
			
			foreach ($mesFiches as $uneFiche)
			{
				if ($uneFiche['id'] == 'CR' || $uneFiche['id'] == 'RE')
				{
					$toutesFichesSignees = false;
				}
				
				if (isset($uneFiche['mois']))
				{
					$aucuneFicheDispo = false;
				}
			}
			
			$buttons =
			'<p class="form-buttons-container">
				<input id="select-controller" class="checkbox" type="checkbox"/><input class="button" id="select" value="Tout cocher/décocher" onclick="selectFiches();" type="button"/><input id="signer-ok" class="button" value="Signer la sélection" onclick="return validSelect();" type="submit"/>
				<input name="'.$this->security->get_csrf_token_name().'" value="'.$this->security->get_csrf_hash().'" type="hidden"/>
			</p>';
			
			$thead =
			'<thead>
				<tr>
					<th class="transparent"></th>
					<th>Mois</th>
					<th>Etat</th>
					<th>Montant</th>
					<th>Date modif.</th>
					<th colspan="2">Actions</th>
				</tr>
			</thead>';
			
			if ($toutesFichesSignees == true)
			{
				$buttons = '';
				$thead =
				'<thead>
					<tr>
						<th>Mois</th>
						<th>Etat</th>
						<th>Montant</th>
						<th>Date modif.</th>
						<th>Action</th>
					</tr>
				</thead>';
			}
			
			$fiches =
			'<form method="post" action="'.base_url('c_visiteur/signeSelect').'">'
				.$buttons.
				'<table class="liste-legere">'
					.$thead.
					'<tbody>';
					foreach ($mesFiches as $uneFiche)
					{
						$checkboxes = '<input id="'.$uneFiche['mois'].'" class="checkbox" name="lesFiches[]" value="'.$uneFiche['mois'].'" onchange="checkSelect();" type="checkbox"/><label class="checkbox-custom" for="'.$uneFiche['mois'].'"></label>';
						$motifRefus = '';
						$action1 = '';
						$action2 = '';
						$status = '';
						
						if ($uneFiche['id'] == 'CR' || $uneFiche['id'] == 'RE')
						{
							$action1 = anchor('c_visiteur/modFiche/'.$uneFiche['mois'], 'Modifier', 'class="cell-button" title="Modifier la fiche"');
							$action2 = anchor('c_visiteur/signeFiche/'.$uneFiche['mois'], 'Signer', 'class="cell-button" title="Signer la fiche" onclick="return confirm(\'Voulez-vous vraiment signer cette fiche ?\');"');
							if ($uneFiche['id'] == 'RE')
							{
								$motifRefus = ', '.anchor('c_visiteur/voirMotifRefus/'.$uneFiche['mois'], 'voir le motif', 'class="link" title="Consulter le motif de refus"');
								$status = ' invalid';
							}
						}
						elseif ($uneFiche['id'] == 'CL' || $uneFiche['id'] == 'VA' || $uneFiche['id'] == 'RB')
						{
							$checkboxes = '';
							$action1 = anchor('c_visiteur/impFiche/'.$uneFiche['mois'], 'Imprimer', 'class="cell-button" title="Imprimer la fiche"');
							$action2 = '';
							if ($uneFiche['id'] == 'VA' || $uneFiche['id'] == 'RB')
							{
								$status = ' valid';
							}
						}
						elseif ($uneFiche['id'] == 'IN')
						{
							$checkboxes = '';
							$action1 = anchor('c_visiteur/supprFiche/'.$uneFiche['mois'], 'Supprimer', 'class="cell-button" title="Supprimer la fiche" onclick="return confirm(\'Voulez-vous vraiment supprimer cette fiche ?\');"');
							$action2 = '';
							$status = ' invalid';
						}
						
						$checkboxesColumn = '<td class="transparent">'.$checkboxes.'</td>';
						$action2Column = '<td class="action align-center" data-th="Action">'.$action2.'</td>';
						
						if ($toutesFichesSignees == true)
						{
							$checkboxesColumn = '';
							$action2Column = '';
						}
						
						$fiches.=
						'<tr>
							'.$checkboxesColumn.'
							<td class="action align-center" data-th="Mois">'.anchor('c_visiteur/voirFiche/'.$uneFiche['mois'], substr_replace($uneFiche['mois'], '-', 4, 0), 'class="cell-button" title="Consulter la fiche"').'</td>
							<td class="text align-left'.$status.'" data-th="Etat"><span class="cell-text">'.$uneFiche['libelle'].$motifRefus.'</span></td>
							<td class="text align-right'.$status.'" data-th="Montant"><span class="cell-text">'.$uneFiche['montantValide'].'€</span></td>
							<td class="text align-center'.$status.'" data-th="Date modif."><span class="cell-text">'.$uneFiche['dateModif'].'</span></td>
							<td class="action align-center" data-th="Action">'.$action1.'</td>
							'.$action2Column.'
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