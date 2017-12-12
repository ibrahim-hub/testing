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
class Driver_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }

    /*
     * function to update tbl_driver_token
     */
    function update_driver_token($driver_id,$params)
    {

        $this->db->where('id',$driver_id);
        $this->db->update('tbl_drivers',$params);
    }

    function get_phone($params)
    {
        return $this->db->get_where('tbl_drivers',$params)->row_array();
    }

    /*
     * get a tbl_driver by id
     */
    function get_driver($id)
    {
        $data = $this->db->get_where('tbl_drivers',array('id'=>$id))->row_array();
        if(!empty($data)){
            $profile = $data['profile_image'];
           
            $data['profile_image'] = base_url().'assets/uploads/profile_image/'.$profile;
            $data['profile_image_thumb'] = base_url().'assets/uploads/profile_image/'.'thumb/'.$profile;
        }
        
        return $data;
    }

    function get_driver_documents($id)
    {
        $data = $this->db->get_where('tbl_documents',array('driver_id'=>$id))->row_array();
        if(!empty($data)){
            $registration_image = $data['registration_image'];
            if($registration_image != ''){
                $data['registration_image'] = base_url().'assets/uploads/registration_image/'.$registration_image;
                $data['registration_image_thumb'] = base_url().'assets/uploads/registration_image/'.'thumb/'.$registration_image;
            }
            else{
                $data['registration_image'] = '';
                $data['registration_image_thumb'] = '';
            }

            $vehicle_front_image = $data['vehicle_front_image'];
            if($vehicle_front_image != ''){
                $data['vehicle_front_image'] = base_url().'assets/uploads/vehicle_front_image/'.$vehicle_front_image;
                $data['vehicle_front_image_thumb'] = base_url().'assets/uploads/vehicle_front_image/'.'thumb/'.$vehicle_front_image;
            }
            else{
                $data['vehicle_front_image'] = '';
                $data['vehicle_front_image_thumb'] = '';
            }

            $vehicle_back_image = $data['vehicle_back_image'];
            if($vehicle_back_image != ''){
                $data['vehicle_back_image'] = base_url().'assets/uploads/vehicle_back_image/'.$vehicle_back_image;
                $data['vehicle_back_image_thumb'] = base_url().'assets/uploads/vehicle_back_image/'.'thumb/'.$vehicle_back_image;
            }
            else{
                $data['vehicle_back_image'] = '';
                $data['vehicle_back_image_thumb'] = '';
            }

            $licence_image = $data['licence_image'];
            if($licence_image != ''){
                $data['licence_image'] = base_url().'assets/uploads/licence_image/'.$licence_image;
                $data['licence_image_thumb'] = base_url().'assets/uploads/licence_image/'.'thumb/'.$licence_image;
            }
            else{
                $data['licence_image'] = '';
                $data['licence_image_thumb'] = '';
            }

            $driver_id_image = $data['driver_id_image'];
            if($driver_id_image != ''){
                $data['driver_id_image'] = base_url().'assets/uploads/driver_id_image/'.$driver_id_image;
                $data['driver_id_image_thumb'] = base_url().'assets/uploads/driver_id_image/'.'thumb/'.$driver_id_image;
            }
            else{
                $data['driver_id_image'] = '';
                $data['driver_id_image_thumb'] = '';
            }

            $owner_id_image = $data['owner_id_image'];
            if($owner_id_image != ''){
                $data['owner_id_image'] = base_url().'assets/uploads/owner_id_image/'.$owner_id_image;
                $data['owner_id_image_thumb'] = base_url().'assets/uploads/owner_id_image/'.'thumb/'.$owner_id_image;
            }
            else{
                $data['owner_id_image'] = '';
                $data['owner_id_image_thumb'] = '';
            }
        }
        
        return $data;
    }

    /*Custom Method*/
    /*
     * get fb_id user details
     */
    function get_fb_driver($fb_id)
    {
        return $this->db->get_where('tbl_drivers',array('fb_id'=>$fb_id))->row_array();
    }

    /*
     * Check old password
     */
    function check_old_password($id,$password)
    {
        return $this->db->get_where('tbl_drivers',array('id'=>$id,'password'=>md5($password)))->row_array();
    }

    /*
     * Change Password
     */
    function change_password($id,$password)
    {
        return $this->db->update('tbl_drivers',array('password'=>md5($password)),array('id'=>$id));
    }
    
    /*
     * get all tbl_driver
     */
    function get_all_drivers($offset)
    {
        $sql = "select * from tbl_drivers LIMIT ".$offset.", ".$this->per_page."";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /*
     * function to add new tbl_driver
     */
    function add_driver($params)
    {
        $this->db->insert('tbl_drivers',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update tbl_driver
     */
    function update_driver($id,$params)
    {   
        $this->db->where('id',$id);
        $this->db->update('tbl_drivers',$params);
    }

    /*
     * function to free tbl_driver
     */
    function free_driver($id)
    {   
        $this->db->where('id',$id);
        $this->db->update('tbl_drivers',array('is_free'=>'1'));
    }
    
    /*
     * function to delete tbl_driver
     */
    function delete_driver($id)
    {
        $this->db->delete('tbl_drivers',array('id'=>$id));
    }

    /*Custom Method*/
    
    /*
     * check email and password
     */
    function checkDriverlogin($email, $password)
    {   
        return $this->db->get_where('tbl_drivers',array('email'=>$email,'password'=>md5($password)))->row_array();
    }

    /*
     * check phone and password
     */
    function checkDriverphonelogin($phone, $password)
    {   
        return $this->db->get_where('tbl_drivers',array('phone'=>$phone,'password'=>md5($password)))->row_array();
    }

    /*
     * check email exists or not
     */
    function checkEmailexists($email)
    {   
        return $this->db->get_where('tbl_drivers',array('email'=>$email))->row_array();
    }

    /*
     * Find driver //free, login, active
     */
    function find_driver_later($order_data)
    {
        $user_data = $this->Common_model->getUserDetails($order_data['user_id']);
        
        $settings = $this->Common_model->getSettings('driver');;
        // Find driver in the pickup area
        $pickup_latitude = $order_data['pickup_latitude'];
        $pickup_longitude = $order_data['pickup_longitude'];
        $car_type = $order_data['car_type'];

        $sql = "SELECT *, 6371 * 2 * ASIN(SQRT( POWER(SIN(($pickup_latitude - latitude) * pi()/180 / 2), 2) + COS($pickup_latitude * pi()/180) * COS(latitude * pi()/180) *POWER(SIN(($pickup_longitude - longitude) * pi()/180 / 2), 2) )) as distance FROM tbl_drivers WHERE is_free=1 AND is_login=1 AND is_active=1 AND is_service = 1 AND is_verified = 1 AND otp_verified = 1  AND car_type=".$car_type." HAVING  distance <= ".$settings['base_distance']." ORDER by distance";
                    

        $query = $this->db->query($sql);
      
        if($query->num_rows() > 0)
        {
            $driver_data = $query->result_array();
            foreach ($driver_data as $value) {
                /*Fetch driver data that is in table temp_order*/
                $query = "select * from tbl_temp_order where driver_id = ".$value['id']. " AND order_id = ".$order_data['id'];
                $temp_data = $this->db->query($query)->num_rows();

                if($temp_data == 0){
                    return $value;
                }
            }
            return false;
        }
        else
            return false;
    }

    /*
     * check driver coupon card was exists or valid or not
     */
    function checkDrivercouponcard($coupon_card)
    {   
        return $this->db->get_where('tbl_driver_couponcard',array('coupon_card'=>$coupon_card))->row_array();
    }
}
