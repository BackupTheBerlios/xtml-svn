<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PSTL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */

	require_once("PSTLRowSupport.class.php");

	/**
	 * 
	 */
	class PSTLTableSupport
	{
		var $rowClasses;
		var $rowCount;
		var $row;
		
		/**
		 * 
		 */
		function PSTLTableSupport()
		{
			$this->rowClasses = null;
			$this->rowCount = 0;
			$this->row = new PSTLRowSupport();
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