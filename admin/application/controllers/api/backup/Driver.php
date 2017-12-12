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

    /*
     * New Driver registration api
    */
    function signup_post()
    {   
        $this->form_validation->set_rules('fname','First Name','required|max_length[64]');
        $this->form_validation->set_rules('lname','Last Name','required|max_length[64]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[tbl_drivers.email]',array('is_unique' => 'This email address has already been registered. Please try again'));
        
        $this->form_validation->set_rules('phone','Phone','required|min_length[6]|max_length[15]|is_unique[tbl_drivers.phone]',array('is_unique' => 'This phone number has already been registered. Please try again'));
        $this->form_validation->set_rules('gender','Gender','required');

        $this->form_validation->set_rules('address','Address','required');
        $this->form_validation->set_rules('apt_suite','APT/SUITE','required');
        $this->form_validation->set_rules('city','City','required');
        $this->form_validation->set_rules('state','State','required');
        $this->form_validation->set_rules('zipcode','Zipcode','required');
        
        $this->form_validation->set_rules('dob','Date of Birth','required');
        $this->form_validation->set_rules('latitude','Latitude','required');
        $this->form_validation->set_rules('longitude','Longitude','required');
        $this->form_validation->set_rules('device_id','Device Id','required');
        $this->form_validation->set_rules('device_type','Device Type','required');

        $this->form_validation->set_rules('bank_acc_no', 'bank_acc_no', 'required');
        $this->form_validation->set_rules('bank_routing_no', 'bank_routing_no', 'required');
        $this->form_validation->set_rules('bank_name', 'bank_name', 'required');
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
            $this->form_validation->set_rules('fb_id', 'Facebook', 'is_unique[tbl_users.fb_id]',array('is_unique' => 'This facebook account has already been registered. Please try again'));
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
            $result = Braintree_MerchantAccount::create(
                  array(
                    'individual' => array(
                      'firstName' => $this->post('fname'),
                      'lastName' => $this->post('lname'),
                      'email' => $this->post('email'),
                      'phone' => $this->post('phone'),
                      'dateOfBirth' => $this->post('dob'),
                      'address' => array(
                        'streetAddress' => $this->post('apt_suite'),
                        'locality' => $this->post('city'),
                        'region' => substr($this->post('state'), 0, 2),
                        'postalCode' => $this->post('zipcode')
                      )
                    ),
                    'funding' => array(
                      'destination' => Braintree_MerchantAccount::FUNDING_DESTINATION_BANK,
                      'email' => $this->post('email'),
                      'mobilePhone' => preg_replace('/(\W*)/', '', $this->post('phone')),
                      'accountNumber' => $this->post('bank_acc_no'),
                      'routingNumber' => $this->post('bank_routing_no')
                    ),
                    'tosAccepted' => true,
                    'masterMerchantAccountId' => $this->config->item('masterMerchantAccountId'),
                  )
                );  
            $merchant_id = '';  
            if ($result->success) 
            {
                //Braintree Save driver accountnumber
                $merchant_id = $result->merchantAccount->id;

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
                    /*'lname' => $this->input->post('lname'),*/
                    'password' => $password,
                    'email' => $this->input->post('email'),

                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude'),
                    'device_id' => $this->input->post('device_id'),
                    'device_type' => $this->input->post('device_type'),
                    
                    'phone' => $this->input->post('phone'),
                    'gender' => $this->input->post('gender'),
                    'dob' => date("Y-m-d",strtotime($this->input->post('dob'))),
                    'address' => $this->input->post('address'),
                    'apt_suite' => $this->input->post('apt_suite'),
                    'city' => $this->input->post('city'),
                    'state' => $this->input->post('state'),
                    'zipcode' => $this->input->post('zipcode'),
                    'referal_code' => $referal_code,
                    'wallet' =>$referal_amount,
                    
                    'car_type' => $car_type,
                    'licence_image' => $this->input->post('licence_image'),
                    'insurance_image' => $this->input->post('insurance_image'),
                    'profile_image' => $this->input->post('profile_image'),
                    'merchant_id' =>$merchant_id,
                    'bank_acc_no' => $this->input->post('bank_acc_no'),
                    'bank_routing_no' => $this->input->post('bank_routing_no'),
                    'bank_name' => $this->input->post('bank_name'),
                    'last_login' => date('Y-m-d H:i:s'),
                    'is_login' => '0',                
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

                
                /********INSURANCE IMAGE********/
                $ii = $this->Common_model->uploadImage('insurance_image');
                if(array_key_exists("error",$ii)){
                    $message = ['status' => FALSE,'message' => strip_tags($ii['error'])];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }else{
                    $params['insurance_image'] = $ii['file_name'];
                }

                /********LICENCE IMAGE********/
                $pi = $this->Common_model->uploadImage('licence_image');
                if(array_key_exists("error",$pi)){
                    
                    $message = ['status' => FALSE,'message' => strip_tags($pi['error'])];
                    $this-> response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                } else{
                    $params['licence_image'] = $pi['file_name'];
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
                    $message = ['status' => TRUE,'message' => 'Your profile is being reviewed. We will inform you once it is approved.','data'=>$data];
                    $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) 
                }
                else
                {
                    $message = ['status' => FALSE,'message' => 'Sorry ! The mail could not be sent'];
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }
            }
            else{
                foreach($result->errors->deepAll() AS $error) 
                {
                    //echo($error->code . ": " . $error->message . "\n");
                    $message = ['status' => FALSE,'message' => $error->message];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }
            }
        }
        else
        {
            
            $message = ['status' => FALSE,'message' => $this->validation_errors()[0]];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
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
            $message = ['status' => TRUE,'message' => 'Profile updated successfully','data'=>$driver_data];
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
    function login_get()
    {
        $this->form_validation->set_rules('latitude','Latitude','required');
        $this->form_validation->set_rules('longitude','Longitude','required');
        $this->form_validation->set_rules('device_id','Device ID','required');
        $this->form_validation->set_rules('device_type','Device Type','required');
        //For facebook login
        $driver = array();
        if(!empty($this->get('fb_id')))
        {
            //get fb driver details
            $driver = $this->driver_model->get_fb_driver($this->get('fb_id'));
        }
        else
        {
            if($this->get('email') == '' || $this->get('password') == ''){
                $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            //Login authentication
            $driver = $this->driver_model->checkDriverlogin($this->get('email'), $this->get('password'));
        }
        
        if(count($driver) > 0)
        {
            if($driver['is_active'] == '0')
            {
                $message = ['status' => FALSE,'message' => 'Your account is not activated'];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($driver['is_verified'] == '0')
            {
                $message = ['status' => FALSE,'message' => 'Your profile is being reviewed. We will inform you once it is approved.'];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else
            {   
                $drivertoken = strtotime(date("Ymd")).random_string('alnum',24).$driver['id'].strtotime(date("his"));
                /*Generate driver token*/
                $params = array('token' => $drivertoken,'last_login' => date('Y-m-d H:i:s'),'is_login' => '1','latitude'=>$this->get('latitude'),'longitude'=>$this->get('longitude'),'device_id'=>$this->get('device_id'),'device_type'=>$this->get('device_type'));
                $this->driver_model->update_driver_token($driver['id'], $params);
                $driver['token'] = $drivertoken;

                // Set the response and exit
                $driver_data = $this->driver_model->get_driver($driver['id']);
                $message = ['status' => TRUE,'message' => 'Login successful','data'=>$driver_data];
                $this->response($message, REST_Controller::HTTP_OK); // OK (200) 
            }
        }   
        else
        {
            if(!empty($this->get('fb_id')))
            {
                $message = ['status' => FALSE,'message' => 'This facebook id not registered'];
                $this->set_response($message, REST_Controller::HTTP_NOT_ACCEPTABLE); // HTTP_NOT_ACCEPTABLE (406)
            }
            else
            {

                $message = ['status' => FALSE,'message' => 'Please enter correct email or password'];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }   
        }
    }

    /*
     * driver can use forgot assword
    */
    function forgotpassword_get()
    {
        $email = $this->get('email');

        $driverdata = $this->driver_model->checkEmailexists($email);

        if(count($driverdata) > 0)
        {
            if($driverdata['signup_type'] == 'F'){
                $message = ['status' => FALSE,'message' => 'You have registered with facebook'];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else{
                /*Random new password code*/
                /*$newpassword = strtoupper(random_string('alnum',8));*/
                $newpassword = '123456';

                if($this->Common_model->forgotpasswordMail($driverdata['fname'],$email,$newpassword))
                {
                    //update new password
                    $params = array('password' => md5($newpassword));
                    $this->driver_model->update_driver($driverdata['id'], $params);

                    $message = ['status' => TRUE,'message' => 'Your password has been sent to your email'];
                    $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
                }
                else
                {
                    $message = ['status' => FALSE,'message' => 'Sorry ! The mail could not be sent'];
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }
            }  
        }
        else
        {
            $message = ['status' => FALSE,'message' => 'Please enter registered email'];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
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
            $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
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
            $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
        //update new password
        $params = array('is_free' => '1');
        $this->driver_model->update_driver($driver_id, $params);

        $message = ['status' => TRUE,'message' => 'Free Successful'];
        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
    }

    function change_password_post()
    {   
        
        $AllPostData = $this->post();
        if(empty($AllPostData)){
            $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
        else
        {
            
            $this->form_validation->set_rules('driver_id', 'Driver Id', 'required');
            $this->form_validation->set_rules('old_password', 'Old Password', 'required|min_length[4]');
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[4]');

            $driver_data = $this->driver_model->check_old_password($AllPostData['driver_id'], $AllPostData['old_password']);

            if(empty($driver_data)){
                $message = ['status' => FALSE,'message' => 'Old password is invalid'];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
            }
            else
            {
                if($AllPostData['old_password'] == $AllPostData['new_password']){
                    $message = ['status' => FALSE,'message' => 'Old Password and New Password cannot be same'];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }

                if($this->form_validation->run())     
                {
                   
                    $params = array(
                        'password' => md5($AllPostData['new_password'])
                    );

                    $this->driver_model->change_password($AllPostData['driver_id'], $AllPostData['new_password']);

                    $message = ['status' => TRUE,'message' => 'Password changed successfully'];
                    $this->response($message, REST_Controller::HTTP_OK); // OK (200) 
                }
                else
                {   
                    $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
                }
            }
        }
    }
}
