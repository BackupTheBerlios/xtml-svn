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

	function __autoload($class_name) 
	{
		$file = $class_name . '.class.php';
		require_once $file;
 	}

	define('PF_DISCARD_WS_TEXT_NODES', 	0x00000001);
	define('PF_TRIM', 					0x00000002);
	define('PF_EVALUATE', 				0x00000004);
 	
	class Pistol
	{
		private $document;
		private $script;
		private $noBodyTags;
		private $classCache;
		private $copyrights;
		private $data;
		private $doc;
		private $previewMode;

		/**
		 * 
		 */
		function Pistol($document = null, $script = null)
		{
			$this->document = $document;
			$this->script = $script;
			$this->setPageLocation();
			
			$this->previewMode = false;
			$this->classCache = array();
			$this->data = array();
			$this->noBodyTags = array(
				"link" => true,
				"img" => true
				);
			$this->doc = new DOMDocument();
			$this->doc->preserveWhiteSpace = true;

			$this->setVar("include_path", ini_get('include_path'));
		}

		/**
		 * 
		 */
		function setPageLocation()
		{
			if ($this->document == null)
			{
				if (isset($_SERVER['PATH_TRANSLATED']))
				{
					$path = $_SERVER['PATH_TRANSLATED'];
					$this->document = "$path.xml";
					$this->script = "$path.php";
				}
				else if (isset($_SERVER['REDIRECT_URL']))
				{
					$path = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REDIRECT_URL'];
					$this->document = "$path.xml";
					$this->script = "$path.php";
				}
				else
				{
					$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
					$scriptDir = str_replace($scriptName, "", $_SERVER['SCRIPT_FILENAME']);
					$xmlFile = str_replace(".php", ".xml", $_SERVER['SCRIPT_FILENAME']);
					
					if (file_exists($xmlFile))
					{
						$this->document = $xmlFile; 
					}
					else
					{
						$this->document = $scriptDir . "/index.xml";
					}
				}
			}
		}

		/**
		 * 
		 */
		function isFlagSet($flags, $flag)
		{
			return ($flags & $flag) == $flag ? true:false;
		}

		/**
		 * Retrieve the named attribute from the element, if the attribute does not exist, 
		 * or has no value, then process the body and return that as a value instead.
		 * 
		 * Supports variable expansion.
		 */
		function _getAttributeOrBody($element, $attribute="value", $flags = PF_EVALUATE)
		{
			if ($element->hasAttribute($attribute))
			{
				$value = $element->getAttribute($attribute);
				
				return $this->evaluate($value, $flags);
			}
			else
			{
				return $this->process($element->firstChild, $flags);
			}
		}
				
		/**
		 * 
		 */
		function _totext($s)
		{
			if (is_object($s))
			{
				$text = "";
			
				if (get_class($s) == "DOMElement")
				{
					if (($tag = explode(":", $s->tagName)) && count($tag) == 2)
					{
						$text .= "<" . $tag[1];
					}
					else
					{
						$text .= "<" . $s->tagName;
					}
					
					if ($s->hasAttributes())
					{
						$attributes = $s->attributes; 
						$i = 0;
						
						while ($attr = $attributes->item($i++))
						{
							$text .= " " . $attr->nodeName . "=\"" . $attr->nodeValue . "\"";
						}
					}
					
					$text .= ">";
				}
				else
				{
				}
				
				return $text;
			}
			else
			{
				return $s;
			}
		}
		
		/**
		 * 
		 */
		function hasData($key)
		{
			return isset($this->data[$key]); 
		}
		
		/**
		 * 
		 */
		function _getObjectData($object, $key, $index)
		{
			if (!is_object($object))
			{
				return $object;
			}
			else
			{
				$vars = get_object_vars($object);
				
				if (isset($vars[$key[$index]]))
				{
					return $this->_getObjectData($vars[$key[$index]], $key, ++$index);
				}
				else
				{
					return "";
				}
			}
		}
		
		/**
		 * 
		 */
		function getObjectData($key)
		{
			return $this->_getObjectData($this->data[$key[0]], $key, 1);
		}
		
		/**
		 * 
		 */
		function _getVar($key)
		{
			if ($key && $key{0} == '$' && $key{1} == '{')
			{
				$keylen = strlen($key);
				
				if ($key{$keylen-1} == '}')
				{
					$key = explode(".", substr($key, 2, $keylen-3));
					
					if ($this->hasData($key[0]))
					{
						if (count($key) > 1)
						{
							return $this->_evaluate($this->getObjectData($key));
						}
						else
						{
							return $this->_evaluate($this->data[$key[0]]);
						}
					}
				}
				
				return "";
			}
			else
			{
				return $key;
			}
		}

		/**
		 * 
		 */
		function _evaluate($text)
		{
			if (is_array($text))
			{
				return $text;
			}
			
			if (strlen(trim($text)) == 0)
			{
				return $text;
			}
			
			$data = $this->_getVar($text);

			if (!is_array($data) && !is_object($data))
			{
				// make sure data becomes string
				$source = $data . "";
				$pos = 0;
				$len = strlen($source);
				$data = "";
				$key="";
				
				while ($pos < $len)
				{
					if ($source{$pos} == '\\')
					{
						$pos++;
						$data .= $source{$pos++};
					}
					else if ($source{$pos} == '$' && $source{$pos+1} == '{')
					{
						$key .= $source{$pos++};
						$key .= $source{$pos++};
						
						while ($pos < $len &&
							($c = $source{$pos}) != '}')
						{
							$key .= $source{$pos};
							$pos++;
						}
						
						if ($c != '}')
						{
							// missing close }
							// XML file is probably faulty
							$key .= "}";
						}
						else
						{
							$key .= $source{$pos};
							$pos++;
						}
						
						$data .= $this->evaluate($key);
						$key="";
					}
					else
					{
						$data .= $source{$pos++};
					}
				}
			}
			
			return $data;
		}
		
		/**
		 * 
		 */
		function evaluate($text, $flags = PF_EVALUATE)
		{
			if (Pistol::isFlagSet($flags, PF_DISCARD_WS_TEXT_NODES))
			{
				if (trim($text) == "")
				{
					return "";
				}
			}
			
			if (Pistol::isFlagSet($flags, PF_EVALUATE))
			{
				return $this->_evaluate($text, $flags);
			}
			else
			{
				return $text;
			}
		}
		
		/**
		 * 
		 */
		function setVar($key, $data)
		{
			$this->data[$key] = $data;
		}
		
		/**
		 * 
		 */
		function unsetVar($key)
		{
			unset($this->data[$key]);
		}

		/**
		 * 
		 */
		function isNoBodyTag($tag)
		{
			return isset($this->noBodyTags[$tag]) ? true:false;
		}
		
		/**
		 * 
		 */
		function createTagClass($tagClass)
		{
			if (!class_exists($tagClass))
			{
				print "<b>$tag[0]</b>: required class '$tagClass' not found<br>";
				die();
			}
			
			if (!isset($this->classCache[$tagClass]))
			{
				$this->classCache[$tagClass] = new $tagClass($this);
				$copyright = $this->classCache[$tagClass]->copyright();
				
				if ($copyright)
				{
					$this->copyrights .= "\n" .$this->classCache[$tagClass]->copyright() . "\n";
				}
				else
				{
					$this->copyrights .= "\n" . $tag[0] . " tag library, no copyright found" . "\n";
				} 
			}
	
			return $this->classCache[$tagClass];
		}
		
		/**
		 * 
		 */
		function doinclude()
		{
			$output = "";
			
			if (file_exists($this->document))
			{
				$content = file_get_contents($this->document);
				
				if (strncmp("<?xml ", $content, 6) == 0)
				{
					if ($this->doc->loadXML($content))
					{
						$child = $this->doc->documentElement->firstChild;
						$output .= "<!-- " . $this->document . " STARTS -->\n";
						$output .= $this->process($child, PF_EVALUATE);
						$output .= "<!-- " . $this->document . " ENDS -->\n";
					}
					else
					{
						$output = "<!-- include " . $this->document . " not valid XML -->\n";
					}
				}
				else
				{
					$output .= "<!-- " . $this->document . " STARTS -->\n";
					$output .= $content;
					$output .= "<!-- " . $this->document . " ENDS -->\n";
					
					if (function_exists("mime_content_type"))
					{
						header("Content-type: " + mime_content_type($this->document));
					}
				}
			}
			else
			{
				$output = "<!-- include " . $this->document . " not found -->\n";
			}
				
			return $output;
		}
		
		/**
		 * 
		 */
		function generate()
		{
			$output = "";
			
			if (file_exists($this->document) && !is_dir($this->document))
			{
				$started = microtime(true);
				$content = file_get_contents($this->document);
				
				if (strncmp("<?xml ", $content, 6) == 0)
				{
					if ($this->doc->loadXML($content))
					{
						if ($this->script)
						{
							$script = $this->script;
							
							if (file_exists($this->script) && !is_dir($this->script))
							{
								require_once $this->script;
								pistolScript($this);
							} 
						}
						
						$output .= "<!-- " . $this->document . " STARTS -->\n";
						$output .= $this->process($this->doc->documentElement, PF_EVALUATE);
						$output .= "<!-- " . $this->document . " ENDS -->\n";
						$finished = microtime(true);
						$renderTime = ($finished - $started) * 1000;
						
						// TODO: Update to add specified DOCTYPE (XHTML, XML)
						$output = 
							"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n" . 
							"	\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n\n" .
							"<!--\nGenerated using PiSToL, the PHP Standard Tag Library\n\n" .
							"The following tag libraries were used to render this document\n" .
							$this->copyrights .
							"\nRendering took " . sprintf("%0.2f", $renderTime) . "ms\n" .
							"-->\n\n" .
							$output;
					}
					else
					{
						$output = "<!-- " . $this->document . " not valid XML -->\n";
					}
				}
				else
				{
					$output .= "<!-- " . $this->document . " STARTS -->\n";
					$output .= $content;
					$output .= "<!-- " . $this->document . " ENDS -->\n";

					if (function_exists("mime_content_type"))
					{
						header("Content-type: " + mime_content_type($this->document));
					}
				}
			}
			else
			{
				$output = "<!-- " . $this->document . " not found -->\n";
			}
			
			return $output;
		}
		
		/**
		 * 
		 */
		function isPreviewModeEnabled()
		{
			return $this->previewMode;
		}
		
		/**
		 * 
		 */
		function preview()
		{
			$this->previewMode = true;
			print $this->generate();
		}
		
		/**
		 * 
		 */
		function render()
		{
			print $this->generate();
		}
		
		/**
		 *
		 */
		function processElement($element, $flags = PF_EVALUATE)
		{
			$output = "";
			
			switch ($element->nodeType)
			{
				case XML_ELEMENT_NODE:
				{
					if (($tag = explode(":", $element->tagName)) && count($tag) == 2)
					{
						$_tagClassName = $tag[0] . "TagLib";
						$_methodName = $tag[0] . "_colon_" . $tag[1];
						$_class = $this->createTagClass($_tagClassName);
	
						//print $tag[0] . "->" . "$_method\n";
						
						if (!method_exists($_class, $_methodName))
						{
							print "<b>$tag[0]:$tag[1]</b>: required method $_tagClassName::$_methodName() not found<br>";
							die();
						}
						
						$data = $_class->$_methodName($element);
						
						if (is_object($data) || is_array($data))
						{
							return $data;
						}
						else
						{
							$output .= $data;
						}
					}
					else
					{
						// check for crappy HTML tags, but maintain XHTML output compatibility
						if ($element->tagName == "br" ||
							$element->tagName == "hr")
						{
							$output .= "<" . $element->tagName . "/>";
						}
						else
						{
							$output .= "<" . $element->tagName;
							
							if ($element->tagName == 'html')
							{
								$output .= " xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\"";
							} 
							
							if ($element->hasAttributes())
							{
								$attributes = $element->attributes; 
								$i = 0;
								
								while ($attr = $attributes->item($i++))
								{
									$output .= " " . $attr->nodeName . "=\"" . $attr->nodeValue . "\"";
								}
							}
							 
							if ($this->isNoBodyTag($element->tagName))
							{
								$output .= "/>";
							}
							else
							{
								$output .= ">";
								$output .= $this->process($element->firstChild, $flags);
								$output .= "</" . $element->tagName . ">";
							}
						}
					}
				}
				break;
				
				case XML_TEXT_NODE:
				{
					$data = $this->evaluate($element->nodeValue, $flags);

					if (is_object($data) || is_array($data))
					{
						$output = $data;
					}
					else
					{
						$output .= $data;
					}
				}
				break;
			
				case XML_COMMENT_NODE:
				{
					// do nothing
				}
				break;
					
				default:
				{
					// do nothing
				}
			}
			
			return $output;
		}
		
		/**
		 * 
		 */
		function isElse($element)
		{
			return $element->nodeType == XML_ELEMENT_NODE && $element->tagName == "c:else";
		}

		/**
		 *
		 */
		function process($child, $flags = PF_EVALUATE)
		{
			$output = "";
			
			while ($child && !$this->isElse($child))
			{
				//print "<pre>";
				//print $child->tagName . ":" . $child->nodeType . "\n";
	
				$data = $this->processElement($child, $flags);
			
				if ($data)
				{
					if (is_object($data) || is_array($data))
					{
						$output = $data;
					}
					else
					{
						$output .= $data;
					}
				}
					
				$child = $child->nextSibling;
			}
			
			return $output;
		}

		/**
		 *
		 */
		function processElse($child, $flags = PF_EVALUATE)
		{
			$output = "";
			
			while ($child && !$this->isElse($child))
			{
				$child = $child->nextSibling;
			}

			if ($child)
			{
				// we found the <c:else> tag, skip over it
				$child = $child->nextSibling;
			}
						
			while ($child)
			{
				//print "<pre>";
				//print $child->tagName . ":" . $child->nodeType . "\n";
	
				$data = $this->processElement($child, $flags);
			
				if ($data)
				{
					if (is_object($data) || is_array($data))
					{
						$output = $data;
					}
					else
					{
						$output .= $data;
					}
				}
					
				$child = $child->nextSibling;
			}
			
			return $output;
		}

	}
?>
