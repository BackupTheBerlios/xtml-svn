<?
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
	 *
	 */
	class eTagLib
		extends PistolTag
	{
		/**
		 * @ignore
		 */
		function eTagLib($pistol)
		{
			parent::PistolTag($pistol);
		}
		
		/**
		 * @ignore
		 */
		function copyright()
		{
			return "e - The PiSToL entity Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
				"Released under the GNU GPL v2";
		}

		/**
		 * @ignore
		 */
		function _stringRepeat($element, $str)
		{
			$num = $this->pistol->evaluate($element->getAttribute("repeat"));
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
		function e_colon_lt($element)
		{
			return $this->_stringRepeat($element, "&lt;");
		}

		/**
		 * 
		 */
		function e_colon_gt($element)
		{
			return $this->_stringRepeat($element, "&gt;");
		}

		/**
		 * 
		 */
		function e_colon_nbsp($element)
		{
			return $this->_stringRepeat($element, "&nbsp;");
		}
		
		/**
		 * 
		 */
		function e_colon_spc($element)
		{
			return $this->_stringRepeat($element, " ");
		}
	}
?>
