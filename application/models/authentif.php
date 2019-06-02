<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentif extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	/**
	 * Teste si un quelconque utilisateur est connecté
	 * 
	 * @return vrai ou faux
	 */
	public function estConnecte()
	{
		return $this->session->userdata('idUser');
	}
	
	/**
	 * Enregistre dans une variable session les infos d'un utilisateur
	 * 
	 * @param $idUser
	 * @param $idProfil
	 * @param $nom
	 * @param $prenom
	 */
	public function connecter($idUser, $idProfil, $nom, $prenom)
	{
		$authUser = array(
			'idUser' => $idUser,
			'idProfil' => $idProfil,
			'nom' => $nom,
			'prenom' => $prenom
		);
		
		$this->session->set_userdata($authUser);
	}
	
	/**
	 * Détruit la session active et redirige vers le contrôleur par défaut
	 */
	public function deconnecter()
	{
		$authUser = array(
			'idUser' => '',
			'idProfil' => '',
			'nom' => '',
			'prenom' => ''
		);
		
		$this->session->unset_userdata($authUser);
		$this->session->sess_destroy();
		
		$this->load->helper('url');
		redirect('/c_default');
	}
	
	/**
	 * Vérifie en base de données si les informations de connexions sont correctes
	 * 
	 * @return : renvoie l'id, le type de profil, le nom et le prenom de l'utilisateur dans un tableau s'il est reconnu, sinon un tableau vide.
	 */
	public function authentifier($login)
	{
		$this->load->model('dataAccess');
		
		$authUser = $this->dataAccess->authentifierUtilisateur($login);
		
		return $authUser;
	}
}