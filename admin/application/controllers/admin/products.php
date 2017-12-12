<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * Project  : Trunoir App
 * Date     : 28-07-2016
 * Developer: Piyush
*/
 
class Products extends CI_Controller
{
    private $viewfolder = 'admin/products';
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/common','',TRUE);
        $this->load->model('admin/Products_model','',TRUE);
        $this->load->model('admin/driver_model','',TRUE);
        
        $this->load->model('admin/order_model','',TRUE);
        $this->load->model('admin/promocode_model','',TRUE);

        
    } 

		  /*Default cotroller method*/
	function index()
	{   
       $this->load->view('admin/products');
	}
   /*
     * Adding a new category
     */
    function add()
    {  
        $AllPostData = $this->input->post();
        
        $this->form_validation->set_rules('productname','Product Name','required');
        $this->form_validation->set_rules('category','Category','required');
        $this->form_validation->set_rules('item_type','Item Type','required');
        $this->form_validation->set_rules('price','Price','required');
       
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if($this->form_validation->run())    
        {   
             $params = array(
				'product_name' => $this->input->post('productname'),
				'item_id' => $this->input->post('category'),
				'item_type' => $this->input->post('item_type'),
				'price' => $this->input->post('price'),
				'descripation' => '',
				'status' => 1,
				'created_date' => date('Y-m-d H:i:s'),
				
			);
             
			$users_id = $this->Products_model->add_products($params);

			$this->session->set_flashdata('succ_msg', 'Categoryl added successfully.');
			redirect('admin/products');
            
        }
        else
        {   
            $data['result'] = $AllPostData; 
			 $this->load->view($this->viewfolder,$data);
        }
    }


}
