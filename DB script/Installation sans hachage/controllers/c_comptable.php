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
			$data = array();
			$this->templates->load('t_connexion', 'v_connexion', $data);
		}
		// contrôle si l'utilisateur est un comptable
		elseif ($this->session->userdata('idProfil') !== 'COM')
		{
			// l'utilisateur n'est pas comptable, on envoie la vue de connexion
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
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
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
				
				// active la fonction monCompte du modèle comptable
				$this->a_comptable->monCompte($idUtilisateur);
			}
			/* majSecurite */
			elseif ($action == 'majSecurite')
			{
				// charge le modèle comptable et recherche l'id de l'utilisateur
				$this->load->model('a_comptable');
				$idUtilisateur = $this->session->userdata('idUser');
				
				// création d'un tableau : $leMdp et obtention des données postées
				$leMdp = array( 
					'currentMdp' => $this->input->post('currentMdp'),
					'newMdp' => $this->input->post('newMdp')
				);
				
				// si les clés currentMdp et newMdp de $leMdp sont initialisées
				if (isset($leMdp['currentMdp'], $leMdp['newMdp']))
				{
					// si la clé newMdp n'est pas vide
					if ($leMdp['newMdp'] !== '')
					{
						$infosUtil = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
						
						// si la clé currentMdp est égal au mdp de l'utilisateur
						if ($leMdp['currentMdp'] == $infosUtil['mdp'])
						{
							// on active majSecurite puis monCompte du modèle comptable
							$this->a_comptable->majSecurite($idUtilisateur, $leMdp['newMdp']);
							$this->a_comptable->monCompte($idUtilisateur, 'Modification(s) du mot de passe enregistrée(s) ...');
						}
						else
						{
							// sinon on active monCompte
							$this->a_comptable->monCompte($idUtilisateur, null, 'Le mot de passe actuel est incorrect.');
						}
					}
					else
					{
						// sinon on active monCompte
						$this->a_comptable->monCompte($idUtilisateur, null, 'Nouveau mot de passe est un champ vide.');
					}
				}
				else
				{
					// sinon on active monCompte
					$this->a_comptable->monCompte($idUtilisateur);
				}
			}
			/* majResidence */
			elseif ($action == 'majResidence')
			{
				// charge le modèle comptable et recherche l'id de l'utilisateur
				$this->load->model('a_comptable');
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
					// on active majResidence puis monCompte du modèle comptable
					$this->a_comptable->majResidence($idUtilisateur, $uneResidence);
					$this->a_comptable->monCompte($idUtilisateur, 'Modification(s) du lieu de résidence enregistrée(s) ...');
				}
				else
				{
					// sinon on active monCompte
					$this->a_comptable->monCompte($idUtilisateur);
				}
			}
			/* validationFiches */
			elseif ($action == 'validationFiches')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// active la fonction validationFiches du modèle comptable
				$this->a_comptable->validationFiches();
			}
			/* suiviPaiement */
			elseif ($action == 'suiviPaiement')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// active la fonction suiviPaiement du modèle comptable
				$this->a_comptable->suiviPaiement();
			}
			/* rechercheVis */
			elseif ($action == 'rechercheVis')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// obtention des données postées : $laRecherche
				$laRecherche = $this->input->post('recherche');
				
				// si le paramètres 0 de rechercheVis et $laRecherche sont initialisés
				if (isset($params[0], $laRecherche))
				{
					// si $laRecherche est un champ vide
					if ($laRecherche == '')
					{
						// on active le paramètre 0 du modèle comptable
						$this->a_comptable->$params[0]();
					}
					else
					{
						// sinon on active le paramètre 0
						$this->a_comptable->$params[0](null, $laRecherche);
					}
				}
				else
				{
					// sinon on active accueil
					$this->a_comptable->accueil();
				}
			}
			/* voirFiche */
			elseif ($action == 'voirFiche')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');

				// si les paramètres 0 et 1 de voirFiche sont initialisés
				if (isset($params[0], $params[1]))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée ou Validée
					if ($laFiche['idEtat'] == 'CL' || $laFiche['idEtat'] == 'VA')
					{
						// on active voirFiche du modèle comptable
						$this->a_comptable->voirFiche($idUtilisateur, $mois);
					}
					else
					{
						// sinon on active accueil
						$this->a_comptable->accueil();
					}
				}
				else
				{
					// sinon on active accueil
					$this->a_comptable->accueil();
				}
			}
			/* modFiche */
			elseif ($action == 'modFiche')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// si les paramètres 0 et 1 de modFiche sont initialisés
				if (isset($params[0], $params[1]))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée
					if ($laFiche['idEtat'] == 'CL')
					{
						// on active modFiche du modèle comptable
						$this->a_comptable->modFiche($idUtilisateur, $mois);
					}
					else
					{
						// sinon on active validationFiches
						$this->a_comptable->validationFiches();
					}
				}
				else
				{
					// sinon on active validationFiches
					$this->a_comptable->validationFiches();
				}
			}
			/* validFiche */
			elseif ($action == 'validFiche')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// si les paramètres 0 et 1 de validFiche sont initialisés
				if (isset($params[0], $params[1]))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$infosUtil = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée
					if ($laFiche['idEtat'] == 'CL')
					{
						// on active validFiche puis validationFiches du modèle comptable
						$this->a_comptable->validFiche($idUtilisateur, $mois);
						$this->a_comptable->validationFiches('La fiche du mois '.substr_replace($mois, '-', 4, 0).' pour le visiteur '.$infosUtil['id'].' '.$infosUtil['nom'].' a été validée.');
					}
					else
					{
						// sinon on active validationFiches
						$this->a_comptable->validationFiches();
					}
				}
				else
				{
					// sinon on active validationFiches
					$this->a_comptable->validationFiches();
				}
			}
			/* ajouterMotifRefus */
			elseif ($action == 'ajouterMotifRefus')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// si les paramètres 0 et 1 de ajouterMotifRefus sont initialisés
				if (isset($params[0], $params[1]))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée
					if ($laFiche['idEtat'] == 'CL')
					{
						// on active ajouterMotifRefus du modèle comptable
						$this->a_comptable->ajouterMotifRefus($idUtilisateur, $mois);
					}
					else
					{
						// sinon on active validationFiches
						$this->a_comptable->validationFiches();
					}
				}
				else
				{
					// sinon on active validationFiches
					$this->a_comptable->validationFiches();
				}
			}
			/* refuFiche */
			elseif ($action == 'refuFiche')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// obtention des données postées : $leMotifRefus
				$leMotifRefus = $this->input->post('motifRefus');
				
				// si les paramètres 0 et 1 de refuFiche et $leMotifRefus sont initialisés
				if (isset($params[0], $params[1], $leMotifRefus))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$infosUtil = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée
					if ($laFiche['idEtat'] == 'CL')
					{
						// on active refuFiche puis validationFiches du modèle comptable
						$this->a_comptable->refuFiche($idUtilisateur, $mois, $leMotifRefus);
						$this->a_comptable->validationFiches('La fiche du mois '.substr_replace($mois, '-', 4, 0).' pour le visiteur '.$infosUtil['id'].' '.$infosUtil['nom'].' a été refusée.');
					}
					else
					{
						// sinon on active validationFiches
						$this->a_comptable->validationFiches();
					}
				}
				else
				{
					// sinon on active validationFiches
					$this->a_comptable->validationFiches();
				}
			}
			/* rembourseFiche */
			elseif ($action == 'rembourseFiche')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// si les paramètres 0 et 1 de rembourseFiche sont initialisés
				if (isset($params[0], $params[1]))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$infosUtil = $this->dataAccess->getLesInfosUtilisateur($idUtilisateur);
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Validée
					if ($laFiche['idEtat'] == 'VA')
					{
						// on active rembourseFiche puis suiviPaiement du modèle comptable
						$this->a_comptable->rembourseFiche($idUtilisateur, $mois);
						$this->a_comptable->suiviPaiement('La fiche du mois '.substr_replace($mois, '-', 4, 0).' pour le visiteur '.$infosUtil['id'].' '.$infosUtil['nom'].' a été remboursée.');
					}
					else
					{
						// sinon on active suiviPaiement
						$this->a_comptable->suiviPaiement();
					}
				}
				else
				{
					// sinon on active suiviPaiement
					$this->a_comptable->suiviPaiement();
				}
			}
			/* majForfait */
			elseif ($action == 'majForfait')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// obtention des données postées : $lesMontants
				$lesMontants = $this->input->post('lesMontants');
				
				// si les paramètres 0 et 1 de majForfait et $lesMontants sont initialisés
				if (isset($params[0], $params[1], $lesMontants))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$laFiche = $this->dataAccess->getLesInfosFicheFrais($idUtilisateur, $mois);
					
					// si la fiche est Signée
					if ($laFiche['idEtat'] == 'CL')
					{
						// on active majForfait puis modFiche du modèle comptable
						$this->a_comptable->majForfait($idUtilisateur, $mois, $lesMontants);
						$this->a_comptable->modFiche($idUtilisateur, $mois, 'Modification(s) des éléments forfaitisés enregistrée(s) ...');
					}
					else
					{
						// sinon on active validationFiches
						$this->a_comptable->validationFiches();
					}
				}
				else
				{
					// sinon on active validationFiches
					$this->a_comptable->validationFiches();
				}
			}
			/* validFrais */
			elseif ($action == 'validFrais')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// si les paramètres 0, 1 et 2 de validFrais sont initialisés
				if (isset($params[0], $params[1], $params[2]))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$idLigneFrais = $params[2];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idUtilisateur, $mois, $idLigneFrais);
					
					// si l'identifiant du frais hors forfait existe
					if (isset($leFrais['id']))
					{
						// si le frais est En attente ou Refusé
						if ($leFrais['idEtat'] == 'EA' || $leFrais['idEtat'] == 'RE')
						{
							// on active validFrais puis modFiche du modèle comptable
							$this->a_comptable->validFrais($idUtilisateur, $mois, $idLigneFrais);
							$this->a_comptable->modFiche($idUtilisateur, $mois, 'Ligne "Hors forfait" validée ...');
						}
						else
						{
							// sinon on active modFiche
							$this->a_comptable->modFiche($idUtilisateur, $mois, null, 'Ligne "Hors forfait" déjà validée ...');
						}
					}
					else
					{
						// sinon on active validationFiches
						$this->a_comptable->validationFiches();
					}
				}
				else
				{
					// sinon on active validationFiches
					$this->a_comptable->validationFiches();
				}
			}
			/* refuFrais */
			elseif ($action == 'refuFrais')
			{
				// charge le modèle comptable
				$this->load->model('a_comptable');
				
				// si les paramètres 0, 1 et 2 de refuFrais sont initialisés
				if (isset($params[0], $params[1], $params[2]))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$idLigneFrais = $params[2];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idUtilisateur, $mois, $idLigneFrais);
					
					// si l'identifiant du frais hors forfait existe
					if (isset($leFrais['id']))
					{
						// si le frais est En attente ou Validé
						if ($leFrais['idEtat'] == 'EA' || $leFrais['idEtat'] == 'VA')
						{
							// on active refuFrais puis modFiche du modèle comptable
							$this->a_comptable->refuFrais($idUtilisateur, $mois, $idLigneFrais);
							$this->a_comptable->modFiche($idUtilisateur, $mois, 'Ligne "Hors forfait" refusée ...');
						}
						else
						{
							// sinon on active modFiche
							$this->a_comptable->modFiche($idUtilisateur, $mois, null, 'Ligne "Hors forfait" déjà refusée ...');
						}
					}
					else
					{
						// sinon on active validationFiches
						$this->a_comptable->validationFiches();
					}
				}
				else
				{
					// sinon on active validationFiches
					$this->a_comptable->validationFiches();
				}
			}
			/* telJustificatif */
			elseif ($action == 'telJustificatif')
			{
				// charge le helper download et charge le modèle comptable
				$this->load->helper('download');
				$this->load->model('a_comptable');
				
				// si les paramètres 0, 1, 2 et 3 de telJustificatif sont initialisés
				if (isset($params[0], $params[1], $params[2], $params[3]))
				{
					$idUtilisateur = $params[0];
					$mois = $params[1];
					$idLigneFrais = $params[2];
					$leFrais = $this->dataAccess->getLesInfosHorsForfait($idUtilisateur, $mois, $idLigneFrais);
					$name = $params[3];
					
					// si l'identifiant du frais hors forfait existe
					if (isset($leFrais['id']))
					{
						// on recherche l'emplacement du fichier
						$data = file_get_contents('application/views/uploads/'.$idUtilisateur.'/'.$mois.'/'.$name);
						
						// on lance le téléchargement
						force_download($leFrais['justificatifNom'], $data);
					}
					else
					{
						// sinon on active validationFiches
						$this->a_comptable->validationFiches();
					}
				}
				else
				{
					// sinon on active validationFiches
					$this->a_comptable->validationFiches();
				}
			}
			else // dans tous les autres cas, on envoie la vue par défaut pour l'erreur 404
			{
				show_404();
			}
		}
	}
}