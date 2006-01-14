<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PSTL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */
	
	/**
	 *
	 */
	class entityTag
		extends tagBase
	{
		function entityTag($pstl)
		{
			parent::tagBase($pstl);
		}
		
		/**
		 * 
		 */
		function copyright()
		{
			return "entity - The entity PHP Standard Tag Library\n" .
				"Copyright 2005, 2006, the dublinux.net group.\n" .
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
