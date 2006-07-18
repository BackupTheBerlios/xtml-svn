<?php
	/*
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * XTML - eXtensible Tag Markup Language
	 * 
	 * This library is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU Lesser General Public
	 * License as published by the Free Software Foundation; either
	 * version 2.1 of the License, or (at your option) any later version.
	 *
	 * This library is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	 * General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License 
	 * along with this library; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 *
	 * You may contact the authors of XTML by e-mail at:
	 * developers@classesarecode.net
	 *
	 * The latest version of XTML can be obtained from:
	 * http://developer.berlios.de/projects/xtml/
	 *
	 * @link http://developer.berlios.de/projects/xtml/
	 * @copyright 2005, 2006 by The Classes Are Code Group (see AUTHORS file for contributor list).
	 * @author John Allen <john.allen@classesarecode.net>
	 * @version 0.99
	 * 
	 */

	/**
	 *
	 */
	class i18nTagLib
		extends XTMLTag
	{
		private $lang;
		
		/**
		 * @ignore
		 */
		function i18nTagLib($xtml)
		{
			parent::XTMLTag($xtml);
			
			// Set language, default to en (English)
			$this->lang = isset($_REQUEST['lang']) ? $_REQUEST['lang']:"value";
		}

		/**
		 * @ignore
		 */
		function copyright()
		{
			return "i18n - The XTML i18n Tag Library\n" .
				"Copyright 2005, 2006 by The Classes Are Code Group (see AUTHORS file for contributor list).\n" .
				"Released under the GNU GPL v2";
		}

		/**
		 * @ignore
		 */
		function getlang()
		{
			return $this->lang;
		}
		
		/**
		 * @ignore
		 */
		function setlang($lang)
		{
			$this->lang = $lang;
		}
		
		/**
		 * 
		 */
		function i18n_colon_setlang($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);
			$lang = $this->xtml->evaluate($value);
			$this->setLang($lang);
		}
		
		/**
		 * 
		 */
		function i18n_colon_getlang($element)
		{
			return $this->getlang();
		}
		
		/**
		 * 
		 */
		function i18n_colon_message($element)
		{
			// TODO: implement translate logic
			$maxlen = $element->getAttribute("maxlen");
			$ellipses = $element->getAttribute("ellipses") == "true" ? true:false;
			$text = gettext($this->xtml->_getAttributeOrBody($element, "value", 0));
			$text = $this->xtml->evaluate($text);
			
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
