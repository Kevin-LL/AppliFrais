/**
 * Gère l'affichage du menu lorsque la page est en mode responsive
 */
function displayMenu() {
	if (document.getElementById("menu").style.display == 'none' || document.getElementById("menu").style.display == '') {
		document.getElementById("menu").style.display = 'block';
		document.getElementById("contenu").style.display = 'none';
	} else {
		document.getElementById("menu").style.display = 'none';
		document.getElementById("contenu").style.display = 'flex';
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
	for (i = 0; i < checkboxes.length; i++) {
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
 * Si aucune fiche de frais n'est cochée affiche une notification et retourne "false",
 * sinon envoie une demande de confirmation
 */
function validSelect() {
	var checkboxes = document.getElementsByTagName("input");
	var rienEstCoche = true;
	for (i = 0; i < checkboxes.length; i++) {
		name = checkboxes[i].getAttribute("name");
		if (name.indexOf("lesFiches") == 0) {
			if (checkboxes[i].checked == true) {
				rienEstCoche = false;
			}
		}
	}
	if (rienEstCoche == true) {
		alert("Aucune fiche n'a été sélectionnée !");
		return false;
	} else {
		var confirmer = confirm("Voulez-vous vraiment confirmer la sélection ?");
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
	for (i = 0; i < checkboxes.length; i++) {
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
	var CP = document.getElementById("txtCP").value;
	if (isNaN(CP)) {
		document.getElementById("txtCP").style.backgroundColor = '#FF7C66';
	} else {
		document.getElementById("txtCP").style.backgroundColor = '';
	}
}

/**
 * Valide le code postal.
 * Si le code postal n'est pas un nombre affiche une notification et retourne "false",
 * sinon retourne "true"
 */
function validCodePostal() {
	var CP = document.getElementById("txtCP").value;
	if (isNaN(CP)) {
		alert("Caractère(s) non valide(s) !");
		return false;
	} else {
		return true;
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
	var inputs = document.getElementsByTagName("input");
	for (i = 0; i < inputs.length; i++) {
		name = inputs[i].getAttribute("name");
		if (name.indexOf("lesFrais") == 0) {
			var id = inputs[i].getAttribute("id");
			var quantite = document.getElementById(id).value;
			var montant = document.getElementById("montant" + id).value;
			var total = quantite * montant;
			document.getElementById("total" + id).innerHTML = total.toFixed(2) + "€";
			array.push(total);
			if (isNaN(quantite)) {
				document.getElementById(id).style.backgroundColor = '#FF7C66';
			} else {
				document.getElementById(id).style.backgroundColor = '';
			}
			if (isNaN(montant)) {
				document.getElementById("montant" + id).style.backgroundColor = '#FF7C66';
			} else {
				document.getElementById("montant" + id).style.backgroundColor = '';
			}
			if (isNaN(total)) {
				document.getElementById("total" + id).innerHTML = "Erreur";
			}
		}
	}
	var totalArray = 0;
	for ( var i in array) {
		totalArray += array[i];
	}
	
	// Lignes hors horfait
	var arrayHF = [];
	var labels = document.getElementsByTagName("label");
	for (i = 0; i < labels.length; i++) {
		name = labels[i].getAttribute("name");
		if (name.indexOf("lesMontantsHF") == 0) {
			var idHF = labels[i].getAttribute("id");
			var montantHF = parseFloat(document.getElementById(idHF).innerHTML);
			arrayHF.push(montantHF);
		}
	}
	var totalArrayHF = 0;
	for ( var i in arrayHF) {
		totalArrayHF += arrayHF[i];
	}
	var txtMontantHF = document.getElementById("txtMontantHF");
	if (typeof txtMontantHF != 'undefined' && txtMontantHF != null) {
		txtMontantHF = txtMontantHF.value;
		if (isNaN(txtMontantHF)) {
			document.getElementById("txtMontantHF").style.backgroundColor = '#FF7C66';
		} else {
			document.getElementById("txtMontantHF").style.backgroundColor = '';
		}
	}
	
	// Total fiche frais
	var totalFinal = Number(totalArray) + Number(totalArrayHF) + Number(txtMontantHF);
	document.getElementById("totalFinal").innerHTML = totalFinal.toFixed(2) + "€";
	if (isNaN(totalFinal)) {
		document.getElementById("totalFinal").innerHTML = "Erreur";
	}
}

/**
 * Valide le total de la fiche de frais.
 * Si le total de la fiche de frais n'est pas un nombre affiche une notification et retourne "false",
 * sinon retourne "true"
 */
function validTotalFicheFrais() {
	var totalFinal = parseFloat(document.getElementById("totalFinal").innerHTML);
	if (isNaN(totalFinal)) {
		alert("Caractère(s) non valide(s) !");
		return false;
	} else {
		return true;
	}
}

/**
 * Valide le nombre de caractères dans le nom du fichier donné comme justificatif.
 * Si le nombre de caractères est inférieur à 35 retourne "true",
 * sinon affiche une notification et retourne "false"
 */
function validJustificatif() {
	var filename = document.getElementById("buttonJustificatifHF").value;
	filename = filename.replace(/^.*[\\\/]/, '');
	if (filename.length <= 35) {
		return true;
	} else {
		alert("Le nom des fichiers est limité à 35 caractères (avec extension).");
		return false;
	}
}

/**
 * Valide le nombre de frais hors forfait saisis.
 * Si le nombre de frais hors forfait est inférieur à 9 (soit 10 en partant de 0) retourne "true",
 * sinon affiche une notification et retourne "false"
 */
function validLignesHorsForfait() {
	var table = document.getElementById("horsForfaitListe").getElementsByTagName("tbody")[0];
	var rowCount = table.rows.length;
	if (rowCount <= 9) {
		return true;
	} else {
		alert("Maximum de lignes hors forfait atteint !");
		return false;
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
 * Réinitialise le formulaire des frais hors forfait
 */
function resetHorsForfait() {
	document.getElementById("horsforfait").reset();
	$(".datepicker").datepicker('setDate', new Date()); // Jquery
	modFicheCalcul();
}