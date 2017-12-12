<?php
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
class Common_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->utc_time = time();
        $this->output->enable_profiler(FALSE);
        $this->car_image_path = base_url().'assets/car_images/';
    }

    /*
     * Validate user/driver header token
    */
    function validate_header_token($response_obj)
    {
        //Get request API method name
        $methodname = $this->router->fetch_method();
        $method_array = array("signup", "login", "forgotpassword", "send_otp","verify_phone","verify_otp","free",'resend_otp',"save_document","save_bank");

         if (!in_array($methodname, $method_array)) {
            $token = $this->input->get_request_header("token",TRUE);

            if($token == 'token4master'){
                //Bypass token only for developers
            }
          
        }
    }

    //Send SMS API using CURL method
    function sendSMS($phone, $message)
    {
        $this->load->library('mobilywsSMS/SMS');
        $smsObj = new SMS;
        /*Send SMS To Member*/
        return $smsObj->sendSMSMsg($phone,$message);
    }


    function uploadImage($fileType){
           
        
        $config['upload_path'] = './assets/uploads/'.$fileType.'/';
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload', $config);            
        $this->upload->initialize($config);

        if($this->upload->do_upload($fileType))
        {
            $w = $this->upload->data();                
            $config = array(
            'image_library'  => 'gd2',
            'new_image'      => './assets/uploads/'.$fileType.'/thumb/',
            'source_image'   => './assets/uploads/'.$fileType.'/'.$w['file_name'],
            'create_thumb'   => false,    
            'width'          => "250",
            'height'         => "250",
            'maintain_ratio' => TRUE,
            );
            $this->load->library('image_lib'); // add library
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $rtn['file_name'] = $w['file_name'];
            return $rtn;
        }
        else
        {
            $rtn['error'] = trim($this->upload->display_errors());
            return $rtn;            
        }         
    }

    public function date_convert($dt, $tz, $df) {
        $date = new DateTime($dt);
        $date->setTimezone(new DateTimeZone($tz));

        return $date->format($df);
    }

    function getCountryList()
    {        
        $this->db->select('*');
        $this->db->from('tbl_country');     
        $this->db->where('is_active',1);        
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return false;
        }
    } 

    function getDriverData($id)
    {        
        $this->db->select('*');
        $this->db->from('tbl_drivers');
        $this->db->where('is_active',1);
        $this->db->where('id',$id);
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        if($query->num_rows() > 0)
        {
            return $query->row_array();
        }
        else
        {
            return false;
        }
    }

    function getPassengerData($id)
    {        
        $this->db->select('*');
        $this->db->from('tbl_users');
        $this->db->where('is_active',1);
        $this->db->where('id',$id);
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        if($query->num_rows() > 0)
        {
            return $query->row_array();
        }
        else
        {
            return false;
        }
    }

    function getCarRatesData($id=1)
    {        
        $this->db->select('fare');
        $this->db->from('tbl_car_type');        
        $this->db->where('id',$id);
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        if($query->num_rows() > 0)
        {
            return $query->row_array();
        }
        else
        {
            return false;
        }
    }

    function RandomString($length)
    {
        $keys = array_merge(range(0,9), range('a', 'z'),range('A','Z'));
        $key='';
        for($i=0; $i < $length; $i++)
        {
            $key .= $keys[array_rand($keys)];
        }
        return strtoupper($key);
    }

    function sendOrderDetails($driver_data, $order_id, $user_id, $trip_type)
    {
        /*Android Push Message*/
        $message = array("message"=>"You have new ride request","order_id"=>$order_id,"user_id"=>$user_id,"trip_type"=>$trip_type,"flag"=>"find_drivers");
        
        /*Android Push*/
        if($driver_data['device_type']=='A' && $driver_data['device_id']!=''){
            $this->send_gcm_notification_driver($driver_data['device_id'],$message);
        }

        /*IOS Push Message*/
        $body = array();
        $bodyI['aps'] = array('sound'=>'default','alert'=>"You have new ride request","order_id"=>$order_id,"user_id"=>$user_id,"trip_type"=>$trip_type,'tag'=>'find_drivers');
        /*IOS Push*/
        if($driver_data['device_type']=='I' && $driver_data['device_id']!=''){
            $this->send_notification_ios_driver($bodyI,$driver_data['device_id']);
        } 
             
    }

    function findDrivers($order_id)
    {
        $order_details = $this->getOrderDetails($order_id);
        $user_data = $this->Common_model->getUserDetails($order_details['user_id']);
        if($order_details['driver_id'] != '0')
        {
            $this->db->insert('tbl_temp_order',array('driver_id'=>$order_details['driver_id'],'order_id'=>$order_id));

            /*Set Previous Driver Free*/
            $sql_driver = "update tbl_drivers set is_free = 1 where id = ".$order_details['driver_id'];
            $this->db->query($sql_driver); 
        }

        
        if(count($order_details) > 1)
        {
            
            extract($order_details);

            $settings = $this->getSettings('driver');

            $sql = "SELECT *, 6371 * 2 * ASIN(SQRT( POWER(SIN(($pickup_latitude - latitude) * pi()/180 / 2), 2) + COS($pickup_latitude * pi()/180) * COS(latitude * pi()/180) *POWER(SIN(($pickup_longitude - longitude) * pi()/180 / 2), 2) )) as distance FROM tbl_drivers WHERE is_free=1 AND is_login=1 AND is_active=1 AND is_service = 1 AND is_verified = 1 AND otp_verified = 1 AND car_type=".$car_type." AND wallet > ".$settings['min_wallet']." HAVING  distance <= ".$settings['base_distance']." ORDER by distance";
            
            $result = $this->db->query($sql)->result_array();
            if(count($result) > 0)
            {

                foreach ($result as $key => $driver_data) 
                {
                    /*Fetch driver data that is in table temp_order*/
                    $query = "select * from tbl_temp_order where driver_id = ".$driver_data['id']. " AND order_id = ".$order_id;
                    $temp_data = $this->db->query($query)->num_rows();

                    if($temp_data == 0)
                    {
                        $sql_driver = "update tbl_drivers set is_free = 0 where id = ".$driver_data['id'];
                        $this->db->query($sql_driver); 

                        $sql_order = "update tbl_trip_details set driver_id = ".$driver_data['id']." where id = ".$order_id;
                        $this->db->query($sql_order);

                        /*Android Push Message*/
                        $message = array("message"=>"You have new ride request","order_id"=>$order_id, "user_id"=>$order_details['user_id'],"trip_type"=>$trip_type,"flag"=>"find_drivers");

                        /*IOS Push Message*/
                        $body = array();
                        $bodyI['aps'] = array('sound'=>'default','alert'=>"You have new ride request","order_id"=>$order_id, "user_id"=> $order_details['user_id'],"trip_type"=>$trip_type,'tag'=>'find_drivers');
                        /*Android Push*/
                        if($driver_data['device_type']=='A' && $driver_data['device_id']!=''){
                            $this->send_gcm_notification_driver($driver_data['device_id'],$message);
                        }
                        /*IOS Push*/
                        if($driver_data['device_type']=='I' && $driver_data['device_id']!=''){
                            $this->send_notification_ios_driver($bodyI,$driver_data['device_id']);
                        }   
                           
                        $data = ['status' => TRUE,'message' => $this->lang->line('text_trip_request_cancelled')];
                        /*$data = array("success"=>"1","method"=>"new_order","msg"=>"Request sent to another driver-".$driver_data['id'] );*/         
                        return $data;
                    }
                }
                $data = ['status' => TRUE,'message' => $this->lang->line('text_trip_request_cancelled')];
                /*Android Push Message*/
                $user_data = $this->getUserdetails($order_details['user_id']);
                $message = array("message"=>"Your ride request is accepted. Admin will contact you soon","order_id"=>$order_id, "user_id"=>$user_data['id'],"trip_type"=>$trip_type,"flag"=>"find_drivers");

                /*IOS Push Message*/
                $body = array();
                $bodyI['aps'] = array('sound'=>'default','alert'=>"Your ride request is accepted. Admin will contact you soon","order_id"=>$order_id, "user_id"=> $user_data['id'],"trip_type"=>$trip_type,'tag'=>'find_drivers');
                /*Android Push*/
                if($user_data['device_type']=='A' && $user_data['device_id']!=''){
                    //$this->send_gcm_notification_user($user_data['device_id'],$message);
                }
                /*IOS Push*/
                if($user_data['device_type']=='I' && $user_data['device_id']!=''){
                    //$this->send_notification_ios_user($bodyI,$user_data['device_id']);
                }  
                
            }
            else
            {
                $data = ['status' => TRUE,'message' => $this->lang->line('text_trip_request_cancelled')];
                /*Android Push Message*/
                $user_data = $this->getUserdetails($order_details['user_id']);
                $message = array("message"=>"Your ride request is accepted. Admin will contact you soon","order_id"=>$order_id, "user_id"=>$user_data['id'],"trip_type"=>$trip_type,"flag"=>"find_drivers");

                /*IOS Push Message*/
                $body = array();
                $bodyI['aps'] = array('sound'=>'default','alert'=>"Your ride request is accepted. Admin will contact you soon","order_id"=>$order_id, "user_id"=> $user_data['id'],"trip_type"=>$trip_type,'tag'=>'find_drivers');
                /*Android Push*/
                if($user_data['device_type']=='A' && $user_data['device_id']!=''){
                    //$this->send_gcm_notification_user($user_data['device_id'],$message);
                }
                /*IOS Push*/
                if($user_data['device_type']=='I' && $user_data['device_id']!=''){
                    //$this->send_notification_ios_user($bodyI,$user_data['device_id']);
                }
              
            }
            $sql_order = "update tbl_trip_details set driver_id = 0, status = 'Waiting' where id = ".$order_id;
            $this->db->query($sql_order);
        }
        else
        {
            $data = ['status' => TRUE,'message' => $this->lang->line('text_trip_request_cancelled')];
        }
        
        return $data;

    }

    function getPromoDetails($promocode)
    {
        $sql_promo = "select * from tbl_promocode where promocode = '".$promocode."'";
        return $this->db->query($sql_promo)->row_array();
    }

    function getPromoDetailsById($id)
    {
        $sql_promo = "select * from tbl_promocode where id = ".$id;
        return $this->db->query($sql_promo)->row_array();
    }

    function verifyPromocode($promocode, $user_id, $response_obj){
        $promo_data = $this->getPromoDetails($promocode);

        if(count($promo_data) > 1){
            $used_count = $this->db->get_where('tbl_usedpromocode',array('promocode' => $promocode, 'user_id'=>$user_id))->num_rows();
        
            if($promo_data['is_active'] == 0){
                $message = ['status' => FALSE, 'message' => $this->lang->line('text_trip_inactivepromocode')];
                $response_obj->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
            if($promo_data['end_date'] < date('Y-m-d')){
                $message = ['status' => FALSE, 'message' => $this->lang->line('text_trip_promocodeexpired')];
                $response_obj->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
            if($used_count > 0){
                $message = ['status' => FALSE, 'message' => $this->lang->line('text_trip_usedpromocode')];
                $response_obj->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
        else{
           $message = ['status' => FALSE, 'message' => $this->lang->line('text_trip_invalidpromocode')];
           $response_obj->response($message, REST_Controller::HTTP_BAD_REQUEST);
       }
        return $promo_data;
    }

    /*Generic Function to add and update data in table===========================================*/

    function addData($table, $params)
    {
        return $this->db->insert($table, $params);
    }

    function updateData($table, $data, $params)
    {
        return $this->db->update($table, $data, $params);
    }

    function invalidParams($response_obj)
    {
        $message = ['status' => FALSE, 'message' => "Invalid Parameters"];
        $response_obj->response($message, REST_Controller::HTTP_BAD_REQUEST); // HTTP_BAD_REQUEST (400)
    }

    /*END of Generic Functions===================================================================*/

    /*
     * Send Sign up mail for new registratiom
    */
    function SignupMail($name, $email, $verificationcode)
    {   
        /*$admindata = $this->getAdmin();*/
        $this->email->from('admin@hyperlinkinfosystem.in', 'SAIK Taxi');
        $this->email->to($email);

        $this->email->subject('SAIK Taxi Application Confirmation');

        // Send mail 
        $to  = $email;
        // subject      
        $sub = 'SAIK Taxi Application Confirmation';            
        // From
        $from = "admin@hyperlinkinfosystem.in";

        //$messag  = "Hello ".$first_name.", <br> <br>";
        $message  = "Welcome to SAIK Taxi Application!<br> <br>";
        $message .= "<br>Your Name: ".$name;
        $message .= "<br>Your Email: ".$email;
        $message .= "<br><br>Have a Good Day!<br>";
        $message .= "<br><br>The SAIK Taxi Application Team<br>";

        // To send HTML mail, the Content-type header must be set

        $this->email->set_header('MIME-Version', '1.0');
        $this->email->set_header('Content-type', 'text/html; charset=UTF-8');
        $this->email->set_header('Content-type', 'text/html; charset=iso-8859-1');
        $this->email->set_header('From', $from);
        $this->email->set_header('X-Priority', '3');

        $this->email->message($message);
        if($this->email->send())
            return true;
        else
            return false;
    }

    /*
     * Send forgot password mail for password change
    */
    function forgotpasswordMail($name, $email, $newpassword)
    {   
        
        $this->email->from('admin@hyperlinkinfosystem.in', 'SAIK Taxi');
        $this->email->to($email);

        $this->email->subject('SAIK Taxi Reset password request');
        $from = "admin@hyperlinkinfosystem.in";
        // message
        $message  = "Hello ".$name.", <br>";
        $message .= "<br>Name: ".$name;
        $message .= "<br>Email: ".$email;
        $message .= "<br>New Password: ".$newpassword;
        $message .= "<br><br>Thank You,<br>";
        $message .= "<br>The SAIK Taxi Application Team<br>";

        $this->email->set_header('MIME-Version', '1.0');
        $this->email->set_header('Content-type', 'text/html; charset=UTF-8');
        $this->email->set_header('Content-type', 'text/html; charset=iso-8859-1');
        $this->email->set_header('From', $from);
        $this->email->set_header('X-Priority', '3');

        $this->email->message($message);
        if($this->email->send())
            return true;
        else
            return false;
    }

    /*Get Car List*/
    function getCarList()
    {
        $sql = "select * from tbl_car_type where is_active = 1";
        $car_data = $this->db->query($sql)->result_array();
        foreach ($car_data as $key => $value) {
            $car_data[$key]['car_image'] = $this->car_image_path.$value['car_image'];
            $car_data[$key]['car_image_unselected'] = $this->car_image_path.$value['car_image_unselected'];
        }
        return $car_data;
    }

    /*Get Order Details*/
    function getOrderDetails($order_id,$user_id='')
    {
        # code...
        
        $trip_data = $this->db->get_where('tbl_trip_details',array('id'=>$order_id))->row_array();
        $user_data = '';
        if($user_id == ''){
            $user_data = $this->Common_model->getUserdetails($trip_data['user_id']);
        }
        else{
            $user_data = $this->Common_model->getUserdetails($user_id);
        }
        
        $driver_data = (object)array();
        if($trip_data['driver_id'] != '0'){
            $driver_data = $this->Common_model->getDriverdetails($trip_data['driver_id']);
        }
        
        $splitfare_temp = $this->db->get_where('tbl_splitfare',array('order_id'=>$order_id))->result_array();
        
        $splitfare_users = array();
        if(count($splitfare_temp) > 0){
            foreach ($splitfare_temp as $key => $value) {
                $splitfare_users[] = $this->Common_model->getUserdetails($value['receiver_id']);
            }
        }
        
        
        if(empty($trip_data)){
            return false;
        }

        $temp_distance = $trip_data['distance'];
        if($temp_distance == 0.00){
           $trip_data['distance'] = $temp_distance; 
        }
        
        if($user_data != '' && $user_data != null){
            $trip_data['user_data'] = $user_data;
        }
        else{
            $trip_data['user_data'] = (object)array();
        }
        $trip_data['car_data'] = $this->getCarById($trip_data['car_type']);
        $trip_data['driver_data'] = $driver_data;
        /*$trip_data['user_data'] = $this->user_model->get_user($trip_data['user_id']);
        $trip_data['driver_data'] = $this->driver_model->get_driver($trip_data['driver_id']);*/
        return $trip_data;
    }

    /*Get Car List*/
    function getCarById($id)
    {
        $car_data = $this->db->get_where('tbl_car_type',array('id'=>$id))->row_array();
        $car_data['car_image'] = $this->car_image_path.$car_data['car_image'];
        $car_data['car_image_unselected'] = $this->car_image_path.$car_data['car_image_unselected'];
        return $car_data;
    }

    /*Get Driver Details*/
    function getDriverDetails($driver_id)
    {
        # code...
        $driver_data = $this->db->get_where('tbl_drivers',array('id'=>$driver_id))->row_array();
        $profile = $driver_data['profile_image'];
        

        $driver_data['profile_image'] = base_url().'assets/uploads/profile_image/'.$profile;
        $driver_data['profile_image_thumb'] = base_url().'assets/uploads/profile_image/'.'thumb/'.$profile;
        return $driver_data;
    }

    /*Get User Details*/
    function getUserDetails($user_id)
    {
        # code...
        $user_data = $this->db->get_where('tbl_users',array('id'=>$user_id))->row_array();
        $profile_image = $user_data['profile_image'];
        $user_data['profile_image'] = base_url().'assets/uploads/user/'.$profile_image;
        $user_data['profile_image_thumb'] = base_url().'assets/uploads/user/thumb/'.$profile_image;
        return $user_data;
    }

    /*Get All Settings*/
    function getSettings($type)
    {   
        $row = $this->db->get_where('tbl_settings',array('type'=>$type))->result_array();
        foreach ($row as $key => $value) {
            $data[$value['meta_key']] = $value['meta_value'];
        }

        return $data;     
    }


    /*==============Send Push Notification===============================*/
    /*For Member*/
    function send_notification_ios_user($payload,$device_tokens)
    {   
        $development = true;
        $Production = true;
        $payload = json_encode($payload);
        
        $apns_url = NULL;
        $apns_cert = NULL;
        $apns_url1 = NULL;
        $apns_cert1 = NULL;
        $apns_port = 2195;
        
        if($development)
        {
            $apns_url = 'gateway.sandbox.push.apple.com';
            //$apns_cert = '/home/hyperlinkserver/public_html/ultimatetaxi/application/models/pem/Member_APNS_Dev.pem';
            $apns_cert = './assets/pem/Member_APNS_Dev.pem';
        
            
            $stream_context = stream_context_create();
            stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
            stream_context_set_option($stream_context, 'ssl', 'passphrase',"hyperlink");
            
            $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
            
            if (!$apns) {
                print "Failed to connect $err $errstr\n";
                //exit;
                $success_connection = 0;
            } else {
                //echo "ok";
                $success_connection = 1;
            }

            if($device_tokens )
            {
                $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device_tokens)) . chr(0) . chr(strlen($payload)) . $payload;
                fwrite($apns, $apns_message);
                //var_dump($apns_message);
            }
            @socket_close($apns);
            @fclose($apns);
        }
        /*----------Production--------------*/
        if($Production)
        {
            $apns_url1 = 'gateway.push.apple.com';
            $apns_cert1 = './assets/pem/Member_APNS_Live.pem';
            
            $stream_context1 = stream_context_create();
            stream_context_set_option($stream_context1, 'ssl', 'local_cert', $apns_cert1);
            stream_context_set_option($stream_context1, 'ssl', 'passphrase',"hyperlink");
            
            $apns1 = stream_socket_client('ssl://' . $apns_url1 . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context1);
            
            if (!$apns1) {
                print "Failed to connect $err $errstr\n";
                //exit;
                $success_connection = 0;
            } else {
                //echo "ok";
                $success_connection = 1;
            }
            

            if($device_tokens )
            {
                $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device_tokens)) . chr(0) . chr(strlen($payload)) . $payload;
                fwrite($apns1, $apns_message);
                //var_dump($apns_message);
            }
            @socket_close($apns1);
            @fclose($apns1);
        }
    
        return;
        // END CODE FOR PUSH NOTIFICATIONS TO ALL USERS
    }

    /*For Driver*/
    function send_notification_ios_driver($payload,$device_tokens)
    {   
        $development = true;
        $Production = true;
        $payload = json_encode($payload);
        
        $apns_url = NULL;
        $apns_cert = NULL;
        $apns_url1 = NULL;
        $apns_cert1 = NULL;
        $apns_port = 2195;
        
        if($development)
        {
            $apns_url = 'gateway.sandbox.push.apple.com';
            $apns_cert = './assets/pem/Driver_APNS_Dev.pem';
        
        
            $stream_context = stream_context_create();
            stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
            stream_context_set_option($stream_context, 'ssl', 'passphrase',"hyperlink");
            
            $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
            
            if (!$apns) {
                print "Failed to connect $err $errstr\n";
                //exit;
                $success_connection = 0;
            } else {
                //echo "ok";
                $success_connection = 1;
            }

            if($device_tokens )
            {
                $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device_tokens)) . chr(0) . chr(strlen($payload)) . $payload;
                fwrite($apns, $apns_message);
                //var_dump($apns_message);
            }
            @socket_close($apns);
            @fclose($apns);
        }
        /*----------Production--------------*/
        if($Production)
        {
            $apns_url1 = 'gateway.push.apple.com';
            $apns_cert1 = './assets/pem/Driver_APNS_Live.pem';
        
            $stream_context1 = stream_context_create();
            stream_context_set_option($stream_context1, 'ssl', 'local_cert', $apns_cert1);
            stream_context_set_option($stream_context1, 'ssl', 'passphrase',"hyperlink");
            
            $apns1 = stream_socket_client('ssl://' . $apns_url1 . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context1);
            
            if (!$apns1) {
                print "Failed to connect $err $errstr\n";
                //exit;
                $success_connection = 0;
            } else {
                //echo "ok";
                $success_connection = 1;
            }
            

            if($device_tokens )
            {
                $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device_tokens)) . chr(0) . chr(strlen($payload)) . $payload;
                fwrite($apns1, $apns_message);
                //var_dump($apns_message);
            }
            @socket_close($apns1);
            @fclose($apns1);
        }
        
        return;
        // END CODE FOR PUSH NOTIFICATIONS TO ALL USERS
    }


    function send_gcm_notification_driver($registatoin_ids, $message){

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $fields = array(
            'registration_ids' => array($registatoin_ids),
            'data' => array("message" => $message)
        );

        $headers = array(
            'Authorization: key=AIzaSyARpKx0p97rF3twiA5EGhDgdPJrOsL9ea0',
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        //echo "+++++++++++++++||||";
        //var_dump($result);
        return;         
    }

    function send_gcm_notification_user($registatoin_ids, $message){
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $fields = array(
            'registration_ids' => array($registatoin_ids),
            'data' => array("message" => $message)
        );

        $headers = array(
            'Authorization: key=AIzaSyCHbiOurx7O5xdHNQc186kmVDkUqYW37M0',
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        //echo "+++++++++++++++||||";
        //var_dump($result);
        return;         
    }

    /*function send_notification_window_user($UDID_URL, $message){
        
        $url_parts = parse_url($UDID_URL);
        $uniqe_url = $url_parts['scheme'].'://'.$url_parts['host'].$url_parts['path'].'?token='.urlencode(str_replace("token=","",$url_parts['query']));
        $objCls    = new WPN_Member();
        $build_tile_xml_data = $objCls->build_tile_xml($message);
        $res = $objCls->post_tile($uniqe_url, $build_tile_xml_data, $type = WPNTypesEnum::Toast, $tileTag = '');    
        
        if($res->httpCode==200)
            return true;
        else
            return false;
        //var_dump($res);exit();
    }
   
    function send_notification_window_driver($UDID_URL, $message){
        
        $url_parts = parse_url($UDID_URL);
        $uniqe_url = $url_parts['scheme'].'://'.$url_parts['host'].$url_parts['path'].'?token='.urlencode(str_replace("token=","",$url_parts['query']));
        $objCls    = new WPN_Driver();
        $build_tile_xml_data = $objCls->build_tile_xml($message);
        $res = $objCls->post_tile($uniqe_url, $build_tile_xml_data, $type = WPNTypesEnum::Toast, $tileTag = '');    
        if($res->httpCode==200)
            return true;
        else
            return false;
        //var_dump($res);
    }*/

    function object_to_array($obj) {
        if(is_object($obj)) $obj = (array) $obj;
        if(is_array($obj)) {
            $new = array();
            foreach($obj as $key => $val) {
                $new[$key] = $this->object_to_array($val);
            }
        }
        else $new = $obj;
        return $new;       
    }

    /*
     * Send documents submission mail for driver upload documents
    */
    function sendVerifyMail($email, $name)
    {   
        
        $this->email->to($email);
        $this->email->from('admin@hyperlinkinfosystem.in', 'SAIK Taxi Infosystem');

        $this->email->subject('Documents Verified');
        $from = 'SAIK Taxi Infosystem';
        // message
        $message  = "Hello ".$name.", <br>";
        $message .= "<br>Your documents have been verified";
        $message .= "<br>Please login to continue";
        $message .= "<br><br>Thank You<br>";

        $this->email->set_header('MIME-Version', '1.0');
        $this->email->set_header('Content-type', 'text/html; charset=UTF-8');
        $this->email->set_header('Content-type', 'text/html; charset=iso-8859-1');
        /*$this->email->set_header('From', $from);*/
        $this->email->set_header('X-Priority', '3');

        $this->email->message($message);

        if($this->email->send())
            return true;
        else
            return false;
    }
}
