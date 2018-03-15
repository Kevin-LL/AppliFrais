<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Modèle qui implémente les fonctions d'accès aux données 
*/
class DataAccess extends CI_Model {
// Codeigniter ne supportant pas les requêtes paramétrées
// on se contentera du binding de paramètres anonymes

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    /**
	 * Retourne les éléments nécessaires à l'authentification d'un utilisateur
	 * 
	 * @param $login : le login de l'utilisateur
	 * @param $mdp : le mot de passe de l'utilisateur
	 * @return : l'id, le type de profil, le nom et le prénom sous la forme d'un tableau associatif 
	*/
	public function authentifierUtilisateur($login, $mdp){
		$req = "SELECT id, idProfil, nom, prenom
				FROM utilisateur
				WHERE login = ? AND mdp = ?";
		$rs = $this->db->query($req, array($login, $mdp));
		$ligne = $rs->row_array();
		return $ligne;
	}
	
	/**
	 * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @return : vrai si la fiche existe, ou faux sinon
	*/
	public function existeFiche($idUtilisateur, $mois){
		$ok = false;
		$req = "SELECT COUNT(*) AS nblignesfrais
				FROM fichefrais
				WHERE idUtilisateur = ? AND mois = ?";
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
		$laLigne = $rs->row_array();
		if ($laLigne['nblignesfrais'] != 0){
			$ok = true;
		}
		return $ok;
	}
	
	/**
	 * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés
	 * L'état de la fiche est mis à 'CR'
	 * Les lignes de frais forfait sont affectées de quantités nulles et du montant actuel de FraisForfait
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	*/
	public function creeFiche($idUtilisateur, $mois){
		$req = "INSERT INTO fichefrais (idUtilisateur, mois, nbJustificatifs, montantValide, dateModif, motifRefus, idEtat) 
				VALUES (?, ?, 0, 0, now(), '', 'CR')";
		$this->db->query($req, array($idUtilisateur, $mois));
		$lesFF = $this->getLesFraisForfait();
		foreach($lesFF as $uneLigneFF){
			$unIdFrais = $uneLigneFF['idfrais'];
			$montantU = $uneLigneFF['montant'];
			$req = "INSERT INTO lignefraisforfait (idUtilisateur, mois, idFraisForfait, quantite, montantApplique) 
					VALUES (?, ?, ?, 0, ?)";
			$this->db->query($req, array($idUtilisateur, $mois, $unIdFrais, $montantU));
		 }
	}
	
	/**
	 * Retourne les dates pour lesquelles les fiches de frais ont été créé pour un visiteur donné
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @return : un tableau associatif comportant les dates de création (au format français jj/mm/aaaa) des fiche de frais
	*/
	public function getLesDatesCreation($idUtilisateur){
		$req = "SELECT mois
				FROM fichefrais
				WHERE idUtilisateur = ?
				ORDER BY mois DESC";
		$rs = $this->db->query($req, array($idUtilisateur));
		$laLigne = $rs->row_array();
		while ($laLigne != null){
			$mois = $laLigne['mois'];
			$numAnnee = substr($mois, 0, 4);
			$numMois = substr($mois, 4, 2);
			$numJour = '01';
			$lesDates["$mois"] = $numJour.'/'.$numMois.'/'.$numAnnee;
			$laLigne = $rs->next_row('array');
		}
		return $lesDates;
	}
	
	/**
	 * Passe une fiche de frais en invalide en modifiant son état de "CR" à "IN"
	 * Ne fait rien si l'état initial n'est pas "CR"
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	*/
	public function invalideFiche($idUtilisateur, $mois){
		//met à 'IN' son champs idEtat
		$laFiche = $this->getLesInfosFicheFrais($idUtilisateur, $mois);
		if ($laFiche['idEtat'] == 'CR'){
			$this->majEtatFicheFrais($idUtilisateur, $mois, 'IN');
		}
	}
	
	/**
	 * Retourne les informations d'un utilisateur
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	*/
	public function getLesInfosUtilisateur($idUtilisateur){
		$this->load->model('functionsLib');
		
		$req = "SELECT *
				FROM utilisateur
				WHERE id = ?";
		$rs = $this->db->query($req, array($idUtilisateur));
		$laLigne = $rs->row_array();
		$dateEmbauche = $laLigne['dateEmbauche'];
		$laLigne['dateEmbauche'] = $this->functionsLib->dateAnglaisVersFrancais($dateEmbauche);
		return $laLigne;
	}
	
