<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }

    //COMMON FUNCTION FOR SENDING SMS
    function send_sms($message = '' , $reciever_phone = '')
    {
        $active_sms_service = $this->db->get_where('settings' , array(
            'type' => 'active_sms_service'
        ))->row()->description;
        if ($active_sms_service == '' || $active_sms_service == 'disabled')
            return;
        if ($active_sms_service == 'clickatell') {
            $this->send_sms_via_clickatell($message , $reciever_phone );
        }
        if ($active_sms_service == 'twilio') {
            $this->send_sms_via_twilio($message , $reciever_phone );
        }
    }
    
    // SEND SMS VIA CLICKATELL API
    function send_sms_via_clickatell($message = '' , $reciever_phone = '') {
        
        $clickatell_user       = $this->db->get_where('settings', array('type' => 'clickatell_user'))->row()->description;
        $clickatell_password   = $this->db->get_where('settings', array('type' => 'clickatell_password'))->row()->description;
        $clickatell_api_id     = $this->db->get_where('settings', array('type' => 'clickatell_api_id'))->row()->description;
        $clickatell_baseurl    = "http://api.clickatell.com";

        $text   = urlencode($message);
        $to     = $reciever_phone;

        // auth call
        $url = "$clickatell_baseurl/http/auth?user=$clickatell_user&password=$clickatell_password&api_id=$clickatell_api_id";

        // do auth call
        $ret = file($url);

        // explode our response. return string is on first line of the data returned
        $sess = explode(":",$ret[0]);
        print_r($sess);echo '<br>';
        if ($sess[0] == "OK") {

            $sess_id = trim($sess[1]); // remove any whitespace
            $url = "$clickatell_baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text";

            // do sendmsg call
            $ret = file($url);
            $send = explode(":",$ret[0]);
            print_r($send);echo '<br>';
            if ($send[0] == "ID") {
                echo "successnmessage ID: ". $send[1];
            } else {
                echo "send message failed";
            }
        } else {
            echo "Authentication failure: ". $ret[0];
        }
    }
    
    
    // SEND SMS VIA TWILIO API
    function send_sms_via_twilio($message = '' , $reciever_phone = '') {
        
        // LOAD TWILIO LIBRARY
        require_once(APPPATH . 'libraries/twilio_library/Twilio.php');


        $account_sid    = $this->db->get_where('settings', array('type' => 'twilio_account_sid'))->row()->description;
        $auth_token     = $this->db->get_where('settings', array('type' => 'twilio_auth_token'))->row()->description;
        $client         = new Services_Twilio($account_sid, $auth_token); 

        $client->account->messages->create(array( 
            'To'        => $reciever_phone, 
            'From'      => $this->db->get_where('settings', array('type' => 'twilio_sender_phone_number'))->row()->description,
            'Body'      => $message   
        ));

    }

    // SEND WHATSAPP MESSAGE VIA TWILIO API
    function send_whatsapp($message = '', $reciever_phone = '')
    {
        $active_whatsapp = $this->db->get_where('settings', array('type' => 'active_whatsapp'))->row();
        if (!$active_whatsapp || $active_whatsapp->description != 'enabled')
            return array('success' => false, 'error' => 'WhatsApp is not enabled in settings');

        $sid_row    = $this->db->get_where('settings', array('type' => 'twilio_account_sid'))->row();
        $token_row  = $this->db->get_where('settings', array('type' => 'twilio_auth_token'))->row();
        $number_row = $this->db->get_where('settings', array('type' => 'twilio_whatsapp_number'))->row();

        $account_sid     = $sid_row    ? trim($sid_row->description)    : '';
        $auth_token      = $token_row  ? trim($token_row->description)  : '';
        $whatsapp_number = $number_row ? trim($number_row->description) : '';

        if (!$account_sid || !$auth_token || !$whatsapp_number) {
            $missing = array();
            if (!$account_sid)     $missing[] = 'twilio_account_sid';
            if (!$auth_token)      $missing[] = 'twilio_auth_token';
            if (!$whatsapp_number) $missing[] = 'twilio_whatsapp_number';
            log_message('error', 'WhatsApp settings missing: ' . implode(', ', $missing));
            return array('success' => false, 'error' => 'Missing settings: ' . implode(', ', $missing));
        }

        $normalized_to = $this->normalize_phone_e164($reciever_phone);
        $to_whatsapp   = (strpos($normalized_to, 'whatsapp:') === 0) ? $normalized_to : 'whatsapp:' . $normalized_to;
        $from_whatsapp = (strpos($whatsapp_number, 'whatsapp:') === 0) ? $whatsapp_number : 'whatsapp:' . $whatsapp_number;

        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode($account_sid) . '/Messages.json';
        $post_fields = http_build_query(array(
            'To'   => $to_whatsapp,
            'From' => $from_whatsapp,
            'Body' => $message,
        ));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_USERPWD, $account_sid . ':' . $auth_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $raw_body  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($raw_body === false) {
            log_message('error', 'WhatsApp cURL error: ' . $curl_err);
            return array(
                'success'   => false,
                'error'     => 'cURL error: ' . $curl_err,
                'to'        => $to_whatsapp,
                'from'      => $from_whatsapp,
                'http_code' => $http_code,
            );
        }

        $decoded = json_decode($raw_body, true);
        $is_ok   = ($http_code >= 200 && $http_code < 300);

        $details = array(
            'success'       => $is_ok,
            'http_code'     => $http_code,
            'to'            => isset($decoded['to']) ? $decoded['to'] : $to_whatsapp,
            'from'          => isset($decoded['from']) ? $decoded['from'] : $from_whatsapp,
            'sid'           => isset($decoded['sid']) ? $decoded['sid'] : null,
            'status'        => isset($decoded['status']) ? $decoded['status'] : null,
            'date_created'  => isset($decoded['date_created']) ? $decoded['date_created'] : null,
            'price'         => isset($decoded['price']) ? $decoded['price'] : null,
            'error_code'    => isset($decoded['error_code']) ? $decoded['error_code'] : (isset($decoded['code']) ? $decoded['code'] : null),
            'error_message' => isset($decoded['error_message']) ? $decoded['error_message'] : (isset($decoded['message']) ? $decoded['message'] : null),
            'more_info'     => isset($decoded['more_info']) ? $decoded['more_info'] : null,
            'raw_response'  => $raw_body,
        );

        if (!$is_ok)
            $details['error'] = $details['error_message'] ? $details['error_message'] : ('HTTP ' . $http_code);

        log_message($is_ok ? 'info' : 'error', 'WhatsApp send: http=' . $http_code . ' sid=' . $details['sid'] . ' status=' . $details['status']);
        return $details;
    }

    private function normalize_phone_e164($phone, $default_country_code = '+91')
    {
        $phone = trim((string)$phone);
        if ($phone === '') return $phone;

        // Preserve any 'whatsapp:' prefix and re-apply at the end
        $prefix = '';
        if (stripos($phone, 'whatsapp:') === 0) {
            $prefix = 'whatsapp:';
            $phone = substr($phone, strlen('whatsapp:'));
        }

        // If already E.164 (+ then digits), keep as-is
        if (strpos($phone, '+') === 0) {
            return $prefix . '+' . preg_replace('/\D/', '', substr($phone, 1));
        }

        // Strip non-digits, then apply default country code
        $digits = preg_replace('/\D/', '', $phone);

        // 10-digit Indian mobile: prepend +91
        if (strlen($digits) === 10) {
            return $prefix . $default_country_code . $digits;
        }

        // 12-digit starting with 91 (e.g. 919768049630): treat as already-prefixed
        if (strlen($digits) === 12 && strpos($digits, '91') === 0) {
            return $prefix . '+' . $digits;
        }

        // Fallback: prepend '+' and let Twilio judge it
        return $prefix . '+' . $digits;
    }
}