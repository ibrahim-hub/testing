<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    //Default constructor
	function __construct()
    {   

        parent::__construct();


    }

    /*Default cotroller method*/
	function index()
	{   
       
		$this->load->view('admin/dashboard');
	}//End index function
	
	//Redirect to dashboard
    function dashboard()
    {
       
        $this->load->view('admin/dashboard');
    }//End dashboard function

   //Redirect to bill
    function bill()
    {
       
        $this->load->view('admin/bill');
    }//End bill function
	
	//Redirect to table
    function table()
    {
       
        $this->load->view('admin/table');
    }//End table function

	//Redirect to category
    function category()
    {
       
        $this->load->view('admin/category');
    }//End category function

	//Redirect to products
    function products()
    {
       
        $this->load->view('admin/products');
    }//End products function

	//Redirect to income
    function income()
    {
       
        $this->load->view('admin/income');
    }//End income function
	
	//Redirect to expence
    function expence()
    {
       
        $this->load->view('admin/expence');
    }//End expence function
	
	/*
     * Adding a new table
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
                
			$users_id = $this->user_model->add_users($params);

			$this->session->set_flashdata('succ_msg', 'Table added successfully.');
			redirect('admin/table');
            
        }
        else
        {   
            //$data['result'] = $AllPostData; 
			echo $this->viewfolder;die;
            $this->load->view($this->viewfolder.'add');
        }
    }

}
/* End of file Home.php */