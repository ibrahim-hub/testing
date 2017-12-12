<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * Project  : Trunoir App
 * Date     : 28-07-2016
 * Developer: Piyush
*/
 
class Category extends CI_Controller
{
    private $viewfolder = 'admin/category';
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/common','',TRUE);
        $this->load->model('admin/category_model','',TRUE);
        $this->load->model('admin/driver_model','',TRUE);
        
        $this->load->model('admin/order_model','',TRUE);
        $this->load->model('admin/promocode_model','',TRUE);

        
    } 

		  /*Default cotroller method*/
	function index()
	{   
       $this->load->view('admin/category');
	}
   /*
     * Adding a new category
     */
    function add()
    {  
        $AllPostData = $this->input->post();
        
        $this->form_validation->set_rules('category','Category','required');
        
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if($this->form_validation->run())    
        {   
             $params = array(
				'item_name' => $this->input->post('category'),
				'descripation' => '',
				'status' => 1,
				'created_date' => date('Y-m-d H:i:s'),
				
			);
             
			$users_id = $this->category_model->add_category($params);

			$this->session->set_flashdata('succ_msg', 'Categoryl added successfully.');
			redirect('admin/category');
            
        }
        else
        {   
            $data['result'] = $AllPostData; 
			 $this->load->view($this->viewfolder,$data);
        }
    }


}
