/**
 * Gère l'affichage du menu lorsque la page est en mode responsive
 */
function toggleMenu() {
	var menu = document.getElementById("menu");
	var contenu = document.getElementById("contenu");
	if (menu.style.display == 'none' || menu.style.display == '') {
		menu.style.display = 'block';
		contenu.style.display = 'none';
	} else {
		menu.style.display = 'none';
		contenu.style.display = 'flex';
	}
}

/**
 * Ferme les zones de notifications
 */
function closeNotify(notify) {
	notify.parentNode.style.display = 'none';
}

/**
 * Gère le champ mois du formulaire de recherche des fiche de frais
 */
function moisRechercher() {
	$("#txt-mois").MonthPicker({
		Button: '<button type="button" class="ui-datepicker-trigger">&#x1f4c5;</button>',
		MonthFormat: 'yy-mm',
		i18n: {
			year: 'Année',
			prevYear: 'Année précédente',
			nextYear: 'Année suivante',
			next12Years: 'Avancer de 12 ans',
			prev12Years: 'Reculer de 12 ans',
			nextLabel: 'Suivant',
			prevLabel: 'Précédent',
			jumpYears: 'Sauter des années',
			backTo: 'Revenir à',
			months: ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juill.', 'août', 'sept.', 'oct.', 'nov.', 'déc.']
		}
	});
	$("#txt-mois").mask('0000-00');
}

/**
 * Sélectionne toutes les fiches de frais.
 * Si toutes les fiches de frais sont cochées elles seront décochées et inversement
 */
function selectFiches() {
	var selectController = document.getElementById("select-controller");
	selectController.checked = !selectController.checked;
	var checkboxes = document.getElementsByTagName("input");
	for (var i = 0; i < checkboxes.length; i++) {
		name = checkboxes[i].getAttribute("name");
		if (name.indexOf("lesFiches") == 0) {
			if (selectController.checked == true) {
				checkboxes[i].checked = true;
			} else {
				checkboxes[i].checked = false;
			}
		}
	}
}

/**
 * Valide la sélection des fiches de frais. 
 * Si au moins une fiche de frais est cochée envoie une demande confirmation
 */
function validSelect() {
	var checkboxes = document.getElementsByTagName("input");
	var rienEstCoche = true;
	for (var i = 0; i < checkboxes.length; i++) {
		name = checkboxes[i].getAttribute("name");
		if (name.indexOf("lesFiches") == 0) {
			if (checkboxes[i].checked == true) {
				rienEstCoche = false;
			}
		}
	}
	if (rienEstCoche == false) {
		var confirmer = confirm("Voulez-vous vraiment confirmer cette action ?");
		if (confirmer == true) {
			return true;
		} else {
			return false;
		}
	}
}

/**
 * Contrôle la sélection des fiches de frais.
 * Si toutes les fiches de frais sont cochées l'élément "select-controller" sera lui aussi coché,
 * sinon il sera décoché
 */
function checkSelect() {
	var selectController = document.getElementById("select-controller");
	var checkboxes = document.getElementsByTagName("input");
	var toutEstCoche = true;
	for (var i = 0; i < checkboxes.length; i++) {
		name = checkboxes[i].getAttribute("name");
		if (name.indexOf("lesFiches") == 0) {
			if (checkboxes[i].checked == false) {
				toutEstCoche = false;
			}
		}
	}
	if (toutEstCoche == true) {
		selectController.checked = true;
	} else {
		selectController.checked = false;
	}
}

/**
 * Gère les calculs des vues "modFiche" du visiteur et du comptable
 */
function modFicheCalculs() {
	// Lignes forfait
	var array = [];
	var spans = document.getElementsByTagName("span");
	for (var i = 0; i < spans.length; i++) {
		name = spans[i].getAttribute("data-name");
		if (name.indexOf("lesTotaux") == 0) {
			var id = spans[i].getAttribute("id");
			var quantite = document.getElementById("quantite-" + id);
			if (quantite.tagName.toLowerCase() == 'input') {
				quantite = Number(quantite.value);
			} else {
				quantite = parseFloat(quantite.innerHTML);
			}
			var montant = document.getElementById("montant-" + id);
			if (montant.tagName.toLowerCase() == 'input') {
				montant = Number(montant.value);
			} else {
				montant = parseFloat(montant.innerHTML);
			}
			var total = quantite * montant;
			document.getElementById(id).innerHTML = total.toFixed(2) + '€';
			if (isNaN(total)) {
				document.getElementById(id).innerHTML = 'Erreur';
				document.getElementById(id).style.color = '#DA3C3B';
				document.getElementById(id).style.fontWeight = 'bold';
			} else {
				document.getElementById(id).style.color = '';
				document.getElementById(id).style.fontWeight = '';
			}
			array.push(total);
		}
	}
	var totalArray = 0;
	for (var i = 0; i < array.length; i++) {
		totalArray += array[i];
	}
	
	// Lignes hors horfait
	var arrayHF = [];
	var spansHF = document.getElementsByTagName("span");
	for (var i = 0; i < spansHF.length; i++) {
		name = spansHF[i].getAttribute("data-name");
		if (name.indexOf("lesMontantsHF") == 0) {
			var idHF = spansHF[i].getAttribute("id");
			var montantHF = document.getElementById(idHF);
			montantHF = parseFloat(montantHF.innerHTML);
			arrayHF.push(montantHF);
		}
	}
	var totalArrayHF = 0;
	for (var i = 0; i < arrayHF.length; i++) {
		totalArrayHF += arrayHF[i];
	}
	var txtMontantHF = document.getElementById("txt-montant-hf");
	if (typeof txtMontantHF != 'undefined' && txtMontantHF != null) {
		txtMontantHF = Number(txtMontantHF.value);
	}
	
	// Total fiche frais
	var totalFiche = totalArray + totalArrayHF + txtMontantHF;
	document.getElementById("total-fiche").innerHTML = totalFiche.toFixed(2) + '€';
	if (isNaN(totalFiche)) {
		document.getElementById("total-fiche").innerHTML = 'Erreur';
		document.getElementById("total-fiche").style.color = '#DA3C3B';
	} else {
		document.getElementById("total-fiche").style.color = '';
	}
}

/**
 * Réinitialise le formulaire des frais au forfait
 */
function resetForfait() {
	document.getElementById("forfait").reset();
	modFicheCalculs();
}

/**
 * Gère le champ date du formulaire des frais hors forfait
 */
function dateHorsForfait() {
	$("#txt-date-hf").datepicker({
		showOn: 'button',
		buttonText: '&#x1f4c5;'
	});
	$("#txt-date-hf").datepicker('setDate', new Date());
	$("#txt-date-hf").mask('00/00/0000');
}

/**
 * Réinitialise le formulaire des frais hors forfait
 */
function resetHorsForfait() {
	document.getElementById("hors-forfait").reset();
	$("#txt-date-hf").datepicker('setDate', new Date());
	modFicheCalculs();
}