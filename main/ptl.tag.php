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
	class ptl
	{
		/**
		 * 
		 */
		function tag_translate($engine, $element)
		{
			// TODO: implement translate logic
			print $element->getAttribute("en");
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

			if ($data{0} == '(')
			{
				$_code = "\$data = array" . $data . ";";
				eval($_code);
			}
			else if ($data{0} == '$' && $engine->hasData($data))
			{
				$data = $engine->getData($data);
			}

			if (is_array($data))
			{
				foreach ($data as $tmp)
				{
					$key = "\$" . $var;
					$engine->setData($key, $tmp);
					$engine->process($element->firstChild);
					$engine->unsetData($key);
				}
			}
		}

		/**
		 * 
		 */
		function tag_out($engine, $element)
		{
			$value = $element->getAttribute("value");
		
			if ($value{0} == '$')
			{
				print $engine->getData($value);
			}
			else
			{
				print "$value";
			}
			
			$engine->process($element->firstChild);
		}
	}
?>
