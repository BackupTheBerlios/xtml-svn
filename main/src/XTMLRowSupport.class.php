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
	 * @copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).
	 * @author John Allen <john.allen@classesarecode.net>
	 * @version 0.99
	 * 
	 */

	/**
	 * 
	 */
	class XTMLRowSupport
	{
		var $colClasses;
		var $colCount;
		
		/**
		 * 
		 */
		function XTMLRowSupport()
		{
			$this->colClasses = null;
			$this->colCount = 0;
		}
		
		/**
		 * 
		 */
		function setColumnCount($count = 0)
		{
			$this->colCount = 0;
		}
		
		/**
		 * 
		 */
		function setColClasses($classes)
		{
			$this->colClasses = $classes;
		}
		
		/**
		 * 
		 */
		function getColClasses()
		{
			return $this->colClasses; 
		}
		
		/**
		 * 
		 */
		function incrementColCount()
		{
			$this->colCount++;
		}

		/**
		 * 
		 */
		function getColCount()
		{
			return $this->colCount;
		}
	}
	
?>