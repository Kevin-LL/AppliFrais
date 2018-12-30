<?php
	$this->load->helper('url');
	$this->load->helper('form');
?>
<div id="contenu-nav">
	<?php echo anchor('c_comptable/paiementFiches', 'Fiches mises en paiement', 'class="link" title="Rembourser les fiches de frais"');?>
	/ Fiches remboursées
</div>
<div id="contenu-titre">
	<h3>Synthèse des fiches de frais remboursées</h3>
</div>
<div id="contenu-list">
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
			
			foreach ($syntheseFiches as $uneFiche)
			{
				if (isset($uneFiche['mois']))
				{
					$aucuneFicheDispo = false;
				}
			}
			
			$fiches = 
			'<table class="liste-legere">
				<thead>
					<tr>
						<th>Mois</th>
						<th>Visiteur</th>
						<th>Montant</th>
						<th>Date remb.</th>
					</tr>
				</thead>
				<tbody>';
				foreach ($syntheseFiches as $uneFiche)
				{
					$fiches.= 
					'<tr>
						<td class="action align-center" data-th="Mois">'.anchor('c_comptable/voirFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], substr_replace($uneFiche['mois'], '-', 4, 0), 'class="cell-button" title="Consulter la fiche"').'</td>
						<td class="text align-left" data-th="Visiteur"><span class="cell-text">'.$uneFiche['idUtilisateur'].' '.$uneFiche['nom'].'</span></td>
						<td class="text align-right" data-th="Montant"><span class="cell-text">'.$uneFiche['montantValide'].'€</span></td>
						<td class="text align-center" data-th="Date remb."><span class="cell-text">'.$uneFiche['dateModif'].'</span></td>
					</tr>';
				}
				$fiches.=
				'</tbody>
			</table>';
			
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