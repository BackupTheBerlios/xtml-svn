<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */
	
	/**
	 *
	 */
	class entityTag
		extends tagBase
	{
		function entityTag($engine)
		{
			parent::tagBase($engine);
		}
		
		/**
		 * 
		 */
		function tag_nbsp($element)
		{
			$engine->append("&nbsp;");
		}
	}
?>
