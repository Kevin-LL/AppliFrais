<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contrôleur du module COMPTABLE de l'application
 */
class C_comptable extends CI_Controller {
	
	/**
	 * Aiguillage des demandes faites au contrôleur
	 * La fonction _remap est une fonctionnalité offerte par CI destinée à remplacer
	 * le comportement habituel de la fonction index. Grâce à _remap, on dispose
	 * d'une fonction unique capable d'accepter un nombre variable de paramètres.
	 * 
	 * @param $action : l'action demandée par le comptable
	 * @param $params : les éventuels paramètres transmis pour la réalisation de cette action
	 */
	public function _remap($action, $params = array())
	{
		// chargement du modèle d'authentification
		$this->load->model('authentif');
		
		// contrôle de la bonne authentification de l'utilisateur
		if ( ! $this->authentif->estConnecte())
		{
			// l'utilisateur n'est pas authentifié, on envoie la vue de connexion
			$data = array('erreur' => '<li>Vous devez être connecté en tant que comptable pour accéder à ce contenu.</li>');
			$this->templates->load('t_default', 'v_connexion', $data);
		}
		// contrôle si l'utilisateur est un comptable
		elseif ($this->session->userdata('idProfil') != 'COM')
		{
			// l'utilisateur n'est pas comptable, on envoie la vue de connexion
			$data = array('erreur' => '<li>Vous devez être connecté en tant que comptable pour accéder à ce contenu.</li>');
			$this->templates->load('t_default', 'v_connexion', $data);
		}
		else
		{
			// Aiguillage selon l'action demandée
			// CI a traité l'URL au préalable de sorte à toujours renvoyer l'action "index"
			// même lorsqu'aucune action n'est exprimée
			/* index */
			if ($action == 'index')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// suppression des informations additionnelles conservées en session
				$this->session->unset_userdata('userFiche');
				$this->session->unset_userdata('moisFiche');
				$this->session->unset_userdata('listeFiches');
				
				// active la fonction accueil du modèle comptable
				$this->a_comptable->accueil();
			}
			/* deconnecter */
			elseif ($action == 'deconnecter')
			{
				// charge le modèle authentif
				$this->load->model('authentif');
				
				// active la fonction deconnecter du modèle authentif
				$this->authentif->deconnecter();
			}
			/* monCompte */
			elseif ($action == 'monCompte')
			{
				// charge le modèle comptable et recherche l'id de l'utilisateur
				$this->load->model('a_comptable');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// suppression des informations additionnelles conservées en session
				$this->session->unset_userdata('userFiche');
				$this->session->unset_userdata('moisFiche');
				$this->session->unset_userdata('listeFiches');
				
				// active la fonction monCompte du modèle comptable
				$this->a_comptable->monCompte($idUtilisateur);
			}
			/* majSecurite */
			elseif ($action == 'majSecurite')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle comptable et recherche l'id de l'utilisateur
					$this->load->library('form_validation');
					$this->load->model('a_comptable');
					$idUtilisateur = $this->session->userdata('idUser');
					
					// configuration des champs du formulaire
					$this->form_validation->set_rules('currentMdp', 'Mot de passe actuel', 'required|max_length[60]');
					$this->form_validation->set_rules('newMdp', 'Nouveau mot de passe', 'required|max_length[60]');
					$this->form_validation->set_rules('confirmMdp', 'Confirmation', 'required|max_length[60]|matches[newMdp]');
					
					// si validation des champs du formulaire
					if ($this->form_validation->run())
					{
						// obtention des données postées : $currentMdp, $newMdp
						$currentMdp = $this->input->post('currentMdp');
						$newMdp = $this->input->post('newMdp');
						
						$infosUtil = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
						
						// si $currentMdp est égal au mdp de l'utilisateur
						if (password_verify($currentMdp, $infosUtil['mdp']))
						{
							// hachage de newMdp
							$newMdp = password_hash($newMdp, PASSWORD_BCRYPT);
							
							// on active majSecurite puis monCompte du modèle comptable
							$this->a_comptable->majSecurite($idUtilisateur, $newMdp);
							$this->a_comptable->monCompte($idUtilisateur, '<li>Modification(s) du mot de passe enregistrée(s) ...</li>');
						}
						else
						{
							// sinon on active monCompte
							$this->a_comptable->monCompte($idUtilisateur, null, '<li>Le mot de passe actuel est incorrect.</li>');
						}
					}
					else
					{
						// sinon on active monCompte
						$this->a_comptable->monCompte($idUtilisateur, null, validation_errors('<li>', '</li>'));
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* majResidence */
			elseif ($action == 'majResidence')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle comptable et recherche l'id de l'utilisateur
					$this->load->library('form_validation');
					$this->load->model('a_comptable');
					$idUtilisateur = $this->session->userdata('idUser');
					
					// configuration des champs du formulaire
					$this->form_validation->set_rules('ville', 'Ville', 'trim|required|max_length[30]');
					$this->form_validation->set_rules('cp', 'Code postal', 'required|exact_length[5]|integer',
						array('integer' => 'Le code postal doit être valide.')
					);
					$this->form_validation->set_rules('adresse', 'Adresse', 'trim|required|max_length[30]');
					
					// si validation des champs du formulaire
					if ($this->form_validation->run())
					{
						// création d'un tableau : $uneResidence et obtention des données postées
						$uneResidence = array(
							'ville' => $this->input->post('ville'),
							'cp' => $this->input->post('cp'),
							'adresse' => $this->input->post('adresse')
						);
						
						// on active majResidence puis monCompte du modèle comptable
						$this->a_comptable->majResidence($idUtilisateur, $uneResidence);
						$this->a_comptable->monCompte($idUtilisateur, '<li>Modification(s) du lieu de résidence enregistrée(s) ...</li>');
					}
					else
					{
						// sinon on active monCompte
						$this->a_comptable->monCompte($idUtilisateur, null, validation_errors('<li>', '</li>'));
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* validationFiches */
			elseif ($action == 'validationFiches')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// suppression des informations additionnelles conservées en session
				$this->session->unset_userdata('userFiche');
				$this->session->unset_userdata('moisFiche');
				
				// initialisation de la liste des fiches
				$this->session->set_userdata('listeFiches', 'validationFiches');
				
				// active la fonction validationFiches du modèle comptable
				$this->a_comptable->validationFiches();
			}
			/* paiementFiches */
			elseif ($action == 'paiementFiches')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// suppression des informations additionnelles conservées en session
				$this->session->unset_userdata('userFiche');
				$this->session->unset_userdata('moisFiche');
				
				// initialisation de la liste des fiches
				$this->session->set_userdata('listeFiches', 'paiementFiches');
				
				// active la fonction paiementFiches du modèle comptable
				$this->a_comptable->paiementFiches();
			}
			/* syntheseFiches */
			elseif ($action == 'syntheseFiches')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// suppression des informations additionnelles conservées en session
				$this->session->unset_userdata('userFiche');
				$this->session->unset_userdata('moisFiche');
				
				// initialisation de la liste des fiches
				$this->session->set_userdata('listeFiches', 'syntheseFiches');
				
				// active la fonction syntheseFiches du modèle comptable
				$this->a_comptable->syntheseFiches();
			}
			/* rechercheFiches */
			elseif ($action == 'rechercheFiches')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle comptable
					$this->load->library('form_validation');
					$this->load->model('a_comptable');
					
