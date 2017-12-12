<?php
/* 
 * Project  : Trunoir Music App
 * Date     : 28-07-2016
 * Developer: Piyush
*/
 
class Promocode_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * get a promocode record counts
     */
    function record_count()
    {
        return $this->db->count_all("tbl_promocode");
    }

    /*
     * List all promocode by limit
     */
    function list_all_promocode($limit,$per_page)
    {
        $this->db->select('*');
        $this->db->from('tbl_promocode');
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
     * function to count searched promocode by value
     */
    function record_count_search($data)
    {   
        $query = $this->db->query("SELECT * FROM tbl_promocode WHERE promocode LIKE '%".$data."%' OR promocodetype LIKE '%".$data."%' OR amount LIKE '%".$data."%' OR start_date LIKE '%".$data."%' OR end_date LIKE '%".$data."%' OR count LIKE '%".$data."%' OR description LIKE '%".$data."%' ORDER BY id desc ");

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
     * function to list searched value by promocode
     */
    function promocode_list_search($limit,$per_page,$data)
    {
        $query = $this->db->query("SELECT * FROM tbl_promocode WHERE promocode LIKE '%".$data."%' OR promocodetype LIKE '%".$data."%' OR amount LIKE '%".$data."%' OR start_date LIKE '%".$data."%' OR end_date LIKE '%".$data."%' OR count LIKE '%".$data."%' OR description LIKE '%".$data."%' ORDER BY id desc LIMIT ".$limit.",".$per_page." ");

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
     * Get promocode by id
     */
    function get_promocode($id)
    {
        return $this->db->get_where('tbl_promocode',array('id'=>$id))->row_array();
    }
    
    /*
     * Get all promocode
     */
    function get_all_promocode()
    {
        return $this->db->get('tbl_promocode')->result_array();
    }
    
    /*
     * function to add new promocode
     */
    function add_promocode($params)
    {
        $this->db->insert('tbl_promocode',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update promocode
     */
    function update_promocode($id,$params)
    {
        $this->db->where('id',$id);
        $this->db->update('tbl_promocode',$params);
    }
    
    /*
     * function to delete promocode
     */
    function delete_promocode($id)
    {
        $this->db->delete('tbl_promocode',array('id'=>$id));
    }

    /*
     * get a promocode user used record counts
     */
    function record_promocodecount()
    {
        //Get selected promocode id
        $promocode_id = $this->session->userdata('promocode_id');
        
        $this->db->where('up.promocode_id', $promocode_id);
        $this->db->from('tbl_usedpromocode up');
        $this->db->join('tbl_users as u','up.user_id = u.id');
        return $this->db->count_all_results();
    }

    /*
     * List all promocode used by user limit
     */
    function list_all_usedpromocode($limit,$per_page)
    {
        //Get selected promocode id
        $promocode_id = $this->session->userdata('promocode_id');

        $this->db->select('up.*, u.fname as username');
        $this->db->from('tbl_usedpromocode up');
        $this->db->join('tbl_users as u','up.user_id = u.id');
        $this->db->where('up.promocode_id', $promocode_id);
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
