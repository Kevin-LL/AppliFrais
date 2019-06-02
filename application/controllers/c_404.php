<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contrôleur qui implémente une erreur 404 personnalisée
 */
class C_404 extends CI_Controller {
	
	/**
	 * Fonctionnalité par défaut du contrôleur.
	 * Change le statut du serveur et envoie la vue de l'erreur 404
	 */
	public function index()
	{
		$this->output->set_status_header('404');
		
		$data = array();
		$this->templates->load('t_default', 'v_404', $data);
	}
}