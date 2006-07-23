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
	 * The XTML String tag library.
	 */
	class stringTagLib
		extends XTMLTag
	{
		/**
		 * @ignore
		 */
		function stringTag($xtml)
		{
			parent::XTMLTagLib($xtml);
		}
		
		/**
		 * @ignore 
		 */
		function copyright()
		{
			return "string - The XTML string Tag Library\n" .
				"Copyright 2005, 2006 by The Classes Are Code Group (see AUTHORS file for contributor list).\n" .
				"Released under the GNU GPL v2";
		}

		/**
		 * This tag replaces one piece of text with another, in either the body of the
		 * element, or if specified the "value" attribute.
		 *
		 * <string:replace find="text to find" replace="text to replace with" value="text"/>
		 * 
		 * <string:replace find="text to find" replace="text to replace with">text</string:replace>
		 * 
		 */
		function string_colon_replace($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);
			$find = $this->xtml->_getAttributeOrBody($element, "find");
			$replace = $this->xtml->_getAttributeOrBody($element, "replace");
			
			return str_replace($find, $replace, $value);
		}
		
		/**
		 * The ucase tag converts either the tag body, or the value attribute to uppercase.
		 * The value attribute, if present, takes precedence over the tag body.
		 * 
		 * eg.
		 * <string:ucase>This is a nice day</string:ucase>
		 * 
		 * Will produce the following output:-
		 * THIS IS A NICE DAY  
		 */		
		function string_colon_ucase($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);

			return strtoupper($value);	
		}
		
		/**
		 * 
		 */		
		function string_colon_ucfirst($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);

			return ucfirst($value);	
		}
		
		/**
		 * 
		 */		
		function string_colon_ucwords($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);

			return ucwords($value);	
		}
		
		/**
		 * 
		 */		
		function string_colon_lcase($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);

			return strtolower($value);	
		}
		
		/**
		 * 
		 */		
		function string_colon_truncate($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);
			$length = $this->xtml->evaluate($element->getAttribute("length"));

			return substr($value, 0, $length);	
		}
		
		/**
		 * 
		 */		
		function string_colon_pad($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);
			$strlen = strlen($value);
			$length = $this->xtml->evaluate($element->getAttribute("length"));

			return $value . str_repeat(" ", $length - $strlen);
		}
		
		/**
		 * 
		 */		
		function string_colon_size($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);
			$strlen = strlen($value);
			$length = $this->xtml->evaluate($element->getAttribute("length"));
			
			if ($strlen > $length)
			{
				return substr($value, 0, $length);
			}
			elseif ($strlen < $length)
			{
				return $value . str_repeat(" ", $length - $strlen);	
			}
			else
			{
				return $value;
			}
		}
		
		/**
		 * 
		 */		
		function string_colon_hide($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);
			$char = $this->xtml->evaluate($element->getAttribute("char"));
			
			return str_repeat($char, strlen($value));
		}
		
		/**
		 * 
		 */		
		function string_colon_mask($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);
			$mask = $this->xtml->evaluate($element->getAttribute("mask"));

			return $mask . substr($value, strlen($mask));	
		}
	}
?>
