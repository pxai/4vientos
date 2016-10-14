<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* $id$
* Session_Checker, checks if session was started or not
*/

class Session_Checker  {
	public $file;
	public $who;
	private $dnum1;
	private $dnum2;	


    var $CI;

    function Session_Checker() {
        $this->CI =& get_instance();
    }


	/**
	* simple_sing_in
	* Sign in using a database table
	*/
	public function check_session ()
	{
		$account_url = "account";
		$login_url = "account/sign_in";
		
		$this->CI->load->library('account/recaptcha');

      if  (!preg_match("%^".$account_url."%", $this->CI->uri->uri_string) && !$this->CI->authentication->is_signed_in())
             redirect($login_url);
        
	}
	
	
}
/* End of file session_checker.php */
/* Location: ./application/hooks/session_checker.php */