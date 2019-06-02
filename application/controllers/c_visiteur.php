<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contrôleur du module VISITEUR de l'application
 */
class C_visiteur extends CI_Controller {
	
	/**
	 * Aiguillage des demandes faites au contrôleur
	 * La fonction _remap est une fonctionnalité offerte par CI destinée à remplacer
	 * le comportement habituel de la fonction index. Grâce à _remap, on dispose
	 * d'une fonction unique capable d'accepter un nombre variable de paramètres.
	 * 
	 * @param $action : l'action demandée par le visiteur
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
			$data = array('erreur' => '<li>Vous devez être connecté en tant que visiteur pour accéder à ce contenu.</li>');
			$this->templates->load('t_default', 'v_connexion', $data);
		}
		// contrôle si l'utilisateur est un visiteur
		elseif ($this->session->userdata('idProfil') != 'VIS')
		{
			// l'utilisateur n'est pas visiteur, on envoie la vue de connexion
			$data = array('erreur' => '<li>Vous devez être connecté en tant que visiteur pour accéder à ce contenu.</li>');
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
				// charge le modèle visiteur
				$this->load->model('a_visiteur');
				
				// suppression des informations additionnelles conservées en session
				$this->session->unset_userdata('moisFiche');
				
				// active la fonction accueil du modèle visiteur
				$this->a_visiteur->accueil();
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
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// suppression des informations additionnelles conservées en session
				$this->session->unset_userdata('moisFiche');
				
				// active la fonction monCompte du modèle visiteur
				$this->a_visiteur->monCompte($idUtilisateur);
			}
			/* majSecurite */
			elseif ($action == 'majSecurite')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->library('form_validation');
					$this->load->model('a_visiteur');
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
							
