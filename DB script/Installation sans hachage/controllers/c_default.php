<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contrôleur par défaut de l'application
 * Si aucune spécification de contrôleur n'est précisée dans l'URL du navigateur
 * c'est le contrôleur par défaut qui sera invoqué. Son rôle est :
 * 		+ d'orienter vers le bon contrôleur selon la situation
 * 		+ de traiter le retour du formulaire de connexion
 */
class C_default extends CI_Controller {
	
	/**
	 * Fonctionnalité par défaut du contrôleur.
	 * Vérifie l'existence d'une connexion :
	 * Soit elle existe et on redirige vers le contrôleur de VISITEUR ou COMPTABLE,
	 * soit elle n'existe pas et on envoie la vue de connexion
	 */
	public function index()
	{
		// charge le modèle authentif
		$this->load->model('authentif');
		
		if ( ! $this->authentif->estConnecte())
		{
			$data = array();
			$this->templates->load('t_default', 'v_connexion', $data);
		}
		elseif ($this->session->userdata('idProfil') == 'VIS')
		{
			$this->load->helper('url');
			redirect('/c_visiteur');
		}
		elseif ($this->session->userdata('idProfil') == 'COM')
		{
			$this->load->helper('url');
			redirect('/c_comptable');
		}
	}
	
	/**
	 * Traite le retour du formulaire de connexion afin de connecter l'utilisateur
	 * s'il est reconnu
	 */
	public function connecter()
	{
		// si une requête "post" est lancée
		if ($this->input->method() == 'post')
		{
			// charge la bibliothèque Form_validation
			// charge le modèle dataAccess et authentif
			$this->load->library('form_validation');
			$this->load->model('dataAccess');
			$this->load->model('authentif');
			
			// configuration des champs du formulaire
			$this->form_validation->set_rules('login', 'Login', 'required|max_length[31]');
			$this->form_validation->set_rules('mdp', 'Mot de passe', 'required|max_length[60]');
			
			// si validation des champs du formulaire
			if ($this->form_validation->run())
			{
				// obtention des données postées : $login, $mdp
				$login = $this->input->post('login');
				$mdp = $this->input->post('mdp');
				
				$currentMdp = $this->dataAccess->getMdpUtilisateur($login);
				
				// si $mdp est égal au mdp de l'utilisateur
				if ($mdp == $currentMdp['mdp'])
				{
					// liste des profils autorisés
					$authorized = array('VIS', 'COM');
					
					$authUser = $this->authentif->authentifier($login);
					
					// si le profil de l'utilisateur est autorisé
					if (in_array($authUser['idProfil'], $authorized))
					{
						// initialisation de la session utilisateur et on active index
						$this->authentif->connecter($authUser['id'], $authUser['idProfil'], $authUser['nom'], $authUser['prenom']);
						$this->index();
					}
					else
					{
						// sinon on active connexion
						$data = array('erreur' => '<li>Vous n\'êtes pas autorisé à vous connecter à cette application.</li>');
						$this->templates->load('t_default', 'v_connexion', $data);
					}
				}
				else
				{
					// sinon on active connexion
					$data = array('erreur' => '<li>Login ou mot de passe incorrect.</li>');
					$this->templates->load('t_default', 'v_connexion', $data);
				}
			}
			else
			{
				// sinon on active connexion
				$data = array('erreur' => validation_errors('<li>', '</li>'));
				$this->templates->load('t_default', 'v_connexion', $data);
			}
		}
		else
		{
			// sinon on envoie l'erreur 404
			show_404();
		}
	}
}