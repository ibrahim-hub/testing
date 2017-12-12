<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * Project  : Trunoir Music App
 * Date     : 10-06-2016
 * Developer: Piyush
*/
class Home extends CI_Controller {

    //Default constructor
	function __construct()
    {   
        parent::__construct();
	
	}

    /*Default cotroller method*/
	function index()
	{   
		//$this->load->view('/admin/dashboard');
		//$this->home->index();
	//	echo base_url();die;
		redirect(base_url('/admin/home'));
		
	}//End index function

	/*
     * set timezone for browser
    */
    function set_timezone()
    {
        if($this->session->userdata('adminlocal_timezone') == ''){
            $this->session->set_userdata('adminlocal_timezone',$this->input->get('timezone'));  
            echo "success";  
        }
        else{
            echo "already set";
        }
    }
}
/* End of file Home.php */