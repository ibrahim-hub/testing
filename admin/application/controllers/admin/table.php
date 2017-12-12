<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * Project  : Trunoir App
 * Date     : 28-07-2016
 * Developer: Piyush
*/
 
class Table extends CI_Controller
{
    private $viewfolder = 'admin/table';
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/common','',TRUE);
        $this->load->model('admin/Table_model','',TRUE);
        $this->load->model('admin/driver_model','',TRUE);
        
        $this->load->model('admin/order_model','',TRUE);
        $this->load->model('admin/promocode_model','',TRUE);

        
    } 

		  /*Default cotroller method*/
	function index()
	{   
       $this->load->view('admin/table');
	}
   /*
     * Adding a new promocode
     */
    function add()
    {   
        $AllPostData = $this->input->post();
        
        $this->form_validation->set_rules('tableno','Table','required');
        $this->form_validation->set_rules('capacity','Capacity','required');
       
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if($this->form_validation->run())    
        {   
             $params = array(
				'table_no' => $this->input->post('tableno'),
				'capacity' => $this->input->post('capacity'),
				'descripation' => '',
				'status' => 1,
				'created_date' => date('Y-m-d H:i:s'),
				
			);
                
			$users_id = $this->Table_model->add_table($params);

			$this->session->set_flashdata('succ_msg', 'Table added successfully.');
			redirect('admin/table');
            
        }
        else
        {   
            $data['result'] = $AllPostData; 
			 $this->load->view($this->viewfolder,$data);
        }
    }


}
