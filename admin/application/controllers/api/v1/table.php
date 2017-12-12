<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

/**
 * This is an REST API
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Jilesh
 * @license         Hyperlinkinfosystem
 * @link            http://localhost/projects/trek/api/
 */
class Table extends REST_Controller {

    function __construct()
    {   
        // Construct the parent class
        parent::__construct();
        $this->load->model('api/user_model');

    	//validate user header token
        $this->Common_model->validate_header_token($this);
	}
    /*
     * Table info api
    */
    function table_post()
    {   
		    $data = $this->db->select('id, table_no, capacity')->get('tbl_table_master')->result_array();
			if(count($data) > 0){
				$message = ['status' => TRUE,'message' => $this->lang->line('table_info_sucess'),'data'=>$data];
				$this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) 
			}	else{
				$message = ['status' => FALSE,'message' => $this->lang->line('table_info_fales')];
				$this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
			}
    }
	
}
