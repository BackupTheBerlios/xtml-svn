<?
	/*
	 * $Author: johnallen $
	 * $LastChangedDate: 2006-02-01 22:17:14Z $
	 * $LastChangedRevision: 224 $
	 * $LastChangedBy: johnallen $
	 * $HeadURL: svn+ssh://johnallen@svn.berlios.de/svnroot/repos/xtml/main/src/preview.php $
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

	if ($_SERVER['REQUEST_URI'] == '/')
	{
		// redirect to /site/home
		// this is used because we are using a rewrite rule
		// to rewrite all /site urls to /index.php/site

		header("Location: /site/home");
	}
	else
	{
		ini_set('include_path', "../xtml/src:../src:" . ini_get('include_path'));
		require_once "XTMLProcessor.class.php";

		/*
		 * Create a new XTMLProcessor instance, using the default constructor.
		 * 
		 * The XTML processor will initialise the document location, and script
		 * location based on the URL requested.
		 * 
		 * eg. for /site/home the document will be /site/home.xml, 
		 * and the optional script will be /site/home.php	
		 * 
		 */
		$xtml = new XTMLProcessor();
		$dataModel = $xtml->getDataModel();
		$dataModel->set("Title", "Classes Are Code");
		$dataModel->set("QuoteOfTheDay", "An <em>OpenSource</em> development group.");
		$xtml->render();
	}
?>
