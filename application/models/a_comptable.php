<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class A_comptable extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		
		// chargement du modèle d'accès aux données qui est utile à toutes les méthodes
		$this->load->model('dataAccess');
	}
	
	/**
	 * Accueil du comptable
	 */
	public function accueil()
	{
		$this->templates->load('t_comptable', 'v_comAccueil');
	}
	
	/**
	 * Présente les informations d'un compte comptable
	 * 
	 * @param $idUtilisateur : l'id du comptable
	 * @param $messageAction : message facultatif destiné à notifier l'utilisateur du résultat d'une action précédemment exécutée
	 * @param $messageErreur : message facultatif destiné à notifier l'utilisateur d'une erreur
	 */
	public function monCompte($idUtilisateur, $messageAction = null, $messageErreur = null)
	{
		$data['notifySuccess'] = $messageAction;
		$data['notifyError'] = $messageErreur;
		$data['infosUtil'] = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
		
		$this->templates->load('t_comptable', 'v_comMonCompte', $data);	
	}
	
	/**
	 * Modifie le mot de passe associé à un comptable donné
	 * 
	 * @param $idUtilisateur : l'id du comptable
	 * @param $leMdp : le mot de passe à mettre à jour
	 */
	public function majSecurite($idUtilisateur, $leMdp)
	{
		$this->dataAccess->majSecurite($idUtilisateur, $leMdp);
	}
	
	/**
	 * Modifie les informations du lieu de résidence associées à un comptable donné
	 * 
	 * @param $idUtilisateur : l'id du comptable
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
	 * Liste les fiches signées à valider pour le comptable connecté et
	 * donne accès aux fonctionnalités associées
	 * 
	 * @param $visiteur : chaîne de caractères permettant au comptable de trier les fiches de frais par visiteur
	 * @param $mois : chaîne de caractères permettant au comptable de trier les fiches de frais par mois
	 * @param $messageAction : message facultatif destiné à notifier l'utilisateur du résultat d'une action précédemment exécutée
	 * @param $messageErreur : message facultatif destiné à notifier l'utilisateur d'une erreur
	 */
	public function validationFiches($visiteur = '%', $mois = '%', $messageAction = null, $messageErreur = null)
	{
		$data['notifySuccess'] = $messageAction;
		$data['notifyError'] = $messageErreur;
		$data['lesVisiteurs'] = $this->dataAccess->getVisiteurs();
		$data['validationFiches'] = $this->dataAccess->comGetFiches('CL', $visiteur, $mois);
		
		$this->templates->load('t_comptable', 'v_comValidationFiches', $data);
	}
	
	/**
	 * Liste les fiches mises en paiement à rembourser pour le comptable connecté et
	 * donne accès aux fonctionnalités associées
	 * 
	 * @param $visiteur : chaîne de caractères permettant au comptable de trier les fiches de frais par visiteur
	 * @param $mois : chaîne de caractères permettant au comptable de trier les fiches de frais par mois
	 * @param $messageAction : message facultatif destiné à notifier l'utilisateur du résultat d'une action précédemment exécutée
	 * @param $messageErreur : message facultatif destiné à notifier l'utilisateur d'une erreur
	 */
	public function paiementFiches($visiteur = '%', $mois = '%', $messageAction = null, $messageErreur = null)
	{
		$data['notifySuccess'] = $messageAction;
		$data['notifyError'] = $messageErreur;
		$data['lesVisiteurs'] = $this->dataAccess->getVisiteurs();
		$data['paiementFiches'] = $this->dataAccess->comGetFiches('VA', $visiteur, $mois);
		
		$this->templates->load('t_comptable', 'v_comPaiementFiches', $data);
	}
	
	/**
	 * Synthèse des fiches remboursées pour le comptable connecté et
	 * donne accès aux fonctionnalités associées
	 * 
	 * @param $visiteur : chaîne de caractères permettant au comptable de trier les fiches de frais par visiteur
	 * @param $mois : chaîne de caractères permettant au comptable de trier les fiches de frais par mois
	 */
	public function syntheseFiches($visiteur = '%', $mois = '%')
	{
		$data['lesVisiteurs'] = $this->dataAccess->getVisiteurs();
		$data['syntheseFiches'] = $this->dataAccess->comGetFiches('RB', $visiteur, $mois);
		
		$this->templates->load('t_comptable', 'v_comSyntheseFiches', $data);
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
		$data['infosUtil'] = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
		$data['infosFiche'] = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idUtilisateur, $mois);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idUtilisateur, $mois);
		$data['nbJustificatifs'] = $this->dataAccess->getNbjustificatifs($idUtilisateur, $mois)['nb'];
		
		$this->templates->load('t_comptable', 'v_comVoirListeFrais', $data);
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
		$data['infosUtil'] = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
		$data['infosFiche'] = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
		$data['lesFraisForfait'] = $this->dataAccess->getLesLignesForfait($idUtilisateur, $mois);
		$data['lesFraisHorsForfait'] = $this->dataAccess->getLesLignesHorsForfait($idUtilisateur, $mois);
		$data['nbJustificatifs'] = $this->dataAccess->getNbjustificatifs($idUtilisateur, $mois)['nb'];
		
		$this->templates->load('t_comptable', 'v_comModListeFrais', $data);
	}
	
	/**
	 * Valide une fiche de frais en changeant son état
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche à valider
	 */
	public function validFiche($idUtilisateur, $mois)
	{
		$this->dataAccess->validFiche($idUtilisateur, $mois);
	}
	
	/**
	 * Permet au comptable d'ajouter un motif de refus à la fiche sélectionnée
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche dont on souhaite ajouter un motif de refus
	 * @param $messageErreur : message facultatif destiné à notifier l'utilisateur d'une erreur
	 */
	public function ajouterMotifRefus($idUtilisateur, $mois, $messageErreur = null)
	{
		$data['notifyError'] = $messageErreur;
		$data['moisFiche'] = $mois;
		$data['infosUtil'] = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
		$data['leMotifRefus'] = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois)['motifRefus'];
		
		$this->templates->load('t_comptable', 'v_comAjouterMotifRefus', $data);
	}
	
	/**
	 * Refuse une fiche de frais en changeant son état et met à jour
	 * le motif de refus
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche à refuser
	 * @param $leMotifRefus : le motif de refus de la fiche
	 */
	public function refuFiche($idUtilisateur, $mois, $leMotifRefus)
	{
		$this->dataAccess->refuFiche($idUtilisateur, $mois, $leMotifRefus);
	}
	
	/**
	 * Rembourse une fiche de frais en changeant son état
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche à rembourser
	 */
	public function rembourseFiche($idUtilisateur, $mois)
	{
		$this->dataAccess->rembourseFiche($idUtilisateur, $mois);
	}
	
	/**
	 * Modifie les montants associés aux frais forfaitisés dans une fiche donnée
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche concernée
	 * @param $lesMontants : les montants liés à chaque type de frais, sous la forme d'un tableau
	 */
	public function majForfait($idUtilisateur, $mois, $lesMontants)
	{
		$this->dataAccess->comMajLignesForfait($idUtilisateur, $mois, $lesMontants);
		$this->dataAccess->recalculeMontantFiche($idUtilisateur, $mois);
	}
	
	/**
	 * Valide un frais hors forfait en changeant son état
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche concernée
	 * @param $idLigneFrais : l'id de la ligne à valider
	 */
	public function validFrais($idUtilisateur, $mois, $idLigneFrais)
	{
		$this->dataAccess->validFrais($idUtilisateur, $mois, $idLigneFrais);
	}
	
	/**
	 * Refuse un frais hors forfait en changeant son état
	 * 
	 * @param $idUtilisateur : l'id du visiteur
	 * @param $mois : le mois de la fiche concernée
	 * @param $idLigneFrais : l'id de la ligne à refuser
	 */
	public function refuFrais($idUtilisateur, $mois, $idLigneFrais)
	{
		$this->dataAccess->refuFrais($idUtilisateur, $mois, $idLigneFrais);
	}
}