	/**
	 * Met à jour le mot de passe pour un utilisateur donné
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $leMDP : le mot de passe à modifier
	*/
	public function majSecurite($idUtilisateur, $leMdp){
		$req = "UPDATE utilisateur
				SET mdp = ?
				WHERE id = ?";
		$this->db->query($req, array($leMdp, $idUtilisateur));
	}
	
	/**
	 * Met à jour les informations du lieu de résidence pour un utilisateur donné
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $laResidence : les informations concernant le lieu de résidence
	*/
	public function majResidence($idUtilisateur, $ville, $cp, $adresse){
		$req = "UPDATE utilisateur
				SET ville = ?, cp = ?, adresse = ?
				WHERE id = ?";
		$this->db->query($req, array($ville, $cp, $adresse, $idUtilisateur));
	}
	
	/**
	 * Obtient toutes les fiches (sans détail) d'un visiteur donné 
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	*/
	public function visGetFiches($idUtilisateur){
		$this->load->model('functionsLib');
		
		$req = "SELECT fichefrais.idUtilisateur, fichefrais.mois, fichefrais.montantValide, fichefrais.dateModif, etatfichefrais.id, etatfichefrais.libelle
				FROM fichefrais
				INNER JOIN etatfichefrais ON fichefrais.idEtat = etatfichefrais.id
				WHERE fichefrais.idUtilisateur = ?
				ORDER BY mois DESC";
		$rs = $this->db->query($req, array($idUtilisateur));
		$lesFiches = $rs->result_array();
		$nbLignes = $rs->num_rows();
		for ($i = 0; $i < $nbLignes; $i++){
			$dateModif = $lesFiches[$i]['dateModif'];
			$lesFiches[$i]['dateModif'] = $this->functionsLib->dateAnglaisVersFrancais($dateModif);
		}
		return $lesFiches;
	}
	
	/**
	 * Obtient toutes les fiches (sans détail) selon un état et une recherche définie
	 *
	 * @param $etat : etat de la fiche
	 * @param $recherche : recherche saisie par le comptable
	*/
	public function comGetFiches($etat, $recherche){
		$this->load->model('functionsLib');
		
		$req = "SELECT fichefrais.idUtilisateur, fichefrais.mois, fichefrais.montantValide, fichefrais.dateModif, etatfichefrais.id, etatfichefrais.libelle, utilisateur.nom,
				utilisateur.prenom, utilisateur.login
				FROM fichefrais
				INNER JOIN etatfichefrais ON fichefrais.idEtat = etatfichefrais.id
				INNER JOIN utilisateur ON fichefrais.idUtilisateur = utilisateur.id
				WHERE fichefrais.idEtat = ?
					AND (fichefrais.idUtilisateur LIKE ?
					OR utilisateur.nom LIKE ?
					OR utilisateur.prenom LIKE ?
					OR utilisateur.login LIKE ?)
				ORDER BY mois DESC";
		$rs = $this->db->query($req, array($etat, $recherche, $recherche, $recherche, $recherche));
		$lesFiches = $rs->result_array();
		$nbLignes = $rs->num_rows();
		for ($i = 0; $i < $nbLignes; $i++){
			$dateModif = $lesFiches[$i]['dateModif'];
			$lesFiches[$i]['dateModif'] = $this->functionsLib->dateAnglaisVersFrancais($dateModif);
		}
		return $lesFiches;
	}
	
	/**
	 * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @return : un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état 
	*/	
	public function getLesInfosFicheFrais($idUtilisateur, $mois){
		$req = "SELECT fichefrais.nbJustificatifs, fichefrais.montantValide, fichefrais.dateModif, fichefrais.motifRefus, fichefrais.idEtat, etatfichefrais.libelle AS libEtat
				FROM fichefrais
				INNER JOIN etatfichefrais ON fichefrais.idEtat = etatfichefrais.id 
				WHERE fichefrais.idUtilisateur = ? AND fichefrais.mois = ?";
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
		$laLigne = $rs->row_array();
		return $laLigne;
	}
	
	/**
	 * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
	 * concernées par les deux arguments
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @return : l'id, le libelle, la quantité et le montant sous la forme d'un tableau associatif
	*/
	public function getLesLignesForfait($idUtilisateur, $mois){
		$req = "SELECT lignefraisforfait.quantite, lignefraisforfait.montantApplique AS montant, fraisforfait.id AS idfrais, fraisforfait.libelle
				FROM lignefraisforfait
				INNER JOIN fraisforfait ON fraisforfait.id = lignefraisforfait.idfraisforfait
				WHERE lignefraisforfait.idUtilisateur = ? AND lignefraisforfait.mois = ?
				ORDER BY lignefraisforfait.idfraisforfait";	
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
		$lesLignes = $rs->result_array();
		return $lesLignes;
	}