							// on active majSecurite puis monCompte du modèle visiteur
							$this->a_visiteur->majSecurite($idUtilisateur, $newMdp);
							$this->a_visiteur->monCompte($idUtilisateur, '<li>Modification(s) du mot de passe enregistrée(s) ...</li>');
						}
						else
						{
							// sinon on active monCompte
							$this->a_visiteur->monCompte($idUtilisateur, null, '<li>Le mot de passe actuel est incorrect.</li>');
						}
					}
					else
					{
						// sinon on active monCompte
						$this->a_visiteur->monCompte($idUtilisateur, null, validation_errors('<li>', '</li>'));
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
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->library('form_validation');
					$this->load->model('a_visiteur');
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
						
						// on active majResidence puis monCompte du modèle visiteur
						$this->a_visiteur->majResidence($idUtilisateur, $uneResidence);
						$this->a_visiteur->monCompte($idUtilisateur, '<li>Modification(s) du lieu de résidence enregistrée(s) ...</li>');
					}
					else
					{
						// sinon on active monCompte
						$this->a_visiteur->monCompte($idUtilisateur, null, validation_errors('<li>', '</li>'));
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* mesFiches */
			elseif ($action == 'mesFiches')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// suppression des informations additionnelles conservées en session
				$this->session->unset_userdata('moisFiche');
				
				// active la fonction mesFiches du modèle visiteur
				$this->a_visiteur->mesFiches($idUtilisateur);
			}
			/* voirFiche */
			elseif ($action == 'voirFiche')
			{
				// si le paramètre 0 de voirFiche est initialisé
				if (isset($params[0]))
				{
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche a un état défini
					if (isset($laFiche['idEtat']))
					{
						// suppression des informations additionnelles conservées en session
						$this->session->unset_userdata('moisFiche');
						
						// on active voirFiche du modèle visiteur
						$this->a_visiteur->voirFiche($idUtilisateur, $mois);
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
			/* voirMotifRefus */
			elseif ($action == 'voirMotifRefus')
			{
				// si le paramètre 0 de voirMotifRefus est initialisé
				if (isset($params[0]))
				{
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Refusée
					if ($laFiche['idEtat'] == 'RE')
					{
						// suppression des informations additionnelles conservées en session
						$this->session->unset_userdata('moisFiche');
						
						// on active voirMotifRefus du modèle visiteur
						$this->a_visiteur->voirMotifRefus($idUtilisateur, $mois);
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
				// si le paramètre 0 de modFiche est initialisé
				if (isset($params[0]))
				{
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Créée ou Refusée
					if ($laFiche['idEtat'] == 'CR' || $laFiche['idEtat'] == 'RE')
					{
						// initialisation du mois de la fiche
						$this->session->set_userdata('moisFiche', $mois);
						
						// on active modFiche du modèle visiteur
						$this->a_visiteur->modFiche($idUtilisateur, $mois);
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
			/* signeFiche */
			elseif ($action == 'signeFiche')
			{
				// si le paramètre 0 de signeFiche est initialisé
				if (isset($params[0]))
				{
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Créée ou Refusée
					if ($laFiche['idEtat'] == 'CR' || $laFiche['idEtat'] == 'RE')
					{
						// suppression des informations additionnelles conservées en session
						$this->session->unset_userdata('moisFiche');
						
						// on active signeFiche puis mesFiches du modèle visiteur
						$this->a_visiteur->signeFiche($idUtilisateur, $mois);
						$this->a_visiteur->mesFiches($idUtilisateur, '<li>La fiche du mois '.substr_replace($mois, '-', 4, 0).' a été signée.</li>');
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
			/* signeSelect */
			elseif ($action == 'signeSelect')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->library('form_validation');
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					// obtention des données postées : $lesFiches
					$lesFiches = $this->input->post('lesFiches');
					
					// si $lesFiches est initialisé
					if (isset($lesFiches))
					{
						// toutes les fiches sont Créées ou Refusées
						$fichesNonCRRE = false;
						
						// pour chaque $lesFiches en tant que $mois
						foreach ($lesFiches as $mois)
						{
							$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
							
							// si des fiches ne sont pas Créées ou Refusées
							if ($laFiche['idEtat'] != 'CR' && $laFiche['idEtat'] != 'RE')
							{
								// il existe des fiches non Créées ou Refusées
								$fichesNonCRRE = true;
							}
							
							// si des fiches sont Créées ou Refusées
							if ($laFiche['idEtat'] == 'CR' || $laFiche['idEtat'] == 'RE')
							{
								// on active signeFiche du modèle visiteur
								$this->a_visiteur->signeFiche($idUtilisateur, $mois);
							}
						}
						
						// configuration des champs du formulaire
						$this->form_validation->set_rules('lesFiches[]', 'Les fiches',
							array('required', 'exact_length[6]', 'integer', 'regex_match[/^[0-9]{4}(0[1-9]|1[0-2])$/]')
						);
						
						// si validation des champs du formulaire
						if ($this->form_validation->run())
						{
							// si toutes les fiches sont Créées ou Refusées
							if ($fichesNonCRRE == false)
							{
								// on active mesFiches
								$this->a_visiteur->mesFiches($idUtilisateur, '<li>Les fiches sélectionnées ont été signées.</li>');
							}
							else
							{
								// sinon on active mesFiches
								$this->a_visiteur->mesFiches($idUtilisateur, null, '<li>Impossible de signer l\'intégralité de la sélection (présence de fiches non créées ou non refusées).</li>');
							}
						}
						else
						{
							// sinon on active mesFiches
							$this->a_visiteur->mesFiches($idUtilisateur, null, '<li>Impossible de signer l\'intégralité de la sélection (présence de valeurs incorrectes en tant que mois).</li>');
						}
					}
					else
					{
						// sinon on active mesFiches
						$this->a_visiteur->mesFiches($idUtilisateur, null, '<li>Aucune fiche n\'a été sélectionnée.</li>');
					}
				}
				else
				{
					// sinon on envoie l'erreur 404
					show_404();
				}
			}
			/* impFiche */
			elseif ($action == 'impFiche')
			{
				// si le paramètre 0 de impFiche est initialisé
				if (isset($params[0]))
				{
					// charge la bibliothèque Pdf
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->library('pdf');
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée, Validée ou Remboursée
					if ($laFiche['idEtat'] == 'CL' || $laFiche['idEtat'] == 'VA' || $laFiche['idEtat'] == 'RB')
					{
						// suppression des informations additionnelles conservées en session
						$this->session->unset_userdata('moisFiche');
						
						// on active impFiche du modèle visiteur
						$this->a_visiteur->impFiche($idUtilisateur, $mois);
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
			/* supprFiche */
			elseif ($action == 'supprFiche')
			{
				// si le paramètre 0 de supprFiche est initialisé
				if (isset($params[0]))
				{
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Invalide
					if ($laFiche['idEtat'] == 'IN')
					{
						// suppression des informations additionnelles conservées en session
						$this->session->unset_userdata('moisFiche');
						
						// on active supprFiche puis mesFiches du modèle visiteur
						$this->a_visiteur->supprFiche($idUtilisateur, $mois);
						$this->a_visiteur->mesFiches($idUtilisateur, '<li>La fiche du mois '.substr_replace($mois, '-', 4, 0).' a été supprimée.</li>');
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
			/* majForfait */
			elseif ($action == 'majForfait')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->library('form_validation');
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					// obtention du mois conservé en session
					$mois = $this->session->userdata('moisFiche');
					
					// si $mois est initialisé
					if (isset($mois))
					{
						// configuration des champs du formulaire
						$this->form_validation->set_rules('lesQuantites[]', 'Quantité', 'required|max_length[3]|integer');
						
						// si validation des champs du formulaire
						if ($this->form_validation->run())
						{
							// obtention des données postées : $lesQuantites
							$lesQuantites = $this->input->post('lesQuantites');
							
							// on active majForfait puis modFiche du modèle visiteur
							$this->a_visiteur->majForfait($idUtilisateur, $mois, $lesQuantites);
							$this->a_visiteur->modFiche($idUtilisateur, $mois, '<li>Modification(s) des éléments forfaitisés enregistrée(s) ...</li>');
						}
						else
						{
							// sinon on active modFiche
							$this->a_visiteur->modFiche($idUtilisateur, $mois, null, validation_errors('<li>', '</li>'));
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
			/* ajouteFrais */
			elseif ($action == 'ajouteFrais')
			{
				// si une requête "post" est lancée
				if ($this->input->method() == 'post')
				{
					// charge la bibliothèque Form_validation
					// charge le modèle visiteur et functionsLib puis recherche l'id de l'utilisateur
					$this->load->library('form_validation');
					$this->load->model('a_visiteur');
					$this->load->model('functionsLib');
					$idUtilisateur = $this->session->userdata('idUser');
					
					// obtention du mois conservé en session
					$mois = $this->session->userdata('moisFiche');
					
					// si $mois est initialisé
					if (isset($mois))
					{
						// configuration des champs du formulaire
						$this->form_validation->set_rules('dateFrais', 'Date',
							array('required', 'exact_length[10]', 'regex_match[/^([0-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/]')
						);
						$this->form_validation->set_rules('libelle', 'Libellé', 'trim|required|max_length[35]');
						$this->form_validation->set_rules('montant', 'Montant',
							array('required', 'max_length[6]', 'numeric', 'less_than[999.99]', 'regex_match[/^[0-9]+(\.[0-9]{1,2})?$/]')
						);
						if ($_FILES['justificatif']['name'] == null)
						{
							$this->form_validation->set_rules('justificatif', 'Justificatif', 'required');
						}
						
						// si validation des champs du formulaire
						if ($this->form_validation->run())
						{
							// si la date d'engagement est valide
							if ($this->functionsLib->estDateValide($this->input->post('dateFrais')))
							{
								// si le nom du justificatif ne dépasse pas 35 caractères
								if (mb_strlen($_FILES['justificatif']['name']) <= 35)
								{
									$nbLignes = $this->dataAccess->getNbLignesHorsForfait($idUtilisateur, $mois);
									
									// si le nombre de lignes de frais hors forfait est inférieur à 10
									if ($nbLignes['nb'] < 10)
									{
										// création d'un dépôt pour les justificatifs
										if ( ! file_exists('application/views/uploads/'.$idUtilisateur.'/'.$mois))
										{
											mkdir('application/views/uploads/'.$idUtilisateur.'/'.$mois, 0777, true);
										}
										
										// configuration du justificatif à mettre en ligne
										$config = array(
											'upload_path' => 'application/views/uploads/'.$idUtilisateur.'/'.$mois,
											'allowed_types' => 'pdf',
											'max_size' => 2000,
											'file_name' => uniqid()
										);
										$this->load->library('upload', $config);
										
										// si le justificatif est mis en ligne
										if ($this->upload->do_upload('justificatif'))
										{
											// création d'un tableau : $uneLigne et obtention des données postées
											$uneLigne = array(
												'dateFrais' => $this->input->post('dateFrais'),
												'libelle' => $this->input->post('libelle'),
												'montant' => $this->input->post('montant'),
												'justificatifNom' => $_FILES['justificatif']['name'],
												'justificatifFichier' => $this->upload->data()['file_name']
											);
											
											// on active ajouteFrais puis modFiche du modèle visiteur
											$this->a_visiteur->ajouteFrais($idUtilisateur, $mois, $uneLigne);
											$this->a_visiteur->modFiche($idUtilisateur, $mois, '<li>Ligne "Hors forfait" ajoutée ...</li><li>Justificatif : '.$uneLigne['justificatifNom'].'</li>');
										}
										else
										{
											// si le dépôt des justificatifs est vide on le supprime
											$dossierMois = 'application/views/uploads/'.$idUtilisateur.'/'.$mois;
											$dossierUtilisateur = 'application/views/uploads/'.$idUtilisateur;
											$dossierUploads = 'application/views/uploads';
											if (file_exists($dossierMois) && count(scandir($dossierMois)) <= 2)
											{
												rmdir($dossierMois);
												if (file_exists($dossierUtilisateur) && count(scandir($dossierUtilisateur)) <= 2)
												{
													rmdir($dossierUtilisateur);
													if (file_exists($dossierUploads) && count(scandir($dossierUploads)) <= 2)
													{
														rmdir($dossierUploads);
													}
												}
											}
											
											// sinon on active modFiche
											$this->a_visiteur->modFiche($idUtilisateur, $mois, null, $this->upload->display_errors('<li>', '</li>'));
										}
									}
									else
									{
										// sinon on active modFiche
										$this->a_visiteur->modFiche($idUtilisateur, $mois, null, '<li>Nombre maximum de lignes "Hors forfait" atteint.</li>');
									}
								}
								else
								{
									// sinon on active modFiche
									$this->a_visiteur->modFiche($idUtilisateur, $mois, null, '<li>Le nom des fichiers est limité à 35 caractères (avec extension).</li>');
								}
							}
							else
							{
								// sinon on active modFiche
								$this->a_visiteur->modFiche($idUtilisateur, $mois, null, '<li>La date d\'engagement doit être valide.</li>');
							}
						}
						else
						{
							// sinon on active modFiche
							$this->a_visiteur->modFiche($idUtilisateur, $mois, null, validation_errors('<li>', '</li>'));
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
			/* supprFrais */
			elseif ($action == 'supprFrais')
			{
				// obtention du mois conservé en session
				$mois = $this->session->userdata('moisFiche');
				
				// si le paramètre 0 de supprFrais et $mois sont initialisés
				if (isset($params[0], $mois))
				{
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					$idLigneFrais = $params[0];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idLigneFrais);
					
					// si l'identifiant du frais hors forfait existe
					if (isset($leFrais['id']))
					{
						// si un justificatif pour le frais hors forfait existe
						if ($leFrais['justificatifFichier'] != null)
						{
							// on supprime le fichier associé au frais hors forfait
							if (file_exists('application/views/uploads/'.$idUtilisateur.'/'.$mois.'/'.$leFrais['justificatifFichier']))
							{
								unlink('application/views/uploads/'.$idUtilisateur.'/'.$mois.'/'.$leFrais['justificatifFichier']);
							}
							
							// si le dépôt des justificatifs est vide on le supprime
							$dossierMois = 'application/views/uploads/'.$idUtilisateur.'/'.$mois;
							$dossierUtilisateur = 'application/views/uploads/'.$idUtilisateur;
							$dossierUploads = 'application/views/uploads';
							if (file_exists($dossierMois) && count(scandir($dossierMois)) <= 2)
							{
								rmdir($dossierMois);
								if (file_exists($dossierUtilisateur) && count(scandir($dossierUtilisateur)) <= 2)
								{
									rmdir($dossierUtilisateur);
									if (file_exists($dossierUploads) && count(scandir($dossierUploads)) <= 2)
									{
										rmdir($dossierUploads);
									}
								}
							}
						}
						
						// on active supprLigneFrais puis modFiche du modèle visiteur
						$this->a_visiteur->supprLigneFrais($idUtilisateur, $mois, $idLigneFrais);
						$this->a_visiteur->modFiche($idUtilisateur, $mois, '<li>Ligne "Hors forfait" supprimée ...</li>');				
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
				// si les paramètres 0, 1 et 2 de telJustificatif sont initialisés
				if (isset($params[0], $params[1], $params[2]))
				{
					// charge le helper download
					// charge le modèle visiteur et recherche l'id de l'utilisateur
					$this->load->helper('download');
					$this->load->model('a_visiteur');
					$idUtilisateur = $this->session->userdata('idUser');
					
					$mois = $params[0];
					$idLigneFrais = $params[1];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idLigneFrais);
					$name = $params[2];
					
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