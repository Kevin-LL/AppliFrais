/**
 * Gère l'affichage du menu lorsque la page est en mode responsive
 */
function displayMenu() {
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
 * Sélectionne toutes les fiches de frais.
 * Si toutes les fiches de frais sont cochées elles seront décochées et inversement
 */
function selectFiches() {
	var selectController = document.getElementById("selectController");
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
 * Si toutes les fiches de frais sont cochées l'élément "selectController" sera lui aussi coché,
 * sinon il sera décoché
 */
function checkSelect() {
	var selectController = document.getElementById("selectController");
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
 * Contrôle si le code postal est un nombre.
 * Si le code postal n'est pas un nombre change la couleur de l'élément "txtCP",
 * sinon ne change rien
 */
function checkCodePostal() {
	var CP = document.getElementById("txtCP");
	CP = Number(CP.value);
	if (isNaN(CP)) {
		document.getElementById("txtCP").style.backgroundColor = '#FF7C66';
	} else {
		document.getElementById("txtCP").style.backgroundColor = '';
	}
}

/**
 * Réinitialise le formulaire du lieu de résidence
 */
function resetResidence() {
	document.getElementById("residence").reset();
	checkCodePostal();
}

/**
 * Gère les calculs des vues "modFiche" du visiteur et du comptable
 */
function modFicheCalcul() {
	// Lignes forfait
	var array = [];
	var spans = document.getElementsByTagName("span");
	for (var i = 0; i < spans.length; i++) {
		name = spans[i].getAttribute("name");
		if (name.indexOf("lesTotaux") == 0) {
			var id = spans[i].getAttribute("id");
			var quantite = document.getElementById("quantite" + id);
			if (quantite.tagName.toLowerCase() == 'input') {
				quantite = Number(quantite.value);
				if (isNaN(quantite)) {
					document.getElementById("quantite" + id).style.backgroundColor = '#FF7C66';
				} else {
					document.getElementById("quantite" + id).style.backgroundColor = '';
				}
			} else {
				quantite = parseFloat(quantite.innerHTML);
			}
			var montant = document.getElementById("montant" + id);
			if (montant.tagName.toLowerCase() == 'input') {
				montant = Number(montant.value);
				if (isNaN(montant)) {
					document.getElementById("montant" + id).style.backgroundColor = '#FF7C66';
				} else {
					document.getElementById("montant" + id).style.backgroundColor = '';
				}
			} else {
				montant = parseFloat(montant.innerHTML);
			}
			var total = quantite * montant;
			document.getElementById(id).innerHTML = total.toFixed(2) + '€';
			if (isNaN(total)) {
				document.getElementById(id).innerHTML = 'Erreur';
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
	var spans = document.getElementsByTagName("span");
	for (var i = 0; i < spans.length; i++) {
		name = spans[i].getAttribute("name");
		if (name.indexOf("lesMontantsHF") == 0) {
			var idHF = spans[i].getAttribute("id");
			var montantHF = document.getElementById(idHF);
			montantHF = parseFloat(montantHF.innerHTML);
			arrayHF.push(montantHF);
		}
	}
	var totalArrayHF = 0;
	for (var i = 0; i < arrayHF.length; i++) {
		totalArrayHF += arrayHF[i];
	}
	var txtMontantHF = document.getElementById("txtMontantHF");
	if (typeof txtMontantHF != 'undefined' && txtMontantHF != null) {
		txtMontantHF = Number(txtMontantHF.value);
		if (isNaN(txtMontantHF)) {
			document.getElementById("txtMontantHF").style.backgroundColor = '#FF7C66';
		} else {
			document.getElementById("txtMontantHF").style.backgroundColor = '';
		}
	}
	
	// Total fiche frais
	var totalFinal = totalArray + totalArrayHF + txtMontantHF;
	document.getElementById("totalFinal").innerHTML = totalFinal.toFixed(2) + '€';
	if (isNaN(totalFinal)) {
		document.getElementById("totalFinal").innerHTML = 'Erreur';
	}
}

/**
 * Réinitialise le formulaire des frais au forfait
 */
function resetForfait() {
	document.getElementById("forfait").reset();
	modFicheCalcul();
}

/**
 * Gère le champ date du formulaire des frais hors forfait
 */
function dateHorsForfait() {
	$(".datepicker").mask('00/00/0000');
	$(".datepicker").datepicker();
	$(".datepicker").datepicker('setDate', new Date());
}

/**
 * Réinitialise le formulaire des frais hors forfait
 */
function resetHorsForfait() {
	document.getElementById("horsforfait").reset();
	$(".datepicker").datepicker('setDate', new Date());
	modFicheCalcul();
}