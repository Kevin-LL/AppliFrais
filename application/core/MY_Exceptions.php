<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {
	var $ci;
	
	function __construct() 
	{
		$this->ci =& get_instance();
	}
	
    function show_404($page = '', $log_error = true)
	{
        $this->ci->output->set_status_header('404');
		
		$data = array();
        $this->ci->templates->load('t_default', 'v_404', $data);
		
        echo $this->ci->output->get_output();
        exit;
    }
}