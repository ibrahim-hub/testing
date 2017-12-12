<?php
/* 
 * Project  : Trunoir Music App
 * Date     : 10-06-2016
 * Developer: Piyush
*/
 
class Cartype_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->car_image_path = base_url().'assets/car_images/';
    }
    
    /*
     * get a cartype record counts
     */
    function record_count()
    {
        return $this->db->count_all("tbl_car_type");
    }

    /*
     * List all cartype by limit
     */
    function list_all_cartype()
    {
            $sql = "select * from tbl_car_type";
            $car_data = $this->db->query($sql)->result_array();
            foreach ($car_data as $key => $value) {
                $car_data[$key]['car_image'] = $this->car_image_path.$value['car_image'];
                $car_data[$key]['car_image_unselected'] = $this->car_image_path.$value['car_image_unselected'];
            }
        /*echo "<pre>";
        print_r($car_data);
        die;*/
        return $car_data;
    }

    /*
     * Check cartype exist
     */
    function check_cartype_exist($cartype, $car_category)
    {
        return $this->db->get_where('tbl_car_type',array('car_type'=>$cartype,'car_category'=>$car_category))->row_array();
    }

    /*
     * Get trip type car list
     */
    function triptype_carlist($trip_type)
    {
        return $this->db->get_where('tbl_car_type',array('trip_type'=>$trip_type))->result_array();
    }

    /*
     * Get cartype by id
     */
    function get_cartype($id)
    {
        $data = $this->db->get_where('tbl_car_type',array('id'=>$id))->row_array();
        $data['car_image'] = $this->car_image_path.$data['car_image'];
        $data['car_image_unselected'] = $this->car_image_path.$data['car_image_unselected'];
        return $data;
    }
    
    /*
     * Get all cartype
     */
    function get_all_cartype()
    {
        return $this->db->get('tbl_car_type')->result_array();
    }

    /*
     * Get max car type order
     */
    function get_max_carorder()
    {
        $this->db->select_max('car_order');
        $result = $this->db->get('tbl_car_type')->row();  
        return $result->car_order + 1;
    }

    /*
     * Check car order exist or not
     */
    function check_carorder_exists($car_order)
    {
        return $this->db->get_where('tbl_car_type',array('car_order'=>$car_order))->row_array();
    }
    
    /*
     * function to add new cartype
     */
    function add_cartype($params)
    {
        $this->db->insert('tbl_car_type',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update car type
     */
    function update_cartype($id,$params)
    {
        $this->db->where('id',$id);
        $this->db->update('tbl_car_type',$params);
    }

    /*
     * function to delete cartype
     */
    function delete_cartype($id)
    {
        $this->db->delete('tbl_car_type',array('id'=>$id));
    }
    
}
