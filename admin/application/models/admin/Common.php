<?php
/* 

*/
class Common extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->utc_time = time();
        $this->output->enable_profiler(FALSE);
        
        //Language Translation
        $language = $this->session->userdata('language');
        
        if(!empty($language))
        {
            $this->config->set_item('language', $language);
            $this->load->language('rest_controller', $this->config->item('language'));
        }
        else
        {
            $this->session->set_userdata('language', 'english');
            $this->config->set_item('language', 'english');
            $this->load->language('rest_controller', $this->config->item('language'));
        }
    }

    /*
     * Get menu action
     */
    function get_menu($menu)
    {
        $menuname= "";
        $filename = $this->uri->segment(2);
        if($filename == $menu)
        {
            $menuname = "active";
        }
        else
        {
            $menuname = "has-submenu";
        }
        return  $menuname;
    }
    
    /*
     * Check login authentication
     */
    function login($email, $password)
	{  
		$this->db->select('*');
		$this->db->from('tbl_admin_details');
		$this->db->where('email', $email);
		$this->db->where('password',md5($password));
        $this->db->where('is_active',1);
		$this->db->limit(1);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{ 
            return $query->result();
		}
		else
		{
			return false;
		}
	}

    /*
     * Get admin detail by id
     */
    function get_admin($id)
    {
        return $this->db->get_where('tbl_admin_details',array('id'=>$id))->row_array();
    }

    /*
     * function to update tbl_admin_details
     */
    function update_admin($id,$params)
    {
        $this->db->where('id',$id);
        $this->db->update('tbl_admin_details',$params);
    }

    /*
     * Upload image and thumb images on specific path
    */
    function uploadImage($files, $filename, $upload_path)
    {
        if (!empty($files['name']) && $files['size'] > 0) 
        {
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name'] = random_string('numeric', 5).strtotime(date("Ymd his"));
            // $config['max_size'] = '2048KB';
            // $config['max_width']  = '2000';
            // $config['max_height']  = '2000';

            $this->upload->initialize($config);
            if($this->upload->do_upload($filename))
            {   
                $w = $this->upload->data();
                $uploaded_image = $w['file_name'];
                $config = array(
                'image_library'  => 'gd2',
                'new_image'      => $upload_path."thumb/",
                'source_image'   => $upload_path.$w['file_name'],
                'create_thumb'   => false,    
                'width'          => "100",
                'height'         => "100",
                'maintain_ratio' => TRUE,
                );
                $this->load->library('image_lib'); // add library
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                return $uploaded_image;
            }
            else
            {
                return array('status' => false, 'error' => $this->upload->display_errors());
            }
        }
    }

    /*
     * Upload edit image and thumb images on specific path
    */
    function uploadEditImage($files, $filename, $old_profile_image, $upload_path)
    {   
        if (!empty($files['name']) && $files['size'] > 0) 
        {
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name'] = random_string('numeric', 5).strtotime(date("Ymd his"));
            // $config['max_size'] = '2048KB';
            // $config['max_width']  = '2000';
            // $config['max_height']  = '2000';

            $this->upload->initialize($config);
            if($this->upload->do_upload($filename))
            {   
                //Remove old file
                $this->removeImage($old_profile_image, $upload_path);

                $w = $this->upload->data();
                $uploaded_image = $w['file_name'];
                $config = array(
                'image_library'  => 'gd2',
                'new_image'      => $upload_path."thumb/",
                'source_image'   => $upload_path.$w['file_name'],
                'create_thumb'   => false,    
                'width'          => "100",
                'height'         => "100",
                'maintain_ratio' => TRUE,
                );
                $this->load->library('image_lib'); // add library
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                return $uploaded_image;
            }
            else
            {
                return array('status' => false, 'error' => $this->upload->display_errors());
            }
        }
    }

    /*
     * Get setings details
    */
    function getSettingsdetails($user_type)
    {
        $this->db->select('*');
        $this->db->from('tbl_settings');
        $this->db->where('type', $user_type);
        $query = $this->db->get();      

        if($query->num_rows() > 0)
        {   
            $settings_data = array();
            foreach ($query->result_array() as $settingskey => $settingsvalue) {
                $settings_data[$settingsvalue['meta_key']] = $settingsvalue['meta_value'];
            }
            return $settings_data;
        }
        else
        {
            return false;
        }
    }

    /*
     * Get near all free driver list
    */
    function getNeardriverlist($pickup_latitude, $pickup_longitude, $user_id, $car_type)
    {
        $user_data = $this->Common_model->getUserDetails($user_id);

        //Get distance details
        $settings = $this->getSettingsdetails('driver');
    
        $sql = "SELECT *, 6371 * 2 * ASIN(SQRT( POWER(SIN(($pickup_latitude - latitude) * pi()/180 / 2), 2) + COS($pickup_latitude * pi()/180) * COS(latitude * pi()/180) *POWER(SIN(($pickup_longitude - longitude) * pi()/180 / 2), 2) )) as distance FROM tbl_drivers WHERE is_free=1 AND is_login=1 AND is_verified = 1 AND is_active=1 AND is_service = 1 AND car_type=".$car_type." AND wallet > ".$settings['min_wallet']." HAVING  distance <= ".$settings['base_distance']." ORDER by distance";

        $query = $this->db->query($sql);

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
     * Date Time Conversion
    */
    function GmtTimeToLocalTime($time) {
        $date =  new DateTime(date('Y-m-d h:i:s',$time),new DateTimezone('UTC'));
        $date->setTimezone(new \DateTimezone('Asia/Calcutta'));
        return $date->format("Y-m-d H:i:s");
    }

    //Date convert on specific timezone.
    function date_convert($date, $timezone, $dateformat) {
        if($date == '0000-00-00 00:00:00')
            return $date;
        $date = new DateTime($date);
        $date->setTimezone(new DateTimeZone($timezone));

        return $date->format($dateformat);
    }

    /*
     * get full address by lat long
    */
    function get_lat_long($address){

        $address = str_replace(" ", "+", $address);

        $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
        $json = json_decode($json);
        $lat = $long = 0;
        if(count($json->results) > 0)
        {
            $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        }
        return $lat.','.$long;
    }
    
    /*
     * Remove org & thumb image on specific path
    */
    function removeImage($image, $path)
    { 
        //Remove uploaded images
        if($image != 'default.jpeg' && file_exists($path.$image))
        {
            unlink($path.$image);   
        }
        if($image != 'default.jpeg' && file_exists($path.'thumb/'.$image))
        {
            unlink($path.'thumb/'.$image);  
        }
    }
}
/*End class Common ends*/
?>