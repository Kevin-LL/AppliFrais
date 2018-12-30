<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Security extends CI_Security {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function csrf_show_error()
	{
		header('Location: ' . htmlspecialchars($_SERVER['REQUEST_URI']), TRUE, 200);
	}
}