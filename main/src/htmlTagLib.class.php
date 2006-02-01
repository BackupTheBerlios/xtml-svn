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
	 * @copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).
	 * @author John Allen <john.allen@classesarecode.net>
	 * @version 0.99
	 * 
	 */

	class htmlTagLib
		extends XTMLTag
	{
		/**
		 * @ignore
		 */
		function htmlTagLib($xtml)
		{
			parent::XTMLTag($xtml);
		}
		
		/**
		 * @ignore
		 */
		function copyright()
		{
			return "html - The XTML html Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
				"Released under the GNU GPL v2";
		}
		
		/**
		 * 
		 */
		function html_colon_select($element)
		{
			$var = $this->xtml->evaluate($this->xtml->_getAttributeOrBody($element));
			$name = $element->getAttribute("name");
			$default = $element->getAttribute("default");
			$output = "<select name='$name'>";
			
			foreach ($var as $item)
			{
				$output .= "<option ";
				if ($item == $default)
				{
					$output .= "SELECTED";
				}
				$output .= ">$item</option>";
			}
			
			$output .= "</select>";
			return $output;
		}

		/**
		 * @ignore
		 */
		function _list($element)
		{
			$output = "";
			$var = $this->xtml->evaluate($this->xtml->_getAttributeOrBody($element));
			$member = $element->getAttribute("member");

			if (is_array($var))
			{
				foreach($var as $item)
				{
					if (is_object($item))
					{
						$output .= "<li>".$item->$member."</li>";
					}
					else
					{
						$output .= "<li>".$item."</li>";
					}
				}
			}
			
			return $output;
		}
		
		/**
		 * 
		 */
		function html_colon_ul($element)
		{
			return "<ul>". $this->_list($element) . "</ul>";
		}
		
		/**
		 * 
		 */
		function html_colon_ol($element)
		{
			return "<ol>". $this->_list($element) . "</ol>";
		}
		
	}	

?>
