<?
	/**
	 * $Author: $
	 * $LastChangedDate: $
	 * $LastChangedRevision: $
	 * $LastChangedBy: $
	 * $HeadURL: $
	 * 
	 * PiSToL - The PHP Standard Tag Library
	 * Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).
	 * Released under the GNU GPL v2
	 */

	require_once("PistolRowSupport.class.php");

	/**
	 * 
	 */
	class PistolTableSupport
	{
		var $rowClasses;
		var $rowCount;
		var $row;
		
		/**
		 * 
		 */
		function PistolTableSupport()
		{
			$this->rowClasses = null;
			$this->rowCount = 0;
			$this->row = new PistolRowSupport();
		}
		
		/**
		 * 
		 */
		function getRow()
		{
			return $this->row;
		}
		
		/**
		 * 
		 */
		function setColumnCount($count = 0)
		{
			$this->row->setColumnCount($count);
		}
		
		/**
		 * 
		 */
		function setRowClasses($classes)
		{
			$this->rowClasses = $classes;
		}
		
		/**
		 * 
		 */
		function getRowClasses()
		{
			return $this->rowClasses; 
		}
		
		/**
		 * 
		 */
		function incrementRowCount()
		{
			$this->rowCount++;
		}

		/**
		 * 
		 */
		function getRowCount()
		{
			return $this->rowCount;
		}
	}
	
?>