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

	/**
	 * 
	 */
	class PSTLRowSupport
	{
		var $colClasses;
		var $colCount;
		
		/**
		 * 
		 */
		function PSTLRowSupport()
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