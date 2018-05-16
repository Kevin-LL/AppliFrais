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
			$data = array();
			$this->templates->load('t_connexion', 'v_connexion', $data);
		}
		// contrôle si l'utilisateur est un visiteur
		elseif ($this->session->userdata('idProfil') != 'VIS')
		{
			// l'utilisateur n'est pas visiteur, on envoie la vue de connexion
			$data = array();
			$this->templates->load('t_connexion', 'v_connexion', $data);
		}
		else
		{
			// Aiguillage selon l'action demandée 
			// CI a traité l'URL au préalable de sorte à toujours renvoyer l'action "index"
			// même lorsqu'aucune action n'est exprimée
			/* index */
			if ($action == 'index')
			{
				// charge le modèle visiteur et désactive le mois
				$this->load->model('a_visiteur');
				$this->session->unset_userdata('mois');
				
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
				// charge le modèle visiteur et désactive le mois
				// recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$this->session->unset_userdata('mois');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// active la fonction monCompte du modèle visiteur
				$this->a_visiteur->monCompte($idUtilisateur);
			}
			/* majSecurite */
			elseif ($action == 'majSecurite')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// création d'un tableau : $leMdp et obtention des données postées
				$leMdp = array( 
					'currentMdp' => $this->input->post('currentMdp'),
					'newMdp' => $this->input->post('newMdp')
				);
				
				// si les clés currentMdp et newMdp de $leMdp sont initialisées
				if (isset($leMdp['currentMdp'], $leMdp['newMdp']))
				{
					// si les clés currentMdp et newMdp de $leMdp ne sont pas vides
					if ($leMdp['currentMdp'] != '' && $leMdp['newMdp'] != '')
					{
						$infosUtil = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
						
						// si la clé currentMdp est égale au mdp de l'utilisateur
						if ($leMdp['currentMdp'] == $infosUtil['mdp'])
						{
							// on active majSecurite puis monCompte du modèle visiteur
							$this->a_visiteur->majSecurite($idUtilisateur, $leMdp['newMdp']);
							$this->a_visiteur->monCompte($idUtilisateur, 'Modification(s) du mot de passe enregistrée(s) ...');
						}
						else
						{
							// sinon on active monCompte
							$this->a_visiteur->monCompte($idUtilisateur, null, 'Le mot de passe actuel est incorrect.');
						}
					}
					else
					{
						// sinon on active monCompte
						$this->a_visiteur->monCompte($idUtilisateur, null, 'Un ou plusieurs champs sont vides.');
					}
				}
				else
				{
					// sinon on active monCompte
					$this->a_visiteur->monCompte($idUtilisateur);
				}
			}
			/* majResidence */
			elseif ($action == 'majResidence')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// création d'un tableau : $uneResidence et obtention des données postées
				$uneResidence = array( 
					'ville' => $this->input->post('ville'),
					'cp' => $this->input->post('cp'),
					'adresse' => $this->input->post('adresse')
				);
				
				// si les clés ville, cp et adresse de $uneResidence sont initialisées
				if (isset($uneResidence['ville'], $uneResidence['cp'], $uneResidence['adresse']))
				{
					// si les clés ville, cp et adresse de $uneResidence ne sont pas vides
					if ($uneResidence['ville'] != '' && $uneResidence['cp'] != '' && $uneResidence['adresse'] != '')
					{
						// on active majResidence puis monCompte du modèle visiteur
						$this->a_visiteur->majResidence($idUtilisateur, $uneResidence);
						$this->a_visiteur->monCompte($idUtilisateur, 'Modification(s) du lieu de résidence enregistrée(s) ...');
					}
					else
					{
						// sinon on active monCompte
						$this->a_visiteur->monCompte($idUtilisateur, null, 'Un ou plusieurs champs sont vides.');
					}
				}
				else
				{
					// sinon on active monCompte
					$this->a_visiteur->monCompte($idUtilisateur);
				}
			}
			/* mesFiches */
			elseif ($action == 'mesFiches')
			{
				// charge le modèle visiteur et désactive le mois
				// recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$this->session->unset_userdata('mois');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// active la fonction mesFiches du modèle visiteur
				$this->a_visiteur->mesFiches($idUtilisateur);
			}
			/* voirFiche */
			elseif ($action == 'voirFiche')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// si le paramètre 0 de voirFiche est initialisé
				if (isset($params[0]))
				{
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche a un état défini
					if (isset($laFiche['idEtat']))
					{
						// on active voirFiche du modèle visiteur
						$this->a_visiteur->voirFiche($idUtilisateur, $mois);
					}
					else
					{
						// sinon on active mesFiches
						$this->a_visiteur->mesFiches($idUtilisateur);
					}
				}
				else
				{
					// sinon on active mesFiches
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* voirMotifRefus */
			elseif ($action == 'voirMotifRefus')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// si le paramètre 0 de voirMotifRefus est initialisé
				if (isset($params[0]))
				{
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Refusée
					if ($laFiche['idEtat'] == 'RE')
					{
						// on active voirMotifRefus du modèle visiteur
						$this->a_visiteur->voirMotifRefus($idUtilisateur, $mois);
					}
					else
					{
						// sinon on active mesFiches
						$this->a_visiteur->mesFiches($idUtilisateur);
					}
				}
				else
				{
					// sinon on active mesFiches
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* modFiche */
			elseif ($action == 'modFiche')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// si le paramètre 0 de modFiche est initialisé
				if (isset($params[0]))
				{
					$mois = $params[0];
					// initialisation du mois de la fiche
					$this->session->set_userdata('mois', $mois);
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Créée ou Refusée
					if ($laFiche['idEtat'] == 'CR' || $laFiche['idEtat'] == 'RE')
					{
						// on active modFiche du modèle visiteur
						$this->a_visiteur->modFiche($idUtilisateur, $mois);
					}
					else
					{
						// sinon on désactive mois et on active mesFiches
						$this->session->unset_userdata('mois');
						$this->a_visiteur->mesFiches($idUtilisateur);
					}
				}
				else
				{
					// sinon on désactive mois et on active mesFiches
					$this->session->unset_userdata('mois');
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* signeFiche */
			elseif ($action == 'signeFiche')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// si le paramètre 0 de signeFiche est initialisé
				if (isset($params[0]))
				{
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Créée ou Refusée
					if ($laFiche['idEtat'] == 'CR' || $laFiche['idEtat'] == 'RE')
					{
						// on active signeFiche puis mesFiches du modèle visiteur
						$this->a_visiteur->signeFiche($idUtilisateur, $mois);
						$this->a_visiteur->mesFiches($idUtilisateur, 'La fiche du mois '.substr_replace($mois, '-', 4, 0).' a été signée.');
					}
					else
					{
						// sinon on active mesFiches
						$this->a_visiteur->mesFiches($idUtilisateur);
					}
				}
				else
				{
					// sinon on active mesFiches
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* signeSelect */
			elseif ($action == 'signeSelect')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// obtention des données postées : $lesFiches
				$lesFiches = $this->input->post('lesFiches');
				
				// si $lesFiches est initialisé
				if (isset($lesFiches))
				{
					// aucune fiche n'est Créée ou Refusée
					$fichesCRRE = false;
					
					// pour chaque $lesFiches en tant que $mois
					foreach ($lesFiches as $mois)
					{
						$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
						
						// si des fiches sont Créées ou Refusées
						if ($laFiche['idEtat'] == 'CR' || $laFiche['idEtat'] == 'RE')
						{
							// il existe des fiches Créées ou Refusées
							$fichesCRRE = true;
							
							// on active signeFiche du modèle visiteur
							$this->a_visiteur->signeFiche($idUtilisateur, $mois);
						}
					}
					
					// si aucune fiche n'est Créée ou Refusée
					if ($fichesCRRE == false)
					{
						// on active mesFiches
						$this->a_visiteur->mesFiches($idUtilisateur);
					}
					else
					{
						// sinon on active mesFiches
						$this->a_visiteur->mesFiches($idUtilisateur, 'Les fiches sélectionnées ont été signées.');
					}
				}
				else
				{
					// sinon on active mesFiches
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* impFiche */
			elseif ($action == 'impFiche')
			{
				// charge la bibliothèque pdf
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->library('Pdf');
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// si le paramètre 0 de impFiche est initialisé
				if (isset($params[0]))
				{
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée, Validée ou Remboursée
					if ($laFiche['idEtat'] == 'CL' || $laFiche['idEtat'] == 'VA' || $laFiche['idEtat'] == 'RB')
					{
						// on active impFiche du modèle visiteur
						$this->a_visiteur->impFiche($idUtilisateur, $mois);
					}
					else
					{
						// sinon on active mesFiches
						$this->a_visiteur->mesFiches($idUtilisateur);
					}
				}
				else
				{
					// sinon on active mesFiches
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* supprFiche */
			elseif ($action == 'supprFiche')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// si le paramètre 0 de supprFiche est initialisé
				if (isset($params[0]))
				{
					$mois = $params[0];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Invalide
					if ($laFiche['idEtat'] == 'IN')
					{
						// on active supprFiche puis mesFiches du modèle visiteur
						$this->a_visiteur->supprFiche($idUtilisateur, $mois);
						$this->a_visiteur->mesFiches($idUtilisateur, 'La fiche du mois '.substr_replace($mois, '-', 4, 0).' a été supprimée.');
					}
					else
					{
						// sinon on active mesFiches
						$this->a_visiteur->mesFiches($idUtilisateur);
					}
				}
				else
				{
					// sinon on active mesFiches
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* majForfait */
			elseif ($action == 'majForfait')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				// initialisation du mois de la fiche
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				$mois = $this->session->userdata('mois');
				
				// obtention des données postées : $lesFrais
				$lesFrais = $this->input->post('lesFrais');
				
				// si $lesFrais est initialisé
				if (isset($lesFrais))
				{
					// si $lesFrais ne contient pas de champs vides
					if ( ! in_array('', $lesFrais))
					{
						// on active majForfait puis modFiche du modèle visiteur
						$this->a_visiteur->majForfait($idUtilisateur, $mois, $lesFrais);
						$this->a_visiteur->modFiche($idUtilisateur, $mois, 'Modification(s) des éléments forfaitisés enregistrée(s) ...');
					}
					else
					{
						// sinon on active modFiche
						$this->a_visiteur->modFiche($idUtilisateur, $mois, null, 'Un ou plusieurs champs sont vides.');
					}
				}
				else
				{
					// sinon on désactive mois et on active mesFiches
					$this->session->unset_userdata('mois');
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* ajouteFrais */
			elseif ($action == 'ajouteFrais')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				// initialisation du mois de la fiche
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				$mois = $this->session->userdata('mois');	
				
				// création d'un tableau : $uneLigne et obtention des données postées
				$uneLigne = array( 
					'dateFrais' => $this->input->post('dateFrais'),
					'libelle' => $this->input->post('libelle'),
					'montant' => $this->input->post('montant'),
					'justificatifNom' => $justificatifNom = '',
					'justificatifFichier' => $justificatifFichier = ''
				);
				
				// si les clés dateFrais, libelle et montant de $uneLigne sont initialisées
				if (isset($uneLigne['dateFrais'], $uneLigne['libelle'], $uneLigne['montant']))
				{
					// si les clés dateFrais, libelle et montant de $uneLigne ne sont pas vides
					if ($uneLigne['dateFrais'] != '' && $uneLigne['libelle'] != '' && $uneLigne['montant'] != '')
					{
						// si le justificatif n'est pas vide
						if ($_FILES['justificatif']['name'] != '')
						{
							// on stocke le nom du justificatif dans $uneLigne
							$uneLigne['justificatifNom'] = $_FILES['justificatif']['name'];
							
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
								'max_width' => 1366,
								'max_height' => 768,
								'file_name' => uniqid()
							);
							$this->load->library('upload', $config);
							
							// si le justificatif est mis en ligne
							if ($this->upload->do_upload('justificatif'))
							{
								$data = $this->upload->data();
								
								// on stocke le fichier associé au justificatif dans $uneLigne
								$uneLigne['justificatifFichier'] = $data['file_name'];
								
								// on active ajouteFrais puis modFiche du modèle visiteur
								$this->a_visiteur->ajouteFrais($idUtilisateur, $mois, $uneLigne);
								$this->a_visiteur->modFiche($idUtilisateur, $mois, 'Ligne "Hors forfait" ajoutée ... <br>Justificatif : '.$uneLigne['justificatifNom']);
							}
							else
							{
								// si le dépôt des justificatifs est vide on le supprime
								$dossierMois = count(scandir('application/views/uploads/'.$idUtilisateur.'/'.$mois));
								if ($dossierMois <= 2)
								{
									rmdir('application/views/uploads/'.$idUtilisateur.'/'.$mois);
									$dossierUtilisateur = count(scandir('application/views/uploads/'.$idUtilisateur));
									if ($dossierUtilisateur <= 2)
									{
										rmdir('application/views/uploads/'.$idUtilisateur);
										$dossierUploads = count(scandir('application/views/uploads'));
										if ($dossierUploads <= 2)
										{
											rmdir('application/views/uploads/');
										}
									}
								}
								
								// on récupère l'erreur et on active modFiche
								$error = array('error' => $this->upload->display_errors('', ''));
								$this->a_visiteur->modFiche($idUtilisateur, $mois, null, 'Justificatif : '.$error['error']);
							}
						}
						else
						{
							// sinon on active modFiche
							$this->a_visiteur->modFiche($idUtilisateur, $mois, null, 'Vous devez fournir un justificatif pour ajouter un élément hors forfait.');
						}
					}
					else
					{
						// sinon on active modFiche
						$this->a_visiteur->modFiche($idUtilisateur, $mois, null, 'Un ou plusieurs champs sont vides.');
					}
				}
				else
				{
					// sinon on désactive mois et on active mesFiches
					$this->session->unset_userdata('mois');
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* supprFrais */
			elseif ($action == 'supprFrais')
			{
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				// initialisation du mois de la fiche
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				$mois = $this->session->userdata('mois');
				
				// si le paramètre 0 de supprFrais et mois sont initialisés
				if (isset($params[0], $mois))
				{
					$idLigneFrais = $params[0];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idUtilisateur, $mois, $idLigneFrais);
					
					// si l'identifiant du frais hors forfait existe
					if (isset($leFrais['id']))
					{
						// si un justificatif pour le frais hors forfait existe
						if($leFrais['justificatifFichier'] != null)
						{
							// on supprime le fichier associé au frais hors forfait
							unlink('application/views/uploads/'.$idUtilisateur.'/'.$mois.'/'.$leFrais['justificatifFichier']);
							
							// si le dépôt des justificatifs est vide on le supprime
							$dossierMois = count(scandir('application/views/uploads/'.$idUtilisateur.'/'.$mois));
							if ($dossierMois <= 2)
							{
								rmdir('application/views/uploads/'.$idUtilisateur.'/'.$mois);
								$dossierUtilisateur = count(scandir('application/views/uploads/'.$idUtilisateur));
								if ($dossierUtilisateur <= 2)
								{
									rmdir('application/views/uploads/'.$idUtilisateur);
									$dossierUploads = count(scandir('application/views/uploads'));
									if ($dossierUploads <= 2)
									{
										rmdir('application/views/uploads/');
									}
								}
							}
						}
						
						// on active supprLigneFrais puis modFiche du modèle visiteur
						$this->a_visiteur->supprLigneFrais($idUtilisateur, $mois, $idLigneFrais);
						$this->a_visiteur->modFiche($idUtilisateur, $mois, 'Ligne "Hors forfait" supprimée ...');				
					}
					else
					{
						// sinon on désactive mois et on active mesFiches
						$this->session->unset_userdata('mois');
						$this->a_visiteur->mesFiches($idUtilisateur);
					}
				}
				else
				{
					// sinon on désactive mois et on active mesFiches
					$this->session->unset_userdata('mois');
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			/* telJustificatif */
			elseif ($action == 'telJustificatif')
			{
				// charge le helper download
				// charge le modèle visiteur et recherche l'id de l'utilisateur
				$this->load->helper('download');
				$this->load->model('a_visiteur');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// si les paramètres 0, 1 et 2 de telJustificatif sont initialisés
				if (isset($params[0], $params[1], $params[2]))
				{
					$mois = $params[0];
					$idLigneFrais = $params[1];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idUtilisateur, $mois, $idLigneFrais);
					$name = $params[2];
					
					// si l'identifiant du frais hors forfait existe
					if (isset($leFrais['id']))
					{
						// on recherche l'emplacement du fichier
						$path = 'application/views/uploads/'.$idUtilisateur.'/'.$mois.'/'.$name;
						
						// si l'emplacement existe on lance le téléchargement
						if (file_exists($path))
						{
							$data = file_get_contents($path);
							force_download($leFrais['justificatifNom'], $data);
						}
						else
						{
							// sinon on désactive mois et on active mesFiches
							$this->session->unset_userdata('mois');
							$this->a_visiteur->mesFiches($idUtilisateur);
						}
					}
					else
					{
						// sinon on désactive mois et on active mesFiches
						$this->session->unset_userdata('mois');
						$this->a_visiteur->mesFiches($idUtilisateur);
					}
				}
				else
				{
					// sinon on désactive mois et on active mesFiches
					$this->session->unset_userdata('mois');
					$this->a_visiteur->mesFiches($idUtilisateur);
				}
			}
			else // dans tous les autres cas, on envoie la vue par défaut pour l'erreur 404
			{
				show_404();
			}
		}
	}
}