/**
 * Gère l'affichage du menu lorsque la page est en mode responsive
 */
function display(){
	if (document.getElementById("menu").style.display == 'none' || document.getElementById("menu").style.display == ''){
		document.getElementById("menu").style.display = 'block';
		document.getElementById("contenu").style.display = 'none';
	}else{
		document.getElementById("menu").style.display = 'none';
		document.getElementById("contenu").style.display = 'flex';
	}
}

/**
 * Ferme les zones de notifications
 */
function closeNotify(notify){
	notify.parentNode.style.display = 'none';
}

/**
 * Gère les caractères saisis pour le code postal, si un des caractères n'est pas numérique
 * la couleur de fond de l'élément "input" change
 */
function modCodePostal(){
	var CP = document.getElementById("txtCP").value;
	if (isNaN(CP)){
		document.getElementById("txtCP").style.backgroundColor = '#FF7C66';
	}else{
		document.getElementById("txtCP").style.backgroundColor = '';
	}
}

/**
 * Contrôle si le code postal est un nombre
 */
function checkCodePostal(){
	var CP = document.getElementById("txtCP").value;
	if (isNaN(CP)){
		alert("Caractère(s) non valide(s) !");
		return false;
	}else{
		return true;
	}
}

/**
 * Réinitialise le formulaire du lieu de résidence
 */
function resetResidence(){
	document.getElementById("residence").reset();
	modCodePostal();
}

/**
 * Gère les calculs des vues "modFiche" du visiteur et du comptable
 */
function modFicheCalcul(){
	//Lignes forfait
	var array = [];
	var inputs = document.getElementsByTagName("input");
	for (i = 0; i < inputs.length; i++){
		name = inputs[i].getAttribute("name");
		if (name.indexOf("lesFrais") == 0){
			var id = inputs[i].getAttribute("id");
			var quantite = document.getElementById(id).value;
			var montant = document.getElementById("montant" + id).value;
			var total = quantite * montant;
			document.getElementById("total" + id).innerHTML = total.toFixed(2) + "€";
			array.push(total);
			if (isNaN(quantite)){
				document.getElementById(id).style.backgroundColor = '#FF7C66';
			}else{
				document.getElementById(id).style.backgroundColor = '';
			}
			if (isNaN(montant)){
				document.getElementById("montant" + id).style.backgroundColor = '#FF7C66';
			}else{
				document.getElementById("montant" + id).style.backgroundColor = '';
			}
			if (isNaN(total)){
				document.getElementById("total" + id).innerHTML = "Erreur";
			}
		}
	}
	var totalArray = 0;
	for (var i in array){
		totalArray += array[i];
	}

	//Lignes hors horfait
	var arrayHF = [];
	var labels = document.getElementsByTagName("label");
	for (i = 0; i < labels.length; i++){
		name = labels[i].getAttribute("name");
		if (name.indexOf("lesMontantsHF") == 0){
			var idHF = labels[i].getAttribute("id");
			var montantHF = document.getElementById(idHF).innerHTML;
			var parsedMontantHF = parseFloat(montantHF);
			arrayHF.push(parsedMontantHF);
		}
	}
	var totalArrayHF = 0;
	for (var i in arrayHF){
		totalArrayHF += arrayHF[i];
	}
	var txtMontantHF = document.getElementById("txtMontantHF");
	if (typeof txtMontantHF !== 'undefined' && txtMontantHF !== null){
		txtMontantHF = txtMontantHF.value;
		if (isNaN(txtMontantHF)){
			document.getElementById("txtMontantHF").style.backgroundColor = '#FF7C66';
		}else{
			document.getElementById("txtMontantHF").style.backgroundColor = '';
		}
	}

	//Total fiche frais
	var totalFinal = Number(totalArray) + Number(totalArrayHF) + Number(txtMontantHF);
	document.getElementById("totalFinal").innerHTML = totalFinal.toFixed(2) + "€";
	if (isNaN(totalFinal)){
		document.getElementById("totalFinal").innerHTML = "Erreur";
	}
}

/**
 * Contrôle si le total de la fiche de frais est un nombre
 */
function checkTotalFicheFrais(){
	var totalFinal = document.getElementById("totalFinal").innerHTML;
	var parsedTotalFinal = parseFloat(totalFinal);
	if (isNaN(parsedTotalFinal)){
		alert("Caractère(s) non valide(s) !");
		return false;
	}else{
		return true;
	}
}

/**
 * Contrôle le nombre de frais hors forfait saisis (limités à 10)
 */
function checkLignesHorsForfait(){
	var table = document.getElementById("horsForfaitListe").getElementsByTagName("tbody")[0];
	var rowCount = table.rows.length;
	if (rowCount <= 9){
		return true;
	}else{
		alert("Maximum de lignes hors forfait atteint !");
		return false;
	}
}

/**
 * Contrôle le nombre de caractères dans le nom du fichier donné comme justificatif
 * (limités à 35 caractères)
 */
function checkJustificatif(){
	var filename = document.getElementById("buttonJustificatifHF").value;
	filename = filename.replace(/^.*[\\\/]/, '');
	if (filename.length <= 35){
		return true;
	}else{
		alert("Le nom des fichiers est limité à 35 caractères (avec extension).");
		return false;
	}
}

/**
 * Réinitialise le formulaire des frais au forfait
 */
function resetForfait(){
	document.getElementById("forfait").reset();
	modFicheCalcul();
}

/**
 * Réinitialise le formulaire des frais hors forfait
 */
function resetHorsForfait(){
	document.getElementById("horsforfait").reset();
	$(".datepicker").datepicker('setDate', new Date()); //Jquery
	modFicheCalcul();
}
