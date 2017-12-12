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
class User extends REST_Controller {

    function __construct()
    {   
        // Construct the parent class
        parent::__construct();
        $this->load->model('api/user_model');

        
        //Listing record per page
        $this->per_page = 10;

        //validate user header token
        $this->Common_model->validate_header_token($this);
    }

    function verify_phone_get()
    {
        $AllData = $this->get();
        if(empty($AllData['phone']) || $AllData['phone'] == ''){
            $this->Common_model->invalidParams($this);
        }
        $params = array('phone'=>$AllData['phone']);

        if($this->user_model->check_user_phone($params) > 0){
            $message = ['status' => FALSE,'message' => 'This phone number is already registered'];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            exit();
        }
        /*$otp = random_string('numeric',4);*/
        $otp = '1234';
        
        $phone_data = $this->user_model->get_phone($params);
        if(count($phone_data) > 0){
            /*Phone Exists so update the OTP*/
            $data = array('otp'=>$otp);
            $params = array('phone'=>$AllData['phone']);
            $this->Common_model->updateData('tbl_verify_otp',$data ,$params);
        }
        else{
            /*Phone doesnot exists so generate new record*/
            $params = array('phone'=>$AllData['phone'],'otp'=>$otp);
            $this->Common_model->addData('tbl_verify_otp', $params);
        }
        $message = ['status' => TRUE,'message' => 'Verification code has been sent to your registered phone number'];
        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)        
    }

    function verify_otp_get()
    {
        $AllData = $this->get();
        if(empty($AllData['phone']) || $AllData['phone'] == '' ||
            empty($AllData['otp']) || $AllData['otp'] == ''){
            $this->Common_model->invalidParams($this);
        }
        
        $params = array('phone'=>$AllData['phone'],'otp'=>$AllData['otp']);
        $phone_data = $this->user_model->get_phone($params);
        if(count($phone_data) > 0){
            /*Phone Exists so update the OTP*/
            $data = array('is_verify'=>'1');
            $params = array('phone'=>$AllData['phone']);
            $this->Common_model->updateData('tbl_verify_otp',$data ,$params);
            $message = ['status' => TRUE,'message' => 'Code verified successfully'];
            $this->set_response($message, REST_Controller::HTTP_OK); // OK (200) 
        }
        else{
            $message = ['status' => FALSE,'message' => 'Please enter valid code'];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
       
    }

    /*
     * New user registration api
    */
    function signup_post()
    {   
        $this->form_validation->set_rules('fname','First Name','required|max_length[64]');
        $this->form_validation->set_rules('lname','Last Name','required|max_length[64]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[tbl_users.email]',array('is_unique' => 'This email address has already been registered. Please try again'));
        
        $this->form_validation->set_rules('phone','Phone','required|min_length[6]|max_length[15]|is_unique[tbl_users.phone]',array('is_unique' => 'This phone number has already been registered. Please try again'));
        $this->form_validation->set_rules('gender','Gender','required');
        $this->form_validation->set_rules('latitude','Latitude','required');
        $this->form_validation->set_rules('longitude','Longitude','required');
        $this->form_validation->set_rules('device_id','Device Id','required');
        $this->form_validation->set_rules('device_type','Device Type','required');
        /*$this->form_validation->set_rules('otp','OTP','required');*/
        $this->form_validation->set_rules('card_number','Card Number','required');
        $this->form_validation->set_rules('expiry_date','Expiration Date','required');
        $this->form_validation->set_rules('cvv','CVV','required');
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
                if($this->db->get_where('tbl_users',array('referal_code'=>$referal_code,'is_active'=>'1'))->num_rows() > 0){
                    $referal_amount = $settings['referal_amount'];
                }
                
            }
            $result = Braintree_Customer::create(array(
                    'firstName' => $this->post('fname'),
                    'lastName' => $this->post('lname'),
                    'email' => $this->post('email'),
                    'creditCard' => array(
                        'number' => $this->post('card_number'),
                        'expirationDate' => $this->post('expiry_date'),
                        'cvv' => $this->post('cvv'),
                        'cardholderName' => $this->post('fname').' '.$this->post('lname'),
                        'options' => array(
                            'verifyCard' => true,
                            'failOnDuplicatePaymentMethod' => true
                        )
                    )
                ));  
            $braintree_id = '';
            $braintree_token = '';    
            if ($result->success) 
            {
                //Braintree Save customer card
                $braintree_id = $result->customer->creditCards[0]->customerId;
                $braintree_token = $result->customer->creditCards[0]->token;

                $profile_image = 'default.jpeg';
                if (!empty($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) 
                {   
                    $config['upload_path'] = './assets/uploads/user/';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $filename = $config['file_name'] = strtotime(date("Ymd his"));
                    $config['max_size'] = '2048KB';
                    $config['max_width']  = '2000';
                    $config['max_height']  = '2000';

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('profile_image'))
                    {   
                        $w = $this->upload->data();
                        $profile_image = $w['file_name'];
                        $config = array(
                            'image_library'  => 'gd2',
                            'new_image'      => "./assets/uploads/user/thumb/",
                            'source_image'   => "./assets/uploads/user/".$w['file_name'],
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
                
                $usertoken = strtotime(date("Ymd")).random_string('alnum',24).strtotime(date("his"));
                $referal_code = $this->Common_model->RandomString(6);
                $params = array(
                    'fb_id' => $fb_id,
                    'fname' => $this->post('fname'),
                    'lname' => $this->post('lname'),
                    'email' => $this->post('email'),
                    'gender' => $this->post('gender'),
                    'password' => $password,
                    'phone' => $this->post('phone'),
                    'latitude' => $this->post('latitude'),
                    'longitude' => $this->post('longitude'),
                    'profile_image' => $profile_image,
                    'device_id' => $this->post('device_id'),
                    'device_type' => $this->post('device_type'),
                    'is_login' => '1',
                    'is_active' => '1',
                    'last_login' => date('Y-m-d H:i:s'),
                    /*'otp' => $this->post('otp'),*/
                    'token' => $usertoken,
                    'braintree_id' => $braintree_id,
                    'braintree_token' => $braintree_token,
                    'referal_code' => $referal_code,
                    'signup_type' => $signup_type,
                    'wallet' => $referal_amount

                    );
                
                //Send New Registration Email
                $email_status = 1;

                /*$email_status = $this->Common_model->SignupMail($this->post('name'), $this->post('email'), $verificationcode);*/

                /*----------------------------------------------------------------------------------------------------*/
                /*=============================Change this to true when going to live=================================*/
                /*----------------------------------------------------------------------------------------------------*/

                if($email_status)
                {
                    //Insert user details
                    $user_id = $this->user_model->add_user($params);

                    $data = $this->user_model->get_user($user_id);
                    $message = ['status' => TRUE,'message' => 'Signup successfully','data'=>$data];
                    $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) 
                }
                else
                {
                    $message = ['status' => FALSE,'message' => 'Sorry ! The mail could not be sent'];
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }
            } 
            else {
                if(count($result->errors->deepAll()) > 0)
                {
                    foreach($result->errors->deepAll() AS $error) {
                        //echo($error->code . ": " . $error->message . "\n");
                        $message = ['status' => FALSE,'message' => $error->message];
                        $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                    }
                }
                else{
                    $message = ['status' => FALSE,'message' => "Invalid card details"];
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
     * Edit user detail api
    */
    function edit_post()
    {   
        // check if the tbl_user exists before trying to edit it
        $user_detail = $this->user_model->get_user($this->post('user_id'));

        $this->form_validation->set_rules('user_id','User Id','required|numeric');
        $this->form_validation->set_rules('fname','First Name','required|max_length[64]');
        $this->form_validation->set_rules('lname','Last Name','required|max_length[64]');
        
        
        if($this->form_validation->run())     
        {   
            $profile_image = basename($user_detail['profile_image']);
            if (!empty($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) 
            {   
                $config['upload_path'] = './assets/uploads/user/';
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
                        'new_image'      => "./assets/uploads/user/thumb/",
                        'source_image'   => "./assets/uploads/user/".$w['file_name'],
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

            $this->user_model->update_user($user_detail['id'], $params);

            $user_data = $this->user_model->get_user($user_detail['id']);
            $message = ['status' => TRUE,'message' => 'Profile updated successfully','data'=>$user_data];
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
     * User login with email & password or facebook login
    */
    function login_get()
    {
        $this->form_validation->set_rules('latitude','Latitude','required');
        $this->form_validation->set_rules('longitude','Longitude','required');
        $this->form_validation->set_rules('device_id','Device ID','required');
        $this->form_validation->set_rules('device_type','Device Type','required');
        //For facebook login
        $user = array();
        if(!empty($this->get('fb_id')))
        {
            //get fb user details
            $user = $this->user_model->get_fb_user($this->get('fb_id'));
        }
        else
        {
            if($this->get('email') == '' || $this->get('password') == ''){
                $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            //Login authentication
            $user = $this->user_model->checkUserlogin($this->get('email'), $this->get('password'));
        }
        
        if(count($user) > 0)
        {
            if($user['otp_verified'] != '1' && !empty($user['otp_verified']))
            {
                $message = ['status' => FALSE,'message' => 'Your account is not verified'];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($user['is_active'] == '0')
            {
                $message = ['status' => FALSE,'message' => 'Your account is not activated'];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else
            {   
                $usertoken = strtotime(date("Ymd")).random_string('alnum',24).$user['id'].strtotime(date("his"));
                /*Generate user token*/
                $params = array('token' => $usertoken,'last_login' => date('Y-m-d H:i:s'),"is_login"=>'1','latitude'=>$this->get('latitude'),'longitude'=>$this->get('longitude'),'device_id'=>$this->get('device_id'),'device_type'=>$this->get('device_type'));
                $this->user_model->update_user_token($user['id'], $params);
                $user['token'] = $usertoken;

                // Set the response and exit
                $user_data = $this->user_model->get_user($user['id']);
                $message = ['status' => TRUE,'message' => 'Login successful','data'=>$user_data];
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
     * User can use forgot assword
    */
    function forgotpassword_get()
    {
        $email = $this->get('email');

        $userdata = $this->user_model->checkEmailexists($email);

        if(count($userdata) > 0)
        {
            if($userdata['signup_type'] == 'S'){
                /*Random new password code*/
                /*$newpassword = strtoupper(random_string('alnum',8));*/
                $newpassword = '123456';
                
                if($this->Common_model->forgotpasswordMail($userdata['fname'],$email,$newpassword))
                {
                    //update new password
                    $params = array('password' => md5($newpassword));
                    $this->user_model->update_user($userdata['id'], $params);

                    $message = ['status' => TRUE,'message' => 'Your password has been sent to your email'];
                    $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
                }
                else
                {
                    $message = ['status' => FALSE,'message' => 'Sorry ! The mail could not be sent'];
                    $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
                }
            }
            else{
                $message = ['status' => FALSE,'message' => 'You have registered with facebook'];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            } 
        }
        else
        {
            $message = ['status' => FALSE,'message' => 'Please enter registered email'];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
    }

    /*
     * Gen all Users as well as single user details
    */
    function users_get()
    {        
        $user_id = $this->get('user_id');
        // If the user_id parameter doesn't exist return all the users
        if (!empty($user_id))
        {   
            // Check if the users data store contains users (in case the database result returns NULL)
            $users = $this->user_model->get_user($user_id);
            if(count($users) > 0)
            {
                // Set the response and exit
                $this->response(['status' => TRUE,'message' => 'User found','data'=>$users], REST_Controller::HTTP_OK); // OK (200)
            }
            else
            {
                $this->response(['status' => FALSE,'message' => 'No users found'], REST_Controller::HTTP_BAD_REQUEST); // HTTP_BAD_REQUEST (400) 
            }
        }
        else
        {   
            $page = $this->get('page');
            if($page < 1){
                $page = 1;
            }
            $startpoint = ($page * $this->per_page) - $this->per_page;
            // Users from a data store e.g. database
            $users = $this->user_model->get_all_users($startpoint);
            if(count($users) > 0)
            {
                $this->set_response(['status' => TRUE,'message' => 'User found','data'=>$users], REST_Controller::HTTP_OK); // OK (200)
            }
            else
            {
                $this->response(['status' => FALSE,'message' => 'User list not found'], REST_Controller::HTTP_BAD_REQUEST); // HTTP_BAD_REQUEST (400) 
            }
        }
    }

    /*POST Update Location*/
    function update_location_post()
    {
       $input = $this->post();
        
        if(!isset($input['user_id']) || $input['user_id'] == '' ||
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
            $table = 'tbl_users';
            $data = array(
                'device_id' => $input['device_id'],
                'device_type' => $input['device_type'],
                'latitude' => $input['latitude'],
                'longitude' => $input['longitude'],
                );

            $params = array(
                'id' => $input['user_id']
                );
            $this->Common_model->updateData($table, $data, $params);
            $this->set_response(['status' => TRUE,'message' => 'Location Updated successfully'], REST_Controller::HTTP_OK); // OK (200)
        }
    }


    /*Get near drivers*/
    function near_free_drivers_get()
    {
        $latitude = $this->get('latitude');
        $longitude = $this->get('longitude');
        $car_type = $this->get('car_type');

        /*echo "<pre>"; print_r($this->get()); die;*/
        if(empty($latitude) || empty($longitude)){
            $this->response(['status' => FALSE,'message' => 'Invalid parameters'], REST_Controller::HTTP_BAD_REQUEST); // HTTP_BAD_REQUEST (400)
        }
        else{
            $data = $this->Common_model->getSettings('driver');
            $car_data = $this->Common_model->getCarList();

            if($car_type == ''){
                $car_type = $car_data[0]['id'];
            }
            $params = array(
                'base_distance'=>$data['base_distance'],
                'latitude'=>$latitude,
                'longitude'=>$longitude,
                'car_type'=>$car_type
                );

            $data = $this->user_model->getNearDrivers($params);
            $time = 0;
            if(count($data) > 0){
                $etadata = file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=".$data[0]['latitude'].','.$data[0]['longitude']."&destinations=".$params['latitude'].','.$params['longitude']."&language=en-EN&sensor=false");
                $etadata = json_decode($etadata);   
                $time = 0;  
                foreach($etadata->rows[0]->elements as $road) {
                    $time = $road->duration->value;
                    $time = ceil($time/60);
                //$distance = $road->distance->value;
                }
                $this->set_response(['status' => TRUE,'message' => 'Drivers found','car_data'=>$car_data,'time'=>(string)$time, 'data'=>$data], REST_Controller::HTTP_OK);   
            }
            else{
                $this->set_response(['status' => FALSE,'message' => 'No drivers found','car_data'=>$car_data,'time'=>'No drivers found','data'=>(object)array()], REST_Controller::HTTP_BAD_REQUEST); // OK (400)
            }
            
        }
    }


    /*
     * User can Logout
    */
    function logout_get()
    {
        $user_id = $this->get('user_id');

        if($user_id == ''){
            $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
        //update new password
        $params = array('token' => '','latitude' => '','longitude' => '','device_id' => '','is_login' => '0');
        $this->user_model->update_user($user_id, $params);

        $message = ['status' => TRUE,'message' => 'Logout Successful'];
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
            
            $this->form_validation->set_rules('user_id', 'User Id', 'required');
            $this->form_validation->set_rules('old_password', 'Old Password', 'required|min_length[4]');
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[4]');

            $user_data = $this->user_model->check_old_password($AllPostData['user_id'], $AllPostData['old_password']);

            if(empty($user_data)){
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

                    $this->user_model->change_password($AllPostData['user_id'], $AllPostData['new_password']);

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

    function test_push_ios_post()
    {
        $input = $this->post();
        if($input['device_id'] == '' || $input['user_type'] == ''){
            $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
        }
        else{
            $bodyI['aps'] = array('sound'=>'default','alert'=>"This is test push","tag"=>"test");

            /*IOS Push*/
            if($input['user_type'] == 'user'){
                if($input['device_id']!=''){
                    $this->Common_model->send_notification_ios_user($bodyI,$input['device_id']);
                    $message = ['status' => TRUE,'message' => 'Push sent successfully'];
                    $this->response($message, REST_Controller::HTTP_OK); 
                }
                else{
                    $message = ['status' => FALSE,'message' => 'Device ID not found'];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
                }
            }
            else{
                if($input['device_id']!=''){
                    $this->Common_model->send_notification_ios_driver($bodyI,$input['device_id']);
                    $message = ['status' => TRUE,'message' => 'Push sent successfully'];
                    $this->response($message, REST_Controller::HTTP_OK); 
                }
                else{
                    $message = ['status' => FALSE,'message' => 'Device ID not found'];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
                }
            } 
        }
    }

    function test_push_post()
    {
        $input = $this->post();
        if($input['device_id'] == '' || $input['user_type'] == ''){
            $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
        }
        else{
            $message = array("message"=>"This is test push","flag"=>"test");
            /*IOS Push*/
            if($input['user_type'] == 'user'){
                if($input['device_id']!=''){
                    $registatoin_ids_D = $input['device_id'];
                    $this->Common_model->send_gcm_notification_user($registatoin_ids_D,$message);
                    $message = ['status' => TRUE,'message' => 'Push sent successfully'];
                    $this->response($message, REST_Controller::HTTP_OK); 
                }
                else{
                    $message = ['status' => FALSE,'message' => 'Device ID not found'];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
                }
            }
            else{
                if($input['device_id']!=''){
                    $registatoin_ids_D = $input['device_id'];
                    $this->Common_model->send_gcm_notification_driver($registatoin_ids_D,$message);
                    $message = ['status' => TRUE,'message' => 'Push sent successfully'];
                    $this->response($message, REST_Controller::HTTP_OK); 
                }
                else{
                    $message = ['status' => FALSE,'message' => 'Device ID not found'];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
                }
            } 
        }
    }
}
