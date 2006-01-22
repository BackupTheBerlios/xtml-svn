<?php

	/**
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
	class stringTag
		extends PistolTag
	{
		function stringTag($pistol)
		{
			parent::PistolTag($pistol);
		}
		
		/**
		 * 
		 */
		function copyright()
		{
			return "string - The PiSToL string Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
				"Released under the GNU GPL v2";
		}

		/**
		 * 
		 */		
		function tag_ucase($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));

			return strtoupper($str);	
		}
		
		/**
		 * 
		 */		
		function tag_ucfirst($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));

			return ucfirst($str);	
		}
		
		/**
		 * 
		 */		
		function tag_ucwords($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));

			return ucwords($str);	
		}
		
		/**
		 * 
		 */		
		function tag_lcase($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));

			return strtolower($str);	
		}
		
		/**
		 * 
		 */		
		function tag_truncate($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
			$length = $this->pistol->getVar($element->getAttribute("length"));

			return substr($str, 0, $length);	
		}
		
		/**
		 * 
		 */		
		function tag_pad($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
			$strlen = strlen($str);
			$length = $this->pistol->getVar($element->getAttribute("length"));

			return $str . str_repeat(" ", $strlen - $length);
		}
		
		/**
		 * 
		 */		
		function tag_size($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
			$strlen = strlen($str);
			$length = $this->pistol->getVar($element->getAttribute("length"));
			
			if ($strlen > $length)
			{
				return substr($str, 0, $length);
			}
			elseif ($strlen < $length)
			{
				return $str . str_repeat(" ", $strlen - $length);	
			}
			else
			{
				return $str;
			}
		}
		
		/**
		 * 
		 */		
		function tag_hide($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
			$char = $this->pistol->getVar($element->getAttribute("char"));
			
			return str_repeat($char, strlen($str));
		}
		
		/**
		 * 
		 */		
		function tag_mask($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
			$mask = $this->pistol->getVar($element->getAttribute("mask"));

			return $mask . substr($str, strlen($mask));	
		}
	}
?>