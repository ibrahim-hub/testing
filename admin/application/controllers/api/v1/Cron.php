<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class Cron extends CI_Controller {

	public function __construct()
    {   
    	
        // Construct the parent class
        parent::__construct();
        
        $this->load->model('api/user_model');
        $this->load->model('api/driver_model');
        $this->load->model('api/order_model');
    }

    public function index()
    {

    }

    /*
     * Place Ride later order push to temp assigned driver (cron call every 5 minutes)
    */
	function later_order()
	{
		
		//Get all ride later list before 1 hours
		$laterrides = $this->order_model->get_later_trip_list();
		$msg = '';
		if(count($laterrides) > 0)
		{
			foreach ($laterrides as $ridekey => $ridevalue) 
			{
				$push_message = "You have new ride request";
		        /*Android Push Message*/
		        $message = array("message"=>$push_message,"order_id"=>$ridevalue['id'],"user_id"=>$ridevalue['user_id'],"trip_type"=>$ridevalue['trip_type'],"flag"=>"find_drivers");
		        /*IOS Push Message*/
		        $body = array();
		        $bodyI['aps'] = array('sound'=>'default','alert'=>$push_message,"order_id"=>$ridevalue['id'],"user_id"=>$ridevalue['user_id'],"trip_type"=>$ridevalue['trip_type'],'tag'=>'find_drivers');

				//If assigned driver not free then find other driver
				$driver_data = $this->driver_model->find_driver_later($ridevalue);

				if($driver_data){

					/*Update the Trip Status*/
					$data = array('driver_id'=>$driver_data['id'],'request_time'=>date('Y-m-d H:i:s'));
					$condition = array('id'=>$ridevalue['id']);
					$this->db->update('tbl_trip_details',$data,$condition);

					/*Make Driver Not Free*/
					$this->db->update('tbl_drivers',array('is_free'=>'0'),array('id'=>$driver_data['id']));

					/*Android Push*/
			        if($driver_data['device_type']=='A' && $driver_data['device_id']!=''){
			            $this->Common_model->send_gcm_notification_driver($driver_data['device_id'],$message);
			        }
			        /*IOS Push*/
			        if($driver_data['device_type']=='I' && $driver_data['device_id']!=''){
			            $this->Common_model->send_notification_ios_driver($bodyI,$driver_data['device_id']);
			        }
					$msg .= '<br>Other driver send push message for Order No::'.$ridevalue['id']." to Driver No:: ".$driver_data['id'];
				} 
			}
			if($msg == ''){
				$msg .= '<br>Order found but no driver found';
			}
		}
		else
		{
			$msg = "Order not found";
		}
		/*$admindata = $this->getAdmin();*/
	        $this->email->from('admin@hyperlinkinfosystem.in', 'SAIK Taxi');
	        $this->email->to('parth@hyperlinkinfosystem.com');
	 
	        $this->email->subject('SAIK Taxi Cron');     
	            
	        // From
	        $from = "admin@hyperlinkinfosystem.in";

	        //$messag  = "Hello ".$first_name.", <br> <br>";
	        $message  = $msg;

	        // To send HTML mail, the Content-type header must be set

	        $this->email->set_header('MIME-Version', '1.0');
	        $this->email->set_header('Content-type', 'text/html; charset=UTF-8');
	        $this->email->set_header('Content-type', 'text/html; charset=iso-8859-1');
	        $this->email->set_header('From', $from);
	        $this->email->set_header('X-Priority', '3');

	        $this->email->message($message);
	        //$this->email->send();
	}

	/*
     * Place Ride later order push to temp assigned driver (cron call every 5 minutes)
    */
	function cron_payment()
	{
		//Make Normal Payment
		$payment_list = $this->order_model->get_payment_list();

		if(count($payment_list) > 0)
		{
			foreach ($payment_list as $ridekey => $ridevalue) 
			{
				$user_data = $this->Common_model->getUserDetails($ridevalue['user_id']);
				$driver_data = $this->Common_model->getDriverDetails($ridevalue['driver_id']);
				/*Get Settings*/
                $settings = $this->Common_model->getSettings('user');

                /*Give whole amount to owner with the tip*/
                $tot_amount = $ridevalue['split_amount'];

				/*Update the Trip Status*/
				$data = array('payment_status'=>'1','payment_type'=>'Cash');
				$condition = array('id'=>$ridevalue['id']);
				$this->db->update('tbl_trip_details',$data,$condition);
				echo 'Order found';
			}
		}
		else
		{
			echo "Order not found";
		}

		echo "<br>";

		//Make Split Payment
		$payment_list = $this->order_model->get_split_payment_list();
		/*echo "<pre>";
        print_r($payment_list);
        die;*/
		if(count($payment_list) > 0)
		{
			foreach ($payment_list as $ridekey => $ridevalue) 
			{
				$user_data = $this->Common_model->getUserDetails($ridevalue['receiver_id']);
				$driver_data = $this->Common_model->getDriverDetails($ridevalue['driver_id']);
				/*Get Settings*/
                $settings = $this->Common_model->getSettings('user');

                /*Give whole amount to owner with the tip*/
                $tot_amount = $ridevalue['split_amount'];

				/*Update the Trip Status*/
				$data = array('payment_status'=>'1','payment_type'=>'Card');
				$condition = array('id'=>$ridevalue['split_id']);
				$this->db->update('tbl_splitfare',$data,$condition);
				echo 'Split Order found';
			}
		}
		else
		{
			echo "Split Order not found";
		}
	}
	
	function cron_transfer_request()
	{
		//Get all ride later list before 1 hours
		$current_time = strtotime(date('Y-m-d H:i:s'));
		$sql = "select * from tbl_trip_details where driver_id <> 0 AND status = 'Waiting' AND (".$current_time." - UNIX_TIMESTAMP(request_time)) > 600"; 

		$laterrides = $this->db->query($sql)->result_array();
		$msg = '';
		if(count($laterrides) > 0)
		{
			foreach ($laterrides as $ridekey => $ridevalue) 
			{
				/*Make Driver Not Free*/
				$this->db->update('tbl_drivers',array('is_free'=>'1','is_service'=>'0'),array('id'=>$ridevalue['driver_id']));


				$push_message = "You have new ride request";
		        /*Android Push Message*/
		        $message = array("message"=>$push_message,"order_id"=>$ridevalue['id'],"user_id"=>$ridevalue['user_id'],"trip_type"=>$ridevalue['trip_type'],"flag"=>"find_drivers");
		        /*IOS Push Message*/
		        $body = array();
		        $bodyI['aps'] = array('sound'=>'default','alert'=>$push_message,"order_id"=>$ridevalue['id'],"user_id"=>$ridevalue['user_id'],"trip_type"=>$ridevalue['trip_type'],'tag'=>'find_drivers');

				//If assigned driver not free then find other driver
				$driver_data = $this->driver_model->find_driver_later($ridevalue);

				if($driver_data){

					/*Update the Trip Status*/
					$data = array('driver_id'=>$driver_data['id'],'request_time'=>date('Y-m-d H:i:s'));
					$condition = array('id'=>$ridevalue['id']);
					$this->db->update('tbl_trip_details',$data,$condition);

					/*Make Driver Not Free*/
					$this->db->update('tbl_drivers',array('is_free'=>'0'),array('id'=>$driver_data['id']));

					/*Android Push*/
			        if($driver_data['device_type']=='A' && $driver_data['device_id']!=''){
			            $this->Common_model->send_gcm_notification_driver($driver_data['device_id'],$message);
			        }
			        /*IOS Push*/
			        if($driver_data['device_type']=='I' && $driver_data['device_id']!=''){
			            $this->Common_model->send_notification_ios_driver($bodyI,$driver_data['device_id']);
			        }
					$msg .= '<br>Other driver send push message for Order No::'.$ridevalue['id']." to Driver No:: ".$driver_data['id'];
				} 
				else{
					/*Update the Trip Status*/
					$data = array('driver_id'=>'0');
					$condition = array('id'=>$ridevalue['id']);
					$this->db->update('tbl_trip_details',$data,$condition);
					 /*Android Push Message*/
	                $user_data = $this->Common_model->getUserdetails($ridevalue['user_id']);
	                $message = array("message"=>"Your ride request is accepted. Admin will contact you soon","order_id"=>$ridevalue['id'], "user_id"=>$ridevalue['user_id'],"trip_type"=>$ridevalue['trip_type'],"flag"=>"find_drivers");

	                /*IOS Push Message*/
	                $body = array();
	                $bodyI['aps'] = array('sound'=>'default','alert'=>"Your ride request is accepted. Admin will contact you soon","order_id"=>$ridevalue['id'], "user_id"=> $ridevalue['user_id'],"trip_type"=>$ridevalue['trip_type'],'tag'=>'find_drivers');
	                /*Android Push*/
	                if($user_data['device_type']=='A' && $user_data['device_id']!=''){
	                    //$this->Common_model->send_gcm_notification_user($user_data['device_id'],$message);
	                }
	                /*IOS Push*/
	                if($user_data['device_type']=='I' && $user_data['device_id']!=''){
	                    //$this->Common_model->send_notification_ios_user($bodyI,$user_data['device_id']);
	                }  
				}
			}
			if($msg == ''){
				$msg .= '<br>Order found but no driver found';
			}
		}
		else
		{
			$msg = "Order not found";
		}
		echo $msg;
		/*$admindata = $this->getAdmin();*/
        $this->email->from('admin@hyperlinkinfosystem.in', 'SAIK Taxi');
        $this->email->to('parth@hyperlinkinfosystem.com');
 
        $this->email->subject('SAIK Taxi Request Transfer Cron');     
            
        // From
        $from = "admin@hyperlinkinfosystem.in";

        //$messag  = "Hello ".$first_name.", <br> <br>";
        $message  = $msg;

        // To send HTML mail, the Content-type header must be set

        $this->email->set_header('MIME-Version', '1.0');
        $this->email->set_header('Content-type', 'text/html; charset=UTF-8');
        $this->email->set_header('Content-type', 'text/html; charset=iso-8859-1');
        $this->email->set_header('From', $from);
        $this->email->set_header('X-Priority', '3');

        $this->email->message($message);
        //$this->email->send();
	}
}
?>