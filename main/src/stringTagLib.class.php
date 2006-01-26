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
		 * This tag replaces one piece of text with another, in either the body of the
		 * element, or if specified the "value" attribute.
		 *
		 * <string:replace find="text to find" replace="text to replace with" value="text"/>
		 * 
		 * <string:replace find="text to find" replace="text to replace with">text</string:replace>
		 * 
		 */
		function string_colon_replace($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);
			$find = $this->pistol->_getAttributeOrBody($element, "find");
			$replace = $this->pistol->_getAttributeOrBody($element, "replace");
			
			return str_replace($find, $replace, $value);
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
			$value = $this->pistol->_getAttributeOrBody($element);

			return strtoupper($value);	
		}
		
		/**
		 * 
		 */		
		function string_colon_ucfirst($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);

			return ucfirst($value);	
		}
		
		/**
		 * 
		 */		
		function string_colon_ucwords($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);

			return ucwords($value);	
		}
		
		/**
		 * 
		 */		
		function string_colon_lcase($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);

			return strtolower($value);	
		}
		
		/**
		 * 
		 */		
		function string_colon_truncate($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);
			$length = $this->pistol->getVar($element->getAttribute("length"));

			return substr($value, 0, $length);	
		}
		
		/**
		 * 
		 */		
		function string_colon_pad($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);
			$strlen = strlen($value);
			$length = $this->pistol->getVar($element->getAttribute("length"));

			return $value . str_repeat(" ", $length - $strlen);
		}
		
		/**
		 * 
		 */		
		function string_colon_size($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);
			$strlen = strlen($value);
			$length = $this->pistol->getVar($element->getAttribute("length"));
			
			if ($strlen > $length)
			{
				return substr($value, 0, $length);
			}
			elseif ($strlen < $length)
			{
				return $value . str_repeat(" ", $length - $strlen);	
			}
			else
			{
				return $value;
			}
		}
		
		/**
		 * 
		 */		
		function string_colon_hide($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);
			$char = $this->pistol->getVar($element->getAttribute("char"));
			
			return str_repeat($char, strlen($value));
		}
		
		/**
		 * 
		 */		
		function string_colon_mask($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);
			$mask = $this->pistol->getVar($element->getAttribute("mask"));

			return $mask . substr($value, strlen($mask));	
		}
	}
?>