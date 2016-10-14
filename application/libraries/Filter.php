<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Sanitizer for content
*/
class Filter  {

	
	/**
	* sanitize
	* Clears HTML tags
	*/
	public function sanitize($input)
	{
		$clear_input = "";

		//$clear_input = htmlentities($input);		
		$clear_input = $input;		
		
		return $clear_input;
	}
	
	
	/**
	* sanitize_array
	* sanitize entire array
	*/
	public function sanitize_post()
	{
		
		foreach ($_POST as $name => $value)
		{
			$_POST[$name] = $this->sanitize($value);
		} 
		
	}
	

	
}
/* End of file Filter.php */
/* Location: ./system/application/controllers/Filter.php */
