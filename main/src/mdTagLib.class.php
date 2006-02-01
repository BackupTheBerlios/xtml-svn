<?
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
	 * @copyright 2005, 2006 by John Allen and others (see AUTHORS file for contributor list).
	 * @author John Allen <john.allen@classesarecode.net>
	 * @version 0.99
	 * 
	 */

	require_once("php-markdown.php");
		
	/**
	 *
	 */
	class mdTagLib
		extends XTMLTag
	{
		/**
		 * @ignore
		 */
		function mdTag($xtml)
		{
			parent::XTMLTagLib($xtml);
		}
		
		/**
		 * @ignore
		 */
		function copyright()
		{
			return "md - The XTML Markdown Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for contributor list).\n" .
				"Released under the GNU GPL v2\n\n" .
				"PHP Markdown by Michel Fortin http://www.michelf.com/projects/php-markdown";
		}

		/**
		 * 
		 */
		function md_colon_text($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);
		
			return Markdown(trim($this->xtml->evaluate($value)));
		}
	}
?>
