<?
	/**
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
	class i18nTag
		extends PistolTag
	{
		private $lang;
		
		function i18nTag($pistol)
		{
			parent::PistolTag($pistol);
			
			// Set language, default to en (English)
			$this->lang = isset($_REQUEST['lang']) ? $_REQUEST['lang']:"value";
		}

		/**
		 * 
		 */
		function copyright()
		{
			return "i18n - The PiSToL i18n Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
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
			$value = $this->pistol->_getValueOrAttribute($element);
			$lang = $this->pistol->getVar($value);
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
			$ellipses = $element->getAttribute("ellipses") == "true" ? true:false;
			$text = gettext($this->pistol->_getValueOrAttribute($element));
			
			// TODO: handle variables in the text
			
			if ($maxlen && strlen($text) > $maxlen)
			{
				if ($ellipses)
				{
					$text = substr($text, 0, $maxlen-3) . "&hellip;";
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
