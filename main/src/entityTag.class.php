<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PSTL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, by Classes Are Code.
	 * Released under the GNU GPL v2
	 */
	
	/**
	 *
	 */
	class entityTag
		extends PSTLTag
	{
		function entityTag($pstl)
		{
			parent::PSTLTag($pstl);
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