					// obtention de la liste conservée en session
					$liste = $this->session->userdata('listeFiches');
					
					// si $liste est initialisé
					if (isset($liste))
					{
						// configuration des champs du formulaire
						$this->form_validation->set_rules('visiteur', 'Visiteur',
							array('min_length[2]', 'max_length[4]', 'alpha_numeric', 'regex_match[/^[a-z]([0-9]{1,3})$/]')
						);
						$this->form_validation->set_rules('mois', 'Mois',
							array('exact_length[7]', 'regex_match[/^[0-9]{4}\-(0[1-9]|1[0-2])$/]')
						);
						
						// si validation des champs du formulaire
						if ($this->form_validation->run())
						{
							// obtention des données postées : $visiteur, $mois
							$visiteur = $this->input->post('visiteur') ?: '%';
							$mois = substr_replace($this->input->post('mois'), '', 4, 1) ?: '%';
							
							// on active la liste du modèle comptable
							$this->a_comptable->$liste($visiteur, $mois);
						}
						else
						{
							// sinon on active la liste
							$this->a_comptable->$liste('', '');
						}
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* voirFiche */
			elseif ($action == 'voirFiche')
			{
				// si les paramètres 0 et 1 de voirFiche sont initialisés
				if (isset($params[0], $params[1]))
				{
					// charge le modèle comptable
					$this->load->model('a_comptable');
					
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée, Validée ou Remboursée
					if ($laFiche['idEtat'] == 'CL' || $laFiche['idEtat'] == 'VA' || $laFiche['idEtat'] == 'RB')
					{
						// suppression des informations additionnelles conservées en session
						$this->session->unset_userdata('userFiche');
						$this->session->unset_userdata('moisFiche');
						$this->session->unset_userdata('listeFiches');
						
						// on active voirFiche du modèle comptable
						$this->a_comptable->voirFiche($idUtilisateur, $mois);
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* modFiche */
			elseif ($action == 'modFiche')
			{
				// si les paramètres 0 et 1 de modFiche sont initialisés
				if (isset($params[0], $params[1]))
				{
					// charge le modèle comptable
					$this->load->model('a_comptable');
					
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée
					if ($laFiche['idEtat'] == 'CL')
					{
						// suppression des informations additionnelles conservées en session
						$this->session->unset_userdata('listeFiches');
						
						// initialisation de l'utilisateur et du mois de la fiche
						$this->session->set_userdata('userFiche', $idUtilisateur);
						$this->session->set_userdata('moisFiche', $mois);
						
						// on active modFiche du modèle comptable
						$this->a_comptable->modFiche($idUtilisateur, $mois);
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* validFiche */
			elseif ($action == 'validFiche')
			{
				// si les paramètres 0 et 1 de validFiche sont initialisés
				if (isset($params[0], $params[1]))
				{
					// charge le modèle comptable
					$this->load->model('a_comptable');
					
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$infosUtil = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					$lesFrais = $this->dataAccess->getLesLignesHorsForfait($idUtilisateur, $mois);
					
					// si la fiche est Signée
					if ($laFiche['idEtat'] == 'CL')
					{
						// tous les frais sont Validés
						$fraisNonVA = false;
						
						// pour chaque $lesFrais en tant que $unFrais
						foreach ($lesFrais as $unFrais)
						{
							// si des frais ne sont pas Validés
							if ($unFrais['idEtat'] != 'VA')
							{
								// il existe des frais non Validés
								$fraisNonVA = true;
							}
						}
						
						// si tous les frais sont Validés
						if ($fraisNonVA == false)
						{
							// suppression des informations additionnelles conservées en session
							$this->session->unset_userdata('userFiche');
							$this->session->unset_userdata('moisFiche');
							
							// initialisation de la liste des fiches
							$this->session->set_userdata('listeFiches', 'validationFiches');
							
							// on active validFiche puis validationFiches du modèle comptable
							$this->a_comptable->validFiche($idUtilisateur, $mois);
							$this->a_comptable->validationFiches('%', '%', '<li>La fiche du mois '.substr_replace($mois, '-', 4, 0).' pour le visiteur '.$infosUtil['id'].' '.$infosUtil['nom'].' a été validée.</li>');
						}
						else
						{
							// suppression des informations additionnelles conservées en session
							$this->session->unset_userdata('userFiche');
							$this->session->unset_userdata('moisFiche');
							
							// initialisation de la liste des fiches
							$this->session->set_userdata('listeFiches', 'validationFiches');
							
							// sinon on active validationFiches
							$this->a_comptable->validationFiches('%', '%', null, '<li>La fiche du mois '.substr_replace($mois, '-', 4, 0).' pour le visiteur '.$infosUtil['id'].' '.$infosUtil['nom'].' comporte des frais non validés.</li>');
						}
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* validSelect */
			elseif ($action == 'validSelect')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle comptable
					$this->load->library('form_validation');
					$this->load->model('a_comptable');
					
					// obtention des données postées : $lesFiches
					$lesFiches = $this->input->post('lesFiches');
					
					// si $lesFiches est initialisé
					if (isset($lesFiches))
					{
						// toutes les fiches sont Signées
						$fichesNonCL = false;
						
						// tous les frais sont Validés
						$fraisNonVA = false;
						
						// pour chaque $lesFiches en tant que $uneFiche
						foreach ($lesFiches as $uneFiche)
						{
							$ficheParams = explode('_', $uneFiche);
							
							// s'il y a deux paramètres
							if (count($ficheParams) == 2)
							{
								list($idUtilisateur, $mois) = $ficheParams;
								$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
								$lesFrais = $this->dataAccess->getLesLignesHorsForfait($idUtilisateur, $mois);
								
								// si des fiches ne sont pas Signées
								if ($laFiche['idEtat'] != 'CL')
								{
									// il existe des fiches non Signées
									$fichesNonCL = true;
								}
								
								// si des fiches sont Signées
								if ($laFiche['idEtat'] == 'CL')
								{
									// pour chaque $lesFrais en tant que $unFrais
									foreach ($lesFrais as $unFrais)
									{
										// si des frais ne sont pas Validés
										if ($unFrais['idEtat'] != 'VA')
										{
											// il existe des frais non Validés
											$fraisNonVA = true;
										}
									}
									
									// si tous les frais sont Validés
									if ($fraisNonVA == false)
									{
										// on active validFiche du modèle comptable
										$this->a_comptable->validFiche($idUtilisateur, $mois);
									}
								}
							}
							else
							{
								// sinon fiche considérée comme non Signée
								$fichesNonCL = true;
							}
						}
						
						// configuration des champs du formulaire
						$this->form_validation->set_rules('lesFiches[]', 'Les fiches',
							array('required', 'min_length[9]', 'max_length[11]', 'alpha_dash', 'regex_match[/^[a-z]([0-9]{1,3})\_[0-9]{4}(0[1-9]|1[0-2])$/]')
						);
						
						// si validation des champs du formulaire
						if ($this->form_validation->run())
						{
							// si toutes les fiches sont Signées
							if ($fichesNonCL == false)
							{
								// si tous les frais sont Validés
								if ($fraisNonVA == false)
								{
									// on active validationFiches
									$this->a_comptable->validationFiches('%', '%', '<li>Les fiches sélectionnées ont été validées.</li>');
								}
								else
								{
									// sinon on active validationFiches
									$this->a_comptable->validationFiches('%', '%', null, '<li>Impossible de valider l\'intégralité de la sélection (présence de frais non validés).</li>');
								}
							}
							else
							{
								// sinon on active validationFiches
								$this->a_comptable->validationFiches('%', '%', null, '<li>Impossible de valider l\'intégralité de la sélection (présence de fiches non signées).</li>');
							}
						}
						else
						{
							// sinon on active validationFiches
							$this->a_comptable->validationFiches('%', '%', null, '<li>Impossible de valider l\'intégralité de la sélection (présence de valeurs incorrectes en tant que paramètres).</li>');
						}
					}
					else
					{
						// sinon on active validationFiches
						$this->a_comptable->validationFiches('%', '%', null, '<li>Aucune fiche n\'a été sélectionnée.</li>');
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* ajouterMotifRefus */
			elseif ($action == 'ajouterMotifRefus')
			{
				// si les paramètres 0 et 1 de ajouterMotifRefus sont initialisés
				if (isset($params[0], $params[1]))
				{
					// charge le modèle comptable
					$this->load->model('a_comptable');
					
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée
					if ($laFiche['idEtat'] == 'CL')
					{
						// suppression des informations additionnelles conservées en session
						$this->session->unset_userdata('listeFiches');
						
						// initialisation de l'utilisateur et du mois de la fiche
						$this->session->set_userdata('userFiche', $idUtilisateur);
						$this->session->set_userdata('moisFiche', $mois);
						
						// on active ajouterMotifRefus du modèle comptable
						$this->a_comptable->ajouterMotifRefus($idUtilisateur, $mois);
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* refuFiche */
			elseif ($action == 'refuFiche')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle comptable
					$this->load->library('form_validation');
					$this->load->model('a_comptable');
					
					// obtention de l'utilisateur et du mois conservé en session
					$idUtilisateur = $this->session->userdata('userFiche');
					$mois = $this->session->userdata('moisFiche');
					
					// si $idUtilisateur et $mois sont initialisés
					if (isset($idUtilisateur, $mois))
					{
						$infosUtil = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
						
						// configuration des champs du formulaire
						$this->form_validation->set_rules('motifRefus', 'Nouveau motif', 'trim|required|max_length[180]');
						
						// si validation des champs du formulaire
						if ($this->form_validation->run())
						{
							// obtention des données postées : $leMotifRefus
							$leMotifRefus = $this->input->post('motifRefus');
							
							// suppression des informations additionnelles conservées en session
							$this->session->unset_userdata('userFiche');
							$this->session->unset_userdata('moisFiche');
							
							// initialisation de la liste des fiches
							$this->session->set_userdata('listeFiches', 'validationFiches');
							
							// on active refuFiche puis validationFiches du modèle comptable
							$this->a_comptable->refuFiche($idUtilisateur, $mois, $leMotifRefus);
							$this->a_comptable->validationFiches('%', '%', '<li>La fiche du mois '.substr_replace($mois, '-', 4, 0).' pour le visiteur '.$infosUtil['id'].' '.$infosUtil['nom'].' a été refusée.</li>');
						}
						else
						{
							// sinon on active ajouterMotifRefus
							$this->a_comptable->ajouterMotifRefus($idUtilisateur, $mois, validation_errors('<li>', '</li>'));
						}
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* rembourseFiche */
			elseif ($action == 'rembourseFiche')
			{
				// si les paramètres 0 et 1 de rembourseFiche sont initialisés
				if (isset($params[0], $params[1]))
				{
					// charge le modèle comptable
					$this->load->model('a_comptable');
					
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$infosUtil = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Validée
					if ($laFiche['idEtat'] == 'VA')
					{
						// suppression des informations additionnelles conservées en session
						$this->session->unset_userdata('userFiche');
						$this->session->unset_userdata('moisFiche');
						
						// initialisation de la liste des fiches
						$this->session->set_userdata('listeFiches', 'paiementFiches');
						
						// on active rembourseFiche puis paiementFiches du modèle comptable
						$this->a_comptable->rembourseFiche($idUtilisateur, $mois);
						$this->a_comptable->paiementFiches('%', '%', '<li>La fiche du mois '.substr_replace($mois, '-', 4, 0).' pour le visiteur '.$infosUtil['id'].' '.$infosUtil['nom'].' a été remboursée.</li>');
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* rembourseSelect */
			elseif ($action == 'rembourseSelect')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle comptable
					$this->load->library('form_validation');
					$this->load->model('a_comptable');
					
					// obtention des données postées : $lesFiches
					$lesFiches = $this->input->post('lesFiches');
					
					// si $lesFiches est initialisé
					if (isset($lesFiches))
					{
						// toutes les fiches sont Validées
						$fichesNonVA = false;
						
						// pour chaque $lesFiches en tant que $uneFiche
						foreach ($lesFiches as $uneFiche)
						{
							$ficheParams = explode('_', $uneFiche);
							
							// s'il y a deux paramètres
							if (count($ficheParams) == 2)
							{
								list($idUtilisateur, $mois) = $ficheParams;
								$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
								
								// si des fiches ne sont pas Validées
								if ($laFiche['idEtat'] != 'VA')
								{
									// il existe des fiches non Validées
									$fichesNonVA = true;
								}
								
								// si des fiches sont Validées
								if ($laFiche['idEtat'] == 'VA')
								{
									// on active rembourseFiche du modèle comptable
									$this->a_comptable->rembourseFiche($idUtilisateur, $mois);
								}
							}
							else
							{
								// sinon fiche considérée comme non Validée
								$fichesNonVA = true;
							}
						}
						
						// configuration des champs du formulaire
						$this->form_validation->set_rules('lesFiches[]', 'Les fiches',
							array('required', 'min_length[9]', 'max_length[11]', 'alpha_dash', 'regex_match[/^[a-z]([0-9]{1,3})\_[0-9]{4}(0[1-9]|1[0-2])$/]')
						);
						
						// si validation des champs du formulaire
						if ($this->form_validation->run())
						{
							// si toutes les fiches sont Validées
							if ($fichesNonVA == false)
							{
								// on active paiementFiches
								$this->a_comptable->paiementFiches('%', '%', '<li>Les fiches sélectionnées ont été remboursées.</li>');
							}
							else
							{
								// sinon on active paiementFiches
								$this->a_comptable->paiementFiches('%', '%', null, '<li>Impossible de rembourser l\'intégralité de la sélection (présence de fiches non validées).</li>');
							}
						}
						else
						{
							// sinon on active paiementFiches
							$this->a_comptable->paiementFiches('%', '%', null, '<li>Impossible de rembourser l\'intégralité de la sélection (présence de valeurs incorrectes en tant que paramètres).</li>');
						}
					}
					else
					{
						// sinon on active paiementFiches
						$this->a_comptable->paiementFiches('%', '%', null, '<li>Aucune fiche n\'a été sélectionnée.</li>');
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* majForfait */
			elseif ($action == 'majForfait')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle comptable
					$this->load->library('form_validation');
					$this->load->model('a_comptable');
					
					// obtention de l'utilisateur et du mois conservé en session
					$idUtilisateur = $this->session->userdata('userFiche');
					$mois = $this->session->userdata('moisFiche');
					
					// si $idUtilisateur et $mois sont initialisés
					if (isset($idUtilisateur, $mois))
					{
						// configuration des champs du formulaire
						$this->form_validation->set_rules('lesMontants[]', 'Montant',
							array('required', 'max_length[6]', 'numeric', 'less_than[999.99]', 'regex_match[/^[0-9]+(\.[0-9]{1,2})?$/]')
						);
						
						// si validation des champs du formulaire
						if ($this->form_validation->run())
						{
							// obtention des données postées : $lesMontants
							$lesMontants = $this->input->post('lesMontants');
							
							// on active majForfait puis modFiche du modèle comptable
							$this->a_comptable->majForfait($idUtilisateur, $mois, $lesMontants);
							$this->a_comptable->modFiche($idUtilisateur, $mois, '<li>Modification(s) des éléments forfaitisés enregistrée(s) ...</li>');
						}
						else
						{
							// sinon on active modFiche
							$this->a_comptable->modFiche($idUtilisateur, $mois, null, validation_errors('<li>', '</li>'));
						}
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* validFrais */
			elseif ($action == 'validFrais')
			{
				// obtention de l'utilisateur et du mois conservé en session
				$idUtilisateur = $this->session->userdata('userFiche');
				$mois = $this->session->userdata('moisFiche');
				
				// si le paramètre 0 de validFrais, $idUtilisateur et $mois sont initialisés
				if (isset($params[0], $idUtilisateur, $mois))
				{
					// charge le modèle comptable
					$this->load->model('a_comptable');
					
					$idLigneFrais = $params[0];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idLigneFrais);
					
					// si l'identifiant du frais hors forfait existe
					if (isset($leFrais['id']))
					{
						// si le frais est En attente ou Refusé
						if ($leFrais['idEtat'] == 'EA' || $leFrais['idEtat'] == 'RE')
						{
							// on active validFrais puis modFiche du modèle comptable
							$this->a_comptable->validFrais($idUtilisateur, $mois, $idLigneFrais);
							$this->a_comptable->modFiche($idUtilisateur, $mois, '<li>Ligne "Hors forfait" validée ...</li>');
						}
						else
						{
							// sinon on active modFiche
							$this->a_comptable->modFiche($idUtilisateur, $mois, null, '<li>Ligne "Hors forfait" déjà validée.</li>');
						}
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* refuFrais */
			elseif ($action == 'refuFrais')
			{
				// obtention de l'utilisateur et du mois conservé en session
				$idUtilisateur = $this->session->userdata('userFiche');
				$mois = $this->session->userdata('moisFiche');
				
				// si le paramètre 0 de refuFrais, $idUtilisateur et $mois sont initialisés
				if (isset($params[0], $idUtilisateur, $mois))
				{
					// charge le modèle comptable
					$this->load->model('a_comptable');
					
					$idLigneFrais = $params[0];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idLigneFrais);
					
					// si l'identifiant du frais hors forfait existe
					if (isset($leFrais['id']))
					{
						// si le frais est En attente ou Validé
						if ($leFrais['idEtat'] == 'EA' || $leFrais['idEtat'] == 'VA')
						{
							// on active refuFrais puis modFiche du modèle comptable
							$this->a_comptable->refuFrais($idUtilisateur, $mois, $idLigneFrais);
							$this->a_comptable->modFiche($idUtilisateur, $mois, '<li>Ligne "Hors forfait" refusée ...</li>');
						}
						else
						{
							// sinon on active modFiche
							$this->a_comptable->modFiche($idUtilisateur, $mois, null, '<li>Ligne "Hors forfait" déjà refusée.</li>');
						}
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* telJustificatif */
			elseif ($action == 'telJustificatif')
			{
				// si les paramètres 0, 1, 2 et 3 de telJustificatif sont initialisés
				if (isset($params[0], $params[1], $params[2], $params[3]))
				{
					// charge le helper download et le modèle comptable
					$this->load->helper('download');
					$this->load->model('a_comptable');
					
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$idLigneFrais = $params[2];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idLigneFrais);
					$name = $params[3];
					
					// si l'identifiant du frais hors forfait existe
					if (isset($leFrais['id']))
					{
						// si l'emplacement du fichier existe on lance le téléchargement
						$path = 'application/views/uploads/'.$idUtilisateur.'/'.$mois.'/'.$name;
						if (file_exists($path))
						{
							$data = file_get_contents($path);
							force_download($leFrais['justificatifNom'], $data);
						}
						else
						{
							// sinon on envoie l'erreur 404
							show_404();
						}
					}
					else
					{
						// sinon on envoie l'erreur 404
						show_404();
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			else // dans tous les autres cas, on envoie la vue par défaut pour l'erreur 404
			{
				show_404();
			}
		}
	}
}