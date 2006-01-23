<?
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

	function __autoload($class_name) 
	{
		$file = $class_name . '.class.php';
		require_once $file;
 	}
 	
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
			if ($document == null)
			{
				$this->document = Pistol::getPageLocation();
				$this->script = Pistol::getPageLocation();
			}
			else
			{
				$this->document = $document;
				$this->script = $script;
			}
			
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
		function getPageLocation()
		{
			if (isset($_SERVER['PATH_TRANSLATED']))
			{
				$pageloc = $_SERVER['PATH_TRANSLATED'];
			}
			else if (isset($_SERVER['REDIRECT_URL']))
			{
				$pageloc = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REDIRECT_URL'];
			}
			else
			{
				$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
				$scriptDir = str_replace($scriptName, "", $_SERVER['SCRIPT_FILENAME']);
				$pistolXML = str_replace(".php", ".pistol.xml", $_SERVER['SCRIPT_FILENAME']);
				
				if (!file_exists($pistolXML))
				{
					$pageloc = $scriptDir . "/index";
				}
				else
				{
					$pageloc = str_replace(".php", "", $_SERVER['SCRIPT_FILENAME']);
				}
			}
			
			return $pageloc;
		}

		/**
		 * Retrieve the named attribute from the element, if the attribute does not exist, or has no value,
		 * then process the child nodes and return that as a value instead
		 */
		function _getValueOrAttribute($element, $attribute="value")
		{
			$value = $element->getAttribute($attribute);
			
			if ($value == "")
			{
				$value = $this->process($element->firstChild, true);
			}
	
			return $value;
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
			if ($key{0} == '$')
			{
				$key = explode(".", $key);
				
				if ($this->hasData($key[0]))
				{
					if (count($key) > 1)
					{
						return $this->getVar($this->getObjectData($key));
					}
					else
					{
						return $this->getVar($this->data[$key[0]]);
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
		function getVar($key)
		{
			$data = $this->_getVar($key);

			if (!is_array($data))
			{
				// make sure data becomes string
				$data .= "";
				$len = strlen($data);
				$pos = 0;
				$source = $data;
				$data = "";
				
				while ($pos < $len)
				{
					if ($source{$pos} == '\\')
					{
						$pos++;
						$data .= $source{$pos++};
					}
					else if ($source{$pos} == '$')
					{
						$key = "";
						
						if ($source{$pos+1} == '{')
						{
							$pos+=2;
							
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
							}
							
							$key = "\${$key}";
							$data .= $this->getVar("\${$key}");
						}
						else
						{
							$pos++;
							
							while ($pos < $len &&
								($c = $source{$pos}) != ' ' &&
								$c != '$')
							{
								$key .= $c;
								$pos++;
							}
							
							$key = "\$$key";
						}

						//print "key=[$key]=" . $this->getVar($key) . "<br>\n";
						$data .= $this->getVar($key);
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
		function setVar($key, $data)
		{
			$this->data["\$$key"] = $data;
		}
		
		/**
		 * 
		 */
		function unsetVar($key)
		{
			unset($this->data["\$$key"]);
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
		function createTagClass($tag)
		{
			$tagClass = $tag . "Tag";
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
			if (file_exists($this->document))
			{
				return file_get_contents($this->document);
			}
			else
			{
				if ($this->doc->load($this->document . ".pistol.xml"))
				{
					$child = $this->doc->documentElement->firstChild;
					$output = $this->process($child);
				}
				else
				{
					$output = "<!-- include " . $this->document . " not found -->\n";
				}
				
				return $output;
			}
		}
		/**
		 * 
		 */
		function generate()
		{
			if (file_exists($this->document))
			{
				if (function_exists("mime_content_type"))
				{
					header("Content-type: " + mime_content_type($this->document));
				}
				
				return file_get_contents($this->document);
			}
			else
			{
				$started = microtime(true);
		
				if ($this->script)
				{
					$script = $this->script . ".pistol.php";
					
					if (file_exists($script))
					{
						require_once "$script";
						pistolScript($this);
					} 
				}
				
				if ($this->doc->load($this->document . ".pistol.xml"))
				{
					$output = $this->process($this->doc->documentElement);
				}
				
				$finished = microtime(true);
				$renderTime = ($finished - $started) * 1000;
	
				$output = 
					"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n" . 
					"	\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n\n" .
					"<!--\nGenerated using PiSToL, the PHP Standard Tag Library\n\n" .
					"The following tag libraries were used to render this document\n" .
					$this->copyrights .
					"\nRendering took ${renderTime}ms\n" .
					"-->\n\n" .
					$output;
				
				return $output;
			}
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
		function processElement($element, $skipws = false)
		{
			$output = "";
			
			switch ($element->nodeType)
			{
				case XML_ELEMENT_NODE:
				{
					if (($tag = explode(":", $element->tagName)) && count($tag) == 2)
					{
						$_class = $this->createTagClass($tag[0]);
						$_method = "tag_" . $tag[1];
	
						//print $tag[0] . "->" . "$_method\n";
						
						if (!method_exists($_class, $_method))
						{
							print "<b>$tag[0]:$tag[1]</b>: required method $_method() not found<br>";
							die();
						}
						
						$data = $_class->$_method($element);
						
						if (is_object($data) || is_array($data))
						{
							$output = $data;
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
								$output .= $this->process($element->firstChild);
								$output .= "</" . $element->tagName . ">";
							}
						}
					}
				}
				break;
				
				case XML_TEXT_NODE:
				{
					if ($element->nodeValue{0} == '$')
					{
						$data = $this->getVar($element->nodeValue);
						
						if (is_object($data) || is_array($data))
						{
							$output = $data;
						}
						else
						{
							$output .= $data;
						}
					}
					else
					{
						if ($skipws)
						{
							$text = trim($element->nodeValue);
							
							if ($text)
							{
								$output .= $element->nodeValue;
							}
						}
						else
						{
							$output .= $element->nodeValue;
						}
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
		function process($child, $skipws = false)
		{
			$output = "";
			
			while ($child && !$this->isElse($child))
			{
				//print "<pre>";
				//print $child->tagName . ":" . $child->nodeType . "\n";
	
				$data = $this->processElement($child, $skipws);
			
				if (is_object($data) || is_array($data))
				{
					$output = $data;
				}
				else
				{
					if ($data)
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
		function processElse($child, $skipws = false)
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
	
				$data = $this->processElement($child, $skipws);
			
				if (is_object($data) || is_array($data))
				{
					$output = $data;
				}
				else
				{
					if ($data)
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
