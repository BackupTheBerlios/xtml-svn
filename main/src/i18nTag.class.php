<?
	/**
	 * $Author: jallen $
	 * $LastChangedDate: 2006-01-13 18:37:04Z $
	 * $LastChangedRevision: 51 $
	 * $LastChangedBy: jallen $
	 * $HeadURL$
	 * 
	 * PSTL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */

	/**
	 *
	 */
	class i18nTag
		extends PSTLTag
	{
		var $lang;
		var $tables;
		var $tablesIndex;
		
		function i18nTag($pstl)
		{
			parent::PSTLTag($pstl);
			
			// Set language, default to en (English)
			$this->lang = isset($_REQUEST['lang']) ? $_REQUEST['lang']:"value";
			
			$this->tables = array();
			$this->tablesIndex = 0;
		}

		/**
		 * 
		 */
		function copyright()
		{
			return "i18n - The i18n PHP Standard Tag Library\n" .
				"Copyright 2005, 2006, the dublinux.net group.\n" .
				"Released under the GNU GPL v2";
		}

		/**
		 * 
		 */
		function getlang()
		{
			return $this->lang;
		}
		
		/**
		 * 
		 */
		function setlang($lang)
		{
			$this->lang = $lang;
		}
		
		/**
		 * 
		 */
		function tag_setlang($element)
		{
			$value = $this->pstl->_getvalue($element);
			$lang = $this->pstl->getVar($value);
			$this->setLang($lang);
		}
		
		/**
		 * 
		 */
		function tag_getlang($element)
		{
			return $this->getlang();
		}
		
		/**
		 * 
		 */
		function tag_message($element)
		{
			// TODO: implement translate logic
			$maxlen = $element->getAttribute("maxlen");
			$ellipsis = $element->getAttribute("ellipsis") == "true" ? true:false;
			$value = $this->pstl->_getvalue($element);
			$text = $this->pstl->getVar($value);
			
			if ($maxlen && strlen($text) > $maxlen)
			{
				if ($ellipsis)
				{
					$text = substr($text, 0, $maxlen-3) . "...";
				}
				else
				{
					$text = substr($text, 0, $maxlen);
				}
			}

			return $text;
		}
	}
?>
