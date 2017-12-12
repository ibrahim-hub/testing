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
        $phone_data = $this->user_model->get_phone($params);
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
     * New user registration api
    */
    function signup_post()
    {   
        $this->form_validation->set_rules('fname','First Name','required|max_length[64]');
        $this->form_validation->set_rules('lname','Last Name','required|max_length[64]');
        
        $this->form_validation->set_rules('country_code','Country Code','required');
        $this->form_validation->set_rules('phone','Phone','required|is_unique[tbl_users.phone]',array('is_unique' =>$this->lang->line('text_rest_phone_unique')));
        /*$this->form_validation->set_rules('location','Location','required');*/
        $this->form_validation->set_rules('emergency_country_code','Emergency Country Code','required');
        $this->form_validation->set_rules('emergency_number','Emergency Phone Number','required');
        $this->form_validation->set_rules('latitude','Latitude','required');
        $this->form_validation->set_rules('longitude','Longitude','required');
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
                else{
                    $message = ['status' => FALSE,'message' => $this->lang->line('text_invalid_referal_code')];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
                }
                
            }
         
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
            
            $email = '';
            if(!empty($this->post('email')))
                $email = $this->post('email');
            
            $usertoken = strtotime(date("Ymd")).random_string('alnum',24).strtotime(date("his"));
            $referal_code = $this->Common_model->RandomString(6);
            $params = array(
                'fb_id' => $fb_id,
                'fname' => $this->post('fname'),
                'lname' => $this->post('lname'),
                'email' => $email,
                'password' => $password,
                'country_code' => $this->post('country_code'),
                'phone' => $this->post('phone'),
                'emergency_country_code' => $this->post('emergency_country_code'),
                'emergency_number' => $this->post('emergency_number'),
                'latitude' => $this->post('latitude'),
                'longitude' => $this->post('longitude'),
                'profile_image' => $profile_image,
                'device_id' => $this->post('device_id'),
                'device_type' => $this->post('device_type'),
                'is_login' => '1',
                'is_active' => '1',
                'last_login' => date('Y-m-d H:i:s'),
                'otp' => $this->post('otp'),
                'otp_verified' => '1',
                'token' => $usertoken,
                'referal_code' => $referal_code,
                'signup_type' => $signup_type,
                'wallet' => $referal_amount

                );

            if($this->post('location') != '' && $this->post('location') != null){
                $params['location'] = $this->post('location');
            }
            
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
                $message = ['status' => TRUE,'message' => $this->lang->line('text_signup_sucess'),'data'=>$data];
                $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) 
            }
            else
            {
                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_signup_mailfail')];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
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
        $this->form_validation->set_rules('emergency_country_code','Emergency Country Code','required');
        $this->form_validation->set_rules('emergency_number','Emergency Phone Number','required');
        $email = $user_detail['email'];
        if(!empty($this->post('email')) && $this->post('email') != $email)
        {
            $this->form_validation->set_rules('email', 'Email', 'valid_email|is_unique[tbl_users.email]',array('is_unique' => $this->lang->line('text_rest_email_unique')));
            $email = $this->post('email');
        }

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
                'email' => $email,
                'profile_image' => $profile_image,
                'emergency_country_code' => $this->post('emergency_country_code'),
                'emergency_number' => $this->post('emergency_number'),
                
                );

            if($this->post('location') != '' && $this->post('location') != null){
                $params['location'] = $this->post('location');
            }

            $this->user_model->update_user($user_detail['id'], $params);

            $user_data = $this->user_model->get_user($user_detail['id']);
            $message = ['status' => TRUE,'message' => $this->lang->line('text_usersedit_sucess'),'data'=>$user_data];
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
    function login_put()
    {
        $this->form_validation->set_rules('latitude','Latitude','required');
        $this->form_validation->set_rules('longitude','Longitude','required');
        $this->form_validation->set_rules('device_id','Device ID','required');
        $this->form_validation->set_rules('device_type','Device Type','required');
        //For facebook login
        $user = array();
        if(!empty($this->put('fb_id')))
        {
            //get fb user details
            $user = $this->user_model->get_fb_user($this->put('fb_id'));
        }
        else
        {
            if(($this->put('email') == '' && $this->put('phone') == '') || $this->put('password') == '' || $this->put('latitude') == '' || $this->put('longitude') == '' || $this->put('device_id') == '' || $this->put('device_type') == ''){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            //Email Login authentication
            if(!empty($this->put('email')))
                $user = $this->user_model->checkUserlogin($this->put('email'), $this->put('password'));
            else
                $user = $this->user_model->checkUserphonelogin($this->put('phone'), $this->put('password'));
        }
        
        if(count($user) > 0)
        {
            if($user['otp_verified'] != '1')
            {
                $message = ['status' => FALSE,'message' => $this->lang->line('otp_verify'),'data'=>$user];
                $this->set_response($message, REST_Controller::HTTP_PRECONDITION_FAILED); // HTTP_PRECONDITION_FAILED (412)
            }
            else if($user['is_active'] == '0')
            {
                $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_account_block')];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else
            {   
                $usertoken = strtotime(date("Ymd")).random_string('alnum',24).$user['id'].strtotime(date("his"));
                /*Generate user token*/
                $params = array('token' => $usertoken,'last_login' => date('Y-m-d H:i:s'),"is_login"=>'1','latitude'=>$this->put('latitude'),'longitude'=>$this->put('longitude'),'device_id'=>$this->put('device_id'),'device_type'=>$this->put('device_type'));
                $this->user_model->update_user_token($user['id'], $params);
                $user['token'] = $usertoken;

                // Set the response and exit
                $user_data = $this->user_model->get_user($user['id']);
                $message = ['status' => TRUE,'message' => $this->lang->line('text_rest_login_success'),'data'=>$user_data];
                $this->response($message, REST_Controller::HTTP_OK); // OK (200) 
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
     * User can use forgot assword
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
        $userdata = $this->user_model->get_phone($params);
        if(empty($userdata) || count($userdata) <= 0){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_forgotpassword_phoneinvalid')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)  
        }
        else
        {
            /*Check this user phone registered with normal signup or not*/
            if($userdata['signup_type'] == 'S'){
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
                    $this->user_model->update_user($userdata['id'], $params);

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
            $user_data = $this->user_model->get_user($input['user_id']);
            $table = 'tbl_users';
            $data = array(
                'device_id' => $input['device_id'],
                'device_type' => $input['device_type'],
                'latitude' => $input['latitude'],
                'longitude' => $input['longitude'],
                'pre_latitude' => $user_data['latitude'],
                'pre_longitude' => $user_data['longitude'],
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

        $car_data = $this->Common_model->getCarList();

        if($car_type == ''){
            $car_type = $car_data[0]['id'];
        }

        /*echo "<pre>"; print_r($this->get()); die;*/
        if(empty($latitude) || empty($longitude)){
            $this->response(['status' => FALSE,'message' => 'No drivers found','car_data'=>$car_data,'time'=>'','data'=>array()], REST_Controller::HTTP_BAD_REQUEST);
        }
        else{
            $data = $this->Common_model->getSettings('driver');
            
            $params = array(
                'base_distance'=>$data['base_distance'],
                'min_wallet'=>$data['min_wallet'],
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
		/*	 if(empty($etadata->rows[0]->elements)){
                foreach($etadata->rows[0]->elements as $road) {
                    $time = $road->duration->value;
                    $time = ceil($time/60);
                //$distance = $road->distance->value;
                }
			 }	*/
                $this->set_response(['status' => TRUE,'message' => 'Drivers found','car_data'=>$car_data,'time'=>(string)$time, 'data'=>$data], REST_Controller::HTTP_OK);   
            }
            else{
                $this->set_response(['status' => FALSE,'message' => 'No drivers found','car_data'=>$car_data,'time'=>'','data'=>array()], REST_Controller::HTTP_BAD_REQUEST); // OK (400)
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
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
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
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
        }
        else
        {
            
            $this->form_validation->set_rules('user_id', 'User Id', 'required');
            $this->form_validation->set_rules('old_password', 'Old Password', 'required|min_length[3]');
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[3]');

            $user_data = $this->user_model->check_old_password($AllPostData['user_id'], $AllPostData['old_password']);

            if(empty($user_data)){
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
                    if($user_data['signup_type'] == 'F'){
                        $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_forgotpassword_fb')];
                        $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
                    }
                    else
                    {
                   
                        $params = array(
                            'password' => md5($AllPostData['new_password'])
                        );

                        $this->user_model->change_password($AllPostData['user_id'], $AllPostData['new_password']);

                        $message = ['status' => TRUE,'message' => $this->lang->line('text_rest_changepassword_success')];
                        $this->response($message, REST_Controller::HTTP_OK); // OK (200) 
                    }
                }
                else
                {   
                    $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
                }
            }
        }
    }

    function test_push_ios_post()
    {
        $input = $this->post();
        if($input['device_id'] == '' || $input['user_type'] == ''){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
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
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
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

    function contact_us_post()
    {
      
        $sender_email = $this->post('email'); 
        $subject  = $this->post('subject');
        $msg      = $this->post('message');

        if($sender_email == '' || $subject == '' || $msg == '' ){
            $message = ['status' => FALSE,'message' => 'Invalid Parameters'];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
        }
                           
        $config['mailtype'] = 'html';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $this->load->library('email');

        $this->email->from($sender_email, $subject);
        $this->email->to("info@hyperlinkinfosystem.in");// Receiver email address
        $this->email->subject("Inquiry Details: ".$subject); // Subject of email
        $this->email->bcc("jileshm@hyperlinkinfosystem.net.in");

        $message = "Hi,<br>";
        $message .="<table cellpadding='5' cellspacing='5'>";
        $message .="<tr><th>Email:</th><td>".$sender_email."</td></tr>";
        $message .="<tr><th>Message:</th><td>".$msg . "</td></tr></table>";
        $message .="<p>Thanks <br><br>The SAIK Taxi Team</p>";
        
        $this->email->set_newline("\r\n");
        $this->email->message($message);

        $this->email->set_header('MIME-Version', '1.0');
        $this->email->set_header('Content-type', 'text/html; charset=UTF-8');
        $this->email->set_header('Content-type', 'text/html; charset=iso-8859-1');
        $this->email->set_header('X-Priority', '3');
        $send = false;
        if($this->email->send())
        {
            $send = TRUE;
        }
        else
        {
            $message = ['status' => FALSE,'message' => 'Something went wrong'];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
        }
        if($send)
        {
            $config['mailtype'] = 'html';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            // Load email library  
            $this->load->library('email');

            $this->email->from("info@hyperlinkinfosystem.in", "SAIK Taxi Team");// Sender email address
            $this->email->to($sender_email);// Receiver email address
            $this->email->subject("Inquiry feedback"); // Subject of email
            $this->email->bcc("jileshm@hyperlinkinfosystem.net.in");



            $message = "Hello,";
            $message .="<h3>Thanks for contacting SAIK Taxi!</h3><br>";
            $message .= "<p>This is just a quick note to let you know we've received your message, and will respond as soon as we can.</p>";
            
            $message .="<p>The SAIK Taxi Team</p>";
            
            $this->email->set_newline("\r\n");
            $this->email->message($message);

            $this->email->set_header('MIME-Version', '1.0');
            $this->email->set_header('Content-type', 'text/html; charset=UTF-8');
            $this->email->set_header('Content-type', 'text/html; charset=iso-8859-1');
            $this->email->set_header('X-Priority', '3');

            $this->email->send();
            $message = ['status' => TRUE,'message' => $this->lang->line('review_success')];
            $this->response($message, REST_Controller::HTTP_OK); 
            /*$params = array(
              'name' => $subject,
              'email' => $sender_email,
              'message' => $msg,
              );
            $this->db->insert('tbl_ticket',$params);*/
        }
    } 
}
