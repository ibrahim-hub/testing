<?php
/* 
 * Project  : Trunoir Music App
 * Date     : 25-07-2016
 * Developer: Piyush
*/
 
class Order_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Trips by dashboard counts
     */
    function record_count_trip_dashboard($status)
    {   
        $this->db->where('t.status', $status);
        $this->db->from('tbl_trip_details t');
        $this->db->join('tbl_users as u','t.user_id = u.id');
        return $this->db->count_all_results();
    }//End record_count_orders method

    /*
     * get a trip record counts
     */
    function record_count()
    {
        //Get selected trip type
        $status = $this->session->userdata("trip_status");
        $trip_type = $this->session->userdata("trip_type");

        $this->db->where('t.status', $status);
        $this->db->where('t.trip_type', $trip_type);
        $this->db->from('tbl_trip_details t');
        $this->db->join('tbl_users as u','t.user_id = u.id');
        return $this->db->count_all_results();

        return $this->db->count_all("tbl_trip_details");
    }

    /*
     * List all trip by limit
     */
    function list_all_trip($limit,$per_page)
    {
        //Get selected trip type
        $status = $this->session->userdata("trip_status");
        $trip_type = $this->session->userdata("trip_type");
        
        $this->db->select('t.*, u.fname as username');
        $this->db->from('tbl_trip_details t');
        $this->db->join('tbl_users as u','t.user_id = u.id');
        $this->db->where('t.status', $status);
        $this->db->where('t.trip_type', $trip_type);
        $this->db->order_by("t.id", "desc");
        $this->db->limit($per_page,$limit);
        $query = $this->db->get();      

        if($query->num_rows() > 0)
        {
            $trip_list = array();
            foreach ($query->result_array() as $orderkey => $tripvalue) {
                $car_data = $this->db->get_where('tbl_car_type',array('id'=>$tripvalue['car_type']))->row_array();
                if($car_data){
                    $tripvalue['car_data'] = $car_data;
                }
                $tripvalue['drivername'] = '';
                if($tripvalue['driver_id'] != '0')
                {
                    $driver_data = $this->driver_model->get_driver($tripvalue['driver_id']);
                    $tripvalue['drivername'] = $driver_data['fname'].' '.$driver_data['lname'];
                }
                //$tripvalue['tripdatetime'] = $this->common->GmtTimeToLocalTime(strtotime($tripvalue['tripdatetime']));
                $trip_list[] = $tripvalue;
            }
            return $trip_list;
        }
        else
        {
            return false;
        }
    }

    /*
     * function to count searched trip by value
     */
    function record_count_search($data)
    {   
        $status = $this->session->userdata("trip_status");
        $trip_type = $this->session->userdata("trip_type");
        
        $query = $this->db->query("SELECT t.*, u.fname as username
        FROM (tbl_trip_details t) JOIN tbl_users as u ON t.user_id = u.id 
        WHERE (u.fname LIKE '%".$data."%' OR t.pickup_address LIKE '%".$data."%' OR t.dropoff_address LIKE '%".$data."%' OR t.distance LIKE '%".$data."%' OR t.tot_amount LIKE '%".$data."%' OR t.tripdatetime LIKE '%".$data."%') AND (t.trip_type LIKE '".$trip_type."' AND t.status LIKE '".$status."') ORDER BY t.id desc");

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
     * function to list searched value by trip
     */
    function trip_list_search($limit,$per_page,$data)
    {
        $status = $this->session->userdata("trip_status");
        $trip_type = $this->session->userdata("trip_type");

        $query = $this->db->query("SELECT t.*, u.fname as username
        FROM (tbl_trip_details t) JOIN tbl_users as u ON t.user_id = u.id 
        WHERE (u.fname LIKE '%".$data."%' OR t.pickup_address LIKE '%".$data."%' OR t.dropoff_address LIKE '%".$data."%' OR t.distance LIKE '%".$data."%' OR t.tot_amount LIKE '%".$data."%' OR t.tripdatetime LIKE '%".$data."%') AND (t.trip_type LIKE '".$trip_type."' AND t.status LIKE '".$status."') ORDER BY t.id desc LIMIT ".$limit.",".$per_page." ");

        if($query->num_rows() > 0)
        {
            $trip_list = array();
            foreach ($query->result_array() as $orderkey => $tripvalue) {
                $car_data = $this->db->get_where('tbl_car_type',array('id'=>$tripvalue['car_type']))->row_array();
                if($car_data){
                    $tripvalue['car_data'] = $car_data;
                }
                $tripvalue['drivername'] = '';
                if($tripvalue['driver_id'] != '0')
                {
                    $driver_data = $this->driver_model->get_driver($tripvalue['driver_id']);
                    $tripvalue['drivername'] = $driver_data['fname'].' '.$driver_data['lname'];
                }
                $tripvalue['tripdatetime'] = $this->common->GmtTimeToLocalTime(strtotime($tripvalue['tripdatetime']));
                $trip_list[] = $tripvalue;
            }
            return $trip_list;
        }
        else
        {
            return false;
        }
    }

    /*
     * Get trip by id
     */
    function get_trip($id)
    {
        $this->db->select('t.*, u.fname as username');
        $this->db->from('tbl_trip_details t');      
        $this->db->join('tbl_users as u','t.user_id = u.id');         
        $this->db->where('t.id', $id);    
        $query = $this->db->get();
        if($query->num_rows() >= 1)
        {
            $trip_data = $query->row_array();
            $trip_data['drivername'] = '';
            if($trip_data['driver_id'] != '0')
            {
                $driver_data = $this->driver_model->get_driver($trip_data['driver_id']);
                $trip_data['drivername'] = $driver_data['fname'].' '.$driver_data['lname'];
            }
            $trip_data['tripdatetime'] = $this->common->GmtTimeToLocalTime(strtotime($trip_data['tripdatetime']));
            return $trip_data;
        }
        else
        {
            return false;
        }
    }

    /*
     * Get all trip
     */
    function get_all_trip()
    {
        return $this->db->get('tbl_trip_details')->result_array();
    }
    
    /*
     * function to add new trip
     */
    function add_trip($params)
    {
        $this->db->insert('tbl_trip_details',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update trip
     */
    function update_trip($id,$params)
    {
        $this->db->where('id',$id);
        $this->db->update('tbl_trip_details',$params);
    }
    
    /*
     * function to delete trip
     */
    function delete_trip($id)
    {
        $this->db->delete('tbl_trip_details',array('id'=>$id));
    }
}
