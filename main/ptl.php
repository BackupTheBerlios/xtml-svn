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
	 * NOTE: for URLs like http://yourvhost/test to automatically be rewritten
	 * to http://yourvhost/ptl.php?test you must have MultiViews turned off
	 * on your vhost
	 */
	$tagClasses = array();
	$templateData = array();

	/**
	 *
	 */
	function process($child)
	{
		while ($child)
		{
			//print "<pre>";
			//print $child->tagName . ":" . $child->nodeType . "\n";

			if ($child->nodeType == XML_ELEMENT_NODE)
			{
				if (($tag = explode(":", $child->tagName)) && count($tag) == 2)
				{
					// create the class
					if (!isset($tagClasses[$tag[0]]))
					{
						$tagClasses[$tag[0]] = new $tag[0];
					}

					$_class = $tagClasses[$tag[0]];
					$_method = "_" . $tag[1];

					//print $tag[0] . "->" . "$_method\n";
					$_class->$_method($child);
				}
				else
				{
					print "<" . $child->tagName . ">";
					process($child->firstChild);
					print "</" . $child->tagName . ">";
				}
			}
			else if ($child->nodeType == XML_TEXT_NODE)
			{
				print $child->nodeValue;
			}
			else
			{
				process($child->firstChild);
			}
				
			$child = $child->nextSibling;
		}
	}

	/**
	 *
	 */
	class entity
	{
		function _nbsp($element)
		{
			print "&nbsp;";
		}
	}

	/**
	 *
	 */
	class ptl
	{
		function _translate($element)
		{
			global $templateData;

			// TODO: implement translate logic
			print $element->getAttribute("en");
		}

		function _if($element)
		{
			global $templateData;

			$var = $element->getAttribute("var");
			$eq = $element->getAttribute("eq");
			$neq = $element->getAttribute("neq");

			if (isset($eq) && $templateData[$var] == $var)
			{
				process($element->firstChild);
			}
			else if (isset($neq) && $templateData[$var] != $var)
			{
				process($element->firstChild);
			}
		}

		function _loop($element)
		{
			global $templateData;

			$data = $element->getAttribute("data");
			$var = $element->getAttribute("var");

			if ($data{0} == '(')
			{
				$_code = "\$data = array" . $data . ";";
				eval($_code);
			}
			else if ($data{0} == '$' && isset($templateData[$data]))
			{
				$data = $templateData[$data];
			}

			if (is_array($data))
			{
				foreach ($data as $tmp)
				{
					$key = "\$" . $var;
					$templateData[$key] = $tmp;
					process($element->firstChild);
				}
			}
		}

		function _out($element)
		{
			global $templateData;

			$value = $element->getAttribute("value");
		
			if ($value{0} == '$')
			{
				print $templateData[$value];
			}
			else
			{
				print "$value";
			}
			
			process($element->firstChild);
		}
	}

	/**
	 *
	 */
	if (isset($_SERVER['REQUEST_URI']))
	{
		if ($_SERVER['REQUEST_URI'] == "/")
		{
			$_SERVER['REQUEST_URI'] = "/index";
		}

		$ptp = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'] . ".ptp";
	}
	else
	{
		$ptp = "index.ptp";
	}

	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = true;

	print "<!-- Generated by PTL, the PHP Tag Library -->\n";

	if ($doc->load($ptp))
	{
		process($doc->documentElement);
	}
	else
	{
		print "Document load failed";
	}
?>
