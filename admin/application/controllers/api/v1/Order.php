<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';


class Order extends REST_Controller {

    function __construct()
    {   
        // Construct the parent class
        parent::__construct();
        $this->load->model('api/order_model');
        $this->load->model('api/user_model');
        $this->load->model('api/driver_model');

        //Listing record per page
        $this->per_page = 10;

        //validate driver header token
        $this->Common_model->validate_header_token($this);
        /*include("WindowsPush.php");*/
    }

     /*
     * Get Order Details api
    */
    function get_order_details_get()
    {   
        $input = $this->get();
        
        if(!isset($input['id']) || $input['id'] == '')     
        {   
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else
        {
            $trip_data = $this->Common_model->getOrderDetails($input['id']);
            if($trip_data){
                $message = ['status' => TRUE,'message' => $this->lang->line('trip_details_found_success'),'data'=>$trip_data];
                $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
            }
            else{
                $message = ['status' => FALSE,'message' => $this->lang->line('trip_details_found_error')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
           
        }
    }

    /*
     * Order State Management api
    */
    function order_status_get()
    {   
        $input = $this->get();
        
        if(!isset($input['id']) || $input['id'] == '' ||
            !isset($input['user_type']) || $input['user_type'] == '')     
        {   
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else
        {
            $is_service = '1';
            if($input['user_type'] == 'user'){
                $trip_data = $this->db->query("SELECT * from tbl_trip_details 
                    where 
                    (user_id = ".$input['id']." AND (status IN ('Assigned', 'Arrived', 'Processing') OR (status IN ('Completed') AND payment_status = 0))) OR 
                    (".$input['id']." IN (select receiver_id from tbl_splitfare where status = 'Accepted' AND payment_status = 0 AND order_id = tbl_trip_details.id) AND status IN ('Completed')) ORDER BY FIELD( tbl_trip_details.status, 'Arrived', 'Processing' ) DESC LIMIT 1")->row_array();
                /*echo "<pre>"; 
                print_r($this->db);
                die;*/
            }
            else{
                $driver_data = $this->driver_model->get_driver($input['id']);
                $is_service = $driver_data['is_service'];
                $trip_data = $this->db->query("SELECT * from tbl_trip_details where driver_id = ".$input['id']." AND status IN ('Waiting','Arrived','Assigned' ,'Processing') LIMIT 1")->row_array();
            }

            if(empty($trip_data)){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_future_trips_notfound'),"is_service"=>$is_service];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // OK (200)
            }
            else{
                
                $user_data = $this->user_model->get_user($trip_data['user_id']);
                if($user_data != '' && $user_data != null){
                    $trip_data['user_data'] = $user_data;
                }
                else{
                    $trip_data['user_data'] = (object)array();
                }
                if($trip_data['driver_id'] != 0){
                    $trip_data['driver_data'] = $this->driver_model->get_driver($trip_data['driver_id']);
                }
                else{
                    $trip_data['driver_data'] = (object)array();
                }
                $trip_data['car_data'] = $this->Common_model->getCarById($trip_data['car_type']);
                
                $message = ['status' => TRUE,'message' => $this->lang->line('trip_found_success'),'data'=>$trip_data];
                $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }
    }

    /*
     * Fare Estimation api
    */
    function fare_estimation_post()
    {   
        $input = $this->post();
        
        if(!isset($input['user_id']) || $input['user_id'] == '' ||
            !isset($input['pickup_latitude']) || $input['pickup_latitude'] == '' ||
            !isset($input['pickup_longitude']) || $input['pickup_longitude'] == '' ||
            !isset($input['dropoff_latitude']) || $input['dropoff_latitude'] == '' ||
            !isset($input['dropoff_longitude']) || $input['dropoff_longitude'] == '' ||
            !isset($input['car_type']) || $input['car_type'] == ''  ) 
                
        {   
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else
        {
            $splitfare_contact = array();
            if(!empty($this->post('splitfare_contact')) && $this->post('splitfare_contact') != ''){
                $contact_data = json_decode($input['splitfare_contact'],true);
                /*echo "<pre>";
                print_r($contact_data);*/
                
                foreach ($contact_data as $key => $value) {
                    $flag = 0;
                    foreach ($value['phoneList'] as $phone_value) {
                        $temp_data = $this->db->get_where('tbl_users',array('phone'=>$phone_value))->row_array();
                        if($temp_data){
                            if($input['user_id'] != $temp_data['id']){
                                $flag = 1;     
                            }
                        }
                    }
                    if($flag == 1){
                        $splitfare_contact[] = $value['name'];
                    }
                }
            }
            
            /*Default area distance in radius*/
            //$settings = $this->Common_model->getSettings('user');

            /*print_r($settings); die;*/
            /*Array
                (
                    [service_tax] => 0.14
                    [cess] => 0.05
                    [referal_amount] => 50
                    [ride_now_cancel_amount] => 5
                    [ride_later_cancel_amount] => 5
                    [ride_now_cancel_time] => 5
                    [ride_later_cancel_time] => 30
                    [per_page] => 10
                    [amount_per_min] => 0.1
                    [base_fare] => 1
                    [per_mile] => 1
                    [safety_fee] => 1.2
                    [minimum_fare] => 4.5
                    [driver_percent] => 80
                    [owner_percent] => 20
                )
            */

            $car_data = $this->db->get_where('tbl_car_type',array('id'=>$input['car_type']))->row_array();

            /*print_r($car_data); die;*/

            /*Array
            (
                [id] => 1
                [car_type] => Economy
                [car_image] => economy.png
                [base_fare] => 10.00
                [rate_per_km] => 7.00
                [rate_per_min] => 1.00
                [cancellation_amount] => 25.00
                [is_active] => 1
                [insertdate] => 2016-03-23 11:10:02
                )*/

            //Calculate estimate time to reach the pickup point--------------------
            $etadata = file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=".$input['pickup_latitude'].','.$input['pickup_longitude']."&destinations=".$input['dropoff_latitude'].','.$input['dropoff_longitude']."&language=en-EN&sensor=false");
            
            $etadata = json_decode($etadata);   
            
            $time = 0;  
            $distance = 0;
           
            foreach($etadata->rows[0]->elements as $road) {
                if($road->status == 'OK'){
                    $time = $road->duration->value;
                    $distance = $road->distance->value;
                    $distance = ($distance/1000);
                    /*Convert km to miles*/
                    /*$distance *= 0.62137;*/
                    $distance = round($distance,2);
                    $time = ceil($time/60);
                }
                else{
                    $message = ['status' => FALSE,'message' => "Please enter valid location"];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
                }
            }

            $data = $input;
            $data['distance'] = (string)$distance;
            $data['time'] = (string)$time;
            $data['base_fare'] = (string)$car_data['base_fare'];
            $amount = $car_data['base_fare'] + ($car_data['rate_per_km'] * $distance) + ($car_data['rate_per_min'] * $time);
            $data['estimate_fare'] = (string)($amount);
            $data['splitfare_contact'] = $splitfare_contact;
            
            $message = ['status' => TRUE,'message' => $this->lang->line('fare_estimation_success'),'data'=>$data];
            $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
        }
    }

    function verify_promocode_get()
    {
        $input = $this->get();
        
        if(!isset($input['user_id']) || $input['user_id'] == '' ||
            !isset($input['promocode']) || $input['promocode'] == '')     
        {   
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else{
            $promocode_data = $this->Common_model->verifyPromocode($input['promocode'], $input['user_id'], $this);
            if($promocode_data){
                $message = ['status' => TRUE,'message' => "Promocode applied successfully"];
                $this->set_response($message, REST_Controller::HTTP_OK);
            }
            else{
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_invalidpromocode')];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    /*
     * Place Order api
    */
    function place_order_post()
    {   

        $this->form_validation->set_rules('user_id','User Id','required|numeric');
        $this->form_validation->set_rules('car_type','Car Type','required|numeric');
        $this->form_validation->set_rules('pickup_address','Pickup Address','required');
        $this->form_validation->set_rules('pickup_latitude','Pickup Latitude','required');
        $this->form_validation->set_rules('pickup_longitude','Pickup Longitude','required');
        
        $this->form_validation->set_rules('trip_type','Trip Type','required');

        $AllPostData = $this->post();
        extract($AllPostData);

        $user_data = $this->Common_model->getUserDetails($user_id);
        if($this->form_validation->run())     
        {   
            if($this->post('trip_type') == 'now'){
                $tripdatetime = date('Y-m-d H:i:s');
            }
            else{
                if($this->post('tripdatetime') != ''){
                    $tripdatetime = $this->post('tripdatetime');
                }
                else{
                    $message = ['status' => FALSE,'message' => $this->lang->line('trip_date_time')];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); 
                }  
            }

            $params = array(
                'user_id' => $this->post('user_id'),
                'car_type' => $this->post('car_type'),
                'pickup_address' => $this->post('pickup_address'),
                'pickup_latitude' => $this->post('pickup_latitude'),
                'pickup_longitude' => $this->post('pickup_longitude'),
                'payment_type' => $this->post('payment_type'),
                'trip_type' => $this->post('trip_type'),
                'tripdatetime' => $tripdatetime,
                );

            if($this->post('dropoff_latitude') != ''){
                $params['dropoff_latitude'] = $this->post('dropoff_latitude'); 
            } 

            if($this->post('dropoff_longitude') != ''){
                $params['dropoff_longitude'] = $this->post('dropoff_longitude'); 
            }

            if($this->post('dropoff_address') != ''){
                $params['dropoff_address'] = $this->post('dropoff_address'); 
            }

            /*Verify Promocode*/
            if($this->post('promocode') != ''){
                $promocode_data = $this->Common_model->verifyPromocode($this->post('promocode'), $this->post('user_id'), $this);
                if($promocode_data){
                    $params['promocode_id'] = $promocode_data['id'];
                    $params['promocode_amount'] = $promocode_data['amount'];
                }
                else{
                    $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_invalidpromocode')];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
                }
            }

            $settings = $this->Common_model->getSettings('user');
            $params['driver_owner_percent'] = $settings['driver_percent'].','.$settings['owner_percent'];
            $car_data = $this->db->get_where('tbl_car_type',array('id'=>$car_type))->row_array();

            /*print_r($car_data); die;*/

            /*Array
            (
                [id] => 1
                [car_type] => Economy
                [car_image] => economy.png
                [base_fare] => 10.00
                [rate_per_km] => 7.00
                [rate_per_min] => 1.00
                [cancellation_amount] => 25.00
                [is_active] => 1
                [insertdate] => 2016-03-23 11:10:02
                )*/

            $params_rate = array();
            /*Check if distance is 0 or not*/
            if($this->post('dropoff_address') != '' && $this->post('dropoff_latitude') != '' && $this->post('dropoff_longitude') != ''){
                $etadata = file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=".$pickup_latitude.','.$pickup_longitude."&destinations=".$dropoff_latitude.','.$dropoff_longitude."&language=en-EN&sensor=false");
                
                $etadata = json_decode($etadata);   
                
                $new_distance = 0;
                foreach($etadata->rows[0]->elements as $road) {
                    $time = $road->duration->value;
                    $time = ceil($time/60);
                    $new_distance = $road->distance->value;
                    $new_distance = ($new_distance/1000);
                    /*Convert km to miles*/
                    /*$new_distance *= 0.62137;*/
                    $new_distance = round($new_distance,2);
                }
                
                $tot_amount = $car_data['base_fare'] + ($car_data['rate_per_km'] * $new_distance) + ($car_data['rate_per_min'] * $time);
                /*Assign all amount to splitamount*/
                $split_amount = $tot_amount;
                $split_base_fare = $car_data['base_fare'];

                $params_rate = array(
                    'distance' => $new_distance,
                    'tot_time' => $time,
                    'tot_amount' => $tot_amount,
                    );
            }


            $order_id = "";
            /*echo $tripdatetime; die;*/
            /*Assign Ride To Driver*/
            if($AllPostData['trip_type'] == 'now'){
                $settings = $this->Common_model->getSettings('driver');

                /*get car type data*/
                $car_type_data = $this->db->get_where('tbl_car_type',array('id'=>$this->post('car_type')))->row_array();
                /*SQL for getting car type ids*/
                $sql_car_type_id = "select id from tbl_car_type WHERE car_order >= ".$car_type_data['car_order']." AND car_order <= ".($car_type_data['car_order']+1)." ";
        
                $sql = "SELECT *, 6371 * 2 * ASIN(SQRT( POWER(SIN(($pickup_latitude - latitude) * pi()/180 / 2), 2) + COS($pickup_latitude * pi()/180) * COS(latitude * pi()/180) *POWER(SIN(($pickup_longitude - longitude) * pi()/180 / 2), 2) )) as distance FROM tbl_drivers WHERE car_type IN (".$sql_car_type_id.") AND is_free=1 AND is_login=1 AND is_active=1 AND is_service = 1 AND is_verified = 1 AND otp_verified = 1 HAVING  distance <= ".$settings['base_distance']." ORDER by distance LIMIT 1";
                $result = $this->db->query($sql)->row_array();
                if($result)
                {
                    $order_id = $this->order_model->placeOrder($params);
                    /*Default area distance in radius*/
                    
                    if($this->post('dropoff_address') != '' && $this->post('dropoff_latitude') != '' && $this->post('dropoff_longitude') != ''){
                        $this->db->update('tbl_trip_details',$params_rate,array('id'=>$order_id));
                    }

                    $this->db->update('tbl_drivers',array('is_free'=>'0'),array('id'=>$result['id']));
                    $this->db->update('tbl_trip_details',array('driver_id'=>$result['id'],'request_time'=>date('Y-m-d H:i:s')),array('id'=>$order_id));
                    /*Send Push to driver*/
                    $this->Common_model->sendOrderDetails($result, $order_id, $user_id, $AllPostData['trip_type']);
                }
                else{
                    $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_drivernotfound')];
                    $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) 
                }
            }
            else{
                $order_id = $this->order_model->placeOrder($params);
                if($this->post('dropoff_address') != ''){
                    $this->db->update('tbl_trip_details',$params_rate,array('id'=>$order_id));
                }
            }

            $order_data = $this->Common_model->getOrderDetails($order_id);

            $message = ['status' => TRUE,'message' => $this->lang->line('text_order_success'),'data'=>$order_data];
            $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201)
        }
        else
        {
            $fields_validation = $this->validation_errors();
            $message = ['status' => FALSE,'message' => (string)$fields_validation[0]];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
    }

    /*Accept Ride*/
    function accept_order_get()
    {
        $order_id = $this->get('order_id');
        $driver_id = $this->get('driver_id');
        if(empty($order_id) || empty($driver_id)){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else{
            $order_data = $this->Common_model->getOrderDetails($order_id);
            if($order_data['status'] == "Assigned"){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_assigned')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['driver_id'] != $driver_id){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_not_assignedtodriver')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Completed'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_completed')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Cancelled'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_cancelled')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else{
                $result = $this->order_model->acceptOrder($order_id, $driver_id);
                $user_data = $this->Common_model->getUserDetails($order_data['user_id']);
                $driver_data = $this->Common_model->getDriverDetails($driver_id);

                //Android Push Message
                $message = array("message"=>"Driver ".$driver_data['fname']." ".$driver_data['lname']." has accepted your ride request",'driver_id'=>$driver_id,'order_id'=>$order_id,"flag"=>"accept_order",'trip_type'=>$order_data['trip_type']);

                    //IOS Push Message
                $body = array();
                $bodyI['aps'] = array('sound'=>'default','alert'=>"Driver ".$driver_data['fname']." ".$driver_data['lname']." has accepted your ride request",'driver_id'=>$driver_id,'order_id'=>$order_id,'tag'=>'accept_order','trip_type'=>$order_data['trip_type']);

                /*Android Push*/
                if($user_data['device_type']=='A' && $user_data['device_id']!=''){
                    $registatoin_ids_D = $user_data['device_id'];
                    $this->Common_model->send_gcm_notification_user($registatoin_ids_D,$message);
                }
                /*IOS Push*/
                if($user_data['device_type']=='I' && $user_data['device_id']!=''){
                    $this->Common_model->send_notification_ios_user($bodyI,$user_data['device_id']);
                }
                $order_data = $this->Common_model->getOrderDetails($order_id);
                $message = ['status' => TRUE,'message' => 'Ride is accepted successfully','data'=>$order_data];
                $this->response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }
    }

    /*Decline Ride and assign to other driver*/
    function decline_order_get()
    {
        $order_id = $this->get('order_id');
        $driver_id = $this->get('driver_id');
        
        if(empty($order_id) || empty($driver_id))
        {
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else
        {
            $order_data = $this->Common_model->getOrderDetails($order_id);

            if($order_data['driver_id'] != $driver_id){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_not_assignedtodriver')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Completed'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_completed')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Cancelled'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_cancelled')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Processing'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_processing')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else{
                $message = $this->Common_model->findDrivers($order_id);
                $this->response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }       
    }

    /*Arrived*/
    function arrived_get()
    {
        $order_id = $this->get('order_id');
        $driver_id = $this->get('driver_id');
        if(empty($order_id) || empty($driver_id)){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else{
            $order_data = $this->Common_model->getOrderDetails($order_id);
            if($order_data['driver_id'] == 0){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_not_assignedtodriver')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Completed'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_completed')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Cancelled'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_cancelled')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else{
                $result = $this->order_model->arrived($order_id);
                $user_data = $this->Common_model->getUserDetails($order_data['user_id']);
                $driver_data = $this->Common_model->getDriverDetails($driver_id);

                //Android Push Message
                $message = array("message"=>"Driver ".$driver_data['fname']." ".$driver_data['lname']." has arrived at your pickup location",'driver_id'=>$driver_id,'order_id'=>$order_id,"flag"=>"arrived",'trip_type'=>$order_data['trip_type']);

                    //IOS Push Message
                $body = array();
                $bodyI['aps'] = array('sound'=>'default','alert'=>"Driver ".$driver_data['fname']." ".$driver_data['lname']." has arrived at your pickup location",'driver_id'=>$driver_id,'order_id'=>$order_id,'tag'=>'arrived','trip_type'=>$order_data['trip_type']);

                /*Android Push*/
                if($user_data['device_type']=='A' && $user_data['device_id']!=''){
                    $registatoin_ids_D = $user_data['device_id'];
                    $this->Common_model->send_gcm_notification_user($registatoin_ids_D,$message);
                }
                /*IOS Push*/
                if($user_data['device_type']=='I' && $user_data['device_id']!=''){
                    $this->Common_model->send_notification_ios_user($bodyI,$user_data['device_id']);
                }
                $order_data = $this->Common_model->getOrderDetails($order_id);
                $message = ['status' => TRUE,'message' => 'Arrived successful','data'=>$order_data];
                $this->response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }
    }

    /*Pickup Ride*/
    function pickup_get()
    {
        $order_id = $this->get('order_id');
        $driver_id = $this->get('driver_id');
        if(empty($order_id) || empty($driver_id)){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else{
            $order_data = $this->Common_model->getOrderDetails($order_id);
            if($order_data['status'] != 'Arrived'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_before_reach_pickup')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['driver_id'] == 0){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_not_assignedtodriver')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Completed'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_completed')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Cancelled'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_cancelled')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else{
                $result = $this->order_model->pickup($order_id);
                $user_data = $this->Common_model->getUserDetails($order_data['user_id']);
                $driver_data = $this->Common_model->getDriverDetails($driver_id);

                //Android Push Message
                $message = array("message"=>"Your ride has been started",'driver_id'=>$driver_id,'order_id'=>$order_id,"flag"=>"pickup",'trip_type'=>$order_data['trip_type']);

                    //IOS Push Message
                $body = array();
                $bodyI['aps'] = array('sound'=>'default','alert'=>"Your ride has been started",'driver_id'=>$driver_id,'order_id'=>$order_id,'tag'=>'pickup','trip_type'=>$order_data['trip_type']);

                /*Android Push*/
                if($user_data['device_type']=='A' && $user_data['device_id']!=''){
                    $registatoin_ids_D = $user_data['device_id'];
                    $this->Common_model->send_gcm_notification_user($registatoin_ids_D,$message);
                }
                /*IOS Push*/
                if($user_data['device_type']=='I' && $user_data['device_id']!=''){
                    $this->Common_model->send_notification_ios_user($bodyI,$user_data['device_id']);
                }

                $order_data = $this->Common_model->getOrderDetails($order_id);
                $message = ['status' => TRUE,'message' => 'Pickup successful','data'=>$order_data];
                $this->response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }
    }

     /*Dropoff Ride*/
    function dropoff_post()
    {
        extract($this->post());

        //print_r($this->post());

        if(!isset($order_id) || $order_id == '' || 
            !isset($driver_id) || $driver_id == '' ||
            !isset($dropoff_address) || $dropoff_address == '' ||
            !isset($dropoff_latitude) || $dropoff_latitude == '' ||
            !isset($dropoff_longitude) || $dropoff_longitude == '' )
        {
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else{
            $order_data = $this->Common_model->getOrderDetails($order_id);
            if($order_data['driver_id'] == 0){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_not_assignedtodriver')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Completed'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_completed')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Cancelled'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_cancelled')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] != 'Processing'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_dropoff_order_not_started')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else{
                $splitting_ratio = explode(',', $order_data['driver_owner_percent']);
                $driver_ratio = $splitting_ratio[0]; $owner_ratio = $splitting_ratio[1];

                $temp_time = date('Y-m-d H:i:s');

                $end_time = date("s",strtotime($temp_time));

                $end_datetime = date('Y-m-d H:i',strtotime($temp_time));
                if($end_time != 0){
                    
                    $end_datetime = date("Y-m-d H:i", time() + 60);
                }

                /*$end_datetime = date('Y-m-d H:i');*/

                $to_time = strtotime($end_datetime);
                $from_time = strtotime($order_data['start_datetime']);
                $time_diff = round(abs($to_time - $from_time),2);
                $tot_time = ceil($time_diff / 60);

                 /*Default area distance in radius*/
                 $settings = $this->Common_model->getSettings('user');

                $car_data = $this->db->get_where('tbl_car_type',array('id'=>$order_data['car_type']))->row_array();

                /*print_r($car_data); die;*/

                /*Array
                (
                    [id] => 1
                    [car_type] => Economy
                    [car_image] => economy.png
                    [base_fare] => 10.00
                    [rate_per_km] => 7.00
                    [rate_per_min] => 1.00
                    [cancellation_amount] => 25.00
                    [is_active] => 1
                    [insertdate] => 2016-03-23 11:10:02
                    )*/

                $etadata = file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=".$order_data['pickup_latitude'].",".$order_data['pickup_longitude']."&destinations=".$dropoff_latitude.",".$dropoff_longitude."&language=en-EN&sensor=false");
                
                $etadata = json_decode($etadata);  
                
                $new_distance = 0;
                foreach($etadata->rows[0]->elements as $road) {
                    if($road->status == 'OK'){
                        $time = $road->duration->value;
                        $new_distance = $road->distance->value;
                        $new_distance = ($distance/1000);
                        /*Convert km to miles*/
                        /*$new_distance *= 0.62137;*/
                        $new_distance = round($distance,2);
                    }
                    else{
                        $message = ['status' => FALSE,'message' => "Location not found"];
                        $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
                    }
                }

                if($new_distance > $distance){
                    $distance = $new_distance;
                }

                if(isset($waiting_time_min) && $waiting_time_min != ''){
                    $tot_time -= $waiting_time_min;
                }
                else{
                    $waiting_time_min = 0;
                }
                
                $tot_amount = $car_data['base_fare'] + ($car_data['rate_per_km'] * $distance) + ($car_data['rate_per_min'] * $tot_time) + ($car_data['waiting_min'] * $waiting_time_min);

                /*Calculate driver ratio and owner ratio*/
                $owner_earning = (($tot_amount * $owner_ratio) / 100);
                $driver_earning = (($tot_amount * $driver_ratio) / 100);
                /*Deduct driver in wallet history*/
                $walletparams = array('user_id' => $driver_id, 'order_id' => $order_id, 'amount' => $owner_earning,'type' => 'Debit', 'user_type'=>'driver','description'=>'Debit trip wallet amount');
                $this->db->insert('tbl_wallet', $walletparams);
                /*update driver wallet amount*/
                $this->db->query('update tbl_drivers set wallet = wallet - '.$owner_earning.' where id = '.$driver_id);

                /*Verify Promocode*/
                $promocode_amount = 0;
                if($order_data['promocode_id'] > 0){
                    $promocode_data = $this->db->get_where('tbl_promocode',array('id'=>$order_data['promocode_id']))->row_array();
                    if($promocode_data){
                        if($promocode_data['promocodetype'] == 'Freeride'){
                            $promocode_amount = $tot_amount;
                            $tot_amount = 0;
                        }
                        else if($promocode_data['promocodetype'] == 'Percentage'){
                            $promocode_amount = ($tot_amount * ($promocode_data['amount'] / 100));
                            $tot_amount = $tot_amount - ($tot_amount * ($promocode_data['amount'] / 100));
                        }
                        else{
                            $promocode_amount = $promocode_data['amount'];
                            $tot_amount = $tot_amount - $promocode_data['amount'];   
                        }
                        $params = array(
                                    'user_id' => $order_data['user_id'],
                                    'promocode_id' => $promocode_data['id'],
                                    'promocode' => $promocode_data['promocode'],
                                    'promocodetype' => $promocode_data['promocodetype'],
                                    'amount' => $promocode_amount,
                                    );
                        $this->db->insert('tbl_usedpromocode',$params);
                    }
                    else{
                        $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_invalidpromocode')];
                        $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
                    }
                }
                $user_data = $this->Common_model->getUserDetails($order_data['user_id']);

                if($user_data['wallet'] > $tot_amount){
                    $this->db->update('tbl_users',array('wallet'=>$user_data['wallet'] - $tot_amount),array('id'=>$order_data['user_id']));
                    $tot_amount = 0;
                }
                else{
                    $this->db->update('tbl_users',array('wallet'=>0),array('id'=>$order_data['user_id']));
                    $tot_amount = $tot_amount - $user_data['wallet'];
                }

                if($tot_amount < 0){
                    $tot_amount = 0;
                }

                /*Assign all amount to splitamount*/
                $split_amount = $tot_amount;
                $split_base_fare = $car_data['base_fare'];
                
                $data = array(
                    'distance' => $distance,
                    'ride_time' => $tot_time,
                    'waiting_time' => $waiting_time_min,
                    'tot_time' => ($tot_time + $waiting_time_min),
                    'promocode_amount' => $promocode_amount,
                    'tot_amount' => $tot_amount,
                    'split_amount' => $split_amount,
                    'split_base_fare' => $split_base_fare,
                    'end_datetime' => $end_datetime,
                    'dropoff_address' => $dropoff_address,
                    'dropoff_latitude' => $dropoff_latitude,
                    'dropoff_longitude' => $dropoff_longitude,
                    'owner_earning' => $owner_earning,
                    'driver_earning' => $driver_earning,
                    'status' => 'Completed'
                    );

                $result = $this->order_model->dropoff($order_id, $data, $driver_id);
                
                $driver_data = $this->Common_model->getDriverDetails($driver_id);
                $this->driver_model->free_driver($driver_id);

                //Android Push Message
                $message = array("message"=>"Your ride is completed",'driver_id'=>$driver_id,'order_id'=>$order_id,"flag"=>"dropoff",'trip_type'=>$order_data['trip_type']);

                    //IOS Push Message
                $body = array();
                $bodyI['aps'] = array('sound'=>'default','alert'=>"Your ride is completed",'driver_id'=>$driver_id,'order_id'=>$order_id,'tag'=>'dropoff','trip_type'=>$order_data['trip_type']);

                /*Android Push*/
                if($user_data['device_type']=='A' && $user_data['device_id']!=''){
                    $registatoin_ids_D = $user_data['device_id'];
                    $this->Common_model->send_gcm_notification_user($registatoin_ids_D,$message);
                }
                /*IOS Push*/
                if($user_data['device_type']=='I' && $user_data['device_id']!=''){
                    $this->Common_model->send_notification_ios_user($bodyI,$user_data['device_id']);
                }

                $order_data = $this->Common_model->getOrderDetails($order_id);
                $message = ['status' => TRUE,'message' => $this->lang->line('text_order_complete'),'data'=>$order_data];
                $this->response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }
    }

    /*
     * Past Ride api
    */
    function past_orders_get()
    {   
        $input = $this->get();
        
        if(!isset($input['id']) || $input['id'] == '' ||
            !isset($input['user_type']) || $input['user_type'] == '' ||
            !isset($input['page']) || $input['page'] == '')     
        {   
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else
        {
            if($input['page'] <= 0){
                $input['page'] = 1;
            }
            if($input['user_type'] == 'user'){
                $settings = $this->Common_model->getSettings('user');
                $sql = "SELECT * from tbl_trip_details where (user_id = ".$input['id']." OR id IN (select order_id from tbl_splitfare where status = 'Accepted' AND receiver_id = ".$input['id'].")) AND status IN ('Completed', 'Cancelled') ORDER BY id DESC LIMIT ".(($input['page'] - 1) * $settings['per_page']).",".$settings['per_page'];

                $trip_data = $this->db->query($sql)->result_array();
            }
            else{
                $settings = $this->Common_model->getSettings('driver');
                $trip_data = $this->db->query("SELECT * from tbl_trip_details where driver_id = ".$input['id']." AND status IN ('Completed', 'Cancelled') ORDER BY id DESC LIMIT ".(($input['page'] - 1) * $settings['per_page']).",".$settings['per_page'])->result_array();
            }

            if(empty($trip_data)){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_past_trips_notfound')];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // OK (200)
            }
            else{
                $past_data = array();
                foreach ($trip_data as $key => $value) {
                    
                   
                    $value['user_data'] = $this->user_model->get_user($input['id']);
                    if($value['driver_id'] != '' && $value['driver_id'] != 0){
                        $value['driver_data'] = $this->driver_model->get_driver($value['driver_id']);
                    }
                    else{
                        $value['driver_data'] = (object)array(); 
                    }
                    
                    $past_data[] = $this->Common_model->getOrderDetails($value['id']);
                }
                
                $message = ['status' => TRUE,'message' => $this->lang->line('past_trip_found_success'),'data'=>$past_data];
                $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }
    }

    /*
     * Future Ride api
    */
    function future_orders_get()
    {   
        $input = $this->get();
        
        if(!isset($input['id']) || $input['id'] == '' ||
            !isset($input['user_type']) || $input['user_type'] == '' ||
            !isset($input['page']) || $input['page'] == '')     
        {   
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else
        {
            if($input['page'] <= 0){
                $input['page'] = 1;
            }
            if($input['user_type'] == 'user'){
                $settings = $this->Common_model->getSettings('user');
                $sql = "SELECT * from tbl_trip_details where (user_id = ".$input['id']." OR id IN (select order_id from tbl_splitfare where status = 'Accepted' AND receiver_id = ".$input['id'].")) AND ((status = 'Waiting' AND trip_type = 'later' AND tripdatetime > '".date('Y-m-d H:i:s')."') OR status IN ('Assigned', 'Arrived', 'Processing')) ORDER BY id DESC LIMIT ".(($input['page'] - 1) * $settings['per_page']).",".$settings['per_page'];
                
                $trip_data = $this->db->query($sql)->result_array();
            }
            else{
                $settings = $this->Common_model->getSettings('driver');
                $trip_data = $this->db->query("SELECT * from tbl_trip_details where driver_id = ".$input['id']." AND status IN ('Assigned', 'Arrived', 'Processing') ORDER BY id DESC LIMIT ".(($input['page'] - 1) * $settings['per_page']).",".$settings['per_page'])->result_array();
            }

            if(empty($trip_data)){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_future_trips_notfound')];
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // OK (200)
            }
            else{
                $past_data = array();
                foreach ($trip_data as $key => $value) {
                    
                   
                    $value['user_data'] = $this->user_model->get_user($input['id']);
                    if($value['driver_id'] != '' && $value['driver_id'] != 0){
                        $value['driver_data'] = $this->driver_model->get_driver($value['driver_id']);
                    }
                    else{
                        $value['driver_data'] = (object)array(); 
                    }
                    
                    $past_data[] = $value;
                }
                
                $message = ['status' => TRUE,'message' => $this->lang->line('future_trip_found_success'),'data'=>$past_data];
                $this->set_response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }
    }

    /*Cancel Ride*/
    function cancel_order_get()
    {
        $order_id = $this->get('order_id');
        $id = $this->get('id');
        $user_type = $this->get('user_type');
        $reason = "";
 
        if(empty($order_id) || empty($id) || empty($user_type)){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else{
            $order_data = $this->Common_model->getOrderDetails($order_id);
            $user_data = $this->Common_model->getUserDetails($order_data['user_id']);
            if($order_data['status'] == 'Completed'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_completed')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Cancelled'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_cancelled')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Processing'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_processing')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else{
                $result = $this->order_model->cancelOrder($order_id,$order_data['driver_id'],$user_type,$reason);
                if($user_type == 'user'){
                    if($order_data['driver_id'] != '0'){
                        $driver_data = $this->Common_model->getDriverDetails($order_data['driver_id']);

                        /*If ride is in assigned or arrived, then take the cancel charge*/
                        /*Get Settings*/
                        $settings = $this->Common_model->getSettings('user');

                        /*Give whole amount to owner with the tip*/
                        $tot_amount = $settings['ride_now_cancel_amount'];

                        $serviceFeeAmount = round((($tot_amount) * 0.2),2);

                        $sql_order = "update tbl_trip_details set payment_status = 1, tot_amount = ".$tot_amount." where id = ".$order_id;
                        $this->db->query($sql_order);
                      

                        //Android Push Message
                        $message = array("message"=>"Ride has been cancelled by passenger",'order_id'=>$order_id,"flag"=>"cancel_order",'trip_type'=>$order_data['trip_type']);

                            //IOS Push Message
                        $body = array();
                        $bodyI['aps'] = array('sound'=>'default','alert'=>"Ride has been cancelled by passenger",'order_id'=>$order_id,'tag'=>'cancel_order','trip_type'=>$order_data['trip_type']);

                        /*Android Push*/
                        if($driver_data['device_type']=='A' && $driver_data['device_id']!=''){
                            $registatoin_ids_D = $driver_data['device_id'];
                            $this->Common_model->send_gcm_notification_driver($registatoin_ids_D,$message);
                        }
                        /*IOS Push*/
                        if($driver_data['device_type']=='I' && $driver_data['device_id']!=''){
                            $this->Common_model->send_notification_ios_driver($bodyI,$driver_data['device_id']);
                        }
                    }   
                }
                else{
                    $user_data = $this->Common_model->getUserDetails($order_data['user_id']);

                    //Android Push Message
                    $message = array("message"=>"Ride has been cancelled by driver",'order_id'=>$order_id,"flag"=>"cancel_order",'trip_type'=>$order_data['trip_type']);

                        //IOS Push Message
                    $body = array();
                    $bodyI['aps'] = array('sound'=>'default','alert'=>"Ride has been cancelled by driver",'order_id'=>$order_id,'tag'=>'cancel_order','trip_type'=>$order_data['trip_type']);

                    /*Android Push*/
                    if($user_data['device_type']=='A' && $user_data['device_id']!=''){
                        $registatoin_ids_D = $user_data['device_id'];
                        $this->Common_model->send_gcm_notification_user($registatoin_ids_D,$message);
                    }
                    /*IOS Push*/
                    if($user_data['device_type']=='I' && $user_data['device_id']!=''){
                        $this->Common_model->send_notification_ios_user($bodyI,$user_data['device_id']);
                    }
                }
                $order_data = $this->Common_model->getOrderDetails($order_id);
                $message = ['status' => TRUE,'message' => 'Ride cancelled successfully','data'=>$order_data];
                $this->response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }
    }

    /*Cancel Ride*/
    function cancel_order_put()
    {
        $order_id = $this->put('order_id');
        $id = $this->put('id');
        $user_type = $this->put('user_type');
        $reason = $this->put('reason');
 
        if(empty($order_id) || empty($id) || empty($user_type)){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else{
            $order_data = $this->Common_model->getOrderDetails($order_id);
            $user_data = $this->Common_model->getUserDetails($order_data['user_id']);
            if($order_data['status'] == 'Completed'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_completed')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Cancelled'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_cancelled')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Processing'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_processing')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else{
                $result = $this->order_model->cancelOrder($order_id,$order_data['driver_id'],$user_type,$reason);
                if($user_type == 'user'){
                    if($order_data['driver_id'] != '0'){
                        $driver_data = $this->Common_model->getDriverDetails($order_data['driver_id']);

                        /*If ride is in assigned or arrived, then take the cancel charge*/
                        /*Get Settings*/
                        $settings = $this->Common_model->getSettings('user');

                        /*Give whole amount to owner with the tip*/
                        $tot_amount = $settings['ride_now_cancel_amount'];

                        $serviceFeeAmount = round((($tot_amount) * 0.2),2);

                        $sql_order = "update tbl_trip_details set payment_status = 1, tot_amount = ".$tot_amount." where id = ".$order_id;
                        $this->db->query($sql_order);
                      

                        //Android Push Message
                        $message = array("message"=>"Ride has been cancelled by passenger",'order_id'=>$order_id,"flag"=>"cancel_order",'trip_type'=>$order_data['trip_type']);

                            //IOS Push Message
                        $body = array();
                        $bodyI['aps'] = array('sound'=>'default','alert'=>"Ride has been cancelled by passenger",'order_id'=>$order_id,'tag'=>'cancel_order','trip_type'=>$order_data['trip_type']);

                        /*Android Push*/
                        if($driver_data['device_type']=='A' && $driver_data['device_id']!=''){
                            $registatoin_ids_D = $driver_data['device_id'];
                            $this->Common_model->send_gcm_notification_driver($registatoin_ids_D,$message);
                        }
                        /*IOS Push*/
                        if($driver_data['device_type']=='I' && $driver_data['device_id']!=''){
                            $this->Common_model->send_notification_ios_driver($bodyI,$driver_data['device_id']);
                        }
                    }   
                }
                else{
                    $user_data = $this->Common_model->getUserDetails($order_data['user_id']);

                    //Android Push Message
                    $message = array("message"=>"Ride has been cancelled by driver",'order_id'=>$order_id,"flag"=>"cancel_order",'trip_type'=>$order_data['trip_type']);

                        //IOS Push Message
                    $body = array();
                    $bodyI['aps'] = array('sound'=>'default','alert'=>"Ride has been cancelled by driver",'order_id'=>$order_id,'tag'=>'cancel_order','trip_type'=>$order_data['trip_type']);

                    /*Android Push*/
                    if($user_data['device_type']=='A' && $user_data['device_id']!=''){
                        $registatoin_ids_D = $user_data['device_id'];
                        $this->Common_model->send_gcm_notification_user($registatoin_ids_D,$message);
                    }
                    /*IOS Push*/
                    if($user_data['device_type']=='I' && $user_data['device_id']!=''){
                        $this->Common_model->send_notification_ios_user($bodyI,$user_data['device_id']);
                    }
                }
                $order_data = $this->Common_model->getOrderDetails($order_id);
                $message = ['status' => TRUE,'message' => 'Ride cancelled successfully','data'=>$order_data];
                $this->response($message, REST_Controller::HTTP_OK); // OK (200)
            }
        }
    }

    /*Cancel Ride*/
    function user_payment_get()
    {
        $order_id = $this->get('order_id');
        $user_id = $this->get('id');
        $driver_tip = $this->get('driver_tip');
        $payment_type = $this->get('payment_type');
        if($driver_tip == ''){
            $driver_tip = 0;
        }
        if(empty($order_id) || empty($user_id)|| empty($payment_type)){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else{
            $order_data = $this->Common_model->getOrderDetails($order_id);
            $user_data = $this->Common_model->getUserDetails($user_id);

            if($order_data['user_id'] != $user_id){
                            
                $sql_order = "select * from tbl_splitfare where payment_status = 1 AND order_id = ".$order_id." AND receiver_id = ".$user_id;
                
                if($this->db->query($sql_order)->num_rows() > 0){
                    $message = ['status' => TRUE,'message' => 'Payment is already received'];
                    $this->response($message, REST_Controller::HTTP_OK); // OK (200)
                }

            }
            else{

                $sql_order = "select * from tbl_trip_details where payment_status = 1 AND id = ".$order_id." AND user_id = ".$user_id;

                if($this->db->query($sql_order)->num_rows() > 0){
                    $message = ['status' => TRUE,'message' => 'Payment is already received'];
                    $this->response($message, REST_Controller::HTTP_OK); // OK (200)
                }
            }

            
            if($order_data['status'] == 'Cancelled'){
                $message = ['status' => FALSE,'message' => $this->lang->line('text_trip_already_cancelled')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
            else if($order_data['status'] == 'Completed'){
                $driver_data = $this->Common_model->getDriverDetails($order_data['driver_id']);

                /*Get Settings*/
                $settings = $this->Common_model->getSettings('user');

                /*Give whole amount to owner with the tip*/
                $tot_amount = $driver_tip + $order_data['split_amount'];

                $serviceFeeAmount = round((($order_data['split_amount']) * ($settings['owner_percent']/100)),2);

                /*$driver_amount = $order_data['tot_amount'] - $merchant_amount;*/


                /*Check if payment is done by normal user or split user*/
                if($user_id == $order_data['user_id']){
                    
                    $sql_order = "update tbl_trip_details set payment_status = 1,payment_type = '".$payment_type."',total_driver_tip = total_driver_tip + ".$driver_tip." ,driver_tip = ".$driver_tip.", tot_amount = ".$order_data['split_amount']." where id = ".$order_id;
                    $this->db->query($sql_order);
                }
                else{
                    $sql_order = "update tbl_splitfare set payment_status = 1, driver_tip = ".$driver_tip." where order_id = ".$order_id." AND receiver_id = ".$user_id;
                    
                    $this->db->query($sql_order);


                    $sql_order = "update tbl_trip_details set total_driver_tip = total_driver_tip + ".$driver_tip." where id = ".$order_id;
                    $this->db->query($sql_order);
                }

            
                $order_data = $this->Common_model->getOrderDetails($order_id);
                $message = ['status' => TRUE,'message' => $this->lang->line('text_payment_success'),'data'=>$order_data];
                $this->response($message, REST_Controller::HTTP_OK); // OK (200)
            }
            else{
                $message = ['status' => FALSE,'message' => $this->lang->line('text_order_not_completed')];
                $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
            }
        }
    }

    function get_rating_list_get()
    {
        $star_data = $this->db->get_where('tbl_star',array('is_active'=>'1'))->result_array();
        foreach ($star_data as $key => $value) {
            $star_data[$key]['reasons'] = $this->db->get_where('tbl_star_data',array('star_id'=>$value['id'],'is_active'=>"1"))->result_array();
        }
        $message = ['status' => TRUE,'message' => "Success",'data'=>$star_data];
        $this->response($message, REST_Controller::HTTP_OK);
    }

    function rate_review_put()
    {
        $input = $this->put();
        if($input['id'] == '' || $input['user_type'] == '' || $input['rate'] == ''|| $input['rate_type'] == ''){
            $message = ['status' => FALSE,'message' => $this->lang->line('text_rest_invalidparam')];
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400)
        }
        else{
            $comment = '';
            if($input['comment'] != '') $comment = $input['comment'];

            $row = '';
            if($input['user_type'] == 'driver'){
                $sql = "insert into tbl_rate_review(user_id,user_type, rate,comment, rate_type) values(".$input['id'].",'driver',".CEIL($input['rate']).",'".$comment."','".$input['rate_type']."')";
                $this->db->query($sql);

                $sql = "select CEIL(AVG(rate)) AS rate from tbl_rate_review r where user_type = 'driver' and user_id = ".$input['id'];
                $row = $this->db->query($sql)->row_array();

                $sql = "update tbl_drivers set rate = ".$row['rate']." where id = ".$input['id'];
                $this->db->query($sql); 
            }
            else{
                $sql = "insert into tbl_rate_review(user_id,user_type, rate,comment, rate_type) values(".$input['id'].",'user',".CEIL($input['rate']).",'".$comment."','".$input['rate_type']."')";
                $this->db->query($sql);

                $sql = "select CEIL(AVG(rate)) AS rate from tbl_rate_review r where user_type = 'user' and user_id = ".$input['id'];
                $row = $this->db->query($sql)->row_array();

                $sql = "update tbl_users set rate = ".$row['rate']." where id = ".$input['id'];
                $this->db->query($sql); 
            }   
            $message = ['status' => TRUE,'message' => $this->lang->line('text_trip_rate_review')];
            $this->response($message, REST_Controller::HTTP_OK); // BAD_REQUEST (400)
        }   
    }
}