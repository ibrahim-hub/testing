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
 * @link            http://localhost/projects/islandryde/api/
 */
class Driver extends REST_Controller {

    function __construct()
    {   
        // Construct the parent class
        parent::__construct();
        $this->load->model('api/driver_model');

        //Listing record per page
        $this->per_page = 10;

        //validate driver header token
        $this->Common_model->validate_header_token($this);
    }

    /*Send OTP*/
    function send_otp_post()
    {
        $AllData = $this->post();
        if(empty($AllData['country_code']) || $AllData['country_code'] == '' ||
            empty($AllData['phone']) || $AllData['phone'] == '' ||
            empty($AllData['otp']) || $AllData['otp'] == ''){
            $this->Common_model->invalidParams($this);
        }
        /*Check phone exists or not*/
        $params = array('country_code'=>$AllData['country_code'], 'phone'=>$AllData['phone']);
        $phone_data = $this->driver_model->get_phone($params);
        if(count($phone_data) > 0){
            $message = ['status' => FALSE,'message' => $this->lang->line('phone_exists')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)  
        }
        else{
            //Send OTP SMS to User
            $mobile = $AllData['country_code'].ltrim($AllData['phone'], '0');
            $otpmessage = 'Dear Customer, '.$AllData['otp'].' is your one time password (OTP). Please enter the OTP to proceed. Thank you,SAIK Taxi';
            $sms_result = $this->Common_model->sendSMS($mobile, $otpmessage);
            if($sms_result == 1)
            {
                $message = ['status' => TRUE,'message' => $this->lang->line('otp_sent_success')];
                $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
            }
            else
            {
                $message = ['status' => FALSE,'message' => $this->lang->line('text_invalid_phone_failtosend')];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)  
            }
        }
       
    }

    /*
     * New Driver registration api
    */
    function signup_post()
    {   
        $this->form_validation->set_rules('fname','First Name','required|max_length[64]');
        $this->form_validation->set_rules('lname','Last Name','required|max_length[64]');
        $this->form_validation->set_rules('phone','Phone','required|is_unique[tbl_drivers.phone]',array('is_unique' =>$this->lang->line('text_rest_phone_unique')));
        $this->form_validation->set_rules('country_code','Country Code','required');
        $this->form_validation->set_rules('latitude','Latitude','required');
        $this->form_validation->set_rules('longitude','Longitude','required');
        $this->form_validation->set_rules('car_model','Car Model','required');
        $this->form_validation->set_rules('car_color','Car Color','required');
        $this->form_validation->set_rules('car_platenumber','Car Plate Number','required');
        $this->form_validation->set_rules('device_id','Device Id','required');
        $this->form_validation->set_rules('device_type','Device Type','required');
        $this->form_validation->set_rules('otp','OTP','required');
        if(!empty($this->post('email')))
            $this->form_validation->set_rules('email', 'Email', 'valid_email|is_unique[tbl_users.email]',array('is_unique' => $this->lang->line('text_rest_email_unique')));

        $fb_id = '';
        $password = '';
        if($this->post('fb_id') == '' || $this->post('fb_id') == null){
            $this->form_validation->set_rules('password', 'Password', 'required');
            $password = md5($this->post('password'));
            $signup_type = 'S';
        }
        else{
            $fb_id = $this->post('fb_id');
            $signup_type = 'F';
            $this->form_validation->set_rules('fb_id', 'Facebook', 'is_unique[tbl_drivers.fb_id]',array('is_unique' => 'This facebook account has already been registered. Please try again'));
        }
        
        if($this->form_validation->run())     
        {   
            $settings = $this->Common_model->getSettings('user');
            $referal_amount = 0;
            if($this->post('referal_code') != ''){
                $referal_code = strtoupper($this->post('referal_code'));
                if($this->db->get_where('tbl_drivers',array('referal_code'=>$referal_code,'is_active'=>'1'))->num_rows() > 0){
                    $referal_amount = $settings['referal_amount'];
                }
                
            }
            
            $email = '';
            if(!empty($this->post('email')))
                $email = $this->post('email');
            
            $otp = "1234";
            $this->Common_model->uploadImage('profile_image');
            $temp = $this->db->get_where('tbl_car_type',array('is_active'=>'1'),1)->row_array();
            $car_type = $temp['id'];
            $driver_token = strtotime(date("Ymd")).random_string('alnum',24).strtotime(date("his"));
            $referal_code = $this->Common_model->RandomString(6);
            $params  = array(
                'fname' => $this->post('fname'),
                'lname' => $this->post('lname'),
                'fb_id' => $fb_id,
                'signup_type' => $signup_type,
                'token' => $driver_token,
                'password' => $password,
                'email' => $email,
                'latitude' => $this->post('latitude'),
                'longitude' => $this->post('longitude'),
                'car_model' => $this->post('car_model'),
                'car_color' => $this->post('car_color'),
                'car_platenumber' => $this->post('car_platenumber'),
                'device_id' => $this->post('device_id'),
                'device_type' => $this->post('device_type'),
                'country_code' => $this->post('country_code'),
                'phone' => $this->post('phone'),
                'otp' => $this->post('otp'),
                'referal_code' => $referal_code,
                'wallet' =>$referal_amount,
                'car_type' => $car_type,
                'profile_image' => $this->post('profile_image'),
                'last_login' => date('Y-m-d H:i:s'),
                'otp_verified' => '1',            
            );

            /*******PROFILE IMAGE*********/
            $params['profile_image'] = 'default.jpeg';
            if(isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0){
                $li = $this->Common_model->uploadImage('profile_image');
                /*$li = $this->uploadImage('profile_image');*/

                if(array_key_exists("error",$li)){
                    
                    $message = ['status' => FALSE,'message' => strip_tags($li['error'])];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }else{
                    $params['profile_image'] = $li['file_name'];
                }
            }
            
            //Send New Registration Email
            $email_status = 1;

            /*$email_status = $this->Common_model->SignupMail($this->post('fname'), $this->post('email'), $verificationcode);*/

            /*----------------------------------------------------------------------------------------------------*/
            /*=============================Change this to true when going to live=================================*/
            /*----------------------------------------------------------------------------------------------------*/

            if($email_status)
            {
                //Insert user details
                $driver_id = $this->driver_model->add_driver($params);

                $data = $this->driver_model->get_driver($driver_id);
                $message = ['status' => TRUE,'message' => $this->lang->line('text_driverdocument_sucess'),'data'=>$data];
                $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) 
            }
            else
            {
                $message = ['status' => FALSE,'message' => 'Sorry ! The mail could not be sent'];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
        }
        else
        {
            
            $message = ['status' => FALSE,'message' => $this->validation_errors()[0]];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
    }

    function save_document_post()
    {   
         /*echo "Die;"; die;*/
        $this->form_validation->set_rules('driver_id','Driver ID','required|max_length[64]');
        $this->form_validation->set_rules('doc_type','Document Type','required');

        $params  = array(
            'driver_id' => $this->post('driver_id'),
            $this->post('doc_type') => $this->post($this->post('doc_type')),
            );
        
        if($this->form_validation->run())     
        {   

            $doc_data = $this->db->get_where('tbl_documents',array('driver_id'=>$this->post('driver_id')))->row_array();
            /*if($doc_data){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_driver_id_unique')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
            }*/

            
            $ri = $this->Common_model->uploadImage($this->post('doc_type'));
            if(array_key_exists("error",$ri)){
                $params[$this->post('doc_type')] = '';
            }
            else{
                $params[$this->post('doc_type')] = $ri['file_name'];
            }
          
            //Insert user details
            $ct = $this->db->get_where('tbl_documents',array('driver_id'=>$this->post('driver_id')))->num_rows();
            if($ct == 0){
                $this->db->insert('tbl_documents',$params);
            }
            else{
                $this->db->update('tbl_documents',$params,array('driver_id'=>$this->post('driver_id')));
            }
            $data = $this->driver_model->get_driver_documents($this->post('driver_id'));
            $message = ['status' => TRUE,'message' => "Upload successful",'data'=>$data];
            $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) 
        }
        else{
            $message = ['status' => FALSE,'message' => $this->validation_errors()[0]];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
    }

    function edit_document_post()
    {   
         /*echo "Die;"; die;*/
        $this->form_validation->set_rules('driver_id','Driver ID','required|max_length[64]');
        $this->form_validation->set_rules('doc_type','Document Type','required');

        $params  = array(
            'driver_id' => $this->post('driver_id'),
            $this->post('doc_type') => $this->post($this->post('doc_type')),
            );
        
        if($this->form_validation->run())     
        {   

            $doc_data = $this->db->get_where('tbl_documents',array('driver_id'=>$this->post('driver_id')))->row_array();
            /*if($doc_data){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_driver_id_unique')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
            }*/

            
            $ri = $this->Common_model->uploadImage($this->post('doc_type'));
            if(array_key_exists("error",$ri)){
                $params[$this->post('doc_type')] = '';
            }
            else{
                $params[$this->post('doc_type')] = $ri['file_name'];
            }
          
            //Insert user details
            $ct = $this->db->get_where('tbl_temp_documents',array('driver_id'=>$this->post('driver_id')))->num_rows();
            if($ct == 0){
                $this->db->insert('tbl_temp_documents',$params);
            }
            else{
                $this->db->update('tbl_temp_documents',$params,array('driver_id'=>$this->post('driver_id')));
            }
            
            $message = ['status' => TRUE,'message' => "Upload successful"];
            $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) 
        }
        else{
            $message = ['status' => FALSE,'message' => $this->validation_errors()[0]];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
    }

    function save_bank_post()
    {   
         /*echo "Die;"; die;*/
        $this->form_validation->set_rules('driver_id','Driver ID','required|max_length[64]');

        $params  = array();
        $params['driver_id'] = $this->post('driver_id'); 
        if($this->post('bank_name') != ''){
            $params['bank_name'] = $this->post('bank_name'); 
        } 

        if($this->post('account_number') != ''){
            $params['account_number'] = $this->post('account_number'); 
        } 
        
        if($this->form_validation->run())     
        {   
            $ct = $this->db->get_where('tbl_documents',array('driver_id'=>$this->post('driver_id')))->num_rows();
            if($ct == 0){
                $this->db->insert('tbl_documents',$params);
            }
            else{
                $this->db->update('tbl_documents',$params,array('driver_id'=>$this->post('driver_id')));
            }
            
            $message = ['status' => TRUE,'message' => "Bank information added successfully"];
            $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) 
        }
        else{
            $message = ['status' => FALSE,'message' => $this->validation_errors()[0]];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
    }

    function edit_bank_post()
    {   
         /*echo "Die;"; die;*/
        $this->form_validation->set_rules('driver_id','Driver ID','required|max_length[64]');

        $params  = array();
        $params['driver_id'] = $this->post('driver_id'); 
        if($this->post('bank_name') != ''){
            $params['bank_name'] = $this->post('bank_name'); 
        } 

        if($this->post('account_number') != ''){
            $params['account_number'] = $this->post('account_number'); 
        } 
        
        if($this->form_validation->run())     
        {   
            //Insert user details
            $ct = $this->db->get_where('tbl_documents',array('driver_id'=>$this->post('driver_id')))->num_rows();
            if($ct == 0){
                $this->db->insert('tbl_documents',$params);
            }
            else{
                $this->db->update('tbl_documents',$params,array('driver_id'=>$this->post('driver_id')));
            }
            
            $message = ['status' => TRUE,'message' => "Bank information updated successfully"];
            $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) 
        }
        else{
            $message = ['status' => FALSE,'message' => $this->validation_errors()[0]];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
    }

    public function get_documents_get()
    {
       $driver_id = $this->get('driver_id');
       if($driver_id == '' || $driver_id == null){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
       }
       else{
            $data = $this->driver_model->get_driver_documents($driver_id);
            $message = ['status' => TRUE,'message' => "Documentfound",'data'=>$data];
            $this->set_response($message, REST_Controller::HTTP_OK);
       } 
    }

    /*
     * Edit driver detail api
    */
    function edit_post()
    {   
        // check if the tbl_driver exists before trying to edit it
        $driver_detail = $this->driver_model->get_driver($this->post('driver_id'));


        $this->form_validation->set_rules('driver_id','Driver Id','required|numeric');
        $this->form_validation->set_rules('fname','First Name','required|max_length[64]');
        $this->form_validation->set_rules('lname','Last Name','required|max_length[64]');
        
        if($this->form_validation->run())     
        {   
            $profile_image = basename($driver_detail['profile_image']);

            if (!empty($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) 
            {   
                /*echo $profile_image; die;*/
                $config['upload_path'] = './assets/uploads/profile_image/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $filename = $config['file_name'] = strtotime(date("Ymd his"));
                $config['max_size'] = '2048KB';
                $config['max_width']  = '2000';
                $config['max_height']  = '2000';

                if(file_exists($config['upload_path'].$profile_image) && $profile_image != 'default.jpeg')
                {
                    unlink($config['upload_path'].$profile_image);
                    unlink($config['upload_path'].'thumb/'.$profile_image);
                }

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('profile_image'))
                {   
                    $w = $this->upload->data();
                    $profile_image = $w['file_name'];
                    $config = array(
                    'image_library'  => 'gd2',
                    'new_image'      => "./assets/uploads/profile_image/thumb/",
                    'source_image'   => "./assets/uploads/profile_image/".$w['file_name'],
                    'create_thumb'   => false,    
                    'width'          => "100",
                    'height'         => "100",
                    'maintain_ratio' => TRUE,
                    );
                    $this->load->library('image_lib'); // add library
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
                else
                {
                    $message = ['status' => FALSE,'message' => $this->upload->display_errors()];
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }
            }

            $params = array(
                'fname' => $this->post('fname'),
                'lname' => $this->post('lname'),
                'profile_image' => $profile_image
            );
            if(!empty($this->post('password')))
            {
                $params['password'] = md5($this->post('password'));
            }

            $this->driver_model->update_driver($driver_detail['id'], $params);

            $driver_data = $this->driver_model->get_driver($driver_detail['id']);
            $message = ['status' => TRUE,'message' => $this->lang->line('text_driveredit_sucess'),'data'=>$driver_data];
            $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
        }
        else
        {
            $fields_validation = $this->validation_errors();
            $message = ['status' => FALSE,'message' => (string)$fields_validation[0]];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
    }

    /*
     * driver login with email & password or facebook login
    */
    function login_put()
    {
        $this->form_validation->set_rules('latitude','Latitude','required');
        $this->form_validation->set_rules('longitude','Longitude','required');
        $this->form_validation->set_rules('device_id','Device ID','required');
        $this->form_validation->set_rules('device_type','Device Type','required');
        //For facebook login
        $driver = array();
        if(!empty($this->put('fb_id')))
        {
            //get fb driver details
            $driver = $this->driver_model->get_fb_driver($this->put('fb_id'));
        }
        else
        {
            if(($this->put('email') == '' && $this->put('phone') == '') || $this->put('password') == '' || $this->put('latitude') == '' || $this->put('longitude') == '' || $this->put('device_id') == '' || $this->put('device_type') == ''){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            //Login email authentication
            if(!empty($this->put('email')))
                $driver = $this->driver_model->checkDriverlogin($this->put('email'), $this->put('password'));
            else
                $driver = $this->driver_model->checkDriverphonelogin($this->put('phone'), $this->put('password'));
        }
        
        if(count($driver) > 0)
        {
            if($driver['otp_verified'] != '1')
            {
                $message = ['status' => FALSE,'message' => $this->lang->line('otp_verify'),'data'=>$driver];
                $this->set_response($message, REST_Controller::HTTP_PRECONDITION_FAILED); // HTTP_PRECONDITION_FAILED (412)
            }
            else if($driver['is_active'] == '0')
            {
                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_account_block')];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else
            {  
                $driver_data = $this->driver_model->get_driver($driver['id']);
                $doc_data = $this->db->get_where('tbl_documents',array('driver_id'=>$driver['id']))->row_array();

                if($doc_data)
                {
                    /*echo "<pre>";
                    print_r($doc_data);
                    die;*/
                    if($doc_data['registration_image'] == '' || 
                        $doc_data['vehicle_front_image'] == '' ||
                        $doc_data['vehicle_back_image'] == '' ||
                        $doc_data['licence_image'] == '' ||
                        $doc_data['driver_id_image'] == '' ||
                        $doc_data['owner_id_image'] == ''){
                        $driver_data['documents'] = $this->driver_model->get_driver_documents($driver['id']);
                        $message = ['status' => FALSE,'message' => "Please submit your documents",'data'=>$driver_data];
                        $this->response($message, REST_Controller::HTTP_EXPECTATION_FAILED); // HTTP_EXPECTATION_FAILED (417) 
                    }
                    else
                    {
                        if($driver['is_verified'] == '0')
                        {
                            $message = ['status' => FALSE,'message' => $this->lang->line('text_document_review')];
                            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                        }

                        $drivertoken = strtotime(date("Ymd")).random_string('alnum',24).$driver['id'].strtotime(date("his"));
                        /*Generate driver token*/
                        $params = array('token' => $drivertoken,'last_login' => date('Y-m-d H:i:s'),'is_login' => '1','latitude'=>$this->put('latitude'),'longitude'=>$this->put('longitude'),'device_id'=>$this->put('device_id'),'device_type'=>$this->put('device_type'));

                        $this->driver_model->update_driver_token($driver['id'], $params);
                        $driver['token'] = $drivertoken;

                        // Set the response and exit
                        $driver_data = $this->driver_model->get_driver($driver['id']);
                        $message = ['status' => TRUE,'message' => $this->lang->line('text_rest_login_success'),'data'=>$driver_data];
                        $this->response($message, REST_Controller::HTTP_OK); // OK (200) 
                    }

                    
                } 
                else{
                    $driver_data['documents'] = $this->driver_model->get_driver_documents($driver['id']);
                    $message = ['status' => FALSE,'message' => "Please submit your bank information",'data'=>$driver_data];
                    $this->response($message, REST_Controller::HTTP_METHOD_NOT_ALLOWED); // HTTP_METHOD_NOT_ALLOWED (405) 
                }
            }
        }   
        else
        {
            if(!empty($this->put('fb_id')))
            {
                $message = ['status' => FALSE,'message' => 'This facebook id not registered'];
                $this->set_response($message, REST_Controller::HTTP_NOT_ACCEPTABLE); // HTTP_NOT_ACCEPTABLE (406)
            }
            else
            {

                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_login_fail')];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }   
        }
    }

    /*
     * Driver can use forgot assword
    */
    function forgotpassword_post()
    {
        $country_code = $this->post('country_code');
        $phone = $this->post('phone');
        if(empty($country_code) || $country_code == '' ||
            empty($phone) || $phone == ''){
            $this->Common_model->invalidParams($this);
        }

        /*Check phone exists or not*/
        $params = array('country_code'=>$country_code, 'phone'=>$phone);
        $driverdata = $this->driver_model->get_phone($params);
        if(empty($driverdata) || count($driverdata) <= 0){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_forgotpassword_phoneinvalid')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)  
        }
        else
        {
            /*Check this user phone registered with normal signup or not*/
            if($driverdata['signup_type'] == 'S'){
                /*Random new password code*/
                $newpassword = strtoupper(random_string('alnum',8));

                //Send OTP SMS to User
                $mobile = $country_code.ltrim($phone, '0');
                $message = 'Dear Customer, Your password will be changed. This is your new password: '.$newpassword.' Thank you,SAIK Taxi';
                $sms_result = $this->Common_model->sendSMS($mobile, $message);
                if($sms_result == 1)
                {
                    //update new password
                    $params = array('password' => md5($newpassword));
                    $this->driver_model->update_driver($driverdata['id'], $params);

                    $message = ['status' => TRUE,'message' => $this->lang->line('text_rest_forgotpassword_success')];
                    $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
                }
                else
                {
                    $message = ['status' => FALSE,'message' => $this->lang->line('text_invalid_phone_failtosend')];
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)  
                }
            }
            else{
                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_forgotpassword_fb')];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
        }
    }
    
    /*
     * Gen all drivers as well as single driver details
    */
    function drivers_get()
    {        
        $driver_id = $this->get('driver_id');
        // If the driver_id parameter doesn't exist return all the drivers
        if (!empty($driver_id))
        {   
            // Check if the drivers data store contains drivers (in case the database result returns NULL)
            $drivers = $this->driver_model->get_driver($driver_id);
            if(count($drivers) > 0)
            {
                // Set the response and exit
                $this->response(['status' => TRUE,'message' => 'Driver found','data'=>$drivers], REST_Controller::HTTP_OK); // OK (200)
            }
            else
            {
                $this->response(['status' => FALSE,'message' => 'No drivers found'], REST_Controller::HTTP_BAD_REQUEST); // HTTP_BAD_REQUEST (400) 
            }
        }
        else
        {   
            $page = $this->get('page');
            if($page < 1){
                $page = 1;
            }
            $startpoint = ($page * $this->per_page) - $this->per_page;
            // drivers from a data store e.g. database
            $drivers = $this->driver_model->get_all_drivers($startpoint);
            if(count($drivers) > 0)
            {
                $this->set_response(['status' => TRUE,'message' => 'Driver found','data'=>$drivers], REST_Controller::HTTP_OK); // OK (200)
            }
            else
            {
                $this->response(['status' => FALSE,'message' => 'driver list not found'], REST_Controller::HTTP_BAD_REQUEST); // HTTP_BAD_REQUEST (400) 
            }
        }
    }

    /*POST Update Location*/
    function update_location_post()
    {
       $input = $this->post();
        
        if(!isset($input['driver_id']) || $input['driver_id'] == '' ||
            !isset($input['device_id']) || $input['device_id'] == '' ||
            !isset($input['device_type']) || $input['device_type'] == '' ||
            !isset($input['latitude']) || $input['latitude'] == '' ||
            !isset($input['longitude']) || $input['longitude'] == '' ) 
                
        {   
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else
        {
            $driver_data = $this->driver_model->get_driver($input['driver_id']);
            $table = 'tbl_drivers';
            $data = array(
                'device_id' => $input['device_id'],
                'device_type' => $input['device_type'],
                'latitude' => $input['latitude'],
                'longitude' => $input['longitude'],
                'pre_latitude' => $driver_data['latitude'],
                'pre_longitude' => $driver_data['longitude'],
                );

            $params = array(
                'id' => $input['driver_id']
                );
            $this->Common_model->updateData($table, $data, $params);
            $this->set_response(['status' => TRUE,'message' => 'Location Updated successfully'], REST_Controller::HTTP_OK); // OK (200)
        }
    }

    /*
     * Driver can Logout
    */
    function logout_get()
    {
        $driver_id = $this->get('driver_id');

        if($driver_id == ''){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
        //update new password
        $params = array('token' => '','latitude' => '','longitude' => '','device_id' => '','is_login' => '0');
        $this->driver_model->update_driver($driver_id, $params);

        $message = ['status' => TRUE,'message' => 'Logout Successful'];
        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
    }

    /*Free Driver*/
    function free_get()
    {
        $driver_id = $this->get('driver_id');

        if($driver_id == ''){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
        //update new password
        $params = array('is_free' => '1');
        $this->driver_model->update_driver($driver_id, $params);

        $message = ['status' => TRUE,'message' => 'Free Successful'];
        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
    }

    /*Free Driver*/
    function service_change_get()
    {
        $driver_id = $this->get('driver_id');
        $status = $this->get('status');

        if($driver_id == ''){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
        //update new password
        $params = array('is_service' => $status);
        $this->driver_model->update_driver($driver_id, $params);
        $driver_data = $this->driver_model->get_driver($driver_id);

        $message = ['status' => TRUE,'message' => $this->lang->line('text_service_changed'),'data'=>$driver_data];
        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
    }

    function change_password_post()
    {   
        $AllPostData = $this->post();
        if(empty($AllPostData)){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
        else
        {
            $this->form_validation->set_rules('driver_id', 'Driver Id', 'required');
            $this->form_validation->set_rules('old_password', 'Old Password', 'required|min_length[3]');
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[3]');

            $driver_data = $this->driver_model->check_old_password($AllPostData['driver_id'], $AllPostData['old_password']);

            if(empty($driver_data)){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_oldpassword_notfound')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
            }
            else
            {
                if($AllPostData['old_password'] == $AllPostData['new_password']){
                    $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_samepassword')];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }

                if($this->form_validation->run())     
                {
                   
                    $params = array(
                        'password' => md5($AllPostData['new_password'])
                    );

                    $this->driver_model->change_password($AllPostData['driver_id'], $AllPostData['new_password']);

                    $message = ['status' => TRUE,'message' => $this->lang->line('text_rest_changepassword_success')];
                    $this->response($message, REST_Controller::HTTP_OK); // OK (200) 
                }
                else
                {   
                    $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
                }
            }
        }
    }

    /*
     * Driver can add his coupon card and get wallet amount
    */
    function add_couponcard_put()
    {
        $driver_id = $this->put('driver_id');
        $coupon_card = $this->put('coupon_card');
        if($driver_id == '' || $coupon_card == ''){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
        /*Get driver details*/
        $driver_data = $this->driver_model->get_driver($driver_id);
        if(!empty($driver_data))
        {
            /*Check coupon card validation*/
            $check_result = $this->driver_model->checkDrivercouponcard($coupon_card);
            if(empty($check_result))
            {
                $message = ['status' => FALSE,'message' => $this->lang->line('text_driver_couponcard_invalid')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
            }
            else if(!empty($check_result) && $check_result['status'] == 'used')
            {
                $message = ['status' => FALSE,'message' => $this->lang->line('text_driver_couponcard_already_used')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)    
            }
            
            /*Add driver coupon card in wallet history*/
            $params = array('user_id' => $driver_id,'couponcard_id' => $check_result['id'],'amount' => $check_result['amount'],'type' => 'Credit', 'user_type'=>'driver','description'=>'Added to driver wallet amount by coupon card');
            $this->db->insert('tbl_wallet', $params);
            /*update driver wallet amount*/
            $this->db->query('update tbl_drivers set wallet = wallet + '.$check_result['amount'].' where id = '.$driver_id);
            /*Update driver coupon card status*/
            $this->db->query('update tbl_driver_couponcard set driver_id = '.$driver_id.', status = "used" where id = '.$check_result['id']);
            /*Get driver details*/
            $driver_data = $this->driver_model->get_driver($driver_id);

            $message = ['status' => TRUE,'message' => $this->lang->line('text_driver_couponcard_success'),'data'=>$driver_data];
            $this->response($message, REST_Controller::HTTP_OK); // OK (200) 
        }
        else
        {
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_driverdetail_notfound')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
    }
}
