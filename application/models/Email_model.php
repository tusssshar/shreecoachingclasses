<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model {
	
	function __construct()
    {
        parent::__construct();
    }

	function account_opening_email($account_type = '' , $email = '')
	{
		$system_name	=	$this->db->get_where('settings' , array('type' => 'system_name'))->row()->description;
		
		$email_msg		=	"Welcome to ".$system_name."<br />";
		$email_msg		.=	"Your account type : ".$account_type."<br />";
		$email_msg		.=	"Your login password : ".$this->db->get_where($account_type , array('email' => $email))->row()->password."<br />";
		$email_msg		.=	"Login Here : ".base_url()."<br />";
		
		$email_sub		=	"Account opening email";
		$email_to		=	$email;
		
		$this->do_email($email_msg , $email_sub , $email_to);
	}
	
	function password_reset_email($new_password = '' , $account_type = '' , $email = '')
	{
		$query			=	$this->db->get_where($account_type , array('email' => $email));
		if($query->num_rows() > 0)
		{
			
			$email_msg	=	"Your account type is : ".$account_type."<br />";
			$email_msg	.=	"Your password is : ".$new_password."<br />";
			
			$email_sub	=	"Password reset request";
			$email_to	=	$email;
			$this->do_email($email_msg , $email_sub , $email_to);
			return true;
		}
		else
		{	
			return false;
		}
	}
	
	function student_registration_email($email = '')
	{
		$system_name	=	$this->db->get_where('settings' , array('type' => 'system_name'))->row()->description;
		
		$email_msg		=	"Successfully registered in Shree Academy Educations<br />";
		$email_msg		.=	"Welcome to ".$system_name."<br />";
		$email_msg		.=	"Your account has been created successfully.<br />";
		
		$email_sub		=	"Registration Confirmation";
		$email_to		=	$email;
		
		$this->do_email($email_msg , $email_sub , $email_to);
	}
	
	/***custom email sender****/
	function do_email($msg=NULL, $sub=NULL, $to=NULL, $from=NULL)
	{
		
		$config = array();
        $config['useragent']	= "CodeIgniter";
        $config['mailpath']		= "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
        $config['protocol']		= "smtp";
        
        // ===== EMAIL CONFIGURATION =====
        // For Mailchimp (Mandrill): smtp.mandrillapp.com, port 587/465, API key as password
        // For SendGrid: smtp.sendgrid.net, port 587, "apikey" as username, API key as password
        // For Gmail: smtp.gmail.com, port 587, your email, app-specific password
        // For standard SMTP: configure your mail server host, port, user, and password
        
        $config['smtp_host']	= "localhost";  // <-- CHANGE THIS
        $config['smtp_port']	= "25";         // <-- CHANGE THIS (25, 465, or 587)
        $config['smtp_user']	= "";           // <-- CHANGE THIS
        $config['smtp_pass']	= "";           // <-- CHANGE THIS
        
        $config['mailtype']		= 'html';
        $config['charset']		= 'utf-8';
        $config['crlf']			= "\r\n";
        $config['newline']		= "\r\n";
        $config['smtp_timeout']	= 30;
        $config['wordwrap']		= TRUE;

        $this->load->library('email');

        $this->email->initialize($config);

		$system_name	=	$this->db->get_where('settings' , array('type' => 'system_name'))->row()->description;
		if($from == NULL)
			$from		=	$this->db->get_where('settings' , array('type' => 'system_email'))->row()->description;
		
		$this->email->from($from, $system_name);
		$this->email->to($to);
		$this->email->subject($sub);
		
		$msg	=	$msg."<br /><br /><br /><br /><br /><br /><br /><hr /><center><a href=\"https://optimumlinkup.com.ng/contact.php\">Contact us</a></center>";
		$this->email->message($msg);

        $sent = $this->email->send();
        if (!$sent)
        {
            log_message('error', 'Email send failed to: ' . $to . ' | Error: ' . $this->email->print_debugger(array('headers', 'subject')));

            // Fallback to PHP mail()
            $config['protocol'] = 'mail';
            $this->email->initialize($config);
            $sent = $this->email->send();
            if (!$sent)
            {
                log_message('error', 'Mail fallback also failed for: ' . $to);
            }
        }
        else
        {
            log_message('info', 'Email sent successfully to: ' . $to);
        }

        return $sent;
	}
}

