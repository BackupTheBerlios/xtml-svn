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
		
		function tag_truncate($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
			$length = $this->pistol->getVar($element->getAttribute("length"));
			return substr($str, 0, $length);	
		}
		
		function tag_pad($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
			$strlen = strlen($str);
			$length = $this->pistol->getVar($element->getAttribute("length"));
			return $str . str_repeat(" ", $strlen - $length);
		}
		
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
		}
		
		function tag_mask($element)
		{
			$str = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
			$mask = $this->pistol->getVar($element->getAttribute("mask"));
			$maskLen = strlen($mask);
			if ($maskLen > 1)
			{
				return $mask . substr($str, $maskLen);	
			}
			else
			{
				return str_repeat($mask, strlen($str));
			}
		}
		
	}
?>