<?
	/**
	 * $Author: jallen $
	 * $LastChangedDate: 2006-01-18 08:10:24Z $
	 * $LastChangedRevision: 74 $
	 * $LastChangedBy: jallen $
	 * $HeadURL: svn+ssh://svn.classesarecode.net/var/svn/pstl/main/src/mdTag.class.php $
	 * 
	 * PSTL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */

	require_once("php-markdown.php");
		
	/**
	 *
	 */
	class mdTag
		extends PSTLTag
	{
		function mdTag($pstl)
		{
			parent::PSTLTag($pstl);
		}
		
		/**
		 * 
		 */
		function copyright()
		{
			return "md - Markdown(http://www.michelf.com/projects/php-markdown/) tag support for the PHP Standard Tag Library\n" .
				"Copyright 2005, 2006, the dublinux.net group.\n" .
				"Released under the GNU GPL v2";
		}

		/**
		 * 
		 */
		function tag_text($element)
		{
			$value = $this->pstl->_getValueOrAttribute($element);
		
			if ($value{0} == '$')
			{
				return Markdown(trim($this->pstl->getVar($value)));
			}
			else
			{
				return Markdown(trim($value));
			}
		}
	}
?>
