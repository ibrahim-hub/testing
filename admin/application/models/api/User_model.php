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
class User_model extends CI_Model
{
    
    function __construct()
    {
        parent::__construct();
    }

    /*
     * function to get phone number
     */
    function get_phone($params)
    {
        return $this->db->get_where('tbl_users',$params)->row_array();
    }

    /*
     * function to get phone number
     */
    function check_user_phone($params)
    {
        return $this->db->get_where('tbl_users',$params)->num_rows();
    }
    
    /*
     * function to add new user token
     */
    function add_usertoken($params)
    {
        $this->db->insert('tbl_users_token',$params);
        return $this->db->insert_id();
    }

    /*
     * function to update tbl_user_token
     */
    function update_user_token($user_id,$params)
    {

        $this->db->where('id',$user_id);
        $this->db->update('tbl_users',$params);
    }

    /*
     * get a tbl_user by id
     */
    function get_user($id)
    {
        $data = $this->db->get_where('tbl_users',array('id'=>$id))->row_array();
        if(!empty($data)){
            $profile = $data['profile_image'];
            $data['profile_image'] = base_url().'assets/uploads/user/'.$profile;
            $data['profile_image_thumb'] = base_url().'assets/uploads/user/'.'thumb/'.$profile;
        }
        return $data;
    }

    /*
     * Check old password
     */
    function check_old_password($id,$password)
    {
        return $this->db->get_where('tbl_users',array('id'=>$id,'password'=>md5($password)))->row_array();
    }

    /*
     * Change Password
     */
    function change_password($id,$password)
    {
        return $this->db->update('tbl_users',array('password'=>md5($password)),array('id'=>$id));
    }
    
    /*
     * get all tbl_users
     */
    function get_all_users($offset)
    {
        $sql = "select * from tbl_users LIMIT ".$offset.", ".$this->per_page."";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /*
     * function to add new tbl_user
     */
    function add_user($params)
    {
        $this->db->insert('tbl_users',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update tbl_user
     */
    function update_user($id,$params)
    {   
        $this->db->where('id',$id);
        $this->db->update('tbl_users',$params);
    }
    
    /*
     * function to delete tbl_user
     */
    function delete_user($id)
    {
        $this->db->delete('tbl_users',array('id'=>$id));
    }

    /*Custom Method*/
    /*
     * get fb_id user details
     */
    function get_fb_user($fb_id)
    {
        return $this->db->get_where('tbl_users',array('fb_id'=>$fb_id))->row_array();
    }


    /*
     * check email and password
     */
    function checkUserlogin($email, $password)
    {   
        return $this->db->get_where('tbl_users',array('email'=>$email,'password'=>md5($password)))->row_array();
    }

    /*
     * check phone and password
     */
    function checkUserphonelogin($phone, $password)
    {   
        return $this->db->get_where('tbl_users',array('phone'=>$phone,'password'=>md5($password)))->row_array();
    }
	
    /*
     * check email exists or not
     */
    function checkEmailexists($email)
    {   
        return $this->db->get_where('tbl_users',array('email'=>$email))->row_array();
    }

    /*get near free driver*/
    function getNearDrivers($params)
    {
        /*get car type data*/
        $car_type_data = $this->db->get_where('tbl_car_type',array('id'=>$params['car_type']))->row_array();
        /*SQL for getting car type ids*/
        $sql_car_type_id = "select id from tbl_car_type WHERE car_order >= ".$car_type_data['car_order']." AND car_order <= ".($car_type_data['car_order']+1)." ";

        $sql = "SELECT *, 6371 * 2 * ASIN(SQRT( POWER(SIN((".$params['latitude']." - latitude) * pi()/180 / 2), 2) + COS(".$params['latitude']." * pi()/180) * COS(latitude * pi()/180) *POWER(SIN((".$params['longitude']." - longitude) * pi()/180 / 2), 2) )) as distance FROM tbl_drivers WHERE car_type IN (".$sql_car_type_id.") AND is_free=1 AND is_login=1 AND is_active=1 AND is_service = '1' AND otp_verified = 1 AND is_verified = 1 AND wallet > ".$params['min_wallet']." HAVING distance <= ".($params['base_distance'])." ORDER by distance";
        return $this->db->query($sql)->result_array();
    }

}
