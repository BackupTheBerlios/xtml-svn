<?
	/*
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PiSToL - PHP Standard Tag Library
	 * Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).
	 * Released under the GNU GPL v2
	 */

	/**
	 *
	 */
	class phpTagLib
		extends PistolTag
	{
		private $tables;
		private $tablesIndex;
		private $iftable;
		
		function phpTagLib($pistol)
		{
			parent::PistolTag($pistol);
		}

		/**
		 * @ignore
		 */
		function copyright()
		{
			return "PHP - The PiSToL php tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
				"Released under the GNU GPL v2\n" .
				"http://pistol.classesarecode.net/"
				;
		}

		/**
		 * 
		 */
		function php_colon_info($element)
		{
			ob_start();
			phpinfo();
			return ob_get_clean();
		}

		/**
		 * 
		 */
		function php_colon_self($element)
		{
			return $_SERVER['PHP_SELF'];
		}
	}
?>