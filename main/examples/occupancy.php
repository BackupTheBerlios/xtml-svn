<?php
	/*
	 * $Author: johnallen $
	 * $LastChangedDate: 2006-07-18 20:06:44Z $
	 * $LastChangedRevision: 354 $
	 * $LastChangedBy: johnallen $
	 * $HeadURL: svn+ssh://johnallen@svn.berlios.de/svnroot/repos/xtml/main/examples/simple/simple.php $
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

	// phpinfo(); die();
	// set the include path for XTML relative to this script
	ini_set('include_path', "../src:" . ini_get('include_path'));
	
	require_once "XTMLProcessor.class.php";
	 
	class Details
	{
		public function __construct()
		{
			$this->TypeName = "B&B";
			$this->Default = "Open";
			$this->Beds = 100;
			$this->Sold = 100;
			$this->Percentage = 100;
		}
	}
	class Summary
	{
		public function __construct()
		{
			$this->Date = time();
			$this->Beds=100;
			$this->Sold = 100;
			$this->Percentage = 100;
			
			for ($i=0; $i < 2; $i++)
			{
				$this->details[] = new Details();
			}		
		}
	}
	/*
	 * Create a new XTMLProcessor instance. The template we want to use is called "occupancy.xml"
	 * 
	 */
	$xtml = new XTMLProcessor("occupancy.xml");

	for ($i=0; $i < 1000; $i++)
	{
		$summary[] = new Summary();
	}

	$dataModel = $xtml->getDataModel();
	$dataModel->set("selectFromDate", time());
	$dataModel->set("selectToDate", time());
	$dataModel->set("printFromDate", time());
	$dataModel->set("printToDate", time());
	$dataModel->set("imageDir", "images");
	$dataModel->set("summary", $summary);
	
	$xtml->render();
?>
