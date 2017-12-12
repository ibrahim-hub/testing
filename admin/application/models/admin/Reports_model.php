<?php
class reports_model  extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->utc_time = time();
        $this->output->enable_profiler(FALSE);
    }

    function driver_list()
    {
        $this->db->select('*');
        $this->db->from('tbl_drivers');
        $this->db->order_by("id", "ASC"); 
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

    // Order Models
    public function record_count_order($data)
    {   
        extract($data);
        /*$status_in = array('Completed','Cancelled');*/
        $status_in = array('Completed');
        if($start_date!='' && $end_date!='')
        {   
            $this->db->where('DATE(tripdatetime) >=', $start_date);
            $this->db->where('DATE(tripdatetime) <=', $end_date);
        }
        
        if($driver_id!=0)
            $this->db->where('driver_id', $driver_id);
        $this->db->where_in('status', $status_in);
        $this->db->from('tbl_trip_details');
        return $this->db->count_all_results();
    }

    function order_listreports($limit, $per_page, $data)
    {
        extract($data);
        /*$status_in = array('Completed','Cancelled');*/
        $status_in = array('Completed');
        $this->db->select('*');
        $this->db->from('tbl_trip_details');
        if($start_date!='' && $end_date!='')
        {   
            $this->db->where('DATE(tripdatetime) >=', $start_date);
            $this->db->where('DATE(tripdatetime) <=', $end_date);
        }
        if($driver_id!=0)
            $this->db->where('driver_id', $driver_id);
       
        $this->db->where_in('status', $status_in);
        $this->db->order_by("id", "DESC");
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

    function exportorder_list($start_date, $end_date, $driver_id)
    {
        /*$status_in = array('Completed','Cancelled');*/
        $status_in = array('Completed');
        $this->db->select('*');
        $this->db->from('tbl_trip_details');
        if($start_date!= 0 && $end_date!=0)
        {   
            $this->db->where('DATE(tripdatetime) >=', $start_date);
            $this->db->where('DATE(tripdatetime) <=', $end_date);
        }
        if($driver_id!=0)
            $this->db->where('driver_id', $driver_id);
        
        $this->db->where_in('status', $status_in);
        $this->db->order_by("id", "DESC");
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
?>