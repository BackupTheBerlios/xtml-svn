<?php

	/*
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PiSToL - PHP Standard Tag Library
	 * Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).
	 * Released under the GNU GPL v2
	 */
	 
	/**
	 * The PiSToL String tag library.
	 */
	class stringTagLib
		extends PistolTag
	{
		/**
		 * @ignore
		 */
		function stringTag($pistol)
		{
			parent::PistolTagLib($pistol);
		}
		
		/**
		 * @ignore 
		 */
		function copyright()
		{
			return "string - The PiSToL string Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
				"Released under the GNU GPL v2";
		}

		/**
		 * The ucase tag converts either the tag body, or the value attribute to uppercase.
		 * The value attribute, if present, takes precedence over the tag body.
		 * 
		 * eg.
		 * <string:ucase>This is a nice day</string:ucase>
		 * 
		 * Will produce the following output:-
		 * THIS IS A NICE DAY  
		 */		
		function string_colon_ucase($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));

			return strtoupper($str);	
		}
		
		/**
		 * 
		 */		
		function string_colon_ucfirst($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));

			return ucfirst($str);	
		}
		
		/**
		 * 
		 */		
		function string_colon_ucwords($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));

			return ucwords($str);	
		}
		
		/**
		 * 
		 */		
		function string_colon_lcase($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));

			return strtolower($str);	
		}
		
		/**
		 * 
		 */		
		function string_colon_truncate($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));
			$length = $this->pistol->getVar($element->getAttribute("length"));

			return substr($str, 0, $length);	
		}
		
		/**
		 * 
		 */		
		function string_colon_pad($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));
			$strlen = strlen($str);
			$length = $this->pistol->getVar($element->getAttribute("length"));

			return $str . str_repeat(" ", $length - $strlen);
		}
		
		/**
		 * 
		 */		
		function string_colon_size($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));
			$strlen = strlen($str);
			$length = $this->pistol->getVar($element->getAttribute("length"));
			
			if ($strlen > $length)
			{
				return substr($str, 0, $length);
			}
			elseif ($strlen < $length)
			{
				return $str . str_repeat(" ", $length - $strlen);	
			}
			else
			{
				return $str;
			}
		}
		
		/**
		 * 
		 */		
		function string_colon_hide($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));
			$char = $this->pistol->getVar($element->getAttribute("char"));
			
			return str_repeat($char, strlen($str));
		}
		
		/**
		 * 
		 */		
		function string_colon_mask($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));
			$mask = $this->pistol->getVar($element->getAttribute("mask"));

			return $mask . substr($str, strlen($mask));	
		}
	}
?>