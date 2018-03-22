<?php
	$this->load->helper('url');
?>
<div id="contenuTitre">
	<h3>Liste des fiches de frais mises en paiement</h3>
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
	?>
	<div id="rechercherVisiteur">
		<form id="rechercher" method="post" action="<?php echo base_url('c_comptable/rechercheVis/suiviPaiement');?>">
			<div class="formList">
				<p>
					<label class="formLabel" for="txtRecherche">Visiteur :</label>
					<input id="txtRecherche" class="input" name="recherche" size="20" maxlength="30" value="" type="text"/>
				</p>
				<p class="formButtonsArea">
					<input class="button" id="okRecherche" value="Rechercher" size="20" type="submit"/><input class="button" id="annulerRecherche" value="Annuler" size="20" type="reset"/>
				</p>
			</div>
		</form>
		<span class="note">Note : la recherche s'effectue via un seul mot clé (identifiant, nom, prénom ou login).</span>
	</div>
	<div id="fichesFrais">
		<table class="listeLegere">
			<thead>
				<tr>
					<th>Mois</th>
					<th>Visiteur</th>
					<th>Montant</th>
					<th>Date modif.</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ($suiviPaiement as $uneFiche)
					{
						$action = anchor('c_comptable/rembourseFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], 'Rembourser', 'class="anchorCell" title="Rembourser la fiche" onclick="return confirm(\'Voulez-vous vraiment rembourser cette fiche ?\');"');
						
						echo 
						'<tr>
							<td class="anchorData alignCenter" data-th="Mois">'.anchor('c_comptable/voirFiche/'.$uneFiche['idUtilisateur'].'/'.$uneFiche['mois'], $uneFiche['mois'] = substr_replace($uneFiche['mois'], '-', 4, 0), 'class="anchorCell" title="Consulter la fiche"').'</td>
							<td class="textData alignLeft" data-th="Visiteur">'.$uneFiche['idUtilisateur'].' '.$uneFiche['nom'].'</td>
							<td class="textData alignRight" data-th="Montant">'.$uneFiche['montantValide'].'€</td>
							<td class="textData alignCenter" data-th="Date modif.">'.$uneFiche['dateModif'].'</td>
							<td class="anchorData alignCenter" data-th="Action">'.$action.'</td>
						</tr>';
					}
				?>
			</tbody>
		</table>
	</div>
</div>