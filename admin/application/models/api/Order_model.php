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
class Order_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }

    /*Place Order*/
    function placeOrder($params)
    {
        $this->db->insert('tbl_trip_details', $params);
        $id = $this->db->insert_id();
        /*Generate Order ID
            Ride Now:: CRN
            Ride Later:: CRL
        */
        /*$order_id = '';
        if($params['trip_type'] == 'now'){
            $order_id = 'CRN'.date('Ymd').$id;
        }
        else{
            $order_id = 'CRL'.date('Ymd').$id;
        }
        $this->db->update('tbl_trip_details',array('order_id'=>$order_id), array('id'=>$id));*/

        

        return $id;
    }

    function check_user_availability($user_id)
    {
        $sql = "SELECT * from tbl_trip_details where user_id = ".$user_id." AND status IN ('Processing','Arrived')";

        return $ct = $this->db->query($sql)->num_rows();

    }

    function addSplitUser($id, $params)
    {
        
        $this->db->insert('tbl_splitfare', $split_params);
    }

    /*Update Order*/
    function updateOrder($order_id, $params)
    {
        return $this->db->update('tbl_trip_details',$params, array('order_id'=>$order_id));
    }

    function findDriver($params)
    {

        $sql = "SELECT *, 6371 * 2 * ASIN(SQRT( POWER(SIN((".$params['pickup_latitude']." - latitude) * pi()/180 / 2), 2) + COS(".$params['pickup_latitude']." * pi()/180) * COS(latitude * pi()/180) *POWER(SIN((".$params['pickup_longitude']." - longitude) * pi()/180 / 2), 2) )) as distance FROM tbl_driver WHERE car_type_id=".$params['car_type_id']." AND is_free=1 AND is_login=1 AND is_active=1 HAVING distance >= (".$params['distance']."-3) AND distance <= ".$params['distance']." ORDER by distance";

        return $this->db->query($sql)->result_array();
    }

    function acceptOrder($order_id, $driver_id)
    {
        $temp = array(
            'is_free' => '0'
        );
        $this->db->update('tbl_drivers', $temp, array('id'=>$driver_id));
        
        $data = array(
            'status' => 'Assigned',
            'driver_id' => $driver_id
        );
        return $this->db->update('tbl_trip_details', $data, array('id'=>$order_id));
    }

    function arrived($order_id)
    {
     
        
        $data = array(
            'status' => 'Arrived'
        );
        return $this->db->update('tbl_trip_details', $data, array('id'=>$order_id));
    }

    function pickup($order_id)
    {
    
        
        $data = array(
            'status' => 'Processing',
            'start_datetime' => date('Y-m-d H:i')
        );
        return $this->db->update('tbl_trip_details', $data, array('id'=>$order_id));
    }

    function dropoff($order_id, $data,$driver_id)
    {
        $temp = array(
            'is_free' => '1'
        );
        $this->db->update('tbl_drivers', $temp, array('id'=>$driver_id));
        
        return $this->db->update('tbl_trip_details', $data, array('id'=>$order_id));
    }

    function cancelOrder($order_id, $driver_id, $cancelled_by,$reason)
    {
        $data = array(
            'status' => 'Cancelled',
            'cancelled_by' => $cancelled_by,
            'reason' => $reason,
        );

        $temp = array(
            'is_free' => '1'
        );
        $this->db->update('tbl_drivers', $temp, array('id'=>$driver_id));
        
        return $this->db->update('tbl_trip_details', $data, array('id'=>$order_id));
    }

    /*
     * get all later orders list
     */
    function get_later_trip_list()
    {
        $current_date_minushours = date('Y-m-d H:i:s', strtotime('+45 minutes'));
        $this->db->select('*');
        $this->db->from('tbl_trip_details');
        $this->db->where('trip_type', 'later');
        $this->db->where('status', 'Waiting');
        $this->db->where("tripdatetime <= '".$current_date_minushours."' AND tripdatetime >= '".date('Y-m-d H:i:s')."'");
        $this->db->order_by("id", "desc");
       /* echo "<pre>";
        print_r($this->db); die;*/
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {   
            return array();
        }
    }

    /*
     * get all pending payment orders list
     */
    function get_payment_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_trip_details');
        $this->db->where('payment_status', '0');
        $this->db->where('status', 'Completed');
        $this->db->order_by("id", "desc");
     
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {   
            return array();
        }
    }


    /*
     * get all split pending payment orders list
     */
    function get_split_payment_list()
    {
        $this->db->select('t.*, s.id as split_id, s.receiver_id,s.status as request_status, s.amount as payment_amount');
        $this->db->from('tbl_trip_details t');
        $this->db->join('tbl_splitfare s', 's.order_id = t.id');
        $this->db->where('s.payment_status', '0');
        $this->db->where('t.status', 'Completed');
        $this->db->where('s.status', 'Accepted');
        
        $this->db->order_by("s.id", "desc");
        
        
        $query = $this->db->get();
        
        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {   
            return array();
        }
    }

    

}
