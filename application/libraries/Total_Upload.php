<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* $Id$
* Extension for upload library to allow any type of document.
*/ 
class Total_Upload extends CI_Upload {

	function is_allowed_filetype() {

		if (count($this->allowed_types) == 0 OR ! is_array($this->allowed_types))
		{
			$this->set_error('upload_no_file_types');
			return FALSE;
		}

		if (in_array("*", $this->allowed_types))
		{
			return TRUE;
		}

		$image_types = array('gif', 'jpg', 'jpeg', 'png', 'jpe');

		foreach ($this->allowed_types as $val)
		{
			$mime = $this->mimes_types(strtolower($val));

			// Images get some additional checks
			if (in_array($val, $image_types))
			{
				if (getimagesize($this->file_temp) === FALSE)
				{
					return FALSE;
				}
			}

			if (is_array($mime))
			{
				if (in_array($this->file_type, $mime, TRUE))
				{
					return TRUE;
				}
			}
			else
			{
				if ($mime == $this->file_type)
				{
					return TRUE;
				}
			}
		}

		return FALSE;

	}

}
