<?
	/**
	 * $Author: jallen $
	 * $LastChangedDate: 2006-01-18 08:10:24Z $
	 * $LastChangedRevision: 74 $
	 * $LastChangedBy: jallen $
	 * $HeadURL: svn+ssh://svn.classesarecode.net/var/svn/pistol/main/src/mdTag.class.php $
	 * 
	 * PiSToL - The PHP Standard Tag Library
	 * Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).
	 * Released under the GNU GPL v2
	 */

	require_once("php-markdown.php");
		
	/**
	 *
	 */
	class mdTag
		extends PistolTag
	{
		function mdTag($pistol)
		{
			parent::PistolTag($pistol);
		}
		
		/**
		 * 
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
		function tag_text($element)
		{
			$value = $this->pistol->_getValueOrAttribute($element);
		
			if ($value{0} == '$')
			{
				return Markdown(trim($this->pistol->getVar($value)));
			}
			else
			{
				return Markdown(trim($value));
			}
		}
	}
?>
