<?php
/* 
 * Project  : Utimate Taxi App
 * Date     : 05-05-2017
 * Developer: Piyush
*/
 
class Couponcard_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * get a couponcard record counts
     */
    function record_count()
    {
        $this->db->from('tbl_driver_couponcard dc');
        $this->db->join('tbl_drivers as d','dc.driver_id = d.id', 'left');
        return $this->db->count_all_results();

        return $this->db->count_all("tbl_driver_couponcard");
    }

    /*
     * List all couponcard by limit
     */
    function list_all_couponcard($limit,$per_page)
    {
        $this->db->select('dc.*, d.fname as drivername');
        $this->db->from('tbl_driver_couponcard dc');
        $this->db->join('tbl_drivers as d','dc.driver_id = d.id', 'left');
        $this->db->order_by("dc.id", "desc"); 
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
     * function to count searched couponcard by value
     */
    function record_count_search($data)
    {   
        $query = $this->db->query("SELECT dc.*, d.fname as drivername FROM (tbl_driver_couponcard dc) LEFT JOIN tbl_drivers as d ON dc.driver_id = d.id 
        WHERE (d.fname LIKE '%".$data."%' OR dc.coupon_card LIKE '%".$data."%' OR dc.amount LIKE '%".$data."%' OR dc.description LIKE '%".$data."%' OR dc.status LIKE '%".$data."%' OR dc.insertdate LIKE '%".$data."%') ORDER BY dc.id desc");

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
     * function to list searched value by couponcard
     */
    function couponcard_list_search($limit,$per_page,$data)
    {
        $query = $this->db->query("SELECT dc.*, d.fname as drivername FROM (tbl_driver_couponcard dc) LEFT JOIN tbl_drivers as d ON dc.driver_id = d.id 
        WHERE (d.fname LIKE '%".$data."%' OR dc.coupon_card LIKE '%".$data."%' OR dc.amount LIKE '%".$data."%' OR dc.description LIKE '%".$data."%' OR dc.status LIKE '%".$data."%' OR dc.insertdate LIKE '%".$data."%') ORDER BY dc.id desc LIMIT ".$limit.",".$per_page." ");

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
     * Get couponcard by id
     */
    function get_couponcard($id)
    {
        return $this->db->get_where('tbl_driver_couponcard',array('id'=>$id))->row_array();
    }
    
    /*
     * Get all couponcard
     */
    function get_all_couponcard()
    {
        $this->db->select('dc.*, d.fname as drivername');
        $this->db->from('tbl_driver_couponcard dc');
        $this->db->join('tbl_drivers as d','dc.driver_id = d.id', 'left');
        $this->db->order_by("dc.id", "desc"); 
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
     * function to add new couponcard
     */
    function add_couponcard($params)
    {
        $this->db->insert('tbl_driver_couponcard',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update couponcard
     */
    function update_couponcard($id,$params)
    {
        $this->db->where('id',$id);
        $this->db->update('tbl_driver_couponcard',$params);
    }
    
    /*
     * function to delete couponcard
     */
    function delete_couponcard($id)
    {
        $this->db->delete('tbl_driver_couponcard',array('id'=>$id));
    }

    /*
     * Check coupon card exists
     */
    function check_couponcard_exists($coupon_card)
    {
        return $this->db->get_where('tbl_driver_couponcard',array('coupon_card'=>$coupon_card))->row_array();
    }
}
