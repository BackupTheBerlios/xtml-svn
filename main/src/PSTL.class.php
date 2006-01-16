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
	 * Testing
	 */

	function __autoload($class_name) 
	{
		$file = $class_name . '.class.php';
		require_once $file;
 	}
 	
	class PSTL
	{
		var $document;
		var $script;
		var $classCache;
		var $copyrights;
		var $data;
		var $doc;

		/**
		 * 
		 */
		function PSTL($document = null, $script = null)
		{
			if ($document == null)
			{
				$this->document = PSTL::getPageLocation();
				$this->script = PSTL::getPageLocation();
			}
			else
			{
				$this->document = $document;
				$this->script = $script;
			}
			
			$this->classCache = array();
			$this->data = array();
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
				$scriptDir =  str_replace($scriptName, "", $_SERVER['SCRIPT_FILENAME']);
				$pageloc = $scriptDir . "/index";
			}
			
			return $pageloc;
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
		function render()
		{
			if (file_exists($this->document))
			{
				print file_get_contents($this->document);
			}
			else
			{
				$started = microtime(true);
		
				if ($this->script)
				{
					$script = $this->script . ".pstl.php";
					
					if (file_exists($script))
					{
						require_once "$script";
						pstlScript($this);
					} 
				}
				
				if ($this->doc->load($this->document . ".pstl.xml"))
				{
					$output = $this->process($this->doc->documentElement);
				}
				
				$finished = microtime(true);
				$renderTime = ($finished - $started);
	
				$output = 
					"<!DOCTYPE html\n" .
					"    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n" .
					"    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n\n" .
					"<!--\nGenerated using PSTL, the PHP Standard Tag Library\n\n" .
					"The following tag libraries were used to render this document\n" .
					$this->copyrights .
					"\nRendering took " . ($finished - $started) . " seconds\n" .
					"-->\n\n" .
					$output;
				
				print $output;
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
		function process($child)
		{
			$output = "";
			
			while ($child)
			{
				//print "<pre>";
				//print $child->tagName . ":" . $child->nodeType . "\n";
	
				if ($child->nodeType == XML_ELEMENT_NODE)
				{
					if (($tag = explode(":", $child->tagName)) && count($tag) == 2)
					{
						$tagClass = $tag[0] . "Tag";
						
						// create the class, if necessary
						
						if (!class_exists($tagClass))
						{
							print "<b>$tag[0]</b>: supporting class ($tagClass) not found<br>";
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
	
						$_class = $this->classCache[$tagClass];
						$_method = "tag_" . $tag[1];
	
						//print $tag[0] . "->" . "$_method\n";
						
						if (!method_exists($_class, $_method))
						{
							print "<b>$tag[0]:$tag[1]</b>: supporting method ($tagClass:$_method) not found<br>";
							die();
						}
						
						$output .= $_class->$_method($child);
					}
					else
					{
						// check for crappy HTML tags, but maintain XHTML output compatibility
						if ($child->tagName == "br")
						{
							$output .= "<" . $child->tagName . "/>";
						}
						else
						{
							$output .= "<" . $child->tagName;
							
							if ($child->hasAttributes())
							{
								$attributes = $child->attributes; 
								$i = 0;
								
								while ($attr = $attributes->item($i++))
								{
									$output .= " " . $attr->nodeName . "=\"" . $attr->nodeValue . "\"";
								}
							}
							 
							$output .= ">";
							$output .= $this->process($child->firstChild);
							$output .= "</" . $child->tagName . ">";
						}
					}
				}
				else if ($child->nodeType == XML_TEXT_NODE)
				{
					$output .= $child->nodeValue;
				}
				else
				{
					$output .= $this->process($child->firstChild);
				}
					
				$child = $child->nextSibling;
			}
			
			return $output;
		}

	}
?>
