<?php
class Sms
{   
    private  $uri;
    public function __construct()
    {
        $this->uri = "http://www.ztsms.cn/sendSms.do";
    }
    
    public function sendsms($content, $mobile)
    {
        $data = array ('username' => 'youhuo',
                       'password' => 'I8vX4MtK',
                       'mobile' => $mobile,
                       'content' => $content . '【YOHO Monitor】',
                       'productid' => '333333',
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $this->uri );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query($data));
        //echo http_build_query($data);
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        
        print_r($return);
    }
}

?>