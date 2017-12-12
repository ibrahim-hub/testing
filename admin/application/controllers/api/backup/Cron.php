<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This is an REST API
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Jilesh
 * @license         Hyperlinkinfosystem
 * @link            http://localhost/projects/trek/api/cron
 */

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
	function laterorder()
	{
		//Get all ride later list before 1 hours
		$laterrides = $this->order_model->get_later_trip_list();

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
					$data = array('driver_id'=>$driver_data['id']);
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
				}
		        
				echo '<br>Other driver send push message for Order No::'.$ridevalue['id']." to Driver No:: ".$driver_data['id'];
			}
		}
		else
		{
			echo "Order not found";
		}
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

                $owner_percent = ($ridevalue['split_amount'] - $ridevalue['split_base_fare']);

                $serviceFeeAmount = round((($owner_percent) * 0.2),2) + $ridevalue['split_base_fare'];

                /*$driver_amount = $ridevalue['tot_amount'] - $merchant_amount;*/

                /*Give tip to driver*/
                $result = Braintree_Transaction::sale([
                    'amount' => $tot_amount,
                    'orderId' => $ridevalue['id'],
                    'customerId' => $user_data['braintree_id'],
                    'serviceFeeAmount' => $serviceFeeAmount,
                    'merchantAccountId' => $driver_data['merchant_id'],
                    'paymentMethodToken' => $user_data['braintree_token'],
                    'options' => [
                        'submitForSettlement' => true
                        ]
                    ]);


                if($result->success)
                {
                    $transaction_id = $result->transaction->id;

					$push_message = "Payment has been received successfully for Trip Id #".$ridevalue['id'];
			        /*Android Push Message*/
			        $message = array("message"=>$push_message,"order_id"=>$ridevalue['id'],"user_id"=>$ridevalue['user_id'],"trip_type"=>$ridevalue['trip_type'],"flag"=>"user_payment");
			        /*IOS Push Message*/
			        $body = array();
			        $bodyI['aps'] = array('sound'=>'default','alert'=>$push_message,"order_id"=>$ridevalue['id'],"user_id"=>$ridevalue['user_id'],"trip_type"=>$ridevalue['trip_type'],'tag'=>'user_payment');

					/*Android Push*/
			        if($driver_data['device_type']=='A' && $driver_data['device_id']!=''){
			            $this->Common_model->send_gcm_notification_driver($driver_data['device_id'],$message);
			        }
			        /*IOS Push*/
			        if($driver_data['device_type']=='I' && $driver_data['device_id']!=''){
			            $this->Common_model->send_notification_ios_driver($bodyI,$driver_data['device_id']);
			        }

					/*Update the Trip Status*/
					$data = array('payment_status'=>'1','transaction_id'=>$transaction_id,'payment_type'=>'Card');
					$condition = array('id'=>$ridevalue['id']);
					$this->db->update('tbl_trip_details',$data,$condition);
				}
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

                $owner_percent = ($ridevalue['split_amount'] - $ridevalue['split_base_fare']);

                $serviceFeeAmount = round((($owner_percent) * 0.2),2) + $ridevalue['split_base_fare'];

                /*$driver_amount = $ridevalue['tot_amount'] - $merchant_amount;*/

                /*Give tip to driver*/
                $result = Braintree_Transaction::sale([
                    'amount' => $tot_amount,
                    'orderId' => $ridevalue['id'],
                    'customerId' => $user_data['braintree_id'],
                    'serviceFeeAmount' => $serviceFeeAmount,
                    'merchantAccountId' => $driver_data['merchant_id'],
                    'paymentMethodToken' => $user_data['braintree_token'],
                    'options' => [
                        'submitForSettlement' => true
                        ]
                    ]);


                if($result->success)
                {
                    $transaction_id = $result->transaction->id;

					$push_message = "Payment has been received successfully for Trip Id #".$ridevalue['id'];
			        /*Android Push Message*/
			        $message = array("message"=>$push_message,"order_id"=>$ridevalue['id'],"user_id"=>$ridevalue['receiver_id'],"trip_type"=>$ridevalue['trip_type'],"flag"=>"user_payment");
			        /*IOS Push Message*/
			        $body = array();
			        $bodyI['aps'] = array('sound'=>'default','alert'=>$push_message,"order_id"=>$ridevalue['id'],"user_id"=>$ridevalue['receiver_id'],"trip_type"=>$ridevalue['trip_type'],'tag'=>'user_payment');

					/*Android Push*/
			        if($driver_data['device_type']=='A' && $driver_data['device_id']!=''){
			            $this->Common_model->send_gcm_notification_driver($driver_data['device_id'],$message);
			        }
			        /*IOS Push*/
			        if($driver_data['device_type']=='I' && $driver_data['device_id']!=''){
			            $this->Common_model->send_notification_ios_driver($bodyI,$driver_data['device_id']);
			        }

					/*Update the Trip Status*/
					$data = array('payment_status'=>'1','transaction_id'=>$transaction_id,'payment_type'=>'Card');
					$condition = array('id'=>$ridevalue['split_id']);
					$this->db->update('tbl_splitfare',$data,$condition);
					echo 'Split Order found';
				}
				else{
					echo 'Split Order transaction failed';
				}
				
			}
		}
		else
		{
			echo "Split Order not found";
		}
	}

}
?>