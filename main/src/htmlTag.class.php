<?php
	/**
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

	class htmlTag
		extends PistolTag
	{
		function htmlTag($pistol)
		{
			parent::PistolTag($pistol);
		}
		
		/**
		 * 
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
		function tag_select($element)
		{
			$var = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
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
		 * 
		 */
		function _list($element)
		{
			$output = "";
			$var = $this->pistol->getVar($this->pistol->_getValueOrAttribute($element));
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
		function tag_ul($element)
		{
			return "<ul>". $this->_list($element) . "</ul>";
		}
		
		/**
		 * 
		 */
		function tag_ol($element)
		{
			return "<ol>". $this->_list($element) . "</ol>";
		}
		
	}	

?>