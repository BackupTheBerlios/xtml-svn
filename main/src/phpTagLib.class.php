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

	/**
	 *
	 */
	class phpTagLib
		extends XTMLTag
	{
		private $tables;
		private $tablesIndex;
		private $iftable;
		
		/**
		 * @ignore
		 */
		function phpTagLib($xtml)
		{
			parent::XTMLTag($xtml);
		}

		/**
		 * @ignore
		 */
		function copyright()
		{
			return "PHP - The XTML php tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for contributor list).\n" .
				"Released under the GNU GPL v2\n" .
				"http://xtml.classesarecode.net/"
				;
		}

		/**
		 * Insert the result of the phpinfo() function into the output.
		 */
		function php_colon_info($element)
		{
			ob_start();
			phpinfo();
			return ob_get_clean();
		}

		/**
		 * Insert the value of PHP_SELF (full path to the script from the document root) into the output
		 */
		function php_colon_self($element)
		{
			return $_SERVER['PHP_SELF'];
		}
	}
?>