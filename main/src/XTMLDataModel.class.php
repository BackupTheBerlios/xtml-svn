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
	 * @copyright 2005, 2006 by The Classes Are Code Group (see AUTHORS file for contributor list).
	 * @author John Allen <john.allen@classesarecode.net>
	 * @version 0.99
	 * 
	 */

	/**
	 * 
	 */
	class XTMLDataModel
	{
		/**
		 * 
		 */
		private $data;
		
		/**
		 * 
		 */
		public function __construct()
		{
			$this->data = array();
		}
		
		/**
		 * 
		 */
		public function set($name, $value)
		{
			$this->data[$name] = array($value);
		}

		/**
		 * 
		 */
		public function push($name, $value)
		{		
			if (isset($this->data[$name]))
			{
				array_push($this->data[$name], $value);
			}
			else
			{
				$this->data[$name] = array($value);
			}
		}
		
		/**
		 * 
		 */
		function pop($name)
		{
			array_pop($this->data[$name]);
			
			if (count($this->data[$name]) == 0)
			{
				unset($this->data[$name]);
			}
		}
		
		/**
		 * 
		 */
		function clear($name)
		{
			unset($this->data[$name]);
		}

		/**
		 * 
		 */
		public function get($name)
		{
			if (!isset($this->data[$name]))
			{
				$this->load($name);
			}
			
			return $this->data[$name];
		}
		
		/**
		 * 
		 */
		function notNull($name)
		{
			return isset($this->data[$name]) && count($this->data[$name]) > 0;
		}
		
		/**
		 * Default function to set undefined variables
		 * 
		 * An application should inherit from this class, and 
		 * over-ride this method to provide dynamic data
		 * loading.
		 */
		protected function load($name)
		{
			die("DataModel error value " . $name . " not set");
		}
	}
?>