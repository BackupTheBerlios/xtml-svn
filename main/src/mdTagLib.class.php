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

	require_once("php-markdown.php");
		
	/**
	 *
	 */
	class mdTagLib
		extends PistolTag
	{
		/**
		 * @ignore
		 */
		function mdTag($pistol)
		{
			parent::PistolTagLib($pistol);
		}
		
		/**
		 * @ignore
		 */
		function copyright()
		{
			return "md - The PiSToL Markdown Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
				"Released under the GNU GPL v2\n\n" .
				"PHP Markdown by Michel Fortin http://www.michelf.com/projects/php-markdown";
		}

		/**
		 * 
		 */
		function md_colon_text($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);
		
			return Markdown(trim($this->pistol->evaluate($value)));
		}
	}
?>
