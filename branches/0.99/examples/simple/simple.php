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

	// set the include path for XTML relative to this script
	ini_set('include_path', "../../src:" . ini_get('include_path'));
	
	require_once "XTMLProcessor.class.php";
	 
	class Language
	{
		var $name;
		var $description;
		
		function Language($name, $description, $cssClass="main")
		{
			$this->name = $name;
			$this->cssClass = $cssClass;
			$this->description = $description;
		}
	}

	/*
	 * Create a new XTMLProcessor instance. The template we want to use is called "simple.xml"
	 * The XTMLProcessor will load the tag file from the same directory as the script.	
	 * 
	 */
	$xtml = new XTMLProcessor("simple.xml");

	$a = array(
		new Language("Java", "A modern object pure oriented language"),
		new Language("C++", "An extension of the original C language, adding object oriented features"),
		new Language("C", "A high level procedural language, that can still be used for programming the bare metal"),
		new Language("PHP", "A procedural web site scripting language , with object oriented features")
	);

	$xtml->setVar("languages", $a);
	$xtml->setVar("logo", "../logo");

	$xtml->render();
?>
