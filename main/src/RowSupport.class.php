<?
	/**
	 * $Author: jallen $
	 * $LastChangedDate: 2006-01-17 21:38:57Z $
	 * $LastChangedRevision: 69 $
	 * $LastChangedBy: jallen $
	 * $HeadURL: svn+ssh://svn.classesarecode.net/var/svn/pstl/main/src/cTag.class.php $
	 * 
	 * PSTL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */

	/**
	 * 
	 */
	class RowSupport
	{
		var $colClasses;
		var $colCount;
		
		/**
		 * 
		 */
		function RowSupport()
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