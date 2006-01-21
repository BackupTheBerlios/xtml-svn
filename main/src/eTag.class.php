<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PiSToL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, by Classes Are Code.
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
				"Copyright 2005, 2006, by Classes Are Code.\n" .
				"Released under the GNU GPL v2";
		}

		/**
		 * 
		 */
		function tag_lt($element)
		{
			return "&lt;";
		}

		/**
		 * 
		 */
		function tag_gt($element)
		{
			return "&gt;";
		}

		/**
		 * 
		 */
		function tag_nbsp($element)
		{
			return "&nbsp;";
		}
	}
?>
