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
	class entityTag
		extends PistolTag
	{
		function entityTag($pistol)
		{
			parent::PistolTag($pistol);
		}
		
		/**
		 * 
		 */
		function copyright()
		{
			return "entity - The entity PHP Standard Tag Library\n" .
				"Copyright 2005, 2006, by Classes Are Code.\n" .
				"Released under the GNU GPL v2";
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