	/**
	 * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
	 * concernées par les deux arguments
	 * La boucle foreach ne peut être utilisée ici car on procède
	 * à une modification de la structure itérée - transformation du champ date-
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @return : tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
	*/
	public function getLesLignesHorsForfait($idUtilisateur, $mois){
		$this->load->model('functionsLib');

		$req = "SELECT lignefraishorsforfait.id, lignefraishorsforfait.idUtilisateur, lignefraishorsforfait.mois, lignefraishorsforfait.libelle, lignefraishorsforfait.date, 
				lignefraishorsforfait.montant, lignefraishorsforfait.justificatifNom, lignefraishorsforfait.justificatifFichier, lignefraishorsforfait.idEtat, etatfraishorsforfait.libelle AS libEtat
				FROM lignefraishorsforfait
				INNER JOIN etatfraishorsforfait ON lignefraishorsforfait.idEtat = etatfraishorsforfait.id
				WHERE lignefraishorsforfait.idUtilisateur = ? AND lignefraishorsforfait.mois = ?";
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
		$lesLignes = $rs->result_array();
		$nbLignes = $rs->num_rows();
		for ($i = 0; $i < $nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] = $this->functionsLib->dateAnglaisVersFrancais($date);
		}
		return $lesLignes; 
	}
		
	/**
	 * Retourne le nombre de justificatifs d'un visiteur pour un mois donné
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @return : le nombre entier de justificatifs 
	*/
	public function getNbjustificatifs($idUtilisateur, $mois){
		$req = "SELECT COUNT(lignefraishorsforfait.justificatifFichier) AS nb
				FROM lignefraishorsforfait
				WHERE idUtilisateur = ?
					AND mois = ?
					AND justificatifFichier != ''";
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
		$laLigne = $rs->row_array();
		return $laLigne;
	}
	
	/**
	 * Signe une fiche de frais en modifiant son état de "CR" ou "RE" à "CL"
	 * Ne fait rien si l'état initial n'est pas "CR" ou "RE" (voir le contrôleur visiteur)
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	*/
	public function signeFiche($idUtilisateur, $mois){
		//met à 'CL' son champs idEtat
		$this->majEtatFicheFrais($idUtilisateur, $mois, 'CL');
	}
	
	/**
	 * Valide une fiche de frais en modifiant son état de "CL" à "VA",
	 * supprime le motif de refus initialisé s'il y en a un et valide 
	 * tous les frais hors forfait
	 * Ne fait rien si l'état initial n'est pas "CL" (voir le contrôleur comptable)
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	*/
	public function validFiche($idUtilisateur, $mois){
		// suppression du motif de refus
		$req = "UPDATE fichefrais
				SET motifRefus = ''
				WHERE idUtilisateur = ? AND mois = ?";
		$this->db->query($req, array($idUtilisateur, $mois));
		
		// valide tous les frais hors forfait
		$this->majEtatFraisHorsForfait($idUtilisateur, $mois, '%', 'VA');
		
		//met à 'VA' son champs idEtat
		$this->majEtatFicheFrais($idUtilisateur, $mois, 'VA');
	}
	
	/**
	 * Refuse une fiche de frais en modifiant son état de "CL" à "RE" et
	 * ajoute un motif de refus
	 * Ne fait rien si l'état initial n'est pas "CL" (voir le contrôleur comptable)
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @param $leMotifRefus : le motif de refus ajouté
	*/
	public function refuFiche($idUtilisateur, $mois, $leMotifRefus){
		// ajout du motif de refus
		$req = "UPDATE fichefrais
				SET motifRefus = ?
				WHERE idUtilisateur = ? AND mois = ?";
		$this->db->query($req, array($leMotifRefus, $idUtilisateur, $mois));
	
		//met à 'RE' son champs idEtat
		$this->majEtatFicheFrais($idUtilisateur, $mois, 'RE');
	}
	
	/**
	 * Rembourse une fiche de frais en modifiant son état de "VA" à "RB"
	 * Ne fait rien si l'état initial n'est pas "VA" (voir le contrôleur comptable)
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	*/
	public function rembourseFiche($idUtilisateur, $mois){
		//met à 'RB' son champs idEtat
		$this->majEtatFicheFrais($idUtilisateur, $mois, 'RB');
	}
	
	/**
	 * Supprime une fiche de frais, les lignes de frais au forfait et hors forfait pour un visiteur et un mois donné
	 * Ne fait rien si l'état initial n'est pas "IN" (voir le contrôleur visiteur)
	 *
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	*/	
	public function supprimeFiche($idUtilisateur, $mois){
		// suppression des frais hors forfait
		$req = "DELETE
				FROM lignefraishorsforfait
				WHERE idUtilisateur = ? AND mois = ?";
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
		
		// suppression des frais au forfait
		$req = "DELETE
				FROM lignefraisforfait
				WHERE idUtilisateur = ? AND mois = ?";
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
		
		// suppression de la fiche de frais
		$req = "DELETE
				FROM fichefrais
				WHERE idUtilisateur = ? AND mois = ?";
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
	}
	
	/**
	 * Valide un frais hors forfait en modifiant son état de "EA" ou "RE" à "VA"
	 * Ne fait rien si l'état initial n'est pas "EA" ou "RE" (voir le contrôleur comptable)
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @param $idFrais : l'identifiant du frais hors forfait
	*/
	public function validFrais($idUtilisateur, $mois, $idFrais){
		//met à 'VA' son champs idEtat
		$this->majEtatFraisHorsForfait($idUtilisateur, $mois, $idFrais, 'VA');
	}
	
	/**
	 * Refuse un frais hors forfait en modifiant son état de "EA" ou "VA" à "RE"
	 * Ne fait rien si l'état initial n'est pas "EA" ou "VA" (voir le contrôleur comptable)
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @param $idFrais : l'identifiant du frais hors forfait
	*/
	public function refuFrais($idUtilisateur, $mois, $idFrais){
		//met à 'RE' son champs idEtat
		$this->majEtatFraisHorsForfait($idUtilisateur, $mois, $idFrais, 'RE');
	}
	
	/**
	 * Met à jour la table ligneFraisForfait pour un visiteur et
	 * un mois donné en enregistrant les nouvelles quantités
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @param $lesFrais : tableau associatif de clé idFrais et de valeur la quantité pour ce frais
	*/
	public function visMajLignesForfait($idUtilisateur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach ($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "UPDATE lignefraisforfait
					SET quantite = ?
					WHERE idUtilisateur = ?
						AND mois = ?
						AND idfraisforfait = ?";
			$this->db->query($req, array($qte, $idUtilisateur, $mois, $unIdFrais));
		}
	}
	
	/**
	 * Met à jour la table ligneFraisForfait pour un visiteur et
	 * un mois donné en enregistrant les nouveaux montants
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @param $lesMontants : tableau associatif de clé idFrais et de valeur le montant pour ce frais
	*/
	public function comMajLignesForfait($idUtilisateur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach ($lesCles as $unIdFrais){
			$montant = $lesFrais[$unIdFrais];
			$req = "UPDATE lignefraisforfait
					SET montantApplique = ?
					WHERE idUtilisateur = ?
						AND mois = ?
						AND idfraisforfait = ?";
			$this->db->query($req, array($montant, $idUtilisateur, $mois, $unIdFrais));
		}
	}
	
	/**
	 * Modifie le montantValide et la date de modification d'une fiche de frais
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 */
	public function recalculeMontantFiche($idUtilisateur, $mois){
		$totalFiche = $this->totalFiche($idUtilisateur, $mois);
		$req = "UPDATE fichefrais
				SET montantValide = ?, dateModif = now() 
				WHERE idUtilisateur = ? AND mois = ?";
		$this->db->query($req, array($totalFiche, $idUtilisateur, $mois));
	}
	
	/**
	 * Crée un nouveau frais hors forfait pour un visiteur et un mois donné
	 * à partir des informations fournies en paramètre
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @param $libelle : le libellé du frais
	 * @param $date : la date du frais au format français jj/mm/aaaa
	 * @param $montant : le montant du frais
	 * @param $justificatifNom : le nom du justificatif
	 * @param $justificatifFichier : le fichier associé au justificatif
	*/
	public function creeLigneHorsForfait($idUtilisateur, $mois, $libelle, $date, $montant, $justificatifNom, $justificatifFichier){
		$this->load->model('functionsLib');
		
		$dateFr = $this->functionsLib->dateFrancaisVersAnglais($date);
		$req = "INSERT INTO lignefraishorsforfait 
				VALUES ('', ?, ?, ?, ?, ?, ?, ?, 'EA')";
		$this->db->query($req, array($idUtilisateur, $mois, $libelle, $dateFr, $montant, $justificatifNom, $justificatifFichier));
	}
	
	/**
	 * met à jour le nombre de justificatifs de la table fichefrais
	 * pour le mois et le visiteur concerné
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	*/
	public function majNbJustificatifs($idUtilisateur, $mois){
		$nbJustificatifs = $this->dataAccess->getNbjustificatifs($idUtilisateur, $mois)['nb'];
		$req = "UPDATE fichefrais
				SET nbjustificatifs = ?
				WHERE idUtilisateur = ? AND mois = ?";
		$this->db->query($req, array($nbJustificatifs, $idUtilisateur, $mois));	
	}
	
	/**
	 * Supprime le frais hors forfait dont l'id est passé en argument
	 * 
	 * @param $idFrais : l'identifiant du frais hors forfait
	*/
	public function supprimerLigneHorsForfait($idFrais){
		$req = "DELETE 
				FROM lignefraishorsforfait 
				WHERE id = ?";
		$this->db->query($req, array($idFrais));
	}
	
	/**
	 * Retourne les informations d'un frais hors forfait pour un visiteur, un mois et un identifiant donné
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @param $idFrais : l'identifiant du frais hors forfait
	 * @return : les informations d'un frais hors forfait
	*/	
	public function getLesInfosHorsForfait($idUtilisateur, $mois, $idFrais){
		$req = "SELECT *
				FROM lignefraishorsforfait
				WHERE id = ?
					AND idUtilisateur = ?
					AND mois = ?";
		$rs = $this->db->query($req, array($idFrais, $idUtilisateur, $mois));
		$leFrais = $rs->row_array();
		return $leFrais;
	}
	
	/**
	 * Retourne tous les FraisForfait
	 * 
	 * @return : un tableau associatif contenant les fraisForfaits
	*/
	public function getLesFraisForfait(){
		$req = "SELECT id AS idfrais, libelle, montant
				FROM fraisforfait
				ORDER BY id";
		$rs = $this->db->query($req);
		$lesLignes = $rs->result_array();
		return $lesLignes;
	}
	
	/**
	 * Modifie l'état et la date de modification d'une fiche de frais
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @param $etat : le nouvel état de la fiche
	 */
	public function majEtatFicheFrais($idUtilisateur, $mois, $etat){
		$req = "UPDATE fichefrais
				SET idEtat = ?, dateModif = now()
				WHERE idUtilisateur = ? AND mois = ?";
		$this->db->query($req, array($etat, $idUtilisateur, $mois));
	}
	
	/**
	 * Modifie l'état d'un frais hors forfait et la date de modification d'une fiche de frais
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @param $idFrais : l'identifiant du frais hors forfait
	 * @param $etat : le nouvel état du frais
	 */
	public function majEtatFraisHorsForfait($idUtilisateur, $mois, $idFrais, $etat){
		// met à jour la date de modification
		$req = "UPDATE fichefrais
				SET dateModif = now()
				WHERE idUtilisateur = ? AND mois = ?";
		$this->db->query($req, array($idUtilisateur, $mois));
		
		// modifie l'état du frais
		$req = "UPDATE lignefraishorsforfait
				SET idEtat = ?
				WHERE id LIKE ?
					AND idUtilisateur = ?
					AND mois = ?";
		$this->db->query($req, array($etat, $idFrais, $idUtilisateur, $mois));
	}
	
	/**
	 * Calcule le montant total de la fiche pour un visiteur et un mois donnés
	 * 
	 * @param $idUtilisateur : l'identifiant de l'utilisateur
	 * @param $mois : le mois sous la forme aaaamm
	 * @return : le montant total de la fiche
	*/
	public function totalFiche($idUtilisateur, $mois){
		// obtention du total hors forfait
		$req = "SELECT SUM(montant) AS totalHF
				FROM lignefraishorsforfait 
				WHERE idUtilisateur = ? AND mois = ?";
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
		$laLigne = $rs->row_array();
		$totalHF = $laLigne['totalHF'];
		
		// obtention du total forfaitisé
		$req = "SELECT SUM(montantApplique * quantite) AS totalF
				FROM lignefraisforfait 
				WHERE idUtilisateur = ? AND mois = ?";
		$rs = $this->db->query($req, array($idUtilisateur, $mois));
		$laLigne = $rs->row_array();
		$totalF = $laLigne['totalF'];

		return $totalHF + $totalF;
	}
}
?>