<?php
class WPNTypesEnum{       
    const Toast = 'wns/toast';
    const Badge = 'wns/badge';
    const Tile  = 'wns/tile';
    const Raw   = 'wns/raw';
}                         
class WPNResponse{
    public $message = 'Piyush';
    public $error = true;
    public $httpCode = '';
    
    function __construct($message, $httpCode, $error = true){
        $this->message = $message;
        $this->httpCode = $httpCode;
        $this->error = $error;
    }
}
class WPN_Member{       
    
    
    private $access_token = '';
    private $sid = '';
    private $secret = '';
         
    function __construct(){
        $this->sid    = 'ms-app://s-1-15-2-2768873194-2761577344-511920887-1834477321-423931976-945623970-3311394411';
        $this->secret = 'wDgRknuAPS4fWT-rVUWBfuvFtZBW2ync';
    }
    
    private function get_access_token(){
        if($this->access_token != ''){
            return;
        }
        $str = "grant_type=client_credentials&client_id=$this->sid&client_secret=$this->secret&scope=notify.windows.com";//s.notify.live.net
        $url = "https://login.live.com/accesstoken.srf"; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$str");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);                       
        $output = json_decode($output);
        if(isset($output->error)){
            throw new Exception($output->error_description);
        }
        $this->access_token = $output->access_token;
        $this->token_type = 'bearer';
    }
    public function build_tile_xml($message='Piyush Test'){
        $push_data = json_decode($message);
        return '<toast launch="'.htmlentities($message).'"><visual><binding template="ToastText01"><text id="1">'.$push_data->message.'</text></binding></visual></toast>';
    }
    public function post_tile($uri, $xml_data, $type = WPNTypesEnum::Toast, $tileTag = ''){
        if($this->access_token == ''){
            $this->get_access_token();
        }
        
        $headers = array('Content-Type: text/xml', "Content-Length: " . strlen($xml_data), "X-WNS-Type: $type", "Authorization: Bearer $this->access_token");
        if($tileTag != ''){
            array_push($headers, "X-WNS-Tag: $tileTag");
        }
        $ch = curl_init($uri);
        # Tiles: http://msdn.microsoft.com/en-us/library/windows/apps/xaml/hh868263.aspx
        # http://msdn.microsoft.com/en-us/library/windows/apps/hh465435.aspx
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $response = curl_getinfo( $ch );
        curl_close($ch);
        
        $code = $response['http_code'];
        if($code == 200){
            return new WPNResponse('Successfully sent message', $code);
        }
        else if($code == 401){
            $this->access_token = '';
            return new WPNResponse('Unauthorized', $code, true);
        }
        else if($code == 410 || $code == 404){
            return new WPNResponse('Expired or invalid URI', $code, true);
        }
        else{
            return new WPNResponse('Unknown error while sending message', $code, true);
        }
    }
}
class WPN_Driver{       
    
    
    private $access_token = '';
    private $sid = '';
    private $secret = '';
         
    function __construct(){
        $this->sid    = 'ms-app://s-1-15-2-1223596184-2995055137-3357499998-1677822917-297878661-3777721113-1178801292';
        $this->secret = 'Pb3KODme0bSWqpKaBCtB7NIZRI/50cvo';
    }
    
    private function get_access_token(){
        if($this->access_token != ''){
            return;
        }
        $str = "grant_type=client_credentials&client_id=$this->sid&client_secret=$this->secret&scope=notify.windows.com";//s.notify.live.net
        $url = "https://login.live.com/accesstoken.srf"; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$str");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);                       
        $output = json_decode($output);
        if(isset($output->error)){
            throw new Exception($output->error_description);
        }
        $this->access_token = $output->access_token;
        $this->token_type = 'bearer';
    }
    public function build_tile_xml($message='Piyush Test'){
        $push_data = json_decode($message);
        return '<toast launch="'.htmlentities($message).'"><visual><binding template="ToastText01"><text id="1">'.$push_data->message.'</text></binding></visual></toast>';
    }
    public function post_tile($uri, $xml_data, $type = WPNTypesEnum::Toast, $tileTag = ''){
        if($this->access_token == ''){
            $this->get_access_token();
        }
        
        $headers = array('Content-Type: text/xml', "Content-Length: " . strlen($xml_data), "X-WNS-Type: $type", "Authorization: Bearer $this->access_token");
        if($tileTag != ''){
            array_push($headers, "X-WNS-Tag: $tileTag");
        }
        $ch = curl_init($uri);
        # Tiles: http://msdn.microsoft.com/en-us/library/windows/apps/xaml/hh868263.aspx
        # http://msdn.microsoft.com/en-us/library/windows/apps/hh465435.aspx
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $response = curl_getinfo( $ch );
        curl_close($ch);
        
        $code = $response['http_code'];
        if($code == 200){
            return new WPNResponse('Successfully sent message', $code);
        }
        else if($code == 401){
            $this->access_token = '';
            return new WPNResponse('Unauthorized', $code, true);
        }
        else if($code == 410 || $code == 404){
            return new WPNResponse('Expired or invalid URI', $code, true);
        }
        else{
            return new WPNResponse('Unknown error while sending message', $code, true);
        }
    }
}
?>
