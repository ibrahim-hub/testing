<?php
/* 
 * Project  : Trunoir Music App
 * Date     : 10-06-2016
 * Developer: Piyush
*/
 
class Products_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * get a users record counts
     */
    function record_count()
    {
   
        return $this->db->count_all("tbl_users");
    }

    /*
     * List all users by limit
     */
    function list_all_users($limit,$per_page)
    {
        $this->db->select('*');
        $this->db->from('tbl_users');
        $this->db->order_by("id", "desc"); 
        $this->db->limit($per_page,$limit);
        $query = $this->db->get();      

        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return false;
        }
    }

    /*
     * function to count searched users by value
     */
    function record_count_search($data)
    {   
        $query = $this->db->query("SELECT * FROM tbl_users WHERE fname LIKE '%".$data."%' OR lname LIKE '%".$data."%' OR email LIKE '%".$data."%' OR phone LIKE '%".$data."%' OR last_login LIKE '%".$data."%' ORDER BY id desc ");

        if($query->num_rows() > 0)
        {
            return $query->num_rows();
        }
        else
        {
            return false;
        }
    }

    /*
     * function to list searched value by users
     */
    function users_list_search($limit,$per_page,$data)
    {
        $query = $this->db->query("SELECT * FROM tbl_users WHERE fname LIKE '%".$data."%' OR lname LIKE '%".$data."%' OR email LIKE '%".$data."%' OR phone LIKE '%".$data."%' OR last_login LIKE '%".$data."%' ORDER BY id desc LIMIT ".$limit.",".$per_page." ");

        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return false;
        }
    }

    /*
     * Get users by id
     */
    function get_users($id)
    {
        return $this->db->get_where('tbl_users',array('id'=>$id))->row_array();
    }
    
    /*
     * Get all users
     */
    function get_all_users()
    {
        return $this->db->get('tbl_users')->result_array();
    }
    
     /*
     * function to add new table
     */
    function add_products($params)
    {
        $this->db->insert('tbl_product_master',$params);
        return $this->db->insert_id();
    }
    
    
    /*
     * function to update users
     */
    function update_users($id,$params)
    {
        $this->db->where('id',$id);
        $this->db->update('tbl_users',$params);
    }
    
    /*
     * function to delete users
     */
    function delete_users($id)
    {
        $this->db->delete('tbl_users',array('id'=>$id));
    }

    /*
     * get a promocode user used record counts
     */
    function record_promocodecount()
    {
        //Get selected user id
        $user_id = $this->session->userdata('user_id');
        
        $this->db->where('up.user_id', $user_id);
        $this->db->from('tbl_usedpromocode up');
        $this->db->join('tbl_users as u','up.user_id = u.id');
        return $this->db->count_all_results();
    }

    /*
     * List all promocode used by user limit
     */
    function list_all_usedpromocode($limit,$per_page)
    {
        //Get selected user id
        $user_id = $this->session->userdata('user_id');

        $this->db->select('up.*, u.fname as username');
        $this->db->from('tbl_usedpromocode up');
        $this->db->join('tbl_users as u','up.user_id = u.id');
        $this->db->where('up.user_id', $user_id);
        $this->db->order_by("up.id", "desc");
        $this->db->limit($per_page,$limit);
        $query = $this->db->get();      

        if($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return false;
        }
    }
}
