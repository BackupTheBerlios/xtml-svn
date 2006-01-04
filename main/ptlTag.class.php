<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */
	
	/**
	 *
	 */
	class ptlTag
	{
		/**
		 * 
		 */
		function tag_translate($engine, $element)
		{
			// TODO: implement translate logic
			$engine->append($element->getAttribute("en"));
		}

		/**
		 * 
		 */
		function tag_if($engine, $element)
		{
			$var = $element->getAttribute("var");
			$eq = $element->getAttribute("eq");
			$neq = $element->getAttribute("neq");

			if (isset($eq) && $engine->getData($var) == $var)
			{
				$engine->process($element->firstChild);
			}
			else if (isset($neq) && $engine->getData($var) != $var)
			{
				$engine->process($element->firstChild);
			}
		}

		/**
		 * 
		 */
		function tag_loop($engine, $element)
		{
			$data = $element->getAttribute("data");
			$var = $element->getAttribute("var");
			$backgroundColors = $element->getAttribute("background-colors");
			
			if ($backgroundColors)
			{
				$backgroundColors = explode(",", $backgroundColors);
			}

			if ($data{0} == '(')
			{
				$_code = "\$data = array" . $data . ";";
				eval($_code);
			}
			else if ($data{0} == '%' && $engine->hasData($data))
			{
				$data = $engine->getData($data);
			}

			if (is_array($data))
			{
				$bgcolorIndex = 0;
				
				foreach ($data as $tmp)
				{
					if (is_array($backgroundColors) && count($backgroundColors) > 0)
					{
						if ($bgcolorIndex >= count($backgroundColors))
						{
							$bgcolorIndex = 0;
						}
						
						$engine->setData("%background-color", $backgroundColors[$bgcolorIndex]);
						$bgcolorIndex++;
					}
					
					$engine->setData("%" . $var, $tmp);
					$engine->process($element->firstChild);
					$engine->unsetData("%" . $var);
				}
				
				$engine->unsetData("%background-color");
			}
		}

		/**
		 * 
		 */
		function tag_redirect($engine, $element)
		{
			$to = $element->getAttribute("to");
			header("Location: $to");
		}
		
		/**
		 * 
		 */
		function tag_tr($engine, $element)
		{
			$style = $element->getAttribute("style");
			
			if ($style)
			{
				$engine->append("<tr");
				
				if ($element->hasAttributes())
				{
					$attributes = $element->attributes; 
					$i = 0;
					
					while ($attr = $attributes->item($i++))
					{
						$value = $attr->nodeValue;
						
						if ($engine->hasData("%background-color"))
						{
							$value = str_replace("%background-color", $engine->getData("%background-color"), $value);
						}
						
						$engine->append(" " . $attr->nodeName . "=\"" . $value . "\"");
					}
				}

				$engine->append(">");
				$engine->process($element->firstChild);
				$engine->append("</tr>");
			}
		}
		
		/**
		 * 
		 */
		function tag_out($engine, $element)
		{
			$value = $element->getAttribute("value");
		
			if ($value{0} == '%')
			{
				$engine->append($engine->getData($value));
			}
			else
			{
				$engine->append("$value");
			}
			
			$engine->process($element->firstChild);
		}
	}
?>
