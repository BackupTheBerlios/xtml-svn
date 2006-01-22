<?
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
	
	/**
	 *
	 */
	class eTag
		extends PistolTag
	{
		function eTag($pistol)
		{
			parent::PistolTag($pistol);
		}
		
		/**
		 * 
		 */
		function copyright()
		{
			return "e - The PiSToL entity Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
				"Released under the GNU GPL v2";
		}

		/**
		 * 
		 */
		function _stringRepeat($element, $str)
		{
			$num = $this->pistol->getVar($element->getAttribute("repeat"));
			if ($num > 0)
			{
				return str_repeat($str, $num);
			}
			else
			{
				return $str;
			}
		}
		/**
		 * 
		 */
		function tag_lt($element)
		{
			return $this->_stringRepeat($element, "&lt;");
		}

		/**
		 * 
		 */
		function tag_gt($element)
		{
			return $this->_stringRepeat($element, "&gt;");
		}

		/**
		 * 
		 */
		function tag_nbsp($element)
		{
			return $this->_stringRepeat($element, "&nbsp;");
		}
		
		/**
		 * 
		 */
		function tag_spc($element)
		{
			return $this->_stringRepeat($element, " ");
		}
	}
?>
