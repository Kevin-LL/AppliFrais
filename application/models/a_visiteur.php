<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class A_visiteur extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		
		// chargement du modèle d'accès aux données qui est utile à toutes les méthodes
		$this->load->model('dataAccess');
	}
	
	/**
	 * Accueil du visiteur
	 * La fonction intègre un mécanisme de contrôle d'existence des
	 * fiches de frais sur les 6 derniers mois.
	 * Si l'une d'elle est absente, elle est créée.
	 * Enfin elle contrôle les fiches non signées de plus de 12 mois.
	 */
	public function accueil()
	{
		// chargement du modèle contenant les fonctions génériques
		$this->load->model('functionsLib');
		
		// obtention de l'id de l'utilisateur mémorisé en session
		$idUtilisateur = $this->session->userdata('idUser');
		
		// obtention de la liste des 6 derniers mois (y compris celui ci)
		$lesMois = $this->functionsLib->getSixDerniersMois();
		
		// contrôle de l'existence des 6 dernières fiches et création si nécessaire
		foreach ($lesMois as $unMois)
		{
			if ( ! $this->dataAccess->existeFiche($idUtilisateur, $unMois))
			{
				$this->dataAccess->creeFiche($idUtilisateur, $unMois);
			}
		}
		
		// obtention des dates pour lesquels une fiche de frais a été créée pour le visiteur
		$datesCreation = $this->dataAccess->getLesDatesCreation($idUtilisateur);
		
		// on passe les fiches de frais non signées de plus de 12 mois en invalide
		foreach ($datesCreation as $lesDates)
		{
			if ($this->functionsLib->estDateDepassee($lesDates))
			{
				$lesMois = $this->functionsLib->getMois($lesDates);
				$this->dataAccess->invalideFiche($idUtilisateur, $lesMois);
			}
		}
		
		// envoie de la vue accueil du visiteur
		$this->templates->load('t_visiteur', 'v_visAccueil');
	}
	
	/**
	 * Présente les informations d'un compte visiteur
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $messageAction : message facultatif destiné à notifier l'utilisateur du résultat d'une action précédemment exécutée
	 * @param $messageErreur : message facultatif destiné à notifier l'utilisateur d'une erreur
	 */
	public function monCompte($idUtilisateur, $messageAction = null, $messageErreur = null)
	{
		$data['notifySuccess'] = $messageAction;
		$data['notifyError'] = $messageErreur;
		$data['infosUtil'] = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
		
		$this->templates->load('t_visiteur', 'v_visMonCompte', $data);	
	}
	
	/**
	 * Modifie le mot de passe associé à un visiteur donné
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $leMdp : le mot de passe à mettre à jour
	 */
	public function majSecurite($idUtilisateur, $leMdp)
	{
		$this->dataAccess->majSecurite($idUtilisateur, $leMdp);
	}
	
	/**
	 * Modifie les informations du lieu de résidence associées à un visiteur donné
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $uneResidence : les informations concernant le lieu de résidence
	 */
	public function majResidence($idUtilisateur, $uneResidence)
	{
		$ville = $uneResidence['ville'];
		$cp = $uneResidence['cp'];
		$adresse = $uneResidence['adresse'];
		
		$this->dataAccess->majResidence($idUtilisateur, $ville, $cp, $adresse);
	}
	
	/**
	 * Liste les fiches existantes du visiteur connecté et
	 * donne accès aux fonctionnalités associées
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $messageAction : message facultatif destiné à notifier l'utilisateur du résultat d'une action précédemment exécutée
	 * @param $messageErreur : message facultatif destiné à notifier l'utilisateur d'une erreur
	 */
	public function mesFiches($idUtilisateur, $messageAction = null, $messageErreur = null)
	{
		$data['notifySuccess'] = $messageAction;
		$data['notifyError'] = $messageErreur;
		$data['mesFiches'] = $this->dataAccess->visGetFiches($idUtilisateur);		
		
		$this->templates->load('t_visiteur', 'v_visMesFiches', $data);	
	}
	
	/**
	 * Présente le détail de la fiche sélectionnée
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche à consulter
	 */
	public function voirFiche($idUtilisateur, $mois)
	{
		$data['moisFiche'] = $mois;
		$data['infosFiche'] = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idUtilisateur, $mois);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idUtilisateur, $mois);
		$data['nbJustificatifs'] = $this->dataAccess->getNbjustificatifs($idUtilisateur, $mois)['nb'];
		
		$this->templates->load('t_visiteur', 'v_visVoirListeFrais', $data);
	}
	
	/**
	 * Présente le motif de refus de la fiche sélectionnée
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche dont on souhaite consulter le motif de refus
	 */
	public function voirMotifRefus($idUtilisateur, $mois)
	{
		$data['moisFiche'] = $mois;
		$data['leMotifRefus'] = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois)['motifRefus'];
			
		$this->templates->load('t_visiteur', 'v_visVoirMotifRefus', $data);
	}
	
	/**
	 * Présente le détail de la fiche sélectionnée et donne
	 * accés à la modification du contenu de cette fiche.
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche à modifier
	 * @param $messageAction : message facultatif destiné à notifier l'utilisateur du résultat d'une action précédemment exécutée
	 * @param $messageErreur : message facultatif destiné à notifier l'utilisateur d'une erreur
	 */
	public function modFiche($idUtilisateur, $mois, $messageAction = null, $messageErreur = null)
	{
		$data['notifySuccess'] = $messageAction;
		$data['notifyError'] = $messageErreur;
		$data['moisFiche'] = $mois;
		$data['infosFiche'] = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idUtilisateur, $mois);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idUtilisateur, $mois);
		$data['nbJustificatifs'] = $this->dataAccess->getNbjustificatifs($idUtilisateur, $mois)['nb'];
		
		$this->templates->load('t_visiteur', 'v_visModListeFrais', $data);
	}
	
	/**
	 * Signe une fiche de frais en changeant son état
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche à signer
	 */
	public function signeFiche($idUtilisateur, $mois)
	{
		$this->dataAccess->signeFiche($idUtilisateur, $mois);
	}
	
	/**
	 * Présente le détail de la fiche sélectionnée en format pdf
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche à imprimer
	 */
	public function impFiche($idUtilisateur, $mois)
	{
		$data['moisFiche'] = $mois;
		$data['infosFiche'] = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idUtilisateur, $mois);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idUtilisateur, $mois);
		$data['nbJustificatifs'] = $this->dataAccess->getNbjustificatifs($idUtilisateur, $mois)['nb'];
		
		$this->templates->load('t_visiteur', 'v_visImpListeFrais', $data);
	}
	
	/**
	 * Supprimer une fiche de frais
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche à supprimer
	 */
	public function supprFiche($idUtilisateur, $mois)
	{
		$this->dataAccess->supprimeFiche($idUtilisateur, $mois);
	}
	
	/**
	 * Modifie les quantités associées aux frais forfaitisés dans une fiche donnée
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche concernée
	 * @param $lesQuantites : les quantités liées à chaque type de frais, sous la forme d'un tableau
	 */
	public function majForfait($idUtilisateur, $mois, $lesQuantites)
	{
		$this->dataAccess->visMajLignesForfait($idUtilisateur, $mois, $lesQuantites);
		$this->dataAccess->recalculeMontantFiche($idUtilisateur, $mois);
	}
	
	/**
	 * Ajoute une ligne de frais hors forfait dans une fiche donnée
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche concernée
	 * @param $uneLigne : les informations du frais à ajouter, sous la forme d'un tableau
	 */
	public function ajouteFrais($idUtilisateur, $mois, $uneLigne)
	{
		$dateFrais = $uneLigne['dateFrais'];
		$libelle = $uneLigne['libelle'];
		$montant = $uneLigne['montant'];
		$justificatifNom = $uneLigne['justificatifNom'];
		$justificatifFichier = $uneLigne['justificatifFichier'];
		
		$this->dataAccess->creeLigneHorsForfait($idUtilisateur, $mois, $libelle, $dateFrais, $montant, $justificatifNom, $justificatifFichier);
		$this->dataAccess->majNbJustificatifs($idUtilisateur, $mois);
		$this->dataAccess->recalculeMontantFiche($idUtilisateur, $mois);
	}
	
	/**
	 * Supprime une ligne de frais hors forfait dans une fiche donnée
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche concernée
	 * @param $idLigneFrais : l'id de la ligne à supprimer
	 */
	public function supprLigneFrais($idUtilisateur, $mois, $idLigneFrais)
	{
		$this->dataAccess->supprimerLigneHorsForfait($idLigneFrais);
		$this->dataAccess->majNbJustificatifs($idUtilisateur, $mois);
		$this->dataAccess->recalculeMontantFiche($idUtilisateur, $mois);
	}
}