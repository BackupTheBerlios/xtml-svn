<?php
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

	class htmlTagLib
		extends PistolTag
	{
		/**
		 * @ignore
		 */
		function htmlTagLib($pistol)
		{
			parent::PistolTag($pistol);
		}
		
		/**
		 * @ignore
		 */
		function copyright()
		{
			return "html - The PiSToL html Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
				"Released under the GNU GPL v2";
		}
		
		/**
		 * 
		 */
		function html_colon_select($element)
		{
			$var = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));
			$name = $element->getAttribute("name");
			$default = $element->getAttribute("default");
			$output = "<select name='$name'>";
			
			foreach ($var as $item)
			{
				$output .= "<option ";
				if ($item == $default)
				{
					$output .= "SELECTED";
				}
				$output .= ">$item</option>";
			}
			
			$output .= "</select>";
			return $output;
		}

		/**
		 * @ignore
		 */
		function _list($element)
		{
			$output = "";
			$var = $this->pistol->getVar($this->pistol->_getAttributeOrBody($element));
			$member = $element->getAttribute("member");

			if (is_array($var))
			{
				foreach($var as $item)
				{
					if (is_object($item))
					{
						$output .= "<li>".$item->$member."</li>";
					}
					else
					{
						$output .= "<li>".$item."</li>";
					}
				}
			}
			
			return $output;
		}
		
		/**
		 * 
		 */
		function html_colon_ul($element)
		{
			return "<ul>". $this->_list($element) . "</ul>";
		}
		
		/**
		 * 
		 */
		function html_colon_ol($element)
		{
			return "<ol>". $this->_list($element) . "</ol>";
		}
		
	}	

?>
